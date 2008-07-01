<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | details.php                                                               |
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
require_once($_CONF['path'] . 'plugins/nexfile/debug.php');  // Common Debug Code

// Check if File ID is set - need to check both GET and POST
if (isset($_GET['fid']) OR  isset($_POST['fid'])) {
    if ($_GET['fid'] != "" ) {
        $fid = $_GET['fid'];
    } else {
        $fid = $_POST['fid'];
    }
    // Determine Category cid if not passed in - now that we have a fileid
    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");

    if (!fm_getPermission($cid, 'view')) {
        echo COM_siteHeader();
        echo COM_startBlock('Access Denied');
        echo 'You do not have access rights to this file.  Your attempt has been logged.';
        echo COM_endBlock();
        echo COM_siteFooter();
    }

} elseif (isset($_GET['id'])) {
    // Check if ID is set from the Project View page - it needs to pass ID
    $fid = $_GET['id'];
} else {
    echo COM_refresh($_CONF['site_url'] . "/nexfile/index.php");
    exit;
}

if (isset($_GET['op']) OR  isset($_POST['op'])) {
    if ($_GET['op'] != "" ) {
        $op = $_GET['op'];
    } else {
        $op = $_POST['op'];
    }
}

if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

// Check if a Project ID was passed back as a number - user wants to return to project
if(is_numeric($op)) {
    echo COM_refresh($_CONF['site_url'] . "/nexproject/viewproject.php?pid=$op");
    exit;
}

