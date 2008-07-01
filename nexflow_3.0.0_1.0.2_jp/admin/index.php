<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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

require_once ('../../../lib-common.php' );

// Only let users with nexflow.edit rights to access this page
if (!SEC_hasRights('nexflow.edit')) { 
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_NF00['access_denied']);
    $display .= $LANG_NF00['admin_access_error'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
} 

require_once ($_CONF['path'] . 'plugins/nexflow/config.php' );
require_once ($_CONF['path_system'] . 'classes/navbar.class.php' );

if (isset($_USER['uid'] ) ) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
} 

$templateID = COM_applyFilter($_POST['templateID'], true );
if($templateID==0){
    $templateID = COM_applyFilter($_GET['templateID'], true );
}
$taskID = COM_applyFilter($_POST['taskID'], true );
$editid = COM_applyFilter($_POST['templateTaskID'], true );
$lID = COM_applyFilter($_POST['logicalID'], true );
$handlerID = COM_applyFilter($_POST['idhandler'], true );
$stepID = COM_applyFilter($_POST['idstepType'], true );

//$taskName = ppPrepareForDB($_POST['taskName'],true);
if (!get_magic_quotes_gpc()) {
    $taskName = addslashes($_POST['taskName']);
} else {
    $taskName = $_POST['taskName'];
}
$taskName=COM_killJS($taskName);

$op = COM_applyFilter($_POST['operation'], false );
$moveop = COM_applyFilter($_POST['moveoperation'], false );
$regen = COM_applyFilter($_POST['regenerate'], true );
$regenAllTasks = COM_applyFilter($_POST['regenerateAllLive'], true );
$taskassigntype = COM_applyFilter($_POST['taskassigntype']);

$retval = '';
echo COM_siteHeader('menu' );
$navbar = new navbar;
$navbar->add_menuitem('My Tasks', $CONF_NF['TaskConsole_URL'] );
if ($templateID > 0) {
    $navbar->add_menuitem('Edit Template', $_CONF['site_admin_url'] . '/plugins/nexflow/index.php?templateID='.$templateID );
    $navbar->set_selected('Edit Template');
}
$navbar->add_menuitem('View Templates', $_CONF['site_admin_url'] . '/plugins/nexflow/templates.php' );
$navbar->add_menuitem('Edit Handlers', $_CONF['site_admin_url'] . '/plugins/nexflow/handlers.php' );   
echo $navbar->generate();


if ($taskID == 0 ) {
    $taskID = null;
} 
// lets check the incoming operation.. if its save, then save either the existing data
// or create a new entry.
if ($moveop != '' || $moveop != null ) {
    // if there is a move operation, this takes precedence over any other click/variable feed.
    $op = $moveop;
}

