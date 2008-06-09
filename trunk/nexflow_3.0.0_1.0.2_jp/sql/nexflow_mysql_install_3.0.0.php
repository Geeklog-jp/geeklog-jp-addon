<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexflow_mysql_install_3.0.0.php                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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

$_SQL[] = "CREATE TABLE {$_TABLES['nfappgroups']} (
  `id` int(11) NOT NULL auto_increment,
  `AppGroup` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nfappgroups']} VALUES (1, 'Sample AppGroup 1');";
$_SQL[] = "INSERT INTO {$_TABLES['nfappgroups']} VALUES (2, 'Testsuite');";

$_SQL[] = "CREATE TABLE {$_TABLES['nfhandlers']} (
  `id` int(11) NOT NULL auto_increment,
  `handler` varchar(255) NOT NULL default '',
  `nf_handlerTypeID` int(11) NOT NULL default '0',
  `description` longtext,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nfhandlers']} (`id`, `handler`, `nf_handlerTypeID`) VALUES (1, 'nexflow/batchhandler1.php', 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfhandlers']} (`id`, `handler`, `nf_handlerTypeID`) VALUES (2, 'testsuite/yes-no.php', 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nfifoperators']} (
  `id` int(11) NOT NULL default '0',
  `operator` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nfifoperators']} (`id`, `operator`) VALUES (1, '=');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifoperators']} (`id`, `operator`) VALUES (2, '>');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifoperators']} (`id`, `operator`) VALUES (3, '<');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifoperators']} (`id`, `operator`) VALUES (4, '!=');";

$_SQL[] = "CREATE TABLE {$_TABLES['nfifprocessarguments']} (
  `id` int(11) NOT NULL default '0',
  `label` varchar(200) NOT NULL default '',
  `logicalEntry` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nfifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (1, 'Last Task Status is Success', 'lasttasksuccess');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (2, 'Last Task Status is Cancel', 'lasttaskcancel');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (3, 'Last Task Status is Hold', 'lasttaskhold');";
$_SQL[] = "INSERT INTO {$_TABLES['nfifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (4, 'Last Task Status is Aborted', 'lasttaskaborted');";

