<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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
include_once("../lib-common.php");

$sortingvalue = '';
$profilSession = '';
$msg = '';
$sortingUser;

if (!empty($_USER['uid'])) {
    if ($sor_cible != "" && $sor_champs != "none") {
        $sortingvalue= $sor_champs . " " . $sor_ordre;
        DB_query("UPDATE {$_TABLES['prj_sorting']} SET $sor_cible='$sortingvalue' WHERE uid = '{$_USER['uid']}' ");
    }
    $results = DB_query("SELECT projects, tasks, team, home_projects, home_tasks, files, discussions, subtasks FROM {$_TABLES['prj_sorting']} WHERE uid = '{$_USER['uid']}'");
    if (DB_numrows($results) > 0) {
      list($sortingUser['project'], $sortingUser['tasks'], $sortingUser['team'], $sortingUser['home_projects'], $sortingUser['home_tasks'],$sortingUser['files'],$sortingUser['forum'], $sortingUser['subtasks']) = DB_fetchArray($results);
    } else {
        DB_query("INSERT INTO {$_TABLES['prj_sorting']} (uid) values('{$_USER['uid']}')");
    }
}



function getProjectToken ($id, $uid, $table) {
    if (empty($id) || empty($uid) || empty($table)){
        return 0;
    } elseif (SEC_inGroup('Root')) {
        return 1;
    } else {
        $token='';
        $result = DB_query("SELECT * FROM $table WHERE (pid=$id AND uid=$uid AND role LIKE 'o') OR (pid=$id AND uid=$uid AND role LIKE 's')");
        $token = DB_numRows($result);
        if (empty($token)) {
            return 0;
        } else {
            return 1;
        }
    }
}

function getTaskToken ($id, $uid, $table, $tasktable) {
    $parentid = DB_getItem ($tasktable, 'parent_task',  "tid = $id");
    if (empty($id) || empty($uid) || empty($table)){
        return 0;
    } elseif (SEC_inGroup('Root')) {
        return 1;
    } else {
        $token='';
        if ($parentid == 0) {
            $result1 = DB_query("SELECT * FROM $table WHERE tid=$id AND uid=$uid AND role LIKE 'o'");
            $token = DB_numRows($result1);
        } else {
            $result1 = DB_query("SELECT * FROM $table WHERE tid=$id AND uid=$uid AND role LIKE 'o'");
            $token = DB_numRows($result1);
        }
        if (empty($token)) {
            return 0;
        } else {
            return 1;
        }
    }
}

function getProjectMember ($id, $uid, $table) {
    if (empty($id) || empty($uid) || empty($table)){
        return 0;
    } elseif (SEC_inGroup('Root')) {
        return 1;
    } else {
        $token='';
        $result = DB_query("SELECT * FROM $table WHERE pid=$id AND uid=$uid");
        $token = DB_numRows($result);
        if (empty($token)){
            return 0;
        } else {
            return 1;
        }
    }
}

function selectBox($name, $array, $selected) {

    $display = '<select name='. $name . ' style="text-indent:0px;">';
    for( $i = 0; $i < count($array); $i++ )
    {

        $display .= '<option value="' . $i . '"';
        if( $i == $selected )
        {
            $display .= ' selected';
        }
        $display .= '>' . $array[$i] . '</option>' . LB;
    }

    $display .= '</select>';
    echo $display;
}


function checkBox($name, $checked) {

    $display = '<input type="checkbox" name='. $name;
      if( $checked == 'on' )
        {
            $display .= ' checked';
        }
        $display .= '>'  . LB;
     echo $display;
}



/* New Version of this plugin that allows you to just return the options
*  usefull when I want to use template files and define the SELECT Element in the template file
*  Blaine Lang: Mar 28/06
*/
function selectBox2($array, $selected='', $name='') {

    if ($name != '') {
        $retval = '<select name='. $name . ' style="text-indent:0px;">';
    } else {
        $retval = '';
    }
    for( $i = 0; $i < count($array); $i++ )
    {

        $retval .= '<option value="' . $i . '"';
        if( $i == $selected )
        {
            $retval .= ' selected';
        }
        $retval .= '>' . $array[$i] . '</option>' . LB;
    }
    if ($name != '') {
        $retval .= '</select>';
    }

    return  $retval;
}


function radioButton($name, $checked) {

    if( $checked == 'Y' ) {
            $display = '&nbsp;YES <input type="radio" name="' . $name .'" value="Y" checked>';
            $display .= '&nbsp;NO <input type="radio" name="' . $name .'" value="N">';
    } else {
            $display = '&nbsp;YES <input type="radio" name="' . $name .'" value="Y">';
            $display .= '&nbsp;NO <input type="radio" name="' . $name .'" value="N" checked>';
    }

    echo $display;
}


function prj_selectUsers($pid,$allusers=false) {
    global $_TABLES;

    /* Removed SQL code to match on location */
    $retval = '';
    if($allusers) {
        $result = DB_query( "SELECT uid, fullname FROM {$_TABLES['users']} ORDER BY fullname" );
        while(list($uid,$username) = DB_fetchArray($result)) {
            if( DB_count($_TABLES['prj_users'], array('pid','uid'), array($pid,$uid)) == 0 ) {
                $retval .= '<option value="' . $uid . '">'. $username . '</option>';
            }
        }
    } else {
        $result = DB_query( "SELECT user.uid, user.fullname, project.role from {$_TABLES['prj_users']} project LEFT join {$_TABLES['users']} user on project.uid=user.uid  WHERE project.pid='$pid'");
        while(list($uid,$username,$role) = DB_fetchArray($result)) {
            if( $role != 'o' ) {
                $retval .= '<option value="' . $uid . '">'. $username . '</option>';
            }
        }
    }
    return $retval;
}


function pm_getdate () {
        // Get current time
        $month = date('m',time());
        $day = date('d',time());
        $year = date('Y',time());
        $hour = date('H', time());
        $min = date('i', time());

        // Convert to a unix timestamp and convert back
        return $createdate = mktime($hour,$min,0,$month,$day,$year);
}

function pm_convertdate($date) {
    if (trim($date) == '' or $date == 0) {
        return pm_getdate();
    }
    $tok = strtok($date," /-\\:");
    while ($tok !== FALSE) {
        $toks[] = $tok;
        $tok = strtok(" /-\\:");
    }

    if ($toks[0] == 1969) {  //  Assume this is an invalid date - return today
        return pm_getdate();
    } else {
        return $timestamp = mktime(0,0,0,$toks[1],$toks[2],$toks[0]);
        //return strtotime($date);
    }
}


/**
* Constructs a mySQL part of a statement based on the filter information currently in the data base.
*
* @param        string/int        $flid        Filter ID from prj_filters table.
* @return        string                        A SQL FROM and WHERE clauses.
*
*/
function prj_constructFilter($flid)
{
    global $_TABLES;

    $thingey = DB_query("SELECT * FROM ".$_TABLES['prj_filters']." WHERE flid = ".$flid);
    if (DB_numRows($thingey) == 0) {
        return "";
    }
    $filter = DB_fetchArray($thingey);

    $retval = " FROM ".$_TABLES['prj_projects']." {$_TABLES['prj_projects']} LEFT JOIN ".$_TABLES['prj_users']." {$_TABLES['prj_users']} ";
    $retval .= "ON {$_TABLES['prj_users']}.pid={$_TABLES['prj_projects']}.pid LEFT JOIN ".$_TABLES['users']." {$_TABLES['users']} ";
    $retval .= "ON {$_TABLES['prj_users']}.uid={$_TABLES['users']}.uid LEFT JOIN ".$_TABLES['prj_department']." {$_TABLES['prj_department']} ";
    $retval .= "ON {$_TABLES['prj_department']}.pid={$_TABLES['prj_projects']}.pid LEFT JOIN ".$_TABLES['prj_location']." {$_TABLES['prj_location']} ";
    $retval .= "ON {$_TABLES['prj_location']}.pid={$_TABLES['prj_projects']}.pid LEFT JOIN ".$_TABLES['prj_projPerms']." {$_TABLES['prj_projPerms']} ";
    $retval .= "ON {$_TABLES['prj_projPerms']}.pid={$_TABLES['prj_projects']}.pid LEFT JOIN ".$_TABLES['prj_category']." {$_TABLES['prj_category']} ";
    $retval .= "ON {$_TABLES['prj_category']}.pid={$_TABLES['prj_projects']}.pid WHERE 1=1 ";
    $retval .= "AND {$_TABLES['prj_users']}.role = 'o' ";

    if (!empty($filter['projects'])) {
        $retval .= "AND {$_TABLES['prj_projects']}.pid in (".$filter['projects'].") ";
    }


    if (!empty($filter['employees'])) {
        $retval .= "AND {$_TABLES['prj_users']}.uid in (".$filter['employees'].") ";
    }

    if (!empty($filter['objective'])) {
        $retval .= "AND objective_id in (".$filter['objective'].") ";
    }

    if (!empty($filter['location'])) {
        $retval .= "AND location_id in (".$filter['location'].") ";
    }

    if (!empty($filter['category'])) {
        $retval .= "AND category_id in (".$filter['category'].") ";
    }

    if (!empty($filter['department'])) {
        $retval .= "AND {$_TABLES['prj_department']}.department_id in (".$filter['department'].") ";
    }

    return array ("clause" => $retval, "name" => $filter['name']);
}


function prj_statuslog ($pid, $uid, $tid, $comment, $date, $table) {
    if (empty($pid) || empty($uid) || empty($table)){
        return false;
    }
    $comment = trim(prj_preparefordb($comment));
    if ($comment != '') {
        if (empty($tid)) {
            DB_query( "INSERT INTO $table (pid, description, uid, updated) VALUES ($pid,'$comment',$uid, $date)");
        } else {
            DB_query( "INSERT INTO $table (pid, description, uid, updated, tid) VALUES ($pid,'$comment',$uid, $date, $tid)");
        }
    }
    return true;
}


function prj_logNotification($logentry) {
    global $_CONF;
    $timestamp = strftime( "%b %d %H:%M" );
    $logfile = $_CONF['path_log'] . 'notifiction.log';

    if( !$file = fopen( $logfile, a )) {
        $retval .= "{$LANG01[33]} {$logfile} ({$timestamp})<br>\n";
    } else {
        fputs( $file, "$timestamp,$logentry \n" );
    }

}


