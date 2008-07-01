<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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

function nf_makeDropDownWithSelected($idCol, $displayCol, $table, $matchvalue,$where='',$suppressSelectTag=0){
    $retval = '';
    $matched = 0;
    $result = DB_query( "SELECT $idCol,$displayCol FROM $table $where" );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ ){
        $A = DB_fetchArray($result);
        $retval .='<option value="' . $A[0] . '"';
        if($matchvalue == $A[0]){
            $retval .=" selected=\"selected\" ";
            $matched=1;
            }
        $retval .='>' . $A[1] . '</option>';
        }
        if(!$suppressSelectTag){
            $retval .= '</select>';
        }
    if($matched){
        if(!$suppressSelectTag){
            $retval = '<select name="' . str_replace(".","",$idCol . $displayCol) . '"><option value="' . $value . '" >' . $label . '</option>' . $retval;
        } else {
            $retval = '<option value="' . $value . '" >' . $label . '</option>' . $retval;
        }
    } else{
        if(!$suppressSelectTag){
            $retval = '<select name="' . str_replace(".","",$idCol . $displayCol) . '"><option value="' . $value . '" >' . $label . '</option>' . $retval;
        } else {
            $retval = '<option value="' . $value . '" >' . $label . '</option>' . $retval;
         }
    }
    return $retval;
}

function nf_makeDropDownSqlSelected($selectedId,$selectedlabel,$selectedTable,$selectedcolumn,$idCol, $displayCol, $sql){

    $retval .= '<select name="' . str_replace(".","",$idCol) . str_replace(".","",$displayCol) . '" style="width:200" size=1>';
    if ($selectedId!='0' and $selectedId!='') {
        $result = DB_query("SELECT $selectedlabel from $selectedTable where $selectedcolumn=$selectedId");
        $nrows = DB_numRows($result);
        if($nrows > 0) {
            $A = DB_fetchArray($result);
            $retval .= '<option value="' . $selectedId . '" selected>' . $A[0] . '</option>';
        } else {
            $retval .= '<option value="" selected></option>';
        }
    } else {
       $retval .= '<option value="" selected></option>';
    }
    $result = DB_query("SELECT $idCol,$displayCol from $sql");
    $nrows = DB_numRows($result);

    for( $i = 0; $i < $nrows; $i++ ) {
        $A = DB_fetchArray( $result );
        $retval .='<option value="' . $A[0] . '">' . $A[1] . '</option>';
    }
    $retval .= '</select>';
    return $retval;
}


function nf_makeDropDownSql($idCol, $displayCol, $sql, $size){

    $retval .= '<select name="' . str_replace(".","",$idCol) . str_replace(".","",$displayCol) . '"  size=' . $size . ' >';
    $retval .= '<option value="" selected></option>';

    $result = DB_query("SELECT $idCol,$displayCol from $sql");
    $nrows = DB_numRows($result);

    for( $i = 0; $i < $nrows; $i++ ){
        $A = DB_fetchArray( $result );
        $retval .='<option value="' . $A[0] . '">' . $A[1] . '</option>';
    }
    $retval .= '</select>';
    return $retval;
}

function nf_makeDropDown($idCol, $displayCol, $table,$style=''){
    global $_TABLES;

    $retval = '';
    $retval .= '<select name="' . str_replace(".","",$idCol) . str_replace(".","",$displayCol) . '" style="width:200;' . $style . '" size=1>';
    $retval .= '<option value="" selected></option>';

    $result = DB_query("SELECT $idCol,$displayCol FROM $table");
    $nrows = DB_numRows($result);

    for( $i = 0; $i < $nrows; $i++ ){
        $A = DB_fetchArray( $result );
        $retval .='<option value="' . $A[0] . '">' . $A[1] . '</option>';
    }
    $retval .= '</select>';
    return $retval;
}


