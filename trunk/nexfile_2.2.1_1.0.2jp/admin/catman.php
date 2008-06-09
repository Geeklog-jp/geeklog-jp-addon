<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | catman.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

require_once('../../../lib-common.php');
require_once($_CONF['path_system'] . 'nexpro/classes/TreeMenu.php');
require_once($_CONF['path'] . 'plugins/nexfile/debug.php');  // Common Debug Code

if (!isset($_USER) OR $_USER['uid'] <= 1) {
    echo COM_siteHeader();
    echo COM_startBlock($LANG_FMERR['access1']);
    echo '<div style="text-align:center;padding-top:20px;">';
    echo $LANG_FMERR['access2'];
    echo "<p><button  onclick='javascript:history.go(-1)'>{$LANG_FM02['RETURN']}</button></p>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit;
}

// Check if the page navigation is being used
if (empty($_GET['show'])) {
    $show = 10;
}
// Check if page was specified
if (empty($_GET['page'])) {
    $page = 1;
}

$query = DB_query("SELECT count(*) FROM {$_TABLES['fm_category']}");
$nrows = DB_fetchArray($query);
$numpages = ceil($nrows['0'] / $show);
$offset = ($page - 1) * $show;
$base_url = $_CONF['site_url'] . '/quiz/index.php?show='.$show. '&page='.$page; 

/**
* Will upload a new image for the category and update the category record
*/
function fm_addcatimage($catid) {
    global $_FILES,$_CONF,$_TABLES,$_FMCONF;
    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $catname = DB_getItem($_TABLES['fm_category'], "name","cid=$catid");
    $upload = new upload();
    $upload->setLogging(true);
    if (!empty($_CONF['image_lib'])) {
        if ($_CONF['image_lib'] == 'imagemagick') {
            // Using imagemagick
            $upload->setMogrifyPath ($_CONF['path_to_mogrify']);
        } else {
            // must be using netPBM
            $upload->_pathToNetPBM= $_CONF['path_to_netpbm'];
        }
        $upload->setAutomaticResize(true);
    }
    $upload->setAllowedMimeTypes($_FMCONF['allowableimagetypes']);
    if (!$upload->setPath($_CONF['path_html'] . 'nexfile/data/'.$catid)) {
        $GLOBALS['fm_errmsg'] .= $LANG_FMERR['upload7'] .':<BR>' . $upload->printErrors(false);
        return false;
    }

    if ($upload->numFiles() == 1) {
        $curfile = current($_FILES);
        if (strlen($curfile['name']) > 0) {
            $pos = strrpos($curfile['name'],'.') + 1;
            $fextension = substr($curfile['name'], $pos);
            $filename = "category$catid.$fextension";
            $upload->setFileNames($filename);
            $upload->setPerms('0644');
            $upload->setMaxDimensions ($FMCONF['max_catimage_width'], $FMCONF['max_catimage_height']);
            $upload->setMaxFileSize($FMCONF['max_catimage_size']);
            reset($_FILES);
            $upload->uploadFiles();
            if ($upload->areErrors()) {
               $GLOBALS['fm_errmsg'] .= 'File Upload Errors:<BR>' . $upload->printErrors(false);
                  DB_query("UPDATE {$_TABLES['fm_category']} SET image='' WHERE cid=$catid");
               return false;
            }
        } else {
            $filename = '';
            return false;
        }
        DB_query("UPDATE {$_TABLES['fm_category']} SET image='$filename' WHERE cid=$catid");
        fm_updateAuditLog("Added new image to Category: $catid");
        return true;
    }
}

function recursive_view(&$node,$cid) { 
    global $_CONF,$_TABLES,$_FMCONF;
    $query = DB_QUERY("SELECT cid,pid,name,description FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0) {
            if (fm_getPermission($cid,'view')) {
                $subnode[$cid] = new HTML_TreeNode(array('text' => $name ,'link' => $_CONF['site_admin_url'] ."/plugins/nexfile/catman.php?cid=" .$cid ,'icon' => 'folder.gif'));
                recursive_view($subnode[$cid], $cid);
                $node->addItem($subnode[$cid]);
            }
        } else { 
            if (fm_getPermission($cid,'view')) {
                $node->addItem(new HTML_TreeNode(array('text' => $name, 'link' =>$_CONF['site_admin_url'] ."/plugins/nexfile/catman.php?cid=" .$cid , 'icon' => 'folder.gif')));
            }
        } 
    } 
}

