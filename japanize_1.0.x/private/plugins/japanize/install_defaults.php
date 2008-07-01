<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | japanize Plugin  日本語化支援プラグイン                                   |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2008 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs         - tony AT tonybibbs DOT com                   |
// |          Mark Limburg       - mlimburg AT users.sourceforge DOT net       |
// |          Jason Whittenburg  - jwhitten AT securitygeeks DOT com           |
// |          Dirk Haun          - dirk AT haun-online DOT de                  |
// |          Trinity Bays       - trinity93 AT gmail DOT com                  |
// |          Oliver Spiesshofer - oliver AT spiesshofer DOT com               |
// |          Euan McKay         - info AT heatherengineering DOT com          |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//
// $Id: install_defaults.php,v 1.4 2008/06/04 20:21:33 dhaun Exp $

if (strpos($_SERVER['PHP_SELF'], 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_JAPANIZE_DEFAULT;

$_JAPANIZE_DEFAULT = array();


/**
* Initialize Links plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_LI_CONF if available (e.g. from
* an old config.php), uses $_LI_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_japanize()
{
    global $_JAPANIZE_CONF, $_JAPANIZE_DEFAULT;



    if (is_array($_JAPANIZE_CONF) && (count($_JAPANIZE_CONF) > 1)) {
        $_JAPANIZE_DEFAULT = array_merge($_JAPANIZE_DEFAULT, $_JAPANIZE_CONF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('japanize')) {

    /* add( 
        $param_name
        , $default_value
        , $type
        , $subgroup, $fieldset,$selection_array=null
        , $sort=0
        , $set=true
        , $group='japanize')
    */

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'japanize');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'japanize');


    }

    return true;
}

?>