function getnextlid($templateid){
    global $_TABLES;

    $sql = "SELECT max(logicalID) FROM {$_TABLES['nftemplatedata']} WHERE nf_templateid=$templateid";
    $result = DB_Query($sql);
    $A = DB_FetchArray($result);
    if($A[0] == NULL) {
        $retval = 1;
    } else {
        $retval = $A[0] + 1;
    }
    return $retval;

}



function nfidtolid($nfid) {
    global $_TABLES;

    $sql = "SELECT logicalid FROM {$_TABLES['nftemplatedata']} WHERE id=$nfid";
    $result = DB_Query($sql);
    $A = DB_FetchArray($result);
    if($A[0] == NULL) {
        $retval = NULL;
    } else {
        $retval = $A[0];
    }
    return $retval;

}


// Convert Logical Task ID ( Task ID per template) to actual Task ID (nexflow id)
function lidtonfid($lid, $templateID) {
    global $_TABLES;
    $sql = "SELECT id FROM {$_TABLES['nftemplatedata']} WHERE nf_templateid={$templateID} AND logicalID={$lid}";
    $result = DB_Query($sql);
    $A = DB_FetchArray($result);
    if($A[0] == NULL){
        $retval = NULL;
    } else {
        $retval = $A[0];
    }
    return $retval;
}



//function to move a logical task down in the list.
function moveliddown($taskid){
    global $_TABLES;
    //check if there is a lid above this one..
    //also take into account if the lid above this one is the first task, this task must replace it as the first task..
    $templateid=DB_getItem( $_TABLES['nftemplatedata'], 'nf_templateID', "id=" . $taskid );
    $thisLid=DB_getItem( $_TABLES['nftemplatedata'], 'logicalid', "id=" . $taskid );

    $sql = "SELECT id,logicalID FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateid' AND logicalID > $thisLid ";
    $sql .= "ORDER BY logicalID ASC LIMIT 1";
    $query = DB_query($sql);

    //only perform work if we're not the last task already..
    if(DB_numRows($query) > 0){
        list ($nextID, $nextLID) = DB_fetchArray($query);
        $sql = "UPDATE {$_TABLES['nftemplatedata']} set logicalID='$thisLid' WHERE id='$nextID'";
        $result = DB_Query($sql);

        $sql = "UPDATE {$_TABLES['nftemplatedata']} set logicalID='$nextLID' WHERE id='$taskid'";
        $result = DB_Query($sql);

        if(DB_getItem($_TABLES['nftemplatedata'],'firstTask', "id=" . $taskid) == 1){
            $sql = "UPDATE {$_TABLES['nftemplatedata']} set firstTask=1 WHERE id='$nextID'";
            $result = DB_Query($sql);
            $sql = "UPDATE {$_TABLES['nftemplatedata']} set firstTask=0 where id='$taskid'";
            $result = DB_Query($sql);
         }
    }
}

//function to move a logical task up the list
function movelidup($taskid){
    global $_TABLES;
    //check if there is a lid above this one..
    //also take into account if the lid above this one is the first task, this task must replace it as the first task..
    $templateid=DB_getItem( $_TABLES['nftemplatedata'], 'nf_templateID', "id=" . $taskid );
    $thisLid=DB_getItem( $_TABLES['nftemplatedata'], 'logicalid', "id=" . $taskid );
    $sql = "SELECT id,logicalID FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateid' AND logicalID < $thisLid ";
    $sql .= "ORDER BY logicalID DESC LIMIT 1";
    $query = DB_query($sql);

    //only perform work if we're not the first task already..
    if(DB_numRows($query) > 0){
        list ($previousID, $previousLID) = DB_fetchArray($query);

        $sql = "UPDATE {$_TABLES['nftemplatedata']} set logicalID='$thisLid' WHERE id='$previousID'";
        $result = DB_Query($sql);

        $sql = "UPDATE {$_TABLES['nftemplatedata']} set logicalID='$previousLID' WHERE id='$taskid'";
        $result = DB_Query($sql);

        if(DB_getItem($_TABLES['nftemplatedata'],'firstTask', "id=" . $previousID) == 1){
            $sql = "UPDATE {$_TABLES['nftemplatedata']} set firstTask=1 WHERE id='$taskid'";
            $result = DB_Query($sql);
            $sql = "UPDATE {$_TABLES['nftemplatedata']} set firstTask=0 WHERE id='$previousID'";
            $result = DB_Query($sql);
        }
    }
}


