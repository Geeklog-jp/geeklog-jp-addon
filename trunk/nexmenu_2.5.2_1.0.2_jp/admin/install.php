<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
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

require_once('../../../lib-common.php');
require_once($_CONF['path'] . 'plugins/nexmenu/config.php');
require_once($_CONF['path'] . 'plugins/nexmenu/functions.inc');

//
// Universal plugin install variables
// Change these to match your plugin
//

$pi_name = 'nexmenu';                        // Plugin name
$pi_version = $CONF_NEXMENU['version'];      // Plugin Version
$gl_version = '1.4.1';                       // GL Version plugin for
$pi_url = 'http://www.nextide.ca';           // Plugin Homepage


// Default data
// Insert table name and sql to insert default data for your plugin.

$DEFVALUES = array();
// This will create the block definition and set the default user preferences

//
// Security Feature to add
// Fill in your security features here
// Note you must add these features to the uninstall routine in function.inc so that they will
// be removed when the uninstall routine runs.
// You do not have to use these particular features.  You can edit/add/delete them
// to fit your plugins security model
//

$NEWFEATURE = array();
$NEWFEATURE['nexmenu.edit']        = "Plugin Administration Rights";

/**
* Checks the requirements for this plugin and if it is compatible with this
* version of Geeklog.
*
* @return   boolean     true = proceed with install, false = not compatible
*
*/
function plugin_compatible_with_this_geeklog_version ()
{
    return true;
}


// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the nexmenu install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_NEXMENU00['access_denied']);
    $display .= $LANG_NEXMENU00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}


/**
* Puts the datastructures for this plugin into the Geeklog database
*
* Note: Corresponding uninstall routine is in functions.inc
* 
* @return    boolean    True if successful False otherwise
*
*/
function plugin_install_now()
{
    global $pi_name, $pi_version, $gl_version, $pi_url, $NEWTABLE, $DEFVALUES, $NEWFEATURE;
    global $_TABLES, $_CONF, $_DB_dbms;

    COM_errorLog("Attempting to install the $pi_name Plugin",1);
    $uninstall_plugin = 'plugin_uninstall_' . $pi_name;

    // Create the Plugins Tables
    require_once($_CONF['path'] . 'plugins/nexmenu/sql/' . $_DB_dbms . '_install_' . $pi_version . '.php');

    for ($i = 1; $i <= count($_SQL); $i++) {
        $progress .= "executing " . current($_SQL) . "<br>\n";
        COM_errorLOG("executing " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("Error Creating $table table",1);
            $uninstall_plugin ('DeletePlugin');
            return false;
            exit;
        }
        next($_SQL);
    }
    COM_errorLog("Success - Created $table table",1);
       
    // Insert Default Data
    
    foreach ($DEFVALUES as $table => $sql) {
        COM_errorLog("Inserting default data into $table table",1);
        DB_query($sql,1);
        if (DB_error()) {
            COM_errorLog("Error inserting default data into $table table",1);
            $uninstall_plugin ();
            return false;
            exit;
        }
        COM_errorLog("Success - inserting data into $table table",1);
    }
    
    // Create the plugin admin security group
    COM_errorLog("Attempting to create $pi_name admin group", 1);
    DB_query("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr) "
        . "VALUES ('$pi_name Admin', 'Users in this group can administer the $pi_name plugin')",1);
    if (DB_error()) {
        $uninstall_plugin();
        return false;
        exit;
    }
    COM_errorLog('...success',1);
    $query = DB_query("SELECT max(grp_id) FROM {$_TABLES['groups']} ");
    list ($group_id) = DB_fetchArray($query);

    // Save the grp id for later uninstall
    COM_errorLog('About to save group_id to vars table for use during uninstall',1);
    DB_query("INSERT INTO {$_TABLES['vars']} VALUES ('{$pi_name}_admin', $group_id)",1);
    if (DB_error()) {
        $uninstall_plugin ();
        return false;
        exit;
    }
    COM_errorLog('...success',1);

    // Add plugin Features
    foreach ($NEWFEATURE as $feature => $desc) {
        COM_errorLog("Adding $feature feature",1);
        DB_query("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) "
            . "VALUES ('$feature','$desc')",1);
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature",1);
            $uninstall_plugin ();
            return false;
            exit;
        }
        $query = DB_query("SELECT max(ft_id) FROM {$_TABLES['features']} ");
        list ($feat_id) = DB_fetchArray($query);

        COM_errorLog("Success",1);
        COM_errorLog("Adding $feature feature to admin group",1);
        DB_query("INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($feat_id, $group_id)");
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature to admin group",1);
            $uninstall_plugin ();
            return false;
            exit;
        }
        COM_errorLog("Success",1);
    }        
    
    // OK, now give Root users access to this plugin now! NOTE: Root group should always be 1
    COM_errorLog("Attempting to give all users in Root group access to $pi_name admin group",1);
    DB_query("INSERT INTO {$_TABLES['group_assignments']} VALUES ($group_id, NULL, 1)");
    if (DB_error()) {
        $uninstall_plugin ();
        return false;
        exit;
    }

    // Register the plugin with Geeklog
    COM_errorLog("Registering $pi_name plugin with Geeklog", 1);
    DB_delete($_TABLES['plugins'],'pi_name',$pi_name);
    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) "
        . "VALUES ('$pi_name', '$pi_version', '$gl_version', '$pi_url', 1)");

    if (DB_error()) {
        $uninstall_plugin ();
        return false;
        exit;
    }

    COM_errorLog("Succesfully installed the $pi_name Plugin!",1);
    return true;
}

function show_prerequisites() {
    global $_CONF, $_TABLES, $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
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