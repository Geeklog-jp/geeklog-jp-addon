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

include ('../lib-common.php');

require_once($_CONF['path_system'] . 'nexpro/classes/TreeMenu.php');
require_once($_CONF['path'] . 'plugins/nexfile/debug.php');  // Common Debug Code

if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

$mytextvars = array ('op', 'id', 'cid');
$_CLEAN = ppGetData($mytextvars);

$op = $_CLEAN['op'];
$cid = $_CLEAN['cid'];
$id = $_CLEAN['id'];
if (($cid != "")) {
    $cname = DB_getItem($_TABLES['fm_category'], "name", "cid=$cid");
}

function fm_recursiveView(&$node,$cid) { 
    global $_CONF,$_TABLES,$_FMCONF;
    $query = DB_QUERY("SELECT cid,pid,name,description FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0) {
            if (fm_getPermission($cid,'view')) {
                /* Get the number of files in this category */
                $filecount = DB_COUNT($_TABLES['fm_files'], 'cid', $cid);
                $subnode[$cid] = new HTML_TreeNode(array('text' => $name . "&nbsp;($filecount)",'link' => $_CONF['site_url'] ."/nexfile/index.php?cid=" .$cid ,'icon' => 'folder.gif'));
                fm_recursiveView($subnode[$cid], $cid);
                $node->addItem($subnode[$cid]);
            }
        } else { 
            if (fm_getPermission($cid,'view')) {
                /* Get the number of files in this category */
                $filecount = DB_COUNT($_TABLES['fm_files'], 'cid', $cid);
                $node->addItem(new HTML_TreeNode(array('text' => $name . "&nbsp;($filecount)", 'link' =>$_CONF['site_url'] ."/nexfile/index.php?cid=" .$cid , 'icon' => 'folder.gif')));
            }
        } 
    } 
}

if($_FMCONF['def_view'] == 'latestfiles' AND $op == '' AND $cid == "") {
    $op = "latestfiles";
}

function fm_displayNotifications($type) {
    global $_CONF,$_TABLES,$_USER,$_FMCONF,$LANG_FM02,$LANG_nexfile, $LANG_FM_NAVBAR,$userid,$cid;

    $submissions = fm_getSubmissionCnt();
    echo COM_siteHeader();
    echo COM_startBlock('Files and Categories being monitored');
    if ($cid != '') {
        $parms = "&cid=$cid";
    } else {
        $parms = '';
    }

    /* Setup Navbar */
    $ncount = DB_query("SELECT id FROM {$_TABLES['fm_notify']} WHERE uid=$userid AND cid!=0");
    $ecount = DB_query("SELECT id FROM {$_TABLES['fm_notify']} WHERE uid=$userid AND cid=0");
    $notifications = DB_numRows($ncount);
    $exceptions = DB_numRows($ecount);

    $navbarMenu = array();
    $navbarMenu[$LANG_FM_NAVBAR['5']] = "{$_CONF['site_url']}/nexfile/index.php?cid={$cid}";
    //only add the add file tab if they have perms
    if ($cid == 0) {    //latest files
        //check to see if user has perms to ANY directory
        if (fm_recursiveCatAddFileList('') != '') {
            $navbarMenu[$LANG_FM_NAVBAR['1']] = "{$_CONF['site_url']}/nexfile/index.php?op=newfile{$parms}";
        }
    }
    else {
        if ( fm_getPermission($cid, array ('admin','upload','upload_dir')) ) {
            $navbarMenu[$LANG_FM_NAVBAR['1']] = "{$_CONF['site_url']}/nexfile/index.php?op=newfile{$parms}";
        }
    }

    if ($notifications > 0) {
        $LANG_FM_NAVBAR['3'] = sprintf($LANG_FM_NAVBAR['3'],$notifications);
        $navbarMenu[$LANG_FM_NAVBAR['3']]  = $_CONF['site_url'] . "/nexfile/index.php?op=notifications{$parms}";
    }
    else {
        $navbarMenu[$LANG_FM_NAVBAR['4']]  = $_CONF['site_url'] . "/nexfile/index.php?op=notifications{$parms}";
    }
    if ($exceptions > 0) {
        $LANG_FM_NAVBAR['18'] = sprintf($LANG_FM_NAVBAR['18'],$exceptions);
        $navbarMenu[$LANG_FM_NAVBAR['18']]  = $_CONF['site_url'] . "/nexfile/index.php?op=exceptions{$parms}";
    }
    else {
        $navbarMenu[$LANG_FM_NAVBAR['19']]  = $_CONF['site_url'] . "/nexfile/index.php?op=exceptions{$parms}";
    }

    $navbarMenu[$LANG_FM_NAVBAR['2']] = "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles";
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
    echo fm_navbar($navbarMenu);

    // Determine if this user has subscribed to any notifications
    $sql  = "SELECT notification.id,notification.fid,notification.cid,file.fname,category.name,notification.date ";
    $sql .= "FROM {$_TABLES['fm_notify']} notification ";
    $sql .= "LEFT JOIN {$_TABLES['fm_files']} file ON notification.fid=file.fid ";
    if ($type == 'notifications') {
        $sql .= "LEFT JOIN {$_TABLES['fm_category']} category ON notification.cid=category.cid ";
    }
    else {
        $sql .= "LEFT JOIN {$_TABLES['fm_category']} category ON file.cid=category.cid ";
    }
    $sql .= "WHERE uid={$userid} AND notification.cid";
    $sql .= ($type=='notifications') ? '!=0':'=0';
    $query = DB_query($sql);

    $notifications = 0;
    echo '<div class="clearboth"></div>';
    echo '<table border="0" cellspacing="2" cellpadding="5" width="100%" bgcolor="#EFEFEF">
        <form name="submissionapproval" action="'.$_SERVER['PHP_SELF'].'" method="post">
        <tr bgcolor="#FFFFFF">
            <td bgcolor=#BBBECE colspan="4" width="100%" align="Left" valign="top"><font size=2><b>'.$LANG_nexfile['msg46'].'</b></font></td>
        </tr>
        <tr bgcolor="#EFEFEF" align="center" valign="top">
            <td>'.$LANG_FM02['CATEGORY'].'</td>
            <td>'.$LANG_FM02['FILENAME'].'</td>
            <td>'.$LANG_FM02['DATEADDED'].'</td>
            <td>'.$LANG_FM02['ACTION'].'</td>
       </tr>';
    while ( list($id,$fid,$cid,$fname,$cname,$date) = DB_fetchArray($query) ) {
        $notifications++;
        $fname = ($fname != "") ? $fname : $LANG_FM02['ALLFILES'];
        $fname = (empty($fid)) ? $LANG_FM02['ALLFILES'] : $fname;
        echo '<tr bgcolor="#FFFFFF" align="center">
                  <td width="30%" align="left">' .$cname.'</td>
                  <td width="30%" align="left">' .$fname.'</td>
                  <td>' . strftime($_CONF['shortdate'], $date) .'</td>
                  <td><a href="' .$_CONF['site_url'] .'/nexfile/index.php?op=delnotification&id='.$id. '">'.$LANG_FM02['DELETE'].'</a>
                      <img src="' .$_FMCONF['imagesurl'] .'trash.gif">
                  </td>
             </tr>';
    } 
    echo '</form></table><br><p>';

}