/**
* nf_getAssignedUID function - Need to replace or merge with nf_getTaskOwner
* nf_getAssignedUID returns the UID(s) of the individual(s) who is/are assigned a task.
  This method takes into account the reassignment table as well as the template itself.
  Note: the output can be a single value or a comma separated list of values
*
* @param        int         $taskID    Task ID from the workflow queue table
* @return       mixed       returns the UID(s) of the individual(s) who is/are assigned a task
*
*/
function nf_getAssignedUID($taskID) {
    global $_TABLES;

    $assigned = array ();

    $sql = "SELECT uid FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$taskID";
    $res = DB_query($sql);
    if (DB_numRows($res) == 0 AND DB_count($_TABLES['nfqueue'],'id',$taskID)) {
        // Check if this is an interactive task
        $sql  = "SELECT is_interactiveStepType FROM {$_TABLES['nfqueue']} a ";
        $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} b on a.nf_templateDataID=b.id ";
        $sql .= "LEFT JOIN {$_TABLES['nfsteptype']} c on b.nf_stepType=c.id ";
        $sql .= "WHERE a.id=$taskID";
        list ($isStepInteractive) = DB_fetchArray(DB_query($sql));
        if ($isStepInteractive == 1) {
            // Valid interactive task that should have an assignment record
            // but there is no current production AssignmentRecord
            // maybe I should create a ProdAssignment record but for now return an array with 0 for the assigned UID
            $assigned[] = 0;
        }
    } else {
        while ($A = DB_fetchArray ($res)) {
            $assigned[] = $A['uid'];
        }
    }

    return $assigned;
}

/**
* nf_reassign_task function
*
* @param        int         $uid                    User ID to re-assign task to
* @param        int         $taskID                 Task ID from the workflow queue table
* @param        int         $currentAssignedUID     Current assigned user - required so we update the correct prodAssignment record
* @return       n/a         No return
*
*/
function nf_reassign_task($queue_id,$assign_uid,$current_uid,$variable_id) {
    global $_TABLES;
    /* Assignment Record has to exist - but there can be multiple for this workflow queue record (process task)
     * If the assign_uid is 0 then it's not presently assigned
     * If the variable is 0 then the task is assigned by UID and not by variable
    */

    // Check that user exists, is valid and status is an active user - else skip the re-assignment
    if ($assign_uid < 2 OR DB_getItem($_TABLES['users'],'status',"uid=$assign_uid") != 3) {
        COM_errorLog("nf_reassign_task - assignment to invalid user detected: UID:$assign_uid");
    } else {
        $sql  = "SELECT id,uid,assignBack_uid FROM {$_TABLES['nfproductionassignments']} WHERE task_id=$queue_id ";
        if ($variable_id > 0) {
            $sql .= " AND nf_processVariable=$variable_id";
        } else {
            $sql .= " AND uid=$current_uid AND nf_processVariable=0";
        }
        $query = DB_query($sql);
        if (DB_numRows($query) == 1) {
            $currentProdRec = DB_fetchArray($query);

            /* If the task has been re-assigned previously for this task, then we will now loose the originally assigned user */
            /* Need to now check if the to-be-assigned user is away and if so .. then assigned to their backup */
            $assignToUserID = nf_getAwayReassignmentUid($assign_uid);

            $sql  = "UPDATE {$_TABLES['nfproductionassignments']} SET uid=$assignToUserID, assignBack_uid=$current_uid ";
            $sql .= "WHERE id={$currentProdRec['id']}";
            DB_query($sql);
        } else {
            COM_errorLog("nf_reassign_task: No record found for: queueid:$queue_id, assign_uid:$assign_uid, current_uid:$current_uid, variable:$variable_id");
        }
    }

}


