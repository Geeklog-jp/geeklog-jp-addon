<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | testsuiteSQL.php                                                          |
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

require_once ('../lib-common.php' );
global $_TABLES;

if(true){
    $sql="delete from gl_nf_process";
    $res=DB_query($sql);
    $sql="delete from gl_nf_template";
    $res=DB_query($sql);
    $sql="delete from gl_nf_templateassignment";
    $res=DB_query($sql);
    $sql="delete from gl_nf_templatedatanextstep";
    $res=DB_query($sql);
    $sql="delete from gl_nf_templatevariables";
    $res=DB_query($sql);
    $sql="delete from gl_nf_queue";
    $res=DB_query($sql);
    $sql="delete from gl_nf_queuefrom";
    $res=DB_query($sql);
    }



    
$sql="INSERT INTO {$_TABLES['nfhandlers']} ( `handler`, `nf_handlerTypeID`, `description`) VALUES ( 'testsuite/yes-no.php', 0, '')";
$res=DB_query($sql);
$handlerID=DB_insertID();
    
//FIRST TEST TEMPLATE
    
$sql="INSERT INTO {$_TABLES['nftemplate']} ( templateName) VALUES ( 'TEST FLOW 1 - Manual Web Test')";
$res=DB_query($sql);
$insertID=DB_insertID();
$sql="INSERT INTO {$_TABLES['nftemplatevariables']} ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ({$insertID}, 0, 'INITIATOR', '')";
$res=DB_query($sql);

//insert first manual task and second IF task
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 1, 1, '{$handlerID}', 1, 'Accept or Reject', 0, NULL, NULL, NULL, NULL, 1, 0, '', 0, 'op-parm-test', 0, 0, 0, '2006-09-20 10:40:38', '', '', '')";
$res=DB_query($sql);
$tfrom=DB_insertID();
$first=$tfrom;
$sql="INSERT INTO {$_TABLES['nftemplateassignment']} (`nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$first}, 2, NULL, NULL , NULL, NULL, NULL)";
$res=DB_query($sql);

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 2, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, '', 0, '', 0, 0, 0, '2006-09-20 10:34:13', '', '', '')";
$res=DB_query($sql);
$tto=DB_insertID();
$tfalse="NULL";
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($tfrom,$tto,$tfalse)";
$res=DB_query($sql);

//batch Tasks insertted to support IF true branch and IF false branch
$tfrom=$tto;
//1st batch
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 3, 6, 0, 0, 'Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_passfail', 0, 'pass', 0, 0, 0, '2006-09-20 14:21:40', '', '', '')";
$res=DB_query($sql);
$tto=DB_insertID();
//second batch
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 4, 6, 0, 0, 'Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_passfail', 0, 'fail', 0, 0, 0, '2006-09-20 14:26:34', '', '', '')";
$res=DB_query($sql);
$tfalse=DB_insertID();
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($tfrom,$tto,$tfalse)";
$res=DB_query($sql);
//next insert the next task that both branches point to
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 5, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, '', 0, '', 0, 0, 0, '2006-09-20 15:13:22', '', '', '')";
$res=DB_query($sql);
$nextIF=DB_insertID();
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($tto,$nextIF,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($tfalse,$nextIF,NULL)";
$res=DB_query($sql);
//insert the last task
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ({$insertID}, 6, 6, 0, 0, 'No Operation', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_dummy', 0, '', 0, 0, 0, '2006-09-20 14:44:43', '', '', '')";
$res=DB_query($sql);
$last=DB_insertID();
//now insert the last linkage
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($nextIF,$last,$first)";
$res=DB_query($sql);














//SECOND TEST TEMPLATE



$sql="INSERT INTO {$_TABLES['nftemplate']} ( `templateName`) VALUES ( 'TEST FLOW 2 - AND Test 1: 2 Branch')";
$res=DB_query($sql);
$insertID=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branching out to tasks #2 AND #3)', 0, 0, 0, '2006-09-20 16:39:30', '', '', '')";
$res=DB_query($sql);
$first=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 2, 7, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #2): Complete. Next: Task #4', 0, 0, 0, '2006-09-20 16:43:06', '', '', '')";
$res=DB_query($sql);
$second=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 3, 7, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #3): Complete.  Next: Task #4', 0, 0, 0, '2006-09-21 10:37:25', '', '', '')";
$res=DB_query($sql);
$third=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$second,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$third,NULL)";
$res=DB_query($sql);


$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 4, 2, 0, 0, 'AND Gate', 0, NULL, NULL, NULL, NULL, 0, 0, '', 0, '', 0, 0, 0, '2006-09-20 16:31:23', '', '', '')";
$res=DB_query($sql);
$fourth=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($second,$fourth,NULL)";
$res=DB_query($sql);


