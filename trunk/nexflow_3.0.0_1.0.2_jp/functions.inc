<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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

require_once ($_CONF['path'] . 'plugins/nexflow/nexflow.class.php');
require_once ($_CONF['path'] . 'plugins/nexflow/config.php');
require_once ($_CONF['path'] . 'plugins/nexflow/library.php');

//in the event the navbar class is not instantiated by the base portal on older systems, this line needs
//to be uncommented
//require_once ($_CONF['path_system'] . 'classes/navbar.class.php');


/* Include the base nextide provided workflow functions as well as all custom function library files */
$customFunctionsDir = $_CONF['path'] .'plugins/nexflow/custom_functions/';
if (is_dir($customFunctionsDir)) {
   if ($dh = opendir($customFunctionsDir)) {
       while (($file = readdir($dh)) !== false) {
           if (filetype($customFunctionsDir . $file) == 'file') {
                include_once ($customFunctionsDir . $file);
           }
       }
       closedir($dh);
   }
}

$langfile = $_CONF['path'] . 'plugins/nexflow/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexflow/language/english.php');
}



/* Used to run the nexflow Orchestrator manually by the plugin
* Set the site config.php parm for the interval to something like 20, 30 or 60 seconds
* This function will then be called to complete all possible workflow queue tasks
*/
function plugin_runScheduledTask_nexflow() {
    global $CONF_NF;

    /* Check and see if we need to run the workflow Orchesrtator or if it's auto-scheduled by the Server */
    if (!$CONF_NF['orchestrator_using_cron']) {
        $processlist = array();

        $nfclass = new nexflow();
        $nfclass->clean_queue();

        //$nfclass->set_debug(true);
        $retval = nf_getListofUncompletedTasks(&$processlist);
        $taskcount = $retval['count'];
        $processlist = $retval['list'];
        $i = 1;

        // Loop thru the workflow orchestrato while we have new un-completed tasks
        while($taskcount > 0){
            if ($nfclass->_debug ) {
                COM_errorLog("Orchestrator: Loop:$i, $taskcount un-completed tasks found");
            }
            $nfclass->clean_queue();
            $retval = nf_getListofUncompletedTasks(&$processlist);
            $taskcount = $retval['count'];
            $processlist = $retval['list'];
            $i++;
        }
    }
}

function plugin_getuseroption_nexflow() {
    global $_CONF, $_USER, $CONF_NF, $LANG_NF00;

    if ( $_USER['uid'] > 1 AND !$CONF_NF['taskconsolepermrequired']) {
       return array ($LANG_NF00['useradminmenu'], $_CONF['site_url'] . '/nexflow/index.php');
    } elseif (SEC_hasRights('nexflow.user')) {
       return array ($LANG_NF00['useradminmenu'], $_CONF['site_url'] . '/nexflow/index.php');
    }
}



function plugin_getadminoption_nexflow() {
    global $_CONF, $_TABLES;
    if(SEC_hasRights('nexflow.admin')){
        return array ('nexFlow', $_CONF['site_admin_url'] . '/plugins/nexflow/templates.php');
    }
}


/**
* This will put an option for nexfile admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexflow()
{
    global $_CONF,$LANG_NF00;
    if (SEC_hasRights('nexflow.admin')) {
        return array($LANG_NF00['adminmenu'],$_CONF['site_admin_url'] . "/plugins/nexflow/catman.php",plugin_geticon_nexflow());
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexflow()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexflow/images/admin/nexflow.gif';
}




/**
* Called if user is created - Adds a default userpref record which is used when member edits account preferences
*/
function plugin_user_create_nexflow ($uid)
{
    global $_TABLES;
    DB_query ("INSERT INTO {$_TABLES['nfuseraway']} (uid) VALUES ('{$uid}')");
}


/**
* Called if admin deletes a user - remove all user messages as well
*/
function plugin_user_delete_nexflow ($uid)
{
    global $_TABLES;

    // Delete the user preference record
    DB_query ("DELETE FROM {$_TABLES['nfuseraway']} WHERE uid = '{$uid}'");

}

