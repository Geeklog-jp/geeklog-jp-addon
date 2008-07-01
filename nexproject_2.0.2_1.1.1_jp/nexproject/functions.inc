<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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

$langfile = $_CONF['path'] . 'plugins/nexproject/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexproject/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexproject/config.php');
require_once ($_CONF['path_html'] .'/jscalendar/calendar.php');

/**
 * Returns the items for this plugin that should appear on the main menu
 */
function plugin_getmenuitems_nexproject()
{
    global $_TABLES, $_CONF;
    if (SEC_inGroup('Logged-in Users')) {
        $menuitems['All Projects'] = "{$_CONF['site_url']}/nexproject/projects.php";
        return $menuitems;
    }
}

/**
 * This will put an option for projects admin in the command and control block on moderation.php
 */
function plugin_cclabel_nexproject()
{
    global $_CONF;
    //there is no global/overall nexproject admin options for this plugin.  This is a stub function for any future requirements.

}


//returns the project icon for the project installation routine
function plugin_geticon_nexproject(){
    global $_CONF;

    return $_CONF['site_url'] . '/nexproject/images/nexproject.gif';

}


function plugin_getuseroption_nexproject()
{
    global $_TABLES, $_CONF;

    $menuitems = array(
        array('My Projects', "{$_CONF['site_url']}/nexproject/index.php"),
        array('Custom Project Filters', "{$_CONF['site_url']}/nexproject/filters.php")
    );
    return $menuitems;
    //return array('Custom Project Filters', $_CONF['site_url'] . '/nexproject/filters.php');
}


/**
 * returns the administrative option for this plugin
 */
function plugin_getadminoption_nexproject()
{
    global $_TABLES, $_CONF,$LANG_PRJ_ADMIN;

    if (SEC_inGroup('Root')) {
        return array($LANG_PRJ_ADMIN['nexproject_admin'], $_CONF['site_admin_url'] . '/plugins/nexproject/index.php');
    }

}

/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexproject()
{
    global $_CONF, $_PRJCONF;

    $code ='';
    if ($_PRJCONF['load_calendar']) {
        $code = "\n<!-- Begin PopUp Calendar Javascript includes -->\n";
        $calendar = new DHTML_Calendar($_CONF['site_url'] . '/jscalendar/', 'en', 'calendar-system', false);
        $code .= $calendar->load_files();
        $code .= "\n". '<!-- End PopUp Calendar Javascript includes -->' ."\n";
    }
    $code .= "\n<!-- Begin Javascript includes for Projects Plugin -->\n";
    $code .= "<script type=\"text/javascript\" src=\"".$_CONF['site_url'] ."/nexproject/javascript/nexproject.js\"></script>\n";
    $code .= "<link rel=\"stylesheet\" href=\"".$_CONF['site_url'] ."/layout/". $_CONF['theme'] ."/nexproject/stylesheet.css\"type=\"text/css\">\n";


    $code .= "<!-- END Javascript includes for Projects Plugin -->\n";
    return $code;
}


/**
 * Geeklog is asking us to provide any new items that show up in the type drop-down
 * on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
 */

function plugin_searchtypes_nexproject()
{
    global $LANG_PRJ;

    $tmp['projects'] = $LANG_PRJ['searchlabel'];
    return $tmp;
}

/**
 * this searches for files matching the user query and returns an array of
 * for the header and table rows back to search.php where it will be formated and
 * printed
 *
 * @query string          Keywords user is looking for
 * @datestart date/time       Start date to get results for
 * @dateend date/time       End date to get results for
 * @topic string          The topic they were searching in
 * @type string          Type of items they are searching
 * @author string          Get all results by this author
 */

