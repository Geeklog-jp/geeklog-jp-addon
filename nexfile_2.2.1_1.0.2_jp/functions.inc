<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
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

$langfile = $_CONF['path'] . 'plugins/nexfile/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexfile/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexfile/config.php');



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getmenuitems_nexfile()
{
    global $_TABLES,$_CONF, $_FMCONF,$LANG_FM00;
    if ( SEC_hasRights('nexfile.user') OR ($_FMCONF['useraccess'] == '2') OR ($_FMCONF['useraccess'] == 1 AND SEC_inGroup('13')) ) {
        $menuitems[$LANG_FM00['headerlabel']] ="$_CONF[site_url]/nexfile/index.php";
        return $menuitems;
    }
}


/**
* This will put an option for nexfile admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexfile()
{
    global $_CONF,$LANG_FM00;
    if (SEC_hasRights('nexfile.edit')) {
        return array($LANG_FM00['useradminmenu'],$_CONF['site_admin_url'] . "/plugins/nexfile/catman.php",plugin_geticon_nexfile());
    }
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexfile()
{
    global $_TABLES, $_CONF,$LANG_FM00;

    if (SEC_hasRights('nexfile.edit')) {
    $numSubmissions = DB_count($_TABLES['fm_submissions']);
    return array($LANG_FM00['adminmenu'], $_CONF['site_admin_url'] . '/plugins/nexfile/catman.php', $numSubmissions);
    }

}


/**
* Returns the URL of the plugin's
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexfile()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexfile/images/admin/nexfile.gif';
}


function plugin_autotags_nexfile($op,$content='',$tag='') {
    global $_CONF,$_TABLES;

    if ($op == 'tagname' ) {
        return 'file';
    } elseif ($op == 'parse') {
       if (DB_count($_TABLES['fm_files'],'fid',$tag['parm1']) == 1) {
           $filelink = '<a href="'.$_CONF['site_url'].'/nexfile/download.php?op=download&fid='.$tag['parm1'].'">'.$tag['parm2'].'</a>';
           $retval = str_replace($tag['tagstr'],$filelink,$content);
           return $retval;
        } else {
           return $content;
        }
    }
}



/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
*
*/

function plugin_searchtypes_nexfile()
{
    global $LANG_FM00;

    $tmp['nexfile'] = $LANG_FM00['searchlabel'];
    return $tmp;
}


/**
* this searches for files matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
*
*/

function plugin_dopluginsearch_nexfile($query, $datestart, $dateend, $topic, $type, $author)
{
    global $LANG_FM00, $LANG_FM11, $_TABLES, $_CONF;

    $query = addslashes($query);

    if (empty($type)) {
        $type = 'all';
    }

    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type <> 'nexfile') {
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = $LANG_FM00['plugin_name'];
        $plugin_results->searchlabel = $LANG_FM00['searchlabel'];
        return $plugin_results;
    }
    // Build search SQL to search the project and task name, details and keywords fields
    $sql  = "SELECT files.fid as fid, files.fname as filename, files.date as date, files.submitter as submitter, detail.description as filedesc, detail.hits as downloads, category.name as category, category.description as catdesc, version.notes as vernote FROM {$_TABLES['fm_files']} files";
    $sql .= " LEFT JOIN {$_TABLES['fm_detail']} detail ON files.fid = detail.fid";
    $sql .= " LEFT JOIN {$_TABLES['fm_category']} category ON files.cid = category.cid";
    $sql .= " LEFT JOIN {$_TABLES['fm_versions']} version ON files.fid = version.fid WHERE ";
    $sql .= "((files.fname like '%$query%' OR files.fname like '$query%' OR files.fname like '%$query') ";
    $sql .= " OR (detail.description like '%$query%' OR detail.description like '$query%' OR detail.description like '%$query') ";
    $sql .= " OR (category.name like '%$query%' OR category.name like '$query%' OR category.name like '%$query') ";
    $sql .= " OR (category.description like '%$query%' OR category.description like '$query%' OR category.description like '%$query') ";
    $sql .= " OR (version.notes like '%$query%' OR version.notes like '$query%' OR version.notes like '%$query')) ";

    if (!empty($datestart) && !empty($dateend)) {
        $delim = substr($datestart, 4, 1);
        $DS = explode($delim,$datestart);
        $DE = explode($delim,$dateend);
        $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
        if ($datestart == $dateend) {
            $enddate = mktime(0,0,0,$DE[1],$DE[2],$DE[0]) + 86500;
        } else {
            $enddate = mktime(0,0,0,$DE[1],$DE[2],$DE[0]) + 3600;
        }
        $sql .= "AND files.date BETWEEN '$startdate' AND '$enddate' ";
    }

    if (!empty($author)) {
        $sql .= "AND (files.submitter = '$author') ";
    }
    $sql    .= "ORDER BY date DESC";

    // Perform search
    $result = DB_query($sql);

    // OK, now return coma delmited string of table header labels
    // Need to use language variables
    require_once($_CONF['path_system'] . 'classes/plugin.class.php');
    $plugin_results = new Plugin();
    $plugin_results->plugin_name = 'projects';
    $plugin_results->searchlabel = $LANG_FM00['searchresults'];
    $plugin_results->addSearchHeading($LANG_FM11['FILE']);
    $plugin_results->addSearchHeading($LANG_FM11['DATE']);
    $plugin_results->addSearchHeading($LANG_FM11['AUTHOR']);
    $plugin_results->addSearchHeading("HITS");
    $totalsearched = DB_numRows($result);
    $searchcount=0;

    for ($i = 1; $i <= $totalsearched; $i++) {
        $A = DB_fetchArray($result);
        $searchcount++;
        $username = DB_getItem($_TABLES['users'],"username","uid = {$A['submitter']}");
        $date = strftime('%b %d %Y', $A['date']);
        $url = $_CONF['site_url']. "/nexfile/details.php?fid={$A['fid']}";
        $row = array("<a href=\"$url\">{$A['filename']}</a>",$date,$username,$A['downloads']);
        $plugin_results->addSearchResult($row);
    }
    $plugin_results->num_searchresults = $searchcount;
    $plugin_results->num_itemssearched = DB_count($_TABLES['fm_files']);
    return $plugin_results;
}