$mytextvars = array ('op', 'cid');
$_CLEAN = ppGetData($mytextvars);
$op = $_CLEAN['op'];
$cid = $_CLEAN['cid'];

if ($cid != '') {
    $parms = "&cid=$cid";
} else {
    $parms = '';
}

switch ( $op ) {

case 'permdelete':

    // Delete the permission record for the access record ID in the category permission listing

    $catid = DB_getItem($_TABLES['fm_access'],"catid","accid={$_GET['accid']}");
    if (fm_getPermission($catid,'admin')) {
        $catid = DB_getItem($_TABLES['fm_access'],"catid","accid={$_GET['accid']}");
        DB_query("DELETE FROM {$_TABLES['fm_access']} WHERE accid={$_GET['accid']}");
        fm_updateAuditLog("Deleted Permission record: " . $_GET['accid'] );
        echo COM_refresh($_CONF['site_admin_url'] . "/plugins/nexfile/catman.php?op=permissions&cid=".$catid);
        exit;
    } else {
        $errmsg .= $LANG_FMERR['perms1'];
    }
    break;

case 'permissions':

    $query = DB_query( "SELECT uid,username FROM {$_TABLES['users']} ORDER BY username" );
    while ( list($uid,$username) = DB_fetchARRAY($query)) {
        $selauthors .= '<option value="' . $uid . '">';
        $selauthors .=  $username . '</option>' . LB;
    }

    $query = DB_query( "SELECT grp_id,grp_name FROM {$_TABLES['groups']} WHERE (grp_gl_core = '0' AND grp_name NOT IN ({$_FMCONF['excludeGroups']}) ) OR ( grp_name = 'All Users' or grp_name = 'Logged-in Users') ORDER BY grp_name" );
    if (DB_numROWS($query) > 0 ) {
        while ( list($grp_id,$grp_name) = DB_fetchARRAY($query)) {
            $selgroups .= '<option value="' . $grp_id . '">';
            $selgroups .=  $grp_name . '</option>' . LB;
        }
    } else {
        $selgroups = '<option value="0">'.$LANG_nexfile['msg36'].'</option>';
    }

    echo COM_siteHeader();
    echo COM_startBlock(sprintf($LANG_nexfile['msg60'],DB_getItem($_TABLES['fm_category'], 'name', "cid='{$cid}'")) );

    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['9']   => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?cid='.$cid,
        $LANG_FM_NAVBAR['14']  => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=editcategory&cid='.$cid
    );
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
        }
    }
    echo fm_navbar($navbarMenu);

    echo '<table width="100%" border="0" cellspacing="0" cellpadding="2"><form  action="'.$_SERVER['PHP_SELF'].'" method="post">
          <tr bgcolor="#BBBECE">
            <td width="1%">&nbsp;</td>
            <td width="15%">&nbsp;<b>'.$LANG_nexfile['msg24'].'</b></td>
            <td width="5%">&nbsp;</td>
            <td width="15%">&nbsp;<b>'.$LANG_nexfile['msg25'].'</b></td>
            <td width="1%">&nbsp;</td>
            <td colspan="4" width="60%" align="center">&nbsp;<b>'.$LANG_nexfile['msg26'].'</b></td>
          </tr>
          <tr><td colspan="10"><img src="" height="5"></td></tr>
          <tr>
            <td>&nbsp;</td>
            <td rowspan="3"><select name="selusers[]" multiple size=10>' . $selauthors . '</select></td>
            <td rowspan="3">&nbsp;</td>
            <td rowspan="3"><select name="selgroups[]" multiple size=10>' . $selgroups . '</select></td>
            <td>&nbsp;</td>
            <td>
              <input type="checkbox" name="cb_access[]" value="view" id="feature1"></td>
            <td><label for="feature1">'.$LANG_nexfile['msg27'].'</label></td>
            <td><input type="checkbox" name="cb_access[]" value="upload"  id="feature2"></td>
            <td><label for="feature2">'.$LANG_nexfile['msg30'].'</label></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="checkbox" name="cb_access[]" value="approval" id="feature3"> </td>
            <td><label for="feature3">'.$LANG_nexfile['msg28'].'</label></td>
            <td><input type="checkbox" name="cb_access[]" value="upload_direct" id="feature4"></td>
            <td><label for="feature4">'.$LANG_nexfile['msg31'].'</label></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="checkbox" name="cb_access[]" value="admin" id="feature5"></td>
            <td><label for="feature5">'.$LANG_nexfile['msg29'].'</label></td>
            <td><input type="checkbox" name="cb_access[]" value="upload_ver" id="feature6"></td>
            <td><label for="feature6">'.$LANG_nexfile['msg32'].'</label></td>
          </tr>
          <tr>
              <td colspan="9" align="center"><br>
                <input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="javascript:history.go(-1)">&nbsp;&nbsp;
                <input type="Submit" name="submit" value="'.$LANG_nexfile['msg33'].'">
                <input type="hidden" name="op" value="savepermissions">
                <input type="hidden" name="catid" value="' .$_GET['cid'] .'"<p /></td>
          </tr></form>
        </table><hr>';

    echo '<table border="1" cellpadding="5" cellspacing="1" width="99%">
            <tr>
                <td bgcolor=#BBBECE colspan="9" width="100%" align="Left" valign="top"><font size=2><b>'.$LANG_nexfile['msg34'].'</b></font></td>
            </tr>
            <tr bgcolor="ECE9D8" align="center" valign="top">
                <td align="left">'.$LANG_FM02['PERMS_USER'].'</td>
                <td>'.$LANG_FM02['PERMS_VIEW'].'</td>
                <td>'.$LANG_FM02['PERMS_UPLOAD1'].'</td>
                <td>'.$LANG_FM02['PERMS_UPLOAD2'].'</td>
                <td>'.$LANG_FM02['PERMS_UPLOAD3'].'</td>
                <td>'.$LANG_FM02['PERMS_UPLOAD4'].'</td>
                <td>'.$LANG_FM02['PERMS_ADMIN'].'</td>
                <td>'.$LANG_FM02['PERMS_ACTION'].'</td>
           </tr>';

    $query = DB_query("SELECT accid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin FROM {$_TABLES['fm_access']} WHERE uid > 0 AND catid = {$_GET['cid']}");
    while ( list($accid,$acc_uid,$acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query)) {

        $username = DB_getItem($_TABLES['users'], "username", "uid=$acc_uid");
        $view = ($acc_view) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $upload = ($acc_upload) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploaddir = ($acc_uploaddirect) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploadver = ($acc_uploadver) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $approve = ($acc_approval) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $admin = ($acc_admin) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];

        echo '<tr align="center">
          <td width="20%" align="left">' .$username.'</td>
          <td>' .$view.'</td>
          <td>' .$upload.'</td>
          <td>' .$uploaddir.'</td>
          <td>' .$uploadver. '</td>
          <td>' .$approve. '</td>
          <td>' .$admin. '</td>
          <td>' .'<a href="'.$_SERVER['PHP_SELF'].'?op=permdelete&accid='.$accid.'">'.$LANG_FM02['DELETE'].'</a></td>
        </tr>';
    } 
    echo '</table><br><p>';
 
    echo '<table border="1" cellpadding="5" cellspacing="1" width="99%">
        <tr>
            <td bgcolor=#BBBECE colspan="9" width="100%" align="Left" valign="top"><font size=2><b>'.$LANG_nexfile['msg35'].'</b></font></td>
        </tr>
        <tr bgcolor="ECE9D8" align="center" valign="top">
            <td align="left">'.$LANG_FM02['PERMS_GROUP'].'</td>
            <td>'.$LANG_FM02['PERMS_VIEW'].'</td>
            <td>'.$LANG_FM02['PERMS_UPLOAD1'].'</td>
            <td>'.$LANG_FM02['PERMS_UPLOAD2'].'</td>
            <td>'.$LANG_FM02['PERMS_UPLOAD3'].'</td>
            <td>'.$LANG_FM02['PERMS_UPLOAD4'].'</td>
            <td>'.$LANG_FM02['PERMS_ADMIN'].'</td>
            <td>'.$LANG_FM02['PERMS_ACTION'].'</td>
       </tr>';

    $query = DB_query("SELECT accid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin FROM {$_TABLES['fm_access']} WHERE grp_id > 0 AND catid = {$_GET['cid']}");
    while ( list($accid,$acc_uid,$acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query)) {

        $groupname = DB_getItem($_TABLES['groups'], "grp_name", "grp_id=$acc_grpid");
        $view = ($acc_view) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $upload = ($acc_upload) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploaddir = ($acc_uploaddirect) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploadver = ($acc_uploadver) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $approve = ($acc_approval) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $admin = ($acc_admin) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];

        echo '<tr align="center">
          <td width="20%" align="left">' .$groupname.'</td>
          <td>' .$view.'</td>
          <td>' .$upload.'</td>
          <td>' .$uploaddir.'</td>
          <td>' .$uploadver. '</td>
          <td>' .$approve. '</td>
          <td>' .$admin. '</td>
          <td>' .'<a href="'.$_SERVER['PHP_SELF'].'?op=permdelete&accid='.$accid.'">'.$LANG_FM02['DELETE'].'</a></td>
        </tr>';
    } 
    echo '</table><br><p>';

    $footer = new Template($_CONF['path_layout'] . 'nexfile');
    $footer->set_file (array ('footer'=>'footer.thtml'));
    $footer->set_var ('endblock', COM_endBlock() );
    $footer->set_var ('sitefooter', COM_siteFooter());
    $footer->parse ('output', 'footer');
    echo $footer->finish ($footer->get_var('output'));

    exit;
    break;

