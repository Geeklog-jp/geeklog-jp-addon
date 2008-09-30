<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | plugins/{plugin}/language/english_utf-8.php                               |
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

/**
* the {plugin} plugin's lang array
*
*/
$LANG_{lang_var_postfix} = array(
    'display_name'      => '{display_name}',
    'menu_title'        => '{display_name}',
    'plugin'            => '{plugin} Plugin',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'             => '{plugin} Plugin Admin',
    'install_header'    => 'Install/Uninstall the {plugin} Plugin',
    'installed'         => 'The Plugin is Installed',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'Installation Successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'The {plugin} Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'warning'           => 'Warning!  the {plugin} Plugin is still Enabled',
    'enabled'           => 'Disable the plugin before uninstalling.',
    'readme'            => 'STOP!  Before you press install please read the ',
    'installdoc'        => 'Install Document.',

    // for stats
    'stats_headline'    => '{display_name}(Top10)',
    'stats_title'       => 'Title',
    'stats_value'       => 'Value',
    'stats_no_value'    => 'No Data.',

    // for admin
    '{plugin}editor'  => '{display_name} Editor',
    'manager'           => '{display_name} Manager',
    'instructions'      => 'To modify or delete a data, click on that data\'s edit icon below.  To create a new data, click on "Create New" above.',
    'missing_fields'    => 'Missing fields.',
);

// Messages for COM_showMessage the submission form

$PLG_{plugin}_MESSAGE2 = '{display_name} data has been successfully saved.';
$PLG_{plugin}_MESSAGE3 = '{display_name} data has been successfully deleted.';

// Messages for the plugin upgrade
$PLG_{plugin}_MESSAGE3001 = 'Plugin upgrade not supported.';
$PLG_{plugin}_MESSAGE3002 = $LANG32[9];


// Localization of the Admin Configuration UI
$LANG_configsections['{plugin}'] = array(
    'label' => '{display_name}',
    'title' => '{display_name} Configuration'
);

$LANG_confignames['{plugin}'] = array(
    '{plugin}loginrequired' => '{display_name} Login Required?',
    '{plugin}ubmission' => 'Enable Submission Queue?',
    'new{plugin}interval' => 'New {display_name} Interval',
    'hidenew{plugin}' => 'Hide New {display_name}?',
    'hide{plugin}menu' => 'Hide {display_name} Menu Entry?',
    '{plugin}perpage' => '{display_name} per Page',
    'show_top10' => 'Show Top 10 {display_name}?',
    'delete_{plugin}' => 'Delete Links with Owner?',
    'aftersave' => 'After Saving {display_name} Data',
    'default_permissions' => '{display_name} Default Permissions'
);

$LANG_configsubgroups['{plugin}'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['{plugin}'] = array(
    'fs_main' => '{display_name} Main Settings',
    'fs_public' => 'Public {display_name} List Settings',
    'fs_permissions' => '{display_name} Default Permissions'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['{plugin}'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    9 => array('Forward to {display_name} Item' => 'item', 'Display Admin List' => 'list', 'Display Public List' => 'plugin', 'Display Home' => 'home', 'Display Admin' => 'admin'),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3)
);

?>