/* nf_revertToOriginalOwner function
* If this task was re-assigned, revert to original owner
*
* @param        int         $id           nf_productionAssignment Record ID
* @return       n/a         No return
*
*/
function nf_revertToOriginalOwner($id) {
    global $_TABLES;

    if (DB_count($_TABLES['nfproductionassignments'],'id',$id)) {
        $sql  = "SELECT uid,assignBack_uid FROM {$_TABLES['nfproductionassignments']} WHERE id=$id";
        $currentProdRec = DB_fetchArray(DB_query($sql));

        $sql  = "UPDATE {$_TABLES['nfproductionassignments']} SET uid={$currentProdRec['assignBack_uid']},assignBack_uid=0 ";
        $sql .= "WHERE id=$id ";
        DB_query($sql);
    }

}

/** nf_getAwayReassignmentUid function
 * Recursive function used to get the user id to re-assign tasks to if the users Out-of-Office feature is active
 * Need to use a recursive function since new user may also be away and linked to another user
 * Function will use the $arrusers variable to test for possible infinite loop
 *
 * @param        int         $userID            User ID to check who has been setup in their preferences to redirect tasks to is
 * @param        mixed       $arrusers          array of users passed by the function to be used to test and prevent a loop
 * @return       int         user id to re-assigned tasks to
 *
 */
function nf_getAwayReassignmentUid($userID,$arrusers='') {
    global $_TABLES;

    if ($arrusers == '') $arrusers = array();  // Initialize array that will be used to test we don't have users reassigning to each other
    $query = DB_query ("SELECT away_start,away_return,reassign_uid,is_active FROM {$_TABLES['nfuseraway']} WHERE uid = $userID");
    list ($datestart,$datereturn,$reassign_uid,$is_active) = DB_fetchArray ($query);
    // Check and see if re-assign user is trying to link back - prevent a infinite loop
    if (in_array($reassign_uid,$arrusers)) {
        COM_errorLog("nf_getAwayReassignmentUid - possible assignment loop detected: UserID:$userID, re-assignuid:$reassign_uid");
        return $userID;
    } else {
        $arrusers[] = $reassign_uid;
        if ($is_active == 1 AND time() > $datestart AND time() < $datereturn) {
            // This user is also away
            $reassignToUserID = nf_getAwayReassignmentUid($reassign_uid,$arrusers);
        } else {
            $reassignToUserID = $userID;
        }
        // Check that user exists, is valid and status is an active user - else return original user id
        if ($reassignToUserID < 2 OR DB_getItem($_TABLES['users'],'status',"uid=$reassignToUserID") != 3) {
            COM_errorLog("nf_getAwayReassignmentUid - assigment to invalid user detected: UserID:$userID, re-assignuid:$reassignToUserID");
            return $userID;
        } else {
            return $reassignToUserID;
        }
    }
}