case 'savepermissions':

    // Verify user has admin access rights

    if (!fm_updateCatPerms(
        $_POST['catid'],               // Category ID
        $_POST['cb_access'],           // Array of permissions checked by user
        $_POST['selusers'],            // Array of site members
        $_POST['selgroups'])           // Array of groups
        ) 
    {
        $errmsg .= "<br>" . $LANG_FMERR['perms2'];
    }
    echo COM_refresh($_CONF['site_admin_url'] . "/plugins/nexfile/catman.php?op=permissions&cid=".$_POST['catid']);
    exit;
    
    break;


case 'newcategory': 

    echo COM_siteHeader();
    echo COM_startBlock($LANG_nexfile['msg63']);

    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['9']   => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?cid='.$cid
    );
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
        }
    }
    echo fm_navbar($navbarMenu);

    if ($_GET['cid'] != "") {
        $selected = DB_getItem($_TABLES['fm_category'],"name", "cid={$_GET['cid']}");
    }
    
    $rights = array ('admin');

    echo '<div class="clearboth"></div>';
    echo '<table border="0"  cellspacing="2" cellpadding="5"width="99%" bgcolor="#EFEFEF">
        <form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
        <tr bgcolor="#FFFFFF">
            <td bgcolor=#BBBECE colspan="5" width="100%" align=left valign="top"><font size=2><b>'.$LANG_nexfile['msg38'].'</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
            <td width="25%">'.$LANG_FM02['CATEGORY'].'</td>
            <td width="36%"><input type="text" name="catname" size="41" maxlength="64"></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
            <td>'.$LANG_FM02['DESCRIPTION'].'</td>
            <td><textarea name="catdesc" cols="40" rows="3" id="catdesc"></textarea></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
            <td>'.$LANG_FM02['PARENT_CAT'].'</td>
            <td><select name="catparent" style="text-indent: 0px;"><option value="0">'.$LANG_FM02['TOP_CAT'].'</option>' .fm_recursiveCatAdmin($selected) . '</select></td>
        </tr>
        <tr bgcolor="#FFFFFF">
            <td width="30%">'.$LANG_FM02['INHERIT_RIGHTS'].'</td>
            <td width="9%"><input type="checkbox" name="catinherit" value="1" CHECKED></td>
        </tr>
        <tr bgcolor="#FFFFFF">
            <td width="30%">'.$LANG_FM02['AUTO_NOTIFY'].'</td>
            <td width="9%"><input type="checkbox" name="autonotify" value="1"></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
            <td>'.$LANG_FM02['REF_IMAGE'].'</td>
            <td><input type="file" name="catimage" size="30" maxlength="64"></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
            <td colspan="2" align="center"><br>
                <input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="javascript:history.go(-1)">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" name="submit" value="'.$LANG_FM02['CREATE_CAT'].'">
                <input type="hidden" name="op" value="createcategory"><p />
            </td>
        </tr>
        </table></form>';

    $footer = new Template($_CONF['path_layout'] . 'nexfile');
    $footer->set_file (array ('footer'=>'footer.thtml'));
    $footer->set_var ('endblock', COM_endBlock() );
    $footer->set_var ('sitefooter', COM_siteFooter());
    $footer->parse ('output', 'footer');
    echo $footer->finish ($footer->get_var('output'));
    exit;
    
    break;