function plugin_profilevariablesdisplay_nexfile ($uid, &$template)
{

}

/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexfile()
{
    global $_CONF,$_FMCONF;
    $code = "\n\n" .'<!-- Begin Nexide nexfile -->' ."\n";

    if ($_FMCONF['load_HTMLTree']) {
        $code .= '<script src="' . $_CONF['site_url'] . '/javascript/TreeMenu.js" language="JavaScript" type="text/javascript"></script>';
    }
    $code .= '<script src="' . $_CONF['site_url'] . '/nexfile/include/mainheader.js" language="JavaScript" type="text/javascript"></script>';
    $code .= "\n<!-- End Nextide nexfile -->\n\n";
    return $code;

}

/**
* Returns any formatted links to files that have been refereced by the GL-Auto Tag
*/
function plugin_replacetags_nexfile($content,$tag)
{
    global $_TABLES,$_CONF;

    print_r($tag);
    if (DB_count($_TABLES['fm_files'],'id',$tag['parm1']) == 1) {
        $filelink = '<a href="'.$_CONF['site_url'].'/nexfile/download.php?op=download&fid='.$tag['parm1'].'">'.$tag['parm2'].'</a>';
        $retval = str_replace($tag['tagstr'],$filelink,$content);
        return $retval;
    } else {
        return $content;
    }
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexfile() {
    global $_FMCONF;
    return $_FMCONF['version'];
}

/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexfile($installCheck='')
{
    global $_TABLES, $LANG_FM00;

    $pi_name='nexfile';
    $FEATURES = array ('nexfile.admin','nexfile.edit', 'nexfile.user');
    $TABLES = array ('fm_access', 'fm_category', 'fm_files', 'fm_detail', 'fm_versions', 'fm_notify', 'fm_submissions', 'auditlog');

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_FM00['warning']);
        $display .= $LANG_FM00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the nexfile plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nexfile'",1);
    // Drop chatterblock tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Admin Security for this plugin
    $grp_id = DB_getItem($_TABLES['vars'], 'value', "name = '{$pi_name}_admin'");
    COM_errorLog("Removing $pi_name Admin Group", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id",1);
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE name = '{$pi_name}_admin'");
    COM_errorLog("Removing root users from admin of $pi_name");
    DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id",1);

    // Remove all the associated features - access rights. The feature ID's were stored in the vars table during install.
    foreach ($FEATURES as $feature) {
        COM_errorLog("Removing $feature feature and rights to it",1);
        $feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
        COM_errorLog("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id");
        DB_query("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id",1);
        DB_query("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id",1);
    }

    COM_errorLog("Removing nexfile - Block Definition ", 1);
    DB_query("DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn = 'phpblock_nexfile_latestfiles'",1);

    COM_errorLog('...success',1);
    return true;
}

function plugin_upgrade_nexfile() {
    global $_TABLES,$_FMCONF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexfile'");

    switch ($curversion) {
    case "2.2.0":
        if (upgrade_221() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.2.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexfile' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexfile'") == $_FMCONF['version']) {
        return 11;
    } else {
        return 12;
    }
}

function plugin_chkPermissions_nexfile(&$template) {
    global $_CONF;
    $data_dir = $_CONF['path_html'] . 'nexfile/data/';

    if (!$file = @fopen ($data_dir . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $data_dir . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($data_dir . 'test.txt');
    }

    return $display;
}


//  ------- GENERAL nexfile FUNCTIONS --------


function nexfile_statusMessage($message='',$url='',$action='') {
    global $_CONF,$_USER,$LANG_nexfile,$_FMCONF;
    $retval .= COM_siteHeader();
    $retval .= COM_startBlock($LANG_nexfile['msg47']);
    $retval .= "<font class='alert'>$message</font>";
    $retval .= "<br><p>";
    if ($action != "") {
        $retval .= $action;
        $retval .= "<p />";
    }elseif ($_FMCONF['msgdelay'] == "0") {
        $retval .= sprintf($LANG_nexfile['msg48'],$LANG_nexfile['msg49'],$url);
        $retval .= "<p />";
    } else {
        if ($url == "") {
        $url = "#";
    }
    $retval .= '<script language="javascript1.1">';
        $retval .= 'window.setTimeout("window.location.href=\'' .$url. '\'",' .$_FMCONF['msgdelay']. ');';
        $retval .= '<!-- window.location.replace("'.$url. '");';
        $retval .= '// --> </script>';
        $retval .= sprintf($LANG_nexfile['msg50'],$url);
        $retval .= "<p />";
    }
    $retval .= COM_endBlock();
    $retval .= COM_siteFooter();
    echo $retval;
}


/**
* Returns a formatted listbox of categories user has access to
* Able to pass in the Rights that user needs to have as a test
* Takes either a single Right or an array of Rights
*
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string|array        $rights       Rights to check (admin,view,upload,upload_dir,upload_ver,approval)
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @return       string                            Return a formatted HTML Select listbox of categories
*/

function fm_recursiveCatList($selected='',$rights='view',$cid='0',$level='1',$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if (fm_getPermission($cid,$rights)) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0)  {
                $selectlist .= '<option value="' . $cid;
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if ($name == $selected) {
                    $selectlist .= '" Selected>' .$indent .$name . '</option>' . LB;
                } else {
                    $selectlist .= '">' . $indent .$name . '</option>' . LB;
                }
                $selectlist = fm_recursiveCatList($selected,$rights,$cid,$level+1,$selectlist);
            } else {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                $selectlist .= '<option value="' . $cid;
                if ($name == $selected) {
                    $selectlist .= '" Selected>' . $indent . $name . '</option>' . LB;
                } else {
                    $selectlist .= '">' . $indent . $name . '</option>' . LB;
                }
            }
        }
    }
    return $selectlist;
}


