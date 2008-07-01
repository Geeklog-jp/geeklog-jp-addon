<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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
require_once($_CONF['path'] . 'plugins/nexfile/debug.php');  // Common Debug Code

if(empty($_USER['uid']) OR $_USER['uid'] == 1 ) {
    echo COM_siteHeader();
    echo COM_startBlock($LANG_FMERR['access1']);
    echo '<div style="text-align:center;padding-top:20px;">';
    echo $LANG_FMERR['access2'];
    echo "<p><button  onclick='javascript:history.go(-1)'>{$LANG_FM02['RETURN']}</button></p>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit();
} else {
    $userid = $_USER['uid'];
}

$mytextvars = array ('op', 'cid');
$_CLEAN = ppGetData($mytextvars);
$op = $_CLEAN['op'];
$cid = $_CLEAN['cid'];


// Function not used currently
function fm_getToplevelCatPid($cid) {
    global $_TABLES;
    $pid = DB_getItem($_TABLES['fm_category'], "pid", "cid=$cid");
    if ($pid != 0 ) {
        return fm_getToplevelCatPid($pid);
    }
}

switch ( $op ) {

case 'delete':              // Delete new file submission

    $id = $_GET['id'];
    $query = DB_query("SELECT fid,cid,tempname,fname,thumbnail,thumbtype,notify FROM {$_TABLES['fm_submissions']} WHERE id={$id}");
    list ($fid,$cid,$tempname,$fname,$thumbfname,$thumbtype,$notify) = DB_fetchArray($query);
    @unlink($_CONF['path_html'] . "nexfile/data/$cid/submissions/$tempname");
    if ($thumbtype == "file") {
        @unlink($_CONF['path_html'] . "nexfile/data/$cid/submissions/images/$thumbfname");
    }
    // Check for notification record for user
    if ($notify == 1) {
        fm_sendNotification($id,"3");
    }
    DB_query("DELETE FROM {$_TABLES['fm_submissions']} WHERE id={$id}");

    break;

case 'approve':
    $id = $_GET['id'];
    $query = DB_query("SELECT fid, cid, fname, tempname, title, description, ftype, size, version, version_note, thumbnail, thumbtype, submitter, date, version_ctl, notify, status FROM {$_TABLES['fm_submissions']} WHERE id=$id");
    list($fid, $cid, $fname, $tmpname, $title, $description, $ftype, $fsize, $version, $verNote, $thumbnail, $thumbtype, $submitter, $date, $versionmgmt, $notify, $status) = DB_fetchARRAY($query);

    // Check if there have been multiple submission requests for the same file and thus have same new version #
    if ($version == 1) {
        if ($ftype == 'file') {
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/' .$tmpname;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$fname;
            $rename =@rename ($curfile,$newfile);
        }
        if ($thumbtype == 'file') {
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/images/' .$thumbnail;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/images/' .$thumbnail;
            $rename =@rename ($curfile,$newfile);
        }
        DB_query("INSERT INTO {$_TABLES['fm_files']} (cid,fname,title,version,ftype,size,thumbnail,thumbtype,submitter,status,date,version_ctl) 
            VALUES ('$cid','$fname','$title','1','$ftype','$fsize','$thumbnail','$thumbtype','$submitter','$status','$date','$versionmgmt')");
        $newfid = DB_insertId();
        DB_query("INSERT INTO {$_TABLES['fm_detail']} (fid,description,platform,hits,rating,votes,comments) 
            VALUES ('$newfid','$description','',0,0,0,0)");
        DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,notes,size,date,uid,status) 
            VALUES ('$newfid','$uploadfilename','$ftype','1','$verNote','$fsize','$date','$submitter','$status')");

    } else {
        // Need to rename the current versioned file
        if ($ftype == 'file') {
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/' .$tmpname;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$fname;
            $rename = @rename ($curfile,$newfile);
        }
        DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,notes,size,date,uid,status) 
           VALUES ('$fid','$fname','$ftype','$version','$verNote','$fsize','$date','$submitter','1')");
        DB_query("UPDATE {$_TABLES['fm_files']} SET fname='$fname',version='$version', date='$date' WHERE fid=$fid");
        $newfid = $fid;
    }

    if ($newfid > 0) {
        // Send out notifications of approval
        fm_sendNotification($newfid,"2");
        DB_query("DELETE FROM {$_TABLES['fm_submissions']} WHERE id=$id");

        // Optionally add notification records and send out notifications to all users with view access to this new file
        if (DB_getItem($_TABLES['fm_category'], 'auto_create_notifications', "cid='$cid'") == 1) {
            fm_autoCreateNotifications($fid, $cid);
        }
        // Send out notifications of update to all subscribed users
        fm_sendNotification($newfid,"1");
    }
    break;

case 'editdetails':

    $query = DB_query("SELECT fid, cid, fname, tempname, title, description, ftype, version, version_note, submitter, thumbnail, thumbtype, version_ctl, notify, status FROM {$_TABLES['fm_submissions']} WHERE id=$id");
    list($fid, $cid, $fname, $tmpname, $title, $description, $ftype, $version, $verNote, $submitter, $thumbnail, $thumbtype, $versionCtl, $notify, $status) = DB_fetchARRAY($query);
    $cname = DB_getItem($_TABLES['fm_category'],"name","cid=$cid");
    $chkOnline = ($status == 1) ? "checked" : "";
    $chkVer     = ($versionCtl == 1) ? "checked" : "";
    $ftype = ($ftype == 'file') ? $LANG_FM02['LOCAL'] : $LANG_FM02['REMOTE'];
    $description = str_replace('<br />','',$description);
    $verNote = str_replace('<br />','',$verNote);
    if ($thumbnail != "") {
        if ($thumbtype == 'file') {
            $thumburl = $_CONF['site_url'] .'/nexfile/data/' .$cid. '/submissions/images/' .$thumbnail;
        } else {
            $thumburl = $thumbnail;
        }
    } else {
        $thumburl = "";
    }

    $vermgmchk = ($versionCtl == 1) ? "CHECKED" : "";
    $notifychk = ($notify == 1) ? "CHECKED" : "";
    $statuschk = ($status == 1) ? "CHECKED" : "";

    echo COM_siteHeader();
    echo  COM_startBlock($LANG_nexfile['msg44']);

    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['13']   => $_CONF['site_admin_url'] .'/plugins/nexfile/index.php'
    );
    if ($status == 0) {
        $navbarMenu[$LANG_FM_NAVBAR['12']] = $_CONF['site_url'] .'/nexfile/download.php?op=chksubmission&id=' .$id;
    } else {
        $navbarMenu[$LANG_FM_NAVBAR['11']] = $_CONF['site_url'] .'/nexfile/download.php?op=chksubmission&id=' .$id;
    }

    echo fm_navbar($navbarMenu);

    echo '<div class="clearboth"></div>';
    echo'<table width="100%" border="0" cellspacing="2" cellpadding="5" bgcolor="#EFEFEF">
        <form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
        <tr bgcolor="#EFEFEF">
            <td bgcolor=#BBBECE colspan="4" width="100%" align=left valign="top"><font size=2><b>' .$ftype.$LANG_nexfile['msg43'] .' </b></font></td>
        </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['FILENAME'].':</td>
        <td>&nbsp;'.$fname.'</td>
        <td nowrap>'.$LANG_FM02['TEMPNAME'].':</td>
        <td>&nbsp;'.$tmpname.'</td>
      </tr>';
    if ($version == 1) {
        echo '<tr  bgcolor="#FFFFFF"> 
        <td width="17%">'.$LANG_FM02['FILETITLE'].':</td>
        <td colspan="1"> <input name="filetitle" type="text" size="40" value="' .$title. '" maxlength="255"></td>';
        echo '<td colspan="2"><select name="category" style="text-indent: 0px;"><option value="0">'.$LANG_FM02['SELECT_CATEGORY'].'</option>' .fm_recursiveCatAdmin($cname). '</select></td></tr>';
    } else {
        echo '<tr bgcolor="#FFFFFF"> 
        <td width="17%">'.$LANG_FM02['FILETITLE'].':</td>
        <td colspan="1"> <input name="filetitle" type="text" size="40" value="' .$title. '" maxlength="255" DISABLED></td>
        <td colspan="2">'.$LANG_FM02['CAT'].': ' . $cname .'</td>';
    }
    if ($thumburl != "") {
        echo '<tr bgcolor="#FFFFFF">
        <td>'.$LANG_FM02['THUMBNAIL'].'</td>
        <td colspan="3" valign="bottom" align="left"><img src="'.$thumburl. '" border="0">&nbsp;&nbsp;<input type="checkbox" name="delthumb" value="1">&nbsp;Delete</td>
      </tr>';
    }
   if ($version <= 1) {
        echo '<tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['DESCRIPTION'].'</td>
        <td colspan="3"><textarea name="filedesc" cols="70" rows="3">'.$description.'</textarea></td>
         </tr>';
   } else {
        echo '<tr bgcolor="#FFFFFF"> 
        <td>Description</td>
        <td colspan="3"><textarea name="filedesc" cols="70" rows="3" DISABLED>'.$description.'</textarea></td>
         </tr>';
   }
   echo '<tr  bgcolor="#FFFFFF">
         <td>'.$LANG_FM02['VERSION_NOTE'].'</td>
        <td colspan="3"><textarea name="fileVerNote" cols="70" rows="3">'.$verNote.'</textarea></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="4"><label for="chk3">'.$LANG_FM02['EMAIL_NOTIFICATION2'].':&nbsp;<input type="checkbox" name="notification" value="1" id="chk3" '.$notifychk. '>&nbsp;'.$LANG_FM02['YES'].'</label></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="4" height="35" align="center" valign="middle">
            <input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="javascript:history.go(-1)">&nbsp;&nbsp;
            <input type="submit" value="'.$LANG_FM02['UPDATE'].'">
            <input type="hidden" name="op" value="submissionupdate">
            <input type="hidden" name="id" value="'.$id.'">
            <input type="hidden" name="version" value="'.$version.'"></td>
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

