<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | outstanding.php                                                           |
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

require_once ($_CONF['path_system'] . 'classes/navbar.class.php');
require_once ($_CONF['path_html'] . 'nexflow/libconsole.php');

if (!SEC_inGroup('nexflow Admin')) {
    echo COM_refresh($_CONF['site_url'] .'/nexflow/index.php');
    exit;
}

$optLinkVars = '';

$op = strtolower(COM_applyFilter($_REQUEST['op']));
$taskid = COM_applyFilter($_REQUEST['taskid'],true);
$currentlyAssignedUID=COM_applyFilter($_REQUEST['assignedUID'],true);
$page = COM_applyFilter($_GET['page'],true);
$order = COM_applyFilter($_GET['order'],true);
$prevorder = COM_applyFilter($_GET['prevorder'],true);
$prevdirection = COM_applyFilter($_GET['prevdirection']);
$direction = COM_applyFilter($_GET['direction']);
$assignmentRecId=COM_applyFilter($_REQUEST['assignmentRecord']);
$filterApp=COM_applyFilter($_REQUEST['taskappmode'], true);

// Size of page - number of tasks per page 
$pagesize = COM_applyFilter($_POST['pagesize'],true);
if ($pagesize > 0) {
    $show = $pagesize;
} else {
    $show = 15;
}

// Check if this is the first page.
if ($page == 0) {
    $page = 1;
}

switch ($op) {
    case 'deltask':
        $assignmentRecId = COM_applyFilter($_REQUEST['taskid'],true);
        DB_query("DELETE FROM {$_TABLES['nfproductionassignments']} WHERE task_id='$assignmentRecId'");
        // If there are no more assignment records for this task then remove the workflow queue item
        if (DB_count($_TABLES['nfproductionassignments'],'task_id',$taskid) == 0) {
            DB_query("DELETE FROM {$_TABLES['nfqueue']} WHERE id='$taskid'");
            DB_query("DELETE FROM {$_TABLES['nfproject_taskhistory']} WHERE task_id='$taskid'");
        }
        break;

    case 'reassign':
        $variable_id = COM_applyFilter($_POST['variable_id'],true);
        $reassign_uid = COM_applyFilter($_POST['task_reassign_uid']);
        $assignmentRecId = COM_applyFilter($_REQUEST['id'],true);
        nf_reassign_task($taskid,$reassign_uid,$currentlyAssignedUID,$variable_id);
        break;
        
    case 'assignback':
        nf_revertToOriginalOwner($assignmentRecId);
        break;
        
    case 'notify':
        $variable_id = COM_applyFilter($_POST['variable_id'],true);
        $notifyUID = $currentlyAssignedUID;
        $email = DB_getItem($_TABLES['users'],'email',"uid='$notifyUID'");
        $message = nl2br($_POST['message']) . "\n\n";
        if ($email != '') {
            $sql  = "SELECT a.id, a.nf_templateDataID,  b.taskname FROM {$_TABLES['nfqueue']} a ";
            $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} b on a.nf_templateDataID = b.id ";
            $sql .= "WHERE a.id='$taskid' ";
            $A = DB_fetchArray(DB_query($sql),false);
            $notifyUser = COM_getDisplayName($notifyUID);
            if ($CONF_NF['debug']) {
                COM_errorLog("Nexflow: Send task reminder notificaton for task id: {$A['id']} ({$A['nf_templateDataID']}), {$A['taskname']} to: $notifyUser ");
            }
            $subject = 'Workflow Task Reminder Notification';
            $message .= nf_formatEmailMessage('reminder',$A['nf_templateDataID'],$A['id'],$notifyUser);
            nf_sendEmail ($email,$subject,$message);
        }

        break;
}

echo COM_siteHeader('menu');
echo COM_startBlock("Outstanding Tasks");
echo taskconsoleShowNavbar('Outstanding Tasks');

$p = new Template($_CONF['path_layout'] . 'nexflow/admin');
$p->set_file (array (
    'report'           =>     'view_outstandingtasks.thtml',
    'tasks'            =>     'view_outstandingtask_record.thtml'));

