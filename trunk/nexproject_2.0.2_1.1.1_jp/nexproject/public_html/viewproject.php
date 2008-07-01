<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | viewproject.php                                                           |
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
require_once($_CONF['path_html'] ."nexproject/includes/library.php");
include_once($_CONF['path_html'] . "nexproject/includes/messages.php");
require_once($_CONF['path_html'] ."nexproject/includes/lib-projects.php"); 
include_once($_CONF['path_system'] . 'classes/navbar.class.php');

if($_COOKIE['windowwidth']=='') setcookie('windowwidth',$_PRJCONF['min_graph_width']);
if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

if(isset($_COOKIE['gdate1'])){
            $gdate1 = $_COOKIE['gdate1'];
        }
        else{
            if (isset($_GET['gdate1']) AND $_GET['gdate1'] != '') {
                $gdate1 = $_GET['gdate1'];
            } elseif (isset($_POST['gdate1']) AND $_POST['gdate1'] != '') {
                $gdate1 = $_POST['gdate1'];
            } else {
                $gdate1 = strftime('%Y/%m/%d',strtotime('-2 weeks'));
            }
        }

        if(isset($_COOKIE['gdate2'])){
            $gdate2 = $_COOKIE['gdate2'];
        }
        else{
            if (isset($_POST['gdate2']) AND $_POST['gdate2'] != '') {
                $gdate2 = $_POST['gdate2'];
            } elseif (isset($_GET['gdate2']) AND $_GET['gdate2'] != '') {
                $gdate2 = $_GET['gdate2'];
            } else {
                $gdate2 = strftime('%Y/%m/%d',strtotime('+2 weeks'));
            }
        }
        
setCookie("gdate1",$gdate1);
setCookie("gdate2",$gdate2);

echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );

require_once("includes/block.class.php");

$id   = COM_applyFilter($_REQUEST['id'],true); 
$tid  = COM_applyFilter($_REQUEST['id'],true);
$pid  = COM_applyFilter($_REQUEST['pid'],true); 
$msg  = COM_applyFilter($_GET['msg']);
$mode = COM_applyFilter($_REQUEST['mode']);

/* Load Plugins Language File and create an array of template keys and language text */
$pluginLangPath = $_CONF['path'] . 'plugins/nexproject/language/english.txt';
$pluginLangLines = @file($pluginLangPath);
if ($pluginLangLines === false){
    echo COM_refresh($_CONF['site_url'] . '/index.php?msg=1&plugin=projects');
    exit;
}

foreach($pluginLangLines as $line){
    if (trim($line) == '' ||
        substr($line, 0, 1) == '#') {
        continue;
    }
    $tokens = explode('=', $line);
    $key = 'LANG_' . trim($tokens[0]);
    array_shift($tokens);
    $val = implode('=', $tokens);
    $pluginLangLabels[$key] = trim($val);
}

/* Record Project ID (pid) in a project session record so we can navigate back from the other plugins */
prj_updateSession($pid);

