<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install.php                                                               |
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

require_once ('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/nexpro/config.php');
require_once ($_CONF['path'] . 'plugins/nexpro/functions.inc');

// Plugin information
$pi_display_name = 'nexPro';
$pi_name         = 'nexpro';
$pi_version      = $CONF_NEXPRO['version'];
$gl_version      = '1.4.1';
$pi_url          = 'http://www.nextide.ca/';


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


    // Finally, register the plugin with Geeklog
    COM_errorLog ("Registering $pi_display_name plugin with Geeklog", 1);

    // silently delete an existing entry
    DB_delete ($_TABLES['plugins'], 'pi_name', $pi_name);

    //modification to make sure nexpro plugin is installed at the top of the list
    $res = DB_query("SELECT pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled FROM {$_TABLES['plugins']} LIMIT 1");
    list ($old_pi_name, $old_pi_version, $old_gl_version, $old_pi_url, $old_pi_enabled) = DB_fetchArray($res);

    //first entry is stored, now UPDATE the first entry with nexpro's
    $sql  = "UPDATE {$_TABLES['plugins']} ";
    $sql .= "SET pi_name='$pi_name', pi_version='$pi_version', pi_gl_version='$gl_version', pi_homepage='$pi_url', pi_enabled=1 ";
    $sql .= "WHERE pi_name='$old_pi_name'";
    DB_query($sql);

    //now insert the first entry to the plugin table again
    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) VALUES "
        . "('$old_pi_name', '$old_pi_version', '$old_gl_version', '$old_pi_url', '$old_pi_enabled')");

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
    else {
        $content = '';
    }
    $p->set_var('content', $content);

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