case 'submissionupdate':
    $_POST   = fm_cleandata($_POST);
    $fid              = $_POST['fid'];
    $filetitle        = $_POST['filetitle'];
    $cid              = $_POST['category'];
    $filedesc         = nl2br($_POST['filedesc']);
    $fileVerNote      = nl2br($_POST['fileVerNote']);
    $versionmgmt      = ($_POST['filevermgmt'] == 1) ? "1" : "0";
    $status           = ($_POST['status'] == 1) ? "1" : "0";
    $notification     = ($_POST['notification'] == 1) ? "1" : "0";
    $version          = $_POST['version'];
    $date = time();

    $query = DB_query("SELECT fid, cid, ftype, tempname, submitter, thumbnail, thumbtype,notify FROM {$_TABLES['fm_submissions']} WHERE id=$id");
    list ($fid,$currentCategory, $ftype,$tempname,$submitter, $thumbnail, $thumbtype,$notify) = DB_fetchArray($query);


    // if category is changed -  I need to move the file and attached image
    if ($cid != $currentCategory) {

        // Move the file
        if ($ftype == 'file') {
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$currentCategory. '/submissions/' .$tempname;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/' .$tempname;
            $rename =@rename ($curfile,$newfile);
        }

        // Move the image if one exists
        if ($thumbtype == 'file' AND $thumbnail !="") {        
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$currentCategory. '/submissions/images/' .$thumbnail;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/images/' .$thumbnail;
            $rename =@rename ($curfile,$newfile);
        }
    }

    if ($_POST['delthumb'] == "1") {
        if ($thumbtype == "file") {
            @unlink($_CONF['path_html'] . "nexfile/data/$cid/submissions/images/$thumbnail");
        }
        DB_query("UPDATE {$_TABLES['fm_submissions']} SET thumbnail='', thumbtype='' ");
    }

    // Don't update the title and description or change the category if this is a new version of an existing file
    if ($version == 1) {
        DB_query("UPDATE {$_TABLES['fm_submissions']} SET title='$filetitle', cid='$cid', description='$filedesc', version_note='$fileVerNote', version_ctl='$versionmgmt', status='$status', date='$date' WHERE id=$id");
    } else {
        DB_query("UPDATE {$_TABLES['fm_submissions']} SET version_note='$fileVerNote', version_ctl='$versionmgmt', status='$status', date='$date' WHERE id=$id");
    }

    if ($notify == 1) {
        $notifyon = true;
    } else {
        $notifyon = false;
    }

    if ( $notification AND !$notifyon ) {
        DB_query("UPDATE {$_TABLES['fm_submissions']} SET notify='1' WHERE id='$id' ");
    } elseif (!$notification and $notifyon) {
        DB_query("UPDATE {$_TABLES['fm_submissions']} SET notify='0' WHERE id='$id' ");
    }

    break;
}

