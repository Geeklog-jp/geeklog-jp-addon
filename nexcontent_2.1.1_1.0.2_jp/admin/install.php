<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install.php                                                               |
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

require_once ('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/nexcontent/config.php');
require_once ($_CONF['path'] . 'plugins/nexcontent/functions.inc');

// Plugin information
$pi_display_name = 'nexcontent';
$pi_name         = 'nexcontent';
$pi_version      = $CONF_SE['version'];
$gl_version      = '1.4.1';
$pi_url          = 'http://www.nextide.ca/';


// name of the Admin group
$pi_admin        = $pi_display_name . ' Admin';

// the plugin's groups - assumes first group to be the Admin group
$GROUPS = array();
$GROUPS[$pi_admin] = 'Has full access to ' . $pi_name . ' features';

$FEATURES = array();
$FEATURES['nexcontent.edit']       = 'Administrative access to ' . $pi_display_name;
$FEATURES['nexcontent.user']       = "Plugin user permission - Required if user will have edit rights to pages";

$MAPPINGS = array();
$MAPPINGS['nexcontent.edit']         = array ($pi_admin);
$MAPPINGS['nexcontent.user']         = array ($pi_admin);

// (optional) data to pre-populate tables with
// Insert table name and sql to insert default data for your plugin.
// Note: '#group#' will be replaced with the id of the plugin's admin group.
$DEFVALUES = array();

// more default data is in the install SQL file in the plugin's directory

/**
* Checks the requirements for this plugin and if it is compatible with this
* version of Geeklog.
*
* @return   boolean     true = proceed with install, false = not compatible
*
*/
function plugin_compatible_with_this_geeklog_version ()
{
    if (function_exists ('COM_showPoll') || function_exists ('COM_pollVote')) {
        // if these functions exist, then someone's trying to install the
        // plugin on Geeklog 1.3.11 or older - sorry, but that won't work
        return false;
    }

    if (!function_exists ('SEC_getGroupDropdown')) {
        return false;
    }

    return true;
}

/**
* When the install went through, give the plugin a chance for any 
* plugin-specific post-install fixes
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_postinstall ()
{ 
    return true;
}

// The code below should be the same for most plugins and usually won't
// require modifications.

$base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';
$langfile = $base_path . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    require_once ($langfile);
} else {
    require_once ($base_path . 'language/english.php');
}
require_once ($base_path . 'config.php');
require_once ($base_path . 'functions.inc');


// Only let Root users access this page
if (!SEC_inGroup ('Root')) {
    // Someone is trying to illegally access this page
    COM_accessLog ("Someone has tried to illegally access the {$pi_display_name} install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);

    $display = COM_siteHeader ('menu', $LANG_ACCESS['accessdenied'])
             . COM_startBlock ($LANG_ACCESS['accessdenied'])
             . $LANG_ACCESS['plugin_access_denied_msg']
             . COM_endBlock ()
             . COM_siteFooter ();

    echo $display;
    exit;
}
 

/**
* Puts the datastructures for this plugin into the Geeklog database
*
*/
function plugin_install_now()
{
    global $_CONF, $_TABLES, $_USER, $_DB_dbms,
           $GROUPS, $FEATURES, $MAPPINGS, $DEFVALUES, $base_path,
           $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    COM_errorLog ("Attempting to install the $pi_display_name plugin", 1);

    $uninstall_plugin = 'plugin_uninstall_' . $pi_name;

    // create the plugin's groups
    $admin_group_id = 0;
    foreach ($GROUPS as $name => $desc) {
        COM_errorLog ("Attempting to create $name group", 1);

        $grp_name = addslashes ($name);
        $grp_desc = addslashes ($desc);
        DB_query ("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr) VALUES ('$grp_name', '$grp_desc')", 1);
        if (DB_error ()) {
            $uninstall_plugin ();

            return false;
        }

        // replace the description with the new group id so we can use it later
        $GROUPS[$name] = DB_insertId ();

        // assume that the first group is the plugin's Admin group
        if ($admin_group_id == 0) {
            $admin_group_id = $GROUPS[$name];
        }
    }

    // Create the plugin's table(s)
    $_SQL = array ();
    if (file_exists ($base_path . 'sql/' . $pi_name . '_' . $_DB_dbms . '_install_' . $pi_version . '.php')) {
        require_once ($base_path . 'sql/' . $pi_name . '_' . $_DB_dbms . '_install_' . $pi_version . '.php');
    }

    foreach ($_SQL as $sql) {
        $sql = str_replace ('#group#', $admin_group_id, $sql);
        DB_query ($sql);
        if (DB_error ()) {
            COM_errorLog ('Error creating table', 1);
            $uninstall_plugin ();

            return false;
        }
    }

    // Add the plugin's features
    COM_errorLog ("Attempting to add $pi_display_name feature(s)", 1);

    foreach ($FEATURES as $feature => $desc) {
        $ft_name = addslashes ($feature);
        $ft_desc = addslashes ($desc);
        DB_query ("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) "
                  . "VALUES ('$ft_name', '$ft_desc')", 1);
        if (DB_error ()) {
            $uninstall_plugin ();

            return false;
        }

        $feat_id = DB_insertId ();

        if (isset ($MAPPINGS[$feature])) {
            foreach ($MAPPINGS[$feature] as $group) {
                COM_errorLog ("Adding $feature feature to the $group group", 1);
                DB_query ("INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($feat_id, {$GROUPS[$group]})");
                if (DB_error ()) {
                    $uninstall_plugin ();

                    return false;
                }
            }
        }
    }

    // Add plugin's Admin group to the Root user group
    // (assumes that the Root group's ID is always 1)
    COM_errorLog ("Attempting to give all users in the Root group access to the $pi_display_name's Admin group", 1);

    DB_query ("INSERT INTO {$_TABLES['group_assignments']} VALUES "
              . "($admin_group_id, NULL, 1)");
    if (DB_error ()) {
        $uninstall_plugin ();

        return false;
    }

    // Pre-populate tables or run any other SQL queries
    if ($_DB_dbms != 'mssql') {
        COM_errorLog ('Inserting default data', 1);
        foreach ($DEFVALUES as $sql) {
            $sql = str_replace ('#group#', $admin_group_id, $sql);
            DB_query ($sql, 1);
            if (DB_error ()) {
                $uninstall_plugin ();
                return false;
            }
        }
    }
    // Finally, register the plugin with Geeklog
    COM_errorLog ("Registering $pi_display_name plugin with Geeklog", 1);

    // silently delete an existing entry
    DB_delete ($_TABLES['plugins'], 'pi_name', $pi_name);

    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) VALUES "
        . "('$pi_name', '$pi_version', '$gl_version', '$pi_url', 1)");

    if (DB_error ()) {
        $uninstall_plugin ();

        return false;
    }

    // give the plugin a chance to perform any post-install operations
    if (function_exists ('plugin_postinstall')) {
        if (!plugin_postinstall ()) {
            $uninstall_plugin ();

            return false;
        }
    }

    COM_errorLog ("Successfully installed the $pi_display_name plugin!", 1);

    return true;
}

