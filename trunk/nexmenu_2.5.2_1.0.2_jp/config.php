<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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

// +----------------------------------------------------------------------------+
// | User Editable Plugin Parms - begin                                         |
// +----------------------------------------------------------------------------+

$CONF_NEXMENU = array();

$CONF_NEXMENU['debug'] = false;
$CONF_NEXMENU['version'] = '2.5.2';

// +------------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['milonicstyles']                                       |
// | Define Milonic styles - mapping a style ID to the style name                 |
// | Styles are defined in the file {themedir}/nexmenu/milonicmenu/menustyles.php |
// | Ensure that you retain the same format for the variable definition           |
// +------------------------------------------------------------------------------+
$CONF_NEXMENU['milonicstyles'] = array(
    1   => 'menuStyle1',
    2   => 'menuStyle2',
    3   => 'menuStyle3',
    4   => 'XPClassicMenuStyle',
    5   => 'XPMainStyle',
    6   => 'XPMenuStyle',
    7   => 'corpMenuStyle',
    8   => 'corpSubmenuStyle',
    9   => 'macMenuStyle',
    10  => 'macSubmenuStyle',
    11  => 'tabMenuStyle',
    12  => 'tabSubmenuStyle',
    13  => 'bwMenuStyle',
    14  => 'bwSubmenuStyle',
    15  => 'background'
);

// +----------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['languages']                                         |
// | Define optional languages for which menuitem labels will be defined        |
// | For each option - ensure the language name is identical to the name        |
// | used for the core GL Language file.                                        |
// | The ID used should not be changed or deleted once the option is used       |
// | If the user selected language matches an available label then it is used   |
// | Add/Delete or edit the item definitions to match your site requirements    |
// +----------------------------------------------------------------------------+

$CONF_NEXMENU['languages'] = array (
    1   => 'french_canada_utf-8',
    2   => 'german_utf-8',
    3   => 'japanese',
    4   => 'chinese_simplified_utf-8',
    5   => 'italian',
    6   => 'spanish'
);


// +----------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['sp_labelonly']                                      |
// | If true only create menuitems for staticpages that have "add to menu"      |
// | flag set in StaticPage definition                                          |
// +----------------------------------------------------------------------------+
$CONF_NEXMENU['sp_labelonly'] = true;


// +---------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['links_maxtoplevels']                               |
// | If greater then 0, will limit the number of link menu categories to show  |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU['links_maxtoplevels'] = 5;

// +---------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['restricted_topics']                                |
// | This setting requires a hack to the lib-common COM_showTopics function    |
// | It then allows you to hide any topic with a sortorder > defined number    |
// | The sortorder field in the Topic Setup above (example: 50) could then be  |
// | used to assign content to but not showup automatically in the topic       |
// | listing. Then use the Story Editing feature to manange site content       |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU['restricted_topics'] = 50;


// +---------------------------------------------------------------------------+
// | Default Milonic menu properties for the header and block menus.           |
// | Reference: http://www.milonic.com/menuproperties.php.                     |
// | Optional Menu properties are defined in the online admin config screen.   |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU['headermenu_default_styles'] = 'orientation="horizontal";position="relative";alwaysvisible=1;';
$CONF_NEXMENU['blockmenu_default_styles'] = 'position="relative";alwaysvisible=1;';

// +---------------------------------------------------------------------------+
// | Set to false if this plugin should not load the Javascript for the        |
// | HTML_TreeMenu - if another plugin is already loading this JS Library      |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU['load_HTMLTree'] = true;


// +---------------------------------------------------------------------------+
// | Set to true if the user login form should appear in the menu              |
// | if the user is not logged in                                              |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU['loginform'] = true;


// +---------------------------------------------------------------------------+
// | Plugin Config Parms - DO NOT EDIT BELOW THIS SECTION                      |
// +---------------------------------------------------------------------------+

$_TABLES['nexmenu']             = $_DB_table_prefix . 'nxmenu';
$_TABLES['nexmenu_language']    = $_DB_table_prefix . 'nxmenu_language';
$_TABLES['nexmenu_config']      = $_DB_table_prefix . 'nxmenu_config';

$CONF_NEXMENU['coremenu'] = array (
    1       => 'usermenu',
    2       => 'adminmenu',
    3       => 'topicmenu',
    4       => 'spmenu',
    5       => 'pluginmenu',
    6       => 'linksmenu'
);

// Only CSS Mode currently supports the headermenu type
if (!$CONF_NEXMENU['milonicmode']) {
    $CONF_NEXMENU['coremenu'][7] = 'headermenu';
    $LANG_NEXMENU03[7] = 'headermenu';
}

$CONF_NEXMENU['icons'] = array (
    1   => 'url_menuitem.gif',
    2   => 'url_menuitem.gif',
    3   => 'folder.gif',
    4   => 'core_menuitem.gif',
    5   => 'custom_menuitem.gif'
);

$CONF_NEXMENU['milonicmodes'] = array('CSS','Milonic');

// Call the function to initialize some additional CONF variables for nexmenu
if (function_exists(init_nexmenuConf)) {
    init_nexmenuConf();
}


?>