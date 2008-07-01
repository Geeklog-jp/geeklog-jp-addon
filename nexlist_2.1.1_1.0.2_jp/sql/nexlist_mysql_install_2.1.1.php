<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexlist_mysql_install_2.1.1.php                                           |
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

$_SQL[] = "CREATE TABLE {$_TABLES['nexlist']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `plugin` varchar(32) NOT NULL default '',
  `category` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `listfields` varchar(255) NOT NULL default '1',
  `view_perms` mediumint(8) NOT NULL default '2',
  `edit_perms` mediumint(8) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
   PRIMARY KEY  (`id`),
   KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['nexlistfields']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `lid` mediumint(8) NOT NULL default '0',
  `fieldname` varchar(64) NOT NULL default '',
  `value_by_function` varchar(64) NOT NULL default '',
  `width` mediumint(8) NOT NULL default 0,
  `predefined_function` tinyint(1) NOT NULL default 0,
   PRIMARY KEY  (`id`),
   KEY `lid` (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['nexlistitems']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `lid` mediumint(8) NOT NULL default '0',
  `itemorder` mediumint(8) NOT NULL default '0',
  `value` longtext NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
   PRIMARY KEY  (`id`),
   KEY `lid` (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



?>