function plugin_dopluginsearch_nexproject($query, $datestart, $dateend, $topic, $type, $author)
{
    global $LANG_PRJ, $_TABLES, $_CONF, $_CONFPRJ;

    if (empty($type)) {
        $type = 'all';
    }
    if ($author == 0) {
        $author = "";
    }
    $query = str_replace("'", "&rsquo;", $query);
    $query = str_replace('"', "&quot;", $query);
    $query = addslashes($query);
    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type != 'projects') {
        //echo " ... no search performed";
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = $LANG_PRJ['plugin_name'];
        $plugin_results->searchlabel = $LANG_PRJ['searchlabel'];
        return $plugin_results;
    }
    if (isset($_USER['uid'])) {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }

    $groups = SEC_getUserGroups($uid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);
    $sql = "SELECT DISTINCT project.pid as pid,project.name as prj_name,project.last_updated_date as prj_date, users.uid as users FROM {$_TABLES['prj_projects']} project";
    if (!SEC_inGroup('Root')) {
        $sql .= " LEFT JOIN {$_TABLES['prj_projPerms']} perms ON perms.pid = project.pid";
    }
    $sql .= " LEFT JOIN {$_TABLES['prj_users']} users ON users.pid = project.pid WHERE";
    if ($author > 1) {
        $sql .= " (users.uid = {$author}) AND ";
    }
    $sql .= " ((project.name like '%$query%' )";
    $sql .= " OR (project.description like '%$query%') ";
    $sql .= " OR (project.keywords like '%$query%')) ";
    if (!SEC_inGroup('Root')) {
        $sql .= " AND perms.taskID=0 AND (perms.uid=$userid OR perms.gid IN ($prjPermGroups)) ";
    }

    if (!empty($datestart) && !empty($dateend)) {
        $delim = substr($datestart, 4, 1);
        $DS = explode($delim, $datestart);
        $DE = explode($delim, $dateend);
        $startdate = mktime(0, 0, 0, $DS[1], $DS[2], $DS[0]);
        if ($datestart == $dateend) {
            $enddate = mktime(0, 0, 0, $DE[1], $DE[2], $DE[0]) + 86500;
        } else {
            $enddate = mktime(0, 0, 0, $DE[1], $DE[2], $DE[0]) + 3600;
        }
        $sql .= "AND project.last_updated_date BETWEEN '$startdate' AND '$enddate' ";
    }

    $sql .= " ORDER BY project.last_updated_date DESC ";
    // Perform search
    $result = DB_query($sql);
    // OK, now return coma delmited string of table header labels
    // Need to use language variables
    require_once($_CONF['path_system'] . 'classes/plugin.class.php');
    $plugin_results = new Plugin();
    $plugin_results->plugin_name = 'projects';
    $plugin_results->searchlabel = $LANG_PRJ['searchresults'];
    $plugin_results->addSearchHeading("Item");
    $plugin_results->addSearchHeading("Description");
    $plugin_results->addSearchHeading($LANG_PRJ['DATE']);
    $plugin_results->addSearchHeading("Owner");
    $totalsearched = DB_numRows($result);
    $searchcount = 0;
    $resultscount = 0;
    $projects = array();
    for ($i = 1; $i <= $totalsearched; $i++) {
        $A = DB_fetchArray($result);
        $searchcount++;
        if (!in_array(sprintf("%d", $A['pid']), $projects)) {
            $projects[] = $A['pid'];
            $date = strftime('%b %d %y', $A['prj_date']);
            if ($author == "" OR DB_getItem($_TABLES['prj_users'], "uid", "pid={$A['pid']}") == $author) {
                $resultscount++;
                $sqlQuery = DB_query("SELECT users.username FROM
                    {$_TABLES['prj_users']} project LEFT  JOIN {$_TABLES['users']} users ON project.uid = users.uid
                     WHERE project.pid = {$A['pid']} AND project.role='o'");
                list ($username) = DB_fetchArray($sqlQuery);
                if (strlen($A['prj_name']) > 45) {
                    $A['prj_name'] = substr($A['prj_name'], 0, 45) . "....";
                }
                $url = $_CONF['site_url'] . "/nexproject/viewproject.php?pid={$A['pid']}";
                $row = array("$resultscount: Project ->", "<a href=\"$url\">{$A['prj_name']}</a>", $date, $username);
                $plugin_results->addSearchResult($row);
            }
        }
    }
    // Now search all the Project Tasks
    $sql = "SELECT DISTINCT task.tid as tid, task.name as task_name, task.last_updated_date as task_date FROM {$_TABLES['prj_tasks']} task";
    $sql .= " LEFT JOIN {$_TABLES['prj_projects']} proj ON proj.pid = task.pid";
    if (!SEC_inGroup('Root')) {
        $sql .= " LEFT JOIN {$_TABLES['prj_projPerms']} perms ON perms.pid = proj.pid";
    }
    $sql .= " LEFT JOIN {$_TABLES['prj_task_users']} users ON users.tid = task.tid WHERE";
    if ($author > 1) {
        $sql .= " (users.uid = {$author}) AND ";
    }
    $sql .= " ((task.name like '%$query%') ";
    $sql .= " OR (task.description like '%$query%') ";
    $sql .= " OR (task.keywords like '%$query%')) ";
    if (!SEC_inGroup('Root')) {
        $sql .= " AND perms.taskID=0 AND (perms.uid=$userid OR perms.gid IN ($prjPermGroups)) ";
    }
    if (!empty($datestart) && !empty($dateend)) {
        $sql .= "AND task.start_date BETWEEN '$startdate' AND '$enddate' ";
    }
    $sql .= " ORDER BY task.last_updated_date DESC";
    // Perform search
    $result = DB_query($sql);
    $tasks = array();
    $totalsearched = DB_numRows($result);
    for ($i = 1; $i <= $totalsearched; $i++) {
        $A = DB_fetchArray($result);
        $searchcount++;
        if (!in_array(sprintf("%d", $A['tid']), $tasks)) {
            $tasks[] = $A['tid'];
            $date = strftime('%b %d %y', $A['task_date']);
            if ($author == '' OR DB_getItem($_TABLES['prj_task_users'], "uid", "tid={$A['tid']}") == $author) {
                $resultscount++;
                $sqlQuery = DB_query("SELECT users.username FROM
                    {$_TABLES['prj_task_users']} task LEFT  JOIN {$_TABLES['users']} users ON task.uid = users.uid
                     WHERE task.tid = {$A['tid']}");
                list ($username) = DB_fetchArray($sqlQuery);
                if (strlen($A['task_name']) > 45) {
                    $A['task_name'] = substr($A['task_name'], 0, 45) . "....";
                }
                $url = $_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id={$A['tid']}";
                $row = array("$resultscount: Task ->", "<a href=\"$url\">{$A['task_name']}</a>", $date, $username);
                $plugin_results->addSearchResult($row);
            }
        }
    }

    $plugin_results->num_searchresults = $resultscount;
    $plugin_results->num_itemssearched = DB_count($_TABLES['prj_tasks']);
    return $plugin_results;
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexproject() {
    global $_PRJCONF;
    return $_PRJCONF['version'];
}




/**
 * Removes the datastructures for this plugin from the Geeklog database
 * This may get called by the install routine to undue anything created during the install.
 * Added check to see that plugin is first disabled.
 */
function plugin_uninstall_nexproject($installCheck = '')
{
    global $_TABLES, $LANG_PRJ;

    $pi_name = 'nexproject';
    $FEATURES = array ();
    $TABLES = array (   'prj_category', 'prj_department', 'prj_location', 'prj_objective',
                        'prj_permissions', 'prj_users', 'prj_projects', 'prj_sorting', 'prj_task_users',
                        'prj_tasks', 'prj_session','prj_filters', 'prj_statuslog','prj_taskSemaphore','prj_projPerms','prj_lockcontrol','prj_config');

    // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'], 'pi_enabled', 'pi_name = "' . $pi_name . '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it", 1);
        $display .= COM_startBlock($LANG_PRJ['warning']);
        $display .= $LANG_PRJ['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }
    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the projects plugin from Geeklog', 1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = '$pi_name'", 1);

    //now we have to drop the config nexlist, nexfile and forums
    $res=DB_query("SELECT * FROM {$_TABLES['prj_config']}");
    while($A=DB_fetchArray($res)){
        switch($A['config_param']){
            case 'forum_parent_cat':
                    if (prj_forumExists()) {
                        //using this row's config value, we'll delete all forums with this ID as the parent and then chuck out the category itself...
                        $sql="SELECT * FROM {$_TABLES['gf_forums']} where forum_cat={$A['config_value']}";
                        $forumres=DB_query($sql);
                        while($X=DB_fetchArray($forumres)){
                            forum_deleteForum($X['forum_id']);
                        }
                        DB_query("DELETE FROM {$_TABLES['gf_categories']} where id={$A['config_value']}");
                    }
                break;
            case 'nexfile_parent_cat':
                    if (prj_nexFileExists()) {
                        //using this row's config value, we'll delete all categories within this file repository including the parent category
                        fm_delCategory($A['config_value'],true);
                    }
                break;
            case 'nexlist_objectives':
            case 'nexlist_categories':
            case 'nexlist_department':
            case 'nexlist_location':
                    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$A['config_value']}");
                    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$A['config_value']}");
                    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$A['config_value']}");
                break;
            }//end switch
    }

    // Drop project tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t", 1);
        DB_query("DROP TABLE $t", 1);
    }
    // Remove Admin Security for this plugin
    $grp_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = '{$pi_name} admin'");
    COM_errorLog("Removing $pi_name Admin Group - grp_id:$grp_id", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id", 1);
    COM_errorLog("Removing root users from admin of $pi_name");
    DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id", 1);
    // Remove all the associated features - access rights. The feature ID's were stored in the vars table during install.
    foreach ($FEATURES as $feature) {
        COM_errorLog("Removing $feature feature and rights to it", 1);
        $feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
        COM_errorLog("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id");
        DB_query("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id", 1);
        DB_query("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id", 1);
    }

    // Remove the projects filter block
    DB_query("DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn='phpblock_projectFilter'");


    COM_errorLog('...success', 1);
    return true;
}