if ($cid != '') {
    $parms = "&cid=$cid";
} else {
    $parms = '';
}

switch ( $op ) {

case 'savefile':
    $_POST = fm_cleandata($_POST);
    $filetitle      = $_POST['filetitle'];
    $cid            = $_POST['category'];

    // Need to consider a better check that a category has been selected - possibly using JS validation with the form
    if( $cid == "0" OR $filetitle == '' ) {
        echo COM_siteHeader();
        echo COM_startBlock();
        echo $LANG_FMERR['upload1'];
        echo COM_endBlock();
        echo COM_siteFooter();
        exit;
    }

    $filedesc     = nl2br($_POST['filedesc']);
    $fileVerNote  = nl2br($_POST['fileVerNote']);
    $versionmgmt  = COM_applyFilter($_POST['filevermgmt'], true);
    $status       = COM_applyFilter($_POST['status'], true);
    $notification = $_POST['notification'];
    $fileurl      = $_POST['fileurl'];
    $thumburl     = $_POST['thumburl'];
    $date = time();

    /* Note Adding files which have a quote in their name like `blaine's file`
    *  are causing a problem where the filename is not being passed in via $_FILES
    *  correctly if magic_quotes_gpc is on. Disabling allows the name to be
    *  passed in correctly but then I need to use addslashes on the name
    *  just before the SQL INSERT
    *
    *  Jul 2005: This appears to just be an issue with PHP 5.
    *  Characters before the ' in the name are stripped out
    *  Only if magic_quotes is on - very odd sounds like a bug
    *
    */

    $uploadfilename     = $_FILES['fileupload']['name'];
    $uploadthumbname    = $_FILES['thumbupload']['name'];

    $pos = strrpos($uploadfilename,'.') + 1;
    $fileExtension = strtolower(substr($uploadfilename, $pos));

    // In case user is uploading both a main file and thumbnail image
    // The upload class will upload both files at once and I don't want that
    // I need to split the POST_FILES array so that only 1 file is uploaded at once and re-initialize the array
    $file1['fileupload']     = $_FILES['fileupload'];
    $file2['thumbupload']    = $_FILES['thumbupload'];
    $_FILES = "";

   if (fm_getPermission($cid,'upload_direct')) {
       // if this is a upload file then use the upload class - else set type to remote and add the URL
        if ($fileurl != "") {
            $filetype = "url";

            $fsize = fm_getFilesize($fileurl);
            if ($_FMCONF['verifyRemoteURL']) {
                if ($fsize == 0) {
                    $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload8']}";
                    break;
                }
            }

            $ext = substr($fileurl, stripos($fileurl, '.') + 1);
            if (!in_array('.'.$ext, $_FMCONF['allowablefiletypes'])) {
                $errmsg .= $LANG_FMERR['upload9'] . ' ' . $ext;
                break;
            }

            DB_query("INSERT INTO {$_TABLES['fm_files']} (cid,fname,title,version,ftype,size,submitter,status,date,version_ctl)
                VALUES ('$cid','$fileurl','$filetitle','1','url','$fsize','$userid','$status','$date','$versionmgmt')");
            $fid = DB_insertId();
            DB_query("INSERT INTO {$_TABLES['fm_detail']} (fid,description,platform,hits,rating,votes,comments)
                VALUES ('$fid','$filedesc','','0','0','0','0')");
            DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,size,notes,date,uid,status)
                VALUES ('$fid','$fileurl','url','1','$fsize','$fileVerNote','$date','$userid','1')");


            // Optionally add notification records and send out notifications to all users with view access to this new file
            if (DB_getItem($_TABLES['fm_category'], 'auto_create_notifications', "cid='$cid'") == 1) {
                fm_autoCreateNotifications($fid, $cid);
            }

            if ($_POST['notification'] == 1) {
                //DB_query("UPDATE {$_TABLES['fm_categories']} SET auto_create_notifications = 1 WHERE cid = $cid");
                // Send out notifications of update
                fm_sendNotification($fid);
                fm_updateAuditLog("Direct upload of Remote File ID: $fid, in Category: $cid");
            }

        } elseif ($uploadfilename != "") {

            // Place the first file info into POST_FILES array for processing by the upload class
            $_FILES['fileupload'] = $file1['fileupload'];

            $filesize =  $_FILES['fileupload']['size'];
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid;
            if ( fm_uploadfile($directory,$uploadfilename) ) {
                if (!get_magic_quotes_gpc()) {
                    $uploadfilename = addslashes($uploadfilename);
                }
                DB_query("INSERT INTO {$_TABLES['fm_files']} (cid,fname,title,version,ftype,size,submitter,status,date,version_ctl)
                    VALUES ('$cid','$uploadfilename','$filetitle','1','file','$filesize','$userid','$status','$date','$versionmgmt')");

                $fid = DB_insertId();  // New File ID

                DB_query("INSERT INTO {$_TABLES['fm_detail']} (fid,description,platform,hits,rating,votes,comments)
                    VALUES ('$fid','$filedesc','','0','0','0','0')");
                DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,notes,size,date,uid,status)
                    VALUES ('$fid','$uploadfilename','file','1','$fileVerNote','$filesize','$date','$userid','1')");

                // Optionally add notification records and send out notifications to all users with view access to this new file
                if (DB_getItem($_TABLES['fm_category'], 'auto_create_notifications', "cid='$cid'") == 1) {
                    fm_autoCreateNotifications($fid, $cid);
                }
                // Send out notifications of update
                if ($_POST['notification'] == 1) {
                    fm_sendNotification($fid);
                }
                fm_updateAuditLog("Direct upload of File ID: $fid, in Category: $cid");

            } else {
                $errmsg = $GLOBALS['fm_errmsg'];
                break;
            }

        } else {
            $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload3']}";
            break;
        }
        // Optional -> If new upload was added, check for an uploaded thumbnail  - either image or remote file
        if ($fid > 0 AND $thumburl != "") {
            if (!strpos(' '.$thumburl, 'http')) {   // Add a space because it will return 0 as well for position 0 if found.
                $thumburl = 'http://' .$thumburl;
            }
            if (fm_verifyURL($thumburl)) {
                DB_query("UPDATE {$_TABLES['fm_files']} SET thumbnail='$thumburl', thumbtype='url' WHERE fid='$fid' ");
            } else {
                $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload4']}";
                break;
            }

        } elseif ($fid > 0 AND $uploadthumbname != "") {
            $_FILES = "";    // Re-Initialize the array so there will only be the one file uploaded
            $_FILES['thumbupload'] = $file2['thumbupload'];    // Place the second file info into POST_FILES array for processing by the upload class
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid.'/images';
            if ( fm_uploadfile($directory,$uploadthumbname,'image') ) {
                DB_query("UPDATE {$_TABLES['fm_files']} SET thumbnail='$uploadthumbname', thumbtype='file' WHERE fid='$fid' ");
            } else {
                 $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload7']}";
            }

        }

   } else {

       // Upload will go into submission queue to be approved
       // if this is a upload file then use the upload class - else set type to remote and add the URL
        if ($fileurl != "") {
            $filetype = "URL";
            /*
            if (!strpos(' '.$fileurl, 'http')) {   // Add a space because it will return 0 as well for position 0 if found.
                $fileurl = 'http://' .$fileurl;
            }
            */
            if ($_FMCONF['verifyRemoteURL']) {
                if (!fm_verifyURL($fileurl)) {
                    $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload5']}";
                    break;
                }
            }
            DB_query("INSERT INTO {$_TABLES['fm_submissions']} (cid,fname,title,ftype,description,version_note,size,submitter,date) 
                VALUES ('$cid','$fileurl','$filetitle','url','$filedesc','$fileVerNote','0','$userid','$date')");
            $sid = DB_insertId();

            // Determine if any users that have upload.admin permission for this category 
            // or nexfile admin rights should be notified of new file awaiting approval
            fm_sendAdminApprovalNoftications($cid,$sid);

            fm_updateAuditLog("New upload submission, in Category: $cid");

        } elseif ($uploadfilename != "") {
            $_FILES['fileupload'] = $file1['fileupload'];    // Place the first file info into POST_FILES array for processing by the upload class
            $filesize =  $_FILES['fileupload']['size'];
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid.'/submissions';

            // Generate random file name for newly submitted file to hide it until approved
            $charset = "abcdefghijklmnopqrstuvwxyz";
            for ($i=0; $i<12; $i++) $random_name .= $charset[(mt_rand(0,(strlen($charset)-1)))];
            $random_name .= '.' .$fileExtension;

            if ( fm_uploadfile($directory,$random_name) ) {
                if (!get_magic_quotes_gpc()) {
                    $uploadfilename = addslashes($uploadfilename);
                }
                DB_query("INSERT INTO {$_TABLES['fm_submissions']} (cid,fname,tempname,title,ftype,description,version_note,size,submitter,date) 
                    VALUES ('$cid','$uploadfilename','$random_name','$filetitle','file','$filedesc','$fileVerNote','$filesize','$userid','$date')");
                $sid = DB_insertId();

                // Determine if any users that have upload.admin permission for this category 
                // or nexfile admin rights should be notified of new file awaiting approval
                fm_sendAdminApprovalNofications($cid,$sid);

                fm_updateAuditLog("New upload submission, in Category: $cid");
            }

        } else {
            $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload6']}";
            break;
        }

        // Optional -> If new upload was added, check for an uploaded thumbnail  - either image or remote file
        if ($sid > 0 AND $thumburl != "") {
            if (!strpos(' '.$thumburl, 'http')) {   // Add a space because it will return 0 as well for position 0 if found.
                $thumburl = 'http://' .$thumburl;
            }
            if (fm_verifyURL($thumburl)) {
                DB_query("UPDATE {$_TABLES['fm_submissions']} SET thumbnail='$thumburl', thumbtype='url' WHERE id='$sid' ");
            } else {
                $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload4']}";
                break;
            }

        } elseif ($sid > 0 AND $uploadthumbname != "") {
            $_FILES = "";    // Re-Initialize the array so there will only be the one file uploaded
            $_FILES['thumbupload'] = $file2['thumbupload'];    // Place the second file info into POST_FILES array for processing by the upload class
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid.'/submissions/images';
            if ( fm_uploadfile($directory,$uploadthumbname,'image') ) {
                DB_query("UPDATE {$_TABLES['fm_submissions']} SET thumbnail='$uploadthumbname', thumbtype='file' WHERE id='$sid' ");
            } else {
                 $errmsg .= $GLOBALS['fm_errmsg'] . "<br>{$LANG_FMERR['upload7']}";
            }

        }
        if ($userid > 1 AND $notification) {
            DB_query("UPDATE {$_TABLES['fm_submissions']} SET notify='1' WHERE id='$sid' ");
        }

    }
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            echo COM_refresh($_CONF['site_url'] . "/nexproject/viewproject.php?pid=$projectid");
            exit;
        }
    }

    break;


case 'newfile':

    echo COM_siteHeader();
    echo COM_startBlock("nexFile Add File Admin");
    $parms = ($cid != '') ? "?cid=$cid": '';

    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] ."/nexfile/index.php{$parms}",
        $LANG_FM_NAVBAR['2']   => $_CONF['site_url'] .'/nexfile/index.php?op=latestfiles'
    );
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
        }
    }
    echo fm_navbar($navbarMenu);

    //$cname = DB_getItem($_TABLES['fm_category'], 'name', "cid={$_CLEAN['cid']}");
    echo '<script>
    function fm_updateFileName (changedField) {
        var ft = document.newfile.filetitle;

        if (ft.value == \'\') {
            var pos2 = changedField.value.lastIndexOf(\'.\');
            var pos1 = changedField.value.lastIndexOf(\'\\\\\');
            if (pos1 == -1) {
                pos1 = changedField.value.lastIndexOf(\'/\');
            }

            if (pos1 >= 0 && pos2 >= 0) {
                pos1++;
                ft.value = changedField.value.substr(pos1, pos2-pos1);
            }
        }
    }

    function fmtogglediv1(mode) {
        obj1 = document.getElementById("localfile");
        obj2 = document.getElementById("remotefile");
        if (mode == "remote" ) {
            obj1.style.display = "none";
            obj2.style.display = "";
            document.all.newfile.encoding = "application/x-www-form-urlencoded";
        } else {
            obj1.style.display = "";
            obj2.style.display = "none";
            document.all.newfile.encoding = "multipart/form-data";
        }
    }

    function fmtogglediv2(img) {
        obj = document.getElementById("thumbnail");
        if (obj.style.display == "" ) {
            obj.style.display = "none";
            img.src="'.$_CONF['layout_url'].'/nexfile/images/expand.gif";
            img.title="'.$LANG_nexfile[msg54].'";
        } else {
            obj.style.display = "";
            img.src="'.$_CONF['layout_url'].'/nexfile/images/collapse.gif";
            img.title="'.$LANG_nexfile[msg55].'";
        }
    }
    </script>';


    echo '<div class="clearboth"></div>';
    echo'<table width="100%" border="0" cellspacing="2" cellpadding="5" bgcolor="#EFEFEF">
        <form name="newfile" action="'.$_SERVER['PHP_SELF'].$parms.'" method="post" enctype="multipart/form-data">
        <tr bgcolor="#FFFFFF">
            <td bgcolor=#BBBECE colspan="4" width="100%" align=left valign="top"><font size=2><b>'.$LANG_FM02['ADDFILE'].'</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF">
        <td width="17%">'.$LANG_FM02['FILENAME'].':</td>
        <td colspan="1"> <input name="filetitle" type="text" size="40" maxlength="255"></td>
        <td colspan="2"><select name="category"  style="text-indent: 0px;"><option value="0">'.$LANG_FM02['SELECT_CATEGORY'].'</option>' . fm_recursiveCatAddFileList($cname) . '</select></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['DESCRIPTION'].'</td>
        <td colspan="3"><textarea name="filedesc" cols="70" rows="3"></textarea></td>
      </tr>
      <tr bgcolor="#FFFFFF">
            <td>'.$LANG_FM02['UPLOADFILE'].'<div style="padding:10px;"><span style="vertical-align:bottom;"><img src="'.$_CONF['layout_url'].'/nexfile/images/expand.gif" TITLE="'.$LANG_nexfile['msg54'].'" border="0"  onClick=\'fmtogglediv2(this);\'></span>&nbsp;Thumbnail</div></td>
            <td colspan="3">
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tr id="localfile" bgcolor="#FFFFFF" style="display:"> 
                        <td width="66%">Browse to select your file to upload<div style="padding:5px;"><input type="file" name="fileupload" onChange="fm_updateFileName(this);" size="25"><span style="padding: 0 10 0 20px;"><a class="nexfilelinks" href="#"  TITLE="'.$LANG_nexfile[msg52].'" onClick=\'fmtogglediv1("remote");\'>Remote File</a></span></div></td>
                    </tr>
                    <tr id="remotefile" bgcolor="#FFFFFF" style="display:none;"> 
                       <td width="66%">Type the URL of the file to reference<div style="padding:5px;"><input name="fileurl" type="text" onChange="fm_updateFileName(this);" size="50" maxlength="255"><span style="padding: 0 10 0 20px;"><a class="nexfilelinks" href="#"  TITLE="'.$LANG_nexfile[msg53].'" onClick=\'fmtogglediv1("local");\'>Local File</a></span></div></td>
                  </tr>
                </table>
            </td>
      </tr>
      <tr id="thumbnail" bgcolor="#FFFFFF" style="display:none;">
            <td colspan="4">
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tr bgcolor="#FFFFFF""> 
                        <td>'.$LANG_FM02['THUMBNAIL'].'</td>
                        <td colspan="3" width="66%"><input type="file" name="thumbupload"  size="25"></td>
                    </tr>
                   <tr bgcolor="#FFFFFF"> 
                       <td>&nbsp;or URL</td>
                       <td colspan="3" width="66%"><input name="thumburl" type="text" size="50" maxlength="255"></td>
                  </tr>
                 </table>
            </td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['VERSION_NOTE'].'</td>
        <td colspan="3"><textarea name="fileVerNote" cols="70" rows="3"></textarea></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td colspan="4"><label for="chk3">'.$LANG_FM02['EMAIL_NOTIFICATION'].':&nbsp;<input type="checkbox" name="notification" value="1" id="chk3"checked>&nbsp;'.$LANG_FM02['YES'].'</label></td>
      </tr>
       <tr bgcolor="#FFFFFF">';
    echo '<td colspan="4" height="35" align="center" valign="middle">
            <input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="javascript:history.go(-1)">&nbsp;&nbsp;
            <input type="submit" value="Submit">
            <input type="hidden" name="op" value="savefile"></td>
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

case 'notifications':
	if ($userid > 1) {
	    fm_displayNotifications('notifications');
	    exit;
    } else {
        $errmsg = $LANG_FMERR['err1'];
    }
    break;

case 'exceptions':
    if ($userid > 1) {
        fm_displayNotifications('exceptions');
        exit;
    } else {
        $errmsg = $LANG_FMERR['err1'];
    }
    break;

case 'addnotification':
    if ($userid > 1 AND $_GET['id'] != "" ) {
        $catname = DB_getItem($_TABLES['fm_category'],"name","cid=$id");
        $date = time();
        if (DB_Count($_TABLES['fm_notify'], array('cid','uid','fid'), array($id,$userid,'0') ) > 0) {
            $errmsg =  $LANG_FMERR['notify1'];
        } else {
            DB_query("INSERT INTO {$_TABLES['fm_notify']} (fid,cid,uid,date) VALUES ('0','$id','{$userid}','$date')");
            $errmsg =  sprintf($LANG_FMERR['notify2'],$catname);
            fm_updateAuditLog("New notification record for FID: $fid and Category: $cid");
        }
    } else {
        $errmsg =  $LANG_FMERR['err2'];
    }
    break;

case 'delnotification' :
    if ($userid > 1 AND $_GET['id'] != "" ) {
        
        if (DB_getItem($_TABLES['fm_notify'], "uid","id={$_GET['id']}") == $userid ) {
            DB_query("DELETE FROM {$_TABLES['fm_notify']} WHERE id={$_GET['id']}");
            $errmsg =  sprintf($LANG_FMERR['notify3'], $_GET['id']);
            fm_updateAuditLog("Deleted notification record: " . $_GET['id'] );
        } else {
            $errmsg = $LANG_FMERR['notify4'];
        }
    }
    if ($_GET['viewcat'] != "") {
        echo COM_refresh($_CONF['site_url'] . "/nexfile/index.php?cid=" . $_GET['viewcat']); 
        exit;
    } else {
        fm_displayNotifications('notifications');
        exit;
    }
    break;

} 


/*
// If the Project Plugin is installed - then clear the project session record
// Used to add the return to project navigation - clears if they decide to stay in the document area
if(function_exists(prj_clrSession)) {
    prj_clrSession();
}
*/

$submissions = fm_getSubmissionCnt();
$notifications = DB_count($_TABLES['fm_notify'], 'uid', $userid);

$page = new Template($_CONF['path_layout'] . 'nexfile');
$page->set_file (array ('directory'=>'filedirectory_view.thtml','records' => 'filelistrow.thtml'));
$page->set_var ('siteheader', COM_siteHeader());
$page->set_var ('beginblock', COM_startBlock());
$page->set_var ('siteurl', $_CONF['site_url']);
$page->set_var ('message', $errmsg);


/* Setup Navbar */
$navbarMenu = array();

//only add the add file link if the user has the right perms
if ($cid == 0) {    //latest files
    //check to see if user has perms to ANY directory
    if (fm_recursiveCatAddFileList('') != '') {
        $navbarMenu[$LANG_FM_NAVBAR['1']] = "{$_CONF['site_url']}/nexfile/index.php?op=newfile{$parms}";
    }
}
else {
    if ( fm_getPermission($cid, array ('admin','upload','upload_dir')) ) {
        $navbarMenu[$LANG_FM_NAVBAR['1']] = "{$_CONF['site_url']}/nexfile/index.php?op=newfile{$parms}";
    }
}
$navbarMenu[$LANG_FM_NAVBAR['2']] = "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles";

if ($userid > 1) {
    if ($notifications > 0) {
        $LANG_FM_NAVBAR['3'] = sprintf($LANG_FM_NAVBAR['3'],$notifications);
        $navbarMenu[$LANG_FM_NAVBAR['3']]  = $_CONF['site_url'] . "/nexfile/index.php?op=notifications{$parms}";
    } else {
        $navbarMenu[$LANG_FM_NAVBAR['4']]  = $_CONF['site_url'] . "/nexfile/index.php?op=notifications{$parms}";
    }
    if ($submissions > 0) {
        $LANG_FM_NAVBAR['6'] = sprintf($LANG_FM_NAVBAR['6'],$submissions);
        $navbarMenu[$LANG_FM_NAVBAR['6']]  = $_CONF['site_admin_url'] .'/plugins/nexfile/index.php';
    }
    if (SEC_hasRights('nexfile.edit')) {
        $navbarMenu[$LANG_FM_NAVBAR['10']]  = $_CONF['site_admin_url'] . "/plugins/nexfile/catman.php?op=newcategory{$parms}";
    }
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] . '/nexproject/viewproject.php?pid=' .$projectid;
        }
    }
}

