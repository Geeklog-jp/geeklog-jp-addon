<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | projectdetails.php                                                        |
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

// this file can't be used on its own
if (strpos ($_SERVER['PHP_SELF'], 'gf_format.php') !== false)
{
    die ('This file can not be used on its own.');
}

require_once('libprocessdetails.php');

//RK - added this fetch piece to ensure that the $usermodeUID variable
//is in scope during this include.  I have found that without this include piece, the
//{taskuser} replacement with $usermodeUID does NOT function.  the usermodeUID
//although set in the preceeding scripts does NOT carry thru on PHP5.X
$selectUser = COM_applyFilter($_REQUEST['taskuser'],true);
if ($selectUser > 0 AND SEC_hasRights('nexflow.admin')) {
    $usermodeUID = $selectUser;
    if (SEC_hasRights('nexflow.admin')) {
        $optLinkVars = "&taskuser=$usermodeUID";
    }
} else {
    $usermodeUID = ($_USER['uid'] > 1) ? $_USER['uid'] : 1;
}
//end of inclusion

$p = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
$p->set_file (array (
    'projectdetail'      =>     'project_detail_record.thtml',
    'outstandingtasks'   =>     'project_detail_outstandingtask_record.thtml',
    'taskhistory'        =>     'project_detail_taskhistory_record.thtml',
    'eventlogs'          =>     'project_detail_eventlog_record.thtml',
    'projectforms'       =>     'project_detail_linkedforms_record.thtml',
    'projectcomments'    =>     'project_detail_comment_record.thtml',
    'addcomment'         =>     'comment_addaction.thtml',
    'delcomment'         =>     'comment_delaction.thtml',
    'javascript'         =>     'javascript/taskconsole.thtml'));

$p->set_var ('layout_url', $_CONF['layout_url']);
$p->set_var ('site_url',$_CONF['site_url']);
$p->set_var ('actionurl',$actionurl);
$p->set_var ('rowid',$rowid);
$p->set_var ('project_id', $project_id);
$p->set_var ('taskuser', $_USER['uid']);

if ($fromprojectlink) {
    $p->set_var('hiderequestlink','none');
} else {
    $p->set_var ('project_link','#" onClick="nfNewWindow(\''.$_CONF['site_url'] .'/nexflow/getproject.php?id=' . $project_id . '\')"');
}

if ($op == 'addcomment') {
    if (!get_magic_quotes_gpc()) {
        $comment = addslashes($_GET['comment']);
    } else {
        $comment = $_GET['comment'];
    }
    $comment =  ppPrepareForDB($comment);
    $sql  = "INSERT INTO {$_TABLES['nfproject_comments']} (project_id, uid, timestamp, comment) ";
    $sql .= "VALUES ('$project_id','{$usermodeUID}',UNIX_TIMESTAMP(),'$comment')";
    if ($CONF_NF['debug']) {
        COM_errorLog($sql);
    }
    DB_query($sql);
} elseif ($op == 'delcomment' and $cid > 0) {
    $sql  = "DELETE FROM {$_TABLES['nfproject_comments']} WHERE id='$cid'";
    DB_query($sql);
}

$sql = "SELECT * FROM {$_TABLES['nfprojects']} WHERE id='$project_id'";
$query = DB_QUERY($sql);
$PD = DB_fetchArray($query);
$p->set_var ('description', $PD['description']);

// Knowing the project id - retrieve the request form results
$result_id = DB_getItem($_TABLES['nfproject_forms'],'results_id',"project_id='$project_id'");
$p->set_var ('submitter_name',$PD['cust3']);
$p->set_var ('project_number',$project_id);
$p->set_var('project_status', $CONF_NF['NFProjectStatus'][$PD['status']]);

