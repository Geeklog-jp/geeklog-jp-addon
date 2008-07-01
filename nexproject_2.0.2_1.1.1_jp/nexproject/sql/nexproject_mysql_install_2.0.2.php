<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexproject_mysql_install_2.0.2.php                                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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
$_SQL[] = "CREATE TABLE `{$_TABLES['prj_category']}` (
  `category_id` int(11) NOT NULL default '0',
  `pid` int(11) NOT NULL default '0'
) TYPE=MyISAM;";


$_SQL[] = "CREATE TABLE `{$_TABLES['prj_department']}` (
  `pid` int(11) NOT NULL default '0',
  `department_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_filters']}` (
  `flid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `projects` mediumtext,
  `employees` mediumtext,
  `department` mediumtext,
  `category` mediumtext,
  `location` mediumtext,
  `objective` mediumtext,
  PRIMARY KEY  (`flid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_location']}` (
  `pid` int(11) NOT NULL default '0',
  `location_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_objective']}` (
  `pid` int(11) NOT NULL default '0',
  `objective_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_permissions']}` (
  `uid` int(11) NOT NULL default '0',
  `pid` int(11) NOT NULL default '0',
  `tid` int(11) NOT NULL default '0',
  `writeaccess` char(1) NOT NULL default ''
) TYPE=MyISAM;";


$_SQL[] = "CREATE TABLE `{$_TABLES['prj_projects']}` (
  `pid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `fid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` mediumtext,
  `is_using_docmgmt_flag` char(1) default NULL,
  `is_using_forum_flag` char(1) default NULL,
  `is_private_project_flag` char(1) default NULL,
  `is_template_project_flag` char(1) default NULL,
  `keywords` mediumtext,
  `priority_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `objective_id` int(11) NOT NULL default '0',
  `progress_id` int(11) NOT NULL default '0',
  `create_date` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `estimated_end_date` int(11) NOT NULL default '0',
  `planned_end_date` int(11) NOT NULL default '0',
  `actual_end_date` int(11) NOT NULL default '0',
  `percent_completion` int(11) NOT NULL default '0',
  `last_updated_date` int(11) NOT NULL default '0',
  `notification_enabled_flag` char(2) default NULL,
  `parent_id` int(11) NOT NULL default '0',
  `lhs` int(11) NOT NULL default '0',
  `rhs` int(11) NOT NULL default '0',
  `tempPID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_projPerms']}` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `taskID` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `viewRead` int(11) NOT NULL default '0',
  `writeChange` int(11) NOT NULL default '0',
  `fullAccess` int(11) NOT NULL default '0',
  `seeDetails` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_session']}` (
  `sess_id` decimal(10,0) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `pid` int(11) NOT NULL default '0',
  `start_time` decimal(10,0) NOT NULL default '0',
  `lastop` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`sess_id`)
) TYPE=MyISAM;";





$_SQL[] = "CREATE TABLE `{$_TABLES['prj_sorting']}` (
  `id` int(11) NOT NULL auto_increment,
  `uid` decimal(8,0) NOT NULL default '0',
  `home_projects` varchar(155) default NULL,
  `home_tasks` varchar(155) default NULL,
  `home_discussions` varchar(155) default NULL,
  `home_reports` varchar(155) default NULL,
  `projects` varchar(155) default NULL,
  `organizations` varchar(155) default NULL,
  `project_tasks` varchar(155) default NULL,
  `discussions` varchar(155) default NULL,
  `project_discussions` varchar(155) default NULL,
  `users` varchar(155) default NULL,
  `team` varchar(155) default NULL,
  `tasks` varchar(155) default NULL,
  `report_tasks` varchar(155) default NULL,
  `assignment` varchar(155) default NULL,
  `reports` varchar(155) default NULL,
  `files` varchar(155) default NULL,
  `organization_projects` varchar(155) default NULL,
  `notes` varchar(155) default NULL,
  `calendar` varchar(155) default NULL,
  `phases` varchar(155) default NULL,
  `support_requests` varchar(155) default NULL,
  `subtasks` varchar(155) default NULL,
  `bookmarks` varchar(155) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";


$_SQL[] = "CREATE TABLE `{$_TABLES['prj_statuslog']}` (
  `slid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `description` mediumtext NOT NULL,
  `updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`slid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";
        





$_SQL[] = "CREATE TABLE `{$_TABLES['prj_tasks']}` (
  `tid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `lhs` int(11) default NULL,
  `rhs` int(11) default NULL,
  `priority_id` int(11) default NULL,
  `status_id` int(11) default NULL,
  `duration_type_id` int(11) default NULL,
  `progress_id` int(11) default NULL,
  `name` varchar(255) NOT NULL default '',
  `parent_task` int(11) default NULL,
  `description` mediumtext,
  `notification_enabled_flag` char(2) default NULL,
  `keywords` mediumtext,
  `create_date` int(11) default NULL,
  `start_date` int(11) default NULL,
  `estimated_end_date` int(11) default NULL,
  `planned_end_date` int(11) default NULL,
  `actual_end_date` int(11) default NULL,
  `last_updated_date` int(11) default NULL,
  `duration` int(11) default NULL,
  `progress` smallint(6) default NULL,
  `make_private_enabled_flag` char(2) default NULL,
  PRIMARY KEY  (`tid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";



$_SQL[] = "CREATE TABLE `{$_TABLES['prj_task_users']}` (
  `tid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `role` char(1) default NULL
) TYPE=MyISAM;";




$_SQL[] = "CREATE TABLE `{$_TABLES['prj_users']}` (
  `pid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `role` char(1) default NULL
) TYPE=MyISAM;";
        




$_SQL[] = "CREATE TABLE `{$_TABLES['prj_lockcontrol']}` (
  `locked` int(11) NOT NULL default '0',
  `timeLocked` varchar(20) NOT NULL default '',
  `tableLocked` varchar(200) default NULL
) TYPE=MyISAM;";
        

$_SQL[] = "CREATE TABLE {$_TABLES['prj_taskSemaphore']} ( 
  `locked` int(11) NOT NULL default '0',
  `timeLocked` varchar(20) NOT NULL default ''
) TYPE=MyISAM; ";



$_SQL[] = "CREATE TABLE {$_TABLES['prj_config']} (
`config_param` VARCHAR( 25 ) NOT NULL ,
`config_value` VARCHAR( 50 ) NOT NULL 
) TYPE=MyISAM; ";






?>