switch ($mode) {

    case 'edit':    // Edit Task
        if ($pid == 0 AND $id > 0) { // If pid not set but task id is - retrieve the pid (project id)
            $pid = DB_getItem($_TABLES['prj_tasks'], 'pid', "tid=$id");
        }
        $uid = $_USER['uid'];
        $protoken = prj_getProjectPermissions($pid, $uid,$id);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        
        if ( $protoken['full'] != 0 || $protoken['teammember'] != 0 || $ownertoken!=0 ) {
            $taskrec = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['prj_tasks']} WHERE tid=$id"));
            $result = DB_query("SELECT pid, name FROM {$_TABLES['prj_projects']} WHERE pid=$pid");
            list($pid,$name ) = DB_fetchArray($result);
            $edit_icons = prj_edit_task_icons($pid,$id,'edit');
            $p = new Template($_CONF['path_layout'] . 'nexproject');
            $p->set_file ('task', 'edittask.thtml');
            $p->set_var('breadcrumb_trail',prj_breadcrumbs($tid,$pid,$strings["edit_task"],$strings["edit_subtask"]));
            $p->set_var($pluginLangLabels);
            $p->set_var('site_url',$_CONF['site_url']);
            $p->set_var('layout_url',$_CONF['layout_url']);
            $p->set_var('mode','save');
            $p->set_var('id',$id);
            $p->set_var('pid',$pid);
            $parent_task_options = '<option value="0">Top Level Task</option>';
            $parent_task_options .= prj_taskParentoptions($taskrec['parent_task']);
            $p->set_var('parent_task_options',$parent_task_options);
            $p->set_var('edit_icons',$edit_icons);
            $p->set_var('LANG_heading',$pluginLangLabels['LANG_edit_task']);
            $p->set_var('project_name',$name);
            $p->set_var('show_resources','none');
            prj_setTemplateVars($p,$taskrec);

            $p->set_var('priority_options',selectBox2($priority,$taskrec['priority_id']));
            $p->set_var('status_options',selectBox2($status,$taskrec['status_id']));
            $p->set_var('progress_options',selectBox2($progress,$taskrec['progress_id']));
            $p->set_var('duration_options',selectBox2($duration,$taskrec['duration_type_id']));
  
            $taskowner = DB_getItem($_TABLES['prj_task_users'], 'uid',  "tid=$id AND role='o'");
           
            $p->set_var('resource_options',prj_getTeamMembersOptionList($pid,$taskowner));
            
            $p->parse ('output', 'task');
            echo $p->finish ($p->get_var('output'));
            
        } else {
            $msg='permissiondenied';
            echo prj_breadcrumbs($tid,$pid);
            $block = new block();
            if ($msg != '') {
                include('includes/messages.php');
                $block->messagebox($msgLabel);
            }
            $block->heading($strings['edit_projects']);
            $block->openContent();
            $block->contentRow('','<input type="button" name="goback" value="'.$strings['back'].'" onClick="history.back();">');
            $block->closeContent();
            $block->closeForm();
        }
        echo COM_siteFooter();
        exit;
        break;

    case 'save':    // Save updated Task    
        if ($pid == 0 AND $id > 0) { // If pid not set but task id is - retrieve the pid (project id)
            $pid = DB_getItem($_TABLES['prj_tasks'], 'pid', "tid=$id");
        }
        $uid = $_USER['uid'];
        $protoken = prj_getProjectPermissions($pid, $uid, $task);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        if ( $protoken['full'] != 0 || $protoken['teammember'] != 0 || $ownertoken!=0 ) {
            // Filter Incoming Variables and make them global
            // Text Variables which may contain quote's or other special characters
            $_CLEAN = array();       
            $mytextvars = array('name','description','keywords','changelog_entry');
            $_CLEAN = array_merge($_CLEAN,ppGetData($mytextvars,false,'POST','text'));
            
            // Integer only Variables
            $myintvars = array('priority_id','duration','duration_type_id','status_id','progress_id','progress','resource');
            $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'POST','int'));
            // Basic Text Variables which should not contain quote's or other special characters        
            $myvars = array('start_date','estimated_end_date','actual_end_date','planned_end_date','last_updated_date','notification_enabled_flag','make_private_enabled_flag');
            $_CLEAN = array_merge($_CLEAN,ppGetData($myvars,false,'POST'));
            
            // Convert Date to a timestamp
            $_CLEAN['start_date'] = pm_convertdate($_CLEAN['start_date']);
            $_CLEAN['estimated_end_date'] = pm_convertdate($_CLEAN['estimated_end_date']); 
            $_CLEAN['actual_end_date'] = pm_convertdate($_CLEAN['actual_end_date']);
            $_CLEAN['planned_end_date'] = pm_convertdate($_CLEAN['planned_end_date']);
            $_CLEAN['last_updated_date'] = pm_getdate();
            $_CLEAN['tid'] = $id;

            $parent_task = DB_getItem ($_TABLES['prj_tasks'], 'parent_task', "tid=$id");
            $_CLEAN['progress'] = ($_CLEAN['progress'] < 0) ? 0 : ($_CLEAN['progress'] > 100) ? 100 : $_CLEAN['progress'];
            prj_updateTask($_CLEAN);        // Update the new record values                
 
            if (!empty($resource)) {
                DB_query("DELETE FROM {$_TABLES['prj_task_users']} WHERE tid=$id AND role='o'");
                DB_query("INSERT INTO {$_TABLES['prj_task_users']} (tid, uid, role) VALUES ($id, {$_CLEAN['resource']}, 'o')");
            }
            prj_statuslog ($pid, $_USER[uid], $id, $_CLEAN['changelog_entry'], $_CLEAN['last_updated_date'], "{$_TABLES['prj_statuslog']}");
            $logentry = "Task ID-" . $id . " was editted";
            prg_updateAuditLog($logentry);
            $results = DB_query("SELECT parent_task FROM {$_TABLES['prj_tasks']} WHERE tid=$id");
            $nrows = DB_numRows($results);
            if ($nrows != "0") { 
                list($newtid) = DB_fetchArray($results);
                DB_query("UPDATE {$_TABLES['prj_tasks']} SET last_updated_date={$_CLEAN['last_updated_date']} WHERE tid=$newtid");
            }
            DB_query("UPDATE {$_TABLES['prj_tasks']} SET last_updated_date={$_CLEAN['last_updated_date']} WHERE tid=$id");
            DB_query("UPDATE {$_TABLES['prj_projects']} SET last_updated_date={$_CLEAN['last_updated_date']} WHERE pid=$pid");
            
            if($_CLEAN['make_private_enabled_flag']!='on'){
                prj_addTrickleDownTaskPerms($pid, $id);
            }else{
                prj_addTeamMemberTaskPerms($pid, $id);
            }
            
            if ($_POST['notification'] =='on') {
                prj_sendNotification($pid, $id, $action=5);
                prj_sendNotification($pid, $id, $action=6);
            }
            $msg = 'editTask';
        } else {
            COM_errorLog("projects: Error saving new task - User: $uid has no right");
        }
        break;

    case 'delete':      // Show Delete Task Confirmation Page
        if (empty($pid)) { 
            $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid',  "tid = $tid");
        }
        echo prj_breadcrumbs($tid,$pid,$strings["delete_tasks"],$strings["delete_subtasks"]);         
        $uid = $_USER['uid'];
        $protoken = prj_getProjectPermissions($pid, $uid, $task);
        $ownertoken = getProjectToken($pid,$uid,"{$_TABLES['prj_users']}");
        if ( $protoken['full'] != 0 || $protoken['teammember'] != 0 || $ownertoken!=0 ) {
            $task_lhs = DB_getItem($_TABLES['prj_tasks'],'lhs',"tid=$tid");
            $task_rhs = DB_getItem($_TABLES['prj_tasks'],'rhs',"tid=$tid");
            if (DB_count($_TABLES['prj_tasks'],'parent_task',$tid) > 0) {
                $has_sub_tasks = true;
            } else {
                $has_sub_tasks = false;
            }
            $mresult = DB_query("SELECT tid, name,rhs FROM {$_TABLES['prj_tasks']} WHERE pid=$pid AND lhs >= $task_lhs ORDER BY lhs ASC");
            $block1 = new block();
            $block1->form = "deleteP";
            $block1->openForm($_CONF['site_url'] . "/nexproject/viewproject.php?mode=erase"."#".$block1->form."Anchor");
            if ($has_sub_tasks ) {
                $block1->heading($strings["delete_parent_task"]);
                $block1->openContent();
                $block1->contentTitle($strings["delete_following_tasks"]);
                while(list($subtaskid,$subtaskname,$subtask_rhs) = DB_fetchArray($mresult)) {
                    if ($subtask_rhs > $task_rhs) {
                        break;
                    }
                    $block1->contentRow('','<input type="checkbox" name="dtid[]" value="'.$subtaskid.'" CHECKED>&nbsp;' . $subtaskname);
                }
            } else {
                list($taskid,$taskName,$task_rhs) = DB_fetchArray($mresult);
                $block1->heading($strings["delete_task"]);
                $block1->openContent();
                $block1->contentTitle($strings["delete_following_task"]);
                $block1->contentRow('',$taskName . '<input type="hidden" name="dtid[]" value="'.$taskid.'">');
            }
            $rcontent  = '<input type="hidden" name="pid" value="'.$pid.'">';
            $rcontent .= '<input type="submit" name="delete" value="'.$strings['delete'] . '" onClick="return confirm(\'Are you sure?\');">';
            $rcontent .= '&nbsp;&nbsp;<input type="button" name="cancel" value="'.$strings['cancel'].'" onClick="history.back();">';
            $block1->contentRow('',$rcontent);
            $block1->closeContent();
            $block1->closeForm();
        } else {
            $msg='permissiondenied';
            $block1 = new block();
            if ($msg != "") {
                $block1 = new block();
                include("includes/messages.php");
                $block1->messagebox($msgLabel);
            }
            if ($listTasks->parent_task[$i] == 0 ){
                $block1->heading($strings["delete_tasks"]);
            } else {
                $block1->heading($strings["delete_subtasks"]);
            }
            $block1->openContent();
            $block1->contentRow("","<input type=\"button\" name=\"goback\" value=\"".$strings["back"]."\" onClick=\"history.back();\">");
            $block1->closeContent();
            $block1->closeForm();
        }
        $display = '';
        $display .= COM_siteFooter();
        echo $display;
        $msg='removeTask';
        exit;
        break;

    case 'copy':
        if (empty($pid)) { 
            $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid',  "tid=$tid");
        }  
        $uid = $_USER['uid'];
        
        $source_task = $tid;        
        $protoken = prj_getProjectPermissions($pid, $uid, $task);
        $ownertoken = getProjectToken($pid,$uid,"{$_TABLES['prj_users']}");
        if ( $protoken['full'] != 0 || $ownertoken!=0 ) {

            //Copy the Tasks
            prj_beginCopyTasks($pid, $source_task);
            $logentry = "Task ID-{$source_task} was copied";
            prg_updateAuditLog($logentry);
       } else {
            echo prj_breadcrumbs($tid,$pid,$strings["copy_task"],$strings["copy_subtask"]);
            $block = new block();
            $msg='permissiondenied';
            include("includes/messages.php");
            $block->messagebox($msgLabel);
            if (DB_getItem ($_TABLES['prj_tasks'], 'parent_task',  "tid = $tid") == 0 ) {
                    $block->heading($strings["copy_task"]);
            } else {
                    $block->heading($strings["copy_subtask"]);
            }
            $block->openContent();
            $block->contentRow('','<input type="button" name="goback" value="'.$strings['back'].'" onClick="history.back();">');
            $block->closeContent();
            $block->closeForm();
            echo COM_siteFooter();
        }
        $msg='copyTask';
        break;

    case 'erase':         // Delete Task
        $uid = $_USER['uid'];
        if (empty($pid)) { 
            $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid',  "tid = $tid");
        }
        $protoken = prj_getProjectPermissions($pid, $uid, $task);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        if ( ($ownertoken!= 0  || $protoken['teammember'] != 0 || $protoken['full'] != 0) and is_array($_POST['dtid'])) {
            foreach ($_POST['dtid'] as $deleteTaskID) {
                $deleteTaskID = COM_applyFilter($deleteTaskID,true);
                if (DB_count($_TABLES['prj_tasks'],'tid',$deleteTaskID) == 1) {
                    DB_query("DELETE FROM {$_TABLES['prj_tasks']} WHERE tid=$deleteTaskID");
                    /* Determine if this task just deleted had subtasks - if so, make these orphaned tasks top level tasks */
                    $query = DB_query("SELECT tid FROM {$_TABLES['prj_tasks']} WHERE parent_task = $deleteTaskID");
                    while (list($orphanedTaskId) = DB_fetchArray($query)) {
                        DB_query("UPDATE {$_TABLES['prj_tasks']} SET parent_task=0 WHERE tid=$orphanedTaskId");
                    }
                    $uid = $_USER['uid'];
                    DB_query("DELETE FROM {$_TABLES['prj_task_users']} WHERE tid=$deleteTaskID and uid=$uid");
                    DB_query("DELETE FROM {$_TABLES['prj_task_users']} WHERE tid=$deleteTaskID");
                    DB_query("DELETE FROM {$_TABLES['prj_projPerms']} WHERE taskID='$deleteTaskID'");
                    $logentry = "Task ID-{$deleteTaskID} was deleted";
                    prg_updateAuditLog($logentry);
                }
            }
            $lastupdated = pm_getdate();
            DB_query( "UPDATE {$_TABLES['prj_projects']} SET last_updated_date=$lastupdated WHERE pid=$pid" );
            $msg='removeTask';
        } else {
            $msg='noremoveTask';
        }

        break;

     case 'create': // Create New Task
        $uid = $_USER['uid'];
        if (empty($pid)) { 
            $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid', "tid=$tid");
        }
        $protoken = prj_getProjectPermissions($pid, $uid);
        if ( $protoken['teammember'] != 0 || $protoken['full'] != 0) {
            // Filter Incoming Variables and make them global
            // Text Variables which may contain quote's or other special characters
            $newrec = array();
            $newrec['pid'] = $pid;        
            $mytextvars = array('name','description','keywords');
            $newrec = array_merge($newrec,ppGetData($mytextvars,false,'POST','text'));

            // Integer only Variables
            $myintvars = array('priority_id','duration','duration_type_id','status_id','progress_id','progress','resource','parent_task');
            $newrec = array_merge($newrec,ppGetData($myintvars,false,'POST','int'));
            // Basic Text Variables which should not contain quote's or other special characters        
            $myvars = array('start_date','estimated_end_date','actual_end_date','planned_end_date','last_updated_date','notification_enabled_flag','make_private_enabled_flag');
            $newrec = array_merge($newrec,ppGetData($myvars,false,'POST'));
            
            // Convert Date to a timestamp
            $newrec['start_date'] = pm_convertdate($newrec['start_date']);
            $newrec['estimated_end_date'] = pm_convertdate($newrec['estimated_end_date']); 
            if($newrec['actual_end_date']==''){
                $newrec['actual_end_date'] = $newrec['estimated_end_date']; 
            }else{
                $newrec['actual_end_date'] = pm_convertdate($newrec['actual_end_date']);
            }
            $newrec['planned_end_date'] = pm_convertdate($newrec['planned_end_date']);
            $newrec['create_date'] = pm_getdate();
            $newrec['last_updated_date'] = pm_getdate();

            if($newrec['actual_end_date']<$newrec['start_date']){
                $newrec['actual_end_date']=$newrec['start_date']+604800 ; //add 1 week to the start date...
            }
            if($newrec['estimated_end_date']<$newrec['start_date']){
                $newrec['estimated_end_date']=$newrec['start_date']+604800 ; //add 1 week to the start date...
            }
            
            $newrec['tid'] = prj_insertTask($pid,$parent_task);  // Insert the new record and update the lhs and rhs values
            prj_updateTask($newrec);        // Update the new record values
            
            
            if (empty($newrec['resource'])){
                DB_query("INSERT INTO {$_TABLES['prj_task_users']} (tid, uid, role) VALUES ('{$newrec['tid']}', $uid, 'o')" );
            } else {
                DB_query("INSERT INTO {$_TABLES['prj_task_users']} (tid, uid, role) VALUES ('{$newrec['tid']}', {$newrec['resource']},'o')");
            }

            /*********************************************/
            //create new task default permissions here
            //whoever created it, gets full perms.
            /*********************************************/
            $sql  ="insert into {$_TABLES['prj_projPerms']} (pid, taskID, uid, gid, viewRead, writeChange, fullAccess, seeDetails) values ";
            $sql .="(";
            $sql .="'$pid',";
            $sql .="'{$newrec['tid']}',";
            $sql .="'{$uid}',";
            $sql .="'0',";
            $sql .="'1',";      //viewread
            $sql .="'1',";      //writechange
            $sql .="'1',";      //fullaccess
            $sql .="'1'";       //seedetails
            $sql .=")";
            DB_query($sql);

            /*********************************************/
            //add trickle down permissions here
            //from the project to this task
            //ONLY if the 'private' task button is not clicked
            //if private, only team members get permissions
            /*********************************************/
            if($newrec['make_private_enabled_flag']!='on'){
                prj_addTrickleDownTaskPerms($pid, $newrec['tid']);
            }else{//
                prj_addTeamMemberTaskPerms($pid, $newrec['tid']);
            }

            DB_query( "UPDATE {$_TABLES['prj_projects']} SET last_updated_date={$newrec['last_updated_date']} WHERE pid=$pid" );
            prj_sendNotification($pid, $newrec['tid'], $action=3);
            prj_sendNotification($pid, $newrec['tid'], $action=4);
            $logentry = "Task ID-{$newrec['tid']} was created";
            prg_updateAuditLog($logentry);
        } else {
            COM_errorLog("projects: Error adding new task - User: $uid has no right");
        }
        $msg="createTask";
        break;

     case 'add':  // Form to add new task
        $uid=$_USER[uid];
        $protoken = prj_getProjectPermissions($pid, $uid);
        $result = DB_query("SELECT name FROM {$_TABLES['prj_projects']} WHERE pid=$pid");
        $A = DB_fetchArray($result);
        if ($msg != "") {
            include("includes/messages.php");
            $blockPage->messagebox($msgLabel);
        }
        if ($protoken['full']==1 || $protoken['teammember']==1) {
            $parent_task_options = '<option value="0">Top Level Task</option>';
            $parent_task_options .= prj_taskParentoptions($id);
            $p = new Template($_CONF['path_layout'] . 'nexproject');
            $p->set_file ('task', 'edittask.thtml');
            $p->set_var($pluginLangLabels);
            $p->set_var('breadcrumb_trail',prj_breadcrumbs($id,$pid,$strings["add_task"]));
            $p->set_var('site_url',$_CONF['site_url']);
            $p->set_var('layout_url',$_CONF['layout_url']);
            $p->set_var('mode','create');
            $p->set_var('pid',$pid);
            $p->set_var('parent_task_options',$parent_task_options);
            $p->set_var('LANG_heading',$pluginLangLabels['LANG_add_task']);
            $p->set_var('show_changelog','none');
            $p->set_var('project_name',$A['name']);
            $p->set_var('priority_options',selectBox2($priority));
            $p->set_var('status_options',selectBox2($status));
            $p->set_var('progress_options',selectBox2($progress));
            $p->set_var('duration_options',selectBox2($duration));
            $p->set_var('resource_options',prj_getTeamMembersOptionList($pid));
            $p->set_var ('VALUE_duration', '0');
            $p->set_var ('VALUE_progress', '0');

            $p->set_var('VALUE_start_date',date("Y/m/d"));
            $p->set_var('VALUE_estimated_end_date',date("Y/m/d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"))));

            $p->parse ('output', 'task');
            echo $p->finish ($p->get_var('output'));
        
        } else {
            $block = new block();
            $msg='permissiondenied';
            include("includes/messages.php");
            $block->messagebox($msgLabel);
            if ($listTasks->parent_task[$i] == 0 ){
                $block->heading($strings["addTask"]);
            } else {
                $block->heading($strings["addSubtask"]);
            }
            $block->openContent();
            $block->contentRow('','<input type="button" name="goback" value="'.$strings['back'].'" onClick="history.back();">');
            $block->closeContent();
            $block->closeForm();
        }
        echo COM_siteFooter();
        exit;
        break;
        
     case 'view' :      // View Task
        $uid=$_USER[uid];
        $taskrec = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['prj_tasks']} WHERE tid=$id"));
        $result = DB_query("SELECT pid, name FROM {$_TABLES['prj_projects']} WHERE pid={$taskrec['pid']}");
        list($pid,$name ) = DB_fetchArray($result);
        //need to use the new permissions on this project here
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        $membertoken=prj_getProjectPermissions($pid, $uid, $id); //
      
        if ($membertoken['teammember'] !=0 || $membertoken['full'] !=0 || $ownertoken!=0) {
            $edit_icons = prj_edit_task_icons($pid,$id,'view');
        } else {
            $edit_icons = '';
        }
        $p = new Template($_CONF['path_layout'] . 'nexproject');
        $p->set_file (array('task' => 'viewtask.thtml', 'tasklog' => 'tasklog_record.thtml'));
        $p->set_var('breadcrumb_trail',prj_breadcrumbs($id,$pid,$strings["view_task"],$strings["view_task"]));
        $p->set_var($pluginLangLabels);
        $p->set_var('site_url',$_CONF['site_url']);
        $p->set_var('layout_url',$_CONF['layout_url']);
        $p->set_var('mode','save');
        $p->set_var('id',$id);
        $p->set_var('pid',$pid);
        $p->set_var('LANG_heading',$pluginLangLabels['LANG_edit_task']);
        $p->set_var('edit_icons',$edit_icons);
        $p->set_var('project_name',$name);
        $p->set_var('show_resources','none');
        $p->set_var('show_submit','none');
        prj_setTemplateVars($p,$taskrec);
        $p->set_var('VALUE_progress_color',$progress[$taskrec['progress_id']]);
        $p->set_var('VALUE_duration_type',$duration[$taskrec['duration_type_id']]);
        $p->set_var('VALUE_status',$status[$taskrec['status_id']]);
        $p->set_var('VALUE_priority',$priority[$taskrec['priority_id']]);

        $taskowner = DB_getItem($_TABLES['prj_task_users'], 'uid',  "tid=$id AND role='o'");
        $p->set_var('VALUE_owner',COM_getDisplayName($taskowner));
        $sql = "SELECT uid, description, updated FROM {$_TABLES['prj_statuslog']} WHERE pid=$pid and tid=$id ORDER BY updated ASC";
        $result1 = DB_query($sql);
        if (DB_numRows($result1) > 0) {
            while (list($user, $comment, $date) = DB_fetchArray($result1)) {
                $p->set_var('member_name', DB_getItem($_TABLES['users'], 'fullname', "uid=$user"). ':&nbsp;');
                $p->set_var('log_date',strftime("%Y/%m/%d %H:%M", $date));
                if($comment == '') {
                    $p->set_var('log_entry', '<br>Edit Task completed, no comment entered');
                } else {
                    $p->set_var('log_entry', '<br>'.$comment);
                }
                $p->parse('task_log_entries','tasklog',true);
            }
        } else {
            $p->set_var('log_entry', $strings['noresults']);
            $p->parse('task_log_entries','tasklog');
        }

        $p->parse('output', 'task');
        echo $p->finish ($p->get_var('output')); 

        
        echo COM_siteFooter();
        exit;
        break;