/* Function will format the custom notification message replacing bbcode like tags */
function nf_formatEmailMessage($type,$tid,$qid,$user) {
    global $CONF_NF,$_TABLES,$_CONF;

    $sql = "SELECT taskname,prenotify_message,postnotify_message,reminder_message FROM {$_TABLES['nftemplatedata']} WHERE id='$tid'";
    list ($taskname,$premessage,$postmessage, $remindermessage) = DB_fetchArray(DB_query($sql));
    $message = '';
    switch ($type) {
        case 'prenotify':
            if (trim($premessage) == '') {
                $message = $CONF_NF['prenotify_default_message'];
            } else {
                $message = $premessage;
            }
            break;
        case 'postnotify':
            if (trim($postmessage) == '') {
                $message = $CONF_NF['postnotify_default_message'];
            } else {
                $message = $postmessage;
            }
            break;
        case 'reminder':
            if (trim($remindermessage) == '') {
                $message = $CONF_NF['reminder_default_message'];
            } else {
                $message = $remindermessage;
            }
            break;
        case 'escalation':
            $message = $CONF_NF['escalation_message'];
            break;
    }

    $dateassigned = DB_getItem($_TABLES['nfqueue'],'createdDate',"id='$qid'");
    $processid = DB_getItem($_TABLES['nfqueue'],'nf_processID',"id='$qid'");
    if ($processid > 0) {
        $nfclass = new nexflow($processid);
        $pid = $nfclass->get_ProcessVariable('PID');
    }
    if (!isset($pid) OR $pid < 1 ) {
        $projectName = 'unknown';
        $projectlink = 'N/A';
        $pid = 0;
    } else {
        $projectName = DB_getItem($_TABLES['nfprojects'],'description',"id=$pid");
        $projectlink = $CONF_NF['RequestDetailLink_URL'] . '?id=' . $pid . '?appmode=';
    }
    $taskowner = COM_getDisplayName(nf_getAssignedUID($tid));
    $link = $CONF_NF['TaskConsole_URL'];
    $search = array ('[taskname]','[taskowner]','[user]','[dateassigned]','[newline]','[here]','[project]','[projectname]','[projectlink]','[siteurl]');
    $replace = array ($taskname,$taskowner,$user,$dateassigned,"\n",$link,$pid,$projectName,$projectlink,$_CONF['site_url']);
    $message = str_replace($search,$replace,$message);

    if ($CONF_NF['debug']) {
        COM_errorLog("nf_formatEmailMessage => Type:$type, Message:$message");
    }

    return $message;

}

/* Function called to handle sending our Notification email messages */
function nf_sendEmail($email,$subject,$message) {
    global $_USER,$_CONF,$_TABLES, $CONF_NF;

    if ($CONF_NF['debug']) {
        COM_errorLog("Nexflow - Sending message to: $email, subject: $subject,Message: $message");
    }

    if (empty ($LANG_CHARSET)) {
        $charset = $_CONF['default_charset'];
        if (empty ($charset)) {
        $charset = "iso-8859-1";
        }
    } else {
        $charset = $LANG_CHARSET;
    }
    if ($CONF_NF['email_notifications_enabled']) {
        COM_mail($email, $subject, $message);
        nf_logNotification("Nexflow: $email, $subject");
    }
    return true;
}

function nf_logNotification($logentry) {
    global $_CONF;
    $timestamp = strftime( "%b %d %H:%M" );
    $logfile = $_CONF['path_log'] . 'notifiction.log';

    if( !$file = fopen( $logfile, a )) {
        COM_errorLog("Unable to write to notification logfile: $logfile");
    } else {
        fputs( $file, "$timestamp,$logentry \n" );
    }
}

function nf_logChange($logentry) {
    global $_CONF;
    $timestamp = strftime( "%b %d %H:%M" );
    $logfile = $_CONF['path_log'] . 'nexflowaudit.log';

    if( !$file = fopen( $logfile, a )) {
        COM_errorLog("Unable to write to notification logfile: $logfile");
    } else {
        fputs( $file, "$timestamp,$logentry \n" );
    }
}

function nf_listUsers($uid=0,$show_fullname=true) {
    global $_TABLES;

    $retval = '';
    if ($show_fullname) {
        $sql = "SELECT uid,username,fullname FROM {$_TABLES['users']} WHERE status=3 ORDER BY fullname ASC";
        $query = DB_query($sql);
        while (list($userid, $username,$fullname) = DB_fetchArray($query)) {
            if (trim($fullname) == '') $fullname = $username;
            if ($userid == $uid) {
                $retval .= '<option value="'.$userid.'" selected=selected>'.$fullname.'</option>';
            } else {
                $retval .= '<option value="'.$userid.'">'.$fullname.'</option>';
            }
        }

    } else {
        $sql = "SELECT uid,username FROM {$_TABLES['users']} WHERE status=3  ORDER BY username ASC";
        $query = DB_query($sql);
        while (list($userid, $username) = DB_fetchArray($query)) {
            if ($userid == $uid) {
                $retval .= '<option value="'.$userid.'" selected=selected>'.$username.'</option>';
            } else {
                $retval .= '<option value="'.$userid.'">'.$username.'</option>';
            }
        }
    }
    return $retval;
}


