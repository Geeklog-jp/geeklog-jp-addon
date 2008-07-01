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

require_once ('../lib-common.php');

require_once ($_CONF['path_system'] . 'classes/navbar.class.php');
require_once ('libconsole.php');

$op = COM_applyFilter($_REQUEST['op']);
$layout = COM_applyFilter($_REQUEST['layout']);
$mode = COM_applyFilter($_REQUEST['mode']);
$formid = COM_applyFilter($_REQUEST['formid'],true);
$result = COM_applyFilter($_REQUEST['result'],true);
$project_id = COM_applyFilter($_REQUEST['project_id'],true);
$selectUser = COM_applyFilter($_REQUEST['taskuser'],true);
$nomenu = COM_applyFilter($_REQUEST['nomenu'],true);
$linkedformsOption = COM_applyFilter($_REQUEST['linkedforms']);
$cookieLayout=$_REQUEST['nflayout'];
$fromFromTaskUser= COM_applyFilter($_REQUEST['taskuser'],true);

$actionurl = $_CONF['site_url'] .'/nexflow/index.php';
$errmsg = '';       // Error Message - returned from a custom inline action

// Test if user should be able to access this script
$noaccess = false;

if ($op == 'allprojects' AND ($CONF_NF['allrequestsloginrequired'] AND $_USER['uid'] < 2)) {
    $noaccess = true;
} elseif ($_USER['uid'] < 2 AND $op != 'allprojects') {
    $noaccess = true;
} elseif ($op != 'allprojects' AND $CONF_NF['taskconsolepermrequired'] AND !SEC_hasRights('nexflow.user')) {
    $noaccess = true;
}
if ($noaccess) {
    echo COM_siteHeader();
    echo COM_startBlock("Access Error");
    echo '<div style="text-align:center;padding-top:20px;">';
    echo "You do not have sufficient access.";
    echo "<p><button  onclick='javascript:history.go(-1)'>Return</button></p><br>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit();    
}

// See if we have priveledges to use the Select Task User Feature
$optLinkVars = '';
if ($selectUser > 0 AND SEC_hasRights('nexflow.admin')) {
    $usermodeUID = $selectUser;
    if (SEC_hasRights('nexflow.admin')) {
        $optLinkVars = "&taskuser=$usermodeUID";
    }
} else {
    $usermodeUID = ($_USER['uid'] > 1) ? $_USER['uid'] : 1;
}

if ($nomenu == 1 OR $_GET['singleuse'] == 1) {
    echo COM_siteHeader('none');
} elseif ($nomenu != 2) {       // Going to do a redirect to another script - don't show the siteheader
    echo COM_siteHeader('menu');
    $username = COM_getDisplayName($usermodeUID);     
    echo COM_startBlock("Workflow Task Console for: $username",'','blockheader.thtml');       
}


        
function display_reassignedTasks() {
    global $NF_CONF,$_USER,$_CONF,$_TABLES,$actionurl,$usermodeUID;
    global $optLinkVars,$errmsg,$NF_TaskStatus;
    
    $imgset = $_CONF['layout_url'] . '/nexflow/images/';

    $p = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
    $p->set_file (array (
        'report'             =>     'viewreassignedtasks.thtml',
        'records'            =>     'viewreassignedtask_record.thtml',
        'javascript'         =>     'javascript/taskconsole.thtml'));

    $p->set_var ('layout_url', $_CONF['layout_url']);
    $p->set_var ('site_url',$_CONF['site_url']);
    $p->set_var ('imgset',$imgset);
    $p->set_var ('actionurl',$actionurl);
    $p->set_var('heading1', 'Project');
    $p->set_var('heading2', 'Re-Assigned Tasks');
    $p->set_var('heading3', 'Owner');
    $p->set_var('heading4', 'Date');      
    $p->set_var('heading5', 'Actions');     

    $sql .= "SELECT a.id,a.uid,a.assignBack_uid,a.task_id,a.last_updated,b.nf_processID,b.createdDate,b.startedDate,c.taskname,b.nf_templateDataID,c.nf_templateID ";
    $sql .= "FROM {$_TABLES['nfproductionassignments']} a LEFT JOIN {$_TABLES['nfqueue']} b on b.id=a.task_id ";
    $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} c on c.id=b.nf_templateDataID ";
    $sql .= "WHERE assignBack_uid = $usermodeUID ";
    $query = DB_query($sql);
    $project_detailsLink  = '<a href="#" onClick=\'ajaxViewProjectDetails(%s,%s,%s,%s);\'>';
    $project_detailsLink .= '<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Project Details"></a>&nbsp;';
    $i=1;
    while ($reassignRec = DB_fetchArray($query,false)) {
        $nfclass= new nexflow($reassignRec['nf_processID']);
        $project_id = $nfclass->get_processVariable('PID');
        // If task is for a project on hold or in a Recycled or Killed status then do not show it
        // There should never be any tasks appearing for Killed as that workflow has been forced to complete state.
        $project_state = DB_getItem($_TABLES['nfprojects'],'status',"id='$project_id'");
        if ($project_state != 5 AND $project_state != 6 AND $project_state != 7) {
                        
            // Retrieve the project description for this task - used as Project Title
            $description = DB_getItem($_TABLES['nfprojects'],'description',"id='$project_id'");
            $project_number = DB_getItem($_TABLES['nfprojects'],'project_num',"id='$project_id'");

            if (trim($description) == '') {
                //$description = 'Not Available';
                $description = DB_getItem($_TABLES['nftemplate'],'templateName',"id='{$reassignRec['nf_templateID']}'");
            } elseif ($project_number != '') {
                $description = "{$project_number} - $description";
            }
            $p->set_var ('project_title',$description);
            $p->set_var ('task_name',$reassignRec['taskname']);
            $p->set_var ('task_id', $reassignRec['task_id']);
            $p->set_var ('rowid', $i);
            $p->set_var ('csscode', ($i%2)+1);
            if ($reassignRec['last_updated'] == NULL OR $reassignRec['last_updated'] == 0) {
                $p->set_var ('task_icon','new_task.gif');
                $p->set_var ('task_started_date',",task not started");
            } else {
                $p->set_var ('task_icon','task.gif');
                $p->set_var ('task_started_date',",started:{$reassignRec['last_updated']}");
            }
            $p->set_var ('reassigned_date', strftime("%Y-%m-%d", $reassignRec['last_updated']));           
            $p->set_var('reassigned_owner', COM_getDisplayName($reassignRec['uid'])); 
                       
            $reclaimlink  = "<a href=\"$actionurl?op=reclaimtask&id={$reassignRec['id']}\" onclick=\"return confirm('Send request to current task owner to reclaim  task?');\">";
            $reclaimlink .= "<img src=\"$imgset/assignback.gif\" border=\"0\" TITLE=\"Reclaim Task\"></a>";            
            $p->set_var ('reclaim',$reclaimlink);                          

            $deletelink  = "<a href=\"$actionurl?op=delreassignedtask&id={$reassignRec['id']}\" onclick=\"return confirm('Delete this re-assignment notice?');\">";
            $deletelink .= "<img src=\"$imgset/delete.gif\" border=\"0\" TITLE=\"Delete Reassignment Notification\"></a>";

             if (is_numeric($projectid)) { 
                $p->set_var ('project_details', sprintf($project_detailsLink,$i,$projectid,$usermodeUID,$reassignRec['task_id']));
             } else {
                $p->set_var ('project_details', '<span style="padding-right:14px;">&nbsp;</span>');
             }            
            
            $p->set_var ('delete',$deletelink);
            $p->set_var ('showdetail','none');
            $p->parse ('view_records', 'records',true);
            $i++;
        }
        $p->set_var ('num_records', $i);         
    }
    $p->parse('javascript_code','javascript');  
    $p->parse ('output', 'report');
        
    return $p->finish ($p->get_var('output'));      
    
    
}