//although we handle this in ajax, these are catch alls in the 
//event the system has the up/dn/lft/right post/get-ed to it.
        
     case 'movetaskup':
        prj_moveTaskUp($tid);
        break;

     case 'movetaskdn':
        prj_moveTaskDown($tid);
        break;        
     
     case 'movetasklft':
        prj_moveTaskLeft($tid);
        break;

     case 'movetaskrht':
        prj_moveTaskRight($tid);
        break;
       
     default:

}


if (empty($pid)) { 
    $pid = DB_getItem ($_TABLES['prj_tasks'], 'pid',  "tid = '$tid'");
}

$result = DB_query("SELECT * FROM {$_TABLES['prj_projects']} WHERE pid='$pid'");
$A = DB_fetchArray($result);
$sql  = "SELECT {$_TABLES['users']}.fullname FROM {$_TABLES['prj_users']}, {$_TABLES['users']} WHERE ";
$sql .= "{$_TABLES['prj_users']}.uid={$_TABLES['users']}.uid AND {$_TABLES['prj_users']}.role='o' AND {$_TABLES['prj_users']}.pid='$pid'";
$result = DB_query($sql);
$B = DB_fetchArray($result);
$uid = $_USER['uid'];

$temptoken=prj_getProjectPermissions($pid, $uid);
$membertoken = $temptoken['teammember'];
$ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");

