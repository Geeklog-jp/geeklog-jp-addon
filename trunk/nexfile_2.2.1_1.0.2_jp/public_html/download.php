<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | download.php                                                              |
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

if ($_GET['op'] == "download") {
    // Check if File ID is set - need to check both GET and POST
    if (isset($_GET['fid']) AND ($_GET['fid'] != "" ))  {
        $fid = $_GET['fid'];
    } else {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }
    include_once($_CONF['path_system'] . 'classes/downloader.class.php');
    if (isset($_GET['version']) AND $_GET['version'] != "") {
        $query = DB_query("SELECT fname,ftype FROM {$_TABLES['fm_versions']} WHERE fid={$fid} AND version={$_GET['version']}");
        list($fname,$ftype) = DB_fetchARRAY($query);
        $cid = DB_getItem($_TABLES['fm_files'], "cid", "fid={$fid}");
    } else {
        $query = DB_query("SELECT cid,fname,ftype FROM {$_TABLES['fm_files']} WHERE fid=$fid");
        list($cid,$fname,$ftype) = DB_fetchARRAY($query);
    }

    //make sure user has access
    if (!fm_getPermission($cid, 'view')) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();;
    }

    // Modification to support linking to another file URL in repository
    /*
    if ($ftype == 'url') {
       $pos = strrpos($fname,"op=download");
       if ($pos > 0) {
           $fid = substr($fname,$pos+12);
           $query = DB_query("SELECT cid,fname,ftype FROM {$_TABLES['fm_files']} WHERE fid='$fid'");
           list($cid,$fname,$ftype) = DB_fetchARRAY($query);
       }
    }
    */
    // End of Modification

    if ($ftype == "file") {
        $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid. '/';
        $logfile = $_CONF['path'] .'logs/error.log';
        $pos = strrpos($fname,'.') + 1;
        $ext = strtolower(substr($fname, $pos));

        $download = new downloader();
        $download->_setAvailableExtensions ($_FMCONF['downloadfiletypes']);
        $download->setAllowedExtensions  ($_FMCONF['downloadfiletypes']);
        $download->setLogFile($logfile);
        $download->setLogging(true);
        $download->setPath($directory);
        $download->downloadFile($fname);
        DB_query("UPDATE {$_TABLES['fm_detail']} SET hits = hits +1 WHERE fid='$fid' ");
        if ($download->areErrors()) {
            $err = $download->printWarnings();
            $err .= "\n" . $download->printErrors();
            COM_errorLog("nexFile: Download error for user: {$_USER['uid']} - file: $fname. $err");
            return false;
        }

    } elseif ($ftype == "url") {
        /* Need to parse out any \'s that may be in name such as a remote file share */
        if ((strpos($fname, 'http://') !== false) OR (strpos($fname, 'https://') !== false) OR (strpos($fname, 'ftp://') !== false) OR (strpos($fname, 'ftps://') !== false)) {  //url file
            $temp = explode('/',$fname);
        }
        else {  //remote file on server
            $temp = explode('\\',$fname);
        }
        $filename = array_pop($temp);
        $download_size = fm_getFilesize($fname);
        if ($download_size > 0) {
            if ($fd = @fopen ($fname, "rb")) {
                $pos = strrpos($fname,"/") + 1;
                $fname = substr($fname,$pos);
                if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
                    $fname = preg_replace('/\./', '%2e', $fname, substr_count($fname, '.') - 1);
                }

                DB_query("UPDATE {$_TABLES['fm_detail']} SET hits = hits +1 WHERE fid='$fid' ");
                header('Pragma:');
                header('Expires: 0');
                header('Cache-Control: public');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header("Content-length: $download_size");
                fpassthru($fd);
                fclose ($fd);
            }
            else {
                 echo $LANG_FMERR['download3'];
                 exit();
            }
        }
        else {
             echo $LANG_FMERR['download3'];
             exit();
        }
    }

} elseif ($_GET['op'] == "chksubmission") {

    if (isset($_GET['id']) AND ($_GET['id'] != "" ))  {
        $fid = $_GET['id'];
    } else {
        echo $LANG_FMERR['download2'];
        exit();
    }

    $cid = DB_getItem($_TABLES['fm_files'], "cid", "fid={$fid}");

    //make sure user has access
    if (!fm_getPermission($cid, 'admin')) {
        echo COM_siteHeader();
        echo COM_startBlock('Access Denied');
        echo 'You do not have access rights to this file.  Your attempt has been logged.';
        echo COM_endBlock();
        echo COM_siteFooter();
    }


    if (DB_count($_TABLES['fm_submissions'],'id',$fid) > 0) {
        include_once($_CONF['path_system'] . 'classes/downloader.class.php');
        $query = DB_query("SELECT cid,ftype,fname,tempname FROM {$_TABLES['fm_submissions']} WHERE id=$fid");
        list($cid,$ftype,$fname,$tname) = DB_fetchARRAY($query);

        $directory = $_CONF['path_html'] . 'nexfile/data/'.$cid. '/submissions/';
        $logfile = $_CONF['path'] .'logs/error.log';

        if ($ftype == "file") {

            $pos = strrpos($tname,'.') + 1;
            $ext = strtolower(substr($tname, $pos));

            $download = new downloader();
            $download->_setAvailableExtensions ($_FMCONF['downloadfiletypes']);
            $download->setAllowedExtensions ($_FMCONF['downloadfiletypes']);
            $download->setLogFile($logfile);
            $download->setLogging(true);
            $download->setPath($directory);
            $download->downloadFile($tname);
            DB_query("UPDATE {$_TABLES['fm_detail']} SET hits = hits +1 WHERE fid='$fid' ");
            if ($download->areErrors()) {
                echo $LANG_FMERR['download1'];
                echo $download->printWarnings();
                echo $download->printErrors();
                return false;
            }
        } else {
            $url = $fname;
            if ($fd = fopen ($url, "rb")) {
                $pos = strrpos($url,"/") + 1;
                $fname = substr($url,$pos);
                if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
                    $fname = preg_replace('/\./', '%2e', $fname, substr_count($fname, '.') - 1);
                }
                DB_query("UPDATE {$_TABLES['fm_detail']} SET hits = hits +1 WHERE fid='$fid' ");
                header("Cache-Control:");
                header("Pragma:");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"{$fname}\"");
                header("Content-length:");
                fpassthru($fd);
                fclose ($fd);
                DB_query("UPDATE {$_TABLES['fm_details']} SET downloads=downloads+1 WHERE fid='$fid' ");
            }
        }
    } else {
        echo $LANG_FMERR['download4'];
        exit();
    }
} else {
    echo $LANG_FMERR['download4'];
    exit();
}


?>