switch (strtolower($op ) ) {
    case 'move up':

        movelidup($taskID );

        break;

    case 'move down':

        moveliddown($taskID );

        break;

    case 'save':

        if (!get_magic_quotes_gpc()) {
            $taskFunction = addslashes($_POST['task_function']);
        } else {
            $taskFunction = $_POST['task_function'];
        }
        $task_formid = COM_applyFilter($_POST['task_form'], true );
        $ifVariable = COM_applyFilter($_POST['ifTaskidifTaskvariableName'], true );
        $ifProcessItem = COM_applyFilter($_POST['ifTaskidifTasklabel'], true );
        $ifOperator = COM_applyFilter($_POST['ifTaskidifTaskoperator'], true );
        $ifArgumentValue = COM_applyFilter($_POST['nfIfTaskArgumentValue'], false );
        $taskType = strtolower(DB_getItem($_TABLES['nfsteptype'], 'steptype', "id=" . $stepID ) );
        $notifyinterval = COM_applyFilter($_POST['notifyinterval'], true );
        $numReminders = COM_applyFilter($_POST['numReminders'], true);
        $escUser = COM_applyFilter($_POST['esc_user'], true);
        
        $optionalParm = COM_applyFilter($_POST['optionalParm']);

        if ($editid > 0 ) {   // Check if this is an existing task
            // insert next steps.. first delete all the old next steps
            // need to determine if this is an IF task or not.. if its an IF task, the nextsteps table
            // needs to be inserted with the appropriate to and toFalse values
            // likewise, we can use this structure to properly tack in the IF task's data into the db
            // this is an existing task.. do straight updates.
            switch ($taskType ) {
                case 'if':

                    if ($ifVariable == 0 ) { // check for the presence of the processItem..
                        if ($ifProcessItem != 0 ) { // if this is here, then we have a scenario where they've chosen the process item as the argument
                            $sql = "UPDATE {$_TABLES['nftemplatedata']} set argumentProcess='$ifProcessItem', argumentVariable='', ";
                            $sql .= "operator='$ifOperator', ifValue='$ifArgumentValue' where id=$editid";
                            $result = DB_Query($sql ); 
                            // exit(0);
                        } 
                        // if we're in here, they've not chosen anything from the process or variable IF task drop downs..
                    } else { // they've chosen the variable as the argument
                        $sql = "UPDATE {$_TABLES['nftemplatedata']} set argumentProcess='', argumentVariable='$ifVariable', ";
                        $sql .= "operator='$ifOperator', ifValue='$ifArgumentValue' where id=$editid";
                        $result = DB_Query($sql );
                    } 
                    $nextStepsTrue = str_replace(" ", "", ($_POST['ifTaskTrue']) );
                    $nextStepsFalse = str_replace(" ", "",($_POST['ifTaskFalse']) );
                    if (strlen($nextStepsTrue ) > 0 ) {
                        $nextStepsTrue = split(",", $nextStepsTrue );
                        $numberStepsTrue = count($nextStepsTrue );
                        $nextStepsFalse = split(",", $nextStepsFalse );
                        $numberStepsFalse = count($nextStepsFalse );

                        if ($numberStepsFalse > $numberStepsTrue ) {
                            $numberSteps = $numberStepsFalse;
                        } else {
                            $numberSteps = $numberStepsTrue;
                        } 

                        $sql = "DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom=$editid";
                        $result = DB_Query($sql );
                        for($cntr = 0;$cntr < $numberSteps;$cntr++ ) {
                            if ($nextStepsTrue[$cntr] == null ) {
                                $nextStepsTrue[$cntr] = 'NULL';
                            } 
                            if ($nextStepsFalse[$cntr] == null ) {
                                $nextStepsFalse[$cntr] = 'NULL';
                            } 
                            $nextStepsTrue[$cntr] = COM_applyFilter($nextStepsTrue[$cntr],true);
                            $nextStepsFalse[$cntr] = COM_applyFilter($nextStepsFalse[$cntr],true);
                            $templateToStep = lidtonfid($nextStepsTrue[$cntr], $templateID );
                            $templateToStepFalse = lidtonfid($nextStepsFalse[$cntr], $templateID );
                            $sql = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse ) ";
                            $sql .= "VALUES ('$editid', '$templateToStep', '$templateToStepFalse')";
                            $result = DB_Query($sql );
                        } 
                    } else {
                        // trying to remove the next steps then as there are none listed!
                        $sql = "DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom=$editid";                         
                        $result = DB_Query($sql );
                    } 
                    // have to save the operator, arguments and values
                    $sql  = "UPDATE {$_TABLES['nftemplatedata']} set argumentVariable='$ifVariable', ";
                    $sql .= "argumentProcess='$ifProcessItem', ";
                    $sql .= "operator='$ifOperator', ";
                    $sql .= "ifValue='$ifArgumentValue', ";
                    $sql .= "nf_handlerID='0' ";
                    $sql .= "where id='$editid'"; 
                    // echo $sql;
                    $result = DB_Query($sql );

                    break;

                default: // not an if task
                    $nextSteps = str_replace(" ", "", ($_POST['nextTasks']) );
                    if (strlen($nextSteps ) > 0 ) {
                        $nextSteps = split(",", $nextSteps );
                        $numberSteps = count($nextSteps );
                        $sql = "DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom='$editid'";
                        $result = DB_Query($sql );
                        for($cntr = 0;$cntr < $numberSteps;$cntr++ ) {
                            $templateNextStep = lidtonfid(COM_applyFilter($nextSteps[$cntr],true), $templateID);
                            $sql  = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (nf_templateDataFrom,nf_templateDataTo ) ";
                            $sql .= "VALUES ('$editid','$templateNextStep')";
                            $result = DB_Query($sql );
                        } 
                    } else {
                        // trying to remove the next steps then as there are none listed!
                        $sql = "DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom='$editid'";
                        $result = DB_Query($sql );
                    } 

                    break;
            } //end switch 
            // continue on merrily saving the task.
            $sql = "UPDATE {$_TABLES['nftemplatedata']} SET nf_stepType='$stepID'";
            if (($stepID == 1 OR $stepID == 4) AND $handlerID > 0 ) {
                $sql .= ", nf_handlerID='$handlerID'";
            } else {
                $sql .= ", nf_handlerID='0'";
            }
            
            if (($stepID == 6 OR $stepID == 7) AND $taskFunction != '' ) {
                $sql .= ", function='$taskFunction'";
            } else {
                $sql .= ", function=''";
            }
            
            if (($stepID == 8) AND $task_formid != '' ) {
                $sql .= ", formid='$task_formid'";
            } else {
                $sql .= ", formid=0";
            }

            $sql .= ", optionalParm='$optionalParm'";
            if (($stepID == 1) || ($stepID == 7) || ($stepID == 8)) {
                $sql .= ", numReminders='$numReminders'";
                $sql .= ", escalateVariableID='$escUser'";
            }
            else {
                $sql .= ", numReminders=0";
                $sql .= ", escalateVariableID=0";
            }
            $sql .= ", regenerate='$regen'";

            /* Update Task Assignment Options */
            if ($taskassigntype == 'user') {
                DB_Query("UPDATE {$_TABLES['nftemplatedata']} set assignedByVariable=0 where id='$editid'");
            } else {
                DB_Query("UPDATE {$_TABLES['nftemplatedata']} set assignedByVariable=1 where id='$editid'");
            }
            $sql .= ", taskname='$taskName'";
            $sql .= ", reminderInterval='$notifyinterval'";
            $sql .= ", logicalID='$lID'";
            $sql .= " WHERE id='$editid'";
            $result = DB_Query($sql );
            
            $taskID = $editid;
            
        } //end if Taskid is null
        else {
            // new task to save - regardless of what type of task this is we have to save it.

            if (trim($taskName) == '') {
                $taskName = 'New Task';
            } 
            if ($stepID == 0) {
                $stepID = 1;  // Default to a manaual task if not selected
            }
            if ($lID == 0) {
                $lID = 1;     // Check if new logical Task ID = 0 - not allowed
            }
            // lets determine if there are any other tasks in this workflow.. otherwise we have to set the first task bit..
            $sql = "SELECT count( * ) FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID = '$templateID'";
            $fields = 'logicalID, nf_templateID,nf_stepType, nf_handlerId, function, formid, optionalParm, firstTask, taskname, regenerate,reminderInterval';
            if (DB_numRows(DB_Query($sql))) {
                // no rows.. thus first task
                $sql = "INSERT INTO {$_TABLES['nftemplatedata']} ($fields) ";
                $sql .= "VALUES ('$lID','$templateID','$stepID','$handlerID','$taskFunction','$task_formid','$optionalParm',1,'$taskName','$regen','$notifyinterval')";
                $result = DB_Query($sql );
                $taskID = DB_insertID();
            } else {
                $sql = "INSERT INTO {$_TABLES['nftemplatedata']} ($fields) ";
                $sql .= "VALUES ('$lID','$templateID','$stepID','$handlerID','$taskFunction','$task_formid','$optonalParm',0,'$taskName','$regen','$notifyinterval')";
                $result = DB_Query($sql );
                $taskID = DB_insertID();
            } 
            // echo $sql;
        } 
        // Update the timestamp - used to sort records if we have duplicates that need to be re-ordered
        // Assume the latest updated record should have the logical ID entered - in case of new duplicate
        DB_query("UPDATE {$_TABLES['nftemplatedata']} set last_updated = now() WHERE id='$taskID'");

        // Check and see if we have any duplicate logical ID's and need to reorder
        $sql = "SELECT id FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateID' AND logicalID = '$lID'";

        if (DB_numRows(DB_query($sql)) > 1) {
            $sql  = "SELECT id,logicalID FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateID' ";
            $sql .= "AND logicalID >= '$lID' ORDER BY logicalID ASC, last_updated DESC";
            $query = DB_query($sql);
            $id = $lID;
            while ($A = DB_fetchArray($query)) {  // Reset field firstTask 
                DB_query("UPDATE {$_TABLES['nftemplatedata']} set logicalID='$id',firstTask=0 WHERE id='{$A['id']}'");
                $id++;
            }
        }

        // Set the firstTask Flag for just the first logical id
        // Reset all to 0 and then set the flag to 1 for the first logical task
        DB_query("UPDATE {$_TABLES['nftemplatedata']} set firstTask=0 WHERE nf_templateID='$templateID'");
        $sql = "SELECT id FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateID' ORDER BY logicalID  Limit 1";
        list ($first_taskID)  = DB_fetchArray(DB_query($sql));
        DB_query("UPDATE {$_TABLES['nftemplatedata']} set firstTask=1 WHERE id='$first_taskID'");


        break;

    case 'delete':
        DB_query("DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom = '{$taskID}'");
        DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='{$taskID}'");
        DB_query("DELETE FROM {$_TABLES['nftemplatedata']} WHERE id='{$taskID}'");
        echo COM_refresh("index.php?templateID=" . $templateID);
        break;
} 

