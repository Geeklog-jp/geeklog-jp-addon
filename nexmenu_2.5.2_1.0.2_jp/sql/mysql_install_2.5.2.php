<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | mysql_install_2.5.2.php                                                   |
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

#
# Table structure for table `gl_nexmenu`
#
# Creation: May 02, 2004 at 01:09 AM
# Last update: May 02, 2004 at 01:51 AM
# Last check: May 02, 2004 at 01:09 AM
#

$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `menutype` tinyint(3) NOT NULL default '0',
  `location` varchar(16) NOT NULL default 'block',
  `menuorder` mediumint(8) NOT NULL default '0',
  `label` varchar(64) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `grp_access` mediumint(8) NOT NULL default '2',
  `is_enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `menuorder` (`menuorder`)
) TYPE=MyISAM COMMENT='nexMenu Main table to setup menus' AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu_language']} (
    `id` int(11) NOT NULL auto_increment,
    `menuitem` mediumint(8) NOT NULL default '0',
    `language` tinyint(1) NOT NULL default '0',
    `label` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`id`),
    KEY `menuitem` (`menuitem`),
    KEY `language` (`language`)
) TYPE=MyISAM ;";


$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu_config']} (
  `mode` varchar(32) NOT NULL default '',
  `multilanguage` tinyint(1) NOT NULL default '0',
  `targetfeatures` varchar(255) NOT NULL default '',
  `blockmenu_style` varchar(64) NOT NULL default '',
  `blocksubmenu_style` varchar(64) NOT NULL default '',
  `headermenu_style` varchar(64) NOT NULL default '',
  `headersubmenu_style` varchar(64) NOT NULL default '',
  `headermenu_properties` varchar(255) NOT NULL default '',
  `blockmenu_properties` varchar(255) NOT NULL default '',
  `headerbg` varchar(8) NOT NULL default '',
  `headerfg` varchar(8) NOT NULL default '',
  `blockbg` varchar(8) NOT NULL default '',
  `blockfg` varchar(8) NOT NULL default '',
  `onhover_headerbg` varchar(8) NOT NULL default '',
  `onhover_headerfg` varchar(8) NOT NULL default '',
  `onhover_blockbg` varchar(8) NOT NULL default '',
  `onhover_blockfg` varchar(8) NOT NULL default ''
) TYPE=MyISAM ;";

$_SQL[] = "INSERT INTO {$_TABLES['nexmenu_config']} VALUES ('CSS', 1, 'targetfeatures=width=700,height=600,left=50,top=50,scrollbars=yes;', 'menuStyle1', 'menuStyle1', 'corpMenuStyle', 'corpSubmenuStyle', 'menuwidth=\"100%\";', '', '#10377C', '#FFFFFF', '#FFFFFF', '#335EA8', '#335EA8', '#FFFFFF', '#FFFFFF', '#296DC1');";


/* Insert default Data */

$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (1, 0, 1, 'block', 10, 'Home', '', '[siteurl]/index.php', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (2, 0, 3, 'block', 20, 'Admin Menu', '', '', 13, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (3, 2, 4, 'block', 10, 'menuitems', '', 'adminmenu', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (4, 0, 3, 'block', 30, 'Links', '', '', 2, 1); ";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (5, 4, 4, 'block', 10, 'Link Items', '', 'linksmenu', 2, 1); ";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (6, 0, 4, 'block', 40, 'menuitems', '', 'usermenu', 2, 1);";

$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (7, 0, 1, 'header', 10, 'Add Story', '', '[siteurl]/submit.php', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (8, 0, 4, 'header', 20, 'plugin_menuitems', '', 'pluginmenu', 2, 1);";

$_SQL[]= "INSERT INTO {$_TABLES['blocks']} (is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon) VALUES ('1','nexmenu','phpblock','Site Menu','all',0,1,'phpblock_nexmenu',2,2,3,3,2,2);";


?>