/**
* Returns a formatted listbox of categories user has Upload Access
* First checks for View access so that delegated admin can be just for sub-categories
* @TODO: Combine this function and fm_recursiveCatAdmin() into a single function
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @return       string                            Return a formatted HTML Select listbox of categories
*/
function fm_recursiveCatAddFileList($selected='',$cid='0',$level='1',$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");

    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if (fm_getPermission($cid,'view')) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0)  {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i< $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,array ('admin','upload','upload_dir'))) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" Selected>' .$indent .$name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent .$name . '</option>' . LB;
                    }
                }
                $selectlist = fm_recursiveCatAddFileList($selected,$cid,$level+1,$selectlist);
            } else {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,array ('admin','upload','upload_dir'))) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" Selected>' . $indent . $name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent . $name . '</option>' . LB;
                    }
                }
            }
        }
    }
    return $selectlist;
}

/**
* Returns a formatted listbox of categories user has ADMIN Access
* First checks for View access so that delegated admin can be just for sub-categories
*
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @param        string              $restricted   Used if you do not want to show this categories subfolders
* @return       string                            Return a formatted HTML Select listbox of categories
*/
function fm_recursiveCatAdmin($selected='',$cid='0',$level='1',$selectlist='',$restricted='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if ($cid != $restricted AND fm_getPermission($cid,'view')) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0)  {
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,'admin')) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" Selected>' .$indent .$name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent .$name . '</option>' . LB;
                    }
                }
                $selectlist = fm_recursiveCatAdmin($selected,$cid,$level+1,$selectlist,$restricted);
            } else {
                $indent = "";
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,'admin')) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" Selected>' . $indent . $name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent . $name . '</option>' . LB;
                    }
                }
            }
        }
    }
    return $selectlist;
}