if ($templateID > 0 ) {

    $actionurl = $_CONF['site_admin_url'] . '/plugins/nexflow/index.php';
    $imgset = "{$_CONF['layout_url']}/nexflow/images";

    $reminder_image = '<span style="padding-left:5px;"><img src ="'.$imgset.'/admin/reminder.gif" TITLE="Task Reminder Enabled"></span>';
    $notify1_image = '<span style="padding-left:5px;"><img src ="'.$imgset.'/admin/notify.gif" TITLE="Task Assignment Notification Enabled"></span>';
    $notify2_image = '<span style="padding-left:5px;"><img src ="'.$imgset.'/admin/postnotify.gif" TITLE="Task Completion Notification Enabled"></span>';

    $p = new Template($_CONF['path_layout'] . 'nexflow/admin');
    $p->set_file (array (
            'page'      => 'template_tasks.thtml',
            'records'   => 'template_task_record.thtml' ));

    $p->set_var ('action_url',$actionurl);
    $p->set_var ('public_url', $_CONF['site_admin_url'] . '/plugins/nexflow');
    $p->set_var ('template_id', $templateID);
    $p->set_var ('edit_task_id', $taskID);
    $p->set_var ('show_taskoptions','none');
    $p->set_var ('show_taskoption1','none');
    $p->set_var ('show_taskoption2','none');
    $p->set_var ('show_emailoptions','none');
    $p->set_var ('show_if','none');
    $p->set_var ('show_function','none');
    $p->set_var ('show_handler','none');
    
    $p->set_var ('show_dynamicformcb','none');
    $p->set_var ('show_dynamicformvars','display:none');
    
    $p->set_var ('show_dynamicnamecb','');
    $p->set_var ('show_dynamicnamevars','display:none');   
    
    $p->set_var ('show_form','none');
    $p->set_var ('LANG_DELCONFIRM', 'Are you sure you want to delete task');

    $p->set_var ('LANG_help1','Checking this will alert the engine to regenerate this task upon a loop back condition');
    $p->set_var ('LANG_help2','Checking this will assign this task to a variable\'s value regardless of who\'s physically assigned to it above');    
    $p->set_var ('LANG_help3','Checking this in combimation with the \'Regenerate This Task\' option will signal the nexFlow engine to carry all<BR>currently in-production tasks to the newly regenerated process.');
    
    $sql  = "SELECT a.templateName,b.steptype, c.*, d.handler FROM {$_TABLES['nftemplatedata']} c ";
    $sql .= "INNER JOIN {$_TABLES['nfsteptype']} b ON c.nf_steptype=b.id ";
    $sql .= "INNER JOIN {$_TABLES['nftemplate']} a on c.nf_templateID=a.id ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfhandlers']} d on d.id=c.nf_handlerID ";
    $sql .= "WHERE 1=1";
    if ($templateID != null ) {
        $sql .= " AND a.id='$templateID'";
        $sql .= " ORDER BY c.logicalid, c.firsttask ASC ";
    } else { // show all templates
        $sql .= " ORDER BY c.logicalid, c.firstTask ASC ";
    } 

    $thisTemplate = 0;
    $templateResult = DB_query($sql );
    $numActions = DB_numRows($templateResult );
    
    /* Loop thru all the template tasks for the left handside display */
    for($cntr = 0;$cntr < $numActions;$cntr++ ) {
        $A = DB_fetchArray($templateResult );
        $p->set_var ('template_name',$A['templateName']);
        $p->set_var ('rowid',$cntr);
        $p->set_var ('task_step_id',$A['logicalID']);
        $p->set_var ('task_id',$A['id']);
        $p->set_var ('task_name',$A['taskname']);
        
        if($A['isDynamicTaskName']==1){
            $dynSQL="SELECT variableName from {$_TABLES['nftemplatevariables']} where id='{$A['dynamicTaskNameVariableID']}'";
            $dynRes=DB_query($dynSQL);
            list($dynVar)=DB_fetchArray($dynRes);
            $p->set_var ('show_has_dynamic_name','<span class="pluginTinyText" style="color:red"><BR>[&nbsp;Dynamic Task Name Using: ' .$dynVar.'&nbsp;]</span>');
        }else{
            $p->set_var ('show_has_dynamic_name','');
        }
        
        
        
        $p->set_var ('task_steptype',$A['steptype']);
        $p->set_var ('show_assigned','none');
        $p->set_var ('show_taskhandler','');
        $p->set_var ('LANG_Handler','Handler');
        if ($taskID == $A['id']) {
            $p->set_var('editmodestyle','nexflowEditTask');
        } else {
            $p->set_var('editmodestyle','');
        }
        
        $A['steptype']=strtolower($A['steptype']); //remove any upper case issues
        if ($A['steptype'] == 'manual web' OR $A['steptype'] == 'interactive function' OR $A['steptype'] == 'nexform') {
            $p->set_var ('show_assigned','');
        }
        if ($A['steptype'] == 'batch function' OR $A['steptype'] == 'interactive function') {
            $p->set_var ('task_handler',$A['function']);
        } elseif ($A['steptype'] == 'nexform') {
            if($A['isDynamicForm']==1){
                $dynSQL="SELECT variableName from {$_TABLES['nftemplatevariables']} where id='{$A['dynamicFormVariableID']}'";
                $dynRes=DB_query($dynSQL);
                list($dynVar)=DB_fetchArray($dynRes);
                
                
                $p->set_var ('task_handler','<span class="pluginTinyText" style="color:red">[&nbsp;Dynamic Form Using: ' .$dynVar.' variable&nbsp;]</span>');
                
            }else{
                $formname = DB_getItem($_TABLES['formDefinitions'],'name',"id='{$A['formid']}'");
                $p->set_var ('task_handler',"[form:{$A['formid']}] - $formname");
            }
            
            
            
        } elseif ($A['steptype'] == 'if') {
            $p->set_var ('LANG_Handler','Condition');
            if ($A['argumentVariable'] > 0) {
                $variableName = DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id='{$A['argumentVariable']}'");
                $operator = DB_getItem($_TABLES['nfifoperators'], 'operator', "id='{$A['operator']}'");
                $if_task_condition = "{$variableName} {$operator} {$A['ifValue']}";
            } else {
                $if_task_condition = DB_getItem($_TABLES['nfifprocessarguments'],'label',"id='{$A['argumentProcess']}'");
            } 
            $p->set_var ('task_handler',$if_task_condition);
        } elseif ($A['steptype'] == 'or' OR $A['steptype'] == 'and') {
            $p->set_var ('show_taskhandler','none');
        } else {
            $p->set_var ('task_handler',$A['handler']); 
        }
        if ($A['regenerate'] == 1) {
            $p->set_var('regen_flag','<span class="pluginTinyText" style="color:red">[&nbsp;Regenerate&nbsp;]</span>');
        } else {
            $p->set_var('regen_flag','');
        }
        if ($A['firstTask'] == 1) {
            $p->set_var('first_step','<span class="pluginTinyText" style="color:red">[&nbsp;First Step&nbsp;]</span>');
        } else {
            $p->set_var('first_step','');
        }
        if ($A['steptype'] == 'if') {
            $p->set_var ('css_option','1');
        } elseif ($A['steptype'] == 'manual web') {
            $p->set_var ('css_option','3');
        } elseif ($A['steptype'] == 'nexform' OR $A['steptype'] == 'interactive function') {
            $p->set_var ('css_option','3');
        } else {
            $p->set_var ('css_option','4');
        }

        $sql  = "SELECT b.* FROM {$_TABLES['nftemplateassignment']}  a, {$_TABLES['users']} b ";
        $sql .= "WHERE a.uid=b.uid AND a.nf_templateDataId='{$A['id']}'";

        $assignedUsers = DB_query($sql );
        $numusers = DB_numRows($assignedUsers );
        if ($A['assignedByVariable'] == 0 AND $numusers > 0 ) {
            $names = array();
            for($userCntr = 0;$userCntr < $numusers;$userCntr++ ) {
                $rec = DB_fetchArray($assignedUsers);
                $names[] = $rec['fullname'];
            } 
            
            $task_assigned = 'User: '. implode(',',$names);              
        } else {
            $variables = array();             
            if ($A['assignedByVariable'] == 1) {
                $asql  = "SELECT variableName FROM {$_TABLES['nftemplateassignment']} a ";
                $asql .= "INNER JOIN {$_TABLES['nftemplatevariables']} b ON a.nf_processVariable=b.id ";
                $asql .= "WHERE a.nf_templateDataID='{$A['id']}' ";
                $aquery = DB_query($asql);
                while (list ($assignmentVariableName) = DB_fetchArray($aquery)) {
                    $variables[] = $assignmentVariableName;
                }
            }
            if (count($variables) > 0) {
                $task_assigned = 'Variable: '. implode(',',$variables);
            } else {
                $task_assigned = "Nobody Assigned...";
            }
        } 
        $p->set_var ('task_assigned',$task_assigned);

        $csql =  "SELECT count(*) FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $csql .= "WHERE a.nf_prenotifyVariable=b.id AND a.nf_templateDataID='{$A['id']}'";
        $taskOwnerNotification = DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($A['id'],999));
        list($count) = DB_fetchArray(DB_query($csql));
        if ($count > 0 OR $taskOwnerNotification == 1) {
            $p->set_var ('notify1', $notify1_image);
        } else {
            $p->set_var ('notify1','');
        }

        $csql =  "SELECT count(*) FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $csql .= "WHERE a.nf_postnotifyVariable=b.id AND a.nf_templateDataID='{$A['id']}'";
        $taskOwnerNotification = DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_postnotifyVariable'),array($A['id'],999));         
        list($count) = DB_fetchArray(DB_query($csql));
        if ($count > 0 OR $taskOwnerNotification == 1) {
            $p->set_var ('notify2', $notify2_image);
        } else {
            $p->set_var ('notify2','');
        }

        $csql =  "SELECT count(*) FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $csql .= "WHERE a.nf_remindernotifyVariable=b.id AND a.nf_templateDataID='{$A['id']}'";
        $taskOwnerNotification = DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($A['id'],999));         
        list($count) = DB_fetchArray(DB_query($csql));
        if ($count > 0 OR $taskOwnerNotification == 1) {
            $p->set_var ('reminder', $reminder_image);
        } else {
            $p->set_var ('reminder','');
        }

        $sql = "SELECT * FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom='{$A['id']}' ";
        $query_nextTasks = DB_query($sql);
        $numTasks = DB_numRows($query_nextTasks );

        $next_task = '';
        if (strtolower($A['steptype'] ) != 'if' ) {
            if ($numTasks > 0 ) {
                for($userCntr = 0;$userCntr < $numTasks; $userCntr++ ) {
                    $C = DB_fetchArray($query_nextTasks );
                    $next_task .= 'Task ID:' . nfidtolid($C['nf_templateDataTo'] ) . '<BR>';
                } 
            } else {
                $next_task =  'End of workflow...';
            }
        } else { // an if task.. display the next steps a bit differently
            $taskTrue = "";
            $taskFalse = "";
            if ($numTasks > 0 ) {
                for($userCntr = 0;$userCntr < $numTasks;$userCntr++ ) {
                    $C = DB_fetchArray($query_nextTasks );
                    if ($C['nf_templateDataTo'] != null or $C['nf_templateDataTo'] != 0 ) {
                        $taskTrue .= "Task ID: " . nfidtolid($C['nf_templateDataTo']) . '<BR>';
                    } 
                    if ($C['nf_templateDataToFalse'] != null or $C['nf_templateDataToFalse'] != 0 ) {
                        $taskFalse .= "Task ID: " . nfidtolid($C['nf_templateDataToFalse']) . '<BR>';
                    } 
                } 
                $next_task  = '<table class="pluginSubTable" border="0" cellpadding="0" cellspacing="0" style="border:0px"><tr><th>When True</th><th style="padding-left:30px;">When False</th></tr><tr>';
                $next_task .= "<td style='border:0px;'>{$taskTrue}</td><td style='padding-left:30px;border:0px;'>{$taskFalse}</td></tr></table>";
            } else {
                $next_task = "End of workflow...";
            } 
        }

        $p->set_var ('next_task',$next_task);
        $thisTemplate = $A['nf_templateID'];

        $p->parse('template_task_records','records',true);
    
    }       // End of Left Handside task detail listing
        

    /* Format the right hand side of the display  - Edit Task Details */
    if ($taskID != null || $taskID != 0 || $taskID != '' ) {
        $taskRec = DB_fetchArray (DB_query("SELECT * FROM {$_TABLES['nftemplatedata']} WHERE id='$taskID'"));
        
        $variableOptions=nf_makeDropDownWithSelected("id", "variableName", $_TABLES['nftemplatevariables'], $taskRec['dynamicTaskNameVariableID'],"WHERE nf_templateID={$templateID}",1);
        $p->set_var ('available_taskvariablesOptions',$variableOptions);
        
        /* Is this an interactive Task */
        if ($taskRec['nf_stepType'] == 1 OR $taskRec['nf_stepType'] >= 7) {
            $p->set_var ('show_taskoptions','');
            $p->set_var ('show_emailoptions','');
        }
        $logical_taskid = $taskRec['logicalID'];
        if ($logical_taskid != null && $logical_taskid != 0 ) {
            $p->set_var ('logical_task_id', $logical_taskid);
        } else {
            // this routine will get the next sequential logical id.
            $logical_taskid = getnextlid($templateID );
            $p->set_var ('logical_task_id', $logical_taskid);
        }

        $p->set_var ('edit_task_name',htmlspecialchars(stripslashes($taskRec['taskname'])));
        $p->set_var('steptype_options',COM_optionList($_TABLES['nfsteptype'],'id,stepType',$taskRec['nf_stepType'],0));
        $p->set_var('form_options', COM_optionList($_TABLES['formDefinitions'],'id,name'));
        $p->set_var ('optional_parm',DB_getItem($_TABLES['nftemplatedata'], 'optionalParm', "id='{$taskID}'"));

        if ($taskRec['isDynamicTaskName'] == 1) {
            $p->set_var ('chk_isDynamicName','CHECKED=CHECKED');
            $p->set_var ('show_dynamicnamevars','');    
        } else {
            $p->set_var ('chk_isDynamicName','');
            $p->set_var ('show_dynamicnamevars','display:none;');
        }    
        
        if ($taskRec['regenerate'] == 1) {
            $p->set_var ('chk_regenerate','CHECKED=CHECKED');
        } else {
            $p->set_var ('chk_regenerate','');
        }
        
        if ($taskRec['regenAllLiveTasks'] == 1) {
            $p->set_var ('chk_regenerateAllLive','CHECKED=CHECKED');
        } else {
            $p->set_var ('chk_regenerateAllLive','');
        }
        
        if ($taskRec['isDynamicForm'] == 1) {
            $p->set_var ('chk_isDynamicForm','CHECKED=CHECKED');
            $p->set_var ('show_dynamicformcb','');
            $p->set_var ('show_dynamicformvars','');
            $p->set_var ('show_form','none');
        } else {
            $p->set_var ('chk_isDynamicForm','');
            $p->set_var ('show_dynamicformvars','display:none');            
        }
        
        if ($taskRec['nf_stepType'] != 8) {
            $p->set_var ('show_dynamicformcb','none');
        }else{
            $p->set_var ('show_dynamicformcb','');
        }
        
        if ($taskRec['dynamicFormVariableID'] != '' && $taskRec['dynamicFormVariableID'] != '0') {
            $variableOptions=nf_makeDropDownWithSelected("id", "variableName", $_TABLES['nftemplatevariables'], $taskRec['dynamicFormVariableID'],"WHERE nf_templateID={$templateID}",1);
            $p->set_var ('available_formvariablesOptions',$variableOptions);
        } 
        
        
        if ($taskRec['assignedByVariable'] == 1) {
            $p->set_var ('chk_byvariable','CHECKED=CHECKED');
            $p->set_var ('chk_byuser','');
            $p->set_var ('show_taskoption2','');
        } else {         
            $p->set_var ('chk_byvariable','');
            $p->set_var ('chk_byuser','CHECKED=CHECKED'); 
            $p->set_var ('show_taskoption1','');
        }
                
        if ($taskRec['nf_stepType'] == 5) {
            $p->set_var ('show_if',''); 
        } elseif ($taskRec['nf_stepType'] == 6 or $taskRec['nf_stepType'] == 7) {
            $p->set_var('show_function','');
            $task_function = DB_getItem($_TABLES['nftemplatedata'], 'function', "id='{$taskID}'");
            $p->set_var ('task_function',$task_function); 
        } elseif ($taskRec['nf_stepType'] == 8)  {
            $task_formid = DB_getItem($_TABLES['nftemplatedata'], 'formid', "id=" . $taskID );
            if($taskRec['isDynamicForm'] == 0){
                $p->set_var('show_form','');
            }else{
                $p->set_var('show_form','none');
            }
            $formOptions=nf_makeDropDownWithSelected("id", "name", $_TABLES['formDefinitions'], $task_formid,'',1);
            $p->set_var ('form_options',$formOptions);

        } elseif ($taskRec['nf_stepType'] == 1 OR $taskRec['nf_stepType'] == 4) {
            $p->set_var('show_handler','');
        }

        $p->set_var ('task_handler_selection', nf_makeDropDownWithSelected("id", "handler", $_TABLES['nfhandlers'],$taskRec['nf_handlerId']) );
        
        $p->set_var ('pre_notify_message', $taskRec['prenotify_message']);
        $p->set_var ('post_notify_message', $taskRec['postnotify_message']);         
        $p->set_var ('reminder_message', $taskRec['reminder_message']);
        $p->set_var ('message_help',$LANG_NF_MESSAGE ['msg1']);
        
        $userOptions = COM_optionList($_TABLES['users'],'uid,fullname','',1,"fullname <> ''");
        $p->set_var ('available_userOptions', $userOptions);

        $variableOptions = COM_optionList($_TABLES['nftemplatevariables'],'id,variableName','',1,"nf_templateID='{$templateID}'");
        $p->set_var ('available_variablesOptions',$variableOptions);

        // Set task assigned users dropdown list options
        $sql  = "SELECT b.uid, b.fullname FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['users']} b ";
        $sql .= "WHERE a.uid=b.uid AND a.nf_templateDataID='{$taskID}' ORDER BY b.fullname";
        $q = DB_query($sql);
        $options = '';
        while (list($id, $label) = DB_fetchArray($q)) {
            $options .= "<option value=\"$id\">$label</option>";
        }
        $p->set_var('assigned_usersOptions',$options);

        // Set task assigned variables dropdown list options
        $sql =  "SELECT b.id, b.variableName FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $sql .= "WHERE a.nf_processVariable=b.id AND a.nf_templateDataID='{$taskID}'";
        $q = DB_query($sql);
        $options = '';
        while (list($id, $label) = DB_fetchArray($q)) {
            $options .= "<option value=\"$id\">$label</option>";
        }
        $p->set_var('assigned_variableOptions',$options);
        
        // Set task pre-notify variables dropdown list options
        $sql =  "SELECT b.id, b.variableName FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $sql .= "WHERE a.nf_prenotifyVariable=b.id AND a.nf_templateDataID='{$taskID}'";
        $q = DB_query($sql);
        $options = '';
        if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($taskID,999)) == 1) {  
           $options = "<option value=\"999\">TASK_OWNER</option>";
        }         
        while (list($id, $label) = DB_fetchArray($q)) {
            $options .= "<option value=\"$id\">$label</option>";
        }
        $p->set_var('assigned_preNotifyVariables',$options);
        
        // Set task pre-notify variables dropdown list options
        $sql =  "SELECT b.id, b.variableName FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $sql .= "WHERE a.nf_postnotifyVariable=b.id AND a.nf_templateDataID='{$taskID}'";
        $q = DB_query($sql);
        $options = '';
        if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_postnotifyVariable'),array($taskID,999)) == 1) {  
           $options = "<option value=\"999\">TASK_OWNER</option>";
        }
        while (list($id, $label) = DB_fetchArray($q)) {
            $options .= "<option value=\"$id\">$label</option>";
        }
        $p->set_var('assigned_postNotifyVariables',$options); 

        // Set task reminder notify variables dropdown list options
        $sql =  "SELECT b.id, b.variableName FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";
        $sql .= "WHERE a.nf_remindernotifyVariable=b.id AND a.nf_templateDataID='{$taskID}'";
        $q = DB_query($sql);
        $options = '';
        if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($taskID,999)) == 1) {  
           $options = "<option value=\"999\">TASK_OWNER</option>";
        }                 
        while (list($id, $label) = DB_fetchArray($q)) {
            $options .= "<option value=\"$id\">$label</option>";
        }
        $p->set_var('assigned_reminderNotifyVariables',$options);
      
        // Set Next-tasks field
        $next_tasks = '';
        if ($taskID != null ) {
            $sql = "Select nf_templateDataTo FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom={$taskID} ";
            $sql .= "ORDER BY nf_templateDataTo";
            $tempvar = DB_query($sql);
            $numTasks = DB_numRows($tempvar );
            if ($numTasks > 0 ) {
                for($tasksCntr = 0;$tasksCntr < $numTasks;$tasksCntr++ ) {
                    if ($tasksCntr > 0 AND $next_tasks != '') {
                        $next_tasks .= ',';
                    } 
                    $X = DB_fetchArray($tempvar );
                    $next_tasks .= nfidtolid($X[0] );
                } 
            } 
        }
        $p->set_var ('next_tasks',$next_tasks);

        $options = '';
        for ($i = 0; $i <=31; $i++ ) {
            if ($taskRec['reminderInterval'] == $i) {
                $options .= "<option value=\"$i\" SELECTED>$i</option>";
            } else {
                $options .= "<option value=\"$i\">$i</option>";
            }
        }
        $p->set_var ('notifyIntervalOptions',$options);
        
        $options = '';
        for ($i = 0; $i <=31; $i++ ) {
            if ($taskRec['subsequentReminderInterval'] == $i) {
                $options .= "<option value=\"$i\" SELECTED>$i</option>";
            } else {
                $options .= "<option value=\"$i\">$i</option>";
            }
        }        
        
        $p->set_var ('subsequentIntervalOptions',$options);          

        $sql = "SELECT * FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID = '{$taskRec['nf_templateID']}';";
        $result = DB_query($sql);
        $numrows = DB_numRows($result);
        $options = "<option value=\"0\">No Escalation</option>\n";
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray($result);
            if ($taskRec['escalateVariableID'] == $A['id']) {
                $options .= "<option value=\"{$A['id']}\" selected>{$A['variableName']}</option>\n";
            }
            else {
                $options .= "<option value=\"{$A['id']}\">{$A['variableName']}</option>\n";
            }
        }
        $p->set_var('esc_user_options', $options);
        $p->set_var('numReminders', $taskRec['numReminders']);

        // Set task IF Options if required
        if ($taskRec['argumentVariable'] > 0) {
            
            $sql = "{$_TABLES['nftemplatevariables']} ifTask ";
            $p->set_var('if_task_variables', nf_makeDropDownWithSelected("ifTask.id", "ifTask.variableName", $sql ,$taskRec['argumentVariable']," WHERE nf_templateID='$templateID'") );
        } else {
            $sql = "{$_TABLES['nftemplatevariables']} ifTask WHERE ifTask.nf_templateID='{$templateID}'";
            $p->set_var('if_task_variables', nf_makeDropDownSql("ifTask.id", "ifTask.variableName", $sql, 1 ) );
        } 

        if ($taskRec['argumentProcess'] > 0) {
            $sql = "{$_TABLES['nfifprocessarguments']} ifTask ";
            $p->set_var('if_task_option', nf_makeDropDownWithSelected("ifTask.id", "ifTask.label", $sql, $taskRec['argumentProcess']) );
        } else {
            $sql = "{$_TABLES['nfifprocessarguments']} ifTask ";
            $p->set_var('if_task_option', nf_makeDropDownSql("ifTask.id", "ifTask.label", $sql , 1 ) );
        }

        if ($taskRec['operator'] > 0 ) { 
            $sql = "{$_TABLES['nfifoperators']} ifTask ";
            $p->set_var('if_task_operator', nf_makeDropDownWithSelected("ifTask.id", "ifTask.operator", $sql, $taskRec['operator']) );
        } else {
            $sql = "{$_TABLES['nfifoperators']} ifTask ";
            $p->set_var('if_task_operator', nf_makeDropDownSql("ifTask.id", "ifTask.operator", $sql, 1) );
        } 

        $p->set_var('if_option_value', DB_getItem($_TABLES['nftemplatedata'], 'ifValue', "id='{$taskID}'") );

        $task_true_value = '';
        if ($taskID != null ) {
            $sql = "Select nf_templateDataTo FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom={$taskID} ";
            $sql .= "ORDER BY nf_templateDataTo ";
            $tempvar = DB_query($sql);
            $numTasks = DB_numRows($tempvar );
            if ($numTasks > 0 ) {
                for($tasksCntr = 0;$tasksCntr < $numTasks;$tasksCntr++ ) {
                    if ($tasksCntr > 0 ) {
                        if ($X[0] != 0 ) {
                            $task_true_value .= ',';
                        } 
                    } 
                    $X = DB_fetchArray($tempvar );
                    if ($X[0] != 0 ) {
                        $task_true_value .= nfidtolid($X[0] );
                    } 
                } 
            } 
        }
        $p->set_var('if_tasktrue_value',$task_true_value);

        $task_false_value = '';
        if ($taskID != null ) {
            $sql = "Select nf_templateDataToFalse FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom={$taskID} ";
            $sql .= "ORDER BY nf_templateDataToFalse ";

            $tempvar = DB_query($sql);
            $numTasks = DB_numRows($tempvar );
            if ($numTasks > 0 ) {
                for($tasksCntr = 0;$tasksCntr < $numTasks;$tasksCntr++ ) {
                    if ($tasksCntr > 0 ) {
                        if ($X[0] != 0 ) {
                            $task_false_value .= ',';
                        } 
                    } 
                    $X = DB_fetchArray($tempvar );
                    if ($X[0] != 0 ) {
                        $task_false_value .= nfidtolid($X[0]);
                    } 
                } 
            } 
        } 
        $p->set_var('if_taskfalse_value',$task_false_value);

    } else {
        $logical_taskid = getnextlid($templateID );
        $p->set_var ('logical_task_id', $logical_taskid);
                
        $p->set_var ('steptype_options',COM_optionList($_TABLES['nfsteptype'],'id,stepType','',0));
        $p->set_var('form_options', COM_optionList($_TABLES['formDefinitions'],'id,name'));
        $p->set_var ('task_handler_selection', nf_makeDropDown("id", "handler", $_TABLES['nfhandlers'] ) );
        $p->set_var('next_tasks','');

        $userOptions = COM_optionList($_TABLES['users'],'uid,fullname','',1,"fullname <> '' AND uid > 1");
        $p->set_var ('available_userOptions', $userOptions);
        $variableOptions=nf_makeDropDownWithSelected("id", "variableName", $_TABLES['nftemplatevariables'], $taskRec['dynamicFormVariableID'],'',1);
        $p->set_var ('available_variablesOptions',$variableOptions);         
        
        $sql = "{$_TABLES['nftemplatevariables']} ifTask WHERE ifTask.nf_templateID='{$templateID}'";
        $p->set_var('if_task_variables', nf_makeDropDownSql("ifTask.id", "ifTask.variableName", $sql, 1 ) );
        $sql = "{$_TABLES['nfifprocessarguments']} ifTask ";
        $p->set_var('if_task_option', nf_makeDropDownSql("ifTask.id", "ifTask.label", $sql , 1 ) );
        $sql = "{$_TABLES['nfifoperators']} ifTask ";
        $p->set_var('if_task_operator', nf_makeDropDownSql("ifTask.id", "ifTask.operator", $sql, 1) ); 
        
        $options = '';
        for ($i = 1; $i <=31; $i++ ) {
            $options .= "<option value=\"$i\">$i</option>";
        }
        $p->set_var ('notifyIntervalOptions',$options);
        
        $p->set_var ('notifyInterval2Options',$options);        
    }

    $p->parse ('output', 'page');
    echo $p->finish ($p->get_var('output'));

} else {
    echo "You must choose a template to edit first...<BR><BR><BR>";
} 

$retval .= COM_siteFooter (false );
echo $retval;

?>