$imgset = $_CONF['layout_url'] . '/nexflow/images';
$actionurl = $_CONF['site_admin_url'] . '/plugins/nexflow/outstanding.php';
$p->set_var ('layout_url', $_CONF['layout_url']);
$p->set_var ('site_url',$_CONF['site_url']);
$p->set_var ('imgset',$imgset);
$p->set_var ('actionurl',$actionurl);
$p->set_var ('heading1', 'Assigned');
$p->set_var ('heading2', 'Owner');
$p->set_var ('heading3','Request');
$p->set_var ('heading4', 'Task Description');
$p->set_var ('heading5','Actions');
$p->set_var ('user_options',nf_listUsers());
$p->set_var ('public_url', $_CONF['site_admin_url'] . '/plugins/nexflow');


$options = array(15,25,50,75,150);
foreach ($options as $size) {
    if ($pagesize == $size) {
        $pagesize_options .= '<option value="'.$size.'" SELECTED>'.$size.'</option>';
    } else {
        $pagesize_options .= '<option value="'.$size.'">'.$size.'</option>';
    }
}
$p->set_var('pagesize_options',$pagesize_options);

$sql  = "SELECT distinct a.id, a.nf_processID, a.nf_templateDataID, b.nf_templateID, b.taskname, a.createdDate, ";
$sql .= "b.assignedByVariable, e.id as assignment_rec, e.uid as assigned_uid, e.assignBack_uid, e.nf_processVariable, ";
$sql .= "f.fullname, a.status, g.AppGroup ";
$sql .= "FROM {$_TABLES['nfqueue']} a ";
$sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} b on a.nf_templateDataID = b.id ";
$sql .= "LEFT JOIN {$_TABLES['nfprocess']} c on a.nf_processID = c.id ";
$sql .= "LEFT JOIN {$_TABLES['nfsteptype']} d on d.id=b.nf_stepType ";
$sql .= "INNER JOIN {$_TABLES['nfproductionassignments']} e ON e.task_id = a.id ";   
$sql .= "LEFT JOIN {$_TABLES['users']} f ON f.uid=e.uid ";
$sql .= "LEFT JOIN {$_TABLES['nftemplate']} g ON g.id=b.nf_templateID ";
$sql .= "WHERE  (c.complete = 0  or c.complete=3) AND (a.status = 0 or a.status=2) ";
$sql .= "AND d.is_interactiveStepType=1 AND (a.archived <> 1 OR a.archived IS NULL OR a.archived=0) ";
if ($filterApp != 0) {
    $sql .= "AND g.AppGroup=$filterApp ";
}
if ($prevorder != $order) {
    $direction = 'desc';
} elseif ($direction == '') {
    $direction = ($prevdirection == 'asc') ? 'desc' : 'asc';
}

switch($order) {
    case 1:
        $orderby = 'a.id';
        break;
    case 2:
        $orderby = 'f.fullname';
        break;
    case 3:
        $orderby = 'a.nf_processID';
        break;
    case 4:
        $orderby = 'b.taskname';
        break;
    default:
        $orderby = 'id';
        $order = 1;
        break;
}

if ($prevorder == 0) $prevorder=1;
if ($direction == "asc") {
    $prevdirection = 'asc';
    $direction = 'desc';
} else {
    $prevdirection = 'desc';
    $direction = 'asc';
}
    

$taskcount = DB_numRows(DB_query($sql));
    
$numpages = ceil($taskcount / $show);
$offset = ($page - 1) * $show;
$base_url = "{$_CONF['site_url']}/scott/outstanding.php?order={$order}&prevorder={$prevorder}&direction={$prevdirection}";

$p->set_var('prevorder',$order);
$p->set_var('prevdirection',$prevdirection);
$p->set_var('page_navigation',COM_printPageNavigation($base_url,$page, $numpages));
$p->set_var('taskcount',$taskcount);
$p->set_var('title1',"Click to sort by Assigned $prevdirection");
$p->set_var('title2',"Click to sort by Owner $prevdirection");
$p->set_var('title3',"Click to sort by Project $prevdirection");
$p->set_var('title4',"Click to sort by Taskname $prevdirection");