if ($temptoken['teammember'] =='1' || $temptoken['monitor'] =='1' || SEC_inGroup('Root') || $ownertoken!=0){
    echo prj_breadcrumbs(0,$pid,$strings["view_project"],$strings["view_project"]);
    $blockPage = new block();
    if ($msg != '') {
        include('includes/messages.php');
        $blockPage->messagebox($msgLabel);
    }
    $idStatus = $A[status_id];
    $idPriority = $A[priority_id];
    $idProgress = $A[progress_id];
    $idObjective = $A[objective_id];

    $block1 =new block();
    $block1->form = "pdD";
    $block1->openForm($_CONF['site_url'] . "/nexproject/viewprojects.php?"."#".$block1->form."Anchor");
    $block1->headingToggle($strings["project"]." : ".$A[name]);
    $temptoken=prj_getProjectPermissions($pid, $uid);
    if ($temptoken['full']  != 0) {
        $block1->openPaletteIcon();
        $block1->paletteIcon(0,"add",$strings["add"]);         
        $block1->paletteIcon(1,"remove",$strings["delete"]);
        $block1->paletteIcon(2,"edit",$strings["edit"]);         
        $block1->paletteIcon(3,"copy",$strings["copy"]);
        $block1->closePaletteIcon();
        $block1->openPaletteScript();
        $block1->paletteScript(0,"add",$_CONF['site_url'] ."/nexproject/index.php?mode=add","true,false,false",$strings["add"]);         
        $block1->paletteScript(1,"remove",$_CONF['site_url'] ."/nexproject/index.php?mode=delete&id=". $pid,"true,true,false",$strings["delete"]);
        $block1->paletteScript(2,"edit",$_CONF['site_url'] ."/nexproject/index.php?mode=edit&id=". $pid,"true,true,false",$strings["edit"]);
        $block1->paletteScript(3,"copy",$_CONF['site_url'] . "/nexproject/index.php?mode=copy&id=". $pid,"true,true,false",$strings["copy"]);
        $block1->closePaletteScript(0,0);        
    }
    /* Display Project Details */
    echo prj_viewproject_details($pid);
    $block1->closeToggle();
    $block1->closeForm();
      
    /* Display Project Tasks */    
    $blockPage->bornesNumber = "4";
    
    prj_displayMyTasks( $blockPage, $pid);

    /* Display the Gantt Chart */
    $blockPage->bornesNumber = "5";
    $blockg = new block();
    $blockg->form = "gaP";
    $blockg->headingToggle('Gantt Chart');

    $sql = "SELECT min(start_date) as mindate, max( estimated_end_date ) as maxdate1 , ";
    $sql .= "max( planned_end_date ) as maxdate2 , max( actual_end_date ) as maxdate3 ";
    $sql .= "FROM {$_TABLES['prj_tasks']} WHERE pid='$pid' and YEAR(FROM_UNIXTIME(start_date)) > 1970";
    $qdates = DB_query($sql);

    list ($mindate,$maxdate1,$maxdate2,$maxdate3) = DB_fetchArray($qdates);
    $maxdate1 = ($maxdate1 < 0) ? 0:$maxdate1;
    $maxdate2 = ($maxdate2 < 0) ? 0:$maxdate2;
    $maxdate3 = ($maxdate3 < 0) ? 0:$maxdate3;
    $maxdate = max($maxdate1,$maxdate2,$maxdate3);
    if($mindate==''){
        $mindate=time();
        }
    if($maxdate==''){
        $maxdate=time();
        }    
    /* Convert to a string */
    $str_mindate = strftime('%Y/%m/%d',$mindate);
    $str_maxdate = strftime('%Y/%m/%d',$maxdate);

    
$p =new Template($_CONF['path_layout'] . 'nexproject');
$p->set_file ('ganttheader', 'ganttheadertasks.thtml');
$p->set_var('siteurl',$_CONF['site_url']);
$p->set_var('gdate1',$gdate1);
$p->set_var('gdate2',$gdate2);
$p->set_var('strmindate',$str_mindate);
$p->set_var('strmaxdate',$str_maxdate);
$p->set_var('pid',$pid);
$p->set_var('windowwidth',$_COOKIE['windowwidth']);
$p->set_var('mingraphwidth',$_PRJCONF['min_graph_width']);
$p->set_var('checked',$checked);
$p->parse ('output', 'ganttheader');
echo$p->finish ($p->get_var('output'));
    
    
    echo '<div id="ganttChartDIV" style="margin-top:10px;margin-bottom:10px;">';
    include "taskgantt.php";
    echo '</div>';
    $blockg->closeToggle();
    $blockg->closeForm();


    /************************************************/
    //discussion board
    /************************************************/
    $pcid = DB_getItem ($_TABLES['prj_projects'], 'cid',  "pid = $pid");
    if ($_POST['select_cid'] == '') {
        $cid = DB_getItem ($_TABLES['prj_projects'], 'cid',  "pid = $pid");
    } else {
        $cid = $_POST['select_cid'];
    }
    if ($A['is_using_docmgmt_flag'] =='Y') {
        $pcname = DB_getItem($_TABLES['fm_category'], "name", "cid=$pcid");
        $cname = DB_getItem($_TABLES['fm_category'], "name", "cid=$cid");
        $selectCategoryHTML = '<span style="padding-left:10px;">Select Category:';
        $selectCategoryHTML .= '<select name="select_cid"><option value="'.$pcid.'">'.$pcname.'</option>';
        $selectCategoryHTML .= fm_recursiveCatAddFileList($cname,$pcid).'</select>';
        $selectCategoryHTML .= '<input type="submit" value="Go"></input></span>';
        if (DB_count($_TABLES['fm_category'],'cid',$cid) > 0) {
            $blockPage->bornesNumber = "5";
            $block4 = new block();
            $block4->form = "docP";
            $block4->openForm($_CONF['site_url'] . "/nexproject/viewproject.php?"."#".$block4->form."Anchor");
            $block4->headingToggle($strings["document"]);
            if ($membertoken !=0) {
                $block4->openPaletteIcon();
                $block4->paletteIcon(0,"view",$strings["view"]);
                $block4->paletteIcon(1,"info",$strings["add"]);
                $block4->paletteIcon(2,"delete",$strings["delete"]);
                $block4->closePaletteIcon($selectCategoryHTML);
            }
            $block4->borne = $blockPage->returnBorne("4");
            $block4->rowsLimit = 5;
            $block4->sorting("files","$sortingUser[files]","{$_TABLES['fm_files']}.title ASC",
                $sortingFields = array(
                    0 => "{$_TABLES['fm_files']}.title",
                    1 => "{$_TABLES['fm_files']}.fname",
                    2 => "{$_TABLES['fm_files']}.version",
                    3 => "{$_TABLES['fm_files']}.submitter"));

            $sql  = "SELECT {$_TABLES['fm_files']}.fid,{$_TABLES['fm_files']}.title,";
            $sql .= "{$_TABLES['fm_files']}.fname,{$_TABLES['fm_files']}.version,";
            $sql .= "{$_TABLES['fm_files']}.size,{$_TABLES['fm_files']}.fname,";
            $sql .= "{$_TABLES['fm_files']}.ftype, {$_TABLES['users']}.fullname ";
            $sql .= "FROM {$_TABLES['fm_files']}, {$_TABLES['users']} ";
            $sql .= "WHERE {$_TABLES['fm_files']}.cid=$cid ";
            $sql .= "AND {$_TABLES['fm_files']}.submitter={$_TABLES['users']}.uid ";
            //$sql .= "ORDER BY $block4->sortingValue";
            $result = DB_query($sql);
            $block4->recordsTotal = DB_numrows($result);
            $sql  = "SELECT {$_TABLES['fm_files']}.fid,{$_TABLES['fm_files']}.title,";
            $sql .= "{$_TABLES['fm_files']}.fname,{$_TABLES['fm_files']}.version,";
            $sql .= "{$_TABLES['fm_files']}.size,{$_TABLES['fm_files']}.fname,";
            $sql .= "{$_TABLES['fm_files']}.ftype, {$_TABLES['users']}.fullname ";
            $sql .= "FROM {$_TABLES['fm_files']}, {$_TABLES['users']} ";
            $sql .= "WHERE {$_TABLES['fm_files']}.cid=$cid ";
            $sql .= "AND {$_TABLES['fm_files']}.submitter={$_TABLES['users']}.uid ";
            //$sql .= "ORDER BY $block4->sortingValue LIMIT $block4->borne,$block4->rowsLimit";
            $result = DB_query($sql);
            $comptListTasks = DB_numrows($result);
            if ($comptListTasks != "0") {
                $block4->openResults();
                $block4->labels($labels = array(
                    0 => $strings["file_name"],
                    1 => $strings["file_description"],
                    2 => $strings["file_version"],
                    3 => $strings["file_submitter"]),"true");

                for( $i = 0; $i < $comptListTasks; $i++ ) {
                    list($listDoc->fid[$i],$title,$fname,$curVersion,$size,$filename,$ftype, $submitter) = DB_fetchARRAY($result);
                    $block4->openRow();
                    $block4->checkboxRow($listDoc->fid[$i]);
                    $block4->cellRow($blockPage->buildLink($_CONF['site_url'] . 
                        "/nexfile/details.php?fid=" . $listDoc->fid[$i],$title,"in",'Click to View File Details'));
                    $block4->cellRow($blockPage->buildLink($_CONF['site_url'] . 
                        "/nexfile/download.php?op=download&fid=" . $listDoc->fid[$i],$filename,"in",'Click to Download File'));
                    $block4->cellRow($curVersion);
                    $block4->cellRow($submitter);
                    $block4->closeRow();
                }
                $block4->closeResults();
                $block4->bornesFooter("4",$blockPage->bornesNumber,"","pid=$pid");
            } else {
                $block4->noresults();
            }
            echo '<input type="hidden" name="pid" value="'.$pid.'">';
            $block4->closeToggle();
            $block4->closeFormResults();
            if ($membertoken !=0) {
                $block4->openPaletteScript();
                $block4->paletteScript(0,"info",$_CONF['site_url'] . 
                    "/nexfile/details.php","false,true,false",$strings["view"]);
                $block4->paletteScript(1,"add",$_CONF['site_url'] . 
                    "/nexfile/index.php?op=newfile&cid=".$cid,"true,true,false",$strings["add"]);
                $block4->paletteScript(2,"remove",$_CONF['site_url'] . 
                    "/nexfile/details.php?op=deletefile","false,true,false",$strings["delete"]);
                $block4->closePaletteScript($comptListTasks,$listDoc->fid);
            }
        }
    }
    if ($A[is_using_forum_flag] =='Y') {
        $blockPage->bornesNumber = "5";
        $block5 = new block();
        $block5->form = "forP";
        $block5->openForm($_CONF['site_url'] . "/nexproject/viewproject.php?"."#".$block2->form."Anchor");
        $block5->headingToggle($strings["forum"]);
        if ($membertoken !=0) {
            $block5->openPaletteIcon();
            $block5->paletteIcon(0,"view",$strings["view"]);
            $block5->paletteIcon(1,"info",$strings["add"]);
            $block5->closePaletteIcon();
        }
        $block5->borne = $blockPage->returnBorne("5");
        $block5->rowsLimit = 5;
        $block5->sorting('discussions',$sortingUser[forum], "{$_TABLES['gf_topic']}.lastupdated DESC",
            $sortingFields = array(
                0 => "{$_TABLES['gf_topic']}.subject",
                1 => "{$_TABLES['gf_topic']}.name",
                2 => "{$_TABLES['gf_topic']}.lastupdated",
                3 => "{$_TABLES['gf_topic']}.replies"));

        $sql  = "SELECT {$_TABLES['gf_topic']}.id,{$_TABLES['gf_topic']}.pid,";
        $sql .= "{$_TABLES['gf_topic']}.subject,{$_TABLES['users']}.fullname,";
        $sql .= "{$_TABLES['gf_topic']}.lastupdated,{$_TABLES['gf_topic']}.replies ";
        $sql .= "FROM {$_TABLES['gf_topic']}, {$_TABLES['users']} ";
        $sql .= "WHERE forum={$A['fid']} AND pid=0 ";
        $sql .= "AND {$_TABLES['gf_topic']}.uid={$_TABLES['users']}.uid ";
        $sql .= "ORDER BY $block5->sortingValue";
        $result = DB_query($sql);
        $block5->recordsTotal = DB_numrows($result);
        $sql  = "SELECT {$_TABLES['gf_topic']}.id,{$_TABLES['gf_topic']}.pid,";
        $sql .= "{$_TABLES['gf_topic']}.subject,{$_TABLES['users']}.fullname,";
        $sql .= "{$_TABLES['gf_topic']}.lastupdated,{$_TABLES['gf_topic']}.replies ";
        $sql .= "FROM {$_TABLES['gf_topic']}, {$_TABLES['users']} ";
        $sql .= "WHERE forum={$A['fid']} AND pid=0 ";
        $sql .= "AND {$_TABLES['gf_topic']}.uid={$_TABLES['users']}.uid ";
        $sql .= "ORDER BY $block5->sortingValue LIMIT $block5->borne,$block5->rowsLimit";
        $result = DB_query($sql);
        $comptListTasks = DB_numrows($result);
        if ($comptListTasks != "0") {
            $block5->openResults();
            $block5->labels($labels = array(
                0 => $strings["Subject"],
                1 => $strings["Author"],
                2 => $strings["lastUpdated"],
                3 => $strings["Replies"]),"true");

            for( $i = 0; $i < $comptListTasks; $i++ ) {
                list($listForum->topic[$i], $parent, $listForum->subject[$i], 
                    $listForum->author[$i], $listForum->date[$i], 
                    $listForum->replies[$i]) = DB_fetchArray($result);
                $block5->openRow();
                $block5->checkboxRow($listForum->topic[$i]);
                $block5->cellRow($blockPage->buildLink($_CONF['site_url'] . 
                    "/forum/viewtopic.php?forum=" .$A[fid]. "&showtopic=" .
                    $listForum->topic[$i],$listForum->subject[$i],"in",'Click to View Discussion'));
                $block5->cellRow($listForum->author[$i]);
                $block5->cellRow(strftime("%Y/%m/%d %H:%M",$listForum->date[$i]));
                $block5->cellRow($listForum->replies[$i]);
                $block5->closeRow();
            }
            $block5->closeResults();
            $block5->bornesFooter("5",$blockPage->bornesNumber,"","pid=$pid");
        } else {
            $block5->noresults();
        }
        echo '<input type="hidden" name="pid" value="'.$pid.'">';
        $block5->closeToggle();
        $block5->closeFormResults();
        if ($membertoken !=0) {
            $block5->openPaletteScript();
            $block5->paletteScript(0,"export",$_CONF['site_url'] . 
                "/forum/createtopic.php?method=postreply&forum=" . $A[fid],"false,true,false",$strings["reply"]);
            $block5->paletteScript(1,"add",$_CONF['site_url'] . 
                "/forum/createtopic.php?method=newtopic&forum=".$A[fid],"true,true,false",$strings["add"]);
            $block5->closePaletteScript($comptListTasks,$listForum->topic);
        }
    }   

    if ($temptoken['teammember'] =='1') {
        $p = new Template($_CONF['path_layout'] . 'nexproject/javascript');
        $p->set_file ('contextmenu', 'taskblock_contextmenu.thtml');
        $p->set_var('site_url',$_CONF['site_url']);
        $p->set_var('action_url',$_CONF['site_url'] . '/nexproject/viewproject.php');
        $p->set_var('imgset',$_CONF['layout_url'] . '/nexproject/images');  
        $p->parse ('output', 'contextmenu');
        echo $p->finish ($p->get_var('output'));
    }
    $p = new Template($_CONF['path_layout'] . 'nexproject/javascript');
    $p->set_file ('projectajax', 'projectajax.thtml');
    $p->parse ('output', 'projectajax');
    echo $p->finish ($p->get_var('output'));    
} else {
    echo "Sorry, you do not have access to this project or task...";
}

echo COM_siteFooter();


?>