if ($PD['status'] == 6) {   // Project in Recycle State
    $onclick_action = 'onClick="return confirm(\'Are you sure you want to Re-Initiate this Project?\');"';
    $reclaim_html = '<form action="'.$_CONF['site_url'] .'/nexflow/reclnfproject.php" method="post" style="display:inline;margin:0px;">' .LB;
    $reclaim_html .= '<input type="hidden" name="projectid" value="'.$project_id.'">' . LB;
    $reclaim_html .= '<input type="hidden" name="taskuser" value="'.$usermodeUID.'">' . LB;
    $reclaim_html .= '<input type="submit" value="Re-Initiate" '.$onclick_action.'></form>';
    $p->set_var ('special_status_action' , $reclaim_html);
} elseif ($PD['status'] == 7 AND SEC_inGroup('nexflow Admin'))  {    // Project in On-Hold State
    $onclick_action = 'onClick="return confirm(\'Are you sure you want to Restart this Project?\');"';
    $reclaim_html = '<form action="'.$_CONF['site_url'] .'/nexflow/reclnfproject.php" method="post" style="display:inline;margin:0px;">' .LB;
    $reclaim_html .= '<input type="hidden" name="projectid" value="'.$project_id.'">' . LB;
    $reclaim_html .= '<input type="hidden" name="taskuser" value="'.$usermodeUID.'">' . LB;
    $reclaim_html .= '<input type="submit" value="Re-Initiate" '.$onclick_action.'></form>';
    $p->set_var ('special_status_action' , $reclaim_html);
} else {
    $p->set_var ('special_status_action','');
}

if ($source != 'mytasks' AND SEC_hasRights('nexflow.admin')) {
    $deleteProjectLink = '<a href="#" onClick="ajaxUpdateDeleteProject('.$project_id.',' .$rowid.');return false;">Delete Project</a>';
    $p->set_var('delete_project_action', $deleteProjectLink);
} else {
    $p->set_var('show_editgatedates','none');
    $p->set_var('delete_project_action','');
}

// Determine if this process' template has an application Flow group associated with it
// if so, run any custom function for display here
$sql = "SELECT c.AppGroup from {$_TABLES['nftemplate']} a  ";
$sql .= "INNER JOIN {$_TABLES['nfprocess']} b on a.id=b.nf_templateID ";
$sql .= "INNER JOIN {$_TABLES['nfappgroups']} c on a.AppGroup=c.id";
$sql .= " where b.id={$PD['wf_process_id']}";
$rs = DB_query($sql);

list($appGroup)=DB_fetchArray($rs);
$appGroup='nf_AppGroupDisplay_' . str_replace(' ','',$appGroup);

if(function_exists($appGroup)){
   $appGroupCustomSummary=$appGroup($A['nf_processID']);
}
$p->set_var ('customWorkflowSummary',$appGroupCustomSummary);

// Update the page to include the Outstanding Tasks for this project
processDetailGetOutstandingTasks($project_id,$p);

// Update the page to include the Task History
processDetailGetTasksHistory($project_id,$p);

