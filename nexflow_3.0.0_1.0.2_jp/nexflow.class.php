<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexflow.class.php                                                         |
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

class nexflow {
    // query-able variables that allow for per instance querying and setting..
    var $_nfProcessId               = null;     //process id
    var $_nfTemplateId              = null;     //template id
    var $_nfQueueId                 = null;     //queue id.  this is either null, a single item or a semi colon delimited list
    var $_nfUserId                  = null;     //current User Id
    var $_nfGroupId                 = null;     //current Group Id
    var $_debug                     = false;    //set the current debug level to false.
    var $_mode                      = '';       //mode for the calls.. user or process centric
    var $_nfuserTaskList = array(
            'id'        => array(),
            'url'       => array(),
            'taskname'  => array(),
            'template'  => array() );            //getQueue will set this tree array
    var $_nfprocessTaskList = array(
            'id'        => array(),
            'url'       => array(),
            'taskname'  => array() );            //getQueue will set this tree array
    var $_nfTemplateList = array(
            'templateid'=> array(),
            'taskid'    => array(),
            'template'  => array(),
            'handler'   => array() );             // array to hold the available templates for the user to execute
    var $_nfUserTaskCount           = 0;        // number of tasks the user has in the queue
    var $_nfTemplateCount           = 0;        // number of templates the user is able to kick off
    var $_nfProcessTaskCount        = 0;        // number of tasks the current process has in the queue
    var $_currentQueueID            = 0;        // Private variable of current queue task id



    // **constructor
    // takes parameters:
    // debug  - boolean.. set to true or false
    // processid - int.. bind to a process instance.. null for new
    function nexflow($processID = null, $userId = 0 )
    {
        $_nfUserTaskCount = 0;
        $_nfProcessTaskCount = 0;
        $this->_nfUserId = $userId;
        $this->_nfProcessId = $processID;
    }


    // getQueue function relies on the fact that there is a processID already set for this
    // instance of the class
    // if there is a userID set already, we know that the flow is looking for a queue item
    // assigned to a userID


    // Set class current task id
    function set_currentTaskid($taskid) {
        $this->_currentQueueID = $taskid;
    }

    function getQueue()
    {
        global $_TABLES;
        if ($this->_nfUserId != null || $this->_nfUserId != 0) { // instance where the user id is known.  need to see if there is a processID given.
            // this means that the mode in which we're working is user based.. we only care about a user in this case
            $this->_mode = 'user';
            if ($this->_debug ) {
                COM_errorLog("Entering getQueue - user mode");
            }
            $this->_nfuserTaskList["id"] = Array();
            $this->_nfuserTaskList["url"] = Array();
            $this->_nfuserTaskList["taskname"] = Array();
            $this->_nfuserTaskList["stepType"] = Array();
            $this->_nfUserTaskCount = 0;
            $this->_nfUserId=NXCOM_filterInt($this->_nfUserId);
            $sql  = "SELECT a.id, b.nf_stepType, b.function, b.formid, b.nf_handlerid, b.nf_templateID, b.taskname, ";
            $sql .= "b.isDynamicForm, b.dynamicFormVariableID, a.nf_processID, b.isDynamicTaskName, b.dynamicTaskNameVariableID ";
            $sql .= "FROM {$_TABLES['nfqueue']} a ";
            $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} b ON a.nf_templateDataId = b.id ";
            $sql .= "LEFT JOIN {$_TABLES['nfproductionassignments']} c ON a.id = c.task_id WHERE c.uid = {$this->_nfUserId} ";
            $sql .= "AND ( a.archived =0 OR a.archived IS NULL )";
            $result = DB_query($sql );
            $nrows = DB_numRows($result );

            switch ($nrows ) {
                case 0:
                    if ($this->_debug ) {
                        COM_errorLog("getQueue - 0 rows returned.  Nothing in queue for this user.");
                    }
                    break;
                default: // one or more than one queue item assigned.
                    // this is going to return a semi-colon delimited list of queue id's for that user.
                    $temparray = Array();
                    for($i = 0; $i < $nrows; $i++ ) {
                        $A = DB_fetchArray($result );
                        if ($this->_nfQueueId == '' ) {
                            $this->_nfQueueId = $A['id'];
                        } else {
                            $this->_nfQueueId .= ";" . $A['id'];
                        }
                        $flag = 0;
                        // simple test to determine if the task ID already exists for this user
                        for($flagcntr = 0;$flagcntr <= $this->_nfUserTaskCount;$flagcntr++ ) {
                            if ($this->_nfuserTaskList["id"][$flagcntr] == $A['id'] ) {
                                $flag = 1;
                            }
                        }
                        if ($flag == 0 ) {
                            $temparray = array(1 => $A['id'] );
                            if ($A['nf_stepType'] == 6 OR $A['nf_stepType'] == 7 )  { // Batch Function or Internactive Function
                                $handler = $A['function'];
                            } elseif ($A['nf_stepType'] == 8 )  {   // nexform Task
                                //in here we have to change the handler to take into account that this could be
                                //a dynamically assigned form
                                if($A['isDynamicForm'] == 1){
                                    $sql  = "SELECT variableValue FROM {$_TABLES['nfprocessvariables']} ";
                                    $sql .= "WHERE nf_processid='{$A['nf_processID']}' and nf_templateVariableID='{$A['dynamicFormVariableID']}'";
                                    $res = DB_query($sql);
                                    list($handler) = DB_fetchArray($res);
                                }else{
                                    $handler = $A['formid'];
                                }
                            } else {
                                $handler = DB_getItem($_TABLES['nfhandlers'],'handler',"id='{$A['nf_handlerid']}'");
                            }

                            $this->_nfuserTaskList["id"] = array_merge($this->_nfuserTaskList["id"], array(1 => $A['id']));
                            $this->_nfuserTaskList["template"] = array_merge($this->_nfuserTaskList["template"], array(1 => $A['nf_templateID']));
                            $this->_nfuserTaskList["url"] = array_merge($this->_nfuserTaskList["url"], array(1 => $handler));
                            //handle dynamic task name based on a variable's value
                            if($A['isDynamicTaskName'] == 1){
                                $sql  = "SELECT variableValue FROM {$_TABLES['nfprocessvariables']} ";
                                $sql .= "WHERE nf_processid='{$A['nf_processID']}' AND nf_templateVariableID='{$A['dynamicTaskNameVariableID']}'";
                                $res = DB_query($sql);
                                list($dynamicTaskName)=DB_fetchArray($res);
                                $this->_nfuserTaskList["taskname"] = array_merge($this->_nfuserTaskList["taskname"], array(1 => $dynamicTaskName));
                            }else{
                                $this->_nfuserTaskList["taskname"] = array_merge($this->_nfuserTaskList["taskname"], array(1 => $A['taskname']));
                            }
                            $this->_nfuserTaskList['stepType'] = array_merge($this->_nfuserTaskList['stepType'], array(1 => $A['nf_stepType']));
                            $this->_nfUserTaskCount += 1; //increment the total user taks counter
                        }
                    } //end for
                    break;
            }
        } else { // userID is null
            if ($this->_nfProcessId != null ) { // userid is null and processid is not null
                // in this instance we have no userId but do have a processid.
                // thus we are in the process mode
                $this->_mode = 'process';
                if ($this->_debug ) {
                    COM_errorLog("Entering getQueue - process mode");
                }
                $this->_nfprocessTaskList["id"] = Array();
                $this->_nfprocessTaskList["url"] = Array();
                $this->_nfprocessTaskList["taskname"] = Array();
                $this->_nfprocessTaskList["stepType"] = Array();
                $this->_nfProcessTaskCount = 0;
                $this->_nfProcessId=NXCOM_filterInt($this->_nfProcessId);

                $sql  = "SELECT a.id,a.status,c.nf_templateID,c.taskname,c.nf_stepType,c.nf_handlerid,c.function, ";
                $sql .= "e.templateName, f.uid, f.gid FROM {$_TABLES['nfqueue']} a, {$_TABLES['nfprocess']} b, ";
                $sql .= "{$_TABLES['nftemplatedata']} c, {$_TABLES['nftemplate']} e, {$_TABLES['nftemplateassignment']} f ";
                $sql .= "WHERE  a.nf_processId = b.id ";
                $sql .= "AND a.nf_templateDataId = c.id ";
                $sql .= "AND b.nf_templateId = e.id ";
                $sql .= "AND f.nf_templateDataid = c.id ";
                $sql .= "AND a.archived <> 1 ";
                $sql .= "AND b.id = '{$this->_nfProcessId}' ";
                $sql .= "AND (a.status=0 or a.status is null) ";

                $result = DB_query($sql );
                $nrows = DB_numRows($result );
                switch ($nrows ) {
                    case 0:
                        if ($this->_debug ) {
                            COM_errorLog("getQueue - 0 rows returned.  Nothing in queue.");
                        }
                        break;
                    default: // one or more than one queue item assigned.
                        // this is going to return a semi-colon delimited list of queue id's for that process.
                        $temparray = Array();
                        for($i = 0; $i < $nrows; $i++ ) {
                            $A = DB_fetchArray($result );
                            if ($this->_nfQueueId == '' ) {
                                $this->_nfQueueId = $A['id'];
                            } else {
                                $this->_nfQueueId .= ";" . $A['id'];
                            }

                            unset($temparray );
                            $temparray = array(1 => $A['id'] );
                            $this->_nfprocessTaskList["id"] = array_merge($this->_nfprocessTaskList["id"], $temparray );

                            unset($temparray );
                            if ($A['nf_stepType'] == 6 OR $A['nf_stepType'] == 7 )  { // Batch Function or Internactive Function
                                $handler = $A['function'];
                            } elseif ($A['nf_stepType'] == 8 )  {   // nexform Task
                                $handler = $A['formid'];
                            } else {
                                //we're in the case where there ARE rows from the query, thus $A['nf_handerlid'] has data. no need to filter
                                $handler = DB_getItem($_TABLES['nfhandlers'],'handler',"id='{$A['nf_handlerid']}'");
                            }
                            $temparray = array(1 => $handler);
                            $this->_nfprocessTaskList["url"] = array_merge($this->_nfprocessTaskList["url"], $temparray );

                            unset($temparray );
                            $temparray = array(1 => $A['taskname'] );
                            $this->_nfprocessTaskList["taskname"] = array_merge($this->_nfprocessTaskList["taskname"], $temparray );
                            $this->_nfuserTaskList['stepType'] = array_merge($this->_nfuserTaskList['stepType'], array(1 => $A['nf_stepType']));
                            $this->_nfProcessTaskCount += 1; //increment the total user taks counter
                        }
                        break;
                }
                // we now have, for a serial flow, a single row item that holds the flow's current queue item.
            } else { // nothing is set here.
                // no processid and no userid.. thus nothing to set the queue to.
                $this->_nfQueueId = null;
                $this->_nfProcessId = null;
            }
        }

