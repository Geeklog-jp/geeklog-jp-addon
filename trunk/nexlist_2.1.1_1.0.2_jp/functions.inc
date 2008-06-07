<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
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

$langfile = $_CONF['path'] . 'plugins/nexlist/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexlist/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexlist/config.php');
require_once ($_CONF['path'] . 'plugins/nexlist/library.php');
require_once ($_CONF['path'] . 'plugins/nexlist/custom.php');

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexlist()
{
    global $_TABLES, $_CONF,$LANG_LL01;

    if (SEC_hasRights('nexlist.edit')) {
        $numrecords = DB_count($_TABLES['nexlist']);
        return array($LANG_LL01['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexlist/index.php', $numrecords);
    }

}

/**
* returns the administrative option for this plugin
*
*/
function plugin_cclabel_nexlist()
{
    global $_TABLES, $_CONF,$LANG_LL01;

    if (SEC_hasRights('nexlist.edit')) {
        return array($LANG_LL01['adminmenupanel'],"{$_CONF['site_admin_url']}/plugins/nexlist/index.php",plugin_geticon_nexlist());
    }

}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexlist()
{
    global $_CONF;

    return $_CONF['layout_url'] . '/nexlist/images/admin/nexlist.gif';
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexlist()
{
    global $_CONF, $CONF_LL;

    $code ='';
    if ($CONF_LL['load_sarissa']) {
        $code .= "\n\n<!-- Load Sarissa Library for AJAX Support  -->\n";
        $code .= '<script language=JavaScript src="' . $_CONF['site_url'] . '/javascript/sarissa.js" type="text/javascript"></script>' . LB;
    }
    return $code;
}


/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexlist() {
    global $CONF_LL;
    return $CONF_LL['version'];
}

/* Example use showing the $tag array structure for the nexlist autotag function
*  [list:13,0]            - returns the selectbox html for list #13 - field 0
*  [list:13,0,79]         - returns the selectbox html for list #13 - field 0 and item 79 selected
*  [list:13,0,79,read]    - returns a single value from list #13 - item 79
*
*  Use the last autotag parm when your form has multiple selectbox dropdowns as in the editlist admin page.
*  [list:13,0,79,edit,5]  - returns the selectbox html for list #13 - field 0 and item 79 selected for row5
*
*  [alist:13,0]             - returns an array of list options for list #13, field 0
*/

function plugin_autotags_nexlist($op,$content='',$tag='') {
    global $_CONF,$_TABLES;
    if ($op == 'tagname' ) {
        // Supports standard formatted HTML list or return an array for the list values
        return array('list','alist');
    } elseif ($op == 'parse') {
        $parms = explode(',',$tag['parm1']);
        $listid = $parms[0];
        if (DB_count($_TABLES['nexlist'],'id',$listid) == 1) {
            if (isset($parms[1])) {
                $fieldnum = $parms[1];
            } else {
                $fieldnum = 0;
            }
            if (isset($parms[2])) {
                $selected = $parms[2];
            } else {
                $selected = 0;
            }
            if (isset($parms[3])) {
                $mode = $parms[3];
            } else {
                $mode = 'edit';
            }
            if (isset($parms[4])) {
                $varname  = $parms[4];  // Used to create the edit select fieldname
            } else {
                $varname = 'nexlistfield';
            }
            if ($CONF_LL['debug']) {
                COM_errorLog("functions.inc => listid: $listid, fieldnum:$fieldnum, selected:$selected,mode:$mode");
            }
            if ($tag['tag'] == 'alist') {
                return nexlistOptionList( 'alist', $varname, $listid, $fieldnum,$selected);
            } else {
                $listHTML = nexlistOptionList( $mode, $varname, $listid, $fieldnum,$selected);
                $retval = str_replace($tag['tagstr'],$listHTML,$content);
                return $retval;
            }
        } else {
            return $content;
        }
    }
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexlist($installCheck='')
{
    global $_TABLES, $CONF_LL;

    $pi_name='nexlist';
    $FEATURES = array ('nexlist.edit');
    $TABLES = array ('nexlist','nexlistfields','nexlistitems');

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($CONF_LL['warning']);
        $display .= $CONF_LL['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the nexlist plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nexlist'",1);
    // Drop Plugun tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Admin Security for this plugin
    $grp_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = '{$pi_name} admin'");
    COM_errorLog("Removing $pi_name Admin Group - grp_id:$grp_id", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id", 1);
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

function plugin_upgrade_nexlist() {
    global $_TABLES,$CONF_LL;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexlist'");

    switch ($curversion) {
    case "2.1.0":
        if (upgrade_211() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.1.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexlist' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexlist'") == $CONF_LL['version']) {
        return 11;
    } else {
        return 12;
    }
}


/*
 * Nexlist function that will call out to all plugins to test if they are using this List Item
 * This function is called before a List Item is deleted
 * Pass in the item id and return a message if a plugin function is using the list item record.
 * Plugins need to create a function called nexlist_delitem_{plugin_name}
 * Plugin function needs to accept two parms - listid and item id
 * Plugin function can use the listid to quickly see if list is used
 * Plugins should return a string message or '' if not using the list item
 *
 * Note: Currently it aborts and returns if one plugin reports item is being used
*/
function nexlist_checkItemDependencies($id) {
    global $_TABLES,$_PLUGINS;

    $retval = '';
    $listid = DB_getItem($_TABLES['nexlistitems'],'lid',"id=$id");
    if ($listid > 0 AND $id > 0) {
        foreach ($_PLUGINS as $pi_name) {
            $function = 'nexlist_delitem_' . $pi_name;
            if (function_exists($function)) {
                $retval = $function($listid,$id);
                if ($retval != '') break;
            }
        }
    } else {
        $retval = "Invalid List or list id";
    }
    return $retval;
}


/*
 * Nexlist function that will call out to all plugins to test if they are using this List
 * This function is called before a List is deleted
 * Pass in the list id and return a message if a plugin function is using the list.
 * Plugins need to create a function called nexlist_deldef_{plugin_name}
 * Plugin function needs to accept one parm - listid
 * Plugins should return a string message or '' if not using the list item
 *
 * Note: Currently it aborts and returns if one plugin reports item is being used
*/
function nexlist_checkListDependencies($listid) {
    global $_TABLES,$_PLUGINS;

    $retval = '';
    if ($listid > 0) {
        foreach ($_PLUGINS as $pi_name) {
            $function = 'nexlist_deldef_' . $pi_name;
            if (function_exists($function)) {
                $retval = $function($listid);
                if ($retval != '') break;
            }
        }
    } else {
        $retval = "Invalid List";
    }
    return $retval;
}


?>