$sql .= "ORDER BY $orderby $direction LIMIT $offset,$show ";
$query = DB_query($sql);

$userlinkURL = $_CONF['site_url'] .'/nexflow/index.php?op=mytasks&taskuser=%s';
if (isset($filterApp)) {
    $userlinkURL .= "&taskappmode=$filterApp";
}
$userlink  = '<a href="%s" TITLE="Task assigned to site member: %s, UID:%s">%s</a>';

$sel_filter_applications = '';
$result = DB_query("SELECT id, AppGroup FROM {$_TABLES['nfappgroups']} ORDER BY AppGroup ASC;");
while (list ($appid, $appgroup) = DB_fetchArray($result)) {
    if ($appid == $filterApp) {
        $sel_filter_applications .= "<option value=\"$appid\" selected=\"selected\">$appgroup</option>";
    }
    else {
        $sel_filter_applications .= "<option value=\"$appid\">$appgroup</option>";
    }
}
$p->set_var('sel_filter_applications', $sel_filter_applications);

$traceScriptUrl = $_CONF['site_admin_url'] .'/plugins/nexflow/wftrace.php?task=%s&process=%s&singletask=1';
$tracelink = '<a href="#"><img src="'.$imgset.'/trace.gif" border="0" TITLE="Launch WorkFlow Trace" ';
$tracelink .= 'onClick="nfNewWindow(\'%s\');return false;"></a>';

$reassignlink = '<a href="#"><img src="'.$imgset.'/reassign.gif" border="0" TITLE="Re-Assign Task" ';
$reassignlink .= 'onClick="toggle_taskrec(\'reassign\',%s);return false;"></a>';
$assignBackLink = '<a href="#"><img src="'.$imgset.'/assignback.gif" border="0" TITLE="Assign Task to Original Owner" ';
$assignBackLink .= 'onClick="toggle_taskrec(\'assignBack\',%s);return false;"></a>'; 
$assignBackLinkOff = '<img src="'.$imgset.'/assignback-off.gif" border="0" TITLE="Assign Task to Original Owner">';  

$holdLink = '<a href="#"><img src="'.$imgset.'/%s" border="0" TITLE="%s" ';
$holdLink .= 'onClick="putTaskOnHOld(%s);return false;" id="onholdimg"></a>'; 

$notifylink = '<a href="#"><img src="'.$imgset.'/send_notification.gif" border="0" TITLE="Send Task Reminder" ';
$notifylink .= 'onClick="toggle_taskrec(\'notify\',%s);return false;"></a>';

$deletelink  = '<a href="'.$actionurl.'?op=deltask&taskid=%s&id=%s" onclick="return confirm(\'Delete this task?\');">';
$deletelink .= '<img src="'.$imgset.'/delete.gif" border="0" TITLE="Delete Task"></a>';

$i=1;