/**
* Send out notifications to all users that have subscribed to this file to file category
* Will check user preferences for notification if Messenger Plugin is installed
* @param        string      $pid        Project ID
* @param        string      $tid        Task ID
* @param        string      $msgid      Message type
* @return       Boolean     Returns True if atleast 1 message was sent out
**/
function prj_sendNotification($pid, $tid, $msgid="1")
{
    global $_CONF,$_TABLES,$_USER, $REMOTE_ADDR, $_FMCONF,$LANG_PRJ01;

    if (empty($pid)) {
            $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid',  "tid = $tid");
    }

    switch ( $msgid ) {
    case "1":         // Project Editted
        //alert only those discretely listed users in the proj permissions table
        $query = DB_query("SELECT project.pid,project.name,project.cid,users.uid FROM {$_TABLES['prj_projects']} project, {$_TABLES['prj_users']} users WHERE project.pid={$pid} and project.pid=users.pid AND users.role='o'");
        list($identifier,$name,$cid,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_projPerms']} WHERE pid=$pid  ");
        $SECONDARYqueryNotifyUsers=FALSE;
        break;

    case "2":               // Project Created
        //alert only those discretely listed in the proj permissions table
        $query = DB_query("SELECT project.pid,project.name,project.cid,users.uid FROM {$_TABLES['prj_projects']} project, {$_TABLES['prj_users']} users WHERE project.pid={$pid} and project.pid=users.pid AND users.role='o'");
        list($identifier,$name,$cid,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_projPerms']} WHERE pid=$pid");
        $SECONDARYqueryNotifyUsers=FALSE;
        break;

    case "3":          // Task Created - send only to task owner and project manager
        $query = DB_query("SELECT task.tid,task.name,users.uid FROM {$_TABLES['prj_tasks']} task, {$_TABLES['prj_task_users']} users WHERE task.tid={$tid} and task.tid=users.tid AND users.role='o'");
        list($identifier,$name,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_task_users']} WHERE {$_TABLES['prj_task_users']}.tid=$tid AND {$_TABLES['prj_task_users']}.role='o'");
        //now get any PMs from the perms table too.....
        $SECONDARYqueryNotifyUsers=DB_query("SELECT uid FROM {$_TABLES['prj_projPerms']} WHERE pid=$pid and fullAccess=1");
        break;

    case "4":          // Task Created - send only project manager
        $query = DB_query("SELECT task.tid,task.name,users.uid FROM {$_TABLES['prj_tasks']} task, {$_TABLES['prj_task_users']} users WHERE task.tid={$tid} and task.tid=users.tid AND users.role='o'");
        list($identifier,$name,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_projPerms']} WHERE pid=$pid AND fullAccess=1" );
        $SECONDARYqueryNotifyUsers=FALSE;
        break;

    case "5":          // Task Modified - send only to task owner and project manager
        $query = DB_query("SELECT task.tid,task.name,users.uid FROM {$_TABLES['prj_tasks']} task, {$_TABLES['prj_task_users']} users WHERE task.tid={$tid} and task.tid=users.tid AND users.role='o'");
        list($identifier,$name,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_task_users']} WHERE {$_TABLES['prj_task_users']}.tid=$tid AND {$_TABLES['prj_task_users']}.role='o'");
        $SECONDARYqueryNotifyUsers=DB_query("SELECT uid FROM {$_TABLES['prj_projPerms']} WHERE pid=$pid and fullAccess=1");
        break;

    case "6":          // Task Modified - send only project manager
        $query = DB_query("SELECT task.tid,task.name,users.uid FROM {$_TABLES['prj_tasks']} task, {$_TABLES['prj_task_users']} users WHERE task.tid={$tid} and task.tid=users.tid AND users.role='o'");
        list($identifier,$name,$submitter) = DB_fetchARRAY($query);
        $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['prj_users']} WHERE {$_TABLES['prj_users']}.pid=$pid AND {$_TABLES['prj_users']}.role='o'");
        break;

    }

    if (DB_numRows($queryNotifyUsers) > 0) {

        while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
            $target_users[] = $uid;
        }

        if($SECONDARYqueryNotifyUsers!==FALSE){
            while ( list($uid) = DB_fetchARRAY($SECONDARYqueryNotifyUsers)) {
                $target_users[] = $uid;
            }
        }

        // Sort the array so that we can check for duplicate user notification records
        sort($target_users);
        reset($target_users);

        // Send out Notifications to all users on distribution
        $lastuser = "";
        foreach ($target_users as $target_user) {
            if ($target_user != $lastuser) {
                $target_username = DB_getItem($_TABLES['users'],"username", "uid='{$target_user}'");
                $target_fullname = DB_getItem($_TABLES['users'],"fullname", "uid='{$target_user}'");

                if ($msgid == "1" || $msgid == "2" ) {
                    $subject = '' . $_CONF['site_name'] . ' ' .$LANG_PRJ01[$msgid]['SUBJECT']. '';
                    $submitter_name = DB_getItem($_TABLES['users'],"fullname", "uid=$submitter");
                    $message  = $LANG_PRJ01[$msgid]['HELLO'] .' '. $target_fullname . ",\n\n";
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE1'], $submitter_name,$name);
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE2'], $_CONF['site_url'] . '/nexproject/viewproject.php?mode=view&id=' .$identifier);
                    $message .= $LANG_PRJ01[$msgid]['LINE3'];
                    $message .= $LANG_PRJ01[$msgid]['LINE4'] . $LANG_PRJ01[$msgid]['ADMIN']."\n";
                    $message .= $_CONF[site_url] . "\n";
                } elseif ($msgid == "3" || $msgid == "4" ) {
                    $subject = '' . $_CONF['site_name'] . ' ' .$LANG_PRJ01[$msgid]['SUBJECT']. '';
                    $submitter_name = DB_getItem($_TABLES['users'],"fullname", "uid=$submitter");
                    $message  = $LANG_PRJ01[$msgid]['HELLO'] .' '. $target_fullname . ",\n\n";
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE1'], $submitter_name,$name);
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE2'], $_CONF['site_url'] . '/nexproject/viewproject.php?mode=view&id=' .$identifier);
                    $message .= $LANG_PRJ01[$msgid]['LINE3'];
                    $message .= $LANG_PRJ01[$msgid]['LINE4'] . $LANG_PRJ01[$msgid]['ADMIN']."\n";
                    $message .= $_CONF[site_url] . "\n";
                } else {
                    $subject = '' . $_CONF['site_name'] . ' ' .$LANG_PRJ01[$msgid]['SUBJECT']. '';
                    $submitter_name = DB_getItem($_TABLES['users'],"fullname", "uid=$submitter");
                    $message  = $LANG_PRJ01[$msgid]['HELLO'] .' '. $target_fullname . ",\n\n";
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE1'], $name, $submitter_name);
                    $message .= sprintf($LANG_PRJ01[$msgid]['LINE2'], $_CONF['site_url'] . '/nexproject/viewproject.php?mode=view&id=' .$identifier);
                    $message .= $LANG_PRJ01[$msgid]['LINE3'];
                    $message .= $LANG_PRJ01[$msgid]['LINE4'] . $LANG_PRJ01[$msgid]['ADMIN']."\n";
                    $message .= $_CONF[site_url] . "\n";
                }

                    prj_sendEmail($target_username,$subject,$message);

            }
            $lastuser = $target_user;
        }
        return true;
    } else {
        return false;
    }
}

function prj_sendEmail($user,$subject,$message) {
    global $_USER,$_CONF,$_TABLES, $_PRJCONF;
    $target_uid = DB_getItem($_TABLES['users'],"uid", "username='{$user}'");
    $emailaddress = DB_getItem($_TABLES['users'],"email", "username = '$user'");
    $emailtest=COM_isEmail($emailaddress);
    if ( $_PRJCONF['notifications_enabled']  && $emailtest ) {
        COM_mail($emailaddress,$subject,$message,$_CONF['site_mail'],false);
        // Log notification for admin viewing and tracking
        $type = "projects";
        $logentry = $type ."," .$user ."," .$subject;
        prj_logNotification($logentry);
    }
    return true;
}

function prj_showtaskBlock(&$mainblock,&$block, $pid,$taskid,$level=1) {
    global $_CONF,$_TABLES,$subTaskImg,$priority,$status,$strings,$progress;

    $sql = "SELECT {$_TABLES['prj_tasks']}.tid, {$_TABLES['prj_tasks']}.progress_id, ";
    $sql .= "{$_TABLES['prj_tasks']}.status_id, {$_TABLES['prj_tasks']}.priority_id, {$_TABLES['prj_tasks']}.name, ";
    $sql .= "{$_TABLES['users']}.fullname, {$_TABLES['prj_tasks']}.parent_task, {$_TABLES['prj_tasks']}.last_updated ";
    $sql .= "FROM {$_TABLES['prj_tasks']}, {$_TABLES['prj_task_users']},  {$_TABLES['users']} ";
    $sql .= "WHERE {$_TABLES['prj_tasks']}.pid=$pid AND {$_TABLES['prj_task_users']}.tid={$_TABLES['prj_tasks']}.tid ";
    $sql .= "AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_task_users']}.role='o' ";
    $sql .= "AND {$_TABLES['prj_tasks']}.parent_task=$taskid ORDER BY $block->sortingValue";
    $result = DB_query($sql);

    for ($j = 0; $j < DB_numrows($result); $j++) {
        list($listsubTasks->pro_id[$j], $listsubTasks->pro_seq[$j],
            $listsubTasks->pro_progress[$j], $listsubTasks->pro_status[$j],
            $listsubTasks->pro_priority[$j], $listsubTasks->pro_name[$j],
            $listsubTasks->username[$j], $listsubTasks->parent[$j], $listsubTasks->lastupdated[$j]) = DB_fetchArray($result);

        if (strlen($listTask->username[$j]) > 15 ) {
            $listTask->username[$j] = substr($listTask->username[$j],0,12) . "..";
        }
        if (strlen($listsubTasks->pro_name[$j]) > 35 ) {
            $listsubTasks->pro_name[$j] = substr($listsubTasks->pro_name[$j],0,35) . "....";
        }
        if ($listsubTasks->pro_org_id[$j] == "1") {
           $listsubTasks->pro_org_name[$j] = $strings["none"];
        }
        $idsubStatus = $listsubTasks->pro_status[$j];
        $idsubPriority = $listsubTasks->pro_priority[$j];
        $idsubProgress = $listsubTasks->pro_progress[$j];
        $block->openRow();
        $block->checkboxRow($listsubTasks->pro_id[$j]);
        $movetaskicon = '<img src="'.$_CONF['layout_url'] .'/nexproject/movetask2.gif">';
        $indent = '';
        for ($i = 1; $i < $level; $i++) {
             $indent .= '&nbsp;&nbsp;';
        }
        $indent .= $subTaskImg;
        $block->cellRow($mainblock->buildLink($_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id=".$listsubTasks->pro_id[$j], $subTaskOrderImg . $listsubTasks->pro_seq[$j],"in"));
        $block->cellRow($indent . $mainblock->buildLink($_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id=".$listsubTasks->pro_id[$j],$listsubTasks->pro_name[$j],"in"));
        $block->cellRow('<a href="#">[Up]</a>&nbsp;<a href="#">[Down]</a>&nbsp;<a href="#">[Left]</a>&nbsp;<a href="#">[Right]</a>');
        //$block->cellRow($priority[$idsubPriority]);
        $block->cellProgress($progress[$idsubProgress]);
        $block->cellRow($status[$idsubStatus]);
        $block->cellRow(strftime("%m-%d %H:%M", $listsubTasks->lastupdated[$j]));
        $block->cellRow($listsubTasks->username[$j]);
        $block->closeRow();
        if (DB_count($_TABLES['prj_tasks'],'parent_task',$listsubTasks->pro_id[$j] ) > 0) {
            prj_showtaskBlock($mainblock,$block, $pid,$listsubTasks->pro_id[$j],$level+1);
        }
    }
}


/**
* Creates a new Project Task Record
*
* @param        int     $projectID       Project ID
* @param        int     $parentTaskID   Optional Parent Task if this is a subtask
* @return       int     Returns new record ID
*
*/
function prj_insertTask($projectID, $parentTaskID=0){
    global $_TABLES;

    //first check if this has a parent TASK ID..
    //if it dosent, then we're insertting a top level task

    $newid = 0;     // New task record id

    if($parentTaskID == 0) {

        if(!prj_checkTableSemaphore()) {
            //its locked....
            //we can loop here, or bail.. I'd loop 1/2 the wait duration if i really had to...
            COM_errorLog('prj_addtask - Table is locked, will try again ...');
        } else {//its not locked
            //first, lock the table
            prj_lockTable();
            //we're now locked for X seconds depending on the lockduration field
            //you could conceivably just keep relocking before each sql call to make sure....
            $sql = "SELECT max(rhs) FROM {$_TABLES['prj_tasks']}";
            $res = DB_query($sql);

            list($lhs)=DB_fetchArray($res);
            $lhs = $lhs+1;
            $rhs = $lhs+1;
            $sql  = "INSERT INTO {$_TABLES['prj_tasks']} (pid, lhs, rhs, parent_task) ";
            $sql .= "VALUES ('{$projectID}', '{$lhs}', '{$rhs}', 0 )";
            DB_query($sql);
            $newid = DB_insertID();
            prj_unlockTable();//set it free!
        }

    } else {     //we have a pid and have to do our crafty inserts here...
        if(!prj_checkTableSemaphore()) {
            //its locked.... we can loop here, or bail.. I'd loop 1/2 the wait duration if i really had to...
            COM_errorLog('prj_addtask - Table is locked, will try again ...');
        } else { //its not locked need to first, lock the table
            prj_lockTable();
            $sql="SELECT rhs FROM {$_TABLES['prj_tasks']} WHERE tid='$parentTaskID'";
            $res=DB_query($sql);
            list($rhs)=DB_fetchArray($res);

            $sql="UPDATE {$_TABLES['prj_tasks']} set lhs = lhs+2 where lhs >= '$rhs'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} set rhs = rhs+2 where rhs >= '$rhs'";
            DB_query($sql);
            $lhs=$rhs;
            $rhs=$rhs+1;

            $sql  = "INSERT INTO {$_TABLES['prj_tasks']} (pid, lhs, rhs, parent_task) ";
            $sql .= "VALUES ('{$projectID}', '{$lhs}', '{$rhs}', '{$parentTaskID}')";
            DB_query($sql);
            $newid = DB_insertID();

            prj_unlockTable();  //set it free!
        }

    }  //end else for testing if we have a pid

    return $newid;

}


function prj_deleteTask($tid) {
    global $_TABLES;

    $sql="DELETE FROM {$_TABLES['prj_tasks']} where tid='$tid'";
    DB_query($sql);
}


//all we have to do is set the parent_task to the parent tasks's parent_task... or 0
function prj_moveTaskLeft($tid) {
    global $_TABLES;

    $sql="SELECT lhs, rhs, pid, parent_task FROM {$_TABLES['prj_tasks']} where tid='{$tid}'";
    $res=DB_query($sql);
    list($lhs, $rhs, $pid, $parentTask)=DB_fetchArray($res);

    $sql="select * from {$_TABLES['prj_tasks']} where lhs>{$lhs} and rhs<{$rhs} and pid={$pid}";
    $thisSubs=DB_query($sql);


    if($parentTask!='0'){
        //we know this isnt already a parent..
        $sql="SELECT parent_task FROM {$_TABLES['prj_tasks']} WHERE tid='{$parentTask}'";
        $res=DB_query($sql);
        list($setTo)=DB_fetchArray($res);
        $sql="UPDATE {$_TABLES['prj_tasks']} SET parent_task='{$setTo}' WHERE tid='{$tid}'";
        DB_query($sql);

        //do a blind insert into the parent now.
        $lastInsert=prj_insertTask($pid,$setTo);
        $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
        $res=DB_query($sql);
        list($newLHS, $newRHS)=DB_fetchArray($res);
        //newLHS and new RHS now hold what our TID should have as a lhs and rhs.
        $sql="DELETE from {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
        DB_query($sql); //deleted the blind insert
        $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$tid}'";
        DB_query($sql);

        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++) {
            //we're doing a blind insert here
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid={$lastInsert}";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid={$lastInsert}";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs={$newLHS}, rhs={$newRHS} WHERE tid={$A['tid']}";
            DB_query($sql);
        }

    } else {
        //parent task IS 0.. so just set this tid's parent task to 0
        $sql="UPDATE {$_TABLES['prj_tasks']} SET parent_task=0 WHERE tid='{$tid}'";
        DB_query($sql);
   }
}


//we have to check to see what task is directly above us.
//do this by odering by lhs and parent_task and choose the top 1 above us.
//then make this task a child of the task above us.
function prj_moveTaskRight($tid) {
    global $_TABLES;

    $sql = "SELECT lhs, rhs, pid FROM {$_TABLES['prj_tasks']} where tid='{$tid}'";
    $res=DB_query($sql);
    list($lhs, $rhs, $pid)=DB_fetchArray($res);
    $sql="SELECT tid FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND rhs < '{$lhs}' ORDER by rhs DESC LIMIT 1";
    $res=DB_query($sql);
    list($newParent)=DB_fetchArray($res);
    if($newParent!='') {
        $sql="UPDATE {$_TABLES['prj_tasks']} SET parent_task='{$newParent}' WHERE tid='{$tid}'";
        DB_query($sql);


        $sql="SELECT * FROM {$_TABLES['prj_tasks']} WHERE lhs>{$lhs} AND rhs<{$rhs} and pid={$pid}";
        $thisSubs=DB_query($sql);
        //$thisSubs now holds all children of the task moving right.

        //do a blind insert into the parent now.
        $lastInsert=prj_insertTask($pid,$newParent);
        $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
        $res=DB_query($sql);
        list($newLHS, $newRHS)=DB_fetchArray($res);
        //newLHS and new RHS now hold what our TID should have as a lhs and rhs.
        $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
        DB_query($sql); //deleted the blind insert
        $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$tid}'";
        DB_query($sql);
        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            //we're doing a blind insert here
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid={$lastInsert}";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid={$lastInsert}";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs={$newLHS}, rhs={$newRHS} WHERE tid={$A['tid']}";
            DB_query($sql);
        }
    }
}


//have to first select the current task's lhs
//then select top 1 task's rhs where rhs<this lhs
//but we also have to constrain the up movement to the current task's parent boundaries
function prj_moveTaskUp($tid) {
    global $_TABLES;

    $sql="SELECT tid, lhs, rhs, parent_task, pid FROM {$_TABLES['prj_tasks']} WHERE tid='{$tid}'";
    $res=DB_query($sql);
    list($thisTID, $thisLHS, $thisRHS,$thisParentTask, $pid)=DB_fetchArray($res);

    //have to determine the lowest boundary.. lhs from the parent container
    //if the parent task is 0, then there's no parent and we're actually moving full containers..
    //thus the resulting sql will have the boundary omitted...
    if($thisParentTask!='0'){
        $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$thisParentTask}'";
        $res=DB_query($sql);
        list($parentLHS, $parentRHS)=DB_fetchArray($res);
        $sql="SELECT tid, lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' and rhs < '{$thisLHS}' AND rhs between '{$parentLHS}' AND '{$parentRHS}' ORDER BY rhs DESC LIMIT 1";
    } else {
        //parent task is 0.. moving a full container
        $sql="SELECT tid, lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND rhs < '{$thisLHS}' ORDER BY rhs DESC LIMIT 1";
    }

    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    if($nrows > 0){
        list($swapTID, $swapLHS, $swapRHS)=DB_fetchArray($res);

        $sql="SELECT * FROM {$_TABLES['prj_tasks']} WHERE lhs > '{$thisLHS}' AND rhs < '{$thisRHS}' AND pid='{$pid}'";
        $thisSubs=DB_query($sql);

        $sql="SELECT * FROM {$_TABLES['prj_tasks']} WHERE lhs > '{$swapLHS}' AND rhs < '{$swapRHS}' AND pid='{$pid}'";
        $swapSubs=DB_query($sql);

        //thissubs and swapsubs now hold all the children for the swapping members.
        //now swap the two parent lhs and rhs values

        $sql  ="UPDATE {$_TABLES['prj_tasks']} SET ";
        $sql .="lhs='{$swapLHS}',";
        $sql .="rhs='{$swapRHS}' ";
        $sql .="WHERE tid='{$thisTID}'";
        DB_query($sql);
        $sql  ="UPDATE {$_TABLES['prj_tasks']} SET ";
        $sql .="lhs='{$thisLHS}',";
        $sql .="rhs='{$thisRHS}' ";
        $sql .="WHERE tid='{$swapTID}'";
        DB_query($sql);

        //now just insert all the children back in...
        //first THIS tid's children
        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$A['tid']}'";
            DB_query($sql);
        }

        $nrows=DB_numRows($swapSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            //we're doing a blind insert here
            $A=DB_fetchArray($swapSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$A['tid']}'";
            DB_query($sql);
        }

    } else {//nothing to swap to
            //here just incase you want to catch this condition

    }

}//end function