// Retrieve any Project forms
$sql  = "SELECT id,form_id,formtype,results_id, status, created_by_uid, is_locked_by_uid FROM {$_TABLES['nfproject_forms']} WHERE project_id='$project_id'";
$query = DB_query($sql);
if (DB_numRows($query) == 0) {
    $p->set_var ('form_records','No Forms');
} else {
    $viewFormURL  = $actionurl . '?op=view&formid=%s&result=%s&projectid=%s';
    $editFormURL  = $actionurl . '?op=edit&formid=%s&result=%s&taskuser=%s';
    $f = 1;
    while($PD = DB_fetchArray($query)) {

        // Get project create and edit information
        $sql = "SELECT date,last_updated_date,uid,last_updated_uid FROM {$_TABLES['formResults']} WHERE id={$PD['results_id']}";
        list ($createdDate,$lastUpdatedDate,$createdUid,$lastUpdatedUid) = DB_fetchArray(DB_query($sql));

        $createdDate = strftime("%Y-%m-%d %H:%M", $createdDate);
        $createdUser = COM_getDisplayName($createdUid);
        $form_date = "<b>[C]</b> $createdDate";
        $form_details = '';
        if (strpos($A['formtype'],'RFI') !== false) {
            $sql = "SELECT b.field_data FROM {$_TABLES['formFields']} a LEFT JOIN {$_TABLES['formResData']} b ON a.id=b.field_id ";
            $sql .= "WHERE label LIKE 'TITLE' AND b.result_id={$A['results_id']};";
            list ($rfi_title) = DB_fetchArray(DB_query($sql));
            $form_details = "RFI Title: $rfi_title<br>";
        }
        $form_details .= "<b>Created:</b> $createdDate<br><b>&nbsp;&nbsp;by:</b> $createdUser";
        if ($lastUpdatedDate != 0) {
            $lastUpdatedDate = strftime("%Y-%m-%d %H:%M", $lastUpdatedDate);
            $lastUpdatedUser = COM_getDisplayName($lastUpdatedUid);
            $form_date = "<b>[U]</b> $lastUpdatedDate";
            $form_details .= "<br><b>Updated:</b> $lastUpdatedDate<br><b>&nbsp;&nbsp;by:</b> $lastUpdatedUser";
        }
        $p->set_var ('form_details',$form_details);

        // Get last timestamp event for this form
        $q = DB_query("SELECT timestamp FROM {$_TABLES['nfproject_timestamps']} WHERE project_formid='{$PD['id']}' ORDER BY timestamp DESC limit 1");
        list ($timestamp) = DB_fetchArray($q);
        $p->set_var ('form_date',strftime("%m-%d-%Y %H:%M:%S", $timestamp));
        $p->set_var ('form_status', $CONF_NF['formstatus'][$PD['status']]);

        $p->set_var ('form_name',$PD['formtype']);
        $p->set_var ('form_url', '#" onClick="nfNewWindow(\''.sprintf($viewFormURL,$PD['form_id'],$PD['results_id'],$project_id).'\');"');

        if ($PD['created_by_uid'] == $_USER['uid'] OR SEC_inGroup('nexflow Admin')) {
            $edit_link = '<a href="#" onClick="nfNewWindow(\''.sprintf($editFormURL,$PD['form_id'],$PD['results_id'],$usermodeUID).'\');">';
            $edit_link .= '<img src="'.$_CONF['layout_url']. '/nexflow/images/edit.gif" Title="Edit Form" border="0"></a>';
        } else {
            $edit_link = '';
        }
        $p->set_var ('edit_link',$edit_link);
        if ($f == 1) {
            $p->parse('form_records','projectforms');
        } else {
            $p->parse('form_records','projectforms',true);
        }
        $f++;
    } // while
}

// Retrieve any Project Comments
$sql  = "SELECT a.id,d.taskname,a.timestamp,a.comment, b.username FROM {$_TABLES['nfproject_comments']} a ";
$sql .= "LEFT JOIN {$_TABLES['users']} b on a.uid=b.uid  ";
$sql .= "LEFT JOIN {$_TABLES['nfqueue']} c on c.id = a.task_id ";
$sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} d on d.id = c.nf_templateDataID ";
$sql .= "WHERE project_id='$project_id' ORDER BY timestamp ASC";
$query = DB_query($sql);
if (DB_numRows($query) == 0) {
     $p->set_var ('comment_records',"<div style=\"padding:5px 0px 5px 15px;\"><a href=\"#\" onClick=\"doComment('add',$rowid);\">New Comment</a></div>");
} else {
    $c=1;
    while($PD = DB_fetchArray($query)) {
        if ($PD['taskname'] != '') {
            $p->set_var ('comment_taskname',"<b>Task:</b>&nbsp;{$PD['taskname']}");
        } else {
            $p->set_var ('comment_taskname','');
        }
        $p->set_var ('comment_username', nl2br($PD['username']));
        $p->set_var ('comment_date',strftime("%a %b %d %Y %H:%M", $PD['timestamp']));
        $p->set_var ('comment', nl2br($PD['comment']));
        $p->set_var ('cid',$PD['id']);
        if ($c == 1) {
            $p->parse ('comment_add_link','addcomment');
            $p->parse ('comment_del_link','delcomment');
            $p->parse('comment_records','projectcomments');
        } else {
            $p->set_var ('comment_add_link','');
            $p->parse ('comment_del_link','delcomment');
            $p->parse('comment_records','projectcomments',true);
        }
        $c++;
    } // while

}

$p->parse('output','projectdetail');
$projectdetails = $p->finish ($p->get_var('output'));

?>