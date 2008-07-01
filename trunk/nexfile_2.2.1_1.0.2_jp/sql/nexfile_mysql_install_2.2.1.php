<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexfile_mysql_install_2.2.1.php                                           |
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

/**
* Table structure for table `fm_access`
*
* Creation: Jul 01, 2003 at 10:10 AM
* Last update: Jul 01, 2003 at 10:10 AM
* Last check: Jul 01, 2003 at 10:10 AM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_access']} (
  `accid` mediumint(9) NOT NULL auto_increment,
  `catid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `grp_id` mediumint(9) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `upload` tinyint(1) NOT NULL default '0',
  `upload_direct` tinyint(1) NOT NULL default '0',
  `upload_ver` tinyint(1) NOT NULL default '0',
  `approval` tinyint(1) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`accid`),
  KEY `catid` (`catid`)
) TYPE=MyISAM COMMENT='nexfile Access Rights - for user or group access to category' AUTO_INCREMENT=1;";


/**
* Table structure for table `fm_categories`
*
* Creation: Jun 26, 2003 at 06:03 PM
* Last update: Jun 26, 2003 at 06:03 PM
* Last check: Jun 28, 2003 at 09:19 PM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_category']} (
  `cid` mediumint(9) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `image` varchar(255) default NULL,
  `auto_create_notifications` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `pid` (`pid`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;";


/**
* Table structure for table `fm_filedetail`
*
* Creation: Jun 26, 2003 at 04:29 PM
* Last update: Jun 26, 2003 at 04:29 PM
* Last check: Jun 28, 2003 at 09:19 PM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_detail']} (
  `fid` mediumint(8) NOT NULL default '0',
  `description` longtext NOT NULL,
  `platform` varchar(32) NOT NULL default '',
  `hits` mediumint(9) NOT NULL default '0',
  `rating` tinyint(4) NOT NULL default '0',
  `votes` tinyint(4) unsigned NOT NULL default '0',
  `comments` tinyint(4) unsigned NOT NULL default '0',
  KEY `fid` (`fid`)
) TYPE=MyISAM;";


/**
* Table structure for table `fm_files`
*
* Creation: Jun 26, 2003 at 05:59 PM
* Last update: Jun 26, 2003 at 05:59 PM
* Last check: Jun 28, 2003 at 09:19 PM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_files']} (
  `fid` mediumint(8) NOT NULL auto_increment,
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '1',
  `ftype` varchar(16) NOT NULL default '',
  `size` mediumint(9) NOT NULL default '0',
  `thumbnail` varchar(255) NOT NULL default '',
  `thumbtype` varchar(16) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `status_changedby_uid` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`fid`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1;";


/**
* Table structure for table `fm_fileversions`
*
* Creation: Jun 26, 2003 at 05:53 PM
* Last update: Jun 26, 2003 at 05:53 PM
* Last check: Jun 28, 2003 at 09:19 PM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_versions']} (
  `id` mediumint(9) NOT NULL auto_increment,
  `fid` mediumint(9) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '0',
  `size` mediumint(9) NOT NULL default '0',
  `notes` longtext NOT NULL,
  `date` int(11) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



/**
* Table structure for table `fm_notifications`
*
* Creation: Jun 26, 2003 at 06:00 PM
* Last update: Jun 26, 2003 at 06:00 PM
* Last check: Jun 28, 2003 at 09:19 PM
**/

$_SQL[] = "CREATE TABLE {$_TABLES['fm_notify']} (
  `id` mediumint(9) NOT NULL auto_increment,
  `fid` mediumint(9) NOT NULL default '0',
  `cid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";


/**
* Table structure for table `fm_filesubmissions`
*
* Creation: Jul 17, 2003 at 10:14 PM
* Last update: Jul 18, 2003 at 03:23 PM
* Last check: Jul 17, 2003 at 10:14 PM
**/
$_SQL[] = "CREATE TABLE {$_TABLES['fm_submissions']} (
 `id` mediumint(8) NOT NULL auto_increment,
  `fid` mediumint(8) NOT NULL default '0',
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `tempname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `description` longtext NOT NULL,
  `version` tinyint(3) unsigned NOT NULL default '1',
  `version_note` longtext NOT NULL,
  `size` mediumint(9) NOT NULL default '0',
  `thumbnail` varchar(255) NOT NULL default '',
  `thumbtype` varchar(16) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `notify` tinyint(1) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1;";


$_SQL[] = "CREATE TABLE {$_TABLES['auditlog']} (
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `script` varchar(255) NOT NULL default '',
  `logentry` varchar(255) NOT NULL default '',
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `script` (`script`)
) TYPE=MyISAM;";


?>