case 'editcategory': 

    // Verify user has admin access rights
    if (fm_getPermission($_GET['cid'],'admin')) {

        $query = DB_query("SELECT pid,name,description,image,auto_create_notifications FROM {$_TABLES['fm_category']} WHERE cid={$_GET['cid']}"); 
        list($pid,$name,$description,$image,$autonotify) = DB_fetchARRAY($query);
        $description = str_replace('<br />','',$description);
        
        if ($pid > 0) {
            $parent = DB_getITEM($_TABLES['fm_category'],"name","cid=$pid");
        } else {
            $parent = '';
        }
        if (!empty($image)) {
            $viewimage = '<br><IMG SRC="' . $_CONF['site_url'] .'/nexfile/data/' . $_GET['cid'] . '/' . $image . '">&nbsp;<input type="checkbox" name="chkdelimage" value="1">&nbsp;'.$LANG_FM02['DELETE'];
        } else {
            $viewimage = '';
        }
        if ($pid > 0) {
            $selected = DB_getItem($_TABLES['fm_category'],"name", "cid={$pid}");
        }
        $chknotify = ($autonotify == 1) ? 'CHECKED=CHECKED' : '';

        echo COM_siteHeader();
        echo COM_startBlock($LANG_nexfile['msg63']);

        /* Setup Navbar */
        $navbarMenu = array(
            $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
            $LANG_FM_NAVBAR['9']   => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?cid='.$cid,
            $LANG_FM_NAVBAR['10']   => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=newcategory&cid='.$cid,
            $LANG_FM_NAVBAR['15']  => $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=permissions&cid='.$cid
        );
        if(function_exists(prj_getSessionProject)) {
            $projectid = prj_getSessionProject();
            if($projectid > 0) {
                $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
            }
        }
        echo fm_navbar($navbarMenu);

        echo '<div class="clearboth"></div>';
        echo '<table border="0" cellspacing="2" cellpadding="5" width="99%" bgcolor="#EFEFEF">
            <form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
            <tr bgcolor="#FFFFFF">
                <td bgcolor=#BBBECE colspan="5" width="100%" align=left valign="top"><font size=2><b>'.$LANG_nexfile['msg40'].'</b></font></td>
            </tr>
            <tr bgcolor="#FFFFFF"> 
                <td width="25%">'.$LANG_FM02['CATEGORY'].'</td>
                <td width="36%"><input type="text" name="catname" value="'.$name.'" size="41" maxlength="64"></td>
            </tr>
            <tr bgcolor="#FFFFFF"> 
                <td>'.$LANG_FM02['DESCRIPTION'].'</td>
                <td><textarea name="catdesc" cols="40" rows="3" id="catdesc">'.$description.'</textarea></td>
            </tr bgcolor="#FFFFFF">';
            echo '<tr bgcolor="#FFFFFF"> 
                <td>'.$LANG_FM02['PARENT_CAT'].'</td>
                <td><select name="catparent" style="text-indent: 0px;width:250px;">';
            
            $category_options = fm_recursiveCatAdmin($selected,'0','1','',$cid);
            if (SEC_hasRights('nexfile.admin')) {
                $category_options = '<option value="0">'.$LANG_FM02['TOP_CAT'].'</option>' . $category_options;
            } elseif (!fm_getPermission($pid,array ('admin'))) {
                $category_options = '<option value="'.$pid.'" SELECTED>'.$selected.'</option>' . $category_options;
            }
            echo $category_options . '</select>';
            echo '</td>
            </tr>
            <tr bgcolor="#FFFFFF">
                <td width="30%">'.$LANG_FM02['AUTO_NOTIFY'].'</td>
                <td width="9%"><input type="checkbox" name="autonotify" value="1" '. $chknotify.'></td>
            </tr>';
        echo '<tr bgcolor="#FFFFFF">
                <td valign="top">' .$LANG_FM02['REF_IMAGE']. '<br><em>'.$LANG_FM02['MAX'].':&nbsp;'.$_FMCONF['max_thumbnail_width'].'&nbsp;x&nbsp;'.$_FMCONF['max_thumbnail_width'].'</em></td>
                <td><input type="file" name="catimage" value="'.$image.'" size="30" maxlength="64">' .$viewimage. '</td>
            </tr>
            <tr bgcolor="#FFFFFF">
                <td colspan = "2">
                  <table border="0" cellpadding="1" cellspacing="5" width="99%">
                    <tr>
                      <td valign="middle" align="right"><p />
                        <input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="javascript:history.go(-1)">&nbsp;&nbsp;
                      </td>
                      <td>
                        <input type="submit" name="submit" value="'.$LANG_FM02['UPDATE'].'">
                        <input type="hidden" name="op" value="updatecategory">&nbsp;&nbsp;
                        <input type="hidden" name="cid" value="' .$_GET['cid'].'">
                      </td></form>
                      <td valign="middle" align="left"><p />
                            <form action="'.$_SERVER['PHP_SELF'].'" method="post">&nbsp;&nbsp;
                                <input type="submit" name="submit" value="'.$LANG_FM02['DELETE'].'" OnClick="return confirm(\''.$LANG_nexfile['msg56'].'\');">
                                <input type="hidden" name="op" value="delcategory">
                                <input type="hidden" name="cid" value="' .$_GET['cid'].'">
                      </td></form>
                   </tr>
                 </table>
            </tr>
            </table>';

        $footer = new Template($_CONF['path_layout'] . 'nexfile');
        $footer->set_file (array ('footer'=>'footer.thtml'));
        $footer->set_var ('endblock', COM_endBlock() );
        $footer->set_var ('sitefooter', COM_siteFooter());
        $footer->parse ('output', 'footer');
        echo $footer->finish ($footer->get_var('output'));
        exit;
    } else {
        $errmsg .= $LANG_FMERR['admin2'];
    }

    break;