//have to first select the current task's lhs then select top 1 task's rhs where rhs < this lhs
function prj_moveTaskDown($tid) {
    global $_TABLES;

    $sql="SELECT tid, lhs, rhs, parent_task, pid FROM {$_TABLES['prj_tasks']} WHERE tid='{$tid}'";
    $res=DB_query($sql);
    list($thisTID, $thisLHS, $thisRHS, $thisParentTask, $pid)=DB_fetchArray($res);

    if($thisParentTask!='0'){
       $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$thisParentTask}'";
        $res=DB_query($sql);
        list($parentLHS, $parentRHS)=DB_fetchArray($res);
        $sql="SELECT tid, lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND lhs > '{$thisRHS}' AND lhs between '{$parentLHS}' AND '{$parentRHS}' ORDER BY lhs ASC LIMIT 1";
    } else {
        //parent task is 0.. moving a full container
        $sql="SELECT tid, lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND lhs > '{$thisRHS}' ORDER BY lhs ASC LIMIT 1";
    }

    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    if($nrows>0){
        list($swapTID, $swapLHS, $swapRHS)=DB_fetchArray($res);

        $sql="SELECT * FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND lhs > '{$thisLHS}' AND rhs < '{$thisRHS}'";
        $thisSubs=DB_query($sql);

        $sql="SELECT * FROM {$_TABLES['prj_tasks']} WHERE pid='{$pid}' AND lhs > '{$swapLHS}' AND rhs < '{$swapRHS}'";
        $swapSubs=DB_query($sql);

        //thissubs and swapsubs now hold all the children for the swapping members.
        //now swap the two parent lhs and rhs values

        $sql  ="UPDATE {$_TABLES['prj_tasks']} SET ";
        $sql .="lhs='{$swapLHS}',";
        $sql .="rhs='{$swapRHS}' ";
        $sql .="WHERE tid='{$thisTID}'";
        DB_query($sql);
        $sql  ="UPDATE {$_TABLES['prj_tasks']} SET ";
        $sql .="lhs='{$thisLHS}',";
        $sql .="rhs='{$thisRHS}' ";
        $sql .="WHERE tid='{$swapTID}'";
        DB_query($sql);

        //now just insert all the children back in...
        //first THIS tid's children
        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            prj_updateTask($A);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid={$lastInsert}";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$A['tid']}'";
            DB_query($sql);
            }

        $nrows=DB_numRows($swapSubs);
        for($cntr=0;$cntr<$nrows;$cntr++) {
            //we're doing a blind insert here
            $A=DB_fetchArray($swapSubs);
            $lastInsert=0;
            $lastInsert=prj_insertTask($A['pid'],$A['parent_task']);
            prj_updateTask($A);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_tasks']} WHERE tid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_tasks']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE tid='{$A['tid']}'";
            DB_query($sql);
        }
    } else {//nothing to swap to
            //here just incase you want to catch this condition

    }
}//end function



function prj_updateTask($A) {
    global $_TABLES;

    $sql = "UPDATE {$_TABLES['prj_tasks']} SET name='{$A['name']}', description='{$A['description']}', ";
    $sql .= "keywords='{$A['keywords']}',priority_id='{$A['priority_id']}',progress='{$A['progress']}', ";
    $sql .= "status_id='{$A['status_id']}',progress_id='{$A['progress_id']}', ";
    if ($A['create_date'] > 0) {
        $sql .= "create_date={$A['create_date']},";
    }
    $sql .= "start_date='{$A['start_date']}',estimated_end_date='{$A['estimated_end_date']}', ";
    $sql .= "planned_end_date='{$A['planned_end_date']}',actual_end_date='{$A['actual_end_date']}',last_updated_date='{$A['last_updated_date']}', ";
    $sql .= "notification_enabled_flag='{$A['notification_enabled_flag']}', make_private_enabled_flag='{$A['make_private_enabled_flag']}', ";
    $sql .= "duration_type_id='{$A['duration_type_id']}',duration='{$A['duration']}' ";
    $sql .= "WHERE tid={$A['tid']} ";
    DB_query($sql);
}


//returns false if its not available..
//return true if its available.
function prj_checkTableSemaphore($tablename='') {
    global $_CONF,$_PRJCONF,$_TABLES;

    if($tablename=='') {
        $checkTable=$_TABLES['prj_tasks'];
    } else {
        $checkTable=$tablename;
    }

    $retval=false;
    $sql="SELECT locked, timelocked FROM {$_TABLES['prj_lockcontrol']} where tableLocked='{$checkTable}'";
    $res=DB_query($sql);
    $num=DB_numRows($res);
    if($num>0) {
        list($lock,$timelocked)=DB_fetchArray($res);
        if($lock==1 || $lock==TRUE){
            // the table is locked.. however lets check how long its been locked for
            // if its been locked for more than the arbitrary amount in the lockduration
            // variable, just unlock it.. may have been a deadlock
            if( (time()-$timelocked) > $_PRJCONF['lockduration'] ) {
                //unlock it
                $sql="UPDATE {$_TABLES['prj_lockcontrol']} set locked=0, timelocked=0 where tableLocked='{$checkTable}'";
                DB_query($sql);
                $retval=true;
            } else {
                $retval=false;
            }
        } else {
            $retval=true;
        }
    } else {
        $sql="INSERT into {$_TABLES['prj_lockcontrol']} (locked, timelocked, tableLocked) values (0,0,'{$checkTable}')";
        DB_query($sql);
        $retval=true;
    }
    return $retval;

}


function prj_lockTable($tablename='') {
    global $_TABLES;
     if($tablename==''){
        $checkTable=$_TABLES['prj_lockcontrol'];
    } else {
        $checkTable=$tablename;
    }
    $sql="UPDATE {$_TABLES['prj_lockcontrol']} set locked=1, timelocked=" . time() . " where tableLocked='{$checkTable}'";
    DB_query($sql);
}


function prj_unlockTable($tablename='') {
    global $_TABLES;
    if($tablename==''){
        $checkTable=$_TABLES['prj_lockcontrol'];
    } else {
        $checkTable=$tablename;
    }
    $sql="UPDATE {$_TABLES['prj_lockcontrol']} set locked=0, timelocked=0 where tableLocked='{$checkTable}'";
    DB_query($sql);
}


function prj_getTaskLevel($tid,$level=1) {
    global $_TABLES;

    $parent_task = DB_getItem($_TABLES['prj_tasks'],'parent_task',"tid='$tid'");
    if ($parent_task == 0) {
        return $level;
    } else {
        $level++;
        return (prj_getTaskLevel($parent_task,$level));
    }
}


/* Returns an array of task ID's that map the task level hierarchy for the intial passed in subtask id */
function prj_getTopLevelTask($tid,&$arr_tids) {
    global $_TABLES;

    $parent_task = DB_getItem($_TABLES['prj_tasks'],'parent_task',"tid='$tid'");
    $arr_tids[] = $tid;
    if ($parent_task == 0) {
        return $tid;
    } else {
        prj_getTopLevelTask($parent_task,$arr_tids);
        return $arr_tids;
    }
}

/* Returns an array of subtask ID's for the intial passed in task id */
function prj_getSubTaskTree($tid,&$arr_tids) {
    global $_TABLES;

    $sql = "SELECT tid FROM {$_TABLES['prj_tasks']} where parent_task=$tid ORDER BY lhs ASC";
    $query = DB_query($sql);
    while (list($taskid) = DB_fetchArray($query)) {
        $arr_tids[] = $taskid;
        if (DB_count($_TABLES['prj_tasks'],'parent_task', $taskid) > 0) {
            prj_getSubTaskTree($taskid,$arr_tids);
        }
    }
    return $arr_tids;
}


function prj_setTemplateVars(&$template,$data,$type='') {

    foreach($data as $key => $value){
        if (trim($value) == '') {
            continue;
        }
        // Is this field a timestamp - convert to a displayable date */
        if (strpos($key,'date') > 0) {

            $value = strftime("%Y/%m/%d", $value);
        }
        // Is this field a checkmark - convert to a be checked or not and set radio button template variables as well */
        if (strpos($key,'flag') > 0) {
            $key = 'VALUE_' . trim($key);
            if ($value==1 OR strtolower($value)=='on' OR strtolower($value)=='y' OR strtolower($value)=='yes') {
                $template->set_var($key,'CHECKED=CHECKED');
                $key .= '_on';    // Radio Button if used - create another template variable and set it checked
                $template->set_var($key,'CHECKED=CHECKED');
            } else {
                $template->set_var($key,'');
                $key .= '_off';   // Radio Button if used - create another template variable and set it checked
                $template->set_var($key,'CHECKED=CHECKED');
            }
        } else {
            $key = 'VALUE_' . trim($key);
            $template->set_var($key,$value);
        }
    }
}


function prj_breadcrumbs($tid=0,$pid=0,$last_label1='',$last_label2='') {
    global $_TABLES,$_CONF,$strings;

    $result = DB_query("SELECT tid,name,parent_task,lhs,rhs FROM {$_TABLES['prj_tasks']} WHERE tid=$tid");
    list($tid,$taskName,$taskParent,$task_lhs,$task_rhs) = DB_fetchArray($result);
    $result = DB_query("SELECT pid, name FROM {$_TABLES['prj_projects']} WHERE pid=$pid");
    list($pid,$projectName) = DB_fetchArray($result);
    $navbar = new navbar();
    $navbar->openBreadcrumbs();
    $navbar->add_breadcrumbs("{$_CONF['site_url']}/nexproject/index.php",$strings["home"]);
    $navbar->add_breadcrumbs("{$_CONF['site_url']}/nexproject/projects.php",$strings["projects"]);
    if ($pid > 0) {
        $navbar->add_breadcrumbs("{$_CONF['site_url']}/nexproject/viewproject.php?pid=$pid",$projectName);
    }
    if ($tid > 0) {
        if ($taskParent == 0 ){
            $navbar->add_breadcrumbs("{$_CONF['site_url']}/nexproject/viewproject.php?mode=view&id=$tid",$taskName);
            $navbar->add_lastBreadcrumb($last_label1);
        } else {
            $hierarchy = '';     // Need to pass 2nd variable as an empty array
            $relatedTasks = implode(',',prj_getTopLevelTask($tid,$hierarchy));
            $result = DB_query("SELECT tid, name,rhs FROM {$_TABLES['prj_tasks']} WHERE tid in ($relatedTasks)");
            while (list($tid,$taskName,$subtask_rhs) = DB_fetchArray($result)) {
                $navbar->add_breadcrumbs("{$_CONF['site_url']}/nexproject/viewproject.php?mode=view&id=$tid",$taskName);
            }
            if ($last_label2 != '') {
                $navbar->add_lastBreadcrumb($last_label2);
            } else {
                $navbar->add_lastBreadcrumb($last_label1);
            }
        }
    } else {
        $navbar->add_lastBreadcrumb($last_label1);
    }
    return $navbar->closeBreadcrumbs();
}

function prj_taskParentoptions ($taskid) {
    global $_TABLES;

    // Get list of parent tasks for this task id - walk back up the tree
    $hierarchy = array();
    $categoryList = prj_getTopLevelTask($taskid,$hierarchy);
    $retval = '';
    if ($categoryList == '') {
        return '';
    } elseif (is_array($categoryList)) {
        krsort($categoryList);  // Reverse the order so we can add the '--' to indicate the indented level
    } else {
        $categoryList = array($categoryList);
    }
    $indent = '';
    foreach ($categoryList as $key => $category) {
        if ($category == 0) {
            break;
        }
        $label = DB_getItem($_TABLES['prj_tasks'],'name',"tid=$category");
        $label = $indent . $label;
        if ($taskid == $category) {
            $retval .= '<option value="'.$category.'" SELECTED=SELECTED>'.$label.'</option>';
        } else {
            $retval .= '<option value="'.$category.'">'.$label.'</option>';
        }
        $indent .= '--';
    }
    return $retval;
}



function prj_edit_task_icons($pid,$taskid,$mode='') {
    global $_CONF,$strings;
    ob_start();
    $block = new block();
    $block->form = "textblk";
    $block->openForm($_CONF['site_url'] . "/nexproject/viewproject.php");
    $block->openPaletteIcon();
    $block->paletteIcon(0,"add",$strings["add"]);
    $block->paletteIcon(1,"remove",$strings["delete"]);
    if ($mode != 'view') {
        $block->paletteIcon(2,"info",$strings["view"]);
    }
    if ($mode != 'edit') {
        $block->paletteIcon(3,"edit",$strings["edit"]);
    }
    $block->paletteIcon(4,"copy",$strings["copy"]);
    $block->closePaletteIcon();

    $block->openPaletteScript();
    $block->paletteScript(0,"add",$_CONF['site_url'] .
        "/nexproject/viewproject.php?mode=add&pid={$pid}&id={$taskid}","true,false,false",$strings["add"]);
    $block->paletteScript(1,"remove",$_CONF['site_url'] .
        "/nexproject/viewproject.php?mode=delete&id={$taskid}","true,false,false",$strings["delete"]);
    if ($mode != 'view') {
        $block->paletteScript(2,"info",$_CONF['site_url'] .
            "/nexproject/viewproject.php?mode=view&id={$taskid}","true,false,false",$strings["view"]);
    }
    if ($mode != 'edit') {
        $block->paletteScript(3,"edit",$_CONF['site_url'] .
            "/nexproject/viewproject.php?mode=edit&id={$taskid}","true,false,false",$strings["edit"]);
    }
    $block->paletteScript(4,"copy",$_CONF['site_url'] .
        "/nexproject/viewproject.php?mode=copy&id={$taskid}","true,false,false",$strings["copy"]);
    $block->closePaletteScript(0,$taskid);
    echo '</form>';
    $edit_icons = ob_get_contents();
    ob_end_clean();
    return $edit_icons;
}

//******************************************************************************************************************
//additions to support parent projects
//******************************************************************************************************************