switch ( $op ) {

case 'filelisting':

    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");
    echo COM_refresh($_CONF['site_url'] . "/nexfile/index.php?cid=".$cid);
    exit;
    break;

case 'saveversion':
    $_POST  = fm_cleandata($_POST);
    $fileurl         = $_POST['fileurl'];
    $fileVerNote     = $_POST['fileVerNote'];
    $notification    = $_POST['notification'];

    /* Note Adding files which have a quote in their name like `blaine's file`
    *  are causing a problem where the filename is not being passed in via $_FILES
    *  correctly if magic_quotes_gpc is on. Disabling allows the name to be
    *  passed in correctly but then I need to use addslashes on the name
    *  just before the SQL INSERT
    *
    *  Jul 2005: This appears to just be an issue with PHP 5.
    *  Characters before the ' in the name are stripped out
    *  Only if magic_quotes is on - very odd sounds like a bug
    */
    $uploadfilename  = $_FILES['fileupload']['name'];

    $date            = time();
    $query = DB_query("SELECT cid,fname,version, ftype FROM {$_TABLES['fm_files']} WHERE fid=$fid");
    list($cid,$fname,$curVersion,$curftype) = DB_fetchARRAY($query);

    if ($curVersion < 1) {
        $curVersion = 1;
    }
    $newVersion = $curVersion + 1;
    $date = time();
    $pos = strrpos($fname,'.');
    $oldFileExtension = strtolower(substr($fname, $pos+1));

    // Check for the filename having the version info as part of the filename - we will need to replace it
    $vpos = strrpos($fname,'_v'.$curVersion);
    if ($vpos > 0) {
        // Extract just the filename portion
        $filename = substr($fname,0,$vpos);
    } else {
        $filename = substr($fname,0,$pos);
    }

    if ($uploadfilename != '') {
        $pos2 = strrpos($uploadfilename, '.');
        $newFileExtension = strtolower(substr($uploadfilename, $pos2 + 1));
    }

    //if type is url we need to strip off the rest of the file name
    if ($curftype == 'url') {
        // Create new filenames with version tag attached
        if ($uploadfilename != '') {
            $filename = substr($uploadfilename, 0, $pos2);
            $newVerFname = strtolower($filename) . '_v' .$newVersion. '.' . $newFileExtension;
        }
        else {
            $newVerFname = $fileurl;
        }
        $curVerFname = $fname;
    }
    else {
        $curVerFname = strtolower($filename) . '_v' .$curVersion. '.' . $oldFileExtension;
        if ($uploadfilename != '') {
            $newVerFname = strtolower($filename) . '_v' .$newVersion. '.' . $newFileExtension;
        }
        else {
            $newVerFname = $fileurl;
        }
    }


    if ($_FMCONF['debug']) {
        echo COM_startBlock();
        echo "Current Fileid is: $fid and filename is: $fname<br>";
        echo "curFile name will be: $curVerFname<br>newFname will be: $newVerFname<br>Position of . is $pos and newversion position is $vpos<br>Filename     is:$filename";
        echo "<br>File URL is:$fileurl";
        echo COM_endBlock();
    }

   if (fm_getPermission($cid,'upload_direct')) {
        // if this is a upload file then use the upload class - else set type to remote and add the URL
        if ($fileurl != "") {
            $filetype = "url";

            $ext = substr($fileurl, stripos($fileurl, '.') + 1);
            if (!in_array('.'.$ext, $_FMCONF['allowablefiletypes'])) {
                $errmsg .= $LANG_FMERR['upload9'] . ' ' . $ext;
                break;
            }

            if (($fsize = fm_getFilesize($fileurl)) > 0) {
                if ($curftype == "file") {
                    $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$fname;
                    $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$curVerFname;
                    $rename = @rename ($curfile,$newfile);            // Rename the current file so that it has a version number in the filename
                    if ($curVersion == 1) {
                        // The filename will have changed possibly if this was version 1 and it had not version suffix in the filename
                        DB_query("UPDATE {$_TABLES['fm_versions']} SET fname='$curVerFname' WHERE fid={$fid} and version={$curVersion}");
                    }
                }
                DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,size,notes,date,uid,status)
                    VALUES ('$fid','$newVerFname','url','$newVersion','$fsize','$fileVerNote','$date','$userid','1')");
                DB_query("UPDATE {$_TABLES['fm_files']} SET fname='$fileurl',version='$newVersion',ftype='url',size='$fsize',date='$date' WHERE fid=$fid");

                // Send out notifications of update
                if ($notification == 1) {
                    fm_sendNotification($fid);
                }
                fm_updateAuditLog("New File Version - Remote File - added for FID: $fid");

            } else {
                fm_updateAuditLog("Error: New Remote File - URL not accessible FID: $fid");
                nexfile_statusMessage($GLOBALS['fm_errmsg'],'',"<center><button onclick='javascript:history.go(-1)'>{$LANG_FM02['RETURN']}</button></center>");
                exit;
            }

        } elseif ($uploadfilename != "") {
            if (!get_magic_quotes_gpc()) {
                $uploadfilename = addslashes($uploadfilename);
            }
            $fsize =  $_FILES['fileupload']['size'];
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid;
            if ( fm_uploadfile($directory,$newVerFname) ) {
                $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$fname;
                $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$curVerFname;

                if (!get_magic_quotes_gpc()) {
                    $fname = addslashes($fname);
                }

                // Need to check there are no other repository entries in this category for the same filename
                if (DB_count($_TABLES['fm_files'], array('cid','fname'), array("$cid","$fname")) == 1) {
                    // Rename the current file so that it has a version number in the filename
                    @rename ($curfile,$newfile);
                } else {
                    // Copy  the current file so that it has a version number in the filename
                    @copy ($curfile,$newfile);
                }

                if (!get_magic_quotes_gpc()) {
                    $newVerFname = addslashes($newVerFname);
                    $curVerFname = addslashes($curVerFname);
                }

                DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid, fname, ftype, version, notes, size, date, uid, status)
                VALUES ('$fid','$newVerFname','file', '$newVersion', '$fileVerNote', '$fsize', '$date', '$userid', '1')");
                DB_query("UPDATE {$_TABLES['fm_files']} SET fname='$newVerFname',ftype='file',version='$newVersion',size='$fsize',date='$date' WHERE fid=$fid");
                if ($curVersion == 1 AND $curftype != 'url') {
                    // The filename will have changed possibly if this was version 1 and it had not version suffix in the filename
                    DB_query("UPDATE {$_TABLES['fm_versions']} SET fname='$curVerFname' WHERE fid={$fid} and version={$curVersion}");
                }

                // Send out notifications of update
                // Optionally add notification records and send out notifications to all users with view access to this new file
                if ($notification == 1) {
                    fm_sendNotification($fid);
                }
                fm_updateAuditLog("New File Version - Local File - added for FID: $fid");

            } else {
                fm_updateAuditLog("Error: New File upload problem: $fid");
                nexfile_statusMessage($GLOBALS['fm_errmsg'],'',"<center><button onclick='javascript:history.go(-1)'>{$LANG_FM02['RETURN']}</button></center>");
                exit;
            }

        } else {
            $errmsg = $LANG_FMERR['upload3'];
            nexfile_statusMessage($errmsg,'',"<center><button onclick='javascript:history.go(-1)'>{$LANG_FM02['RETURN']}</button></center>");
            exit;
        }

    } else {
        $query = DB_query("SELECT title,description FROM {$_TABLES['fm_files']} file, {$_TABLES['fm_detail']} detail WHERE file.fid=detail.fid and file.fid={$fid}");
        list ($filetitle,$filedesc) = DB_fetchArray($query);

       // Upload will go into submission queue to be approved
       // if this is a upload file then use the upload class - else set type to remote and add the URL
        if ($fileurl != "") {
            $filetype = "url";
            if (!strpos(' '.$fileurl, 'http')) {   // Add a space because it will return 0 as well for position 0 if found.
                $fileurl = 'http://' .$fileurl;
            }
            if (fm_verifyURL($fileurl)) {
                DB_query("INSERT INTO {$_TABLES['fm_submissions']} (fid,cid, fname, title, ftype, description, version, version_note, size, submitter, date)
                    VALUES ('$fid','$cid','$fileurl','$filetitle','url','$filedesc','$newVersion','$fileVerNote','0','$userid','$date')");
                $sid = DB_insertId();

                // Determine if any users that have upload.admin permission for this category 
                // or nexfile admin rights should be notified of new file awaiting approval
                fm_sendAdminApprovalNofications($cid,$sid);

                fm_updateAuditLog("New File - Remote File - Version submission added for FID: $fid");
            } else {
                $errmsg .= "<br>{$LANG_FMERR['upload5']}";
                break;
            }

        } elseif ($uploadfilename != "") {
            $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid.'/submissions';
            $fsize =  $_FILES['fileupload']['size'];
            
            // Generate random file name for newly submitted file to hide it until approved
            $charset = "abcdefghijklmnopqrstuvwxyz";
            for ($i=0; $i<12; $i++) $random_name .= $charset[(mt_rand(0,(strlen($charset)-1)))];
            $random_name .= '.' .$fileExtension;

            if ( fm_uploadfile($directory,$random_name) ) {
                if (!get_magic_quotes_gpc()) {
                    $uploadfilename = addslashes($uploadfilename);
                }
                DB_query("INSERT INTO {$_TABLES['fm_submissions']} (fid, cid,fname,tempname,title,ftype,description,version,version_note,size,submitter,date,version_ctl) 
                VALUES ('$fid', '$cid','$newVerFname','$random_name','$filetitle','file','$filedesc','$newVersion','$fileVerNote','$fsize','$userid','$date','1')");
                $sid = DB_insertId();

                // Determine if any users that have upload.admin permission for this category 
                // or nexfile admin rights should be notified of new file awaiting approval
                fm_sendAdminApprovalNofications($cid,$sid);

                fm_updateAuditLog("New File - Local File - Version submission added for FID: $fid");
            }

        } else {
            $errmsg .= "<br>{$LANG_FMERR['upload3']}";
            break;
        }

        if ($userid > 1 AND $notification) {
            if (!DB_count($_TABLES['fm_notify'], array('fid','uid'), array($fid,$userid) ) ) {
                DB_query("INSERT INTO {$_TABLES['fm_notify']} (fid,cid,uid,date) VALUES ('$fid','$cid','$userid','$date')");
            }
        }

    }
    
    break;

case 'updatefile':
    $_POST     = fm_cleandata($_POST);
    $filetitle          = $_POST['filetitle'];
    $newcid             = $_POST['category'];
    $filedesc           = nl2br($_POST['filedesc']);
    $fileVerNote        = nl2br($_POST['fileVerNote']);
    $versionmgmt        = ($_POST['filevermgmt'] == 1) ? "1" : "0";
    $status             = ($_POST['status'] == 1) ? "1" : "0";
    $thumburl           = $_POST['thumburl'];
    $version            = $_POST['version'];
    $date               = time();

    $query = DB_query("SELECT fname,cid,version,thumbnail,thumbtype FROM {$_TABLES['fm_files']} WHERE fid={$fid}");
    list ($fname,$cid,$curVersion,$thumbnail,$thumbtype) = DB_fetchArray($query);

    if ($_POST['delimage'] == "1") {
        $curimage = $_CONF['path_html'] ."/nexfile/data/{$cid}/images/{$thumbnail}";
        if (file_exists($curimage)) {
            @unlink($curimage);
            DB_query("UPDATE {$_TABLES['fm_files']} SET thumbnail='', thumbtype='' WHERE fid='{$fid}' ");
        }
    }

    // Allow updating the title, description and image for the current and master record 
    if ($version == $curVersion) {
        DB_query("UPDATE {$_TABLES['fm_files']} SET title='$filetitle', cid='$cid', version_ctl='$versionmgmt', status='$status', date='$date' WHERE fid=$fid");
        if ($thumburl != "") {
            if (fm_verifyURL($thumburl)) {
                DB_query("UPDATE {$_TABLES['fm_files']} SET thumbnail='$thumburl', thumbtype='url' WHERE fid='$fid' ");
                $curimage = $_CONF['path_html'] ."/nexfile/data/{$cid}/images/{$thumbnail}";
                if (file_exists($curimage)) {
                    @unlink($curimage);
                }
            }
        }
        
        if ($_FILES['thumbupload']['name'] != "") {
            $uploadthumbname = $_FILES['thumbupload']['name'];
            $filesize =  $_FILES['thumbupload']['size'];
            $directory = $_CONF['path_html'] . "nexfile/data/{$cid}/images/";
            if ( fm_uploadfile($directory,$uploadthumbname,'image') ) {
                $curimage = $_CONF['path_html'] ."nexfile/data/{$cid}/images/{$thumbnail}";
                if (file_exists($curimage)) {
                    @unlink($curimage);
                }
                DB_query("UPDATE {$_TABLES['fm_files']} SET thumbnail='$uploadthumbname', thumbtype='file' WHERE fid='$fid' ");
            } else {
                echo $GLOBALS['fm_errmsg'];
            }
        }
        DB_query("UPDATE {$_TABLES['fm_detail']} SET description='$filedesc' WHERE fid=$fid");

        if ($newcid !== $cid) {

            // Check if there is more then 1 reference to this file in this category
            $fname = addslashes($fname); // In case filename stored has quotes in it
            if (DB_count($_TABLES['fm_files'], array('cid','fname'), array("$cid","$fname")) > 1) {
                $dupfile_inuse = true;
            } else {
                $dupfile_inuse = false;
            }

            /* Need to move the file and related images */
            $query2 = DB_query("SELECT id,fname FROM {$_TABLES['fm_versions']} WHERE fid={$fid}");
            $filemoved = false;

            while (list ($fileid,$fname) = DB_fetchArray($query2)) {
                $sourcefile = $_CONF['path_html'] . "nexfile/data/{$cid}/{$fname}";
                if ( file_exists($sourcefile) )  {
                    $targetfile = $_CONF['path_html'] . "nexfile/data/{$newcid}/{$fname}";
                    // If there is more then 1 reference to this file in this category
                    if ($dupfile_inuse) {
                        @copy($sourcefile,$targetfile);
                    } else {
                        if (file_exists($targetfile)) {
                            @unlink($sourcefile);
                        } else {
                            @rename($sourcefile,$targetfile);
                        }
                    }
                    $filemoved = true;
                }
            }

            if ($filemoved) {    // At least one file moved - so now update record and check for image
                DB_query("UPDATE {$_TABLES['fm_files']} SET cid ='$newcid' WHERE fid='$fid' ");
                $sourceimage = $_CONF['path_html'] . "nexfile/data/{$cid}/images/{$thumbnail}";
                if ( $thumbnail != '' AND file_exists($sourceimage) ) {
                    $targetimage = $_CONF['path_html'] . "nexfile/data/{$newcid}/images/{$thumbnail}";
                    @rename($sourceimage,$targetimage);
                }
            }
        }

    } else {

        DB_query("UPDATE {$_TABLES['fm_files']} SET status='$status', date='$date' WHERE fid=$fid AND version=$version");

    }
    fm_updateAuditLog("Updated File FID: $fid");
    DB_query("UPDATE {$_TABLES['fm_versions']} SET notes='$fileVerNote' WHERE fid=$fid and version=$version");
    break;


case 'deletefile':

    if (isset($_GET['version']) AND $_GET['version'] != "") {
        $editVersion = $_GET['version'];
        $query = DB_query("SELECT file.cid, version.fname, file.title,  version.version FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_versions']} version ON file.fid=version.fid WHERE file.fid=$fid and version.version=$editVersion");
    } else {
        $query = DB_query("SELECT file.cid, file.fname, file.title, file.version FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_versions']} version ON file.version=version.version WHERE file.fid=$fid");
    }

    list ($cid,$fname,$title,$curVersion) = DB_fetchArray($query);
    $version = (isset($editVersion) AND $editVersion > 1) ? $editVersion : $curVersion;

    echo COM_siteHeader();
    echo  COM_startBlock($LANG_nexfile['msg12']);
    
    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['8']   => "{$_CONF['site_url']}/nexfile/details.php?fid=$fid",
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['2']   => "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles"
    );
    echo fm_navbar($navbarMenu);

    echo '<table width="100%" border="0" cellspacing="2" cellpadding="5"  bgcolor="#EFEFEF"><br><p />
        <form action="'.$_SERVER['PHP_SELF'].'" method="post">
        <input type="hidden" name="fid" value="'.$fid.'">
        <input type="hidden" name="op" value="deleteconfirm">
        <input type="hidden" name="version" value="' .$version.'">
        <tr bgcolor="#FFFFFF">
            <td width="20%">&nbsp;<b>'.$LANG_FM02['FILENAME'].':</b> </td>
            <td>'.$fname.'</td>
          </tr>
          <tr bgcolor="#FFFFFF">
            <td width="20%">&nbsp;<b>'.$LANG_FM02['TITLE'].':</b></td>
            <td>'.$title.'</td>
          </tr>
          <tr bgcolor="#FFFFFF">
            <td colspan="2" height="35px" align="center" valign="middle"><input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="location.replace(\'details.php?fid='.$fid.'\')">&nbsp;&nbsp;<input type="submit" name="submit" value="'.$LANG_FM02['DELETE'].'"></td>
        </tr>
          </table>
          </form>';
    exit;
    break;


case 'deleteconfirm':

    // Need to later add logic to delete just one file or all versions on record for this file
    if ( ($_POST['submit'] == "Delete") AND isset($_POST['version']) )  {
        $delversion = $_POST['version'];
        $query = DB_query("SELECT cid,version FROM {$_TABLES['fm_files']} WHERE fid={$fid}");
        list ($cid,$curVersion) = DB_fetchArray($query);
        $fname = DB_getItem($_TABLES['fm_versions'],"fname","fid=$fid AND version=$delversion");
        $fname = addslashes($fname);  // In case stored filename has quotes in it;
        DB_query("DELETE FROM {$_TABLES['fm_versions']} WHERE fid={$fid} AND version={$delversion}");

        // Need to check there are no other repository entries in this category for the same filename
        if (DB_count($_TABLES['fm_files'], array('cid','fname'), array("$cid","$fname")) == 1) {
            @unlink($_CONF['path_html'] . "nexfile/data/$cid/$fname");
            fm_updateAuditLog("Delete File id: $fid, Version: $delversion. File: $fname. Single reference - file deleted");
        } else {
            fm_updateAuditLog("Delete File id: $fid, Version: $delversion. File: $fname. Other references - not deleted");
        }
        // If there is at least 1 more version record on file then I may need to update current version
        if (DB_count($_TABLES['fm_versions'], "fid", "$fid") > 0) {
            if ($delversion == $curVersion) {
                // Retrieve most current version on record
                $query = DB_query("SELECT fname,ftype,version,date FROM {$_TABLES['fm_versions']} WHERE fid={$fid} ORDER BY version DESC LIMIT 1");
                list ($fname,$ftype,$version,$date) = DB_fetchArray($query);
                DB_query("UPDATE {$_TABLES['fm_files']} SET fname='$fname',version='$version',ftype='$ftype', date='$date' WHERE fid=$fid");
            }
            echo COM_refresh($_CONF['site_url'] . "/nexfile/details.php?fid=".$fid);
            exit;
        } else {
            fm_updateAuditLog("Delete File final version for fid: $fid , Main file records deleted");
            DB_query("DELETE FROM {$_TABLES['fm_files']} WHERE fid={$fid}");
            DB_query("DELETE FROM {$_TABLES['fm_detail']} WHERE fid={$fid}");
            echo COM_refresh($_CONF['site_url'] . "/nexfile/index.php?cid=".$cid);
            exit;
        }
    } else {
        fm_updateAuditLog("Delete File id: $fid request but version is not set");
        echo COM_refresh($_CONF['site_url'] . "/nexfile/details.php?fid=".$fid);
        exit;
    }
    break;


case 'newversion':

    $filetitle = DB_getItem($_TABLES['fm_files'], "title", "fid=$fid");
    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");
    echo COM_siteHeader();
    echo  COM_startBlock($LANG_nexfile['msg12']);
    
    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['8']   => "{$_CONF['site_url']}/nexfile/details.php?fid=$fid",
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['2']   => "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles"
    );
    echo fm_navbar($navbarMenu);

    echo '<div class="clearboth"></div>';
    echo'<table width="100%" border="0" cellspacing="2" cellpadding="5" bgcolor="#EFEFEF">
        <form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
        <tr bgcolor="#FFFFFF">
            <td bgcolor=#BBBECE colspan="4" width="100%" align=left valign="top"><font size=1>'.$LANG_nexfile['msg11'].$filetitle.' </font></td>
        </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['UPLOADFILE'].'</td>
        <td width="33%"><input type="file" name="fileupload" size="25"></td>
        <td width="15%">&nbsp;'.$LANG_FM02['UPLOADURL'].'</td>
        <td width="30%"><input name="fileurl" type="text" size="32" maxlength="255"></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['VERSION_NOTE'].'</td>
        <td colspan="3"><textarea name="fileVerNote" cols="70" rows="3"></textarea></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="4" align="left"><label for="chk3">'.$LANG_FM02['EMAIL_NOTIFICATION'].':&nbsp;<input type="checkbox" name="notification" value="1" id="chk1"checked>&nbsp;'.$LANG_FM02['YES'].'</label></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="4" height="35" align="center" valign="middle"><input type="button" value="'.$LANG_FM02['CANCEL'].'" onclick="location.replace(\'details.php?fid='.$fid.'\')">&nbsp;&nbsp;<input type="submit" value="Submit"><input type="hidden" name="op" value="saveversion"><input type="hidden" name="fid" value="'.$fid.'"></td>
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