/**
* Will upload a new file or image using the Geeklog upload class
*/
function fm_uploadfile($directory,$filename,$type='file') {
    global $_FILES,$_CONF,$_TABLES,$_FMCONF,$LANG_FMERR;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $upload = new upload();
    $upload->setLogging(true);
    if ($type == 'image' AND !empty($_CONF['image_lib'])) {
        if ($_CONF['image_lib'] == 'imagemagick') {
            // Using imagemagick
            $upload->setMogrifyPath ($_CONF['path_to_mogrify']);
        } else {
            // must be using netPBM
            $upload->_pathToNetPBM= $_CONF['path_to_netpbm'];
        }
        $upload->setAutomaticResize(true);
        $upload->setAllowedMimeTypes($_FMCONF['allowableimagetypes']);
        $upload->setMaxDimensions ($_FMCONF['max_thumbnail_width'], $_FMCONF['max_thumbnail_height']);
        $upload->setMaxFileSize($_FMCONF['max_thumbnail_size']);
    } else {
        $upload->setAutomaticResize(false);
        $upload->setAllowedMimeTypes($_FMCONF['allowablefiletypes']);
        // Set max dimensions as well in case user is uploading full size images
        $upload->setMaxDimensions ($_FMCONF['max_uploadimage_width'], $_FMCONF['max_uploadimage_height']);
        $upload->setMaxFileSize($_FMCONF['max_uploadfile_size']);
    }

    if (!$upload->setPath($directory)) {
        $GLOBALS['fm_errmsg'] = $LANG_FMERR['upload7'] .'<BR>' . $upload->printErrors(false);
        return false;
    }
    if ($upload->numFiles() == 1) {
        $curfile = current($_FILES);
        if (strlen($curfile['name']) > 0) {
            $upload->setFileNames($filename);
            $upload->setPerms( $_FMCONF['fileperms'] );
            reset($_FILES);
            $upload->uploadFiles();
            if ($upload->areErrors()) {
                $GLOBALS['fm_errmsg'] = $LANG_FMERR['upload7'] .':<BR>' . $upload->printErrors(false);
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    return false;

}


/**
* Function to verify the URL being referenced is valid
*/
function fm_verifyURL($url) {
    global $LANG_FMERR;
    if (!preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i', $url, $m)) {
        $GLOBALS['fm_errmsg'] = $LANG_FMERR['err5'];
        return false;
    } else {
      $proto=$m[1];
      $hostname=strtolower($m[2]);
      $port=$m[3];
    }
    if (!@fsockopen("$hostname", '80', $errno, $errstr, 30)) {
        $GLOBALS['fm_errmsg'] = sprintf($LANG_FMERR['err6'], $hostname);
        return false;
    }
    if (!@fopen("$url", 'r')) {
        $GLOBALS['fm_errmsg'] =  sprintf($LANG_FMERR['err6'], $url);
        return false;
    } else {
        return true;
    }
}


function fm_preparefordb($str,$htmlallowed=false,$htmlfilter=false,$censor=false) {

    if ($censor) {
        $str = COM_checkWords($str);
    }
    if($htmlallowed) {
        if(!get_magic_quotes_gpc() ) {
        if ($htmlfilter) {
        $str = addslashes(COM_checkHTML($str));
        } else {
        $str = addslashes($str);
        }
    } else {
        if ($htmlfilter) {
        // COM_checkHTML will strip slashes so I need to add them again
        $str = addslashes(COM_checkHTML($str));
        } else {
        $str = $str;
        }
    }
    } else {
        if(get_magic_quotes_gpc() ) {
            $str = htmlspecialchars($str);
        } else {
            $str = addslashes(htmlspecialchars($str));
        }
    $str = str_replace("'", "&rsquo;",$str);
    }
    return $str;
}

// Callback Function for the array walk function below to apply the data filters to clean any posted data
function fm_cleanfield(&$field) {
    if (gettype($field) == "string" ) {
        $field = fm_preparefordb($field);
    }
}

// Function to clean any posted data
function fm_cleandata($postarray) {
    array_walk($postarray,'fm_cleanfield');
    return $postarray;
}


/* Function to check if user has a particular right or permission to a category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string              $cid         Category to check user access for
* @param        string|array     $rights         Rights to check (admin,view,upload,upload_dir,upload_ver,approval)
* @return        Boolean                         Returns True if user has one of the requested access rights else FALSE
*/
function fm_getPermission($cid,$rights,$userid=0) {
    global $_TABLES,$_USER,$_CONF;
    if ($cid == '') {
        return false;
    }

    // If user is an admin - they should have access to all rights on all categories
    if ($userid == 0) {
        if(empty($_USER['uid']) OR $_USER['uid'] == 1 ) {
            $uid = 1;
        } else {
            $uid = $_USER['uid'];
        }
    }
    else {
        $uid = $userid;
    }

    if (SEC_inGroup('nexfile Admin', $uid)) {
        return true;
    }
    else {
        // Check user access records
       $query = DB_query("SELECT view,upload,upload_direct,upload_ver,approval,admin from {$_TABLES['fm_access']} WHERE catid=$cid AND uid=$uid");
       while (list ($view,$upload,$upload_dir,$upload_ver,$approval,$admin) = DB_fetchArray($query)) {
            if (is_array($rights)) {
                foreach ($rights as $key) {      // Field name above needs to match access right name
                    if ($$key == 1)  {
                        return true;
                    }
                }
            } elseif ($$rights == 1) {
                return true;
            }
       }

        // For each group that the user is a member of - check if they have the right
        $groups = SEC_getUserGroups();
        foreach ($groups as $groupid) {
            $query = DB_query("SELECT view,upload,upload_direct,upload_ver,approval,admin from {$_TABLES['fm_access']} WHERE catid=$cid AND grp_id=$groupid");
            while (list ($view,$upload,$upload_dir,$upload_ver,$approval,$admin) = DB_fetchArray($query)) {
                if (is_array($rights)) {
                    // If any of the required permissions set - return true
                    foreach ($rights as $key) {
                        if ($$key == 1)  {      // Field name above needs to match access right name
                            return true;
                        }
                    }
                } elseif ($$rights == 1) {
                    return true;
                }
            }
       }
    }
    return false;
}

/**
* Create a log entry in the audit table, recording the user, time, script name and logentry message
* Function will prepare the logentry for DB insert - example quotes escaped
**/
function fm_updateAuditLog($logentry) {
    global $_USER,$_TABLES,$HTTP_SERVER_VARS;
    if (isset($_USER['uid']) AND $_USER['uid'] != "") {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }
    $date = time();
    $logentry = fm_preparefordb($logentry);
    DB_query("INSERT INTO {$_TABLES['auditlog']} (uid,date,script,logentry) VALUES ('$userid', '$date','{$HTTP_SERVER_VARS['SCRIPT_NAME']}', '$logentry' )");
    return true;
}

function fm_autoCreateNotifications($fid, $cid) {
    global $_TABLES;

    $ausers = array();
    $query = DB_query("SELECT uid FROM {$_TABLES['users']}");
    while ($A = DB_fetchArray($query)) {
        $exceptions = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array ($fid,0,$A['uid']));
        if (fm_getPermission($cid,'view',$A['uid']) AND $A['uid'] > 1 AND $exceptions == 0) {
            $ausers[] = $A['uid'];
        }
    }

    $date = time();
    foreach ($ausers as $nuid) {
        if (!DB_count($_TABLES['fm_notify'], array('fid','uid'), array($fid,$nuid) ) ) {
            DB_query("INSERT INTO {$_TABLES['fm_notify']} (fid,cid,uid,date) VALUES ('$fid','$cid','$nuid','$date')");
        }
    }
}
/**
* Send out notifications to all users that have subscribed to this file or file category
* Will check user preferences for notification if Messenger Plugin is installed
* @param        string      $id         Key used to retrieve details depending on message type
* @param        string      $msgid      Message type - 1:New file notification, 2: Submission Approval, 3: Submission Deleted
* @return       Boolean     Returns True if atleast 1 message was sent out
**/
function fm_sendNotification($id,$msgid="1")
{
    global $_CONF,$_TABLES,$_USER, $REMOTE_ADDR, $_FMCONF,$LANG_FM10;

    switch ( $msgid ) {
    case "1":               // New File added - send to all subscribed users
        // File id was passed in as parameter
        $query = DB_query("SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM {$_TABLES['fm_files']} file, {$_TABLES['fm_category']} category WHERE file.cid=category.cid and file.fid={$id}");
        list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
        if ($cid > 0) {
            $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['fm_notify']} WHERE fid={$fid} OR cid={$cid}");
        } else {
            $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['fm_notify']} WHERE fid={$fid}");
        }
        break;

    case "2":               // File submission being approved by admin - send only to user
       // File id was passed in as parameter
        $query = DB_query("SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM {$_TABLES['fm_files']} file, {$_TABLES['fm_category']} category WHERE file.cid=category.cid and file.fid={$id}");
        list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
        // Just need to create this SQL record for this user - to fake out logic below
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['users']} WHERE uid = {$submitter}");
        break;

    case "3":                // File submission being declined by admin - send only to user
        // Submission record ID was passed in as parameter
        $fname = DB_getItem($_TABLES['fm_submissions'],"fname", "id=$id");
        $submitter = DB_getItem($_TABLES['fm_submissions'],"submitter", "id=$id");
        // Just need to create this SQL record for this user - to fake out logic below
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['users']} WHERE uid = {$submitter}");
        break;

    }

    if (DB_numRows($queryNotifyUsers) > 0) {
        $target_users = array();
        while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
            //if new file message/new version, check to see if user has exception record
            if ($msgid == 1) {
                $exceptions = DB_count($_TABLES['fm_notify'], array ('fid','cid','uid'), array ($id,0,$uid));
                if ($exceptions == 0) {
                    $target_users[] = $uid;
                }
            }
            else {
                $target_users[] = $uid;
            }
        }

        // Sort the array so that we can check for duplicate user notification records
        sort($target_users);
        reset($target_users);

        // Send out Notifications to all users on distribution
        $lastuser = "";
        foreach ($target_users as $target_user) {
            if ($target_user != $lastuser) {
                $target_username = DB_getItem($_TABLES['users'],"username", "uid='{$target_user}'");

                if ($msgid != "3" ) {
                    $subject = '' . $_CONF['site_name'] . ' ' .$LANG_FM10[$msgid]['SUBJECT']. '';
                    $submitter_name = DB_getItem($_TABLES['users'],"username", "uid=$submitter");
                    $message  = $LANG_FM10[$msgid]['HELLO'] .' '. $target_username . ",\n\n";
                    $message .= sprintf($LANG_FM10[$msgid]['LINE1'], $submitter_name,$catname);
                    $message .= sprintf($LANG_FM10[$msgid]['LINE2'], $fname, $_CONF['site_url'] . '/nexfile/details.php?fid=' .$fid);
                    $message .= $LANG_FM10[$msgid]['LINE3'];
                    $message .= $LANG_FM10[$msgid]['LINE4'] . $LANG_FM10[$msgid]['ADMIN']."\n";
                    $message .= $_CONF[site_url] . "\n";
                } else {
                    $subject = '' . $_CONF['site_name'] . ' ' .$LANG_FM10[$msgid]['SUBJECT']. '';
                    $message  = $LANG_FM10[$msgid]['HELLO'] .' '. $target_username . ",\n\n";
                    $message .= sprintf($LANG_FM10[$msgid]['LINE1'], $fname);
                    $message .= $LANG_FM10[$msgid]['LINE2'];
                    $message .= $LANG_FM10[$msgid]['LINE3'] . $LANG_FM10[$msgid]['ADMIN']."\n";
                    $message .= $_CONF[site_url] . "\n";
                }
                if (function_exists(messenger_send))  {
                    $query = DB_query("SELECT notifications,sitepreference FROM {$_TABLES['messenger_userinfo']} WHERE uid={$target_user}");
                    list($notifyoption,$sitepreference) = DB_fetchArray($query);
                    if (DB_numRows($query) == 0 OR (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 0)) {
                        msg_sendNotification($target_username,$subject,$message,"nexfile");
                    } elseif (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 1)  {
                        // Or wants all notifications via the messenger plugin
                        msg_send($target_username,$subject,nl2br($message));
                    }
                } else {
                    fm_sendEmail($target_username,$subject,$message);
                }
            }
            $lastuser = $target_user;
        }
        return true;
    } else {
        return false;
    }
}