/**
* Get all related process id's for this project
* Recursive function that walks the process table to return all related processes
* For the initial project process - check and see if there are any child processes
*
* @param        int         $id             initialy contains the project id but on subsequent calls, contains a process id
* @param        array       $processes      array of related processes
* @return       mixed       returns an array of related processes or empty string
*
*/
function nf_GetAllRelatedProcesses($id,$processes='') {
    global $_TABLES;

    if (!is_array($processes)) {
        // If $processes is not set - then id is the project_id - need to get the initial process id
        $procid = DB_getItem($_TABLES['nfprojects'],'wf_process_id', "id=$id");
        if ($procid > 0) {
            $processes = array();
            $processes[] = $procid;
            $processes = nf_GetAllRelatedProcesses($procid,$processes);
            return $processes;
        } else {
            return '';
        }
    } else {
        // Check if there is another process whose parent "pid" is set
        $procid = DB_getItem($_TABLES['nfprocess'],'id', "pid=$id");
        if ($procid != 0) {
            $processes[] = $procid;
            $processes = nf_GetAllRelatedProcesses($procid,$processes);
            return $processes;
        } else {
            return $processes;
        }
    }
}


/**
* Function used to control the nunber of times the workflow engine needs to be called each instance
* The function determines the number of non-interactive tasks in the queue for uncompleted processes
* We orchestrator will call this function with an array of taskid's already attempted to complete.
* We want to run the orchestrator while we still have new tasks
* Initially, the array $processlist will be null
*
* @param        array       $processes      array of queue task ID's already related processes
* @return       mixed       returns an array of related processes or empty string
*
*/
function nf_getListofUncompletedTasks(&$processlist) {
    global $_TABLES;

    $retval = array();
    $plist = implode(',',$processlist);
    $sql = "SELECT distinct a.id ";
    $sql .= "FROM {$_TABLES['nfqueue']} a inner join {$_TABLES['nfprocess']} b on  a.nf_processId = b.id ";
    $sql .= "inner join {$_TABLES['nftemplatedata']} c on a.nf_templateDataId = c.id ";
    $sql .= "inner join {$_TABLES['nfprocess']} d on  a.nf_processId = d.id ";
    $sql .= "inner join {$_TABLES['nftemplate']} e on b.nf_templateId = e.id ";
    $sql .= "inner join {$_TABLES['nfsteptype']} h on c.nf_steptype = h.id ";
    $sql .= "WHERE d.complete=0 ";
    if (count($processlist) > 0) {
        $sql .= "AND a.id not in ($plist) ";
    }
    $sql .= " AND (a.status=0 AND a.archived is null AND h.is_interactiveStepType=0)";
    $query = DB_query($sql);
    $retval['count'] = DB_numRows($query);
    while (list($taskid) = DB_fetchArray($query)) {
        array_push($processlist,$taskid);
    }
    $retval['list'] = $processlist;
    return $retval;
}



/* Get the project result record id 'prjformid' and form (form_id and result_id) for this project
/* The key is the template task id - the optionalParm field lists the form id(s)
 * Returns an array
*/
function nf_getFormResult($templateTaskid,$projectid) {
    global $_TABLES;

    $retval = array();
    /* Retrieve the form for that this review task is defined for */
    // Potentially more then 1 form could be valid to verify - explode into an array
    // Get the latest result record in case there are more then 1 for the same form.
    $aforms = explode(',',DB_getItem($_TABLES['nftemplatedata'], 'optionalParm', "id={$templateTaskid}"));
    if (is_array($aforms)) {
        foreach ($aforms as $formid) {
            $sql  = "SELECT id,results_id FROM {$_TABLES['nfproject_forms']} ";
            $sql .= "WHERE project_id='$projectid' ";
            $sql .= "AND form_id='{$formid}' ";
            $sql .= "ORDER BY id DESC LIMIT 1";
            $query = DB_query($sql);
            if (DB_numRows($query) == 1) {
              list ($prjformid,$resultid) = DB_fetchArray($query);
              $retval['formid'] = $formid;
              $retval['prjformid'] = $prjformid;
              $retval['resultid'] = $resultid;
              break;
            }
        }
    }
    return $retval;
}