        if ($this->_debug ) {
            COM_errorLog("Exiting getQueue");
        }
    }


    // we will generate a whole new processID with this function.
    // requirement is which template to use... the template argument is mandatory
    // returns a processid
    // $startoffset allows for the regeneration idea and PID idea
    function newprocess($template, $startoffset = null, $pid = null , $appGroupAssociation=0)
    {
        global $_TABLES,$_USER;
        $template = NXCOM_filterInt($template);
        // this sql statement will retrieve the first step of the process and kick it off
        if ($startoffset == null ) {
            $sql = "SELECT a.nf_templateDataFrom, b.regenAllLiveTasks, c.useProject, c.templateName FROM {$_TABLES["nftemplatedatanextstep"]} a ";
            $sql .= "inner join {$_TABLES["nftemplatedata"]} b on a.nf_templateDataFrom = b.id ";
            $sql .= "inner join {$_TABLES["nftemplate"]} c on b.nf_templateid = c.id ";
            $sql .= "left outer join {$_TABLES["nftemplateassignment"]} d on d.nf_templateDataID = b.id ";
            $sql .= "left outer join {$_TABLES["nfhandlers"]} e on e.id = b.nf_handlerid ";
            $sql .= "WHERE b.firstTask = 1 AND c.id ='$template' ORDER BY nf_templateDataFrom ASC LIMIT 1 ";
        } else {
            $startoffset=NXCOM_filterInt($startoffset);
            $sql = "SELECT a.id, a.regenAllLiveTasks FROM {$_TABLES['nftemplatedata']} a where a.id='$startoffset'";
        }
        if ($this->_debug ) {
            COM_errorLog("New Process Code");
        }

        $result = DB_query($sql );
        $nrows = DB_numRows($result );
        $A = DB_fetchArray($result );
        $templateDataID = $A[0];
        $templateName = $A['templateName'];
        $regenAllLiveTasks = $A['regenAllLiveTasks'];
        if ($templateDataID != null or $templateDataID != '' ) {
            $thisDate = date('Y-m-d H:i:s' );
            $pid = NXCOM_filterInt($pid);
            $sql = "INSERT INTO {$_TABLES['nfprocess']} (nf_templateID , complete, pid,initiatedDate ) ";
            $sql .= "VALUES ('{$template}', 0, $pid, '{$thisDate}')";
            $result = DB_query($sql );
            $insertID = DB_insertID();   // New record id is the processid
            $thisDate = date('Y-m-d H:i:s' );
            $sql = "INSERT INTO {$_TABLES['nfqueue']} (nf_processID,nf_templateDataID,status,archived,createdDate) ";
            $sql .= "VALUES ('$insertID','$templateDataID',0,0,'$thisDate')";
            DB_query($sql );
            $newTaskid = DB_insertID();

            // Determine if task has a reminder set and if so then update the nextReminderTime field in the new queue record
            $reminderInterval = DB_getItem($_TABLES['nftemplatedata'],'reminderInterval',"id='{$templateDataID}'");
            if ($reminderInterval > 0) {
                DB_query("UPDATE {$_TABLES['nfqueue']} SET nextReminderTime=DATE_ADD( NOW(), INTERVAL $reminderInterval DAY) where id='$newTaskid'");
            }

            // Check if notification has been defined for new task assignment
            $this->private_sendTaskAssignmentNotifications();

            // now determine if the offset is set.. if so, pack the original pid pointer with a status of 2
            if ($startoffset != null && $pid != null ) {
                $sql = "UPDATE {$_TABLES['nfprocess']} SET complete=2, completedDate=NOW() where id='$pid'";
                $result = DB_query($sql );

                //Within this section we need to detect whether or not the startoffset task has the "regenerate all live tasks" option set.
                //if so, the process we just layed to rest will hold some in-production tasks.  those tasks will have their pids set to the new pid.
                if($regenAllLiveTasks == '1'){
                    $sql  = "SELECT a.id FROM {$_TABLES['nfqueue']} a LEFT JOIN {$_TABLES['nftemplatedata']} b ";
                    $sql .= "ON a.nf_templateDataID=b.id WHERE b.nf_stepType=2 AND a.nf_processID='$pid' ";
                    $sql .= "AND (a.archived=0 OR a.archived IS NULL);";
                    $res = DB_query($sql);
                    while ($A = DB_fetchArray($res)) {
                        $sql = "SELECT fromQueueID FROM {$_TABLES['nfqueuefrom']} WHERE queueID={$A['id']};";
                        $res2 = DB_query($sql);
                        while ($B = DB_fetchArray($res2)) {
                            $sql="UPDATE {$_TABLES['nfqueue']} SET nf_processID='$insertID' WHERE id={$B['fromQueueID']};";
                            $result = DB_query($sql);
                        }
                        $sql = "UPDATE {$_TABLES['nfqueue']} SET nf_processID='$insertID' WHERE id={$A['id']} AND (archived = 0 OR archived IS NULL);";
                        $result = DB_query($sql);
                    }
                }

                /* SQL Call albeit more elegant fails on older MySQL 3.23 releases
                   $sql  = "INSERT INTO {$_TABLES['nfprocessvariables']} (nf_processID,nf_templateVariableID, variableValue) ";
                   $sql .= "SELECT $insertID ,nf_templateVariableID, variableValue FROM {$_TABLES['nfprocessvariables']} b WHERE b.nf_processID='$pid'";
                   DB_query($sql );
                */

                /* Alternative SQL approach using Loop to copy the running process variables and values */
                $sql = "SELECT nf_templateVariableID, variableValue FROM {$_TABLES['nfprocessvariables']} b WHERE b.nf_processID='$pid'";
                $query = DB_query($sql);
                while ($A = DB_fetchArray($query)) {
                     $sql  = "INSERT INTO {$_TABLES['nfprocessvariables']} (nf_processID,nf_templateVariableID, variableValue) VALUES ";
                     $sql .= "('$insertID','{$A['nf_templateVariableID']}','{$A['variableValue']}')";
                     DB_query($sql);
                }


            } else { // situation where this is the root process
                // inserts the template variables into the process
                $sql = "INSERT INTO {$_TABLES['nfprocessvariables']} (nf_processID,nf_templateVariableID, variableValue) ";
                $sql .= "SELECT $insertID,id, variableValue FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID='$template'";
                $result = DB_query($sql );
            }
            $this->_nfProcessId = $insertID;
            if ($this->_debug ) {
                COM_errorLog("Nexflow: New queue id (1) : $newTaskid - Template Taskid: $templateDataID ");
            }

            // Set the initiator variable here if not already set - via a regenerated process creation
            if ($this->get_processVariable('INITIATOR') == 0) {
                $this->set_ProcessVariable('INITIATOR',$_USER['uid']);
            }
            $newTaskAssignedUsers = $this->private_getAssignedUID($newTaskid);
            if (is_array($newTaskAssignedUsers) AND count($newTaskAssignedUsers) > 0) {
                $this->assign_task($newTaskid,$newTaskAssignedUsers);
            }

            if($appGroupAssociation == 0 ) {
                //have to detect whether this new process needs a more detailed project table association created for it.
                if($A['useProject'] == 1 && $pid == NULL){
                    //this is the condition where there is no parent (totally new process)
                    $sql  = "INSERT INTO {$_TABLES['nfprojects']} (originator_uid,wf_process_id,wf_task_id,status,description) ";
                    $sql .= "VALUES ('{$_USER['uid']}','{$insertID}','{$newTaskid}','0','{$templateName}') ";
                    DB_query($sql);
                    $project_id = DB_insertID();
                    $this->set_ProcessVariable('PID',$project_id);
                    if ($this->_debug ) {
                        COM_errorLog("Nexflow newProcess: Create new project_id: $project_id");
                    }
                } elseif($A['useProject'] == 1 && ($pid != NULL || $pid != 0)) {
                    //this is the condition where there IS a parent AND we want a project table association
                    //we have one different step here - to update the wf process association for the original PID to the new insertID
                    $sql = "UPDATE {$_TABLES['nfprojects']} set wf_process_id='{$insertID}' where wf_process_id='{$pid}'";
                    $res = DB_query($sql);
                    if ($this->_debug ) {
                        COM_errorLog("Nexflow newProcess: Updated project_id: $project_id with new wf_process_id of: $insertID");
                    }
                }
            } else {
                //we have the condition here where we are spawning a new process from an already existing process
                //BUT we are not going to create a new tracking project.  Rather we are going to associate this process with the
                //parent's already established tracking project
                if($pid != NULL || $pid != 0) {
                    //first, pull back the existing nfprojects entry
                    $sql = "SELECT id,related_processes FROM {$_TABLES['nfprojects']} where wf_process_id='{$pid}'";
                    $res = DB_query($sql);
                    list($existingID,$relatedProcesses) = DB_fetchArray($res);
                    if($relatedProcesses != ''){
                        $relatedProcesses .= ",";
                    }
                    $relatedProcesses .= $insertID;
                    $existingID = NXCOM_filterInt($existingID);
                    if($existingID != 0){
                        $sql = "UPDATE {$_TABLES['nfprojects']} set related_processes='{$relatedProcesses}' where id='{$existingID}'";
                        DB_query($sql);
                    }
                }//end if($pid!=NULL || $pid!=0)

            }//end if/else

            return $insertID;
        } else {
            COM_errorLog("New Process Code FAIL! - Template: $template not defined");
        }
    }


    // simply sets the debug parameter.
    function set_debug($debug )
    {
        if ($debug) {
            COM_errorLog("Nexflow Class -> Set debug mode on");
        }
        $this->_debug = $debug;
    }


    // Get a process variable as defined for this template
    // Requires the processID to be set and then pass in a variable's name.
    // if both the process and the name exist, you get a value..
    // otherwise, you get NULL
    function get_processVariable($variable)
    {
        global $_TABLES;
        $retval = null;
        $thisvar = strtolower($variable);
        if($this->_nfProcessId==NULL || $this->_nfProcessId==''){
            if ($this->_debug ) {
                COM_errorLog("get_ProcessVariable: The Process ID has not been set.");
            }
            $retval=NULL;
        }else{
            $sql  = "SELECT a.variableValue FROM {$_TABLES['nfprocessvariables']} a ";
            $sql .= "INNER JOIN {$_TABLES['nftemplatevariables']} b ON a.nf_templateVariableID=b.id ";
            $sql .= "WHERE a.nf_processID='{$this->_nfProcessId}' AND b.variableName='$thisvar'";
            $result = DB_query($sql );
            if (DB_numRows($result ) > 0 ) {
                list ($retval) = DB_fetchArray($result );
                if ($this->_debug ) {
                    COM_errorLog("get_ProcessVariable: $variable -> $retval");
                }
            } else {
                if ($this->_debug ) {
                    COM_errorLog("get_processVariable -> Process:{$this->_nfProcessId}, variable:$variable - DOES NOT EXIST");
                }
            }
        }
        return $retval;
    }


    // Set a process variable as defined for this template
    // Requires the processID to be set and then pass in a variable's name and value
    // if both the process and the name exist, you get a value..
    // otherwise, you get NULL
    function set_processVariable($variableName, $variableValue=0)
    {
        global $_TABLES;
        $retval = null;
        $thisvar = strtolower($variableName);
        if($this->_nfProcessId==NULL || $this->_nfProcessId==''){
            if ($this->_debug ) {
                COM_errorLog("set_ProcessVariable: The Process ID has not been set.");
            }
            $retval=NULL;
        }else{
            // setting the value
            $sql  = "SELECT a.id, a.nf_templateVariableID FROM {$_TABLES['nfprocessvariables']} a ";
            $sql .= "INNER JOIN {$_TABLES['nftemplatevariables']} b ON a.nf_templateVariableID=b.id ";
            $sql .= "WHERE a.nf_processID='{$this->_nfProcessId}' ";
            $sql .= "AND b.variableName='$thisvar'";
            $result = DB_query($sql );
            if (DB_numRows($result ) > 0 ) {
                list($processVariable_id,$variable_id) = DB_fetchArray($result );
                $sql =  "UPDATE {$_TABLES['nfprocessvariables']} set variableValue='$variableValue' WHERE id='$processVariable_id' ";
                $sql .= "AND nf_processID='{$this->_nfProcessId}'";
                $result = DB_Query($sql);
                if ($this->_debug ) {
                    COM_errorLog("set_processVariable -> Process:{$this->_nfProcessId}, variable:$variableName, value:$variableValue");
                }
                if ($result) {
                    $retval = $variableValue;
                }
                //now see if that process variable controlled assignment
                $sql  = "SELECT a.id FROM {$_TABLES['nfqueue']} a LEFT JOIN {$_TABLES['nftemplatedata']} b ON a.nf_templateDataID=b.id ";
                $sql .= "LEFT JOIN {$_TABLES['nftemplateassignment']} c ON a.nf_templateDataID=c.nf_templateDataID ";
                $sql .= "WHERE (a.archived IS NULL OR a.archived=0) AND a.nf_processID={$this->_nfProcessId} AND b.assignedByVariable=1 ";
                $sql .= "AND c.nf_processVariable=$variable_id;";
                $res = DB_query($sql);
                while ($queueRec = DB_fetchArray($res)) {
                    $userAssignmentInfo = array();
                    $userAssignmentInfo[$variable_id] = $variableValue;
                    $this->assign_task($queueRec['id'],$userAssignmentInfo);
                }
            } else {
                if ($this->_debug ) {
                    COM_errorLog("set_processVariable -> Process:{$this->_nfProcessId}, variable:$variableName - DOES NOT EXIST");
                }
            }
        }
        return $retval;
    }

    // Gets an internal class variable using a more friendly name
    function get_internalVar($varname )
    {
        $retval = '';
        switch (strtolower($varname ) ) {
            case 'processid':
                $retval = $this->_nfProcessId;
                break;
            case 'templateid':
                $retval = $this->_nfTemplateId;
                break;
            case 'queueid':
                $retval = $this->_nfQueueId;
                break;
            case 'userid':
                $retval = $this->_nfUserId;
                break;
            case 'groupid':
                $retval = $this->_nfGroupId;
                break;
            case 'mode':
                $retval = $this->_mode;
                break;
            case 'debug':
                $retval = $this->_debug;
                break;
        }
        if ($this->_debug ) {
            echo "get_var($" . $varname . ") returned " . $retval;
        }
        return $retval;
    }


    // this function will retrieve a single item out of the queue
    function get_queue_item($item )
    {
        if ($this->_debug ) {
            echo "get_queue_item($" . $item . ") - Entered";
        }

        if ($this->_debug ) {
            echo "get_queue_item($" . $item . ") - Exit";
        }
    }


    // simple function to display all the tasks based on the mode you're after.
    function get_tasks()
    {
        global $_TABLES;
        $retval = "";
        $tasks = array();
        switch ($this->_mode ) {
            case 'user':
                for($cntr = 0;$cntr < $this->_nfUserTaskCount;$cntr++ ) {
                    $taskid = NXCOM_filterInt($this->_nfuserTaskList["id"][$cntr]);
                    $processId = DB_getItem($_TABLES['nfqueue'], 'nf_processID', "id='$taskid'");
                    $templateTaskId = DB_getItem($_TABLES['nfqueue'], 'nf_templateDataID', "id='$taskid'");
                    $cdate = DB_getItem($_TABLES['nfqueue'], 'createdDate', "id='$taskid'");
                    $taskrec = array();
                    $taskrec['id'] = $taskid;
                    $taskrec['url'] = $this->_nfuserTaskList["url"][$cntr];
                    $taskrec['template'] = $this->_nfuserTaskList["template"][$cntr];
                    $taskrec['taskname'] = $this->_nfuserTaskList["taskname"][$cntr];
                    $taskrec['stepType'] = $this->_nfuserTaskList["stepType"][$cntr];
                    $taskrec['processid'] = $processId;
                    $taskrec['cdate'] = $cdate;
                    $taskrec['templateTaskid'] = $templateTaskId;
                    array_push ($tasks, $taskrec);
                }
                return $tasks;
                break;
            case 'process':
                for($cntr = 0;$cntr < $this->_nfProcessTaskCount;$cntr++ ) {
                    $tempstr = "";
                    $tempstr = str_replace("{id}", $this->_nfprocessTaskList["id"][$cntr], $format );
                    $tempstr = str_replace("{url}", $this->_nfprocessTaskList["url"][$cntr], $tempstr );
                    $tempstr = str_replace("{taskname}", $this->_nfprocessTaskList["taskname"][$cntr], $tempstr );
                    $retval .= $tempstr;
                }
                return $retval;
                break;
        }
    }
    // this function is responsible for setting the status of the
    // queue item from 0 to 1.  must have the queueid to do this.
    function complete_task($queueID)
    {
        global $_TABLES;

        $queueID = NXCOM_filterInt($queueID);
        $this->_currentQueueID = $queueID;
        $processID = DB_getItem($_TABLES['nfqueue'],'nf_processID',"id='{$queueID}'");

        if ($this->_debug ) {
            COM_errorLog("Nexflow: Complete_task - updating queue item: $queueID");
        }

        // Update Project Task History record as completed
        //RK - lets check if there's even an entry for this task first.  if there's no entry, create one
        //this takes into account those flows that do NOT have taskhistory records (non-'project' flows);
        $res = DB_query("SELECT * FROM {$_TABLES['nfproject_taskhistory']}  WHERE task_id=$queueID");
        if(DB_numRows($res) > 0) {
            DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP(),status=1 WHERE task_id=$queueID and status=0");
        } else {
            $dateCreated = DB_getItem($_TABLES['nfqueue'],'createdDate',"id='{$queueID}'");
            $sql = "INSERT INTO {$_TABLES['nfproject_taskhistory']} ";
            $sql .= "(task_id, process_id,  date_assigned, date_started, date_completed, status) ";
            $sql .= "values ({$queueID},{$processID},   UNIX_TIMESTAMP('{$dateCreated}'),UNIX_TIMESTAMP('{$dateCreated}'),UNIX_TIMESTAMP(),1)";
            DB_query($sql);
        }

        if ($this->_nfUserId == '' or $this->_nfUserId == null ) {
            $currentUID = DB_getItem($_TABLES['nfproductionassignments'],'uid',"task_id=$queueID");
            if ($currentUID == '' OR $currentUID == null) {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid=NULL, status=1 where id='$queueID'");
            } else {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid=$currentUID, status=1 where id='$queueID'");
            }
        } else {
            DB_query("UPDATE {$_TABLES['nfqueue']} set uid='{$this->_nfUserId}',status=1 where id='$queueID'");
        }
        // Self Prune Production Assignment table - delete the now completed task assignment record
        DB_query("DELETE FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$queueID");

        $this->private_sendTaskCompletionNotifications();

    }

    // this function is responsible for setting the status of the
    // queue item from 0 to 3.  must have the queueid to do this.
    function cancel_task($queueID)
    {
        global $_TABLES;
        $queueID=NXCOM_filterInt($queueID);
        $this->_currentQueueID = $queueID;
        $processID = DB_getItem($_TABLES['nfqueue'],'nf_processID',"id='{$queueID}'");
        if ($this->_debug ) {
            COM_errorLog("Nexflow: Cancel_task- updating queue item: $queueID");
        }

        // Update Project Task History record as completed
        DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP(),status=3 WHERE task_id=$queueID");

        if ($this->_nfUserId == '' or $this->_nfUserId == null ) {
            DB_query("UPDATE {$_TABLES['nfqueue']} set uid=NULL, status=3 where id='$queueID'");
        } else {
            DB_query("UPDATE {$_TABLES['nfqueue']} set uid='{$this->_nfUserId}',status=3 where id='$queueID'");
        }
        // Self Prune Production Assignment table - delete the now completed task assignment record
        DB_query("DELETE FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$queueID");
        $this->private_sendTaskCompletionNotifications();

    }


    /**
    * Method assign task - create productionAssignment Record and test if to-be-assigned user has their out-of-office setting active
    * @param        int         $queueID     Task ID from the workflow queue table
    * @param        array       $assignemnt  Array of records where the key is the variable id  if applicable and the user id
                                             If the assignment is by user, the key will be 0 or a negative value - in the case of multiple assignments
    * @return       n/a         No return
    */
    function assign_task($queueID,$userinfo)
    {
        global $_TABLES;
        $queueID=NXCOM_filterInt($queueID);
        foreach ($userinfo as $processVariable => $userID) {

            /* The array of users to be assigned may be an array of multiple assignments by user not variable
             * In this case, we can not have multiple array records with a key of 0 - so a negative value is used
            */

            if($processVariable < 0) $processVariable = 0;

            if ($userID > 1) {
                $query = DB_query ("SELECT away_start,away_return,is_active FROM {$_TABLES['nfuseraway']} WHERE uid = $userID");
                list ($datestart,$datereturn,$is_active) = DB_fetchArray ($query);
                // Check if user is away - away feature active and current time within the away window
                if ($is_active == 1 AND time() > $datestart AND time() < $datereturn) {
                    /* User is away - determine who to re-assign task to */
                    $assignToUserID = nf_getAwayReassignmentUid($userID);

                    // If we have a new value for the assignment - then we need to set the assignBack field

                    if ($assignToUserID != $userID) {
                        $assignBack = $userID;
                    } else {
                        $assignBack = 0;
                    }

                } else {
                    $assignToUserID = $userID;
                    $assignBack = 0;
                }
            } else {
                $assignToUserID = 0;
                $assignBack = 0;
            }

            // Check and see if we have an production assignment record for this task and processVariable
            $sql = "SELECT uid FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$queueID ";
            if ($processVariable > 0) {
                $sql .= "AND nf_processVariable=$processVariable";
            } else {
                $sql .= "AND nf_processVariable=0 AND uid=$userID";
            }
            if (DB_numRows(DB_query($sql)) == 0) {
                $sql  = "INSERT INTO {$_TABLES['nfproductionassignments']} (task_id,uid,nf_processVariable,assignBack_uid,last_updated) ";
                $sql .= "VALUES ($queueID, $assignToUserID, $processVariable, $assignBack, UNIX_TIMESTAMP() )";
                DB_query($sql);
            } else {
                $sql  = "UPDATE {$_TABLES['nfproductionassignments']} set uid=$assignToUserID, last_updated=UNIX_TIMESTAMP(), ";
                $sql .= "assignBack_uid = $assignBack ";
                $sql .= "WHERE task_id=$queueID AND nf_processVariable=$processVariable";
                DB_query($sql);
            }

        }

    }

    /**
    * Method to archive task and prune the production assigment table for this completed queue record
    * Similar to complete_task and cancel_task but don't want to trigger notifications
    * called by orchestrator when completing non-interactive tasks
    */
    function archive_task($queueID,$status=0)
    {
        global $_TABLES;

        $queueID = NXCOM_filterInt($queueID);
        $processID = DB_getItem($_TABLES['nfqueue'],'nf_processID',"id='{$queueID}'");
        $status = NXCOM_filterInt($status);
        $thisDate = date('Y-m-d H:i:s' );
        // Set the status field to completed if not set
        $setstatus = '';
        if ($status == 0) {
            // If status has no current value then set the status to 1 (completed)
            $currentStatus = DB_getItem($_TABLES['nfqueue'],'status',"id=$queueID");
            if ($currentStatus == 0) $status = 1;
        }
        if ($status > 0) {
            DB_query("UPDATE {$_TABLES['nfqueue']} SET status=$status,completedDate='{$thisDate}', archived=1 where id=$queueID");
        } else {
            DB_query("UPDATE {$_TABLES['nfqueue']} SET completedDate='{$thisDate}', archived=1 where id=$queueID");
        }

        // Self Prune Production Assignment table - delete the now completed task assignment record
        DB_query("DELETE FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$queueID");
    }

    function delete_process($pid) {
        global $_TABLES;
        $pid = intval($pid);

        $res = DB_query("SELECT id FROM {$_TABLES['nfqueue']} WHERE nf_processID=$pid");
        while (list ($qid) = DB_fetchArray($res)) {
            DB_query("DELETE FROM {$_TABLES['nfqueuefrom']} WHERE queueID=$qid");
            DB_query("DELETE FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$qid");
            DB_query("DELETE FROM {$_TABLES['nfnotifications']} WHERE queueID=$qid");
        }
        DB_query("DELETE FROM {$_TABLES['nfqueue']} WHERE nf_processID=$pid");
        DB_query("DELETE FROM {$_TABLES['nfprocessvariables']} WHERE nf_processID=$pid");
        DB_query("DELETE FROM {$_TABLES['nfprocess']} WHERE id=$pid");
    }


    // Retrieve the value of the optionalParm as defined in the template
    // The class current task must be set first
    function get_taskOptionalParm() {
        global $_TABLES;
        $testVar=NXCOM_filterInt($this->_currentQueueID);
        if($testVar==0){
            if ($this->_debug ) {
                COM_errorLog("Nexflow: get_taskOptionalParm - currentQueueID has not been set. Returning NULL");
                return NULL;
            }
        }else{
            // Retrieve the template id for the current queue record
            $templateDataId = DB_getItem($_TABLES['nfqueue'], 'nf_templateDataID', "id={$this->_currentQueueID}");

            // Retrive the value of the optionalParm from the template for this task
            $optionalParm = DB_getItem($_TABLES['nftemplatedata'], 'optionalParm', "id=$templateDataId");
            return $optionalParm;
        }
    }


    /*
    *  Function called to check if a task assignment notification has been defined for this task
    */
    function private_sendTaskAssignmentNotifications() {
        global $_TABLES;

        // Retrieve all the un-completed interactive type tasks in the queue that have a reminder task time set
        $fields  = 'a.id, a.status,a.nf_templateDataID,';
        $fields .= 'c.nf_templateID,c.taskname,c.nf_stepType,c.assignedByVariable, b.id AS processID ';
        $sql  = "SELECT distinct $fields ";
        $sql .= "FROM {$_TABLES['nfqueue']} a ";
        $sql .= "INNER JOIN {$_TABLES['nfprocess']} b on a.nf_processID = b.id ";
        $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c on a.nf_templateDataID = c.id ";
        $sql .= "WHERE a.status = 0 ";                                              // Uncompleted task and reminder datetime set
        $sql .= "AND (c.nf_stepType=1 OR c.nf_stepType=7 OR c.nf_stepType=8) ";     // Interactive Task Types
        $sql .= "AND (a.archived <> 1 OR a.archived IS NULL OR a.archived=0 ) AND (b.complete=0) ";
        $q1 = DB_query($sql);

        $subject = 'Workflow New Task Notification';
        $message = "You have a new task: {$A['taskname']}";
        while ($A = DB_fetchArray($q1)) {   // Foreach un-completed interactive task in the queue
            // Determine which process variables for this task - contain valid users to send notification to
            // Note: Test if variable value greater then 1 for a valid userid.
            // If task assignment is done by variable, the variable may be set after the task is created
            // So I can not create the notification tracking record upon task assignment as I may not know the user
            $sql =  "SELECT b.variableValue FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nfprocessvariables']} b ";
            $sql .= "WHERE a.nf_templateDataID={$A['nf_templateDataID']} ";
            $sql .= "AND a.nf_prenotifyVariable=b.nf_templateVariableID AND b.nf_processID='{$A['processID']}' ";
            $sql .= "AND b.variableValue > 1";
            $q2 = DB_query($sql);
            while (list($notifyUID) = DB_fetchArray($q2)) {
                $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                if ($email != '') {
                    // Log this notification if it does not already exist and send the email to user
                    if (DB_count($_TABLES['nfnotifications'], array('queueID','uid'), array($A['id'],$notifyUID)) == 0) {
                        $notifyUser = COM_getDisplayName($notifyUID);
                        $logmsg  = "Nexflow: Send assignment notification for task id: {$A['id']} ";
                        $logmsg .= "({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ";
                        nf_logNotification($logmsg);
                        $message = nf_formatEmailMessage('prenotify',$A['nf_templateDataID'],$A['id'],$notifyUser);
                        nf_sendEmail ($email,$subject,$message);
                        DB_query("INSERT INTO {$_TABLES['nfnotifications']} (queueID,uid,notification_sent) VALUES ('{$A['id']}','$notifyUID',1)");
                    }
                }
            }
            // Now check and see if this task has a notification set for the special "TASK_OWNER" resource variable (id=999)
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($A['nf_templateDataID'],999)) == 1) {
                // Check if this task is assigned by variable or UID - Get all assigned users
                $sql  = "SELECT b.uid,b.nf_processVariable FROM {$_TABLES['nftemplateassignment']} b ";
                $sql .= "WHERE b.nf_templateDataID = '{$A['nf_templateDataID']}' ";
                $sql .= "AND (uid IS NOT NULL OR nf_processVariable IS NOT NULL) ";
                $q3 = DB_query($sql);
                while (list ($notifyUID,$variableid) = DB_fetchArray($q3)) {
                    $email = '';
                    if ($variableid != NULL AND $A['assignedByVariable'] == 1) {
                        $notifyUID = DB_getItem($_TABLES['nfprocessvariables'],'variableValue',"nf_processID='{$A['processID']}' AND nf_templateVariableID='$variableid'");
                        $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                    } elseif($A['assignedByVariable'] == 0 ) {
                        $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                    }
                    // Log this notification if it does not already exist and send the email to user
                    if ($email != '' AND DB_count($_TABLES['nfnotifications'], array('queueID','uid'), array($A['id'],$notifyUID)) == 0) {
                        $notifyUser = COM_getDisplayName($notifyUID);
                        DB_query("INSERT INTO {$_TABLES['nfnotifications']} (queueID,uid,notification_sent) VALUES ('{$A['id']}','$notifyUID',1)");
                        $logmsg  = "Nexflow: Send assignment notification for task id: {$A['id']} ";
                        $logmsg .= "({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ";
                        nf_logNotification($logmsg);
                        $message = nf_formatEmailMessage('prenotify',$A['nf_templateDataID'],$A['id'],$notifyUser);
                        nf_sendEmail ($email,$subject,$message);
                    }
                }
            }
        }
    }

    /*
    *  Function called by the complete_task and cancel_task functions
    *  Send out any task assignment notifications and clean up notification recordss
    */
    function private_sendTaskCompletionNotifications() {
        global $_TABLES;
        $this->_currentQueueID=NXCOM_filterInt($this->_currentQueueID);
        // For the current task check if any notifications have been defined
        $fields  = 'a.id,a.nf_templateDataID,a.status,c.nf_templateID,c.assignedByVariable,c.taskname,b.id AS processID';
        $sql  = "SELECT distinct $fields ";
        $sql .= "FROM {$_TABLES['nfqueue']} a ";
        $sql .= "INNER JOIN {$_TABLES['nfprocess']} b on a.nf_processID = b.id ";
        $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c on a.nf_templateDataID = c.id ";
        $sql .= "WHERE a.id='{$this->_currentQueueID}' ";
        $A = DB_fetchArray(DB_query($sql));

        if ($A['status'] == 3) {
            $subject = 'Workflow Task Cancellation Notification';
            $message = "Task: {$A['taskname']} has been cancelled.";
        } else {
            $subject = 'Workflow Task Completion Notification';
            $message = "Task: {$A['taskname']} has been completed.";
        }

        // Retrieve any variables that have been assigned for Task Completion notifications
        // Send out notifications to all defined users - using distinct to eliminate duplicates
        $sql =  "SELECT DISTINCT b.variableValue FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nfprocessvariables']} b ";
        $sql .= "WHERE a.nf_postnotifyVariable=b.nf_templateVariableID AND b.nf_processID='{$A['processID']}'";
        $sql .= "AND b.variableValue > 1";
        $q2 = DB_query($sql);
        while (list ($notifyUID) = DB_fetchArray($q2)) {
            $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
            if ($email != '') {
                $notifyUser = COM_getDisplayName($notifyUID);
                nf_notificationLog("Nexflow: Send completion notification for task id: {$A['id']} ({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ");
                $message = nf_formatEmailMessage('postnotify',$A['nf_templateDataID'],$A['id'],$notifyUser);
                nf_sendEmail ($email,$subject,$message);
            }
        }
        // Now check and see if this task has a notification set for the special "TASK_OWNER" resource variable (id=999)
        // BL: May need to compare against users sent notifications above to prevent duplicates
        if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_postnotifyVariable'),array($A['nf_templateDataID'],999)) == 1) {
            // Check if this task is assigned by variable or UID - Get all assigned users
            $sql  = "SELECT b.uid,b.nf_processVariable FROM {$_TABLES['nftemplateassignment']} b ";
            $sql .= "WHERE b.nf_templateDataID = '{$A['nf_templateDataID']}' ";
            $sql .= "AND (uid IS NOT NULL OR nf_processVariable IS NOT NULL) ";
            $q3 = DB_query($sql);
            while (list ($notifyUID,$variableid) = DB_fetchArray($q3)) {
                $email = '';
                if ($variableid != NULL AND $A['assignedByVariable'] == 1) {
                    $notifyUID = DB_getItem($_TABLES['nfprocessvariables'],'variableValue',"nf_processID='{$A['processID']}' AND nf_templateVariableID='$variableid'");
                    $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                } elseif($A['assignedByVariable'] == 0 ) {
                    $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                }
                if ($email != '') {
                    $notifyUser = COM_getDisplayName($notifyUID);
                    nf_notificationLog("Nexflow: Send completion notification for task id: {$A['id']} ({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ");
                    $message = nf_formatEmailMessage('postnotify',$A['nf_templateDataID'],$A['id'],$notifyUser);
                    nf_sendEmail ($email,$subject,$message);
                }
            }
        }
        // Delete any notification records now for this task in the queue
        DB_query("DELETE FROM {$_TABLES['nfnotifications']} WHERE queueID='{$this->_currentQueueID}'");
    }

    // Check for any task reminder notifications that need to be sent out
    function  private_sendTaskReminders() {
        global $_TABLES, $_CONF;

        // Retrieve all the un-completed interactive type tasks in the queue that have a reminder task time set
        $fields  = 'a.id, a.nextReminderTime, a.status, a.nf_templateDataID, a.numRemindersSent, a.uid, a.createdDate, ';
        $fields .= 'c.nf_templateID,c.taskname,c.reminderInterval,c.subsequentReminderInterval,c.assignedByVariable, ';
        $fields .= 'c.numReminders,c.escalateVariableID, b.id AS processID ';
        $sql  = "SELECT distinct $fields ";
        $sql .= "FROM {$_TABLES['nfqueue']} a ";
        $sql .= "INNER JOIN {$_TABLES['nfprocess']} b on a.nf_processID = b.id ";
        $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c on a.nf_templateDataID = c.id ";
        $sql .= "WHERE a.status = 0 AND  a.nextReminderTime > 0 ";   // Uncompleted task and reminder datetime set
        $sql .= "AND c.reminderInterval > 0 ";                       // Interval set for reminder in number of days
        $sql .= "AND (c.nf_stepType=1 OR c.nf_stepType=7 OR c.nf_stepType=8) ";                 // Interactive Task Types
        $sql .= "AND (a.archived <> 1 OR a.archived IS NULL OR a.archived=0 ) AND (b.complete=0) ";
        $sql .= "AND NOW() > a.nextReminderTime ";                   // Current time has passed reminder time
        $q1 = DB_query($sql);
        // For each un-completed task in the queue - check if reminder notification should be sent out.
        while ($A = DB_fetchArray($q1)) {

            // Check if have exceeded the number of notifications and need to send out Escalation email.
            if (($A['numReminders'] != 0 AND $A['numReminders'] == ($A['numRemindersSent'] + 1) )) {
                $notifyUID = DB_getItem($_TABLES['nfprocessvariables'], 'variableValue', "nf_templateVariableID = '{$A['escalateVariableID']}' AND nf_processID = '{$A['processID']}'");
                $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                $notifyUser = COM_getDisplayName($notifyUID);
                $message = nf_formatEmailMessage('escalation',$A['nf_templateDataID'],$A['id'],$notifyUser);
                $subject = 'Workflow Task Escalation Notification';
                nf_sendEmail ($email,$subject,$message);
                DB_query("UPDATE {$_TABLES['nfqueue']} SET numRemindersSent = '0' WHERE id = '{$A['id']}';");
            } else {
                $subject = 'Workflow Task Reminder Notification';
                $message = "Task: {$A['taskname']} is un-completed.";
                DB_query("UPDATE {$_TABLES['nfqueue']} SET numRemindersSent = numRemindersSent + 1 WHERE id = '{$A['id']}';");
                // Determine which process variables contain users to send reminder to
                $sql =  "SELECT DISTINCT b.variableValue FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nfprocessvariables']} b ";
                $sql .= "WHERE a.nf_remindernotifyVariable=b.nf_templateVariableID AND b.nf_processID='{$A['processID']}'";
                $sql .= "AND b.variableValue > 1 AND a.nf_templateDataID={$A['nf_templateDataID']}";
                $q2 = DB_query($sql);
                // Loop through users setup to receive reminders for this task
                while (list ($notifyUID) = DB_fetchArray($q2)) {
                    nf_notificationLog("Nexflow: Send reminder for task id: {$A['id']} ({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUID ");
                    $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                    if ($email != '') {
                        $notifyUser = COM_getDisplayName($notifyUID);
                        $logmsg  = "Nexflow: Send task reminder notification for task id: {$A['id']} ";
                        $logmsg .= "({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ";
                        nf_notificationLog($logmsg);
                        $message = nf_formatEmailMessage('reminder',$A['nf_templateDataID'],$A['id'],$notifyUser);
                        nf_sendEmail ($email,$subject,$message);
                    }
                }
                // Now check and see if this task has a reminder set for the special "TASK_OWNER" resource variable (id=999)
                if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($A['nf_templateDataID'],999)) == 1) {
                    // Check if this task is assigned by variable or UID - Get all assigned users
                    $sql  = "SELECT b.uid,b.nf_processVariable FROM {$_TABLES['nftemplateassignment']} b ";
                    $sql .= "WHERE b.nf_templateDataID = '{$A['nf_templateDataID']}' ";
                    $sql .= "AND (uid IS NOT NULL OR nf_processVariable IS NOT NULL) ";
                    $q3 = DB_query($sql);
                    while (list ($notifyUID,$variableid) = DB_fetchArray($q3)) {
                        $email = '';
                        if ($variableid != NULL AND $A['assignedByVariable'] == 1) {
                            $notifyUID = DB_getItem($_TABLES['nfprocessvariables'],'variableValue',"nf_processID='{$A['processID']}' AND nf_templateVariableID='$variableid'");
                            $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                        } elseif($A['assignedByVariable'] == 0 ) {
                            $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
                        }
                        if ($email != '') {
                            $notifyUser = COM_getDisplayName($notifyUID);
                            $logmsg  = "Nexflow: Send task reminder notification for task id: {$A['id']} ";
                            $logmsg .= "({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ";
                            nf_notificationLog($logmsg);
                            $message = nf_formatEmailMessage('reminder',$A['nf_templateDataID'],$A['id'],$notifyUser);
                            nf_sendEmail ($email,$subject,$message);
                        }
                    }
                }
                // Update the nextReminder Timestamp for this task in the queue
                if ($A['subsequentReminderInterval'] > 0) {
                    $reminderInterval = $A['subsequentReminderInterval'];
                } else {
                    $reminderInterval = $A['reminderInterval'];
                }
                $sql = "UPDATE {$_TABLES['nfqueue']} SET nextReminderTime = DATE_ADD( NOW(), INTERVAL $reminderInterval DAY) WHERE id='{$A['id']}'";
                DB_query($sql);
            }
        }
    }

    // this function takes no arguments
    // we determine if any of the
    // items in the queue associated with a process are
    // complete.  if they are complete, its the job of this
    // function to determine if there are any next steps and
    // fill the queue.
    function clean_queue()
    {
        global $_TABLES, $_CONF;
        $processTaskList = array("id" => array(), "processid" => array() );
        $processTaskListcount = 0;
        $this->private_sendTaskAssignmentNotifications();
        $this->private_sendTaskReminders();

        $sql = "SELECT distinct a.id, a.status,a.nf_templateDataID, c.nf_templateID, c.nf_stepType, c.nf_handlerid, ";
        $sql .= "c.function, e.templateName, f.handler, b.id AS processId, h.steptype ";
        $sql .= "FROM {$_TABLES['nfqueue']} a inner join {$_TABLES['nfprocess']} b on  a.nf_processId = b.id ";
        $sql .= "inner join {$_TABLES['nftemplatedata']} c on a.nf_templateDataId = c.id ";
        $sql .= "inner join {$_TABLES['nftemplate']} e on b.nf_templateId = e.id ";
        $sql .= "inner join {$_TABLES['nfsteptype']} h on c.nf_steptype = h.id ";
        $sql .= "left outer join {$_TABLES['nfhandlers']} f on c.nf_handlerId = f.id ";
        $sql .= "left outer join {$_TABLES['nftemplateassignment']} g on g.nf_templateDataid = c.id ";
        $sql .= "WHERE ((a.status <>0 AND a.status IS NOT NULL and a.status<>2 and (h.id=1 OR h.id=7 OR h.id=8)) ";
        $sql .= "OR ((a.status=0 or a.status=3 or a.status=4) and (h.id=2 or h.id=3 or h.id=4 or h.id=5 or h.id=6 or h.id=9 or h.id=10 or h.id=11)) ) ";
        $sql .= "AND (a.archived <> 1 OR a.archived IS NULL OR a.archived =0 ) and (b.complete=0)";

        $result = DB_query($sql );
        $nrows = DB_numRows($result );
        if ($this->_debug ) {
            COM_errorLog("nexflow: Number of processes in queue: $nrows");
        }

        switch ($nrows ) {

            case 0:
                if ($this->_debug ) {
                    echo "clean_queue - 0 rows returned.  Nothing in queue.<BR>";
                }
                break;

            default: // there is one or more queue items to fuss over.
                if ($this->_debug ) {
                    echo "clean_queue - $nrows rows returned.<BR>";
                }

                for($i = 0; $i < $nrows; $i++ ) {
                    $A = DB_fetchArray($result );
                    $stepType = $A['steptype'];
                    $processID = NXCOM_filterInt($A['processId']);
                    $queueID = NXCOM_filterInt($A['id']);
                    $handler = $A['handler'];
                    $templateName = $A['templateName'];
                    $templateDataID = $A['nf_templateDataID'];
                    // this switch is used to determine what task type it is.
                    // in the event its a manual web task, we'll just go ahead and clean it up..
                    // however, in the event that its an AND task, we have to be careful that
                    // we check the preceeding queue elements to ensure that they're all done before completing
                    // the and task and then also entering the next queue item.
                    if ($this->_debug ) {
                        COM_errorLog("Process: {$A['processId']} , Step Type: $stepType");
                    }

                    switch (strtolower($stepType ) ) {
                        case 'start':
                        case 'end':
                            $this->private_nfNextStep($queueID, $processID );
                            break;

                        case 'and':
                            // have processid and tempatedataid
                            // first determine how many tasks must be and-ed together
                            // then determine how many are in wms_nf_queueFrom which are pointing to me
                            // if the first and 2nd numbers are equal, then we're good to test..
                            // otherwise, skip it.

                            $numComplete = 0;
                            $numIncomplete = 0;

                            $sql  = "SELECT count( a.id ) AS templateCount FROM {$_TABLES['nfqueue']} a ";
                            $sql .= "INNER JOIN {$_TABLES['nftemplatedatanextstep']} b ";
                            $sql .= "ON (a.nf_templateDataID = b.nf_templateDataTo ";
                            $sql .= "OR a.nf_templateDataID = b.nf_templateDataToFalse) ";
                            $sql .= "WHERE a.ID ='$queueID'";

                            $templateCountResult = DB_fetchArray(DB_query($sql));
                            $numComplete = $templateCountResult[0];

                            $sql  = "SELECT  count( a.id ) AS processCount FROM {$_TABLES['nfqueuefrom']} a ";
                            $sql .= "INNER JOIN {$_TABLES['nfqueue']} b on a.FromQueueID=b.id ";
                            $sql .= "WHERE (a.queueid = '$queueID' or a.queueid=0) ";
                            $sql .= "AND b.nf_processid='$processID'";

                            $processCountResult = DB_fetchArray(DB_query($sql));
                            $numIncomplete = $processCountResult[0];
                            if ($this->_debug ) {
                                COM_errorLog("Template Count: {$numComplete} and Process Count: {$numIncomplete}");
                            }

                            // sounds confusing, but if the processCount is greater than the completed ones, we're ok too
                            if ($numIncomplete == $numComplete || $numIncomplete > $numComplete ) {
                                // we have all of the incoming items done for this AND
                                // we can now carry out updating this queue item's information
                                $this->private_nfNextStep($queueID, $processID );
                            } else {
                                // not all the incomings for the AND are done
                                // just here for troubleshooting purposes
                            }
                            break;

                        case 'or':
                            break;

                        case 'batch':
                            // since this is an automated task, we need to say that it has not succeeded until the handler's code executes
                            // and resets this variable back to true.
                            $success = false;
                            // Run the batch task's php code, checking to see if it exists first
                            if (file_exists($_CONF['path_html'] . $A['handler'])) {
                                require($_CONF['path_html'] . $A['handler'] );
                            } elseif (file_exists($A['handler'])) {  // Check in current directory
                                include ($A['handler']);
                            }
                            if ($success ) {
                                $this->private_nfNextStep($queueID, $processID );
                            }

                            break; //end batch task

                        case 'batch function':
                            // since this is an automated task, we need to say that it has not succeeded until the handler's code executes
                            // and resets this variable back to true.
                            $success = false;
                            // Run the batch task's php code, checking to see if it's a function exists
                            if ($A['function'] != '') {
                               if ($this->_debug ) {
                                   COM_errorLog("Batch Function: {$A['function']}");
                               }
                               if (function_exists($A['function'])) {
                                   $success = $A['function']($queueID,$processID);
                               }
                            }

                            if ($success ) {
                                $this->complete_task($queueID);
                                $this->private_nfNextStep($queueID, $processID );
                            }

                            break; //end batch task

                        case 'set process variable':
                            $spvRes = DB_query("SELECT formid, fieldid, varValue, incValue, varToSet FROM {$_TABLES['nftemplatedata']} WHERE id=$templateDataID");
                            list ($formid, $fieldid, $varvalue, $incvalue, $vartoset) = DB_fetchArray($spvRes);

                            if ($vartoset > 0) {    //needs to be valid variable to set
                                if ($varvalue != '') {  //set by input
                                    $setvalue = NXCOM_filterText($varvalue);
                                    DB_query("UPDATE {$_TABLES['nfprocessvariables']} SET variableValue='$setvalue' WHERE nf_processID=$processID AND nf_templateVariableID=$vartoset");
                                }
                                else if ($formid > 0 && $fieldid > 0) {  //set by form result
                                    //have to find the form result
                                    //first get the project id
                                    $spvSql  = "SELECT a.variableValue FROM {$_TABLES['nfprocessvariables']} a ";
                                    $spvSql .= "LEFT JOIN {$_TABLES['nftemplatevariables']} b ON b.id=a.nf_templateVariableID ";
                                    $spvSql .= "WHERE b.variableName='PID' AND a.nf_processID=$processID";
                                    $spvRes = DB_query($spvSql);
                                    list ($pid) = DB_fetchArray($spvRes);
                                    $pid = intval ($pid);

                                    //now get the form result id
                                    $resid = intval (DB_getItem($_TABLES['nfproject_forms'], 'results_id', "project_id=$pid AND form_id=$formid"));

                                    //now get the result from the field id
                                    $setvalue = DB_getItem($_TABLES['formResData'], 'field_data', "result_id=$resid AND field_id=$fieldid");
                                    DB_query("UPDATE {$_TABLES['nfprocessvariables']} SET variableValue='$setvalue' WHERE nf_processID=$processID AND nf_templateVariableID=$vartoset");
                                }
                                else if ($incvalue != 0) {  //set by increment
                                    $curvalue = intval (DB_getItem($_TABLES['nfprocessvariables'], 'variableValue', "nf_processID=$processID AND nf_templateVariableID=$vartoset"));
                                    $setvalue = $curvalue + $incvalue;
                                    DB_query("UPDATE {$_TABLES['nfprocessvariables']} SET variableValue='$setvalue' WHERE nf_processID=$processID AND nf_templateVariableID=$vartoset");
                                }
                            }

                            $this->private_nfNextStep($queueID, $processID);
                            break;

                        case 'if':
                            // if task is a conditional task that looks for a single expression to evaluate
                            // a true and false branch is required for this task type.
                            // 1st determine what the argument is
                            $sql  = "SELECT a.ifValue,b.variableName, b.id as variableID, c.logicalEntry, d.operator ";
                            $sql .= "FROM {$_TABLES['nftemplatedata']} a ";
                            $sql .= "LEFT OUTER JOIN {$_TABLES['nftemplatevariables']} b on a.argumentVariable=b.id ";
                            $sql .= "LEFT OUTER JOIN {$_TABLES['nfifprocessarguments']} c on a.argumentProcess=c.id ";
                            $sql .= "LEFT OUTER JOIN {$_TABLES['nfifoperators']} d on a.operator=d.id ";
                            $sql .= "WHERE a.id='$templateDataID' limit 0,1 ";
                            $nextTaskResult = DB_query($sql );
                            $nextTaskRows = DB_numRows($nextTaskResult );
                            // holding the template's data in the result set now. should only be 1 row.
                            if ($nextTaskRows > 0 ) {
                                $C = DB_fetchArray($nextTaskResult );
                                $templateVariableID = $C['variableID'];
                                $operator = $C['operator'];
                                $ifValue = $C['ifValue'];

                                if ($C['variableID'] == null or $C['variableID'] == '' ) { // logical entry it is
                                    // using the logical entry, lets switch the logicalEntry value and determine what it is we should be comparing to
                                    // no matter how you slice it, we're going to need the last task's status.
                                    // get the last task's TemplateDataID, then query the queue for this process with the newly sourced templateDataID
                                    $sql  = "SELECT  b.status FROM {$_TABLES['nfqueuefrom']} a ";
                                    $sql .= "LEFT OUTER JOIN {$_TABLES['nfqueue']} b on b.id=a.fromqueueid ";
                                    $sql .= "WHERE a.queueid=$queueID ";
                                    $statusResult = DB_query($sql );
                                    $D = DB_fetchArray($statusResult );
                                    $lastStatus = $D[0];
                                    // $lastStatus now holds the value
                                    $whichBranch = null;
                                    // status
                                    // 0-ready //1-complete //2-on hold //3-aborted //4-if Condition False
                                    switch (strtolower($C['logicalEntry'] ) ) {
                                        case 'lasttasksuccess':
                                            if ($lastStatus == 0 or $lastStatus == 1 ) {
                                                $whichBranch = 1;
                                            } else {
                                                $whichBranch = 0;
                                            }
                                            break;
                                        case 'lasttaskcancel':
                                            if ($lastStatus == 3 ) {
                                                $whichBranch = 1;
                                            } else {
                                                $whichBranch = 0;
                                            }
                                            break;
                                        case 'lasttaskhold':
                                            if ($lastStatus == 2 ) {
                                                $whichBranch = 1;
                                            } else {
                                                $whichBranch = 0;
                                            }
                                            break;
                                        case 'lasttaskaborted':
                                            if ($lastStatus == 3 ) {
                                                $whichBranch = 1;
                                            } else {
                                                $whichBranch = 0;
                                            }
                                            break;
                                    } //end switch
                                    if ($this->_debug ) {
                                        COM_errorLog("NEXFLOW cleanqueue: Task: $queueID, IF Task => laststatus: $lastStatus, Branch: $whichBranch");
                                    }
                                } //end if($C['variableID']==NULL or $C['variableID']=='')
                                else { // variableID it is
                                    // need to perform a variable to value operation based on the selected operation!
                                    // $templateVariableID ,$operator ,$ifValue, $processID
                                    // need to select the process variable using the ID from the current process
                                    $sql  = "SELECT variableValue FROM {$_TABLES['nfprocessvariables']} ";
                                    $sql .= "WHERE nf_processID=$processID AND nf_templateVariableID=$templateVariableID";
                                    $ifQuery = DB_query($sql );
                                    $ifQueryNumRows = DB_numRows($ifQuery );

                                    if ($ifQueryNumRows > 0 ) {
                                        // should have a variable Value here.
                                        $ifArray = DB_fetchArray($ifQuery );
                                        $variableValue = $ifArray[0];
                                        if ($this->_debug ) {
                                            COM_errorLog("IF COMPARE => Operator: $operator and compare $variableValue to $ifValue");
                                        }
                                        switch ($operator ) {
                                            case '=':
                                                if ($variableValue == $ifValue ) {
                                                    $whichBranch = 1;
                                                } else {
                                                    $whichBranch = 0;
                                                }
                                                break;
                                            case '<':
                                                if ($variableValue < $ifValue ) {
                                                    $whichBranch = 1;
                                                } else {
                                                    $whichBranch = 0;
                                                }
                                                break;
                                            case '>':
                                                if ($variableValue > $ifValue ) {
                                                    $whichBranch = 1;
                                                } else {
                                                    $whichBranch = 0;
                                                }
                                                break;
                                            case '!=':
                                                if ($variableValue != $ifValue ) {
                                                    $whichBranch = 1;
                                                } else {
                                                    $whichBranch = 0;
                                                }

                                                break;
                                        } //end switch($operator)
                                    } //end if$ifQueryNumRows>0)
                                    else { // force the branch to the false side since the variable dosent exist...
                                        // can't be true if it dosent exist!!!
                                        $whichBranch = 0;
                                    }
                                } //end else variableID
                                // here we have common code for both the logical or the variable driven IF
                                // create new queue items dependent upon the $whichBranch variable
                                if ($whichBranch == 1 ) {
                                    // complete this task and create queue items that point to the true branch
                                    $sql  = "SELECT c.nf_templateDataTo FROM {$_TABLES['nfqueue']} a, {$_TABLES['nftemplatedatanextstep']} c ";
                                    $sql .= "WHERE a.nf_templateDataid=c.nf_templateDataFrom ";
                                    $sql .= "AND a.nf_processID=$processID AND a.id=$queueID";
                                    $statusToinsert = 1;
                                } else {
                                    // complete this task and create queue items that point to the false branch
                                    $sql  = "SELECT c.nf_templateDataToFalse FROM {$_TABLES['nfqueue']} a, {$_TABLES['nftemplatedatanextstep']} c ";
                                    $sql .= "WHERE a.nf_templateDataid=c.nf_templateDataFrom ";
                                    $sql .= "AND a.nf_processID=$processID AND a.id=$queueID";
                                    $statusToinsert = 4;
                                }
                                $nextTaskResult = DB_query($sql );
                                $nextTaskRows = DB_numRows($nextTaskResult );
                                if ($nextTaskRows == 0 ) {
                                    // if there are no rows for this specific QueueID and nothing for this processID, there's no next task
                                    $this->archive_task($queueID,$statusToinsert);
                                    $sql = "UPDATE {$_TABLES['nfprocess']} set complete=1 where id=$processID";
                                    $updateQuery = DB_query($sql );
                                    //if there is a project, update that status
                                    $sql = "UPDATE {$_TABLES['nfprojects']} set status=1 where wf_process_id=$processID";
                                    DB_query($sql );
                                } else { // we've got tasks
                                    for($nextStepCntr = 0;$nextStepCntr < $nextTaskRows;$nextStepCntr++ ) {
                                        $C = DB_fetchArray($nextTaskResult );

                                        if ($C[0] == null or $C[0] == '' ) {
                                            // the process is done, Archive the queue item adn set process to complete
                                            $this->archive_task($queueID,$statusToinsert);
                                            $updateQuery = DB_query("UPDATE {$_TABLES['nfprocess']} set complete=1 where id=$processID");
                                        } else {
                                            // we have a next step, thus we can archive the queue item and also insert a
                                            // new queue item with the next step populated as the next templatestepid
                                            // echo "next step available";
                                            $sql  = "SELECT * FROM {$_TABLES['nfqueue']} a ";
                                            $sql .= "WHERE a.nf_processid='$processID' AND a.nf_templateDataid='{$C[0]}'";
                                            $updateQuery = DB_query($sql );
                                            $updateQueryRows = DB_numRows($updateQuery );
                                            $retrieveQueryArray = DB_fetchArray($updateQuery );
                                            if ($this->_debug ) {
                                                COM_errorLog("If Task - Queue records Number: $updateQueryRows");
                                            }
                                            if ($updateQueryRows == 0 ) {
                                                // we have the situation here where we have no next item.. this means we
                                                // can create the next queue item..
                                                $thisDate = date('Y-m-d H:i:s' );
                                                $sql  = "INSERT INTO {$_TABLES['nfqueue']} (nf_processID, nf_templateDataID, status,createdDate) ";
                                                $sql .= "VALUES ('$processID','{$C[0]}',0,'$thisDate')";
                                                $updateQuery = DB_query($sql );
                                                $newTaskid = DB_insertID();
                                                if ($this->_debug ) {
                                                    $logmsg  = "Nexflow: New queue id (2) : $newTaskid - Template Taskid: {$C[0]} - ";
                                                    $logmsg .= "Assigned to: " . COM_getDisplayName(nf_getTaskOwner($C[0],$processID));
                                                    nf_notificationLog($logmsg);
                                                }

                                                // Add a new records to the queueFrom table now as well for this new queue record
                                                $sql = "INSERT INTO {$_TABLES['nfqueuefrom']} (queueID,fromQueueID) values ('$newTaskid','$queueID')";
                                                DB_query($sql );

                                                // Insert new assignment records for this new task - if an interactive task
                                                $newTaskAssignedUsers = $this->private_getAssignedUID($newTaskid);
                                                if (is_array($newTaskAssignedUsers) AND count($newTaskAssignedUsers) > 0) {
                                                    $this->assign_task($newTaskid,$newTaskAssignedUsers);
                                                }

                                                // Determine if task has a reminder set and if so then update the nextReminderTime field in the new queue record
                                                $reminderInterval = DB_getItem($_TABLES['nftemplatedata'],'reminderInterval',"id='{$C[0]}'");
                                                if ($reminderInterval > 0) {
                                                    $sql  = "UPDATE {$_TABLES['nfqueue']} SET nextReminderTime=DATE_ADD( NOW(), ";
                                                    $sql .= "INTERVAL $reminderInterval DAY) where id='$newTaskid'";
                                                    DB_query($sql );
                                                }
                                                // Check if notification has been defined for new task assignment
                                                $this->private_sendTaskAssignmentNotifications();
                                                $this->archive_task($queueID,$statusToinsert);

                                            } else {
                                                // we have a situation here where the next item already exists.
                                                // need to determine if the next item has a regeneration flag.
                                                // if there is a regeneration flag, then create a new process starting with that regeneration flagged item
                                                $sql = "SELECT * FROM {$_TABLES['nftemplatedata']} a WHERE a.id={$C[0]}";
                                                $regenResult = DB_query($sql );
                                                $regenCount = DB_numRows($regenResult );
                                                $regenArray = DB_fetchArray($regenResult );

                                                $toRegenerate = $regenArray['regenerate'];
                                                $template = $regenArray['nf_templateID'];

                                                if ($toRegenerate ) {
                                                    // regenerate the same process starting at the next step
                                                    // set the current process' complete status to 2.. 0 is active, 1 is done, 2 is has children
                                                    $this->newprocess($template, $C[0], $processID );
                                                    $this->archive_task($queueID,$statusToinsert);
                                                    $newTaskid = DB_getItem($_TABLES['nfqueue'],'id',"nf_processID='{$this->_nfProcessId}'");
                                                    if ($this->_debug ) {
                                                        COM_errorLog("Regenerate Task QueueID: $toQueueID");
                                                    }
                                                } else {
                                                    // no regeneration  we're done
                                                    $this->archive_task($queueID);
                                                }
                                                // Add a new records to the queueFrom table for this matching queue record
                                                $sql  = "INSERT INTO {$_TABLES['nfqueuefrom']} (queueID,fromQueueID) ";
                                                $sql .= "VALUES ({$retrieveQueryArray['id']},$queueID)";
                                                DB_query($sql );
                                                $this->archive_task($queueID,$statusToinsert);

                                            } //end else
                                        } //end else
                                    } //end for $nextstep
                                } //end else portion for nextStepTest=0
                            } //end if($nextTaskRows)
                            break;

                        default: // all other task types that just should be processed
                            $this->private_nfNextStep($queueID, $processID );
                            break;

                    } //end switch steptype
                } //end for $i=0
                break;
        } //end switch $nrows
    } //end function
    // function to carry out next step generation or
    // new process generation


    function private_nfNextStep($queueID, $processID )
    {
        global $_TABLES;
        $queueID = NXCOM_filterInt($queueID);
        $processID = NXCOM_filterInt($processID);
        if ($this->_debug ) {
            COM_errorLog("_nfNextStep: Queueid: $queueID, Processid: $processID");
        }
        // using the queueid and the processid, we are able to create or generate the
        // next step or the regenerated next step in a new process
        $thisDate = date('Y-m-d H:i:s' );
        $sql  = "SELECT  c.nf_templateDataTo FROM {$_TABLES['nfqueue']} a, {$_TABLES['nftemplatedatanextstep']} c ";
        $sql .= "WHERE a.nf_templateDataid=c.nf_templateDataFrom AND a.nf_processID='$processID' AND a.id='$queueID'";
        $nextTaskResult = DB_query($sql );
        $nextTaskRows = DB_numRows($nextTaskResult );

        if ($nextTaskRows == 0 ) {
            // echo "no rows! qid:" . $queueID . " procid:" . $processID . "<HR>";
            // if there are no rows for this specific QueueID and nothing for this processID, there's no next task
            $this->archive_task($queueID);
            $sql = "UPDATE {$_TABLES['nfprocess']} set complete=1, completedDate='{$thisDate}' where id=$processID";
            $updateQuery = DB_query($sql );

        } else { // we've got tasks
            for($nextStepCntr = 0;$nextStepCntr < $nextTaskRows;$nextStepCntr++ ) {
                $C = DB_fetchArray($nextTaskResult );
                if ($this->_debug ) {
                    COM_errorLog("Got tasks  qid: $queueID. procid: $processID and Next taskid: {$C[0]}");
                }
                // if statement to check if the next template id is null
                // this is a catch all scenario to ensure that if we're on the last task and it points to null, that we end it properly
                if ($C[0] == null or $C[0] == '' ) {
                    // echo "thinks the process is done..  qid:" . $queueID . " procid:" . $processID . "<HR>";
                    // Process is done, set the process status to complete and archive queue item
                    $this->archive_task($queueID);
                    $sql = "UPDATE {$_TABLES['nfprocess']} set complete=1, completedDate='{$thisDate}' where id=$processID";
                    $updateQuery = DB_query($sql );
                } else {
                    if ($this->_debug ) {
                        COM_errorLog("Next step qid:$queueID, procid:$processID");
                    }
                    // we have a next step, thus we can archive the queue item and also insert a
                    // new queue item with the next step populated as the next templatestepid
                    $sql  = "SELECT * FROM {$_TABLES['nfqueue']} a ";
                    $sql .= "WHERE a.nf_processid='{$processID}' ";
                    $sql .= "AND a.nf_templateDataid='{$C[0]}'";
                    $updateQuery = DB_query($sql );
                    $updateQueryRows = DB_numRows($updateQuery );
                    $retrieveQueryArray = DB_fetchArray($updateQuery );
                    if ($updateQueryRows == 0 ) {
                        // no next item in the queue.. just create it
                        $sql = "INSERT INTO {$_TABLES['nfqueue']} (nf_processID, nf_templateDataID, status, createdDate) ";
                        $sql .= " values ('{$processID}','{$C[0]}',0,'{$thisDate}')";
                        $updateQuery = DB_query($sql );
                        $newTaskid = DB_insertID();
                        if ($this->_debug ) {
                            $logmsg  = "Nexflow: New queue id (3) : $newTaskid - Template Taskid: {$C[0]} - ";
                            $logmsg .= "Assigned to " . COM_getDisplayName(nf_getTaskOwner($C[0],$processID));
                            nf_notificationLog($logmsg);
                        }
                        $newTaskAssignedUsers = $this->private_getAssignedUID($newTaskid);
                        if (is_array($newTaskAssignedUsers) AND count($newTaskAssignedUsers) > 0) {
                            $this->assign_task($newTaskid,$newTaskAssignedUsers);
                        }

                        // Determine if task has a reminder set and if so then update the nextReminderTime field in the new queue record
                        $reminderInterval = DB_getItem($_TABLES['nftemplatedata'],'reminderInterval',"id='{$C[0]}'");
                        if ($reminderInterval > 0) {
                            DB_query("UPDATE {$_TABLES['nfqueue']} SET nextReminderTime=DATE_ADD( NOW(), INTERVAL $reminderInterval DAY) where id='$newTaskid'");
                        }
                        DB_query("INSERT INTO {$_TABLES['nfqueuefrom']} (queueID,fromQueueID) values ('$newTaskid','{$queueID}')");

                        $this->archive_task($queueID);

                        // Check if notification has been defined for new task assignment
                        $this->private_sendTaskAssignmentNotifications();

                    } else {
                        // we have a situation here where the next item already exists.
                        // need to determine if the next item has a regeneration flag.
                        // if there is a regeneration flag, then create a new process starting with that regeneration flagged item
                        $regenResult = DB_query("SELECT * FROM {$_TABLES['nftemplatedata']} a where a.id='{$C[0]}'");
                        $regenCount = DB_numRows($regenResult );
                        $regenArray = DB_fetchArray($regenResult );

                        $toRegenerate = $regenArray['regenerate'];
                        $template = $regenArray['nf_templateID'];

                        if ($toRegenerate ) {
                            // regenerate the same process starting at the next step
                            // set the current process' complete status to 2.. 0 is active, 1 is done, 2 is has children
                            $this->newprocess($template, $C[0], $processID );
                            $this->archive_task($queueID);

                        } else{
                            //no regeneration  we're done
                            $toQueueID = $retrieveQueryArray['id'];
                            $sql = "INSERT INTO {$_TABLES['nfqueuefrom']} (queueID,fromQueueID) values ('{$toQueueID}','{$queueID}')";
                            $updateQuery = DB_query($sql );
                            $this->archive_task($queueID);

                            $sql = "SELECT * FROM {$_TABLES['nfqueue']} a WHERE a.nf_processid='{$processID}' AND a.nf_templateDataid='{$C[0]}'";
                            $updateQuery = DB_query( $sql );
                            $updateQueryRows = DB_numRows($updateQuery);
                            if($updateQueryRows == 0){
                                $sql = "UPDATE {$_TABLES['nfprocess']} SET complete=1, completedDate='{$thisDate}' WHERE id='{$processID}'";
                                $updateQuery = DB_query( $sql );
                                }
                            }
                        }  //end else

                } //end else for the next step routine
            } //end for $nextstep
        } //end else portion for nextStepTest=0
    }


    /**
    * private_getAssignedUID Method
    * private getAssignedUID returns the UID(s) of the individuals who is/are assigned a task.
    * The difference between this function and getAssignedUID is that this function looks through
    * The assignment table to find out who is assigned.  This function should only be used on task assignment
    * Returns an array of records where the key is either 0 or the nf_processVariableID
    *
    * @param        int         $taskID   Task ID from the workflow queue table
    * @return       array       array of assignment records
    *
    */
    function private_getAssignedUID($taskID) {
        global $_TABLES;
        $taskID = NXCOM_filterInt($taskID);
        $assigned = array();

        $sql  = "SELECT a.nf_templateDataID, b.assignedByVariable, c.is_interactiveStepType FROM {$_TABLES['nfqueue']} a ";
        $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} b on a.nf_templateDataID=b.id ";
        $sql .= "LEFT JOIN {$_TABLES['nfsteptype']} c on b.nf_stepType=c.id ";
        $sql .= "WHERE a.id=$taskID";
        $query = DB_query($sql);

        list ($templateDataID,$assignedByVariable,$isStepInteractive) = DB_fetchArray($query);
        if ($isStepInteractive) { // Only need to create assignment records for interactive tasks

            if($assignedByVariable == 1 || $assignedByVariable == true) {
                $processID=DB_getItem($_TABLES['nfqueue'],'nf_processID',"id='{$taskID}'");
                $sql  = "SELECT a.nf_processVariable, b.variableValue FROM {$_TABLES['nftemplateassignment']} a ";
                $sql .= "LEFT JOIN {$_TABLES['nfprocessvariables']} b ON b.nf_templateVariableID=a.nf_processVariable ";
                $sql .= "WHERE a.nf_templateDataID=$templateDataID AND b.nf_processID=$processID;";
                $res = DB_query($sql);
                while ($A = DB_fetchArray($res)) {
                    $assigned[$A['nf_processVariable']] = $A['variableValue'];
                }
            } else {
                $sql = "SELECT uid FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID=$templateDataID AND uid is not NULL";
                $res = DB_query($sql);
                $nrows = DB_numRows($res);

                /* Create an array of assignment records - but if there are multple assignments for this task by user then
                 * we need to create multiple array records but can not have multiple array records with a key of 0
                 * In this case, use a negative key value. Any non positive value is therefore not a variableID and
                 * can be assumed to be an assignment by user record
                */
                for($cntr=0;$cntr<$nrows;$cntr++) {
                    $A = DB_fetchArray($res);
                    $assigned[-$cntr] = $A['uid'];  // Possible negative value for key if multiple assignments
                }
            }

            if (count($assigned) == 0) {
                // Valid interactive task that should have an assignment record
                $assigned[0] = 0;
            }
        }

        return $assigned;
    }

    // this function is responsible for setting the status of the
    // queue item from 0 to 2.  must have the queueid to do this.
    function hold_task($queueID, $ignoreUID=false) {
        global $_TABLES;

        $queueID = NXCOM_filterInt($queueID);
        $this->_currentQueueID = $queueID;
        if ($this->_debug ) {
            COM_errorLog("Nexflow: hold_task- updating queue item: $queueID");
        }

        if($ignoreUID){
            DB_query("UPDATE {$_TABLES['nfqueue']} set status=2 where id='$queueID'");
        }else{
            if ($this->_nfUserId == '' or $this->_nfUserId == null ) {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid=NULL, status=2 where id='$queueID'");
            } else {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid='{$this->_nfUserId}',status=2 where id='$queueID'");
            }
        }
        //now we need to create the new project record - if it exists.
        $ifExists=DB_getItem($_TABLES['nfproject_taskhistory'],"id","task_id={$queueID} AND status=0");
        $ifExists=NXCOM_filterInt($ifExists);
        if($ifExists>0){//we have a valid taskhistory record.. thus this is a project tracked workflow
            $theTime=time();
            $sql ="UPDATE {$_TABLES['nfproject_taskhistory']} SET status=2, date_started={$theTime}, date_completed=0 WHERE id={$ifExists}";
            $res=DB_query($sql);

        }
    }

    // this function is responsible for setting the status of the
    // queue item from 2 to 0.  must have the queueid to do this.
    function unhold_task($queueID, $ignoreUID=false) {
        global $_TABLES;

        $queueID=NXCOM_filterInt($queueID);
        $this->_currentQueueID = $queueID;
        if ($this->_debug ) {
            COM_errorLog("Nexflow: unhold_task- updating queue item: $queueID");
        }

        if($ignoreUID){
            DB_query("UPDATE {$_TABLES['nfqueue']} set status=0 where id='$queueID'");
        }else{
            if ($this->_nfUserId == '' or $this->_nfUserId == null ) {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid=NULL, status=0 where id='$queueID'");
            } else {
                DB_query("UPDATE {$_TABLES['nfqueue']} set uid='{$this->_nfUserId}',status=0 where id='$queueID'");
            }
        }
        //now we need to create the new project record - if it exists.
        $ifExists=DB_getItem($_TABLES['nfproject_taskhistory'],"id","task_id={$queueID} AND status=2 ORDER BY id DESC LIMIT 1");
        $ifExists=NXCOM_filterInt($ifExists);
        if($ifExists>0){//we have a valid taskhistory record.. thus this is a project tracked workflow
            $theTime=time();
            $sql  ="INSERT INTO {$_TABLES['nfproject_taskhistory']} (project_id,process_id,task_id,assigned_uid,date_assigned,date_started,date_completed,status) ";
            $sql  .="SELECT project_id,process_id,task_id,assigned_uid,{$theTime},0,0,0 ";
            $sql  .="FROM {$_TABLES['nfproject_taskhistory']} WHERE id={$ifExists}";
            $res=DB_query($sql);
            $newEntry=DB_insertId();
            $sql ="UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed={$theTime} WHERE id={$ifExists}";
            $res=DB_query($sql);

        }

    }

    //this method places all of the specified process' tasks into a status of 2
    function hold_process($pid) {
        global $_TABLES;

        $pid = NXCOM_filterInt($pid);
        if ($this->_debug ) {
            COM_errorLog("Nexflow: hold_process- updating process: $pid");
        }

        $sql = "SELECT id FROM {$_TABLES['nfqueue']} where nf_processID={$pid} and (status=0 or status is NULL) and (archived=0 or archived is null)";
        $res = DB_query($sql);
        $nrows = DB_numRows($res);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A = DB_fetchArray($res);
            $this->hold_task($A['id'],true);
            //$sql = "UPDATE {$_TABLES['nfqueue']} set status=2 where id={$A['id']}";
            //DB_query($sql);
        }
        $sql = "UPDATE {$_TABLES['nfprocess']} set complete=3 where id={$pid}";
        DB_query($sql);
    }


    //this method places all of the specified process' tasks from 2 to 0
    function unhold_process($pid) {
        global $_TABLES;

        $pid = NXCOM_filterInt($pid);
        if ($this->_debug ) {
            COM_errorLog("Nexflow: unhold_process- updating process: $pid");
        }

        $sql = "SELECT id FROM {$_TABLES['nfqueue']} where nf_processID={$pid} and (status=2) and (archived=0 or archived is null)";
        $res = DB_query($sql);
        $nrows = DB_numRows($res);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A = DB_fetchArray($res);
            //$sql = "UPDATE {$_TABLES['nfqueue']} set status=0 where id={$A['id']}";
            //DB_query($sql);
            $this->unhold_task($A['id'], true);

        }
        $sql = "UPDATE {$_TABLES['nfprocess']} set complete=0 where id={$pid}";
        DB_query($sql);

    }


    //set display name dyamic area
    //method to programmatically set the project table's description
    //need the processID and the textual information you want to set the column to
    function setRequestTitle($processid,$txt) {
        global $_TABLES;
        $processid = NXCOM_filterInt($processid);
        if($txt != '' && $processid > 0) {
            $sql = "UPDATE {$_TABLES['nfprojects']} set description='{$txt}' where wf_process_id={$processid}";
            DB_query($sql);
        }
    }



    //method to programmatically set the process' customFlowName column value
    //need the processID and the textual information you want to set the column to
    //this column is used in the myflows and all flows view to dynamically append this column's data to it's title
    function setCustomFlowName($processid,$txt) {
        global $_TABLES;
        $processid = NXCOM_filterInt($processid);
        if($txt != '' && $processid > 0){
            $sql = "UPDATE {$_TABLES['nfprocess']} set customFlowName='{$txt}' where id={$processid}";
            DB_query($sql);
        }
    }

}//end class




?>