function fm_sendEmail($user,$subject,$message) {
    global $_USER,$_CONF,$_TABLES, $_FMCONF;

    $user = addslashes($user);   // Added since Inco uses DomainName\username
    if ($_FMCONF['debug']) {
        echo "<br>Sending this message<br>User: $user<br>$subject: $subject<br>$message<br>";
    }
    $target_uid = DB_getItem($_TABLES['users'],"uid", "username='{$user}'");
    $emailaddress = DB_getItem($_TABLES['users'],"email", "username = '$user'");
    if (empty ($LANG_CHARSET)) {
        $charset = $_CONF['default_charset'];
        if (empty ($charset)) {
        $charset = "iso-8859-1";
        }
    } else {
        $charset = $LANG_CHARSET;
    }

    COM_mail($emailaddress, $subject, $message);

    // Log notification for admin viewing and tracking
    $type = "nexfile";
    $logentry = $type ."," .$user ."," .$subject;
    fm_logNotification($logentry);
    return true;
}

function fm_logNotification($logentry) {
    global $_CONF;
    $timestamp = strftime( "%b %d %H:%M" );
    $logfile = $_CONF['path_log'] . 'notifiction.log';

    if( !$file = fopen( $logfile, a )) {
        $retval .= $LANG01[33] . ' ' . $logfile . ' (' . $timestamp . ')<br>' . LB;
    } else {
        fputs( $file, "$timestamp,$logentry \n" );
    }
}


