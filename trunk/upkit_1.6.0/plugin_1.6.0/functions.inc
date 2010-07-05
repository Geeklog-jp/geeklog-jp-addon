<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | plugins/{plugin}/functions.inc                                            |
// +---------------------------------------------------------------------------|
// | Copyright (C) 2008 by {author_name} - {author_email}                      |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// | Copyright (C) 2002 by the following authors:                              |
// | Tom Willett               -    twillett AT users DOT sourceforge DOT net  |
// | Blaine Lang               -    langmail AT sympatico DOT ca               |
// | The Universal Plugin is based on prior work by:                           |
// | Tony Bibbs                -    tony AT tonybibbs DOT com                  |
// | Modified by:                                                              |
// | mystral-kk                -    geeklog AT mystral-kk DOT net              |
// | dengen                    -    taharaxp AT gmail DOT com                  |
// | hiroron                   -    hiroron AT hiroron DOT com                 |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

/**
* Include language file
*/
$plugin_path = $_CONF['path'] . 'plugins/{plugin}/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/*
* Check and see if we need to load the plugin configuration
*/
{conf_var_prefix}_loadConfig();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_{plugin}()
{
    global $_CONF, $_{conf_var_prefix}_CONF, $LANG_{lang_var_postfix};

    if (($_{conf_var_prefix}_CONF['hide{plugin}menu'] == 1) || (COM_isAnonUser() &&
            ($_CONF['loginrequired'] || $_{conf_var_prefix}_CONF['{plugin}loginrequired']))) {
        return false;
    }

    $menuitems[$LANG_{lang_var_postfix}['menu_title']] = $_CONF['site_url'] . '/{plugin}/index.php';

    return $menuitems;
}

/**
* Check to see if this Plugin uses comments
*
* Geeklog is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how geeklog
* does comments.
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_{plugin}()
{
    return false;
}

/**
* Shows the statistics for the {plugin} plugin on stats.php.
* If $showsitestats is 1 then we are to only print the overall stats in the
* 'site statistics box' otherwise we show the detailed stats
*
* @param    int     $showsitestate  Flag to let us know which stats to get
* @param    string                  HTML for the stats section
*
*/
function plugin_showstats_{plugin}($showsitestats)
{
    global $_CONF, $_TABLES, $LANG_{lang_var_postfix};

    require_once($_CONF['path_system'] . 'lib-admin.php');

    $retval = '';

    $result = DB_query("SELECT title,value FROM {$_TABLES['{plugin}']} WHERE (value > 0)"
                     . COM_getPermSQL ('AND')
                     . " ORDER BY value DESC LIMIT 10");
    $nrows  = DB_numRows($result);
    if ($nrows > 0) {
        $header_arr = array(
            array('text'         => $LANG_{lang_var_postfix}['stats_title'],
                  'field'        => 'title',
                  'header_class' => 'stats-header-title'
            ),
            array('text'         => $LANG_{lang_var_postfix}['stats_value'],
                  'field'        => 'value',
                  'header_class' => 'stats-header-count',
                  'field_class'  => 'stats-list-count'
            )
        );
        $data_arr = array();
        $text_arr = array('has_menu' => false,
                          'title'    => $LANG_{lang_var_postfix}['stats_headline'],
        );
        for ($i = 0; $i < $nrows; $i++) {
            $A = DB_fetchArray($result);
            $title = stripslashes(str_replace ('$', '&#36;', $A['title']));
            $value = COM_numberFormat($A['value']);
            $data_arr[] = array('title' => $title,
                                'value' => $value
                          );
        }
        $retval .= ADMIN_simpleList('', $header_arr, $text_arr, $data_arr);
    } else {
        $retval .= COM_startBlock($LANG_{lang_var_postfix}['stats_headline']);
        $retval .= $LANG_{lang_var_postfix}['stats_no_value'];
        $retval .= COM_endBlock();
    }

    return $retval;
}