function plugin_profilevariablesedit_nexflow($uid, &$template)
{
    global $_CONF, $_TABLES, $LANG_NF01;

    $p = new Template($_CONF['path_layout'] . '/nexflow/userprefs');
    $p->set_file ('prefs', 'awaysettings_edit.thtml');
    $p->set_var('layout_url',$_CONF['layout_url']);

    $query = DB_query ("SELECT away_start,away_return,reassign_uid,reason,is_active FROM {$_TABLES['nfuseraway']} WHERE uid = $uid");
    list ($datestart,$datereturn,$reassign_uid,$reason,$is_active) = DB_fetchArray ($query);

    if ($datestart == 0) $datestart = time();
    if ($datereturn == 0) $datereturn = time();
    if ($is_active) {
        $p->set_var('chk_awayactive', 'CHECKED');
    } else {
        $p->set_var('chk_awayactive', '');
    }

    $p->set_var ('away_sdate', strftime('%m-%d-%Y',$datestart));
    $p->set_var ('away_rdate',  strftime('%m-%d-%Y',$datereturn));
    $p->set_var ('away_reassignoptions', nf_listUsers($reassign_uid));
    $p->set_var ('away_reason', $reason);

    $p->set_var('lang_awaytitle', $LANG_NF01['title']);
    $p->set_var ('lang_awaylabel', $LANG_NF01['label']);
    $p->set_var ('lang_awayfrom', $LANG_NF01['from']);
    $p->set_var ('lang_awayto', $LANG_NF01['to']);
    $p->set_var ('lang_awayreason', $LANG_NF01['reason']);
    $p->set_var ('lang_selectowner', $LANG_NF01['selectowner']);
    $p->set_var ('lang_awayowner', $LANG_NF01['owner']);
    $p->set_var ('lang_awayactive', $LANG_NF01['active']);
    $template->set_var('out_of_office_fields',$p->finish($p->parse ('output', 'prefs')));

}

function plugin_profilevariablesdisplay_nexflow($uid, &$template)
{
    global $_CONF,$_TABLES, $LANG_NF01;

    $query = DB_query ("SELECT away_start,away_return,reassign_uid,reason,is_active FROM {$_TABLES['nfuseraway']} WHERE uid = $uid");
    list ($datestart,$datereturn,$reassign_uid,$reason,$is_active) = DB_fetchArray ($query);

    if ($is_active == 1 AND time() > $datestart AND time() < $datereturn) {
        $dispStartDate = COM_getUserDateTimeFormat($datestart);
        $dispReturnDate = COM_getUserDateTimeFormat($datereturn);
        $p = new Template($_CONF['path_layout'] . '/nexflow/userprefs');
        $p->set_file ('prefs', 'awaysettings_view.thtml');
        $p->set_var ('away_sdate', $dispStartDate[0]);
        $p->set_var ('away_rdate',  $dispReturnDate[0]);
        $p->set_var ('away_reason', nl2br($reason));

        $p->set_var('lang_awaytitle', $LANG_NF01['title']);
        $p->set_var ('lang_awaylabel', $LANG_NF01['label']);
        $p->set_var ('lang_awayfrom', $LANG_NF01['from']);
        $p->set_var ('lang_awayto', $LANG_NF01['to']);
        $p->set_var ('lang_awayreason', $LANG_NF01['reason']);
        $p->set_var ('lang_selectowner', $LANG_NF01['selectowner']);
        $p->set_var ('lang_awayowner', $LANG_NF01['owner']);
        $p->parse ('output', 'prefs');
        $template->set_var('out_of_office_message', $p->finish($p->get_var('output')));

    }
}

function plugin_profileextrassave_nexflow()
{
    global $_USER, $_TABLES;
    $uid = $_USER['uid'];

    $startdate = COM_applyFilter(ppConvertDate($_POST['away_sdate']),true);
    $returndate = COM_applyFilter(ppConvertDate($_POST['away_rdate']),true);
    $reassignuid = COM_applyFilter($_POST['away_ressignuid'],true);
    $reason =  addslashes(COM_checkWords(COM_checkHTML(COM_killJS($_POST['away_reason']))));
    $is_active = COM_applyFilter($_POST['away_chkstatus'],true);

    if (DB_count($_TABLES['nfuseraway'],'uid',$uid) == 0) {
        $sql = "INSERT INTO {$_TABLES['nfuseraway']} (uid,away_start,away_return,reassign_uid,reason,is_active) VALUES ";
        $sql .= "($uid,$startdate,$returndate,$reassignuid,'$reason',$is_active) ";

    } else {
        $sql  = "UPDATE {$_TABLES['nfuseraway']} SET away_start=$startdate,away_return=$returndate, ";
        $sql .= "reassign_uid=$reassignuid,reason='$reason',is_active=$is_active WHERE uid=$uid";
    }
    DB_query($sql);

}