case 'editfile':
    if (isset($_GET['version']) AND $_GET['version'] > '0') {
        $editversion = $_GET['version'];
        $readonly = "DISABLED";
        $query = DB_query("SELECT file.cid, version.fname, version.ftype, file.title, detail.description, version.version, version.notes, file.thumbnail, file.thumbtype, file.version_ctl, file. status FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_versions']} version ON file.fid=version.fid LEFT JOIN {$_TABLES['fm_detail']} detail ON file.fid=detail.fid WHERE file.fid=$fid and version.version=$editversion");

    } else {
        $readonly = "";
        $query = DB_query("SELECT file.cid,file.fname,file.ftype,file.title,description,file.version,version.notes, thumbnail,thumbtype,version_ctl,file.status FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_detail']} detail ON file.fid=detail.fid LEFT JOIN {$_TABLES['fm_versions']} version ON file.version=version.version WHERE file.fid=$fid AND version.fid=$fid");
    }
    list($cid,$fname,$ftype,$title,$description,$version,$verNote,$thumbnail,$thumbtype,$versionCtl,$status) = DB_fetchARRAY($query);
    $cname = DB_getItem($_TABLES['fm_category'],"name","cid=$cid");
    $chkOnline = ($status == 1) ? "checked" : "";
    $chkVer     = ($versionCtl == 1) ? "checked" : "";
    $description = str_replace ( '<br />','',$description);
    $verNote = str_replace ( '<br />','',$verNote);
    if ($thumbtype == 'url') {
        $thumburl = $thumbnail;
    } elseif ( $thumbnail != "" )  {
        $fileimage = $_CONF['path_html'] .'nexfile/data/' .$cid. '/images/' . $thumbnail;
        if (!file_exists( $fileimage)) {
            $fileimage = "";
        } else {
            $fileimage = $_CONF['site_url'] .'/nexfile/data/' .$cid. '/images/' . $thumbnail;
        }
    }

    echo COM_siteHeader();
    echo  COM_startBlock($LANG_nexfile['msg12']);
    
    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['8']   => "{$_CONF['site_url']}/nexfile/details.php?fid=$fid",
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['2']   => "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles"
    );
    echo fm_navbar($navbarMenu);
    echo '<script>
    function toggleaddimage() {
        obj1 = document.getElementById("101");
        obj2 = document.getElementById("102");
        obj1.style.display = "none";
        obj2.style.display = "";
    }
    </script>';
    echo '<div class="clearboth"></div>';
    echo'<table width="100%" border="0" cellspacing="2" cellpadding="5" bgcolor="#EFEFEF">
        <form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
        <tr bgcolor="#FFFFFF">
            <td bgcolor=#BBBECE colspan="4" width="100%" align=left valign="top"><font size=2><b>'.$LANG_nexfile['msg13'].'</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
        <td width="17%">'.$LANG_FM02['FILENAME'].':</td>
        <td colspan="3">'.$fname.'</td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
        <td width="17%">'.$LANG_FM02['FILETITLE'].':</td>
        <td colspan="1"> <input name="filetitle" type="text" size="40" value="' .$title. '" maxlength="255" '.$readonly.'></td>
        <td colspan="2"><select name="category" style="text-indent: 0px;"><option value="0">'.$LANG_FM02['SELECT_CATEGORY'].'</option>' .fm_recursiveCatAdmin($cname) . '</select></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['DESCRIPTION'].'</td>
        <td colspan="3"><textarea name="filedesc" cols="70" rows="3" '.$readonly.'>'.$description.'</textarea></td>
      </tr>';
    if (!isset($editversion)) {
        echo '<tr bgcolor="#FFFFFF">
        <td valign="top">'.$LANG_FM02['THUMBNAIL'].'</td>';
        if ($fileimage != "" ) {
            echo '<td colspan="3" valign="top"><img src="' . $fileimage. '" width="110" height="110">';
            echo '<span style="padding-left:10px;vertical-align:top;">Delete&nbsp;<input type="checkbox" name="delimage" value="1"></span></td></tr>';
        } else {
            echo '<td width="80%" id="101" colspan=3" style="display:;padding-left:20px;"><a class="nexfilelinks" href="#" onClick=\'toggleaddimage();\'>Add Image</a>';
            echo '<td colspan="3" id="102" valign="top" style="padding-left:25px;display:none;"><input type="file" name="thumbupload"  size="32"><div style="padding-top:5px;"><input name="thumburl" type="text" size="32" maxlength="255" value="' .$thumburl .'">&nbsp;'.$LANG_FM02['UPLOADURL'].'</div></td></tr>';
        }
    }
    echo '<tr bgcolor="#FFFFFF"> 
        <td>'.$LANG_FM02['VERSION_NOTE'].'</td>
        <td colspan="3"><textarea name="fileVerNote" cols="70" rows="3">'.$verNote.'</textarea></td>
      </tr>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="4" height="35" align="center" valign="middle">
            <input type="button" value="'.$LANG_FM02['CANCEL'].'"  onclick="location.replace(\'details.php?fid='.$fid.'\');">&nbsp;&nbsp;
            <input type="submit" value="'.$LANG_FM02['UPDATE'].'">
            <input type="hidden" name="op" value="updatefile">
            <input type="hidden" name="fid" value="'.$fid.'">
            <input type="hidden" name="version" value="'.$version.'"></td>
      </tr></form>
    </table>';

    $footer = new Template($_CONF['path_layout'] . 'nexfile');
    $footer->set_file (array ('footer'=>'footer.thtml'));
    $footer->set_var ('endblock', COM_endBlock() );
    $footer->set_var ('sitefooter', COM_siteFooter());
    $footer->parse ('output', 'footer');
    echo $footer->finish ($footer->get_var('output'));
    exit;
    break;