function prj_insertProject($parentID=0){
    global $_TABLES;

    //first check if this has a parent project ID..
    //if it dosent, then we're insertting a top level task

    $newid = 0;     // New project record id

    if($parentID == 0) {

        if(!prj_checkTableSemaphore("{$_TABLES['prj_projects']}")) {
            //its locked....
            //we can loop here, or bail.. I'd loop 1/2 the wait duration if i really had to...
            COM_errorLog('prj_insertproject - Table is locked, will try again ...');

        } else {//its not locked
            //first, lock the table
            prj_lockTable("{$_TABLES['prj_projects']}");
            //we're now locked for X seconds depending on the lockduration field
            //you could conceivably just keep relocking before each sql call to make sure....
            $sql = "SELECT max(rhs) FROM {$_TABLES['prj_projects']}";
            $res = DB_query($sql);

            list($lhs)=DB_fetchArray($res);
            $lhs = $lhs+1;
            $rhs = $lhs+1;
            $sql  = "INSERT INTO {$_TABLES['prj_projects']} (lhs, rhs, parent_id) ";
            $sql .= "VALUES ('{$lhs}', '{$rhs}', 0 )";
            DB_query($sql);
            $newid = DB_insertID();
            prj_unlockTable("{$_TABLES['prj_projects']}");//set it free!
        }

    } else {     //we have a pid and have to do our crafty inserts here...
        if(!prj_checkTableSemaphore("{$_TABLES['prj_projects']}")) {
            //its locked.... we can loop here, or bail.. I'd loop 1/2 the wait duration if i really had to...
            COM_errorLog('prj_insertProject - Table is locked, will try again ...');
        } else { //its not locked need to first, lock the table
            prj_lockTable("{$_TABLES['prj_projects']}");
            $sql="SELECT rhs FROM {$_TABLES['prj_projects']} WHERE pid='$parentID'";
            $res=DB_query($sql);
            list($rhs)=DB_fetchArray($res);

            $sql="UPDATE {$_TABLES['prj_projects']} set lhs = lhs+2 where lhs >= '$rhs'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_projects']} set rhs = rhs+2 where rhs >= '$rhs'";
            DB_query($sql);
            $lhs=$rhs;
            $rhs=$rhs+1;

            $sql  = "INSERT INTO {$_TABLES['prj_projects']} (lhs, rhs, parent_id) ";
            $sql .= "VALUES ( '{$lhs}', '{$rhs}', '{$parentID}')";
            DB_query($sql);
            $newid = DB_insertID();

            prj_unlockTable("{$_TABLES['prj_projects']}");  //set it free!
        }

    }  //end else for testing if we have a pid

    return $newid;

}
function prj_moveProjectRight($tid) {
    global $_TABLES,$_USER;

    //if this is not the owner/pm of the project, dont let them do this.
    //retrieve the project's permissions for this user
    $perms=prj_getProjectPermissions($tid, $_USER['uid'], '0');  //$perms is an array
    if($perms['full']==1){;

        $plist = prj_getProjectIds();

        $sql = "SELECT lhs, rhs FROM {$_TABLES['prj_projects']} where pid='{$tid}'";
        $res=DB_query($sql);
        list($lhs, $rhs)=DB_fetchArray($res);
        $sql="SELECT pid FROM {$_TABLES['prj_projects']} WHERE pid in ($plist) AND rhs < '{$lhs}' ORDER by rhs DESC LIMIT 1";

        $res=DB_query($sql);
        list($newParent)=DB_fetchArray($res);
        //now, lets check here to see if this user has access to promote to this parent... if not.. bail...
        $perms=prj_getProjectPermissions($newParent, $_USER['uid'], '0');  //$perms is an array


        if($perms['full']!=1 && $perms['teammember']!=1){  //sorry, you dont have full access to the new parent.. bail out of this routine
            return;
        }

        if($newParent!='') {
            $sql="UPDATE {$_TABLES['prj_projects']} SET parent_id='{$newParent}' WHERE pid='{$tid}'";
            DB_query($sql);

            $sql="SELECT * FROM {$_TABLES['prj_projects']} WHERE lhs>{$lhs} AND rhs<{$rhs} ";
            $thisSubs=DB_query($sql);
            //$thisSubs now holds all children of the task moving right.

            //do a blind insert into the parent now.

            $lastInsert=prj_insertProject($newParent);
            //new insert function for projects here....

            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            //newLHS and new RHS now hold what our ID should have as a lhs and rhs.
            $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql); //deleted the blind insert
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$tid}'";
            DB_query($sql);
            $nrows=DB_numRows($thisSubs);
            for($cntr=0;$cntr<$nrows;$cntr++){
                //we're doing a blind insert here
                $A=DB_fetchArray($thisSubs);
                $lastInsert=0;
                $lastInsert=prj_insertProject($A['parent_id']);
                $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid={$lastInsert}";
                $res=DB_query($sql);
                list($newLHS, $newRHS)=DB_fetchArray($res);
                $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid={$lastInsert}";
                DB_query($sql);
                $sql="UPDATE {$_TABLES['prj_projects']} SET lhs={$newLHS}, rhs={$newRHS} WHERE pid={$A['pid']}";
                DB_query($sql);
            }
        }
    }
}

function prj_moveProjectUp($projectId) {
    global $_TABLES;

    $plist = prj_getProjectIds();

    $sql="SELECT pid, lhs, rhs, parent_id FROM {$_TABLES['prj_projects']} WHERE pid='{$projectId}'";
    $res=DB_query($sql);
    list($thisPID, $thisLHS, $thisRHS,$thisParentProject)=DB_fetchArray($res);

    //have to determine the lowest boundary.. lhs from the parent container
    //if the parent task is 0, then there's no parent and we're actually moving full containers..
    //thus the resulting sql will have the boundary omitted...
    if($thisParentProject!='0'){
        $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$thisParentProject}'";
        $res=DB_query($sql);
        list($parentLHS, $parentRHS)=DB_fetchArray($res);
        $sql="SELECT pid, lhs, rhs FROM {$_TABLES['prj_projects']} WHERE  rhs < '{$thisLHS}' AND pid in ($plist) AND rhs between '{$parentLHS}' AND '{$parentRHS}' ORDER BY rhs DESC LIMIT 1";
    } else {
        //parent task is 0.. moving a full container
        $sql="SELECT pid, lhs, rhs FROM {$_TABLES['prj_projects']} WHERE  rhs < '{$thisLHS}' AND pid in ($plist) ORDER BY rhs DESC LIMIT 1";
    }

    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    if($nrows > 0){
        list($swapTID, $swapLHS, $swapRHS)=DB_fetchArray($res);

        $sql="SELECT * FROM {$_TABLES['prj_projects']} WHERE lhs > '{$thisLHS}' AND rhs < '{$thisRHS}' ";
        $thisSubs=DB_query($sql);

        $sql="SELECT * FROM {$_TABLES['prj_projects']} WHERE lhs > '{$swapLHS}' AND rhs < '{$swapRHS}' ";
        $swapSubs=DB_query($sql);

        //thissubs and swapsubs now hold all the children for the swapping members.
        //now swap the two parent lhs and rhs values

        $sql  ="UPDATE {$_TABLES['prj_projects']} SET ";
        $sql .="lhs='{$swapLHS}',";
        $sql .="rhs='{$swapRHS}' ";
        $sql .="WHERE pid='{$thisPID}'";
        DB_query($sql);
        $sql  ="UPDATE {$_TABLES['prj_projects']} SET ";
        $sql .="lhs='{$thisLHS}',";
        $sql .="rhs='{$thisRHS}' ";
        $sql .="WHERE pid='{$swapTID}'";
        DB_query($sql);

        //now just insert all the children back in...
        //first THIS pid's children
        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertProject($A['parent_id']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$A['pid']}'";
            DB_query($sql);
        }

        $nrows=DB_numRows($swapSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            //we're doing a blind insert here
            $A=DB_fetchArray($swapSubs);
            $lastInsert=0;
            $lastInsert=prj_insertProject($A['parent_id']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$A['pid']}'";
            DB_query($sql);
        }

    } else {//nothing to swap to
            //here just incase you want to catch this condition

    }

}//end function


function prj_getProjectLevel($pid,$level=1) {
    global $_TABLES;

    $parent_task = DB_getItem($_TABLES['prj_projects'],'parent_id',"pid='$pid'");
    if ($parent_task == 0) {
        return $level;
    } else {
        $level++;
        return (prj_getProjectLevel($parent_task,$level));
    }
}


function prj_moveProjectLeft($projectId) {
    global $_TABLES,$_USER;

    //if this is not the owner/pm of the project, dont let them do this.
    //retrieve the project's permissions for this user
    $perms=prj_getProjectPermissions($projectId, $_USER['uid'], '0');  //$perms is an array
    if($perms['full']==1){

        $plist = prj_getProjectIds();

        $sql="SELECT lhs, rhs, parent_id FROM {$_TABLES['prj_projects']} where pid='{$projectId}'";
        $res=DB_query($sql);
        list($lhs, $rhs, $parent_id)=DB_fetchArray($res);

        $sql="select * from {$_TABLES['prj_projects']} where pid in ($plist) AND lhs>{$lhs} and rhs<{$rhs} ";
        $thisSubs=DB_query($sql);


        if($parent_id!='0'){
            //we know this isnt already a parent..
            //determine if we have team member or pm perms for the parent we're moving into
            $perms=prj_getProjectPermissions($parent_id, $_USER['uid'], '0');  //$perms is an array

            if($perms['full']!=1 && $perms['teammember']!=1){  //sorry, you dont have full access to the new parent.. bail out of this routine
                return;
            }


            $sql="SELECT parent_id FROM {$_TABLES['prj_projects']} WHERE pid='{$parent_id}'";
            $res=DB_query($sql);
            list($setTo)=DB_fetchArray($res);
            $sql="UPDATE {$_TABLES['prj_projects']} SET parent_id='{$setTo}' WHERE pid='{$projectId}'";
            DB_query($sql);

            //do a blind insert into the parent now.
            $lastInsert=prj_insertProject($setTo);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            //newLHS and new RHS now hold what our TID should have as a lhs and rhs.
            $sql="DELETE from {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql); //deleted the blind insert
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$projectId}'";
            DB_query($sql);

            $nrows=DB_numRows($thisSubs);
            for($cntr=0;$cntr<$nrows;$cntr++) {
                //we're doing a blind insert here
                $A=DB_fetchArray($thisSubs);
                $lastInsert=0;
                $lastInsert=prj_insertProject($A['parent_id']);
                $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid={$lastInsert}";
                $res=DB_query($sql);
                list($newLHS, $newRHS)=DB_fetchArray($res);
                $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid={$lastInsert}";
                DB_query($sql);
                $sql="UPDATE {$_TABLES['prj_projects']} SET lhs={$newLHS}, rhs={$newRHS} WHERE pid={$A['pid']}";
                DB_query($sql);
            }

        } else {
            //parent task IS 0.. so just set this tid's parent task to 0
            $sql="UPDATE {$_TABLES['prj_projects']} SET parent_id=0 WHERE pid='{$projectId}'";
            DB_query($sql);
       }

   }//end if perms['full']==1
}


function prj_moveProjectDown($projectId) {
    global $_TABLES;

    $plist = prj_getProjectIds();

    $sql="SELECT pid, lhs, rhs, parent_id FROM {$_TABLES['prj_projects']} WHERE pid='{$projectId}'";
    $res=DB_query($sql);
    list($thisPID, $thisLHS, $thisRHS, $thisParentID)=DB_fetchArray($res);

    if($thisParentID!='0'){
       $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$thisParentID}'";
        $res=DB_query($sql);
        list($parentLHS, $parentRHS)=DB_fetchArray($res);
        $sql="SELECT pid, lhs, rhs FROM {$_TABLES['prj_projects']} WHERE  lhs > '{$thisRHS}' AND pid in ($plist) AND lhs between '{$parentLHS}' AND '{$parentRHS}' ORDER BY lhs ASC LIMIT 1";
    } else {
        //parent task is 0.. moving a full container
        $sql="SELECT pid, lhs, rhs FROM {$_TABLES['prj_projects']} WHERE  lhs > '{$thisRHS}' AND pid in ($plist) ORDER BY lhs ASC LIMIT 1";
    }

    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    if($nrows>0){
        list($swapTID, $swapLHS, $swapRHS)=DB_fetchArray($res);

        $sql="SELECT * FROM {$_TABLES['prj_projects']} WHERE lhs > '{$thisLHS}' AND rhs < '{$thisRHS}'";
        $thisSubs=DB_query($sql);

        $sql="SELECT * FROM {$_TABLES['prj_projects']} WHERE lhs > '{$swapLHS}' AND rhs < '{$swapRHS}'";
        $swapSubs=DB_query($sql);

        //thissubs and swapsubs now hold all the children for the swapping members.
        //now swap the two parent lhs and rhs values

        $sql  ="UPDATE {$_TABLES['prj_projects']} SET ";
        $sql .="lhs='{$swapLHS}',";
        $sql .="rhs='{$swapRHS}' ";
        $sql .="WHERE pid='{$thisPID}'";
        DB_query($sql);
        $sql  ="UPDATE {$_TABLES['prj_projects']} SET ";
        $sql .="lhs='{$thisLHS}',";
        $sql .="rhs='{$thisRHS}' ";
        $sql .="WHERE pid='{$swapTID}'";
        DB_query($sql);

        //now just insert all the children back in...
        //first THIS tid's children
        $nrows=DB_numRows($thisSubs);
        for($cntr=0;$cntr<$nrows;$cntr++){
            $A=DB_fetchArray($thisSubs);
            $lastInsert=0;
            $lastInsert=prj_insertProject($A['parent_id']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid={$lastInsert}";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$A['pid']}'";
            DB_query($sql);
            }

        $nrows=DB_numRows($swapSubs);
        for($cntr=0;$cntr<$nrows;$cntr++) {
            //we're doing a blind insert here
            $A=DB_fetchArray($swapSubs);
            $lastInsert=0;
            $lastInsert=prj_insertProject($A['parent_id']);
            $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            $res=DB_query($sql);
            list($newLHS, $newRHS)=DB_fetchArray($res);
            $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
            DB_query($sql);
            $sql="UPDATE {$_TABLES['prj_projects']} SET lhs='{$newLHS}', rhs='{$newRHS}' WHERE pid='{$A['pid']}'";
            DB_query($sql);
        }
    } else {//nothing to swap to
            //here just incase you want to catch this condition

    }
}//end function

