<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexform_mysql_install_2.0.1.php                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

$_SQL[] = "CREATE TABLE {$_TABLES['formDefinitions']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `shortname` varchar(32) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `responses` mediumint(8) NOT NULL default '0',
  `template` varchar(128) NOT NULL default '',
  `post_method` varchar(32) NOT NULL default '',
  `post_option` varchar(255) NOT NULL default '',
  `fieldsets` longtext NOT NULL,
  `before_formid` mediumint(8) NOT NULL default '0',
  `after_formid` mediumint(8) NOT NULL default '0',
  `show_as_tab` tinyint(1) NOT NULL default '0',
  `tab_label` varchar(128) NOT NULL default '',
  `metavalues` mediumint(8) NOT NULL default '0',
  `intro_text` longtext NOT NULL,
  `after_post_text` longtext NOT NULL,
  `on_submit` varchar(255) NOT NULL default '',
  `return_url` varchar(255) NOT NULL default '',
  `perms_view` mediumint(8) NOT NULL default '2',
  `perms_access` mediumint(8) NOT NULL default '2',
  `perms_edit` mediumint(8) NOT NULL default '2',
  `status` tinyint(1) NOT NULL default '0',
  `gid` varchar(32) NOT NULL default '',
  `admin_url` varchar(255) NOT NULL default '',
  `comments` longtext NOT NULL,
  `show_mandatory_note` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['formFields']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `formid` mediumint(8) NOT NULL default '0',
  `tfid` mediumint(8) NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `fieldorder` mediumint(8) NOT NULL default '0',
  `field_name` varchar(64) NOT NULL default '',
  `label` text NOT NULL,
  `style` varchar(32) NOT NULL default 'default',
  `layout` varchar(16) NOT NULL default '',
  `col_width` smallint(1) default NULL,
  `col_padding` smallint(1) default NULL,
  `is_vertical` tinyint(1) NOT NULL default '0',
  `is_newline` tinyint(1) NOT NULL default '1',
  `is_mandatory` tinyint(1) NOT NULL default '0',
  `is_searchfield` tinyint(1) NOT NULL default '0',
  `is_resultsfield` tinyint(1) NOT NULL default '0',
  `is_reverseorder` tinyint(1) NOT NULL default '0',
  `is_htmlfiltered` tinyint(1) NOT NULL default '0',
  `is_internaluse` tinyint(1) NOT NULL default '0',
  `hidelabel` tinyint(1) NOT NULL default '0',
  `field_attributes` varchar(255) NOT NULL default '',
  `field_help` varchar(255) NOT NULL default '',
  `field_values` longtext NOT NULL,
  `value_by_function` tinyint(1) NOT NULL default '0',
  `validation` varchar(255) NOT NULL default '',
  `javascript` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `formid` (`formid`),
  KEY `fieldorder` (`fieldorder`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['formResults']} (
  `id` int(11) NOT NULL auto_increment,
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `related_results` varchar(255) NOT NULL default '',
  `last_updated_date` int(11) NOT NULL default '0',
  `last_updated_uid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['formResData']} (
  `id` int(11) NOT NULL auto_increment,
  `result_id` mediumint(8) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` varchar(255) NOT NULL default '',
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`),
  KEY `result_id` (`result_id`)
) TYPE=MyISAM ;";


$_SQL[] = "CREATE TABLE {$_TABLES['formResText']} (
  `result_id` mediumint(11) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` longtext NOT NULL,
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  KEY `field_id` (`field_id`)
) TYPE=MyISAM ;";


$_SQL[] = "CREATE TABLE {$_TABLES['formResultsTmp']} (
  `id` int(11) NOT NULL auto_increment,
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `related_results` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['formResDataTmp']} (
  `id` int(11) NOT NULL auto_increment,
  `result_id` mediumint(8) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` varchar(255) NOT NULL default '',
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`),
  KEY `result_id` (`result_id`)
) TYPE=MyISAM ;";


$_SQL[] = "CREATE TABLE {$_TABLES['formResTextTmp']} (
  `result_id` mediumint(11) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` longtext NOT NULL,
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  KEY `field_id` (`field_id`)
) TYPE=MyISAM ;";

$_SQL[] = "INSERT INTO {$_TABLES['formDefinitions']} VALUES (1, 'Install Test','Install Test' ,1121781422, 33, 'defaultform.thtml', 'dbsave', '','', 0, 0, 1, '', 0, 0x5468697320697320612074657374206f662074686520706c7567696e20696e7374616c6c202d206a75737420736f6d652072616e646f6d206669656c64732e, '', '', '', 26, 1, 1, 1, '', '', '', 0);";

$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (1, 1, 1, 'select', 10, 'sel_frm1_1', 'Salutation', '1', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, '', '', 'nx_getSalutations', 1, '', '');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (2, 1, 2, 'text', 20, 'txt_frm1_2', 'Name', '2', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, '', '', '', 0, '', '');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (3, 1, 3, 'text', 30, 'txt_frm1_3', 'Address1', '1', '', NULL, NULL, 0, 1, 1, 0, 1, 0, 0, 0, 0, 'size=\"60\" maxlength=\"40\"', '', 'your address here', 0, '', 'realname=\"Full address is required\"');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (5, 1, 4, 'textarea1', 40, 'ta1_frm1_5', 'Comments', '1', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, 'cols=\"50\" rows=\"3\"', '', 'This will clear when you click on field. Testing adding JS to the field', 0, '', 'onFocus=''this.value=\"\"''');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (7, 1, 7, 'text', 50, 'txt_frm1_7', 'Age', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, 'size=&quot;3&quot;', '', '', 0, '', '');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (6, 1, 9, 'checkbox', 70, 'chk_frm1_6', 'Sign up for newsletter', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 'yes', 0, '', '');";
$_SQL[] = "INSERT INTO {$_TABLES['formFields']} VALUES (8, 1, 10, 'submit', 80, 'sub_frm1_8', '', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 'Submit', 0, '', '');";



?>