case 'updatecategory':

    // Verify user has admin access rights
    if (fm_getPermission($_POST['cid'],'admin')) {

        $_POST = fm_cleandata($_POST);
        $catid      = $_POST['cid'];
        $catpid     = $_POST['catparent'];
        $catname    = $_POST['catname'];
        $catdesc    = nl2br($_POST['catdesc']);
        $catimage   = $_FILES['catimage'];
        $autonotify   = COM_applyFilter($_POST['autonotify'],true);

        DB_query("UPDATE {$_TABLES['fm_category']} SET pid='$catpid', name='$catname', description='$catdesc', auto_create_notifications='$autonotify' WHERE cid=$catid");
        fm_updateAuditLog("Updated Category : $catid");

        if ($_POST['chkdelimage'] == "1") {
            $image = DB_getItem($_TABLES['fm_category'],"image","cid={$_POST['cid']}");
            $imagefile = "{$_CONF['path_html']}nexfile/data/{$_POST['cid']}/$image";
            if(unlink($imagefile)) {
                DB_query("UPDATE {$_TABLES['fm_category']} SET image='' WHERE cid={$_POST['cid']}");
                fm_updateAuditLog("Deleted Category: $catid Image");
            } else {
                COM_errorLog("nexfile: Unable to delete $imagefile");
                fm_updateAuditLog("Unable to Delete Category: $catid Image");
            }
        }

        if ($catimage['name'] != "") {
            $pos = strrpos($catimage['name'],'.') + 1;
            $fextension = strtolower(substr($catimage['name'], $pos));
            $catimagename = "category$catid.$fextension";
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$catid;

            $curimage = $_CONF['path_html'] ."/nexfile/data/$catid/" . DB_getItem($_TABLES['fm_category'],"image","cid=$catid");
            if (file_exists($curimage)) {
                @unlink($curimage);
            }

            if (!fm_addcatimage($catid)) {
                $errmsg .= $GLOBALS['fm_errmsg'];
            }
        }

    } else {
        $errmsg .= $LANG_FMERR['admin1'];
    }

    break;