$_SQL[] = "CREATE TABLE {$_TABLES['nfnotifications']} (
  `queueID` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `notification_sent` tinyint(1) NOT NULL default '0',
  KEY `queueID` (`queueID`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfprocess']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `customFlowName` varchar(255) NOT NULL default '',
  `complete` int(11) default NULL,
  `initiator_uid` int(11) default NULL,
  `pid` int(11) default NULL,
  `initiatedDate` date default NULL,
  `completedDate` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfprocessvariables']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `variableValue` varchar(255) NOT NULL default '',
  `nf_templateVariableID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateVariableID` (`nf_templateVariableID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproductionassignments']} (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(8) NOT NULL default '0',
  `uid` int(8) NOT NULL default '0',
  `nf_processVariable` mediumint(8) NOT NULL default '0',
  `assignBack_uid` mediumint(8) NOT NULL default '0',
  `last_updated` int(11) NOT NULL default '0',
  `security_hash` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `task_id` (`task_id`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `assignBack_uid` (`assignBack_uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfqueue']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `nf_templateDataID` int(11) default '0',
  `status` int(11) default NULL,
  `uid` int(11) default NULL,
  `archived` int(11) default NULL,
  `prePopulate` int(11) default NULL,
  `createdDate` datetime default NULL,
  `completedDate` datetime default NULL,
  `nextReminderTime` datetime default NULL,
  `startedDate` datetime default NULL,
  `numRemindersSent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `status` (`status`),
  KEY `archived` (`archived`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfqueuefrom']} (
  `id` int(11) NOT NULL auto_increment,
  `queueID` int(11) default NULL,
  `fromQueueID` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfsteptype']} (
  `id` int(11) NOT NULL default '0',
  `stepType` varchar(50) NOT NULL default '',
  `flexField` varchar(100) default NULL,
  `is_interactiveStepType` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (1, 'Manual Web', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (2, 'And', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (4, 'Batch', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (5, 'If', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (6, 'batch function', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (7, 'interactive function', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (8, 'nexform', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (9, 'Start', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (10, 'End', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nfsteptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (11, 'Set Process Variable', NULL, 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nftemplate']} (
  `id` int(11) NOT NULL auto_increment,
  `templateName` varchar(100) NOT NULL default '',
  `useProject` int(11) NOT NULL default '0',
  `AppGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (1, 'TEST FLOW 1 - Manual Web Test', 1, 2);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (2, 'TEST FLOW 2 - AND Test 1: 2 Branch', 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (3, 'TEST FLOW 3 - AND Test 2: 3 Branch Regen', 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (4, 'TEST FLOW 4 - OR Test', 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (5, 'TEST FLOW 5 - IF Test 1: Variable Value Tests', 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplate']} (`id`, `templateName`, `useProject`, `AppGroup`) VALUES (6, 'TEST FLOW 6 - IF Test 2: Task Status Tests', 0, 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nftemplateassignment']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataID` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `nf_processVariable` int(11) NOT NULL default '0',
  `nf_prenotifyVariable` int(11) NOT NULL default '0',
  `nf_postnotifyVariable` int(11) NOT NULL default '0',
  `nf_remindernotifyVariable` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `nf_prenotifyVariable` (`nf_prenotifyVariable`),
  KEY `nf_postnotifyVariable` (`nf_postnotifyVariable`),
  KEY `nf_remindernotifyVariable` (`nf_remindernotifyVariable`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (1, 1, 2, 0, 1, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (12, 8, 0, 0, 8, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (3, 9, 2, 0, 0, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (19, 15, 0, 0, 2, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (5, 20, 2, 0, 0, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (6, 21, 2, 0, 0, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (7, 23, 0, 0, 4, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (8, 36, 0, 0, 6, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (9, 40, 0, 0, 6, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (13, 11, 0, 0, 8, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (14, 9, 0, 0, 8, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (15, 6, 0, 0, 1, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (18, 54, 0, 0, 2, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (20, 18, 0, 0, 2, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (21, 55, 0, 0, 3, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (22, 22, 0, 0, 3, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (23, 21, 0, 0, 3, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (24, 20, 0, 0, 3, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (25, 56, 0, 0, 4, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (26, 7, 0, 0, 8, 0, 0, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplateassignment']} (`id`, `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES (27, 57, 0, 0, 6, 0, 0, 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nftemplatedata']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `logicalID` int(11) default NULL,
  `nf_stepType` tinyint(1) NOT NULL default '0',
  `nf_handlerId` int(11) NOT NULL default '0',
  `firstTask` tinyint(1) NOT NULL default '0',
  `taskname` varchar(150) default NULL,
  `assignedByVariable` tinyint(1) NOT NULL default '0',
  `argumentVariable` varchar(255) default NULL,
  `argumentProcess` varchar(255) default NULL,
  `operator` varchar(10) default NULL,
  `ifValue` varchar(255) default NULL,
  `regenerate` tinyint(1) NOT NULL default '0',
  `regenAllLiveTasks` tinyint(1) default '0',
  `isDynamicForm` tinyint(1) NOT NULL default '0',
  `dynamicFormVariableID` int(11) NOT NULL default '0',
  `isDynamicTaskName` tinyint(1) NOT NULL default '0',
  `dynamicTaskNameVariableID` int(11) NOT NULL default '0',
  `function` varchar(255) NOT NULL default '',
  `formid` mediumint(8) NOT NULL default '0',
  `fieldid` mediumint(8) NOT NULL default '0',
  `varValue` varchar(255) default NULL,
  `incValue` mediumint(8) NOT NULL default '0',
  `varToSet` int(11) NOT NULL default '0',
  `optionalParm` varchar(64) NOT NULL default '',
  `reminderInterval` tinyint(1) NOT NULL default '0',
  `subsequentReminderInterval` tinyint(1) NOT NULL default '0',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `prenotify_message` varchar(255) NOT NULL default '',
  `postnotify_message` varchar(255) NOT NULL default '',
  `reminder_message` varchar(255) NOT NULL default '',
  `numReminders` tinyint(1) NOT NULL default '0',
  `escalateVariableID` int(11) NOT NULL default '0',
  `offsetLeft` int(4) unsigned NOT NULL default '0',
  `offsetTop` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (1, 1, 1, 1, 2, 0, 'Basic External Script Task', 1, '0', '0', '0', '0', 1, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, 'op-parm-test', 0, 0, '2007-02-28 10:49:53', '', '', '', 0, 0, 348, 281);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (2, 1, 2, 5, 0, 0, 'Test response from Manual Web Task', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 10:49:20', '', '', '', 0, 0, 346, 374);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (3, 1, 3, 6, 0, 0, 'Accept Branch', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Manual Web Task - Accept Branch excecuting', 0, 0, '2007-02-28 10:59:06', '', '', '', 0, 0, 69, 375);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (4, 1, 4, 6, 0, 0, 'Reject Branch', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Manual Web Task - Fail Branch excecuting', 0, 0, '2007-02-28 10:59:32', '', '', '', 0, 0, 346, 465);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (6, 1, 6, 7, 0, 0, 'Manual Web Test - Last task', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Workflow Test Completed', 0, 0, '2007-02-28 11:01:58', '', '', '', 0, 0, 67, 462);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (7, 2, 1, 7, 0, 0, 'Start Test', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Complete task to launch 2 parallel tasks', 0, 0, '2007-02-28 18:47:06', '', '', '', 0, 0, 321, 261);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (8, 2, 2, 7, 0, 0, 'Test Suite Task - Branch 1', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 0, NULL, 0, 0, 'Interactive Function (Task #2): Complete. Next: Task #4', 0, 0, '2007-02-28 10:05:01', '', '', '', 0, 0, 47, 352);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (9, 2, 3, 7, 0, 0, 'Test Suite Task - Branch 2', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 0, NULL, 0, 0, 'Interactive Function (Task #3): Complete.  Next: Task #4', 0, 0, '2007-02-28 10:05:29', '', '', '', 0, 0, 321, 352);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (10, 2, 4, 2, 0, 0, 'Wait for both branches to complete', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 21:10:01', '', '', '', 0, 0, 319, 439);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (11, 2, 5, 7, 0, 0, 'Last task for the ''AND Branch'' Test Suite', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Both Branches of workflow completed', 0, 0, '2007-02-28 10:31:17', '', '', '', 0, 0, 318, 526);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (12, 3, 2, 6, 0, 0, 'Launch 3 Parallel Branches', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_noOperation', 0, 0, NULL, 0, 0, 'Batch Function (Task #1): Branching to Task #2', 0, 0, '2007-02-28 21:11:36', '', '', '', 0, 0, 311, 335);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (13, 3, 3, 6, 0, 0, 'Branch 1 - Basic No-Op placeholder', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 0, NULL, 0, 0, 'Batch Function (Task #2): Now Moving to AND (Task #6)', 0, 0, '2007-02-28 12:39:07', '', '', '', 0, 0, 51, 421);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (14, 3, 4, 6, 0, 0, 'Branch 2 - Basic No-Op placeholder', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 0, NULL, 0, 0, 'Batch Function (Task #3): Now Moving to AND (Task #6)', 0, 0, '2007-02-28 12:39:17', '', '', '', 0, 0, 311, 421);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (15, 3, 5, 7, 0, 0, 'Branch 3 - Interactive Task', 1, '0', '0', '0', '0', 1, 1, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 0, NULL, 0, 0, 'Interactive Function (Task #4): If accept', 0, 0, '2007-03-28 22:31:27', '', '', '', 0, 0, 583, 419);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (16, 3, 6, 5, 0, 0, 'Test if task accepted - Recycle if required', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 12:36:32', '', '', '', 0, 0, 584, 509);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (17, 3, 7, 2, 0, 0, 'Wait for three branches to complete', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 12:40:49', '', '', '', 0, 0, 312, 513);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (18, 3, 8, 7, 0, 0, 'Last task for the Test', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'All Branches of workflow completed', 0, 0, '2007-02-28 12:34:15', '', '', '', 0, 0, 313, 597);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (20, 4, 3, 7, 0, 0, 'Dynamic Taskname', 1, '0', '0', '0', '0', 0, 0, 0, 0, 1, 9, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Branch 1 - Press complete to continue', 0, 0, '2007-02-28 22:45:54', '', '', '', 0, 0, 338, 451);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (21, 4, 4, 7, 0, 0, 'Branch 2 Test Task', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Branch 2 - Press complete to continue', 0, 0, '2007-02-28 15:59:34', '', '', '', 0, 0, 70, 364);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (22, 4, 5, 7, 0, 0, 'End of test', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Last task in workflow', 0, 0, '2007-02-28 16:00:33', '', '', '', 0, 0, 72, 450);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (23, 5, 1, 7, 0, 0, 'Set Variable for Test', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_setvar1', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 16:44:13', '', '', '', 0, 0, 310, 258);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (24, 5, 2, 5, 0, 0, 'Test for VAR1 &gt; 5', 0, '5', '0', '2', '5', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 17:05:20', '', '', '', 0, 0, 581, 260);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (25, 5, 3, 6, 0, 0, 'Variable is &gt; 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMmessage', 0, 0, NULL, 0, 0, 'VAR1 is greater than 5', 0, 0, '2007-02-28 16:50:40', '', '', '', 0, 0, 579, 342);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (26, 5, 4, 6, 0, 0, 'Variable is not &gt; 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'VAR1 is NOT greater than 5', 0, 0, '2007-02-28 17:04:47', '', '', '', 0, 0, 311, 343);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (27, 5, 5, 5, 0, 0, 'Test for VAR1 less then 5', 0, '5', '0', '3', '5', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 17:32:33', '', '', '', 0, 0, 580, 423);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (28, 5, 6, 6, 0, 0, 'Variable is less then 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'VAR1 is less than 5', 0, 0, '2007-02-28 17:06:50', '', '', '', 0, 0, 311, 423);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (29, 5, 7, 6, 0, 0, 'Variable is &gt; = 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'VAR1 is NOT less than 5', 0, 0, '2007-02-28 17:07:13', '', '', '', 0, 0, 580, 509);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (30, 5, 8, 5, 0, 0, 'Test for VAR1 = 5', 0, '5', '0', '1', '5', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '9', 0, 0, '2007-02-28 17:32:18', '', '', '', 0, 0, 313, 514);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (31, 5, 9, 6, 0, 0, 'Variable is = 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Test VAR1 = 5: VAR1 is equal to 5', 0, 0, '2007-02-28 22:11:56', '', '', '', 0, 0, 44, 510);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (32, 5, 10, 6, 0, 0, 'Variable is not = 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Test VAR1 = 5: VAR1 is NOT equal to 5', 0, 0, '2007-02-28 22:12:16', '', '', '', 0, 0, 315, 598);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (33, 5, 11, 5, 0, 0, 'If VAR1 != 5', 0, '5', '0', '4', '5', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2006-09-21 16:37:19', '', '', '', 0, 0, 45, 594);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (34, 5, 12, 6, 0, 0, 'Variable is != 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Test VAR1 != 5: VAR1 is NOT equal to 5', 0, 0, '2007-02-28 22:12:30', '', '', '', 0, 0, 44, 680);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (35, 5, 13, 6, 0, 0, 'Variable is = 5', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Test VAR1 != 5: VAR1 is equal to 5', 0, 0, '2007-02-28 22:12:50', '', '', '', 0, 0, 320, 681);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (36, 6, 1, 7, 0, 0, 'Interactive Task - test user Accept', 1, '6', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 0, NULL, 0, 0, 'Batch Function (Task #1): Success', 0, 0, '2007-02-28 21:23:43', '', '', '', 0, 0, 324, 265);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (37, 6, 2, 5, 0, 0, 'Check previous task status', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2007-02-28 21:17:59', '', '', '', 0, 0, 324, 353);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (38, 6, 3, 6, 0, 0, 'Success Test Passed', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Batch Function (Task #3): Success Test Passed', 0, 0, '2007-02-28 21:20:17', '', '', '', 0, 0, 322, 438);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (39, 6, 4, 6, 0, 0, 'Success Test Failed', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Batch Function (Task #4): Success Test Failed', 0, 0, '2007-02-28 21:20:38', '', '', '', 0, 0, 598, 354);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (40, 6, 5, 7, 0, 0, 'Interactive Task - test user Cancel', 1, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 0, NULL, 0, 0, 'Interactive Function (Task #5): Cancel Task', 0, 0, '2007-02-28 21:23:59', '', '', '', 0, 0, 599, 437);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (41, 6, 6, 5, 0, 0, 'If Cancel', 0, '0', '2', '0', '', 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '2006-09-22 13:53:32', '', '', '', 0, 0, 598, 527);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (42, 6, 7, 6, 0, 0, 'Cancel Test Pass', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Batch Function (Task #7): Cancel Test Passed', 0, 0, '2007-02-28 21:58:46', '', '', '', 0, 0, 597, 616);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (43, 6, 8, 6, 0, 0, 'Cancel Test Fail', 0, '0', '0', '0', '0', 0, 0, 0, 0, 0, 0, 'nf_logMessage', 0, 0, NULL, 0, 0, 'Batch Function (Task #8): Cancel Test Failed', 0, 0, '2007-02-28 21:58:57', '', '', '', 0, 0, 322, 527);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (58, 4, 2, 6, 0, 0, 'Set Task3 Taskname', 0, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, 0, 'nf_testsuiteSetTaskname', 0, 0, NULL, 0, 0, 'Branch 1 Test Task', 0, 0, '2007-02-28 22:47:46', '', '', '', 0, 0, 336, 366);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (54, 3, 1, 7, 0, 0, 'Test Overview', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Test 3 parallel Tasks ( 1 interactive). Complete task to proceed', 0, 0, '2007-02-28 12:25:21', '', '', '', 0, 0, 313, 252);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (55, 4, 1, 7, 0, 0, 'Start Test', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Press complete to launch 2 parallel tasks', 0, 0, '2007-03-01 00:07:57', '', '', '', 0, 0, 337, 276);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (56, 5, 14, 7, 0, 0, 'End of test - Last task', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'All conditional tests have completed.', 0, 0, '2007-02-28 17:39:22', '', '', '', 0, 0, 42, 767);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (57, 6, 9, 7, 0, 0, 'End of test', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_alertUserMessage', 0, 0, NULL, 0, 0, 'Last task in workflow', 0, 0, '2007-02-28 21:58:34', '', '', '', 0, 0, 321, 612);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (59, 1, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 68, 281);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (60, 1, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 69, 554);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (61, 2, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 46, 262);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (62, 2, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 40, 527);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (63, 3, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 46, 254);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (64, 3, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 585, 595);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (65, 4, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 61, 277);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (66, 4, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 73, 544);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (67, 5, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 34, 259);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (68, 5, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 321, 768);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (69, 6, NULL, 9, 0, 1, 'Start', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 43, 264);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedata']} (`id`, `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `isDynamicForm`, `dynamicFormVariableID`, `isDynamicTaskName`, `dynamicTaskNameVariableID`, `function`, `formid`, `fieldid`, `varValue`, `incValue`, `varToSet`, `optionalParm`, `reminderInterval`, `subsequentReminderInterval`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`, `numReminders`, `escalateVariableID`, `offsetLeft`, `offsetTop`) VALUES (70, 6, NULL, 10, 0, 0, 'End', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, 0, NULL, 0, 0, '', 0, 0, '0000-00-00 00:00:00', '', '', '', 0, 0, 50, 611);";

$_SQL[] = "CREATE TABLE {$_TABLES['nftemplatedatanextstep']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataFrom` int(11) NOT NULL default '0',
  `nf_templateDataTo` int(11) default '0',
  `nf_templateDataToFalse` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (60, 1, 2, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (59, 2, 3, 4);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (65, 3, 6, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (66, 4, 6, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (69, 54, 12, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (128, 7, 9, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (127, 7, 8, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (56, 8, 10, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (58, 9, 10, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (129, 10, 11, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (132, 12, 15, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (131, 12, 14, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (130, 12, 13, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (168, 15, 16, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (71, 16, 17, 15);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (81, 13, 17, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (82, 14, 17, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (90, 17, 18, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (157, 35, 56, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (156, 34, 56, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (159, 20, 22, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (95, 21, 22, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (99, 23, 24, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (108, 24, 25, 26);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (104, 25, 27, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (107, 26, 27, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (114, 27, 28, 29);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (111, 28, 30, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (112, 29, 30, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (113, 30, 31, 32);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (154, 31, 33, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (155, 32, 33, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (33, 33, 34, 35);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (143, 36, 37, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (134, 37, 38, 39);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (139, 38, 40, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (140, 39, 40, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (144, 40, 41, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (39, 41, 42, 43);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (149, 42, 57, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (150, 43, 57, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (163, 58, 20, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (167, 55, 58, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (166, 55, 21, NULL);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (169, 59, 1, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (170, 6, 60, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (171, 61, 7, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (172, 11, 62, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (173, 63, 54, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (174, 18, 64, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (175, 65, 55, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (176, 22, 66, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (177, 67, 23, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (178, 56, 68, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (179, 69, 36, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`id`, `nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) VALUES (180, 57, 70, 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nftemplatevariables']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `nf_variableTypeID` int(11) NOT NULL default '0',
  `variableName` varchar(100) NOT NULL default '',
  `variableValue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`),
  KEY `nf_variableTypeID` (`nf_variableTypeID`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (1, 1, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (2, 3, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (3, 4, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (4, 5, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (5, 5, 0, 'VAR1', '1');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (6, 6, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (7, 7, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (8, 2, 0, 'INITIATOR', '');";
$_SQL[] = "INSERT INTO {$_TABLES['nftemplatevariables']} (`id`, `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES (9, 4, 0, 'TASKNAME', '');";

$_SQL[] = "CREATE TABLE {$_TABLES['nfuseraway']} (
  `uid` mediumint(8) NOT NULL default '0',
  `away_start` int(11) NOT NULL default '0',
  `away_return` int(11) NOT NULL default '0',
  `reassign_uid` mediumint(8) NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `is_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproject_approvals']} (
  `id` int(11) NOT NULL auto_increment,
  `process_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date_updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `form_id` (`form_id`),
  KEY `uid` (`uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproject_comments']} (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `comment` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproject_forms']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `trial_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `formtype` varchar(32) NOT NULL default '',
  `results_id` mediumint(8) NOT NULL default '0',
  `created_by_taskid` mediumint(8) NOT NULL default '0',
  `created_by_uid` mediumint(8) NOT NULL default '0',
  `is_locked_by_uid` mediumint(8) NOT NULL default '0',
  `is_trial_project` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `form_id` (`form_id`),
  KEY `results_id` (`results_id`),
  KEY `created_by_taskid` (`created_by_taskid`),
  KEY `trial_id` (`trial_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproject_taskhistory']} (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `assigned_uid` mediumint(8) NOT NULL default '0',
  `date_assigned` int(11) NOT NULL default '0',
  `date_started` int(11) NOT NULL default '0',
  `date_completed` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `assigned_uid` (`assigned_uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfproject_timestamps']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `project_formid` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `statusmsg` varchar(255) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_formid` (`project_formid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nfprojects']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `wf_process_id` mediumint(8) NOT NULL default '0',
  `wf_task_id` mediumint(8) NOT NULL default '0',
  `project_num` varchar(12) NOT NULL default '',
  `originator_uid` mediumint(8) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `prev_status` tinyint(1) NOT NULL default '0',
  `related_processes` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `resultsid` (`project_num`),
  KEY `wf_templateid` (`wf_process_id`),
  KEY `originator_uid` (`originator_uid`),
  KEY `status` (`status`)
) TYPE=MyISAM;";

?>