function display_mytasks() {
    global $CONF_NF,$_USER,$_CONF,$_POST,$_TABLES,$actionurl,$formstatus_options,$usermodeUID,$optLinkVars,$errmsg,$LANG_NF00;

    $nfclass= new nexflow();
    $nfclass->_nfUserId = $usermodeUID;
    $nfclass->set_debug(false);
    $nfclass->getQueue();
    $srchFilter=COM_applyFilter($_REQUEST['srchFilter']);
    $srchText=COM_applyFilter($_REQUEST['srchText']);
    $idForAppGroup=COM_applyFilter($_REQUEST['idAppGroup'],true);
    $searchString=COM_applyFilter($_REQUEST['srchText']);
    $srchStatus=COM_applyFilter($_REQUEST['srchStatus']);
    $doSearch=COM_applyFilter($_POST['dosearch']);

    $taskconsolefilter = COM_applyFilter($_POST['taskconsolefilter'],true);
    $taskSort=COM_applyFilter($_REQUEST['tasksort']);
    $sortDirection=COM_applyFilter($_REQUEST['sortorder']);

    if (empty($taskSort)) $taskSort = 'cdate';
    if (empty($sortDirection)) $sortDirection = 'desc';

    //RK included these items here for future filtering abilities
    $pagesize = COM_applyFilter($_REQUEST['$pagesize'],true);
    $filterdate = COM_applyFilter($_REQUEST['filterdate']);
    $page = COM_applyFilter($_REQUEST['$page'],true);
    $imgset = $_CONF['layout_url'] . '/nexflow/images';

    $headingFilterOptions = '&taskuser='.$usermodeUID;
    if ($sortDirection == 'desc') {
        $headingFilterOptions .= '&sortorder=asc';
    } else {
        $headingFilterOptions .= '&sortorder=desc';
    }
    if (!empty($srchFilter)) $headingFilterOptions .= "&srchFilter={$srchFilter}";
    if (!empty($srchText)) $headingFilterOptions .= "&srchText={$srchText}";
    if (!empty($idForAppGroup)) $headingFilterOptions .= "&idAppGroup={$idForAppGroup}";
    if (!empty($srchStatus)) $headingFilterOptions .= "&srchStatus={$srchStatus}";

    $p = new Template($_CONF['path_layout'] . 'nexflow');
    $p->set_file (array (
        'report'             =>     'taskconsole/viewtasks.thtml',
        'records'            =>     'taskconsole/viewtask_record.thtml',
        'javascript'         =>     'taskconsole/javascript/taskconsole.thtml'));

    $p->set_var ('layout_url', $_CONF['layout_url']);
    $p->set_var ('site_url',$_CONF['site_url']);
    $p->set_var ('imgset',$imgset);
    $p->set_var ('actionurl',$actionurl);
    $p->set_var ('taskuser', $usermodeUID);
    $p->set_var ('show_awaystatus','none');

    $heading1 = "<a href=\"{$actionurl}?tasksort=template{$headingFilterOptions}\">Flow Name</a>";
    if ($taskSort == 'template') {
        if ($sortDirection == 'asc') {
            $heading1 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowdown.gif" border="0"></span>';
        } else {
            $heading1 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowup.gif" border="0"></span>';
        }
    }
    $p->set_var('heading1', $heading1);

    $heading2 = "<a href=\"{$actionurl}?tasksort=taskname{$headingFilterOptions}\">Task Name</a>";
    if ($taskSort == 'taskname') {
        if ($sortDirection == 'asc') {
            $heading2 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowdown.gif" border="0"></span>';
        } else {
            $heading2 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowup.gif" border="0"></span>';
        }
    }
    $heading2 .= '<span style="padding-left:5px;font-weight:normal;font-size:9px;">[click on task name to perform]</span>';
    $p->set_var('heading2', $heading2);

    $heading3 = "<a href=\"{$actionurl}?tasksort=cdate{$headingFilterOptions}\">Assigned</a>";
    if ($taskSort == 'cdate') {
        if ($sortDirection == 'asc') {
            $heading3 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowdown.gif" border="0"></span>';
        } else {
            $heading3 .= '<span style="padding-left:10px;"><img src="'.$imgset.'/bararrowup.gif" border="0"></span>';
        }
    }
    $p->set_var('heading3', $heading3);

    $p->set_var('srchText',$LANG_NF00['srchText']);
    $p->set_var('srchFilter',$LANG_NF00['srchFilter']);
    $p->set_var('srchFilterTitle',$LANG_NF00['srchFilterTitle']);
    $p->set_var('srchFilterReqDesc',$LANG_NF00['srchFilterReqDesc']);
    $p->set_var('srchFilterPrjName',$LANG_NF00['srchFilterPrjName']);
    $p->set_var('srchDoSearch',$LANG_NF00['srchDoSearch']);

    //search/filter area setup
    $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup');

    $p->set_var('show_selectappfield','none');
    $p->set_var('show_searchtextfield','');
    switch(strtolower($srchFilter)){
        case 'appgroup':
            $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup',$idForAppGroup);
            $p->set_var('srchselappgroup','selected');
            $p->set_var('show_selectappfield','');
            $p->set_var('show_searchtextfield','none');
        case 'title':
            $p->set_var('srchseltitle','selected');
            break;
        case 'desc':
            $p->set_var('srchselreqdesc','selected');
            break;
    }
    $p->set_var('srchTextValue', $srchText);
    $p->set_var('srchApplicationGroups', $appGroupDDL);

    switch($srchStatus){
        case  1:
            $srchStatus=0;  //since the COM_applyfilter makes everything zero, we need to change this from 1 to 0 in code.
            $p->set_var('srchselactive', 'selected');
            break;
        case 2:
            $p->set_var('srchselonhold', 'selected');
            break;
        case 3:
            $p->set_var('srchselstarted', 'selected');
            break;
        case 4:
            $p->set_var('srchselunstarted', 'selected');
            break;
        case -1:
            $p->set_var('srchselany', 'selected');
            break;

    }
    $sel_sort_options = '';
    foreach ($CONF_NF['sortOptions'] as $value => $label) {
        if ($taskSort == $value) {
            $sel_sort_options .= '<option value="'.$value.'" SELECTED=SELECTED>'.$label.'</option>';
            $p->set_var('selected_tasksort_option',$value);
        } else {
            $sel_sort_options .= '<option value="'.$value.'">'.$label.'</option>';
        }
    }
 
    $p->set_var('sel_sort_options',$sel_sort_options);
    //end of search/filter area
       
    if (trim($errmsg) != '') {
        $p->set_var ('error_message',$errmsg);
    } else {
        $p->set_var ('show_message','none');
    }    
    
    // Test to see if we enable the ability to select taskconsole view for another user
    if (SEC_hasRights('nexflow.admin')) {
        $p->set_var('show_seltaskuser','');
        $p->set_var('sel_user_options', COM_optionList($_TABLES['users'],'uid,username',$usermodeUID));
    } else {
        $p->set_var('show_seltaskuser','none');
        $p->set_var('sel_user_options', '');
    }
   
    if ($_REQUEST['autoclose']) {
        $autoclose = '<script type="text/javascript">' . LB;
        $autoclose .= 'window.onload = function() { ' . LB;
        $autoclose .= '    self.close();' . LB;
        $autoclose .= '    return true;' . LB;
        $autoclose .= '}' . LB;
        $autoclose .= '</script>' . LB;
        $p->set_var('javascript_close_onload',$autoclose);
    }    

    $LANG_CONFIRM = 'Please confirm that you want to delete this process and task records';

    /* Clicking on Task Name triggers action and need to use icon in Actions Column to display project Details */
    $newFormLink = $actionurl . '?op=edit&formid=%s&projectid=%s&taskid=%s'.$optLinkVars;
    $editFormLink = $actionurl . '?op=edit&formid=%s&result=%s&taskid=%s'.$optLinkVars;
    $onClick_action = 'OnClick="ajaxStartTask(%s);"';

    /* @TODO: Commented out for now (Blaine)
    //$holdTaskLink = '<a href="#" onclick="ajaxPutOnHold(%s,%s);"><img src="' . $_CONF['layout_url'] . '/nexflow/images/onhold.png" border=0 alt="%s"></a>';
    */
    // Check if this user has any tasks that were reassigned
    $reassignedTaskCount = DB_count($_TABLES['nfproductionassignments'], 'assignBack_uid', $usermodeUID);
    if ($reassignedTaskCount > 0) {
        $reassignment_message .= '<div style="font-weight:normal;padding-left:20px;">';
        if ($reassignedTaskCount == 1) {
            $reassignment_message .= "You have 1 task that has been re-assigned. Click ";
        } else {
            $reassignment_message .= "You have $reassignedTaskCount tasks that have been re-assigned. Click ";
        }
        $reassignment_message .= '<a href="'.$actionurl.'?op=reassignments'.$optLinkVars.'">here</a> to view them</div>';        
        $p->set_var('reassignment_message',$reassignment_message);
    } else {
        $p->set_var('show_reassignmentmessage','none');
    }

    /* This delete feature is disabled for production use via a config option. It will delete all related records for the project this task is linked to */
    $deleteLink  = '<a href="' . $actionurl . '?op=delete&taskid=%s&project_id=%s'.$optLinkVars.'" onclick="return confirm(\''.$LANG_CONFIRM.'\');">';
    $deleteLink .= '<img src="'.$_CONF['layout_url'].'/nexflow/images/delete.gif" border="0" TITLE="Delete Record"></a>';

    $tasks = $nfclass->get_tasks();
    if ($taskconsolefilter) {
        $p->set_var('lang_hidefilter','hide filter');
        $sortedtasks = nf_getSortedTaskArray($tasks,$srchFilter,$taskSort,$srchText,$idForAppGroup, $srchStatus, $sortDirection);
    } elseif (!empty($taskSort)) {
        $p->set_var('hidefilter','none');
        $p->set_var('lang_hidefilter','show filter');
        $sortedtasks = nf_getSortedTaskArray($tasks,$srchFilter,$taskSort,$srchText,$idForAppGroup, $srchStatus, $sortDirection);
    } else {
        $p->set_var('hidefilter','none');
        $p->set_var('lang_hidefilter','show filter');
        if (is_array($tasks) and count($tasks) > 0) {
            arsort($tasks); // Show latest task first
            $sortedtasks = $tasks;
        } else {
            $sortedtasks = '';
        }
    }

    if (is_array($sortedtasks) and count($sortedtasks) > 0) {
        $i=1;
        $p->set_var ('num_records', count($sortedtasks));

          foreach ($sortedtasks as $taskrec) {

            $p->set_var ('task_action_url','');
            $p->set_var ('task_onclick', '');
            $p->set_var ('edit', '<span style="padding-left:2px;">&nbsp;</span>');
            $p->set_var ('rowid', $i);
            $p->set_var ('csscode', ($i%2)+1);
            $p->set_var ('class_newtask','');

            $startedDate = DB_getItem($_TABLES['nfqueue'], 'startedDate',"id='{$taskrec['id']}'");
            $taskStatus = DB_getItem($_TABLES['nfqueue'], 'status',"id='{$taskrec['id']}'");
            $p->set_var ('on_hold_notice','');
            if($taskStatus == 2) {
                $p->set_var ('task_icon','onhold2.png');
                $p->set_var ('on_hold_notice','<p style="margin-bottom:5px;color:red">This Task is ON HOLD. It cannot be executed until it is put back into active status.</p>');
            } else {
                $p->set_var ('task_icon','task.gif');
            }
            if ($startedDate == NULL OR $startedDate == 0) {
                $p->set_var ('task_icon','new_task.gif');
                $p->set_var ('task_started_date',",task not started");
                $p->set_var ('task_onclick', sprintf($onClick_action,$taskrec['id']));
            } else {
                $p->set_var ('task_started_date',",started:$startedDate");
                $p->set_var ('task_onclick', '');
            }

            $nfclass->_nfProcessId = $taskrec['processid'];
            $project_id = $nfclass->get_ProcessVariable('PID');
            $project_id = NXCOM_filterInt($project_id);
            if($project_id == 0) {
                //lets try to do a simple select in the nfprojects table to ensure no project exists.
                $sql="SELECT id from {$_TABLES['nfprojects']} where wf_process_id='{$taskrec['processid']}'";
                $res=DB_query($sql);
                list($project_id) = DB_fetchArray($res);
                $project_id = NXCOM_filterInt($project_id);
            }
            //at this point, if the project_id is still 0, then we have no project data to show
            //show a general task console line item for execution by the end user.
            $taskStatus = DB_getItem($_TABLES['nfqueue'],'status',"id='{$taskrec['id']}'");
            if (SEC_hasRights('nexflow.admin')) {
                if($taskStatus == 2) {
                    $p->set_var ('hold', sprintf($holdTaskLink,$i,$taskrec['id'],'Re-activate'));
                } else{
                    $p->set_var ('hold', sprintf($holdTaskLink,$i,$taskrec['id'],'Toggle On-Hold'));
                }
            }

            if($project_id > 0)  {

                $p->set_var('hidetaskinfo','');
                $project_detailsLink  = '<a href="#" onClick=\'ajaxViewProjectDetails(%s,%s,%s,%s);\'>';
                $project_detailsLink .= '<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Project Details"></a>&nbsp;';
                // If task is for a project on hold or in a Recycled or Killed status then do not show it
                // There should never be any tasks appearing is status is Killed as that workflow should have been forced to complete state.
                $project_state = DB_getItem($_TABLES['nfprojects'],'status',"id='$project_id'");
                if ($project_state != 6 && $project_state != 7 ) {
                    if ($nfclass->_debug ) {  
                        $logmsg  = "Row:$i -> Project ID:$project_id,Task ID:{$taskrec['id']}. ";
                        $logmsg .= "Processid:{$taskrec['processid']}, Task:{$taskrec['taskname']}, ";
                        $logmsg .= "TaskID: {$taskrec['templateTaskid']}, TaskType: {$taskrec['stepType']}";
                        COM_errorLog($logmsg);
                    }
                    $p->set_var ('task_id',$taskrec['id']);
                    $p->set_var ('project_id', $project_id);
                    $p->set_var ('project_details', sprintf($project_detailsLink,$i,$project_id,$usermodeUID,$taskrec['id']));
    
                    // Determine if this task is for a regenerated workflow and we need to update the main project/request record
                    $parentProcessID = DB_getItem($_TABLES['nfprocess'],'pid',"id='{$taskrec['processid']}'");
                    if ($parentProcessID > 0) {
                        // Now check if this same template task id was executed in the previous process - if so then it is a recycled task
                        // Don't show the re-generated attribute if in this instance of the process we proceed further and are executing new tasks
                        if (DB_count($_TABLES['nfqueue'], array('nf_processID','nf_templateDataId'),array($parentProcessID,$taskrec['templateTaskid'])) > 0) {
                            $taskrec['taskname'] = '<div style="color:red;padding-right:5px;display:inline;">[R]</div>' . $taskrec['taskname'];
                        }
                    }
                      
                    $pquery = DB_query("SELECT wf_process_id  FROM {$_TABLES['nfprojects']} WHERE id='$project_id'");
                    list ($wf_process_id)  = DB_fetchArray($pquery);
                    if ($wf_process_id > 0 AND $wf_process_id == $parentProcessID) {
                        if ($nfclass->_debug ) {                         
                            COM_errorLog("Taskconsole: Updated wf_process_id for project: $project_id from $wf_process_id to {$taskrec['processid']}");
                        }
                        DB_query("UPDATE {$_TABLES['nfprojects']} SET wf_process_id='{$taskrec['processid']}' WHERE id='$project_id'");
                    }
                    
                    $p->set_var ('project_number',$project_id);
                    
                    // Retrieve any Project Comments
                    $comment_count = DB_count($_TABLES['nfproject_comments'],'project_id',$project_id);
                    if ($comment_count > 0 ) {
                        $csql  = "SELECT timestamp, b.username FROM {$_TABLES['nfproject_comments']} a ";
                        $csql .= "LEFT JOIN {$_TABLES['users']} b on a.uid=b.uid WHERE project_id='$project_id' ";
                        $csql .= "ORDER BY timestamp DESC LIMIT 1";
                        list($timestamp,$username) = DB_fetchArray(DB_query($csql));
                        $p->set_var ('comments_note',"($comment_count) <b>Last by:</b>&nbsp;$username, " . strftime('%m/%d/%Y %H:%M',$timestamp));
                    } else {
                        $p->set_var ('comments_note','No Comments');
                    }                    
    
                    // If this this is an interactive tasktype - Check and see if taskhistory record has a "started" timestamp set.
                    if ($taskrec['stepType'] == 1 OR $taskrec['stepType'] == 7 OR $taskrec['stepType'] == 8) {
                        $q1 = DB_query("SELECT project_id,date_started FROM {$_TABLES['nfproject_taskhistory']} WHERE task_id='{$taskrec['id']}'");
                        if (DB_numRows($q1) == 0) { // No task history record yet
                            $p->set_var ('class_newtask','class="nexflowNewTask"');
                            $q2 = DB_query("SELECT UNIX_TIMESTAMP(createdDate) FROM {$_TABLES['nfqueue']} WHERE id='{$taskrec['id']}' ");
                            list ($date_assigned) = DB_fetchArray($q2);
                            DB_query("INSERT INTO {$_TABLES['nfproject_taskhistory']} (project_id,process_id,task_id,assigned_uid,date_assigned)
                                VALUES ('$project_id','{$taskrec['processid']}','{$taskrec['id']}','{$usermodeUID}','$date_assigned') ");
                        } else {
                            list ($xprj_id, $xdate_started) = DB_fetchArray($q1);
                            if ($xprj_id == 0) { // Task history record - but missing project_id
                               $p->set_var ('class_newtask','class="nexflowNewTask"');
                               DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET project_id='$project_id' WHERE task_id='{$taskrec['id']}'");
                            }
                        }
    
                    } else {
                        unset($xdate_started);
                    }
     
                    // Retrieve the project description for this task - used as Project Title
                    $pquery = DB_query("SELECT description,originator_uid FROM {$_TABLES['nfprojects']} WHERE id='$project_id'");
                    list ($description,$originator) = DB_fetchArray($pquery);
                    
                    $submitted_date = DB_getItem($_TABLES['nfprocess'],'initiatedDate', "id={$taskrec['processid']}");                 
                    $submitter_info = COM_getDisplayName($originator) . " / $submitted_date";
                    
                    // Retrieve the flow name dynamic custom functions for appending to the display name to be used for the description
                    $descSQL  ="SELECT b.templateName, a.customFlowName FROM {$_TABLES['nfprocess']} a ";
                    $descSQL .="INNER JOIN {$_TABLES['nftemplate']} b on b.id=a.nf_templateId ";
                    $descSQL .="WHERE a.id={$taskrec['processid']} ";
                    $descRes = DB_query($descSQL);
                    list($templateName,$processCustomName) = DB_fetchArray($descRes);
                    if (trim($description) != '') {
                        $p->set_var ('description', $description);
                    } else {
                        $p->set_var ('description', $templateName);
                    }

                    if($processCustomName != ''){
                        $p->set_var ('project_title',$processCustomName);
                    }  else {
                        $p->set_var ('project_title',$templateName);
                    }

                    $p->set_var ('assigned_date', $taskrec['cdate']);
                    $p->set_var ('submitter_info',$submitter_info);                
    
                    if ($taskrec['stepType'] == 8) {   // This is a nexform autotag handler
                        $form_id = $taskrec['url'];
                        // Check and see if the same form has been submitted for this task yet.
                        $sql  = "SELECT a.id,a.formtype,a.results_id,a.status,a.created_by_taskid, b.nf_templateDataID ";
                        $sql .= "FROM {$_TABLES['nfproject_forms']} a ";
                        $sql .= "LEFT JOIN {$_TABLES['nfqueue']} b on b.id=a.created_by_taskid ";
                        $sql .= "WHERE project_id='$project_id' AND form_id='$form_id' ";
                        $query = DB_query($sql);
                        $newFormRecord = false;
                        if (DB_numRows($query) >= 1 ) {
                            $newFormRecord = true;
                            while (list ($prj_formid,$formtype,$result_id,$state,$created_by_taskid,$form_taskTemplateDataID) = DB_fetchArray($query)) {
                                // Check if this is the same task editing, Rejected form so Task is a new queue ID but same templateDataID or Final Edit Task
                                if ($taskrec['id'] == $created_by_taskid 
                                    || $form_taskTemplateDataID == $taskrec['templateTaskid'] 
                                    || in_array($taskrec['templateTaskid'],$CONF_NF['final_edit_tasks'])) 
                                {
                                    // Check and see if the created_by_taskid has been updated - since it will have the original task id
                                    if ($processPID != 0 AND $created_by_taskid != $taskrec['id']) {
                                        DB_query("UPDATE {$_TABLES['nfproject_forms']} SET created_by_taskid='{$taskrec['id']}' WHERE id='$prj_formid'");
                                    }
    
                                    $p->set_var ('state', $formstatus_options[$state]);
                                    if ($state == 0 or $state == 2 or $state == 3  or $state == 6) {    // Not final distributed version or rejected
                                        // Need to reset the process variable used to check the form approval result
                                        $nfclass->_nfProcessId = $taskrec['processid'];
                                        $nfclass->set_ProcessVariable('Review_Approval',0);

                                        /* Using Click on Task to trigger action method */
                                        if($taskStatus!=2){
                                            $p->set_var ('task_action_url', sprintf($editFormLink,$form_id,$result_id,$taskrec['id']));
                                        } else {
                                            $p->set_var ('task_action_url', "#");
                                        }
                                    }
                                    $sql  = "SELECT timestamp FROM {$_TABLES['nfproject_timestamps']} ";
                                    $sql .= "WHERE project_id=$project_id ORDER BY timestamp DESC LIMIT 1";
                                    $q = DB_query($sql);
                                    list ($timestamp) = DB_fetchArray($q);
                                    if ($timestamp > 0) {
                                        $p->set_var ('date',strftime("%Y-%m-%d", $timestamp));
                                    } else {
                                        $q2 = DB_query("SELECT UNIX_TIMESTAMP(createdDate) FROM {$_TABLES['nfqueue']} WHERE id='{$taskrec['id']}' ");
                                        list ($date_assigned) = DB_fetchArray($q2);
                                        $p->set_var ('date',strftime("%Y-%m-%d", $date_assigned));
                                    }
                                    $newFormRecord = false;
                                }
                            }
                        }
    
                        if (DB_numRows($query) == 0 OR $newFormRecord) {  // No record yet for this form and process - create mode
                            $p->set_var ('state', 'New Task');
                            $p->set_var ('class_newtask','class="nexflowNewTask"');
    
                            /* Using Click on Task to trigger action method */
                            if($taskStatus !=2 ){
                                $p->set_var ('task_action_url', sprintf($newFormLink,$form_id,$project_id,$taskrec['id']));
                            } else {
                                $p->set_var ('task_action_url', "#");
                            }
    
                            $q2 = DB_query("SELECT UNIX_TIMESTAMP(createdDate) FROM {$_TABLES['nfqueue']} WHERE id='{$taskrec['id']}' ");
                            list ($date_assigned) = DB_fetchArray($q2);
                            $p->set_var ('date',strftime("%Y-%m-%d", $date_assigned));    
                        }
    
                        $q = DB_QUERY("SELECT statusmsg FROM {$_TABLES['nfproject_timestamps']} WHERE project_id = '$project_id' ORDER BY timestamp DESC LIMIT 1");
                        list ($statusmsg) = DB_fetchArray($q);
                        $p->set_var ('full_statusmsg', $statusmsg);
                        $msglen = strpos($statusmsg,'.');
                        if ($msglen > 0 AND $pos !== FALSE) {
                            $statusmsg = substr($statusmsg,0,$msglen);
                        }
                        $p->set_var ('statusmsg', $statusmsg);
                        $p->set_var ('id', $project_id);
                        $p->set_var('task_name',$taskrec['taskname']);
                        $p->set_var('view','');
                        $p->set_var('action_record','');
    
    
                    } else {  // Nexflow task - not a form, Check for interactive function or manualweb step type
                        $p->set_var('id',$taskrec['id']);
                        $p->set_var('process_id',$taskrec['processid']);
    
                        /* Task date is in format yyyy-mm-dd hh:mm:ss -- only want to show date portion */
                        $showdate = explode(' ',$taskrec['cdate']);
                        $p->set_var('date',$showdate[0]);
                        $sql = "SELECT timestamp,statusmsg FROM {$_TABLES['nfproject_timestamps']} ";
                        $sql .= "WHERE project_id = '$project_id' ORDER BY timestamp DESC LIMIT 1";
                        $q = DB_query($sql);
                        list ($timestamp,$statusmsg) = DB_fetchArray($q);
                        $p->set_var ('full_statusmsg', $statusmsg);
                        $msglen = strpos($statusmsg,'.');
                        if ($msglen > 0 AND $pos !== FALSE) {
                            $statusmsg = substr($statusmsg,0,$msglen);
                        }
                        $p->set_var ('statusmsg', $statusmsg);
                        
                        /* @TODO: $xdate_started has not be set  */
                        if (isset($xdate_started) AND $xdate_started == 0) {  // Task exists in the taskhistory table but no start_date yet
                            $p->set_var ('state', 'New Task');
                        } else {
                            $p->set_var ('state', 'Started');
                        }
                        $p->set_var('task_name',$taskrec['taskname']);

                        if (strrpos($taskrec['url'],'?') > 0) {
                            $url = "{$_CONF['site_url']}/nexflow/{$taskrec['url']}&processid={$taskrec['processid']}&taskid={$taskrec['id']}";
                        } else {
                            $url = "{$_CONF['site_url']}/nexflow/{$taskrec['url']}?processid={$taskrec['processid']}&taskid={$taskrec['id']}";
                        }
                        $url .= $optLinkVars;
    
                        /* Using Click on Task to trigger action method */
                        if($taskStatus!=2){
                            $p->set_var ('task_action_url', '#');
                            $p->set_var ('task_onclick', "onClick=\"togglerec('action',$i);ajaxStartTask({$taskrec['id']});\" ");
                        } else {
                            $p->set_var ('task_action_url', '#');
                            $p->set_var ('task_onclick', "");
                            
                        }
    
                        $p->set_var ('chk_accept', '');
                        $p->set_var ('chk_reject', '');
                        $p->set_var ('project_id', $project_id);
                        $p->set_var ('project_id', $project_id);
                        $p->set_var ('form_id', $form_id);
                        $p->set_var ('taskuser', $usermodeUID);
    
                        /* Check for any specific tasks that will then over-ride the action url link */
                        if ($taskrec['stepType'] == 7) {        // Interactive Function
                            $function = $taskrec['url'];
                            if (function_exists($function)) {
                                /* Call the interactive function passing
                                *  taskrec, template, rowid and current user if using the user-switch feature
                                */
                                $function($taskrec,$p,$i,$usermodeUID);
                            }
    
                        } elseif ($taskrec['stepType'] == 8)  {//nexform
                            /* @TODO: What should we be doing in this case?  */
                        
                        }  else {
                            /* Using Click on Task to trigger action method */
                            if($taskStatus!=2){
                                $p->set_var ('task_action_url', $url);
                                $p->set_var ('task_onclick', sprintf($onClick_action,$taskrec['id']));
                            } else{
                                $p->set_var ('task_action_url', "#");
                                $p->set_var ('task_onclick', "");
                            }   
                            
                            $p->set_var('action_record','');
                        }
                    }

                    if (!is_numeric($project_id)) {
                        // Disable the icon to show the task/project detail
                        $p->set_var ('project_details', '<span style="padding-right:14px;">&nbsp;</span>');
                        $p->set_var('project_detail','');
                    }

                }
                
            } else {
                //there is no project ID to be found.
                //we'll display the non-project formatted item instead

                $project_detailsLink  = '<a href="#" onClick=\'ajaxViewProjectDetails(%s,%s,%s,%s);\'>';
                $project_detailsLink .= '<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Details"></a>&nbsp;';

                $p->set_var('hidetaskinfo','none');
                $p->set_var ('task_id',$taskrec['id']);
                $p->set_var ('project_id', $project_id);
                $p->set_var ('project_details', sprintf($project_detailsLink,$i,$project_id,$usermodeUID,$taskrec['id']));
                
                $p->set_var('id',$taskrec['id']);
                $p->set_var('process_id',$taskrec['processid']);
                //get the template name here:
                $sql  = "SELECT  c.templateName, d.customFlowName FROM {$_TABLES['nfqueue']} a ";
                $sql .= "inner join {$_TABLES['nftemplatedata']} b on a.nf_templatedataid=b.id ";
                $sql .= "inner join {$_TABLES['nftemplate']} c on b.nf_templateid=c.id ";
                $sql .= "inner join {$_TABLES['nfprocess']} d on a.nf_processid=d.id ";
                $sql .= "WHERE a.id={$taskrec['id']}";
                $res2 = DB_query($sql);
                list($tname, $customDisplay)=DB_fetchArray($res2);

                if($customDisplay!='')  $tname .= $customDisplay;

                $p->set_var ('project_title',$tname );

                /* Task date is in format yyyy-mm-dd hh:mm:ss -- only want to show date portion */
                $showdate = explode(' ',$taskrec['cdate']);
                $p->set_var('date',$showdate[0]);
                $q = DB_QUERY("SELECT timestamp,statusmsg FROM {$_TABLES['nfproject_timestamps']} WHERE project_id=$project_id ORDER BY timestamp DESC LIMIT 1");
                list ($timestamp,$statusmsg) = DB_fetchArray($q);
                $p->set_var ('full_statusmsg', $statusmsg);
                $msglen = strpos($statusmsg,'.');
                if ($msglen > 0 AND $pos !== FALSE) {
                    $statusmsg = substr($statusmsg,0,$msglen);
                }
                $p->set_var ('statusmsg', $statusmsg);
                if (isset($xdate_started) AND $xdate_started == 0) {  // Task exists in the taskhistory table but no start_date yet
                    $p->set_var ('state', 'New Task');
                } else {
                    $p->set_var ('state', 'Started');
                }
                $p->set_var('task_name',$taskrec['taskname']);

                if (strrpos($taskrec['url'],'?') > 0) {
                    $url = "{$_CONF['site_url']}/nexflow/{$taskrec['url']}&processid={$taskrec['processid']}&taskid={$taskrec['id']}";
                } else {
                    $url = "{$_CONF['site_url']}/nexflow/{$taskrec['url']}?processid={$taskrec['processid']}&taskid={$taskrec['id']}";
                }
                $url .= $optLinkVars;

                /* Using Click on Task to trigger action method */
                $p->set_var ('task_action_url', '#');
                if($taskStatus != 2){
                    $p->set_var ('task_onclick', "onClick=\"togglerec('action',$i);ajaxStartTask({$taskrec['id']});\" ");
                } else {
                    $p->set_var ('task_onclick', "");
                }
                $p->set_var ('task_name',$taskrec['taskname']);
                $p->set_var ('chk_accept', '');
                $p->set_var ('chk_reject', '');
                $p->set_var ('project_id', $project_id);
                $p->set_var ('form_id', $form_id);
                $p->set_var ('taskuser', $usermodeUID);

                /* Check for any specific tasks that will then over-ride the action url link */
                if ($taskrec['stepType'] == 7) {        // Interactive Function
                    $function = $taskrec['url'];
                    if (function_exists($function)) {
                        /* Call the interactive function passing
                        *  taskrec, template, rowid and current user if using the user-switch feature
                        */
                        $function($taskrec,$p,$i,$usermodeUID);
                    }
                } elseif ($taskrec['stepType'] == 8) {      // nexform Task
                    $form_id = $taskrec['url'];
                    /* Using Click on Task to trigger action method */
                    if($taskStatus !=2){
                        $p->set_var ('task_action_url', sprintf($newFormLink,$form_id,$project_id,$taskrec['id']));
                    } else {
                        $p->set_var ('task_action_url', "#");
                    }
                } else {
                    /* Using Click on Task to trigger action method */
                    if($taskStatus!=2){
                        $p->set_var ('task_action_url', $url);
                        $p->set_var ('task_onclick', sprintf($onClick_action,$taskrec['id']));
                    } else {
                        $p->set_var ('task_action_url', "#");
                        $p->set_var ('task_onclick', "");
                    }
                    $p->set_var('action_record','');
                }
                
                $p->set_var ('project_details', '<span style="padding-right:14px;">&nbsp;</span>');
                $p->set_var('project_detail','');

            }  //end if - test for valid project_id

            $p->parse ('view_records', 'records',true);
            $i++;


        }//end foreach

    } else {
        $p->set_var ('num_records', 0);
        $p->set_var ('view_records', '<tr><td colspan=5 style="padding-left:20px;">No Tasks</td></tr>' );
    }
    $p->parse('javascript_code','javascript');
    $p->parse ('output', 'report');
    return $p->finish ($p->get_var('output'));

}



/* MAIN Control Section */

switch ($op) {
    case 'edit':
        $prj_id = COM_applyFilter($_GET['projectid'],true);
        $taskid = COM_applyFilter($_GET['taskid'],true);
        if ($prj_id == 0) {
            $prj_formid = DB_getItem($_TABLES['nfproject_forms'],'id',"form_id='$formid' AND results_id='$result' ");
            $prj_id = DB_getItem($_TABLES['nfproject_forms'],'project_id',"id='$prj_formid'");
        } else {
            $prj_formid = COM_applyFilter($_GET['formid'],true);
        }
        $processid = DB_getItem($_TABLES['nfqueue'],'nf_processID', "id='$taskid'");
        $parms = array(
            'usermodeuid'      => $usermodeUID,
            'project_id'       => $prj_id,
            'project_formid'   => $prj_formid,
            'taskid'           => $taskid,
            'processid'        => $processid
        );

        if ($_GET['singleuse'] == 1 OR $nomenu == 1) {
            $parms['singleuse'] = 1;
        } else {
            echo taskconsoleShowNavbar('My Tasks');
        }
        echo nexform_showform($formid,$result,'edit',$parms);
        break;
        
    case 'view':
        if ($_GET['singleuse'] != 1 AND $nomenu != 1) {
            echo taskconsoleShowNavbar('My Tasks');
        }
        echo nexform_showform($formid,$result,'review','',$linkedformsOption);
        break;

    case 'myprojects':
        echo taskconsoleShowNavbar('My Flows');
        if($layout=='' && $cookieLayout!=''){
            $layout=$cookieLayout;
            }
        if($layout=='status'){
            echo display_wfFlowsStatus($usermodeUID,false);
        }else{
            echo display_wfFlowsTabular($usermodeUID,false);
        }
        break;

    case 'allprojects':
        echo taskconsoleShowNavbar('All Flows');
        if($layout=='' && $cookieLayout!=''){
            $layout=$cookieLayout;
            }
        if($layout=='status'){
             echo display_wfFlowsStatus($usermodeUID,true);
        }else{
            echo display_wfFlowsTabular($usermodeUID,true);
        }
        break;

    case 'mytasks':
        echo taskconsoleShowNavbar('My Tasks');
        echo display_mytasks();
        break;
        
    case 'canceltask':
        if($taskid != NULL){
            $nfclass= new nexflow();
            $nfclass->set_debug(false);
               $nfclass->cancel_task($taskid);
        }
        echo taskconsoleShowNavbar('My Tasks');
        echo display_mytasks();
        break;

    case 'Re-Assign':
        if(SEC_hasRights('nexflow.admin')) {
            $variable_id = COM_applyFilter($_POST['variable_id'],true);
            $reassign_uid = COM_applyFilter($_POST['task_reassign_uid']);
            $assignmentRecId = COM_applyFilter($_REQUEST['id'],true);   
            $taskid = COM_applyFilter($_REQUEST['id'],true);   
            $currentlyAssignedUID=nf_getAssignedUID($taskid);
            nf_reassign_task($taskid,$reassign_uid,$fromFromTaskUser,$variable_id);
        }
        echo taskconsoleShowNavbar('My Tasks');
        echo display_mytasks($prj_id);        
        break;

    case 'function':         // Intertactive WorkFlow action - Execute CallBack Function
        $function_handler = COM_applyFilter($_POST['function_handler']);
        $prj_id = COM_applyFilter($_POST['projectid']);
        $taskid = COM_applyFilter($_POST['taskid']);
        $processid = COM_applyFilter($_POST['processid']);
        if (function_exists($function_handler)) {
            $errmsg = $function_handler($processid,$taskid,$usermodeUID,$prj_id);
        }
        echo taskconsoleShowNavbar('My Tasks');
        echo display_mytasks();
        break;

    case 'newRequest':
        $workFlowTemplate = COM_applyFilter($_REQUEST['wflow']);
        $workFlowOffset = COM_applyFilter($_REQUEST['offset']);
        $nfclass= new nexflow();
        $newprocid = $nfclass->newprocess($workFlowTemplate,$workFlowOffset);
        $nfclass->set_processVariable('INITIATOR', $usermodeUID);
        echo COM_refresh($CONF_FE['post_url'] . '/index.php?op=edit&id=58&processid='.$newprocid .'&taskid=0&usermodeuid='.$usermodeUID);
        break;
        
    case 'reassignments':
        echo taskconsoleShowNavbar('My Tasks');
        echo display_reassignedTasks();         
        break;
        
    case 'reclaimtask':
        $id = COM_applyFilter($_REQUEST['id'], true);
        //added assignBack_uid check in sql statement only to ensure authenticated user is requesting task back
        $sql = "SELECT a.task_id, a.uid, a.security_hash, b.fullname, b.email
            FROM {$_TABLES['nfproductionassignments']} a
            LEFT JOIN {$_TABLES['users']} b ON a.uid=b.uid
            WHERE id=$id AND assignBack_uid={$_USER['uid']};";
        $res = DB_query($sql);
        
        //should have 1 row return.  Otherwise, user is either a) not the user
        //that is the assignback_uid or b) user has tampered with the url
        $A = DB_fetchArray($res);
        
        if ($A != FALSE) {
            if ($A['security_hash'] != '') {
                $security_hash = $A['security_hash'];
            }
            else {
                $security_hash = nf_getRandomString(32);
                $sql = "UPDATE {$_TABLES['nfproductionassignments']} SET security_hash='$security_hash' WHERE id=$id;";
                DB_query($sql);
            }

            $keep_url = "{$_CONF['site_url']}/nexflow/index.php?op=keepreassignedtask&id=$id&sec=$security_hash";
            $return_url = "{$_CONF['site_url']}/nexflow/index.php?op=returnreassignedtask&id=$id&sec=$security_hash";
            $sql = "SELECT a.task_id, c.taskname, d.description, d.project_num, d.id
                FROM {$_TABLES['nfproductionassignments']} a
                LEFT JOIN {$_TABLES['nfqueue']} b ON a.task_id=b.id
                LEFT JOIN {$_TABLES['nftemplatedata']} c ON b.nf_templateDataID=c.id
                LEFT JOIN {$_TABLES['nfprojects']} d ON b.nf_processID=d.wf_process_id
                WHERE a.id=$id;";
            
            $res = DB_query($sql);
            $B = DB_fetchArray($res);
            $taskname = $B['taskname'];
            $projname = ($B['project_num'] != '') ? $B['project_num'].' - '.$B['description']:$B['description'];
            
            $msg = "Hello {$A['fullname']},<br><br>{$_USER['fullname']} has requested the task \"$taskname\" from the the project \"$projname\" back.  If you have already started working on this task, you can keep it, or at your approval, {$_USER['fullname']} can have the task back.<br><br><a href=\"$keep_url\">Keep This Task</a>&nbsp;&nbsp;&nbsp;<a href=\"$return_url\">Return To {$_USER['fullname']}</a><br><br>Regards,<br>Administrator";
            if ($CONF_NF['email_notifications_enabled']) {
                COM_mail($A['email'], "Reassign-back Approval", $msg, '', true);
            }
        }
    
        echo taskconsoleShowNavbar('My Tasks');
        echo display_reassignedTasks();         
        break;
        
    case 'keepreassignedtask':
        $security_hash = COM_applyFilter($_GET['sec']);
        $id = COM_applyFilter($_GET['id'], true);
        $compare_hash = DB_getItem($_TABLES['nfproductionassignments'], 'security_hash', "id=$id");

        if ($security_hash != $compare_hash) {
            echo 'The link you followed in your email has expired.  You have either already kept/returned this task, or a newer email has been sent.';
            break;
        }
        
        //security check is embedded in the sql, so user must have the correct security hash to keep task
        $sql = "UPDATE {$_TABLES['nfproductionassignments']} SET assignBack_uid=0, security_hash='' WHERE id=$id AND security_hash='$security_hash';";
        DB_query($sql);

        echo "You have successfully kept the task for yourself.";
        break;
        
    case 'returnreassignedtask':
        $security_hash = COM_applyFilter($_GET['sec']);
        $id = COM_applyFilter($_GET['id'], true);
        $ruid = DB_getItem($_TABLES['nfproductionassignments'], 'assignBack_uid', "id=$id");
        $fullname = DB_getItem($_TABLES['users'], 'fullname', "uid=$ruid");
        $compare_hash = DB_getItem($_TABLES['nfproductionassignments'], 'security_hash', "id=$id");

        if ($security_hash != $compare_hash) {
            echo 'The link you followed in your email has expired.  You have either already kept/returned this task, or a newer email has been sent.';
            break;
        }
        
        //security check is embedded in the sql, so user must have the correct security hash to keep task
        $sql = "UPDATE {$_TABLES['nfproductionassignments']} SET uid=$ruid, assignBack_uid=0, security_hash='' WHERE id=$id AND security_hash='$security_hash';";
        DB_query($sql);

        $sql = "SELECT a.task_id, c.taskname, d.description, d.project_num, d.id
            FROM {$_TABLES['nfproductionassignments']} a
            LEFT JOIN {$_TABLES['nfqueue']} b ON a.task_id=b.id
            LEFT JOIN {$_TABLES['nftemplatedata']} c ON b.nf_templateDataID=c.id
            LEFT JOIN {$_TABLES['nfprojects']} d ON b.nf_processID=d.wf_process_id
            WHERE a.id=$id;";
        
        $res = DB_query($sql);
        $B = DB_fetchArray($res);
        if ($B['id'] != '') {
            $sql = "INSERT INTO {$_TABLES['nfproject_comments']} (project_id, task_id, uid, timestamp, comment) VALUES ({$B['id']}, {$B['task_id']}, $ruid, ".time().", 'Task was returned to original owner, $fullname');";
            DB_query($sql);
        }            
        
        echo "You have successfully returned the task to $fullname";
        break;
        
    case 'delreassignedtask':
        $id = COM_applyFilter($_REQUEST['id'], true);
        //added assignBack_uid check in sql statement only to ensure authenticated user is deleting the reassignment record
        $sql = "UPDATE {$_TABLES['nfproductionassignments']} SET assignBack_uid=0, security_hash='' WHERE id=$id AND assignBack_uid={$_USER['uid']};";
        DB_query($sql);
    
        echo taskconsoleShowNavbar('My Tasks');
        if (DB_count($_TABLES['nfproductionassignments'], 'assignBack_uid', $_USER['uid']) == 0) { 
            echo display_mytasks();         
        }
        else {
            echo display_reassignedTasks();         
        }
        break;

    default:
        echo taskconsoleShowNavbar();
        echo display_mytasks();
        break;
}

if ($_GET['singleuse'] != 1 AND $nomenu != 1) {  
    echo COM_endblock();
}



echo COM_siteFooter();


?>