case 'delcategory':

    // Verify user has admin access rights
    $delresult = fm_delCategory($_POST['cid']);
    if ($delresult['0'] > 0 ) {
        $errmsg .= $delresult['1'];
    }
    echo COM_refresh($_CONF['site_url'] . "/nexfile/index.php");
    exit;

    break;

case 'createcategory':

    $_POST = fm_cleandata($_POST);
    $catpid     = $_POST['catparent'];
    $catname    = $_POST['catname'];
    $catdesc    = $_POST['catdesc'];
    $catinherit = $_POST['catinherit'];
    $catimage   = $_POST['catimage'];
    $autonotify   = COM_applyFilter($_POST['autonotify'],true);

    $catresult = fm_createCategory($catpid,$catname,$catdesc);

    if ($catresult['0'] > 0 ) {
        $newcid = $catresult['0'];
        if ($autonotify == 1) {
            DB_query("UPDATE {$_TABLES['fm_category']} set auto_create_notifications='1' WHERE cid='$newcid'");
        }

        if ($catinherit == 1) {
            // Retrieve parent User access records - for each record create a new one for this category
            $query1 = DB_query("SELECT uid,view,upload,upload_direct,upload_ver,approval,admin FROM {$_TABLES['fm_access']} WHERE uid > 0 AND catid = {$catpid}");
            while ( list($acc_uid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query1)) {
                DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('$newcid','$acc_uid',0,'$acc_view','$acc_upload','$acc_uploaddirect','$acc_uploadver','$acc_approval','$acc_admin')");
            }
            // Retrieve parent Group access records - for each record create a new one for this category
            $query2 = DB_query("SELECT grp_id,view,upload,upload_direct,upload_ver,approval,admin FROM {$_TABLES['fm_access']} WHERE grp_id > 0 AND catid = {$catpid}");
            while ( list($acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query2)) {
                DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('$newcid',0,'$acc_grpid','$acc_view','$acc_upload','$acc_uploaddirect','$acc_uploadver','$acc_approval','$acc_admin')");
            }

        } else {
            // Create default permissions record for the user that created the category
            fm_updateCatPerms($newcid,$_FMCONF['defOwnerRights'],$_USER['uid'],"");
            fm_updateCatPerms($newcid,$_FMCONF['defCatGroupRights'],"",$_FMCONF['defCatGroup'] );
        }
        fm_addcatimage($newcid);
        fm_updateAuditLog("New Category: $cid created");
    } else {
        $errmsg = $catresult['1'];
    }
    break;

}