function plugin_upgrade_nexproject() {
    global $_TABLES,$_PRJCONF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexproject'");

    switch ($curversion) {
    case "2.0.0":
    case "2.0.1":
        if (upgrade_202() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.0.2', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexproject' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexproject'") == $_PRJCONF['version']) {
        return 11;
    } else {
        return 12;
    }
}

function phpblock_projectFilter()
{
    global $_CONF, $_TABLES, $_USER, $strings;
    require_once($_CONF['path_system'] . 'classes/TreeMenu.php');
    $retval = '';
    if ($_USER['uid'] > 1) {
        $menu = new HTML_TreeMenu();
        $baseurl = $_CONF['site_url'];
        $imagesdir = "$baseurl/nexproject/images";
        $folder_icon = "folder.gif";
        $msg = "";
        include ($_CONF['path_html'] . '/nexproject/filterbrowser.php');
    }
    return $retval;
}

function prg_updateAuditLog($logentry)
{
    if (!prj_nexFileExists()) {
        return false;
    }
    global $_USER, $_TABLES;
    if (isset($_USER['uid']) AND $_USER['uid'] != "") {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }
    $date = time();
    $logentry = prj_preparefordb($logentry);
    DB_query("INSERT INTO {$_TABLES['auditlog']} (uid,date,script,logentry) VALUES ('$userid', '$date','{$_SERVER['SCRIPT_NAME']}', '$logentry' )");
    return true;
}

function prj_preparefordb($str, $htmlallowed = false, $htmlfilter = false, $censor = false)
{
    if ($censor) {
        $str = COM_checkWords($str);
    }
    if ($htmlallowed) {
        if (!get_magic_quotes_gpc()) {
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
        if (get_magic_quotes_gpc()) {
            $str = htmlspecialchars($str);
        } else {
            $str = addslashes(htmlspecialchars($str));
        }
        $str = str_replace("'", "&rsquo;", $str);
    }
    return $str;
}
// Callback Function for the array walk function below to apply the data filters to clean any posted data
function prj_cleanfield(&$field)
{
    if (gettype($field) == "string") {
        $field = prj_preparefordb($field);
    }
}
// Function to clean any posted data
function prj_cleandata($postarray)
{
    array_walk($postarray, 'prj_cleanfield');
    return $postarray;
}

function prj_updateSession($pid,$lastop="") {
    global $_USER,$_TABLES, $_CONF;

    if(DB_count( $_TABLES['prj_session'], 'sess_id', $_COOKIE[$_CONF['cookie_session']] )  > 0) {
        DB_query("UPDATE {$_TABLES['prj_session']} SET pid = '$pid', lastop ='$lastop' WHERE sess_id = '{$_COOKIE[$_CONF['cookie_session']]}'");
        /* Delete any other records for this user */
        if($HTTP_COOKIES_VARS[$_CONF['cookie_session']]!=''){
            DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE uid = '{$_USER['uid']}' AND sess_id <> '{$HTTP_COOKIES_VARS[$_CONF['cookie_session']]}'");
        }
    } else {
        /* Delete any other records for this user */
        DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE uid = '{$_USER['uid']}'");

        $time=time();
        DB_query("INSERT INTO {$_TABLES['prj_session']} (sess_id,start_time, uid, pid)
            VALUES ('{$_COOKIE[$_CONF['cookie_session']]}', '$time' , '{$_USER['uid']}', '$pid')");
    }
}


function prj_getSessionProject()
{
    global $_TABLES, $_CONF;
    if (isset($_COOKIE[$_CONF['cookie_session']])) {
        $pid = DB_getItem($_TABLES['prj_session'], "pid" , "sess_id = {$_COOKIE[$_CONF['cookie_session']]}");
        if (isset($pid) AND (DB_count($_TABLES['prj_projects'], "pid", "$pid") > 0)) {
            Return $pid;
        }
    }
    Return 0;
}

/* Clear out the session record used to track which project the user is looking at
/* Delete the user's current session record and any older records */
function prj_clrSession()
{
    global $_TABLES, $_CONF;
    DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE sess_id = '{$_COOKIE[$_CONF['cookie_session']]}'");
    DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE start_time < " . (time() - $_CONF['whosonline_threshold']));
    Return true;
}

/* Used to show the ProjectBrowser Block and a limited set of blocks while on the main Projects page
*  Called by COM_siteHeader to format the leftblocks
*  @param    array      $showblocks     An array of blocks (using block name) as key to show
*/
function prj_showBlocks($showblocks) {
    global $_CONF, $_TABLES;
    $retval = '';
    foreach($showblocks as $block) {
        $sql = "SELECT bid, name,type,title,content,rdfurl,phpblockfn,help FROM {$_TABLES['blocks']} WHERE name='$block' and is_enabled = '1'";
        $result = DB_query($sql);
        if (DB_numRows($result) == 1) {
            $A = DB_fetchArray($result);
            $retval .= COM_formatBlock($A);
        }
    }
    return $retval;
}

/**
* Returns an array of ids of any sub-categories for the passed in category
* Called when adding new members to a project to grant the same access to the project file repository
*
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $cid          Used by this function to be able to append to the formatted select list
* @return       string                            Return an array of category id's
*/
function prj_recursiveCatList($cid=0,$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['fm_category']} WHERE pid = $cid ORDER BY cid");
    $selectlist[] = $cid;
    while ( list($cid,$pid,$name) = DB_fetchArray($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['fm_category'], 'pid', $cid) > 0)  {
            $selectlist[] = $cid;
            $selectlist = prj_recursiveCatList($cid,$selectlist);
        } else {
            $selectlist[] = $cid;
        }
    }
    return $selectlist;
}

function prj_nexFileExists() {
    global $_TABLES;
    return (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexfile' AND pi_enabled=1") == 'nexfile') ? true:false;
}

function prj_forumExists() {
    global $_TABLES;
    return (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='forum' AND pi_enabled=1") == 'forum') ? true:false;
}

?>