function plugin_getHeaderCode_nexflow() {
    global $_CONF;

    $code  = "\n\n<!-- Begin nexflow  -->\n";
    $code .= '<script language="javascript" src="' . $_CONF['site_url'] . '/nexflow/include/header.js" type="text/javascript"></script>';
    $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/nexflow/include/wz_jsgraphics.js"></script>';
    $code .= "\n<!-- End nexflow -->\n\n";
    return $code;

}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexflow() {
    global $CONF_NF;
    return $CONF_NF['version'];
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexflow($installCheck='')
{
    global $_TABLES, $LANG_NF00;

    $pi_name='nexflow';
    $FEATURES = array ('nexflow.admin','nexflow.edit', 'nexflow.user');
    $TABLES = array (
        'nfprocess',
        'nfqueue',
        'nftemplate',
        'nftemplatedata',
        'nftemplatevariables',
        'nftemplateassignment',
        'nfhandlers',
        'nfsteptype',
        'nftemplatedatanextstep',
        'nfprocessvariables',
        'nfifprocessarguments',
        'nfifoperators',
        'nfqueuefrom',
        'nfnotifications',
        'nfproductionassignments',
        'nfuseraway',
        'nfprojects',
        'nfproject_forms',
        'nfproject_timestamps',
        'nfproject_comments',
        'nfproject_taskhistory',
        'nfproject_approvals',
        'nfappgroups'
    );

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_NF00['warning']);
        $display .= $LANG_NF00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = '$pi_name'",1);
    // Drop Plugin tables
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

    COM_errorLog('...success',1);
    return true;
}

function plugin_upgrade_nexflow() {
    global $_TABLES,$CONF_NF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexflow'");

    switch ($curversion) {
    case "2.0.0":
        if (upgrade_300() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.0.0', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexflow' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexflow'") == $CONF_NF['version']) {
        return 11;
    } else {
        return 12;
    }
}

// function to test permissions on directories
function plugin_chkPermissions_nexflow(&$template) {
    global $CONF_NF;

    if (!$file = @fopen ($CONF_NF['export_dir'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_NF['export_dir'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_NF['export_dir'] . 'test.txt');
    }

    return $display;
}

/* Function call my lookuplist admin - when deleting a list item
 * Gives us a chance to see if item is being used and abort the delete
 * Custom workflow functions that use lookuplists should append any lists used
 * to the Nexflow common lookuplist array $NF_LISTS
*/
function nexlist_delitem_nexflow($listid,$id) {
    global $_TABLES,$NF_LISTS;

    $retval = '';
    // Check and see if this lookuplist is used
    if (in_array($listid,$NF_LISTS)) {
        /* Add logic here or in the custom function located in the function nf_chkLookupListsbeforeDelete()
         * This function is located in the file plugins/nexflow/custom_functions/custom_batch_functions.php
        */
        $retval = nf_chkLookupListsbeforeDelete($listid, $id);
    }
    return $retval;
}

/* Function call my lookuplist admin - when deleting a Lookup List Definition
 * Gives us a chance to see if list is being used and abort the delete
*/
function nexlist_deldef_nexflow($listid) {
    global $_TABLES,$NF_LISTS;

    $retval = '';
    // Check and see if this lookuplist is used
    if (in_array($listid,$NF_LISTS)) {
        $retval = 'Nexflow custom function is using this list';
    }
    return $retval;
}

/**
* Logs messages to nfchange.log
* Writes a nexflow related message to an error log.
* @param        string      $logentry       Text to log to error log
* @return       nothing
*/
function nf_changeLog( $logentry )
{
    global $_CONF, $LANG01;

    $retval = '';
    if( !empty( $logentry ))   {
        $timestamp = strftime( '%c' );
        $logfile = $_CONF['path_log'] . 'nfchange.log';
        if($file = fopen( $logfile, 'a' )) {
            fputs( $file, "$timestamp - $logentry \n" );
        }
    }
}

?>