// Main Code 

// Determine if this user has any submitted files that they can approve
$query = DB_query("SELECT cid from {$_TABLES['fm_submissions']}");
$submissions = 0;
while ( list($catid) = DB_fetchArray($query) ) {
    if (fm_getPermission($catid,'approval')) {
        $submissions++;
    }
}

$page = new Template($_CONF['path_layout'] . 'nexfile');
$page->set_file (array ('directory'=>'filedirectory_view.thtml','records' => 'filelistrow.thtml'));
$page->set_var ('siteheader', COM_siteHeader());
$page->set_var ('siteurl', $_CONF['site_url']);
$page->set_var ('layout_url',  $_CONF['layout_url'] . '/nexfile');
$page->set_var ('message',  $errmsg );

$imgset = $_CONF['layout_url'] .'/nexfile/images';
$page->set_var ('imgset', $imgset);

if ($cid != "") {
    $catname = DB_getItem($_TABLES['fm_category'], "name", "cid=$cid");
    $page->set_var ('beginblock',  COM_startBlock(sprintf($LANG_nexfile['msg41'],$catname)) );
} else {
    $page->set_var ('beginblock',  COM_startBlock(sprintf($LANG_nexfile['msg41'],$catname)) );
}


/* Setup Navbar */
$navbarMenu = array(
    $LANG_FM_NAVBAR['1']   => $_CONF['site_url'] .'/nexfile/index.php?op=newfile&cid=' .$cid,
    $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid='.$cid,
);