/* Function to updated permission records for a list of users or list of groups
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string         $catid          Category ID to update permission rights
* @param        array          $accessrights   Array of access rights to add (admin,view,upload,upload_dir,upload_ver,approval)
* @param        string|array   $users          String of userid or Array of users to add access rights
* @param        string|array   $groups         String of groupid or Array of groups to add access rights
* @return       Boolean     Returns True if update was successful else FALSE
*/
function fm_updateCatPerms($catid,$accessrights,$users,$groups) {
    global $_TABLES;

    if ($users != "" AND !is_array($users)) {
        $users = array($users);
    }
    if ($groups != ""  AND !is_array($groups)) {
        $groups = array($groups);
    }

        if (!empty($accessrights)) {
            if (in_array("view", $accessrights)) {
                $view = 1;
            } else {
                $view = 0;
            }
            if (in_array("upload", $accessrights)) {
                $upload = 1;
            } else {
                $upload = 0;
            }
            if (in_array("approval", $accessrights)) {
                $approval = 1;
            } else {
                $approval = 0;
            }
            if (in_array("upload_direct", $accessrights)) {
                $direct = 1;
            } else {
                $direct = 0;
            }
            if (in_array("admin", $accessrights)) {
                $admin = 1;
            } else {
                $admin = 0;
            }
            if (in_array("upload_ver", $accessrights)) {
                $versions = 1;
            } else {
                $versions = 0;
            }

            if (!empty($users)) {
                foreach($users as $uid ) {
                    $query = DB_query("SELECT accid FROM {$_TABLES['fm_access']} WHERE catid={$catid} AND uid=$uid");
                    if (DB_numRows($query) == 0) {
                        DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('" . $catid. "','$uid','0','$view','$upload','$direct','$versions','$approval','$admin')");
                        fm_updateAuditLog("Added new User Permission record for: $uid, Category: " . $catid );
                    } else {
                        DB_query("UPDATE {$_TABLES['fm_access']} SET view=$view, upload=$upload, upload_direct=$direct, upload_ver=$versions, approval=$approval, admin=$admin WHERE catid=$catid AND uid=$uid");
                        fm_updateAuditLog("Updated User Permission record for: $uid, Category: " . $catid );
                    }
                }
            }

            if (!empty($groups)) {
                foreach($groups as $grp_id ) {
                    $query = DB_query("SELECT accid FROM {$_TABLES['fm_access']} WHERE catid={$catid} AND grp_id=$grp_id");
                    if (DB_numRows($query) == 0) {
                        DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('" . $catid. "','0','$grp_id','$view','$upload','$direct','$versions','$approval','$admin')");
                        fm_updateAuditLog("Added new Group: $grp_id, Permission record for Category: " . $catid );
                    } else {
                        DB_query("UPDATE {$_TABLES['fm_access']} SET view=$view, upload=$upload, upload_direct=$direct, upload_ver=$versions, approval=$approval, admin=$admin WHERE catid=$catid AND grp_id=$grp_id");
                        fm_updateAuditLog("Updated perms for Group: $grp_id, Permission record for Category: " . $catid );
                    }
                }
            }
            return true;

        } else {
            return false;
        }

}



/* Function to return and array of subcatories
*  Recursive function calls itself building the list
*
* @param        array      $list        Array of categories
* @param        string     $cid         Category to lookup
* @param        string     $perms       Permissions to check if user has access to catgegory
*/
function fm_getRecursiveCatIDs (&$list,$cid,$perms) {
    global $_CONF,$_TABLES,$_FMCONF;
    $query = DB_QUERY("SELECT cid,pid FROM {$_TABLES['fm_category']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid) = DB_fetchARRAY($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0) {
            if (fm_getPermission($cid, $perms)) {
                array_push($list,$cid);
                fm_getRecursiveCatIDs($list, $cid,$perms);
            }
        } else {
            if (fm_getPermission($cid,$perms)) {
                array_push($list,$cid);
            }
        }
    }
    return $list;
}


/* Function to delete a filemgmt pro category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string     $catid       Category ID to delete
* @param        boolean    $bypass      Optional to Bypass category admin security check
* @return       array      Returns an empty with success (0 for sucess OR 1 for error) and error message
*/
function fm_delCategory($catid,$bypass=false) {
    global $_TABLES,$_CONF, $LANG_FMERR,$LANG_nexfile;
    $errmsg = "";
    if ($bypass OR fm_getPermission($catid,'admin')) {

        /* Build an array of all linked categories under this category the user has admin access to */
        $list = array();
        array_push($list,$catid);
        fm_getRecursiveCatIDs ($list,$catid,'admin');
        foreach ($list as $cid) {
            $catdir = $_CONF['path_html'] .'nexfile/data/' .$cid;
            $catimage = DB_getItem($_TABLES['fm_category'],"image", "cid={$cid}");
            if (file_exists($catdir)) {
                /* Recursively delete all files and folders */
                ppRmdir($catdir);
            }
            DB_query("DELETE FROM {$_TABLES['fm_category']} WHERE cid='{$cid}'");
            DB_query("DELETE FROM {$_TABLES['fm_access']} WHERE catid='{$cid}'");
            $query = DB_query("SELECT fid FROM {$_TABLES['fm_files']} WHERE cid='{$cid}'");
            while (list ($fid) = DB_fetchArray($query))  {
                DB_query("DELETE FROM {$_TABLES['fm_files']} WHERE fid='{$fid}'");
                DB_query("DELETE FROM {$_TABLES['fm_detail']} WHERE fid='{$fid}'");
                DB_query("DELETE FROM {$_TABLES['fm_versions']} WHERE fid='{$fid}'");
            }
        }
        $errmsg = $LANG_nexfile['msg58'];
        fm_updateAuditLog(sprintf($LANG_nexfile['msg57'], $catid));
        return array(1,$errmsg);

    } else {
        $errmsg .= $LANG_FMERR['err11'];
        return array(1,$errmsg);
    }
}

