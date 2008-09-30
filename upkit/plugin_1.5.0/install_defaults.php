<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | plugins/{plugin}/install_defaults.php                                     |
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

if (strpos($_SERVER['PHP_SELF'], 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * {display_name} default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

global $_{conf_var_prefix}_DEFAULT;

$_{conf_var_prefix}_DEFAULT = array();

$_{conf_var_prefix}_DEFAULT['{plugin}loginrequired'] = 0;

$_{conf_var_prefix}_DEFAULT['hide{plugin}menu'] = 0;

/** What to show after a data has been saved? Possible choices:
 * 'item'   -> forward to the target of the item
 * 'list'   -> display the admin-list of {plugin}
 * 'plugin' -> display the public homepage of the {plugin} plugin
 * 'home'   -> display the site homepage
 * 'admin'  -> display the site admin homepage
 */
$_{conf_var_prefix}_DEFAULT['aftersave'] = 'list';

/**
 * Define default permissions for new {plugin} created from the Admin panel.
 * Permissions are perm_owner, perm_group, perm_members, perm_anon (in that
 * order). Possible values:<br>
 * - 3 = read + write permissions (perm_owner and perm_group only)
 * - 2 = read-only
 * - 0 = neither read nor write permissions
 * (a value of 1, ie. write-only, does not make sense and is not allowed)
 */
$_{conf_var_prefix}_DEFAULT['default_permissions'] = array (3, 2, 2, 2);

/**
* Initialize {display_name} plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_{conf_var_prefix}_CONF if available (e.g. from
* an old config.php), uses $_{conf_var_prefix}_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_{plugin}()
{
    global $_{conf_var_prefix}_CONF, $_{conf_var_prefix}_DEFAULT;

    if (is_array($_{conf_var_prefix}_CONF) && (count($_{conf_var_prefix}_CONF) > 1)) {
        $_{conf_var_prefix}_DEFAULT = array_merge($_{conf_var_prefix}_DEFAULT, $_{conf_var_prefix}_CONF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('{plugin}')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, '{plugin}');

        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, '{plugin}');

        $c->add('{plugin}loginrequired', $_{conf_var_prefix}_DEFAULT['{plugin}loginrequired'], 'select',
                0, 0, 0, 10, true, '{plugin}');

        $c->add('hide{plugin}menu', $_{conf_var_prefix}_DEFAULT['hide{plugin}menu'], 'select',
                0, 0, 0, 30, true, '{plugin}');

        $c->add('aftersave', $_{conf_var_prefix}_DEFAULT['aftersave'], 'select',
                0, 0, 9, 40, true, '{plugin}');

        $c->add('fs_permissions', NULL, 'fieldset', 0, 2, NULL, 0, true, '{plugin}');

        $c->add('default_permissions', $_{conf_var_prefix}_DEFAULT['default_permissions'],
                '@select', 0, 2, 12, 140, true, '{plugin}');
    }

    return true;
}

?>