/* Main Code */

echo COM_siteHeader();
echo  COM_startBlock($LANG_nexfile['msg44']);
$notifications = DB_COUNT($_TABLES['fm_notify'],"uid","{$userid}");

/* Setup Navbar */
$navbarMenu = array(
    $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php',
    $LANG_FM_NAVBAR['2']   => $_CONF['site_url'] .'/nexfile/index.php?op=latestfiles'
);

if ($notifications > 0) {
    $LANG_FM_NAVBAR['3'] = sprintf($LANG_FM_NAVBAR['3'],$notifications);
    $navbarMenu[$LANG_FM_NAVBAR['3']]  = $_CONF['site_url'] . "/nexfile/index.php?op=notifications{$parms}";
}
if(function_exists(prj_getSessionProject)) {
    $projectid = prj_getSessionProject();
    if($projectid > 0) {
        $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
    }
}
echo fm_navbar($navbarMenu);


// Determine if this user has any submitted files that they can approve
$query = DB_query("SELECT id,fid,cid,fname,submitter,date from {$_TABLES['fm_submissions']}");
$submissions = 0;
while ( list($id,$fid,$cid,$fname,$submitter,$date) = DB_fetchArray($query) ) {
    if (fm_getPermission($cid,'approval')) {
        $submissions++;
        if ($submissions == 1) {
            echo '<div class="clearboth"></div>';
            echo '<table border="0" cellpadding="5" cellspacing="2" width="100%" bgcolor="#EFEFEF">
                <form name="submissionapproval" action="'.$_SERVER['PHP_SELF'].'" method="post">
                <tr bgcolor="#FFFFFF">
                    <td bgcolor=#BBBECE colspan="4" width="100%" align="Left" valign="top"><font size=2><b>'.$LANG_nexfile['msg45'].'</b></font></td>
                </tr>
                <tr bgcolor="EFEFEF" align="center" valign="top">
                    <td align="left">'.$LANG_FM02['FILENAME'].'</td>
                    <td>'.$LANG_FM02['SUBMITTER'].'</td>
                    <td>'.$LANG_FM02['DATE'].'</td>
                    <td>'.$LANG_FM02['ACTION'].'</td>
               </tr>';
        }
        $profilelink = "<a href='{$_CONF['site_url']}/users.php?mode=profile&uid=$submitter'>";

        echo '<tr bgcolor="#FFFFFF" align="center">
          <td width="45%" align="left"><a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/index.php?op=editdetails&id=' .$id. '">' .$fname.'</a></td>
          <td>' . $profilelink . DB_getItem($_TABLES['users'],"username","uid=$submitter") .'</a></td>
          <td>' . strftime($_CONF['shortdate'] .' %H:%M', $date) .'</td>
          <td><a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/index.php?op=approve&id='.$id. '">'.$LANG_FM02['APPROVE'].'</a>&nbsp;&nbsp;&nbsp;<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/index.php?op=delete&id=' .$id. '">'.$LANG_FM02['DELETE'].'</a></td>
        </tr>
     </tr>';
    } 

}

if ($submissions > 0 ) {
    echo '</form></table><br><p>';
}


echo COM_endBlock();
echo COM_siteFooter();

?>