$page->set_var('toolbar',fm_navbar($navbarMenu));

// Category Listing
$menu  = new HTML_TreeMenu();
$query = DB_QUERY("SELECT cid,pid,name,description from {$_TABLES['fm_category']} WHERE pid='0' ORDER BY CID");
while ( list($category,$pid,$name,$description) = DB_fetchARRAY($query)) {
    if (fm_getPermission($category,'view')) {
        /* Get the number of files in this category */
        $filecount = DB_COUNT($_TABLES['fm_files'], 'cid', $category);
        $node[$category] = new HTML_TreeNode(array('text' => $name ."&nbsp;($filecount)" ,'link' => $_CONF['site_url'] ."/nexfile/index.php?cid=" .$category ,'icon' => 'folder.gif'));
        fm_recursiveView($node[$category], $category); 
        $menu->addItem($node[$category]);
    }
}
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $_CONF['layout_url'] .'/nexpro/images/treemenu' ,'defaultClass' => 'treeMenuDefault'));

$imgset = $_CONF['layout_url'] .'/nexfile/images';

if ($cid != "") {

    if (SEC_hasRights("nexfile.admin") OR (fm_getPermission($cid,'admin')) ) {
        $managelink = '<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=editcategory&cid='.$cid.'"><img src="' .$imgset. '/manage.gif" border="0" ALT="'.$LANG_nexfile['msg01'].'" TITLE="'.$LANG_nexfile['msg01'].'"></a>&nbsp;';
        $managelink .= '<img src="' .$_CONF['layout_url']. '/images/bar.gif">&nbsp;';
        $permslink = '<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/catman.php?op=permissions&cid='.$cid.'"><img src="' .$imgset. '/editperms.gif" border="0" ALT="'.$LANG_nexfile['msg02'].'" TITLE="'.$LANG_nexfile['msg02'].'"></a>&nbsp;';
        $permslink .= '<img src="' .$_CONF['layout_url']. '/images/bar.gif">&nbsp;';
    }
	if ($userid > 1) {
        if (DB_count($_TABLES['fm_notify'],array('uid','cid','fid'), array($_USER['uid'],$cid,'0')) > 0 ) {
            $notifyrecid = DB_getItem($_TABLES['fm_notify'], "id", "cid=$cid AND uid=$userid");
            $subscribelink = '<a href="' .$_CONF['site_url'] .'/nexfile/index.php?op=delnotification&id='.$notifyrecid.'&viewcat='.$cid.'"><img src="' .$imgset. '/notify_on.gif" border="0" alt="'.$LANG_nexfile['msg03'].'" TITLE="'.$LANG_nexfile['msg03'].'"></a>&nbsp;&nbsp;';
        } else {
            $subscribelink = '<a href="' .$_CONF['site_url'] .'/nexfile/index.php?op=addnotification&id='.$cid.'&cid='.$cid.'"><img src="' .$imgset. '/subscribe.gif" border="0" alt="'.$LANG_nexfile['msg04'].'" TITLE="'.$LANG_nexfile['msg04'].'"></a>&nbsp;&nbsp;';
        }
    } else {
        $subscribelink = "";
    }

    $query = DB_QUERY("SELECT name,description,image from {$_TABLES['fm_category']} WHERE cid={$cid}");
    list ($catname,$catdesc,$catimage) = DB_fetchArray($query);
    if (strlen($catname) > 35) {
        $catname = substr($catname,0,35) . " ...";
    }
} elseif (SEC_hasRights("nexfile.admin"))  {
    $managelink = '<a href="' .$_CONF['site_admin_url'] .'/plugins/nexfile/catman.php"><img src="' .$imgset. '/manage.gif" border="0" ALT="'.$LANG_nexfile['msg05'].'" TITLE="'.$LANG_nexfile['msg05'].'"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
}