//retrive a comma separated list of project ids the user has access to
//returns
function prj_getProjectIds() {
    global $_TABLES, $_USER, $_SERVER;

    $userid = ($_USER['uid'] != '') ? $_USER['uid']:1;
    $groups = SEC_getUserGroups($userid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);
    if ($_COOKIE['screen'] == 'myprojects') {
        $querycolumns  = "SELECT DISTINCT a.pid, a.progress_id, a.estimated_end_date, a.last_updated_date, a.priority_id, ";
        $querycolumns .= "a.name, c.fullname, 'r' as role, a.parent_id, a.lhs, a.rhs ";
        $queryfrom     = "FROM {$_TABLES['prj_projects']} a ";
        $queryfrom    .= "left join {$_TABLES['prj_users']} b on a.pid=b.pid ";
        $queryfrom    .= "left join {$_TABLES['users']} c on b.uid=c.uid ";
        $queryfrom    .= "left join {$_TABLES['prj_projPerms']} d on a.pid=d.pid ";
        $querywhere   .= "WHERE d.taskID=0 and (d.uid=$userid OR d.gid in ($prjPermGroups)) ";
        $sql = $querycolumns . $queryfrom . $querywhere;
    }
    else {
        if (SEC_inGroup('Root')) {
            $sql  = "SELECT DISTINCT a.pid, a.lhs, a.rhs, a.parent_id ";
            $sql .= "FROM {$_TABLES['prj_projects']} a ";
            $sql .= "WHERE 1=1 ";
        }
        else {
            $sql  = "SELECT DISTINCT a.pid, a.lhs, a.rhs, a.parent_id ";
            $sql .= "FROM {$_TABLES['prj_projects']} a, {$_TABLES['prj_projPerms']} b ";
            $sql .= "WHERE b.pid =a.pid ";
            $sql .= "AND b.taskID=0 AND (b.uid=$userid OR b.gid in ($prjPermGroups)) ";
        }
    }
    $result = DB_query($sql);
    $plist = '';
    while ($A = DB_fetchArray($result)) {
        if ($plist != '') {
            $plist .= ',';
        }
        $plist .= $A['pid'];
    }
    return $plist;
}



//retrieve the project's permissions for this user
//full access = PM
//monitor = view/read
//teammember= write/change
//seeDetails = future use
function prj_getProjectPermissions($pid, $uid, $taskID='0') {
    global  $_TABLES;
    if($taskID==''){
        $taskID='0';
        }
    if($uid==''){
        $uid='0';
        }
    if($pid==''){
        $pid='0';
        }

    $groups = SEC_getUserGroups($uid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);

    $permssql  = "SELECT sum(a.viewRead) as viewread, sum( a.writeChange) writechange, sum(a.fullAccess) fullaccess, sum(a.seeDetails) as seeDetails ";
    $permssql .= "FROM {$_TABLES['prj_projPerms']} a ";
    $permssql .= "LEFT JOIN {$_TABLES['group_assignments']} b on a.gid=b.ug_main_grp_id ";
    $permssql .= "WHERE a.pid='{$pid}' and a.taskID='{$taskID}' and ( a.uid='{$uid}' or ";
    $permssql .= "b.ug_uid=$uid";

    /******* this works in sql server and mysql 4.1 and greater... however
    //i have to alter this to make it work for our antiquidated mysql versions..
    $permssql .=" b.ug_main_grp_id in ";
    $permssql .="(";
    $permssql .=" select ug_main_grp_id ";
    $permssql .=" from {$_TABLES['group_assignments']} ";
    $permssql .=" where ug_uid='{$uid}' ";
    $permssql .=" ) )";
    ***/

    $permssql .="  )";

    $retArr=array();
    $resperms=DB_query($permssql);
    list($read, $write, $full, $seeDetails)=DB_fetchArray($resperms);
    $read=(bool)$read;
    $write=(bool)$write;
    $full=(bool)$full;
    $seeDetails=(bool)$seeDetails;
    $read=ppApplyFilter( $read, $isnumeric = true ,$returnzero=true);
    $write=ppApplyFilter( $write, $isnumeric = true ,$returnzero=true);
    $full=ppApplyFilter( $full, $isnumeric = true ,$returnzero=true);
    $seeDetails=ppApplyFilter( $seeDetails, $isnumeric = true ,$returnzero=true);
    if(SEC_inGroup('Root')){
        $full=1;
        $write=1;
        $read=1;
        }
    prj_array_push_associative(&$retArr, array("monitor" => (int)$read), array("teammember" => (int)$write),array("full" => (int)$full), array("seeDetails" => (int)$seeDetails));
    return ($retArr);
    }

//thanks to php.net for this
function prj_array_push_associative(&$arr) {
    $args = func_get_args();
    foreach ($args as $arg) {
      if (is_array($arg)) {
          foreach ($arg as $key => $value) {
              @$arr[$key] = $value;
              $ret++;
          }
      }else{
          @$arr[$arg] = "";
      }
    }
    return $ret;
}


/**
* Outputs the permissions for this PID and TaskID.  if no taskID, its for a PID only.
*
* @param        obj     $template      Pass by reference - Template object
* @param        int     $pid       Project ID
* @param        int     $task   Optional taskID
* @param        boolean $hideCols   Optional boolean used to hide edit and delete columns
* @param        int     $orderby   Optional int to order the results by column #
*
*/