function fm_createCatFolder($path) {
  if (@is_dir($path)) {
    @chmod($path, FM_CHMOD_DIRS);
    return true;
  }
  else {
    $oldumask = umask(0);
    $result = @mkdir($path, FM_CHMOD_DIRS);
    umask($oldumask);
    if (!@is_dir($path) || !$result) {
      $result = @mkdir($path, 0755);
      @chmod($path, FM_CHMOD_DIRS);
    }
    return $result;
  }
}


/* Function to create a filemgmt pro category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string     $catpid      Category Parent ID to create this folder under
* @param        string     $catname     Category Name
* @param        string     $catdesc     Optional Category Description
* @param        boolean    $bypass      Optional to Bypass category admin security check
* @return       array      Returns an array with the Category ID and any error message
*/
function fm_createCategory($catpid,$catname,$catdesc="",$bypass=false) {
    global $_TABLES,$_CONF,$_FMCONF,$LANG_FMERR;

    $errmsg = "";
    // Verify user has admin access rights
    if ($bypass OR fm_getPermission($catpid,'admin')) {
        DB_query("INSERT INTO {$_TABLES['fm_category']} (pid,name,description) VALUES ('$catpid','$catname','$catdesc')");
        $cid = DB_insertId();
        $catdir = $_CONF['path_html'] .'nexfile/data/' . $cid;
        $error = 0;    // Check for error creating directories. If so - then delete new category record
        if (!file_exists($catdir)) {
            $result = fm_createCatFolder($catdir);
            if (!$result) {
                $errmsg .= sprintf($LANG_FMERR['err12'],$catdir);
                $error = 1;
            }
            $result = fm_createCatFolder($catdir.'/images');
            if (!$result) {
                $errmsg .= sprintf($LANG_FMERR['err13'],$catdir);
                $error = 1;
            }
            $result = fm_createCatFolder($catdir .'/submissions');
            if (!$result) {
                $errmsg .= sprintf($LANG_FMERR['err14'],$catdir);
                $error = 1;
            }
            $result = fm_createCatFolder($catdir .'/submissions/images');
            if (!$result) {
                $error = 1;
                $errmsg .= sprintf($LANG_FMERR['err15'],$catdir);
            }
        }

        if ($error != 0 ) {
            DB_query("DELETE FROM {$_TABLES['fm_category']} WHERE cid={$cid}");
            $cid = 0;
        }

    } else {
        $errmsg .= $LANG_FMERR['err16'];
    }

    return array ($cid,$errmsg);

}

function fm_getSubmissionCnt() {
    global $_TABLES;
    // Determine if this user has any submitted files that they can approve
    $query = DB_query("SELECT cid from {$_TABLES['fm_submissions']}");
    $submissions = 0;
    while ( list($catid) = DB_fetchArray($query) ) {
        if (fm_getPermission($catid,'approval')) {
            $submissions++;
        }
    }
    return $submissions;
}


/* Notify Category Admin users that have full admin or upload-approval rights to category
   and have subscribed to category. Call for new files and new file version submissions
*/
function fm_sendAdminApprovalNofications($cid,$sid) {
    global $_CONF,$_TABLES,$_USER, $REMOTE_ADDR, $_FMCONF,$LANG_FM10;

    $q1 = DB_query("SELECT uid, grp_id FROM {$_TABLES['fm_access']} WHERE catid='$cid' AND (admin=1 OR approval=1) " );
    $notifyusers = array();
    while (list ($uid, $grpid) = DB_fetchArray($q1)) {
        if ($grpid > 0) {
            $q2 = DB_query("SELECT ug_uid FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id='$grpid' AND ug_uid IS NOT NULL");
            while (list ($ug_uid) = DB_fetchArray($q2)) {
                if (!in_array($ug_uid,$notifyusers)) {
                    $notifyusers[] = $ug_uid;
                }
            }
        } else {
            $notifyusers[] = $uid;
        }
    }

    foreach ($notifyusers as $notify_uid) {
        if (DB_COUNT($_TABLES['fm_notify'],"uid","{$notify_uid}")) {
            $query = DB_query("SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM {$_TABLES['fm_submissions']} file, {$_TABLES['fm_category']} category WHERE file.cid=category.cid and file.id={$sid}");
            list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
            $subject = '' . $_CONF['site_name'] . ' ' .$LANG_FM10[4]['SUBJECT']. '';
            $submitter_name = DB_getItem($_TABLES['users'],"username", "uid=$submitter");
            $message  = $LANG_FM10[4]['HELLO'] .' '. $target_username . ",\n\n";
            $message .= sprintf($LANG_FM10[4]['LINE1'], $submitter_name,$catname);
            $message .= sprintf($LANG_FM10[4]['LINE2'], $fname, $_CONF['site_url'] . '/nexfile/index.php?op=notifications&cid=' .$cid);
            $message .= $LANG_FM10[4]['LINE3'];
            $message .= $LANG_FM10[4]['LINE4'] . $LANG_FM10[4]['ADMIN']."\n";
            $message .= $_CONF[site_url] . "\n";

            if (function_exists(messenger_send))  {
                $query = DB_query("SELECT notifications,sitepreference FROM {$_TABLES['messenger_userinfo']} WHERE uid={$target_user}");
                list($notifyoption,$sitepreference) = DB_fetchArray($query);
                if (DB_numRows($query) == 0 OR (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 0)) {
                    msg_sendNotification($target_username,$subject,$message,"nexfile");
                } elseif (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 1)  {
                    // Or wants all notifications via the messenger plugin
                    msg_send($target_username,$subject,nl2br($message));
                }
            } else {
                fm_sendEmail($target_username,$subject,$message);
            }
        }
    }
}





