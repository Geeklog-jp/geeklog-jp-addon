<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0                                               |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by {author_name} - {author_email}                      |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// | Copyright (C) 2008-2010 by the following authors:                         |
// | Dirk Haun                  - dirk AT haun-online DOT de                   |
// | hiroron                    - hiroron AT hiroron DOT com                   |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Autoinstall API functions for the {display_name} plugin
*
* @package {display_name}
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_{plugin}($pi_name)
{
    $pi_name         = '{plugin}';
    $pi_display_name = '{display_name}';
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '1.0.0',
        'pi_gl_version'   => '{gl_version}',
        'pi_homepage'     => '{pi_url}'
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.view'    => 'Ability to view ' . $pi_display_name,
        $pi_name . '.edit'    => 'Access to ' . $pi_display_name . ' editor'
    );

    $mappings = array(
        $pi_name . '.view'    => array($pi_admin),
        $pi_name . '.edit'    => array($pi_admin)
    );

    $tables = array(
        '{plugin}'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
}

/**
* Load plugin configuration from database
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true on success, otherwise false
* @see      plugin_initconfig_staticpages
*
*/
function plugin_load_configuration_{plugin}($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_{plugin}();
}

/**
* Does post-installation operations
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_postinstall_{plugin}($pi_name)
{
    global $_CONF, $_TABLES;

    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $plg_config = config::get_instance();
    $_PLG_CONF = $plg_config->get_config('{plugin}');

    $inst_parms = plugin_autoinstall_{plugin}($pi_name);
    $pi_admin = key($inst_parms['groups']);

    $admin_group_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = '{$pi_admin}'");
    $blockadmin_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Block Admin'");

    $DEF_SQL = array();
    $DEF_SQL[] = "INSERT INTO {$_TABLES['{plugin}']} (up_title, up_value, owner_id, group_id) VALUES ('Sample Data', 100, 2, #group#)";

    $DEF_SQL[] = "INSERT INTO {$_TABLES['{plugin}']} (up_title, up_value, owner_id, group_id) VALUES ('More Sample Data', 200, 2, #group#)";

    foreach ($DEF_SQL as $sql) {
        $sql = str_replace('#group#', $admin_group_id, $sql);
        DB_query($sql, 1);
        if (DB_error()) {
            COM_errorLog("SQL error in {plugin} plugin postinstall, SQL: " . $sql);
            return false;
        }
    }

    return true;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_{plugin}($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    if (! function_exists('SEC_getGroupDropdown')) {
        return false;
    }

    if (! function_exists('SEC_createToken')) {
        return false;
    }

    if (! function_exists('COM_showMessageText')) {
        return false;
    }

    if (! function_exists('COM_setLangIdAndAttribute')) {
        return false;
    }

    if (! isset($_CONF['meta_tags'])) {
        return false;
    }

    if (! function_exists('SEC_getTokenExpiryNotice')) {
        return false;
    }

    return true;
}

?>