$page->set_var ('LANG_heading1', $LANG_nexfile['msg06']);
if ($op == "latestfiles") {
    $page->set_var ('LANG_heading2', sprintf($LANG_nexfile['msg07'],$_FMCONF['shownewlimit']));
} else {
    $page->set_var ('LANG_heading2',  sprintf($LANG_nexfile['msg59'],$catname));
}

$page->set_var ('folderview', $treeMenu->toHTML());
if ($catimage != NULL AND $catimage != "" ) {
    $page->set_var ('catimage', '<img src="' . $_CONF['site_url'] .'/nexfile/data/' .$cid. '/' .$catimage. '" width="75" height="75">');
}
$page->set_var ('catdesc', nl2br($catdesc));
$page->set_var ('imgset', $imgset);
$page->set_var ('subscribelink', $subscribelink);
$page->set_var ('managelink', $managelink);
$page->set_var ('permslink', $permslink);
$page->set_var ('LANG_DOWNLOAD_MESSAGE',$LANG_nexfile['msg61']);


// File Listing for Category
// If the Category ID is set and there are files - then show the files in the right area of the display
if ( ($cid !="" and DB_getItem($_TABLES['fm_files'], "fid", "cid=$cid")) OR ($op =="latestfiles")) {

    if ($op == "latestfiles") {
        $query = DB_query("SELECT file.fid as fid,cid,title,fname,date,version,submitter,status,size,description FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_detail']} detail ON file.fid=detail.fid ORDER BY date DESC");
        $count = 1;
    } else {
        $query = DB_query("SELECT file.fid as fid,cid,title,fname,date,version,submitter,status,size,description FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_detail']} detail ON file.fid=detail.fid WHERE cid=$cid ORDER BY date DESC");
    }
    while ( list($fid,$cid,$title,$fname,$date,$version,$submitter,$status,$size,$description) = DB_fetchARRAY($query)) {
        if (fm_getPermission($cid,'view')) {
            $pos = strrpos($fname,'.') + 1;
            $ext = strtolower(substr($fname, $pos));
            if (array_key_exists($ext, $_FMCONF['inconlib'] )) {
                $icon = $_FMCONF['imagesurl'] . $_FMCONF['inconlib'][$ext];
            } else {
                $icon = $_FMCONF['imagesurl'] . $_FMCONF['inconlib']['none'];
            }

            if ($status == 2) {
                $statUserUid = DB_getItem($_TABLES['fm_files'], "status_changedby_uid", "fid=$fid");
                $statUser = DB_getItem($_TABLES['users'], "username", "uid=$statUserUid");
                $title .= '&nbsp;<img src="' .$_FMCONF['imagesurl'] . 'padlock.gif" border="0" TITLE="' .sprintf($LANG_nexfile['msg08'],$statUser).'">';
            }

            if (isset($_FMCONF['dateformat'])) {
                $longdate = strftime( $_FMCONF['dateformat'],$date );
            } else {
                $longdate = COM_getUserDateTimeFormat($date);
                $longdate = $longdate[0];
            }
            $size = intval($size);
            if ($size/1000000 > 1) {
                $size = round($size/1000000,2) . " MB";
            } elseif ($size/1000 > 1) {
                $size = round($size/1000,2) . " KB";
            } else {
                $size = round($size,2) . " Bytes";
            }
            $fileinfo = '';
            if ($description != '') {
                $fileinfo = "$description<br>";
            }

            $fileinfo .= sprintf($LANG_nexfile['msg51'],$longdate,$version,$size);
            if ($op == "latestfiles") {
                $category = DB_getItem($_TABLES['fm_category'],"name","cid='{$cid}'");
                $fileinfo .= "<br>Category:$category";
            }
            $page->set_var ('fileicon', $icon);
            $page->set_var ('filetitle', $title);
            $page->set_var ('fileinfo', $fileinfo);
            $page->set_var ('fileid', $fid);
            $page->set_var ('LANG_DETAILS', $LANG_FM02['DETAILS']);
            $page->parse('file_records','records',true);

            if ($op == "latestfiles") {
                if ($count >= $_FMCONF['shownewlimit']) {
                    break;
                } else {
                    $count++;
                }
            }
        }
    }

}

$page->set_var ('endblock', COM_endBlock() );
$page->set_var ('sitefooter', COM_siteFooter());

$page->parse ('output', 'directory');
echo  $page->finish ($page->get_var('output'));

?>