/**
* This will put an option for {plugin} in the command and control block on
* moderation.php
*
* @return   mixed   array(title, url, icon), or boolean false when not allowed
*
*/
function plugin_cclabel_{plugin}()
{
    global $_CONF, $LANG_{lang_var_postfix};

    if (SEC_hasRights('{plugin}.edit')) {
        return array ($LANG_{lang_var_postfix}['display_name'],
                      $_CONF['site_admin_url'] . '/plugins/{plugin}/index.php',
                      plugin_geticon_{plugin}());
    }

    return false;
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_{plugin}()
{
    global $_CONF;

    return $_CONF['site_url'] . '/{plugin}/images/{plugin}.gif';
}

/**
* returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify use it
*
* @return   mixed   array(title, url, num. items), or void when not allowed
*
*/
function plugin_getadminoption_{plugin}()
{
    global $_CONF, $_TABLES, $LANG_{lang_var_postfix};

    if (SEC_hasRights('{plugin}.edit', 'OR')) {
        $pi_name   = $LANG_{lang_var_postfix}['display_name'];
        $admin_url = $_CONF['site_admin_url'] . '/plugins/{plugin}/index.php';
        $num_items = '';
        return array($pi_name, $admin_url, $num_items);
    }
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different than the version installed and registered currently.
* If newer then you may want to run the update
*
* @return   string  version number
*
*/
function plugin_chkVersion_{plugin}()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/{plugin}/autoinstall.php';

    $inst_parms = plugin_autoinstall_{plugin}('{plugin}');

    return $inst_parms['info']['pi_version'];
}

/**
* Returns the user option for this plugin
*
* Adds the plugin to the user menu
*
* Universal Plugin:  modify/uncomment use it
*
* @return   array   Array containing (plugin name, plugin user url, 0)
*
*/
function plugin_getuseroption_{plugin}()
{
//    global $_CONF;

//    if (SEC_hasRights('{plugin}.edit,{plugin}.view','OR')) {
//        $pi_name = ucfirst("{plugin}");
//        $url     = $_CONF['site_url'] . '/{plugin}/index.php';
//        return array($pi_name, $url, 0);
//    }
}

/**
* Upgrade the {plugin} plugin
*
* @return   int     Number of message to display (true = generic success msg)
*
*/
function plugin_upgrade_{plugin}()
{
    global $_CONF, $_TABLES, $_{conf_var_prefix}_CONF;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = '{plugin}'");
    $code_version = plugin_chkVersion_{plugin}();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/{plugin}/autoinstall.php';

    // the plugin needs these function so complain when they don't exist
    if (! plugin_compatible_with_this_version_{plugin}('{plugin}')) {
        return 3002;
    }

    $inst_parms = plugin_autoinstall_{plugin}('{plugin}');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    //// upgrades are done by the install script - return a generic error
    //COM_errorLog("Plugin upgrade function not implemented");
    //return 3001;

    // no db changes - just update the version numbers
    DB_query ("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = '{plugin}'");

    return true;
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_{plugin}()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/{plugin}/autoinstall.php';
    $inst_parms = plugin_autoinstall_{plugin}('{plugin}');

    $out = array(
        /* give the name of the tables, without $_TABLES[] */
        'tables' => $inst_parms['tables'],
        /* give the full name of the group, as in the db */
        'groups' => array_keys($inst_parms['groups']),
        /* give the full name of the feature, as in the db */
        'features' => array_keys($inst_parms['features']),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}


function plugin_getListField_{plugin}($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG25, $LANG_ACCESS;

    $retval = '';

    $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
                            $A['perm_owner'], $A['perm_group'],
                            $A['perm_members'], $A['perm_anon']);
    if ($access > 0) {
        switch ($fieldname) {
            case 'edit':
                if ($access == 3) { // User is in Root group
                    $retval = "<a href=\"{$_CONF['site_admin_url']}/plugins/{plugin}/index.php"
                            . "?mode=edit&amp;up_id={$A['up_id']}\">{$icon_arr['edit']}</a>";
                }
                break;
            default:
                $retval = $fieldvalue;
                break;
        }
    } else {
        $retval = false;
    }

    return $retval;
}

/**
* Loads config infor with config.class.php
*/
function {conf_var_prefix}_loadConfig() {
    global $_CONF, $_TABLES, $_DB_table_prefix, $_{conf_var_prefix}_CONF;
    
    if (!isset($_{conf_var_prefix}_CONF['{plugin}loginrequired'])) {
        require_once $_CONF['path_system'] . 'classes/config.class.php';
        $plg_config = config::get_instance();
        if ($plg_config->group_exists('{plugin}')) {
            $_{conf_var_prefix}_CONF = $plg_config->get_config('{plugin}');
        }
    }

    if (!isset($_TABLES['{plugin}'])) {
        // add plugin tables, if any
        require_once $_CONF['path'] . 'plugins/{plugin}/autoinstall.php';
        $inst_parms = plugin_autoinstall_{plugin}('{plugin}');
        if (! empty($inst_parms['tables'])) {
            $tables = $inst_parms['tables'];
            foreach ($tables as $table) {
                $_TABLES[$table] = $_DB_table_prefix . $table;
            }
        }
    }

}

?>