function nf_getFormResultData($result,$field_id) {
    global $_TABLES;

    $retval = '';
    // Check if the field is part of the result record - else it may be part of a related form (Dynamic Form)
    if (DB_count($_TABLES['formResData'],array(result_id,field_id),array($result,$field_id)) == 1) {
        $retval = DB_getItem($_TABLES['formResData'],'field_data',"result_id='$result' AND field_id='$field_id'");
    } elseif (DB_count($_TABLES['formResText'],array(result_id,field_id),array($result,$field_id)) == 1) {
        $retval = DB_getItem($_TABLES['formResText'],'field_data',"result_id='$result' AND field_id='$field_id'");
        $retval = stripslashes($retval);
    } else {
       // Check if there is a related form as part of this result
       $related = explode(',',DB_getItem($_TABLES['formResults'],'related_results',"id='$result'"));
       foreach ($related as $relatedResult) {
            if (DB_count($_TABLES['formResData'],array(result_id,field_id),array($relatedResult,$field_id)) == 1) {
                $retval = DB_getItem($_TABLES['formResData'],'field_data',"result_id='$relatedResult' AND field_id='$field_id'");
                break;
            }
       }
    }
    return $retval;

}

function nf_updateFormResultData($result,$id,$value) {
    global $_TABLES;

    if ($result > 0 AND $id > 0 AND $value != '') {
        // Check if the field is part of the result record - else it may be part of a related form (Dynamic Form)
        if (DB_count($_TABLES['formResData'],array(result_id,field_id),array($result,$id)) == 1) {
            DB_query("UPDATE {$_TABLES['formResData']} SET field_data = '$value' WHERE result_id='$result' AND field_id='$id'");
        } else {
           // Check if there is a related form as part of this result
           $related = explode(',',DB_getItem($_TABLES['formResults'],'related_results',"id='$result'"));
           foreach ($related as $relatedResult) {
                if (DB_count($_TABLES['formResData'],array(result_id,field_id),array($relatedResult,$id)) == 1) {
                    DB_query("UPDATE {$_TABLES['formResData']} SET field_data = '$value' WHERE result_id='$relatedResult' AND field_id='$id'");
                    break;
                }
           }
        }
    }
}



//this function is used in the libconsole.php file to get the task name consistent.
//since task names can be dynamic, this just returns a string of either the taskname OR the dynamic task name.
function nf_getFinalTaskName($taskid){
    global $_TABLES;

    $sql  ="SELECT a.nf_processID,b.isDynamicTaskName,b.dynamicTaskNameVariableID,b.taskname ";
    $sql .="FROM {$_TABLES['nfqueue']} a ";
    $sql .="INNER JOIN {$_TABLES['nftemplatedata']} b on a.nf_templateDataID=b.id ";
    $sql .="WHERE a.id={$taskid}";
    $res=DB_query($sql);
    list($processID,$isDynamicTaskName,$dynamicTaskNameVariableID,$taskname)=DB_fetchArray($res);
    if($isDynamicTaskName==1){
        $sql  = "SELECT variableValue FROM {$_TABLES['nfprocessvariables']} ";
        $sql .= "WHERE nf_processid='{$processID}' AND nf_templateVariableID='{$dynamicTaskNameVariableID}'";
        $res=DB_query($sql);
        list($taskname)=DB_fetchArray($res);
    }

    return $taskname;
}

?>