/**
* Get a list (actually an array) of all groups this group belongs to.
*
* @param   basegroup   int     id of group
* @return              array   array of all groups 'basegroup' belongs to
*
*/
function fm_getGroupList ($basegroup)
{
    global $_TABLES;

    $to_check = array ();
    array_push ($to_check, $basegroup);

    $groups = array ();

    while (sizeof ($to_check) > 0) {
        $thisgroup = array_pop ($to_check);
        if ($thisgroup > 0) {
            $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $thisgroup");
            $numGroups = DB_numRows ($result);
            for ($i = 0; $i < $numGroups; $i++) {
                $A = DB_fetchArray ($result);
                if (!in_array ($A['ug_grp_id'], $groups)) {
                    if (!in_array ($A['ug_grp_id'], $to_check)) {
                        array_push ($to_check, $A['ug_grp_id']);
                    }
                }
            }
            $groups[] = $thisgroup;
        }
    }

    return $groups;
}


/**
* Get a list (actually an array) of all userss in this group or sub-groups.
*
* @param   basegroup   int     id of group
* @parm    users       Optional int or array of users that are already known
* @return              array   array of all users in group(s)
*
*/
function fm_getUserList ($basegroup,$users='')
{
    global $_TABLES;

    $groups = fm_getGroupList ($basegroup);
    $groupList = implode (',', $groups);
    $sql = 'SELECT DISTINCT uid ';
    $sql .= "FROM {$_TABLES['users']},{$_TABLES['group_assignments']} ";
    $sql .= "WHERE {$_TABLES['users']}.uid > 1 AND {$_TABLES['users']}.uid = ";
    $sql .= "{$_TABLES['group_assignments']}.ug_uid AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList}))";
    $query = DB_query($sql);

    $ret_users = array();
    if ($users != '' AND is_array($users)) {
        $ret_users = $users;
    } else {
        $ret_users[] = $users;
    }
    while (list($uid) = DB_fetchArray($query)) {
        if (!in_array ($uid, $ret_users)) {
            array_push ($ret_users, $uid);
        }
    }
    return $ret_users;

}



/* Navbar function for nexfile - based on ppNavbar */
function fm_navbar ($menuitems, $selected='') {
    global $_CONF;
    require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

    $navbar = new navbar();
    foreach ($menuitems as $label => $url) {
        $navbar->add_menuitem($label, $url);
    }
    $navbar->set_selected($selected);
    $retval = $navbar->generate();

    return $retval;
}


/* Function to display the latest files in a block
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @return       string     Returns a HTML formatted table
*/
function phpblock_nexfile_latestfiles() {
    global $_TABLES,$_CONF,$_USER;

    // Get a list of groups this user is a member of
    $grp_array = SEC_getUserGroups();
    $grp_list = '';
    foreach ($grp_array as $key) {
        if ($grp_list != '') {
            $grp_list .= ",$key";
        } else {
            $grp_list = $key;
        }
    }

    // Now get a list of categories user as view access to
    $sql = "SELECT fid, title, catid, submitter,version,date FROM {$_TABLES['fm_access']} acc ";
    $sql .= "LEFT JOIN {$_TABLES['fm_files']} files ON acc.catid = files.cid ";
    $sql .= "WHERE view = '1' AND ( uid = '{$_USER['uid']}' OR grp_id ";
    $sql .= "IN ( $grp_list ) ) ";
    $sql .= "AND fid IS NOT NULL AND status = '0' ";
    $sql .= "GROUP BY fid ORDER BY date desc LIMIT 7";

    $query = DB_query($sql);
    $retval = '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
    $i = 2;
    while( list( $fid,$fname,$catid,$uid,$version,$date ) = DB_fetchArray($query) ) {
        $description = str_replace('<br />','',DB_getItem($_TABLES['fm_detail'],'description',"fid='$fid'"));
        $author = DB_getItem($_TABLES['users'],'username',"uid='$uid'");
        $date = strftime('%b/%d/%y %I:%M&nbsp;%p',$date);
        $alttag = "Title: $fname\n";
        if(strlen ($fname) > 23) {
            $fname = substr ($fname, 0, 23);
            $fname .= '...';
        }
        $alttag .= "Description:$description\nAuthor:&nbsp;$author\nDate:&nbsp;$date\nVersion:&nbsp;$version";
        $retval .= "<tr><td class=\"plgAlt$i\"><img src=\"{$_CONF['layout_url']}/images/triangle-red.png\">&nbsp;<a href=\"{$_CONF['site_url']}/nexfile/details.php?fid=$fid\" TITLE=\"$alttag\">$fname</a></td></tr>";
        $i = ($i == 2) ? 1:2;
    }
    $retval .= "<tr><td class=\"plgAlt$i\" style=\"text-align:right;padding:5 10 5 0px;\"><a href=\"{$_CONF['site_url']}/nexfile/index.php\">View all Documents ...</a></td></tr>";
    $retval .= '</table>';

    return $retval;

}

function fm_getFilesize($url) {
    $i = 0;  //start count at -1 because we increment AT LEAST once
    if ($stream = @fopen($url, 'rb')) {
        do {
            $byte = fread($stream, 1);
            $i++;
        } while (!feof($stream));

        fclose($stream);
    }
    return $i;
}






?>