while ($A = DB_fetchArray($query,false)) {
    $nfclass= new nexflow();
    $nfclass->_nfProcessId = $A['nf_processID'];
    $project_id = $nfclass->get_ProcessVariable('PID');
    
    $project_status = DB_getItem($_TABLES['nfprojects'],'status', "id='$project_id'");
    
    $p->set_var ('id',$i);
    $p->set_var ('csscode', ($i%2)+1);    
    $p->set_var ('taskid',$A['id']);
    $p->set_var ('processid',$A['nf_processID']);
    $p->set_var ('assignment_rec',$A['assignment_rec']);
    $p->set_var ('taskname',$A['taskname']); 

    $taskdate = explode(' ',$A['createdDate']);
    $p->set_var ('date',$taskdate[0]);
    $templateName = DB_getItem($_TABLES['nftemplate'],'templateName',"id='{$A['nf_templateID']}'");
    $project_name = DB_getItem($_TABLES['nfprojects'],'description', "wf_process_id='{$A['nf_processID']}'");
    $p->set_var ('assigned_UID',$A['assigned_uid']);
    
    if ($project_name == '') {
        $project_name = DB_getItem($_TABLES['nfprojects'],'description', "id='$project_id'");
        if ($project_name == '') {
            $project_name = $templateName;
        }        
    }
    
    $p->set_var ('project',$project_name);
    if ($A['assigned_uid'] > 0) {
        $loginname = DB_getItem($_TABLES['users'],'username',"uid={$A['assigned_uid']}");
        $taskconsoleLinkURL = sprintf($userlinkURL,$A['assigned_uid']);
        $p->set_var ('assigned',sprintf($userlink,$taskconsoleLinkURL,$loginname,$A['assigned_uid'],$A['fullname']));
    } else {
        $p->set_var ('assigned','Un-Assigned');
    }

    if ($A['assignedByVariable'] == 1) {
        $p->set_var ('variable_id',$A['nf_processVariable']);
        $p->set_var ('taskassign_mode', 'variable');

    } else {
        $p->set_var ('variable_id','');
        $p->set_var ('taskassign_mode', 'user');
    }
    
    // Check if this task's process has been Regenerated
    $parentTaskID = DB_getItem($_TABLES['nfprocess'],'pid',"id={$A['nf_processID']}");
    if ($parentTaskID > 0) {
        // Now check if this same template task id was executed in the previous process - if so then it is a recycled task
        // Don't show the re-generated attribute if in this instance of the process we proceed further and are executing new tasks
        if (DB_count($_TABLES['nfqueue'], array('nf_processID','nf_templateDataId'),array($parentTaskID,$A['nf_templateDataID'])) > 0) {
            $A['taskname'] = '<div style="color:red;padding-right:5px;display:inline;">[R]</div>' . $A['taskname'];
        }
    }
    
    $infolink  = "<a class=\"info\" href=\"#\">";
    $infolink .= "<img src=\"$imgset/info.gif\" border=\"0\"><span>";
    $infolink .= "<b>Process:&nbsp;</b>{$A['nf_processID']}";
    $infolink .= "<br><b>Queue ID:&nbsp;</b>{$A['id']}";
    $infolink .= "<br><b>Assigned:&nbsp;</b>{$A['createdDate']}";
    $infolink .= "<br><b>Template:&nbsp;</b>$templateName";
    $infolink .= "<br><b>Task:&nbsp;</b>{$A['nf_templateDataID']}";

    if ($A['assignedByVariable'] == 1) {
        $variable = DB_getItem($_TABLES['nftemplatevariables'],'variableName',"id='$variableID'");
        $infolink .= '<br><b>Assignment by Variable:&nbsp;</b>'.$variable;
    }
    $infolink .= '</span></a>';
    $p->set_var ('info',$infolink);
    
    $traceLinkURL = sprintf($traceScriptUrl,$A['id'],$A['nf_processID']);
    $p->set_var ('trace',sprintf($tracelink,$traceLinkURL));
    $p->set_var ('re_assign',sprintf($reassignlink,$i));
    if($A['assignBack_uid'] > 0) {
        $p->set_var('original_owner', COM_getDisplayName($A['assignBack_uid']));
        $p->set_var ('assign_back',sprintf($assignBackLink,$i));        
    } else {
        $p->set_var('original_owner','');
        $p->set_var ('assign_back',$assignBackLinkOff);        
    }
    $p->set_var ('notify',sprintf($notifylink,$i));
    if (SEC_hasRights('nexflow.edit')) {
        $test = sprintf($deletelink,$A['id'],$A['assignment_rec']);
        $p->set_var ('delete',sprintf($deletelink,$A['id'],$A['assignment_rec']));         
    } else {
        $deletelink = '';
    }
    if (SEC_hasRights('nexflow.edit')) {
        if($A['status']==2){
            $title='ON HOLD';
            $img='onhold2.png';
        }else{
            $title='NOT ON HOLD';
            $img='onhold.png';
        }
        $p->set_var ('hold',sprintf($holdLink,$img,$title,$A['id']));         
    } else {
        $holdLink = '';
    }
    
    
    $p->parse ('outstanding_tasks', 'tasks',true);
    $i++;

}
$p->set_var ('num_records',$i - 1);
$p->parse('javascript_code','javascript');
$p->parse ('output', 'report');
echo $p->finish ($p->get_var('output'));

echo COM_endBlock();
echo COM_siteFooter();


?>