case 'subscribe':
    $date = time();
    $message = '';
    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");
    $direct = DB_count($_TABLES['fm_notify'], array('fid','uid'), array($fid,$userid) );
    $indirect = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array(0,$cid,$userid) );
    $exception = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array($fid,0,$userid) );

    if ($exception) {
        //delete the exception record
        DB_query("DELETE FROM {$_TABLES['fm_notify']} WHERE fid=$fid AND cid=0 AND uid=$userid");
        $message .= 'Notification exception record removed.<br>';
    }

    if ($direct > 0 OR $indirect > 0) {
        $message .= 'You have already subscribed to updates for this file';
    }
    else {
        DB_query("INSERT INTO {$_TABLES['fm_notify']} (fid,cid,uid,date) VALUES ('$fid','$cid','$userid','$date')");
        $message .= 'You will be notified of any new versions of this file';
    }
    fm_updateAuditLog("Subscription record added for FID: $fid");
    nexfile_statusMessage($message,$_CONF['site_url'] . "/nexfile/details.php?fid=$fid");
    exit;
    break;

case 'unsubscribe':
    $date = time();
    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");
    $direct = DB_count($_TABLES['fm_notify'], array('fid','uid'), array($fid,$userid) );
    $indirect = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array(0,$cid,$userid) );

    if ($direct > 0) {
        DB_query("DELETE FROM {$_TABLES['fm_notify']} WHERE fid=$fid AND cid=$cid AND uid=$userid");
        $message = 'You will not be notified of any new versions of this file';
    }
    else if ($indirect > 0) {
        DB_query("INSERT INTO {$_TABLES['fm_notify']} (fid,cid,uid,date) VALUES ('$fid',0,'$userid','$date')");
        $message = 'You will not be notified of any new versions of this file';
    } else {
        $message = 'You are already unsubscribed!';
    }
    fm_updateAuditLog("Unsubscription record added for FID: $fid");
    nexfile_statusMessage($message,$_CONF['site_url'] . "/nexfile/details.php?fid=$fid");
    exit;
    break;