function prj_displayPerms(&$template, $pid, $task='0', $hideCols=false, $orderby=''){
    global $_CONF, $_TABLES, $_USER,$pluginLangLabels;

    if($task==''){
        $task='0';
    }

    $sql  ="select a.id, a.uid, a.viewread, a.writechange, a.fullaccess, b.fullname ";
    $sql .="from {$_TABLES['prj_projPerms']}  a ";
    $sql .="inner join {$_TABLES['users']}  b on a.uid=b.uid ";
    $sql .="where a.uid<>0 and a.pid={$pid} ";
    $sql .=" and a.taskID='{$task}' ";

    if($orderby==''){
        $sql .=" ORDER BY b.fullname asc ";
    }else{
        $sql .=" ORDER BY {$orderby}  ";
    }

    $res=DB_query($sql);
    $permsRows=DB_numRows($res);
    if($hideCols){
        $template->set_var('showhide_actions','none');
    }

    if($permsRows>0){

        for( $i = 0; $i < $permsRows; $i++ ) {
            list($rid, $Uuid, $viewread, $writechange, $fullaccess, $UfullName) = DB_fetchArray($res);
            $template->set_var('rid',$rid);
            $template->set_var('uid','U' . $Uuid);
            $template->set_var('user',$UfullName);
            if($viewread=='1'){
                $template->set_var('read',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('read','');
            }

            if($writechange=='1'){
                $template->set_var('write',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('write','');
            }
            if($fullaccess=='1'){
                $template->set_var('change',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('change','');
            }

            $template->set_var('numread',$viewread);
            $template->set_var('numwrite',$writechange);
            $template->set_var('numchange',$fullaccess);
            $cssid = ($i%2)+1;
            $template->set_var('cssid', $cssid);

            $template->parse ('user_perm_records', 'permrec',true);
        }
        $template->set_var('show_usernote','none');
    }

    $sql  ="select a.id, b.grp_id, a.viewread, a.writechange, a.fullaccess, b.grp_name ";
    $sql .="from {$_TABLES['prj_projPerms']}  a ";
    $sql .="inner join {$_TABLES['groups']}  b on a.gid=b.grp_id ";
    $sql .="where a.gid<>0 and a.pid={$pid} ";
    $sql .=" and a.taskID='{$task}' ";
    if($orderby==''){
        $sql .=" ORDER BY b.grp_name asc ";
    }else{
    $sql .=" ORDER BY {$orderby}  ";
    }

    $res=DB_query($sql);
    $permsRows=DB_numRows($res);

    if($permsRows>0){
        for( $i = 0; $i < $permsRows; $i++ ) {
            list($rid, $Uuid, $viewread, $writechange, $fullaccess, $UfullName) = DB_fetchArray($res);
            $template->set_var('rid',$rid);
            $template->set_var('uid','G' . $Uuid);
            $template->set_var('user',$UfullName);
            if($viewread=='1'){
                $template->set_var('read',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('read','');
            }
            if($writechange=='1'){
                $template->set_var('write',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('write','');
            }
            if($fullaccess=='1'){
                $template->set_var('change',"<img src='{$_CONF['layout_url']}/nexproject/images/circle-green.png'>");
            } else {
                $template->set_var('change','');
            }

            $template->set_var('numread',$viewread);
            $template->set_var('numwrite',$writechange);
            $template->set_var('numchange',$fullaccess);
            $cssid = ($i%2)+1;
            $template->set_var('cssid', $cssid);
            $template->parse ('group_perm_records', 'permrec',true);
        }
        $template->set_var('show_groupnote','none');
    }

}


function prj_getTeamMembersOptionList($pid, $makeSelected=''){
    global $_TABLES;
    $sql  ="select distinct a.uid, b.fullname, b.email, b.username ";
    $sql .="from {$_TABLES['prj_projPerms']} a ";
    $sql .="inner join {$_TABLES['users']} b on a.uid=b.uid ";
    $sql .="where a.uid<>0 and a.pid='{$pid}' and writechange=1 ";
    $sql .="ORDER BY fullname ASC ";

    $retval="";
    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    for($cntr=0;$cntr<$nrows;$cntr++){
        list( $uid, $fullname, $email, $username) = DB_fetchArray($res);
        $retval .="<option value=\"{$uid}\" ";
        if($makeSelected!=''){
            if($makeSelected==$uid){
                $retval .=" selected ";
            }
        }
        $retval .=">{$fullname}</option>";
    }
    return($retval);
    }



//add permissions to a project
function prj_addProjectPermission($postVar, $pid, $checkView, $checkWrite, $checkFull){
    global $_TABLES;

    foreach($postVar as $val){
        $val=ppApplyFilter( $val,  false ,false);
        $ugid=substr($val,1);
        $type=substr($val,0,1);
        $sql="select id from {$_TABLES['prj_projPerms']} ";
        if($type=='G'){
            $sql .="where gid='{$ugid}'";
        } else{
            $sql .="where uid='{$ugid}'";
        }
        $sql .=" and pid='{$pid}' and taskID='0' group by id";
        $countRes=DB_query($sql);
        list( $rid)=DB_fetchArray($countRes);
        $cnt=DB_numRows($countRes);
        if($cnt>0){
            //already have a row.. update
            $sql="select viewRead,writeChange,fullAccess from {$_TABLES['prj_projPerms']} where id='{$rid}'";
            $res=DB_query($sql);
            list($vr, $wc, $fa)=DB_fetchArray($res);
            $vr=(bool)($vr+$checkView);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($wc+$checkWrite);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($fa+$checkFull);
            $fa=ppApplyFilter($fa, true, true );
            //we're now holding the new booleans for the database
            $sql="update {$_TABLES['prj_projPerms']} set viewRead='{$vr}', writeChange='{$wc}', fullAccess='{$fa}' where id='{$rid}'";
            DB_query($sql);
        } else{
            //no row, insert
            $vr=(bool)($checkView);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($checkWrite);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($checkFull);
            $fa=ppApplyFilter($fa, true, true );
            $sql="insert into {$_TABLES['prj_projPerms']} (pid, uid, gid, viewRead, writeChange, fullAccess) values(";
            $sql .="'{$pid}',";
            if($type=='U'){
                $sql .="'{$ugid}',";
            } else{
                $sql .="'0',";
            }
            if($type=='G'){
                $sql .="'{$ugid}',";
            }else{
                $sql .="'0',";
            }
            $sql .="'{$vr}',";
            $sql .="'{$wc}',";
            $sql .="'{$fa}'";
            $sql .=")";
            DB_query($sql);
        }
    }//end foreach
    prj_pushDownNewPermissions($pid);

}


function prj_editProjectPermission($evr, $ewc, $efa, $erid){
    global $_TABLES;
    $thisRid=$erid;
    $vr=(bool)($evr);
    $vr=ppApplyFilter($vr, true, true );
    $wc=(bool)($ewc);
    $wc=ppApplyFilter($wc, true, true );
    $fa=(bool)($efa);
    $fa=ppApplyFilter($fa, true, true );

    $sql="update {$_TABLES['prj_projPerms']} set viewRead='{$vr}', writeChange='{$wc}', fullAccess='{$fa}' where id='{$thisRid}'";
    DB_query($sql);
}



function prj_addTrickleDownTaskPerms($pid, $taskID){
    global $_TABLES;

    $sql  ="select id, uid, gid, viewread, writechange, fullaccess, seedetails from {$_TABLES['prj_projPerms']} ";
    $sql .="where pid='{$pid}' and taskid='0'";
    $cursorRes=DB_query($sql);
    $nCursorRows=DB_numRows($cursorRes);
    //use cursorRes as an in-code cursor to run thru to insert or update permissions
    for($cntr=0;$cntr<$nCursorRows;$cntr++){
        list($rid, $uid, $gid, $pvr, $pwc, $pfa, $psd)=DB_fetchArray($cursorRes);
        //now we hold the project perms..we have to do the same check to see if a user/group already has perms to add them properly here
        $sql="select id from {$_TABLES['prj_projPerms']} ";
        if($uid=='0'){
            $sql .="where gid='{$gid}'";
        }else{
            $sql .="where uid='{$uid}'";
        }
        $sql .=" and pid='{$pid}' and taskID='$taskID'";
        $countRes=DB_query($sql);
        list( $rid)=DB_fetchArray($countRes);
        $cnt=DB_numRows($countRes);
        if($cnt>0){
            //already have a row.. update
            $sql="select viewRead,writeChange,fullAccess from {$_TABLES['prj_projPerms']} where id='{$rid}'";
            $res=DB_query($sql);
            list($vr, $wc, $fa)=DB_fetchArray($res);
            $vr=(bool)($vr+$pvr);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($wc+$pwc);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($fa+$pfa);
            $fa=ppApplyFilter($fa, true, true );
            //we're now holding the new booleans for the database
            $sql="update {$_TABLES['prj_projPerms']} set viewRead='{$vr}', writeChange='{$wc}', fullAccess='{$fa}' where id='{$rid}'";
            DB_query($sql);
        } else{
            //no row, insert
            $vr=(bool)($pvr);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($pwc);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($pfa);
            $fa=ppApplyFilter($fa, true, true );
            $sql="insert into {$_TABLES['prj_projPerms']} (pid,taskID, uid, gid, viewRead, writeChange, fullAccess) values(";
            $sql .="'{$pid}',";
            $sql .="'{$taskID}',";
            if($uid!='0'){
                $sql .="'{$uid}',";
            }else{
                $sql .="'0',";
            }
            if($gid!='0'){
                $sql .="'{$gid}',";
            }
            else{
                $sql .="'0',";
            }
            $sql .="'{$vr}',";
            $sql .="'{$wc}',";
            $sql .="'{$fa}'";
            $sql .=")";
            DB_query($sql);
        }
    }
}


//this function will limit the task's permissions to only allow
//those who have team member writechange access to the project
//to have access to this task... all others will have their
//access revoked for this task...
function prj_addTeamMemberTaskPerms($pid, $taskID){
    global $_TABLES;

    $sql  ="select id, uid, gid, viewread, writechange, fullaccess, seedetails from {$_TABLES['prj_projPerms']} ";
    $sql .="where pid='{$pid}' and taskID='0' and writechange='1'";
    $cursorRes=DB_query($sql);
    $nCursorRows=DB_numRows($cursorRes);
    //use cursorRes as an in-code cursor to run thru to insert or update permissions

    //need to revoke anyone's who has monitor only rights to this task.  delete the row
    $sql="delete from {$_TABLES['prj_projPerms']} ";
    $sql .="where pid='{$pid}' and taskID='{$taskID}' and ((viewread='1' and writechange='0' and fullaccess='0') ";
    $sql .="or (viewread='0' and writechange='0' and fullaccess='0')) ";
    DB_query($sql);
    for($cntr=0;$cntr<$nCursorRows;$cntr++){
        list($rid, $uid, $gid, $pvr, $pwc, $pfa, $psd)=DB_fetchArray($cursorRes);
        //now we hold the project perms..we have to do the same check to see if a user/group already has perms to add them properly here
        $sql="select id from {$_TABLES['prj_projPerms']} ";
        if($uid=='0'){
            $sql .="where gid='{$gid}'";
        }else{
            $sql .="where uid='{$uid}'";
        }
        $sql .=" and pid='{$pid}' and taskID='$taskID'";
        $countRes=DB_query($sql);
        list( $rid)=DB_fetchArray($countRes);
        $cnt=DB_numRows($countRes);
        if($cnt>0){
            //already have a row.. update
            $sql="select viewRead,writeChange,fullAccess from {$_TABLES['prj_projPerms']} where id='{$rid}'";
            $res=DB_query($sql);
            list($vr, $wc, $fa)=DB_fetchArray($res);
            $vr=(bool)($vr+$pvr);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($wc+$pwc);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($fa+$pfa);
            $fa=ppApplyFilter($fa, true, true );
            //we're now holding the new booleans for the database
            $sql="update {$_TABLES['prj_projPerms']} set viewRead='{$vr}', writeChange='{$wc}', fullAccess='{$fa}' where id='{$rid}'";
            DB_query($sql);
        } else{
            //no row, insert
            $vr=(bool)($pvr);
            $vr=ppApplyFilter($vr, true, true );
            $wc=(bool)($pwc);
            $wc=ppApplyFilter($wc, true, true );
            $fa=(bool)($pfa);
            $fa=ppApplyFilter($fa, true, true );
            $sql="insert into {$_TABLES['prj_projPerms']} (pid,taskID, uid, gid, viewRead, writeChange, fullAccess) values(";
            $sql .="'{$pid}',";
            $sql .="'{$taskID}',";
            if($uid!='0'){
                $sql .="'{$uid}',";
            }else{
                $sql .="'0',";
            }
            if($gid!='0'){
                $sql .="'{$gid}',";
            }else{
                $sql .="'0',";
            }
            $sql .="'{$vr}',";
            $sql .="'{$wc}',";
            $sql .="'{$fa}'";
            $sql .=")";
            DB_query($sql);
        }
    }
}


function prj_pushDownNewPermissions($pid) {
    global $_TABLES;

    // First -  remove all permissions on tasks
    $query = DB_query("SELECT tid from {$_TABLES['prj_tasks']} WHERE pid=$pid");
    while (list($tid) = DB_fetchArray($query)) {
        $tids[] = $tid;
    }
    if (count($tids) > 0) {
        $project_tasks = implode(',',$tids);
        $sql = "DELETE FROM {$_TABLES['prj_projPerms']} WHERE pid='{$pid}' ";
        $sql .= "AND taskid  in ( $project_tasks )";
        DB_query($sql);
    }

    // Set perms for non-private tasks
    $sql="select tid from {$_TABLES['prj_tasks']} where pid='{$pid}' and make_private_enabled_flag<>'on'";
    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    for($cntr=0;$cntr<$nrows;$cntr++){
        list($taskID)=DB_fetchArray($res);
        prj_addTrickleDownTaskPerms($pid, $taskID);
    }

    // Do the same but for private tasks....
    $sql="select tid from {$_TABLES['prj_tasks']} where pid='{$pid}' and make_private_enabled_flag='on'";
    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    for($cntr=0;$cntr<$nrows;$cntr++){
        list($taskID)=DB_fetchArray($res);
        prj_addTeamMemberTaskPerms($pid, $taskID);
    }
}

 //this function will copy any available tasks and properly insert them into the tasks table
 //keeping the sub-tasks nesting in order.
function prj_copyProjectTasks($parentPid, $newPid, $taskID='0', $inserttedID='0', $namePrefix=''){
    global $_TABLES, $_CONF, $_USER;
    $sql="select tid, pid, lhs, rhs, priority_id, status_id, duration_type_id, progress_id, name, parent_task, description, notification_enabled_flag, keywords, ";
    $sql .="create_date, start_date, estimated_end_date, planned_end_date, actual_end_date, last_updated_date, duration, progress, ";
    $sql .="make_private_enabled_flag from {$_TABLES['prj_tasks']} where parent_task='{$taskID}' and pid='{$parentPid}'";
    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    for($cntr=0;$cntr<$nrows;$cntr++){
        $A= DB_fetchArray($res);
        $currentTID=$A['tid'];
        $insertID=prj_insertTask($newPid, $inserttedID);
        $A['tid']=$insertID;
        $A['pid']=$newPid;
        $A['name']=$namePrefix . $A['name'];
        prj_updateTask($A);
        //copy permissions
        $sql="insert into {$_TABLES['prj_projPerms']} (pid, taskid, uid, gid, viewread, writechange, fullaccess, seedetails) ";
        $sql .="(select '{$newPid}','$insertID', uid, gid, viewread, writechange, fullaccess, seedetails  from {$_TABLES['prj_projPerms']} ";
        $sql .="where taskid='{$currentTID}' and pid='{$parentPid}')";
        DB_query($sql);
        //copy ownership
        $sql="insert into {$_TABLES['prj_task_users']} (tid, uid, role) ";
        $sql .="(select '$insertID', uid, role from {$_TABLES['prj_task_users']} where tid='{$currentTID}')";
        DB_query($sql);
        $sql="select tid from {$_TABLES['prj_tasks']} where parent_task='{$currentTID}'";
        $innerRes=DB_query($sql);

        if(DB_numRows($innerRes)>0){//we have the case where this task has children...
            prj_copyProjectTasks($parentPid, $newPid, $currentTID, $insertID, $namePrefix);
        }
    }
}


//this function will begin the recursive copy task functionality
function prj_beginCopyTasks($pid, $source_task){
    global $_TABLES, $_CONF, $_USER;
    $insertID=prj_insertTask($pid, 0);
    $sql="select tid, pid, lhs, rhs, priority_id, status_id, duration_type_id, progress_id, name, parent_task, description, notification_enabled_flag, keywords, ";
    $sql .="create_date, start_date, estimated_end_date, planned_end_date, actual_end_date, last_updated_date, duration, progress, ";
    $sql .="make_private_enabled_flag from {$_TABLES['prj_tasks']} where tid='{$source_task}' and pid='{$pid}'";
    $res=DB_query($sql);
    $A= DB_fetchArray($res);
    $A['tid']=$insertID;
    $A['name']="Copy of " . $A['name'];
    prj_updateTask($A);
    //copy permissions
    $sql="insert into {$_TABLES['prj_projPerms']} (pid, taskid, uid, gid, viewread, writechange, fullaccess, seedetails) ";
    $sql .="(select '{$pid}','$insertID', uid, gid, viewread, writechange, fullaccess, seedetails  from {$_TABLES['prj_projPerms']} ";
    $sql .="where taskid='{$source_task}' and pid='{$pid}')";
    DB_query($sql);
    //copy ownership
    $sql="insert into {$_TABLES['prj_task_users']} (tid, uid, role) ";
    $sql .="(select '$insertID', uid, role from {$_TABLES['prj_task_users']} where tid='{$source_task}')";
    DB_query($sql);
    prj_copyProjectTasks($pid, $pid, $source_task, $insertID, 'Copy of ');
}



function prj_displayMyProjects(&$blockPage){
    global $_TABLES, $_CONF, $_USER, $_COOKIE,$subTaskImg, $progress, $priority, $strings, $labels, $_PRJCONF, $_COOKIE;

    if (isset($_USER['uid'])) {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }

    $limitbase=$_COOKIE['myprjmin'];
    if($limitbase==''){
        $limitbase=0;
    }

    $cookieString=$_COOKIE['filterTasks'];

    if($blockPage== NULL or $blockPage==''){
        $blockPage = new block();
    }

    $block1 = new block();
    if ($msg != '') {
        require_once("includes/messages.php");
        $blockPage->messagebox($msgLabel);
    }

    $block1->form = "saP";
    $block1->openForm($_CONF['site_url'] . "/nexproject/index.php?" . "#" . $block1->form . "Anchor");
    $headingTitle = "{$strings['my_projects']}&nbsp;&nbsp;(<a href=\"{$_CONF['site_url']}/nexproject/index.php?mode=add\">{$strings["add"]}</a>)";
    $headingStatusArea = '<span id="ajaxstatus_myprojects" class="pluginInfo" style="display:none">&nbsp;</span>';
//    $block1->headingToggle( $headingTitle,$headingStatusArea );

    $block1->rowsLimit = $_PRJCONF['project_block_rows'];
    if (!isset($_USER['uid']) OR $_USER['uid'] == "") {
        $uid = 1;
    } else {
        $uid = $_USER['uid'];
    }
    echo '<!--startMyProjects-->';
    echo '<div id="divMyProjects">';

    // Get a list of groups user is a member of and setup to be used in SQL to test user can view project
    $groups = SEC_getUserGroups($uid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);


    $querycolumns  = "SELECT DISTINCT a.pid, a.progress_id, a.estimated_end_date, a.last_updated_date, a.priority_id, ";
    $querycolumns .= "a.name, 'r' as role, a.parent_id, a.lhs, a.rhs ";
    $queryfrom     = "FROM {$_TABLES['prj_projects']} a ";
    $queryfrom    .= "left join {$_TABLES['prj_users']} b on a.pid=b.pid ";
    $queryfrom    .= "left join {$_TABLES['users']} c on b.uid=c.uid ";
    $queryfrom    .= "left join {$_TABLES['prj_projPerms']} d on a.pid=d.pid ";
    $querywhere   .= "WHERE d.taskID=0 and (d.uid=$userid OR d.gid in ($prjPermGroups)) ";

    $filter = $_COOKIE['filter'];
    $category_string = substr($filter, 0, 3);
    $needle = substr($filter, 3);

    switch ($category_string) {
        case 'cat':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . "inner join {$_TABLES['prj_category']} e on e.pid=a.pid ";
            $querywhere = $querywhere . "AND e.category_id=$needle ";
            $name = DB_getItem ($_TABLES['prj_site_category'], 'description', "category_id = $needle");
            $header = nexlistOptionList('view', '', $_PRJCONF['lookuplist_category'], 0, $needle);
            break;

        case 'loc':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . "inner join {$_TABLES['prj_location']} e on e.pid=a.pid ";
            $querywhere = $querywhere . "AND e.location_id=$needle ";
            $name = DB_getItem ($_TABLES['prj_site_location'], 'description', "location_id = $needle");
            $header = nexlistOptionList('view', '', $_PRJCONF['lookuplist_locations'], 0, $needle);
            break;

        case 'dep':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . "inner join {$_TABLES['prj_department']} e on e.pid=a.pid ";
            $querywhere = $querywhere . "AND e.department_id=$needle ";
            $name = DB_getItem ($_TABLES['prj_site_department'], 'description', "department_id = $needle");
            $header = nexlistOptionList('view', '', $_PRJCONF['lookuplist_departments'], 0, $needle);
            break;

        case 'pri':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.priority_id=$needle ";
            $header = $strings["filter_priority"] . $priority[$needle];
            break;

        case 'pro':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.progress_id=$needle ";
            $header = $strings["filter_progress"] . $progress[$needle];
            break;

        case 'sta':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.status_id=$needle ";
            $header = $strings["filter_status"] . $status[$needle];
            break;

        case 'ctm':
            $needle = substr("$filter", 3, 3);
            $customFilter = prj_constructFilter($needle);
            $querycolumns  = "SELECT DISTINCT {$_TABLES['prj_projects']}.pid, {$_TABLES['prj_projects']}.progress_id, {$_TABLES['prj_projects']}.estimated_end_date, {$_TABLES['prj_projects']}.last_updated_date, {$_TABLES['prj_projects']}.priority_id, ";
            $querycolumns .= "{$_TABLES['prj_projects']}.name, 'r' as role, {$_TABLES['prj_projects']}.parent_id, {$_TABLES['prj_projects']}.lhs, {$_TABLES['prj_projects']}.rhs  ";
            $queryfrom = $customFilter['clause'] . "AND {$_TABLES['prj_projPerms']}.taskID=0 and ({$_TABLES['prj_projPerms']}.uid=$userid OR {$_TABLES['prj_projPerms']}.gid in ($prjPermGroups)) ";
            $querywhere = "";
            $header = $strings["filter_custom"] . $customFilter['name'];
            break;

        default:
            $needle = '';
            $customFilter = '';
            $header = '';
    }

    if ($header != '') {
        $headingTitle = $strings["my_projects"] . "&nbsp;-&nbsp;$header";
        $headingTitle .= "(<a href=\"{$_CONF['site_url']}/nexproject/index.php?mode=add\">{$strings['add']}</a>)";
    } else {
        $headingTitle = "{$strings['my_projects']}&nbsp;&nbsp;";
        $headingTitle .= "(<a href=\"{$_CONF['site_url']}/nexproject/index.php?mode=add\">{$strings['add']}</a>)";
    }
    $headingStatusArea = '<span id="ajaxstatus_myprojects" class="pluginInfo" style="display:none">&nbsp;</span>';
    $block1->headingToggle($headingTitle,$headingStatusArea);

    $sql = $querycolumns . $queryfrom . $querywhere;

    $result = DB_query($sql);
    $block1->recordsTotal = DB_numrows($result);

    $lim=$limitbase*$block1->rowsLimit;
//    $sql .= " LIMIT $lim, $block1->rowsLimit ";
    if ($category_string == 'ctm') {
        $queryend = " ORDER BY {$_TABLES['prj_projects']}.lhs";
    }
    else {
        $queryend = " ORDER BY a.lhs";
    }
    $lim=$limitbase*$block1->rowsLimit;
    $sql = $querycolumns . $queryfrom . $querywhere . $queryend;

    $result = DB_query($sql);
    $comptListProjects = DB_numrows($result);

    if ($comptListProjects != "0") {
        $block1->openResults('false');
        $block1->labels($labels = array(
            0 => $strings["project"],
            1 => $strings["priority"],
            2 => $strings["estimated_end_date"],
            3 => $strings["lastupdated"],
            4 => $strings["owner"]),false,"false");

        for ($i = 0; $i < DB_numrows($result); $i++) {
        list($id, $idProgress,$estend,$lastupdate,$idPriority,$name,$user,$role, $parent_id, $lhs, $rhs) = DB_fetchArray($result);
        $fullname = $name;

        $permsArray=prj_getProjectPermissions($id, $uid);
        if ($permsArray['teammember']== '1') {
            $sql  = "SELECT users.fullname FROM {$_TABLES['users']} users, {$_TABLES['prj_users']} prjusers ";
            $sql .= "WHERE users.uid = prjusers.uid and prjusers.pid=$id AND prjusers.role='o'";
            $query = DB_query($sql);
            list($projectOwner) = DB_fetchArray($query);
        } else {
            $projectOwner = $user;
        }
        $owner_uid = DB_getItem($_TABLES['prj_users'], 'uid', "pid=$id AND role='o'");
        if ($owner_uid >= 2) {
            $projectOwner = DB_getItem($_TABLES['users'], 'fullname', "uid=$owner_uid");
            if (strlen($projectOwner) > 15) {
                $projectOwner = substr($projectOwner, 0, 12) . "..";
            }
        }
        if ($permsArray['teammember']== '1' || $permsArray['full']== '1') {
                $filterArr=split(",",$cookieString);
                $filterFlag=0;
                for($filterCntr=0;$filterCntr<count($filterArr);$filterCntr++){
                    if(strcmp($filterArr[$filterCntr], $id)==0){
                        $filterFlag=1;
                        break;
                    }
                }

                if($filterFlag==1){
                    $block1->openRow(true);
                }else{
                    $block1->openRow();
                }

                $block1->cellProgress($progress[$idProgress]);
                $indent = '';
                //we need to determine if the user has access to the parent that this item is related to.
                //we do this by determining which project is on its left hand side.
                $testparent=DB_getItem($_TABLES['prj_projects'],"parent_id","pid={$id}");
                $aGroups=array();
                $groups = SEC_getUserGroups($uid);
                foreach ($groups as $gid) {
                    $aGroups[] = $gid;
                }
                $prjPermGroups = implode(',',$aGroups);
                $testsql  = "SELECT a.* ";
                $testsql .= "FROM  {$_TABLES['prj_projPerms']} a ";
                $testsql .= "WHERE a.pid={$testparent} ";
                $testsql .= " AND a.taskID=0 AND (a.uid={$_USER['uid']} OR a.gid in ($prjPermGroups)) ";
                $testres=DB_query($testsql);
                $testrows=DB_numRows($testres);

                if($testrows>0 && $testparent>0){
                    if ($parent_id != 0) {
                        $level = prj_getProjectLevel($id);
                        for ($z = 1; $z < $level; $z++) {
                            $indent .= '&nbsp;&nbsp;';
                        }
                        $indent .= $subTaskImg;
                    }
                }



                if(strlen($name)>$_PRJCONF['project_name_length'] ){
                    $span="<span title=\"{$name}\">";
                    $name=substr($name,0,$_PRJCONF['project_name_length'] );
                    $name .="...";
                    $name=$span . $name . "</span>";
                    }

                $block1->cellRow($indent . $blockPage->buildLink("{$_CONF['site_url']}/nexproject/viewproject.php?pid=$id", $name, "context", $fullname,$id));
                $block1->cellRow($priority[$idPriority]);
                $block1->cellRow(strftime("%Y/%m/%d", $estend));
                $block1->cellRow(strftime("%Y/%m/%d %H:%M", $lastupdate));
                $block1->cellRow($projectOwner);
                $block1->closeRow();
            }
        }
        $block1->closeResults();
        $pages=intval($block1->recordsTotal/$block1->rowsLimit);
        if(fmod($block1->recordsTotal,$block1->rowsLimit ) > 0){
            $pages+=1;
            }
        if ($pages > 1) {
            for($pagecntr=0;$pagecntr<$pages;$pagecntr++){
                echo '<span  style="text-decoration:underline;cursor: hand" onclick=\'setCookie("myprjmin","';
                echo $pagecntr ;
                echo '","","");prj_getMyProjects("", "", "myprojects")\'>';
                if($limitbase==$pagecntr){
                    echo '<span style="color:red">';
                    echo  $pagecntr+1;
                    echo '</span>';
                } else {
                    echo  $pagecntr+1;
                }
                echo  '</span>&nbsp;';
                }
        }
        echo '</div>';
        echo '<!--endMyProjects-->';
    } else {
        $block1->noresults();
    }
    $block1->closeToggle();
    $block1->closeFormResults();
}



function prj_displayMyTasks( &$blockPage, $pid){
    global $_TABLES, $_CONF, $_USER, $subTaskImg, $progress, $priority, $strings, $labels, $_PRJCONF, $_COOKIE;

    $limitbase=$_COOKIE['mytasksmin'];
    if($limitbase=='') {
        $limitbase=0;
    }
    $useThisTIDforAjax=0;

    if($blockPage==NULL or $blockPage==''){
        $blockPage = new block();
    }

    $uid = $_USER['uid'];
    $temptoken=prj_getProjectPermissions($pid, $uid);
    $membertoken = $temptoken['teammember'];

    $block2 = new block();
    $block2->form = "taP";
    $block2->openForm($_CONF['site_url'] . "/nexproject/viewproject.php");
    $headingTitle = $strings['tasks'];
    $headingTitle = "{$strings['tasks']}";
    if ($membertoken !=0 ) $headingTitle .= "&nbsp;&nbsp;(<a href=\"{$_CONF['site_url']}/nexproject/viewproject.php?mode=add&pid=$pid\">{$strings["add"]}</a>)";
    $headingStatusArea = '<span id="ajaxstatus_tasks" class="pluginInfo" style="display:none">&nbsp;</span>';
    $block2->headingToggle( $headingTitle,$headingStatusArea );
    $block2->borne = $blockPage->returnBorne("2");

    $block2->rowsLimit = $_PRJCONF['project_task_block_rows'];
    $lim=$limitbase*$block2->rowsLimit;

    echo '<!--startMyTasks-->';
    echo '<div id="divMyTasks" style="padding-bottom:10px;">';
    $sql1 = "SELECT {$_TABLES['prj_tasks']}.tid FROM {$_TABLES['prj_tasks']} WHERE {$_TABLES['prj_tasks']}.pid=$pid ";
    $result = DB_query ($sql1);
    $block2->recordsTotal = DB_numrows($result);

    $sql2 = "SELECT {$_TABLES['prj_tasks']}.tid, {$_TABLES['prj_tasks']}.progress_id, ";
    $sql2 .= "{$_TABLES['prj_tasks']}.status_id, {$_TABLES['prj_tasks']}.priority_id, {$_TABLES['prj_tasks']}.name, ";
    $sql2 .= "{$_TABLES['users']}.fullname, {$_TABLES['prj_tasks']}.parent_task, {$_TABLES['prj_tasks']}.last_updated_date, ";
    $sql2 .= "{$_TABLES['prj_tasks']}.lhs, {$_TABLES['prj_tasks']}.rhs ";
    $sql2 .= "FROM {$_TABLES['prj_tasks']}, {$_TABLES['prj_task_users']}, {$_TABLES['users']} ";
    $sql2 .= "WHERE {$_TABLES['prj_tasks']}.pid='$pid' AND {$_TABLES['prj_task_users']}.tid={$_TABLES['prj_tasks']}.tid ";
    $sql2 .= "AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_task_users']}.role='o' ";
    $sql2 .= "ORDER BY lhs ASC ";
    $sql2 .= " LIMIT $lim, $block2->rowsLimit ";

    $result = DB_query($sql2);
    $comptListTasks = DB_numrows($result);

    if ($comptListTasks > 0) {
        $block2->openResults(false);
        $block2->labels($labels = array(
            0 => $strings["task"],
            1 => $strings["priority"],
            2 => $strings["lastupdated"],
            3 => $strings["owner"]),true,false);

        for ($i = 0; $i < DB_numrows($result); $i++) {
            list(
                $listTask->pro_id[$i], $listTask->pro_progress[$i],
                $listTask->pro_status[$i], $listTask->pro_priority[$i], $listTask->pro_name[$i],
                $listTask->username[$i], $listTask->parent[$i], $listTask->lastupdated[$i],
                $listTask->pro_lhs[$i],$listTask->pro_rhs[$i]
                ) = DB_fetchArray($result);
            if($i==0){
                $useThisTIDforAjax=$listTask->pro_id[0];
                }
            if (strlen($listTask->pro_name[$i]) > 35 ) {
                $listTasks->pro_name[$i] = substr($listTasks->pro_name[$i],0,35) . "....";
            }
            if (strlen($listTask->username[$i]) > 15 ) {
                $listTask->username[$i] = substr($listTask->username[$i],0,12) . "..";
            }
            if ($listTask->pro_org_id[$i] == "1") {
                $listTask->pro_org_name[$i] = $strings["none"];
            }
            $idStatus = $listTask->pro_status[$i];
            $idPriority = $listTask->pro_priority[$i];
            $idProgress = $listTask->pro_progress[$i];
            $block2->openRow();
            $block2->cellProgress($progress[$idProgress]);
            $indent = '';
            if ($listTask->parent[$i] != 0) {
                $level = prj_getTaskLevel($listTask->pro_id[$i]);
                for ($z = 1; $z < $level; $z++) {
                    $indent .= '&nbsp;&nbsp;';
                }
                $indent .= $subTaskImg;
            }

            $taskname = $listTask->pro_name[$i];
            if(strlen($taskname)>$_PRJCONF['project_name_length'] ){
                $taskname=substr($taskname,0,$_PRJCONF['project_name_length'] );
                $taskname .="...";
            }
            //here's where the task is displayed
            if ($membertoken !=0 ) {
                $block2->cellRow($indent . $blockPage->buildLink($_CONF['site_url'] .
                    "/nexproject/viewproject.php?mode=view&id=".$listTask->pro_id[$i],$taskname,"context",'',$pid,$listTask->pro_id[$i]));
            } else {
                $block2->cellRow($indent . $blockPage->buildLink($_CONF['site_url'] .
                    "/nexproject/viewproject.php?mode=view&id=".$listTask->pro_id[$i],$taskname,"in",'',$pid,$listTask->pro_id[$i]));
            }
            $actionlinkurl = $_CONF['site_url'] . '/nexproject/viewproject.php?id=' . $listTask->pro_id[$i] . '&pid='.$pid;
            $block2->cellRow($priority[$idPriority]);

           // $block2->cellRow($status[$idStatus]);
            $block2->cellRow(strftime("%m-%d %H:%M", $listTask->lastupdated[$i]));
            $block2->cellRow($listTask->username[$i]);
            $block2->closeRow();
        }

        $block2->closeResults();

        $pages=intval($block2->recordsTotal/$block2->rowsLimit);
        if(fmod($block2->recordsTotal,$block2->rowsLimit)>0){
            $pages+=1;
            }
        if ($pages > 1) {
            for($pagecntr=0;$pagecntr<$pages;$pagecntr++) {
                echo '<span  style="text-decoration:underline;cursor: hand" onclick=\'setCookie("mytasksmin","';
                echo $pagecntr ;
                echo '","","");prj_getMyTasks("refresh", "' . $useThisTIDforAjax . '" )\'>';
                if($limitbase==$pagecntr){
                    echo '<span style="color:red">';
                    echo  $pagecntr+1;
                    echo '</span>';
                } else {
                    echo  $pagecntr+1;
                }
                echo  '</span>&nbsp;';
            }
            echo '&nbsp;&nbsp;<span  style="text-decoration:underline;cursor: hand" TITLE="Return to page 1" onclick=\'setCookie("mytasksmin","","","");prj_getMyTasks("refresh", "' . $useThisTIDforAjax . '" )\'>';
            echo '<<</span>';
        }

        echo '</div>';
        echo '<!--endMyTasks-->';

        echo '<input type=hidden name=pid value='. $pid . '>';
        $block2->closeToggle();
        $block2->closeFormResults();

    } else {
        $block2->noresults();
        echo '<input type=hidden name=pid value='. $pid . '>';
        $block2->closeToggle();
        $block2->closeFormResults();
    }

    $block2->closeToggle();
    $block2->closeForm();

}


function prj_displayAllProjects( &$blockPage){
    global $_TABLES, $_CONF, $_USER, $subTaskImg, $progress, $priority, $strings, $labels,$_PRJCONF, $_COOKIE;

    $limitbase=$_COOKIE['allprjmin'];
    if($limitbase==''){
        $limitbase=0;
    }

    if (isset($_USER['uid'])) {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }
        if($blockPage==NULL or $blockPage==''){
            $blockPage = new block();
        }

    $filter = COM_applyFilter($_COOKIE['filter']);
    $category_string = substr("$filter", 0, 3);

    // Get a list of groups user is a member of and setup to be used in SQL to test user can view project
    $groups = SEC_getUserGroups($uid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);

    if (SEC_inGroup('Root')) {
        $querycolumns  = "SELECT DISTINCT a.pid, a.progress_id, a.status_id, a.priority_id, a.name, ";
        $querycolumns .= "a.last_updated_date, a.lhs, a.rhs, a.parent_id  ";
        $queryfrom    = "FROM {$_TABLES['prj_projects']} a";
        $querywhere = " WHERE 1=1 ";
    }
    else {
        $querycolumns  = "SELECT DISTINCT a.pid, a.progress_id, a.status_id, a.priority_id, a.name, ";
        $querycolumns .= "a.last_updated_date, a.lhs, a.rhs, a.parent_id  ";
        $queryfrom    = "FROM {$_TABLES['prj_projects']} a, {$_TABLES['prj_projPerms']} b ";
        $querywhere  .= "WHERE b.pid =a.pid";
        $querywhere  .= " AND b.taskID=0 AND (b.uid=$userid OR b.gid in ($prjPermGroups)) ";
    }

    switch ($category_string) {
        case 'cat':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_category']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.category_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_category'], 0, $needle);
            break;

        case 'loc':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_location']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.location_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_locations'], 0, $needle);
            break;

        case 'dep':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_department']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.department_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_departments'], 0, $needle);
            break;

        case 'pri':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.priority_id=$needle ";
            $header = $strings["filter_priority"] . $priority[$needle];
            break;

        case 'pro':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.progress_id=$needle ";
            $header = $strings["filter_progress"] . $progress[$needle];
            break;

        case 'sta':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.status_id=$needle ";
            $header = $strings["filter_status"] . $status[$needle];
            break;

        case 'ctm':
            if (SEC_inGroup('Root')) {
                $querycolumns  = "SELECT DISTINCT {$_TABLES['prj_projects']}.pid, {$_TABLES['prj_projects']}.progress_id, {$_TABLES['prj_projects']}.status_id, {$_TABLES['prj_projects']}.priority_id, {$_TABLES['prj_projects']}.name, ";
                $querycolumns .= "{$_TABLES['prj_projects']}.last_updated_date, {$_TABLES['prj_projects']}.lhs, {$_TABLES['prj_projects']}.rhs, {$_TABLES['prj_projects']}.parent_id  ";
                $querywhere = "";
            }
            else {
                $querycolumns  = "SELECT DISTINCT {$_TABLES['prj_projects']}.pid, {$_TABLES['prj_projects']}.progress_id, {$_TABLES['prj_projects']}.status_id, {$_TABLES['prj_projects']}.priority_id, {$_TABLES['prj_projects']}.name, ";
                $querycolumns .= "{$_TABLES['prj_projects']}.last_updated_date, {$_TABLES['prj_projects']}.lhs, {$_TABLES['prj_projects']}.rhs, {$_TABLES['prj_projects']}.parent_id, {$_TABLES['prj_projPerms']}.viewRead  ";
                $querywhere  = "WHERE b.pid =a.pid";
                $querywhere  .= " AND b.taskID=0 AND (b.uid=$userid OR b.gid in ($prjPermGroups)) ";
            }

            $needle = substr("$filter", 3, 3);
            $customFilter = prj_constructFilter($needle);
            $queryfrom = $customFilter['clause'];
            $header = $strings["filter_custom"] . $customFilter['name'];;
            break;

        default:
            $needle = '';
            $customFilter = '';
            $header = '';
    }

    $block1 = new block();
    $block1->form = "allP";
    $block1->openForm($_CONF['site_url'] . "/nexproject/projects.php?" . "#" . $block1->form . "Anchor");

    if ($header != '') {
        $headingTitle = $strings["projects"] . "&nbsp;-&nbsp;$header";
    } else {
        $headingTitle = "{$strings['projects']}&nbsp;-&nbsp;{$strings['allprojects']}&nbsp;&nbsp;";
        $headingTitle .= "(<a href=\"{$_CONF['site_url']}/nexproject/index.php?mode=add\">{$strings['add']}</a>)";
    }
    $headingStatusArea = '<span id="ajaxstatus_myprojects" class="pluginInfo" style="display:none">&nbsp;</span>';
    $block1->heading($headingTitle,$headingStatusArea);

    $block1->borne = $blockPage->returnBorne("1");

    $block1->rowsLimit = $_PRJCONF['project_block_rows'];
    if ($category_string == 'ctm') {
        $queryend = " ORDER BY {$_TABLES['prj_projects']}.lhs";
    }
    else {
        $queryend = " ORDER BY a.lhs";
    }
    $lim=$limitbase*$block1->rowsLimit;

    $query = $querycolumns . $queryfrom . $querywhere . $queryend;

    $countRes= DB_query($query);
    $block1->recordsTotal = DB_numrows($countRes);

    $query .= " LIMIT $lim, $block1->rowsLimit ";

    $result = DB_query($query);

    echo '<!--startMyProjects-->';
    echo '<div id="divMyProjects" style="padding-bottom:0px;">';

    $comptListProjects = DB_numrows($result);
    if ($comptListProjects != "0") {
        $block1->openResults('false');
        $block1->labels($labels = array(
            0 => $strings["project"],
            1 => $strings["priority"],
            2 => $strings["lastupdated"],
            3 => $strings["owner"]), false,"false");

        for ($i = 0; $i < DB_numrows($result); $i++) {
            list($id, $idProgress,$idStatus,$idPriority,$projectName,$lastupdated,$lhs,$rhs,$parent_id) = DB_fetchArray($result);

            $pArray = prj_getProjectPermissions($id, $userid);   //based on the projectID, fetch the permissions for this user...
            if($pArray['monitor'] =='1' || $pArray['teammember'] =='1' || $pArray['full'] =='1'){
                $fullname = $projectName;
                $owner_uid = DB_getItem($_TABLES['prj_users'], 'uid', "pid=$id AND role='o'");
                if ($owner_uid >= 2) {
                    $projectOwner = DB_getItem($_TABLES['users'], 'fullname', "uid=$owner_uid");
                    if (strlen($projectOwner) > 15) {
                        $projectOwner = substr($projectOwner, 0, 12) . "..";
                    }
                }
                $block1->openRow();
               // $block1->checkboxRow($id);
                $block1->cellProgress($progress[$idProgress]);
                $indent = '';
                //we need to determine if the user has access to the parent that this item is related to.
                //we do this by determining which project is on its left hand side.

                $testparent=DB_getItem($_TABLES['prj_projects'],"parent_id","pid={$id}");
                $aGroups=array();
                $groups = SEC_getUserGroups($uid);
                foreach ($groups as $gid) {
                    $aGroups[] = $gid;
                }
                $prjPermGroups = implode(',',$aGroups);
                $testsql  = "SELECT a.* ";
                $testsql .= "FROM  {$_TABLES['prj_projPerms']} a ";
                $testsql .= "WHERE a.pid={$testparent} ";
                $testsql .= " AND a.taskID=0 AND (a.uid={$_USER['uid']} OR a.gid in ($prjPermGroups)) ";
                $testres=DB_query($testsql);
                $testrows=DB_numRows($testres);
                if($testrows>0 && $testparent>0){
                    if ($parent_id != 0) {
                        $level = prj_getProjectLevel($id);
                        for ($z = 1; $z < $level; $z++) {
                            $indent .= '&nbsp;&nbsp;';
                        }
                        $indent .= $subTaskImg;
                    }
                }

                if (strlen($projectName) > $_PRJCONF['project_name_length']) {
                    $span="<span title=\"{$projectName}\">";
                    $projectName = substr($projectName, 0, $_PRJCONF['project_name_length']) . "....";
                    $projectName=$span . $projectName . "</span>";
                }

                $block1->cellRow($indent . $blockPage->buildLink("{$_CONF['site_url']}/nexproject/viewproject.php?pid=$id",$projectName, "context",$fullname,$id));
                $block1->cellRow($priority[$idPriority]);
                //$block1->cellProgress($status[$idStatus]);
                $block1->cellRow(strftime("%Y/%m/%d %H:%M", $lastupdated));
                $block1->cellRow($projectOwner);
                $block1->closeRow();
            }
        } //end for
        $block1->closeResults();
        //$block1->bornesFooter("1", $blockPage->bornesNumber, "", "typeProjects=$typeProjects");

        $pages=intval($block1->recordsTotal/$block1->rowsLimit);
        if(fmod($block1->recordsTotal,$block1->rowsLimit) > 0) {
            $pages+=1;
            }
        if ($pages > 1) {
            for($pagecntr=0;$pagecntr<$pages;$pagecntr++) {
                echo '<span  style="text-decoration:underline;cursor: hand" onclick=\'setCookie("allprjmin","';
                echo $pagecntr ;
                echo '","","");prj_getMyProjects("", "", "allprojects")\'>';
                if($limitbase==$pagecntr){
                    echo '<span style="color:red">';
                    echo  $pagecntr+1;
                    echo '</span>';
                }else{
                    echo  $pagecntr+1;
                }
                echo  '</span>&nbsp;';
            }
        }

    } else {
        $block1->noresults();
    }
    echo '</div>';
    echo '<!--endMyProjects-->';
    $block1->closeToggle();
    $block1->closeFormResults();

}