if ( $cid != "" AND fm_getPermission($cid,'admin')) {
    $navbarMenu[$LANG_FM_NAVBAR['10']] = $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=newcategory&cid='.$cid;
} elseif (SEC_hasRights('nexfile.admin')) {
    $navbarMenu[$LANG_FM_NAVBAR['10']] = $_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=newcategory';
}
if ($notifications > 0) {
    $LANG_FM_NAVBAR['3'] = sprintf($LANG_FM_NAVBAR['3'],$notifications);
    $navbarMenu[$LANG_FM_NAVBAR['3']]  = $_CONF['site_url'] .'/nexfile/index.php?op=notifications&cid='.$cid;
} else {
    $navbarMenu[$LANG_FM_NAVBAR['4']]  = $_CONF['site_url'] .'/nexfile/index.php?op=notifications&cid='.$cid;
}
if ($submissions > 0) {
    $LANG_FM_NAVBAR['6'] = sprintf($LANG_FM_NAVBAR['6'],$submissions);
    $navbarMenu[$LANG_FM_NAVBAR['6']]  = $_CONF['site_admin_url'] .'/plugins/nexfile/index.php';
}
if(function_exists(prj_getSessionProject)) {
    $projectid = prj_getSessionProject();
    if($projectid > 0) {
        $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
    }
}

$page->set_var('toolbar',fm_navbar($navbarMenu));

// Category Listing
$menu  = new HTML_TreeMenu();
$query = DB_QUERY("SELECT cid,pid,name,description from {$_TABLES['fm_category']} WHERE pid='0' ORDER BY CID");
while ( list($category,$pid,$name,$description) = DB_fetchARRAY($query)) {
    if (fm_getPermission($category,'view')) {
        $node[$category] = new HTML_TreeNode(array('text' => $name ,'link' => $_CONF['site_admin_url'] ."/plugins/nexfile/catman.php?cid=" .$category ,'icon' => 'folder.gif'));
        recursive_view($node[$category], $category); 
        $menu->addItem($node[$category]);
    }
}
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $_CONF['layout_url'] .'/nexpro/images/treemenu' ,'defaultClass' => 'treeMenuDefault'));

// If the Category ID is set - then show the Description information for the category
if ( $cid != "" AND DB_count($_TABLES['fm_category'], "cid", $cid) == 1)    {
    $query = DB_query("SELECT name,description,image FROM {$_TABLES['fm_category']} WHERE cid=$cid");
    list ($catname,$description,$catimage) = DB_fetchArray($query);
    if (strlen($catname) > 30) {
        $catname = substr($catname,0,30) . " ...";
    }
    if (fm_getPermission($cid,'admin') ) {
        $managelink = '<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=editcategory&cid='.$cid.'"><img src="' .$imgset. '/manage.gif" border="0" alt="'.$LANG_nexfile['msg01'].'" TITLE="'.$LANG_nexfile['msg01'].'"></a>&nbsp;&nbsp;';
        $permslink = '<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=permissions&cid='.$cid.'"><img src="' .$imgset. '/editperms.gif" border="0" alt="'.$LANG_nexfile['msg02'].'" TITLE="'.$LANG_nexfile['msg02'].'"></a>&nbsp;&nbsp;';
    }

    $page->set_var ('LANG_heading1', $LANG_nexfile['msg06']);
    $page->set_var ('LANG_heading2',  sprintf($LANG_nexfile['msg59'],$catname));
    if ($catimage != NULL AND $catimage != "" ) {
        $page->set_var ('catimage', '<img src="' . $_CONF['site_url'] .'/nexfile/data/' .$cid. '/' .$catimage. '" width="75" height="75">');
    }
    $page->set_var ('catdesc', $description);
    $page->set_var ('managelink', $managelink);
    $page->set_var ('permslink', $permslink);
    $page->set_var ('folderview', $treeMenu->toHTML());

} else {
    $page->set_var ('LANG_heading1', $LANG_nexfile['msg06']);
    $page->set_var ('folderview', $treeMenu->toHTML());
}

$page->set_var ('endblock', COM_endBlock() );
$page->set_var ('sitefooter', COM_siteFooter());

$page->parse ('output', 'directory');
echo  $page->finish ($page->get_var('output'));

?>