function show_prerequisites() {
    global $CONF_SE, $_CONF, $_TABLES, $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    $disabled = '';

    $p = new Template($_CONF['path'] . 'plugins/' . $pi_name . '/templates/');
    $p->set_file('prereq_form', 'prereq_form.thtml');

    $p->set_var('layout_url', $_CONF['layout_url']);
    $p->set_var('site_admin_url', $_CONF['site_admin_url']);
    $p->set_var('pi_name', $pi_name);
    $p->set_var('pi_display_name', $pi_display_name);
    $p->set_var('pi_image', PLG_getIcon($pi_name));

    $content_function = 'plugin_getinstallcontent_' . $pi_name;
    if (function_exists($content_function)) {
        $content = $content_function();
    }
    else if (function_exists('plugin_getinstallcontent_nexpro')) {
        $content = plugin_getinstallcontent_nexpro();
    }
    else {
        $content = '';
    }
    $p->set_var('content', $content);

    //test if nexpro plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexpro' && pi_enabled=1") != '') ? 'icon_check.png':'icon_fail.png';
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: nexPro is not installed or enabled");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexPro plugin is installed and enabled', true);

    //test if data directory has write permissions
    $fp = @fopen($CONF_SE['uploadpath'] . 'test.txt', 'w');
    if ($fp != NULL) {
        fclose($fp);
        unlink($CONF_SE['uploadpath'] . 'test.txt');
        $image = 'icon_check.png';
    }
    else {
        $image = 'icon_fail.png';
    }
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: Cannot write to \"{$CONF_SE['uploadpath']}\"");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> Write permissions on "'.$CONF_SE['uploadpath'] . '"', true);

    $p->set_var('disabled', $disabled);

    $p->parse('output', 'prereq_form');

    return $p->finish($p->get_var('output'));
}


// MAIN
$action = $_REQUEST['action'];
$display = '';

switch ($action) {
case 'install':     //display page to check for requirements
    if (DB_count ($_TABLES['plugins'], 'pi_name', $pi_name) == 0) {
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . show_prerequisites()
                 . COM_siteFooter();
    } else {
        // plugin already installed
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . COM_startBlock ($LANG32[6])
                 . '<p>' . $LANG32[7] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter();
    }
    break;

case 'uninstall': //uninstall the plugin
    if ($_REQUEST['action'] == 'uninstall') {
        $uninstall_plugin = 'plugin_uninstall_' . $pi_name;
        if ($uninstall_plugin ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=45');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=73');
        }
    }

case 'install_now': //install the plugin
    if (plugin_compatible_with_this_geeklog_version ()) {
        if (plugin_install_now ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=44');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=72');
        }
    } else {
        // plugin needs a newer version of Geeklog
        $display .= COM_siteHeader ('menu', $LANG32[8])
                 . COM_startBlock ($LANG32[8])
                 . '<p>' . $LANG32[9] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter ();
    }
    break;
}

echo $display;

?>