function prj_displayMyProjectTasks(&$blockPage){
    global $_TABLES, $_CONF, $_USER, $_COOKIE,$subTaskImg, $progress, $priority, $strings, $labels,$_PRJCONF;

    $limitbase=$_COOKIE['alltasksmin'];
    if($limitbase==''){
        $limitbase=0;
    }
    $useThisTIDforAjax=0;

    $filterCSV=COM_applyFilter($_COOKIE['filterTasks']);



    if($blockPage==NULL or $blockPage==''){
        $blockPage = new block();
    }

    $block2 = new block();
    if ($msg != "") {
        require_once("includes/messages.php");
        $blockPage->messagebox($msgLabel);
    }

    if (!isset($_USER['uid']) OR $_USER['uid'] == "") {
        $uid = 1;
    } else {
        $uid = $_USER['uid'];
    }
    //my tasks
    $blockPage->bornesNumber = "2";
    $block2 = new block();
    $block2->form = "taP";

    $block2->openForm($_CONF['site_url'] . "/nexproject/index.php?" . "#" . $block2->form . "Anchor");
    $headingTitle = $strings['my_tasks'];
    $headingStatusArea = '<span id="ajaxstatus_tasks" class="pluginInfo" style="display:none">&nbsp;</span>';
    $block2->headingToggle( $headingTitle,$headingStatusArea );

    $block2->borne = $blockPage->returnBorne("2");
    $block2->rowsLimit = $_PRJCONF['task_block_rows'];
    $lim=$limitbase*$block2->rowsLimit;

    echo '<!--startMyTasks-->';
    echo '<div id="divMyTasks">';

    $sql  = "SELECT {$_TABLES['prj_tasks']}.tid FROM {$_TABLES['prj_tasks']}, {$_TABLES['prj_task_users']}, {$_TABLES['users']} ";
    $sql .= "WHERE {$_TABLES['prj_task_users']}.uid=$uid AND {$_TABLES['prj_task_users']}.tid={$_TABLES['prj_tasks']}.tid ";
    $sql .= "AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_task_users']}.uid=$uid ";
    $sql .= "AND {$_TABLES['prj_task_users']}.role='o' AND {$_TABLES['prj_tasks']}.status_id in (0,3) ";

    $result = DB_query($sql);
    $block2->recordsTotal = DB_numrows($result);

    $lim=$limitbase*$block2->rowsLimit;
    $sql  = "SELECT {$_TABLES['prj_tasks']}.tid,{$_TABLES['prj_tasks']}.progress_id, {$_TABLES['prj_projects']}.name, ";
    $sql .= "{$_TABLES['prj_tasks']}.priority_id, {$_TABLES['prj_tasks']}.name, {$_TABLES['prj_tasks']}.estimated_end_date, ";
    $sql .= "{$_TABLES['prj_tasks']}.start_date, {$_TABLES['prj_tasks']}.pid  FROM {$_TABLES['prj_tasks']}, ";
    $sql .= "{$_TABLES['prj_task_users']}, {$_TABLES['users']}, {$_TABLES['prj_projects']} ";
    $sql .= "WHERE {$_TABLES['prj_task_users']}.uid=$uid AND {$_TABLES['prj_task_users']}.tid={$_TABLES['prj_tasks']}.tid ";
    $sql .= "AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_task_users']}.role='o' ";
    $sql .= "AND {$_TABLES['prj_task_users']}.uid=$uid AND {$_TABLES['prj_tasks']}.pid={$_TABLES['prj_projects']}.pid ";
    $sql .= "AND {$_TABLES['prj_tasks']}.status_id in (0,3) ";
    if($filterCSV!=''){
        $sql .= "AND  {$_TABLES['prj_tasks']}.pid  in ({$filterCSV})";
    }
    $sql .= " ORDER BY {$_TABLES['prj_tasks']}.estimated_end_date ";
    $sql .= " LIMIT $lim, $block2->rowsLimit ";

    $result = DB_query($sql,true);
    $comptListTasks = DB_numrows($result);
    if ($result == FALSE) {  //remove the filterCSV as there might be a cookie issue with it...
        $sql  = "SELECT {$_TABLES['prj_tasks']}.tid,{$_TABLES['prj_tasks']}.progress_id, {$_TABLES['prj_projects']}.name, ";
        $sql .= "{$_TABLES['prj_tasks']}.priority_id, {$_TABLES['prj_tasks']}.name, {$_TABLES['prj_tasks']}.estimated_end_date, ";
        $sql .= "{$_TABLES['prj_tasks']}.start_date, {$_TABLES['prj_tasks']}.pid  FROM {$_TABLES['prj_tasks']}, ";
        $sql .= "{$_TABLES['prj_task_users']}, {$_TABLES['users']}, {$_TABLES['prj_projects']} ";
        $sql .= "WHERE {$_TABLES['prj_task_users']}.uid=$uid AND {$_TABLES['prj_task_users']}.tid={$_TABLES['prj_tasks']}.tid ";
        $sql .= "AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_task_users']}.role='o' ";
        $sql .= "AND {$_TABLES['prj_task_users']}.uid=$uid AND {$_TABLES['prj_tasks']}.pid={$_TABLES['prj_projects']}.pid ";
        $sql .= "AND {$_TABLES['prj_tasks']}.status_id in (0,3) ";
        $sql .= " ORDER BY {$_TABLES['prj_tasks']}.estimated_end_date ";
        $sql .= " LIMIT $lim, $block2->rowsLimit ";
        $result = DB_query($sql);
        $comptListTasks = DB_numrows($result);
    }


    if ($comptListTasks != "0") {
        $block2->openResults(false);
        $block2->labels($labels = array(
            0 => $strings["task"],
            1 => $strings["priority"],
            2 => $strings["project"],
            3 => $strings["start_date"],
            4 => $strings["estimated_end_date"]), "true");

        for ($i = 0; $i < DB_numrows($result); $i++) {
            list($tid,$idProgress,$projectname,$idPriority,$taskname,$estenddate,$startdate,$pid) = DB_fetchArray($result);
            $full_projectname = $projectname;
            $full_taskname = $taskname;
            if (strlen($taskname) > 25) {
                $taskname = substr($taskname, 0, 25) . "....";
            }
            if (strlen($projectname) > 20) {
                $projectname = substr($projectname, 0, 20) . "....";
            }

            $block2->openRow();
            //$block2->checkboxRow($pid);
            $block2->cellProgress($progress[$idProgress]);
            $block2->cellRow($blockPage->buildLink("{$_CONF['site_url']}/nexproject/viewproject.php?mode=view&id=$tid",$taskname, "mytaskcontext",$full_taskname,'',$tid));
            $block2->cellRow($priority[$idPriority]);
            $block2->cellRow($blockPage->buildLink("{$_CONF['site_url']}/nexproject/viewproject.php?pid=$pid", $projectname, "context",$full_projectname,$pid));
            $block2->cellRow(strftime("%Y/%m/%d", $startdate));
            $block2->cellRow(strftime("%Y/%m/%d", $estenddate));
            $block2->closeRow();
        }
        $block2->closeResults();
        $pages=intval($block2->recordsTotal/$block2->rowsLimit);
        if(fmod($block2->recordsTotal,$block2->rowsLimit) > 0){
            $pages+=1;
            }
        if ($pages > 1) {
            for($pagecntr=0;$pagecntr<$pages;$pagecntr++) {
                echo '<span  style="text-decoration:underline;cursor: hand" onclick=\'setCookie("alltasksmin","';
                echo $pagecntr ;
                echo '","","");prj_getMyTasks("myprj_refresh", "' . $useThisTIDforAjax . '" )\'>';
                if($limitbase==$pagecntr){
                    echo '<span style="color:red">';
                    echo  $pagecntr+1;
                    echo '</span>';
                } else {
                    echo  $pagecntr+1;
                }
                echo  '</span>&nbsp;';
            }
            echo '&nbsp;&nbsp;<span  style="text-decoration:underline;cursor: hand" TITLE="Return to page 1" onclick=\'setCookie("alltasksmin","","","");prj_getMyTasks("myprj_refresh", "' . $useThisTIDforAjax . '" )\'>';
            echo '<<</span>';
        }
    } else {
        $block2->noresults();
    }
    echo '</div>';
    echo '<!--endMyTasks-->';

    echo '<input type=hidden name=pid value=' . $pid . '>';
    $block2->closeToggle();
    $block2->closeFormResults();
}






//simple csv function to eliminate a member of a comma separated list...


function prj_setFilter($pid){
    global $_COOKIES;
    $currentCookie = $_COOKIE['filterTasks'];

    $pos=strstr($currentCookie, $pid);
    if($pos!=FALSE){
        //the cookie has the pid in it already.
        //this means remove that pid.
        $pos=strpos($currentCookie, $pid);
        $left=substr($currentCookie, 0, $pos);
        if(strlen($left)>0){
            if(substr($left,-1,1)==','){
                $left=substr($left,0,strlen($left)-1);
            }
        }
        $right=substr($currentCookie, $pos+strlen($pid), strlen($currentCookie));
        $currentCookie=$left . $right;
    }else{
        //add the pid to the cookie
        $slen= strlen($currentCookie);
        if($slen==0){
            $currentCookie ="{$pid}";
        }else{
            $currentCookie .=",{$pid}";
        }
    }
    if(strlen($currentCookie)>0){
            if(substr($currentCookie,0,1)==','){
                $currentCookie=substr($currentCookie,1,strlen($currentCookie));
            }
        }
    //setcookie('filterTasks',$currentCookie);

    }

?>