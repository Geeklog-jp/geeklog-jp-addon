<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexcontent_mysql_install_2.1.1.php                                        |
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

$_SQL[] = "CREATE TABLE {$_TABLES['nexcontent']} (
  `help` longtext
) TYPE=MyISAM;";


$_SQL[] = "CREATE TABLE {$_TABLES['nexcontent_pages']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `sid` varchar(40) NOT NULL default '',
  `pid` mediumint(8) NOT NULL default '0',
  `gid` varchar(32) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `pageorder` int(5) NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `blockformat` varchar(32) NOT NULL default '',
  `heading` varchar(255) NOT NULL default '',
  `content` longtext,
  `hits` int(11) NOT NULL default '0',
  `menutype` tinyint(1) NOT NULL default '0',
  `is_menu_newpage` tinyint(1) NOT NULL default '0',
  `submenu_item` tinyint(1) NOT NULL default '0',
  `show_submenu` tinyint(1) NOT NULL default '1',
  `show_blockmenu` tinyint(1) NOT NULL default '1',
  `show_breadcrumbs` tinyint(1) NOT NULL default '1',
  `is_draft` tinyint(1) NOT NULL default '0',
  `grp_access` tinyint(5) NOT NULL default '0',
  `owner_id` mediumint(8) NOT NULL default '0',
  `group_id` mediumint(8) NOT NULL default '0',
  `perm_owner` tinyint(1) NOT NULL default '0',
  `perm_group` tinyint(1) NOT NULL default '0',
  `perm_members` tinyint(1) NOT NULL default '0',
  `perm_anon` tinyint(1) NOT NULL default '0',
  `pagetitle` varchar(255) NOT NULL default '',
  `meta_description` text,
  `meta_keywords` text,
  PRIMARY KEY  (`id`),
  KEY `parent` (`pid`),
  KEY `pageorder` (`pageorder`),
  KEY `gid` (`gid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";


$_SQL[] = "CREATE TABLE {$_TABLES['nexcontent_images']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `page_id` mediumint(8) NOT NULL default '0',
  `imagenum` tinyint(5) NOT NULL default '0',
  `imagefile` varchar(64) NOT NULL default '',
  `autoscale` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`)
) TYPE=MyISAM;";

$_SQL[]= "INSERT INTO {$_TABLES['nexcontent_pages']} (id,pid,type,pageorder,name,blockformat,heading,content,meta_description,meta_keywords) VALUES (1, 0, 'category', '10', 'frontpage', 'none', 'Front Page Folder', 'Create a page under this folder if you want to have a page loaded as the frontpage', '', '');";



?>