$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($third,$fourth,NULL)";
$res=DB_query($sql);


$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 5, 6, 0, 0, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #5): End of Workflow.  Test Passed!', 0, 0, 0, '2006-09-20 16:43:29', '', '', '')";
$res=DB_query($sql);
$fifth=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fourth,$fifth,NULL)";
$res=DB_query($sql);

$sql="INSERT INTO {$_TABLES['nftemplateassignment']}  ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$second}, 2, NULL, NULL, NULL, NULL, NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplateassignment']}  ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$third}, 2, NULL, NULL, NULL, NULL, NULL)";
$res=DB_query($sql);






//THIRD TEST TEMPLATE


$sql="INSERT INTO {$_TABLES['nftemplate']} ( `templateName`) VALUES ( 'TEST FLOW 3 - AND Test 2: 3 Branch Regen')";
$res=DB_query($sql);
$insertID=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} (`nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, 1, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branching to Task #2, #3, #4', 0, 0, 0, '2006-09-21 10:47:49', '', '', '')";
$res=DB_query($sql);
$first=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 2, 6, 0, 0, 'Task 1', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #2): Now Moving to AND (Task #6)', 0, 0, 0, '2006-09-21 10:49:35', '', '', '')";
$res=DB_query($sql);
$second=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 3, 6, 0, 0, 'Task 2', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #3): Now Moving to AND (Task #6)', 0, 0, 0, '2006-09-21 11:15:41', '', '', '')";
$res=DB_query($sql);
$third=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 4, 7, 0, 0, 'Task 3', 0, NULL, NULL, NULL, NULL, 1, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #4): If accept, goto 6, else, goto 4', 0, 0, 0, '2006-09-21 16:07:24', '', '', '')";
$res=DB_query($sql);
$fourth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 5, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, '', 0, '', 0, 0, 0, '2006-09-21 11:00:30', '', '', '')";
$res=DB_query($sql);
$fifth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 6, 2, 0, 0, 'AND Gate', 0, NULL, NULL, NULL, NULL, 0, 0, '', 0, '', 0, 0, 0, '2006-09-21 11:01:18', '', '', '')";
$res=DB_query($sql);
$sixth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 7, 6, 0, 0, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #7): Passed AND gate. EoW. Test Passed!', 0, 0, 0, '2006-09-21 11:07:15', '', '', '')";
$res=DB_query($sql);
$seventh=DB_insertID();



$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$second,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$third,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$fourth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fourth,$fifth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fifth,$sixth,$fourth)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($second,$sixth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($third,$sixth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($sixth,$seventh,NULL)";
$res=DB_query($sql);


$sql="INSERT INTO `gl_nf_templatevariables` ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ( {$insertID}, 0, 'INITIATOR', '')";
$res=DB_query($sql);
$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ({$fourth} , 2, NULL, NULL, NULL, NULL, NULL)";
$res=DB_query($sql);





//FOURTH TEST FLOW


$sql="INSERT INTO {$_TABLES['nftemplate']} ( `templateName`) VALUES ( 'TEST FLOW 4 - OR Test')";
$res=DB_query($sql);
$insertID=DB_insertID();
$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, NULL, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branch out to tasks #2 and #3', 0, 0, 0, '2006-09-22 11:27:08', '', '', '')";
$res=DB_query($sql);
$first=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 2, 7, 0, 0, 'Task 1', 1, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #2): Completed.', 0, 0, 0, '2006-09-22 11:30:13', '', '', '')";
$res=DB_query($sql);
$second=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 3, 7, 0, 0, 'Task 2', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Task (Task #3): Completed.', 0, 0, 0, '2006-09-22 11:27:18', '', '', '')";
$res=DB_query($sql);
$third=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 4, 6, 0, 0, 'No Op', NULL, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 3, 'Batch Function (Task #5): EoW.  Successful Test!', 0, 0, 0, '2006-09-22 11:24:34', '', '', '')";
$res=DB_query($sql);
$fourth=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$second,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$third,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($second,$fourth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($third,$fourth,NULL)";
$res=DB_query($sql);


$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ({$second} , 2, NULL, NULL, NULL, NULL, NULL)";
$res=DB_query($sql);
$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ({$third} , 2, NULL, NULL, NULL, NULL, NULL)";
$res=DB_query($sql);
$sql="INSERT INTO `gl_nf_templatevariables` ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ({$insertID}, 0, 'INITIATOR', '')";
$res=DB_query($sql);




//FIFTH TEST FLOW


$sql="INSERT INTO {$_TABLES['nftemplate']} ( `templateName`) VALUES ( 'TEST FLOW 5 - IF Test 1: Variable Value')";
$res=DB_query($sql);
$insertID=DB_insertID();



$sql="INSERT INTO `gl_nf_templatevariables` ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ({$insertID}, 0, 'INITIATOR', '')";
$res=DB_query($sql);
$initiatorVar=DB_insertID();
$sql="INSERT INTO `gl_nf_templatevariables` ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ({$insertID}, 0, 'VAR1', '1')";
$res=DB_query($sql);
$var1=DB_insertID();




$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 1, 7, 0, 1, 'Set VAR1', 1, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_setvar1', 0, '', 0, 0, 0, '2006-09-21 16:34:30', '', '', '')";
$res=DB_query($sql);
$first=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 2, 5, 0, 0, 'If VAR1 &gt; 5', 0, '{$var1}', '0', '2', '5', 0, 0, '', 0, '', 0, 0, 0, '2006-09-21 16:32:44', '', '', '')";
$res=DB_query($sql);
$second=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 3, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is greater than 5', 0, 0, 0, '2006-09-21 16:28:43', '', '', '')";
$res=DB_query($sql);
$third=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 4, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT greater than 5', 0, 0, 0, '2006-09-21 16:28:24', '', '', '')";
$res=DB_query($sql);
$fourth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 5, 5, 0, 0, 'If VAR1', 0, '{$var1}', '0', '3', '5', 0, 0, '', 0, '', 0, 0, 0, '2006-09-21 16:51:07', '', '', '')";
$res=DB_query($sql);
$fifth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 6, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is less than 5', 0, 0, 0, '2006-09-21 16:43:33', '', '', '')";
$res=DB_query($sql);
$sixth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 7, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT less than 5', 0, 0, 0, '2006-09-21 16:42:17', '', '', '')";
$res=DB_query($sql);
$seventh=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 8, 5, 0, 0, 'If VAR1 = 5', 0, '{$var1}', '0', '1', '5', 0, 0, '', 0, '9', 0, 0, 0, '2006-09-21 16:36:21', '', '', '')";
$res=DB_query($sql);
$eighth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 9, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is equal to 5', 0, 0, 0, '2006-09-21 16:41:17', '', '', '')";
$res=DB_query($sql);
$ninth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 10, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT equal to 5', 0, 0, 0, '2006-09-21 16:39:50', '', '', '')";
$res=DB_query($sql);
$tenth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 11, 5, 0, 0, 'If VAR1 != 5', 0, '{$var1}', '0', '4', '5', 0, 0, '', 0, '', 0, 0, 0, '2006-09-21 16:37:19', '', '', '')";
$res=DB_query($sql);
$eleventh=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 12, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT equal to 5', 0, 0, 0, '2006-09-21 16:39:05', '', '', '')";
$res=DB_query($sql);
$twelveth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 13, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is equal to 5', 0, 0, 0, '2006-09-21 16:38:38', '', '', '')";
$res=DB_query($sql);
$thirteenth=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$second,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($second,$third,$fourth)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($third,$fifth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fourth,$fifth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fifth,$sixth,$seventh)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($sixth,$eighth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($seventh,$eighth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($eighth,$ninth,$tenth)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($ninth,$eleventh,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($tenth,$eleventh,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($eleventh,$twelveth,$thirteenth)";
$res=DB_query($sql);


$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$first}, NULL, NULL, {$initiatorVar}, NULL, NULL, NULL)";
$res=DB_query($sql);







//TEST FLOW 6

$sql="INSERT INTO {$_TABLES['nftemplate']} ( `templateName`) VALUES ( 'TEST FLOW 6 - IF Test 2: Task Status')";
$res=DB_query($sql);
$insertID=DB_insertID();


$sql="INSERT INTO `gl_nf_templatevariables` ( `nf_templateID`, `nf_variableTypeID`, `variableName`, `variableValue`) VALUES ({$insertID}, 0, 'INITIATOR', '')";
$res=DB_query($sql);
$variableID=DB_insertID();


$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 1, 7, 0, 1, 'Success Task', 1, '{$variableID}', NULL, NULL, NULL, 0, 0, 'nf_testsuite_success', 0, 'Batch Function (Task #1): Success', 0, 0, 0, '2006-09-22 15:06:27', '', '', '')";
$res=DB_query($sql);
$first=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 2, 5, 0, 0, 'If Success', 0, '0', '1', '0', '', NULL, 0, '', 0, '', 0, 0, 0, '2006-09-22 11:48:49', '', '', '')";
$res=DB_query($sql);
$second=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 3, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #3): Test Passed', 0, 0, 0, '2006-09-22 13:36:05', '', '', '')";
$res=DB_query($sql);
$third=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 4, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #4): Test Failed', 0, 0, 0, '2006-09-22 13:38:30', '', '', '')";
$res=DB_query($sql);
$fourth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 5, 7, 0, 0, 'Cancel Task', 1, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_cancel', 0, 'Interactive Function (Task #5): Cancel Task', 0, 0, 0, '2006-09-22 15:05:09', '', '', '')";
$res=DB_query($sql);
$fifth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 6, 5, 0, 0, 'If Cancel', 0, '0', '2', '0', '', 0, 0, '', 0, '', 0, 0, 0, '2006-09-22 13:53:32', '', '', '')";
$res=DB_query($sql);
$sixth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 7, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #7): Test Passed', 0, 0, 0, '2006-09-22 14:00:03', '', '', '')";
$res=DB_query($sql);
$seventh=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 8, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #8): Test Failed', 0, 0, 0, '2006-09-22 14:00:33', '', '', '')";
$res=DB_query($sql);
$eighth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 13, 7, 0, 0, 'Abort Task', 1, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_abort', 0, 'Interactive Function (Task #13): Abort Task', 0, 0, 0, '2006-09-22 15:54:46', '', '', '')";
$res=DB_query($sql);
$thirteenth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 14, 5, 0, 0, 'If Aborted', 0, '0', '4', '0', '', 0, 0, '', 0, '', 0, 0, 0, '2006-09-22 13:54:54', '', '', '')";
$res=DB_query($sql);
$fourteenth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 15, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #15): Test Passed', 0, 0, 0, '2006-09-22 13:59:17', '', '', '')";
$res=DB_query($sql);
$fifteenth=DB_insertID();

$sql="INSERT INTO {$_TABLES['nftemplatedata']} ( `nf_templateID`, `logicalID`, `nf_stepType`, `nf_handlerId`, `firstTask`, `taskname`, `assignedByVariable`, `argumentVariable`, `argumentProcess`, `operator`, `ifValue`, `regenerate`, `regenAllLiveTasks`, `function`, `formid`, `optionalParm`, `reminderInterval`, `numReminders`, `escalateVariableID`, `last_updated`, `prenotify_message`, `postnotify_message`, `reminder_message`) VALUES ( {$insertID}, 16, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #16): Test Failed', 0, 0, 0, '2006-09-22 13:59:02', '', '', '')";
$res=DB_query($sql);
$sixteenth=DB_insertID();



$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($first,$second,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($second,$third,$fourth)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($third,$fifth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fourth,$fifth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fifth,$sixth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($sixth,$seventh,$eighth)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($seventh,$thirteenth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($eighth,$thirteenth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($thirteenth,$fourteenth,NULL)";
$res=DB_query($sql);
$sql="INSERT INTO {$_TABLES['nftemplatedatanextstep']} (`nf_templateDataFrom`, `nf_templateDataTo`, `nf_templateDataToFalse`) values ($fourteenth,$fifteenth,$sixteenth)";
$res=DB_query($sql);



$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$first}, NULL, NULL, {$variableID}, NULL, NULL, NULL)";
$res=DB_query($sql);

$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$fifth}, NULL, NULL, {$variableID}, NULL, NULL, NULL)";
$res=DB_query($sql);

$sql="INSERT INTO `gl_nf_templateassignment` ( `nf_templateDataID`, `uid`, `gid`, `nf_processVariable`, `nf_prenotifyVariable`, `nf_postnotifyVariable`, `nf_remindernotifyVariable`) VALUES ( {$thirteenth}, NULL, NULL, {$variableID}, NULL, NULL, NULL)";
$res=DB_query($sql);

?>