case 'lockfile':
    fm_updateAuditLog("Lock File FID: $fid");
    DB_query("UPDATE {$_TABLES['fm_files']} SET status='2', status_changedby_uid='$userid' WHERE fid=$fid");
    break;

case 'unlockfile':
    fm_updateAuditLog("Unlock File FID: $fid");
    DB_query("UPDATE {$_TABLES['fm_files']} SET status='0', status_changedby_uid='$userid' WHERE fid=$fid");
    break;

} 


// Main Display Code Begins

// Check that the file exist
if (!DB_count($_TABLES['fm_files'], "fid","$fid")) {

    $message = "Error: Attempt to access File ID: $fid that does not exist";
    fm_updateAuditLog($message);
    nexfile_statusMessage($message,$_CONF['site_url'] . "/nexfile/index.php");

} else {

    echo COM_siteHeader();

    $cid = DB_getItem($_TABLES['fm_files'],"cid","fid=$fid");
    $direct = DB_count($_TABLES['fm_notify'], array('fid','cid','uid'), array($fid,$cid,$userid));
    $indirect = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array(0,$cid,$userid));
    $exception = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array($fid,0,$userid));

    /* Setup Navbar */
    $navbarMenu = array(
        $LANG_FM_NAVBAR['11']  => "{$_CONF['site_url']}/nexfile/download.php?op=download&fid=$fid",
        $LANG_FM_NAVBAR['5']   => $_CONF['site_url'] .'/nexfile/index.php?cid=' .$cid,
        $LANG_FM_NAVBAR['2']   => "{$_CONF['site_url']}/nexfile/index.php?op=latestfiles"
    );
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $navbarMenu[$LANG_FM_NAVBAR['7']]  = $_CONF['site_url'] .'/nexproject/viewproject.php?pid=' .$projectid;
        }
    }

    if ($_USER['uid'] > 0) {
        if ($exception == 0 AND ($direct > 0 OR $indirect > 0)) {
            $navbarMenu[$LANG_FM_NAVBAR['20']]  = "{$_CONF['site_url']}/nexfile/details.php?op=unsubscribe&fid={$fid}";
        }
        else {
            $navbarMenu[$LANG_FM_NAVBAR['16']]  = "{$_CONF['site_url']}/nexfile/details.php?op=subscribe&fid={$fid}";
        }
    }

    $page = new Template($_CONF['path_layout'] . 'nexfile');
    $page->set_file (array ('page' => 'filedetail.thtml', 'versions'=>'filedetail_versions.thtml'));
    $page->set_var ('startblock',COM_startBlock($LANG_nexfile['msg14']));
    $page->set_var ('endblock',COM_endBlock());
    $page->set_var ('toolbar',fm_navbar($navbarMenu));
    $page->set_var ('errmsg', $errmsg);

    $query = DB_query("SELECT cid,title,fname,date,version,size,description,submitter,thumbnail,thumbtype,status FROM {$_TABLES['fm_files']} file LEFT JOIN {$_TABLES['fm_detail']} detail ON file.fid=detail.fid WHERE file.fid=$fid");

    list($cid,$title,$fname,$date,$curVersion,$size,$description,$submitter,$thumbnail,$thumbtype,$status) = DB_fetchARRAY($query);

    $shortdate = strftime($_CONF['shortdate'],$date);
    $longdate = COM_getUserDateTimeFormat($date);
    $longdate = $longdate[0];
    $curVerNotes = DB_getItem($_TABLES['fm_versions'], "notes", "fid=$fid and version=$curVersion");

    $size = intval($size);
    if ($size/1000000 > 1) {
        $size = round($size/1000000,2) . " MB";
    } elseif ($size/1000 > 1) {
        $size = round($size/1000,2) . " KB";
    } else {
        $size = round($size,2) . " Bytes";
    }


    $pos = strrpos($fname,'.') + 1;
    $ext = strtolower(substr($fname, $pos));
    if (array_key_exists($ext, $_FMCONF['inconlib'] )) {
        $fileicon = $_FMCONF['imagesurl'] . $_FMCONF['inconlib'][$ext];
    } else {
        $fileicon = $_FMCONF['imagesurl'] . $_FMCONF['inconlib']['none'];
    }
    if ($thumbnail != "") {
        if ($thumbtype == "file") {
            $fileimage = "{$_CONF['site_url']}/nexfile/data/{$cid}/images/{$thumbnail}";
        } else {
            $fileimage = $thumbnail;
        }
    } elseif ($_FMCONF['def_catimage']) {
        $catimage = DB_getItem($_TABLES['fm_category'],"image", "cid=$cid");

        if ($catimage != NULL AND $catimage != "" ) {
            $fileimage = "{$_CONF['site_url']}/nexfile/data/{$cid}/{$catimage}";
        } else {
            $fileimage = $fileicon;
        }
    } else {
        $fileimage = $fileicon;
    }

    $curAuthorUid = DB_getItem($_TABLES['fm_versions'], "uid", "fid='$fid' AND version='$curVersion'");
    $author = DB_getItem($_TABLES['users'], "username", "uid='$curAuthorUid'");
    $catname = DB_getItem($_TABLES['fm_category'], "name", "cid=$cid");

    $page->set_var ('site_url',$_CONF['site_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);
    $page->set_var ('PHP_SELF',$_SERVER['PHP_SELF']);
    $page->set_var ('heading',$heading);
    $page->set_var ('fid',$fid);
    $page->set_var ('longdate',$longdate);
    $page->set_var ('shortdate',$shortdate);
    $page->set_var ('fileicon',$fileicon);
    $page->set_var ('fname',$fname);
    $page->set_var ('current_version','(V'.$curVersion.')');
    $page->set_var ('title',$title);
    $page->set_var ('author',$author);
    $page->set_var ('description',$description);
    $page->set_var ('catname',$catname);
    $page->set_var ('size',$size);
    $page->set_var ('LANG_SIZE',$LANG_FM02['SIZE']);
    $page->set_var ('LANG_AUTHOR',$LANG_FM02['AUTHOR']);
    $page->set_var ('LANG_CAT',$LANG_FM02['CAT']);
    $page->set_var ('LANG_DESCRIPTION',$LANG_FM02['DESCRIPTION']);
    $page->set_var ('LANG_VERSION_NOTE',$LANG_FM02['VERSION_NOTE']);
    $page->set_var ('LANG_DOWNLOAD',$LANG_FM02['DOWNLOAD']);
    $page->set_var ('LANG_DOWNLOAD_MESSAGE',$LANG_nexfile['msg61']);
    $page->set_var ('LANG_LASTUPDATED',$LANG_nexfile['msg62']);


    $page->set_var ('current_ver_note',$curVerNotes);
    $page->set_var ('fileimage',$fileimage);

    if ($status == "2") {
        $statUserUid = DB_getItem($_TABLES['fm_files'], "status_changedby_uid", "fid=$fid");
        $statUser = DB_getItem($_TABLES['users'], "username", "uid=$statUserUid");
        $page->set_var ('status_image', '<img src="'.$_FMCONF['imagesurl'].'padlock.gif">');
        $page->set_var ('statusmessage', '* '. sprintf($LANG_nexfile['msg08'],$statUser));
    } else {
        $page->set_var ('status_image','&nbsp;');
        $page->set_var ('statusmessage', '&nbsp;');
    }

    $action_options = '';
    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $action_options .= '<option value="' .$projectid.'">'.$LANG_nexfile['msg15'].'</option>' . LB;
        }
    }

    $action_options .= '<option value="filelisting">'.$LANG_nexfile['msg16'].'</option>' . LB;
    $action_options .= '<option value="download">'.$LANG_nexfile['msg17'].'</option>' . LB;
    if (fm_getPermission($cid,'admin')) {
        $action_options .= '<option value="editfile">'.$LANG_nexfile['msg18'].'</option>' . LB;
        $action_options .= '<option value="deletefile">'.$LANG_nexfile['msg19'].'</option>' . LB;
        if ($status == "2") {
            $action_options .= '<option value="unlockfile">'.$LANG_nexfile['msg21'].'</option>' . LB;
        } else {
            $action_options .= '<option value="lockfile">'.$LANG_nexfile['msg20'].'</option>' . LB;
            $action_options .= '<option value="newversion">'.$LANG_nexfile['msg22'].'</option>' . LB;
        }
    } elseif (fm_getPermission($cid,'upload_ver') AND $status != "2") {
        $action_options .= '<option value="newversion">'.$LANG_nexfile['msg22'].'</option>' . LB;
    }
    if ($_USER['uid'] > 1) {
        if ($exception == 0 AND ($direct > 0 OR $indirect > 0)) {
            $action_options .= '<option value="unsubscribe">'.$LANG_nexfile['msg64'].'</option>' . LB;
        }
        else {
            $action_options .= '<option value="subscribe">'.$LANG_nexfile['msg23'].'</option>' . LB;
        }
    }
    $page->set_var ('action_options',$action_options);

    $query = DB_query("SELECT fname,version,notes,size,date,uid 
                FROM {$_TABLES['fm_versions']} 
                WHERE fid=$fid AND version < $curVersion ORDER by version DESC");

    while ( list($fname,$file_version,$ver_note,$ver_size,$ver_date,$submitter) = DB_fetchARRAY($query)) {
        $ver_shortdate = strftime($_CONF['shortdate'],$ver_date);
        $ver_longdate = COM_getUserDateTimeFormat($ver_date);
        $ver_longdate = $longdate[0];
        $ver_author = DB_getItem($_TABLES['users'], "username", "uid=$submitter");
        $ver_size = intval($ver_size);

        if ($ver_size/1000000 > 1) {
            $ver_size = round($ver_size/1048576,2) . " MB";
        } elseif ($ver_size/1000 > 1) {
            $ver_size = round($ver_size/1024,2) . " KB";
        } else {
            $ver_size = round($ver_size,2) .$LANG_FM02['BYTES'];
        }
        $page->set_var ('vname',$fname);
        $page->set_var ('ver_shortdate',$ver_shortdate);
        $page->set_var ('ver_author',$ver_author);
        $page->set_var ('ver_size',$ver_size);
        $page->set_var ('file_versionnum','(V'.$file_version.')');
        $page->set_var ('file_version',$file_version);
        $page->set_var ('version_note',$ver_note);
        if (fm_getPermission($cid,'admin')) {
            $page->set_var ('link_edit','<a href="'.$_SERVER['PHP_SELF']. '?op=editfile&fid='.$fid.'&version='.$file_version.'">' .$LANG_FM02['EDIT']. '</a>');
            $page->set_var ('link_delete','<a href="'.$_SERVER['PHP_SELF']. '?op=deletefile&fid='.$fid.'&version='.$file_version.'">' .$LANG_FM02['DELETE']. '</a>');
        }
        $page->parse('version_records','versions',true);
    }



    $page->parse ('output', 'page');
    echo  $page->finish ($page->get_var('output'));

    echo COM_siteFooter();

}

if ($op == 'download') {
    fm_updateAuditLog("Download File Request for FID: $fid");
    echo COM_refresh($_CONF['site_url'] . "/nexfile/download.php?op=download&fid=".$fid);
}

?>