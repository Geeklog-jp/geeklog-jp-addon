<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | base_interactive_functions.php                                            |
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

/* +-----------------------------------------------------------------------------+
// |                   Interactive Function Defintions                           |
// +-----------------------------------------------------------------------------*/


// Common interactive Task that will create an inline action (form)
// Show the message defined in the tasks optionalParm value
// Inline Tasks that are forms post back to the workflow engine
// A matching handler function needs to be defined which is then called on task completion.
function nf_alertUserMessage($taskrec,&$template,$rowid,$userid) {

    $processid = $taskrec['processid'];
    $nfclass = new nexflow($processid);
    $nfclass->set_currentTaskid($taskrec['id']);
    $alertMsg = $nfclass->get_taskOptionalParm();

    // Define the template to use for this inline task
    $template->set_file ("action{$rowid}",'application/alertuser.thtml');

    // Define the Post-Form Handler function to use when task completes
    $template->set_var ('function_handler','nf_alertUserMessage_posthandler');
    $template->set_var ('rowid', $rowid);
    $template->set_var ('message',$alertMsg);
    $template->parse('action_record',"action{$rowid}");

}

// Post Form Handler - that is called when the matching task is completed
function nf_alertUserMessage_posthandler($processid,$taskid,$userid,$projectid) {

    if ($processid > 0 AND $taskid > 0) {
        $nfclass = new nexflow($processid,$userid);
        $nfclass->complete_task($taskid);
    }
}


/* Basic Review Form action - task completes when action is closed */
function nf_reviewForm ($taskrec,&$template,$rowid,$userid) {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $actionurl = $_CONF['site_url'] .'/nexflow/index.php';
    if (SEC_inGroup('nexFlow_Admin')) {
        $optLinkVars = "&taskuser=$userid";
    } else {
        $optLinkVars = '';
    }

    $reviewFormLink = $actionurl . '?op=view&nomenu=1&&formid=%s&result=%s&projectid=%s&singleuse=1'.$optLinkVars;

    $processid = $taskrec['processid'];
    $nfclass= new nexflow($processid);
    $project_id = $nfclass->get_processVariable('PID');

    $projectFormResults = nf_getFormResult($taskrec['templateTaskid'],$project_id);
    $form_id = $projectFormResults['formid'];
    $result_id = $projectFormResults['resultid'];

    $template->set_file ("action{$rowid}",'application/basic_formreview_action.thtml');
    $template->set_var ('function_handler','nf_reviewForm_posthandler');
    $template->set_var ('rowid', $rowid);
    if ($result_id > 0) {
        $template->set_var ('review_link','href="#" onClick="nfNewWindow(\''.sprintf($reviewFormLink,$form_id,$result_id,$project_id).'\');"');
    } else {
        $template->set_var ('review_link', 'href="#" onClick="alert(\'No Result on file found\');"');
    }
    $template->parse('action_record',"action{$rowid}");

}


/* Basic Review Form action - task completes when action is closed */
function nf_reviewForm_posthandler($processid,$taskid,$userid,$projectid) {

    if ($processid > 0 AND $taskid > 0) {
        $nfclass= new nexflow($processid,$userid);
        $nfclass->complete_task($taskid);
    }
}



/* Basic Review and Approval Form action */
function nf_approveForm ($taskrec,&$template,$rowid,$userid) {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $actionurl = $_CONF['site_url'] .'/nexflow/index.php';
    if (SEC_inGroup('nexFlow_Admin')) {
        $optLinkVars = "&taskuser=$userid";
    } else {
        $optLinkVars = '';
    }

    $reviewFormLink = $actionurl . '?op=view&nomenu=1&&formid=%s&result=%s&projectid=%s&singleuse=1'.$optLinkVars;

    $processid = $taskrec['processid'];
    $nfclass= new nexflow($processid);
    $project_id = $nfclass->get_processVariable('PID');

    // Check and see if process defined Result ID and Form ID are defined
    $rid = $nfclass->get_processVariable('RID');
    $fid = $nfclass->get_processVariable('FID');

    if (empty($rid) or empty($fid)) {   // If not defined look for them via the form(s) defined in the Task optional argument
        $projectFormResults = nf_getFormResult($taskrec['templateTaskid'],$project_id);
        $form_id = $projectFormResults['formid'];
        $prj_formid = $projectFormResults['prjformid'];
        $result_id = $projectFormResults['resultid'];
    } elseif ($fid > 0) {
        $prj_formid = DB_getItem($_TABLES['nfproject_forms'],'id',"project_id=$project_id AND form_id=$fid");
        $form_id = $fid;
        $result_id = $rid;
    }

    $template->set_file ("action{$rowid}",'application/basic_formapprove_action.thtml');
    $template->set_var ('function_handler','nf_approveForm_posthandler');
    if ($result_id > 0) {
        $template->set_var ('review_link','href="#" onClick="nfNewWindow(\''.sprintf($reviewFormLink,$form_id,$result_id,$project_id).'\');"');
    } else {
        $template->set_var ('review_link', 'href="#" onClick="alert(\'No Result on file found\');"');
    }
    $template->set_var ('form_id',$form_id);
    $template->set_var ('process_id',$processid);
    $template->set_var ('id',$taskrec['id']);
    $template->set_var ('rowid', $rowid);

    if (isset($prj_formid) and $prj_formid > 0)  {
        /* Retrieve the status if user has actioned this form */
        $sql  = "SELECT status FROM {$_TABLES['nfproject_approvals']} ";
        $sql .= "WHERE uid='{$userid}' AND form_id='$prj_formid' AND process_id='{$taskrec['processid']}'";
        $query = DB_query($sql);
        list ($status) = DB_fetchArray($query);

        // Determine if this task has recorded a comment yet
        $notes = DB_getItem($_TABLES['nfproject_comments'], 'comment', "project_id='$project_id' AND task_id='{$taskrec['id']}'");
        $template->set_var ('notes',$notes);
        if ($status == 3) {
            $template->set_var ('chk_accept', 'CHECKED');
        } elseif ($status == 6) {
            $template->set_var ('chk_reject', 'CHECKED');
        }
    }
    $template->parse('action_record',"action{$rowid}");

}


/* Basic Review Form action - task completes when action is closed */
function nf_approveForm_posthandler($processid,$taskid,$userid,$projectid) {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $nfclass= new nexflow($processid);
    if ($projectid == '' OR $projectid == 0) {
        $projectid = $nfclass->get_processVariable('PID');
    }

    $actionopt = COM_applyFilter($_POST['actionopt']);
    $taskid = COM_applyFilter($_POST['taskid']);
    $formid = COM_applyFilter($_POST['formid']);
    $processid = COM_applyFilter($_POST['processid']);
    if ($projectid > 0) {
        $prj_formid = DB_getItem($_TABLES['nfproject_forms'],'id', "project_id='$projectid' AND form_id='$formid'");
    }

    $status = DB_getItem($_TABLES['nfproject_forms'],'status', "id='$prj_formid'");

    if (DB_count($_TABLES['nfproject_approvals'],array('uid','form_id','process_id'), array($userid,$prj_formid,$processid)) == 0) {
        DB_query("INSERT INTO {$_TABLES['nfproject_approvals']} (process_id,form_id,uid) VALUES ('$processid','$prj_formid','{$userid}')");
    }
    if ($actionopt == 'accept') {
        DB_query("UPDATE {$_TABLES['nfproject_approvals']} SET status='3', date_updated=UNIX_TIMESTAMP() WHERE uid='{$userid}' AND form_id='$prj_formid'");
    } elseif ($actionopt == 'reject') {
        DB_query("UPDATE {$_TABLES['nfproject_approvals']} SET status='6', date_updated=UNIX_TIMESTAMP() WHERE uid='{$userid}' AND form_id='$prj_formid'");
    }

    if (trim($_POST['notes']) != '' ) {
        $notes =  ppPrepareForDB($_POST['notes']);
        if (DB_count($_TABLES['nfproject_comments'], array('project_id','task_id'), array($projectid,$taskid)) == 0) {
            $sql  = "INSERT INTO {$_TABLES['nfproject_comments']} (project_id, task_id, uid, timestamp, comment) ";
            $sql .= "VALUES ('$projectid','$taskid','{$userid}',UNIX_TIMESTAMP(),'$notes')";
        } else {
            $sql  = "UPDATE {$_TABLES['nfproject_comments']} SET comment='$notes', timestamp=UNIX_TIMESTAMP() ";
            $sql .= "WHERE project_id='$projectid' AND task_id='$taskid' ";
        }
        DB_query($sql);
    }

    $formtype = DB_getItem($_TABLES['nfproject_forms'],'formtype', "id='$prj_formid'");

    if ($_POST['taskaction'] == 'Complete Task') {
        if ($processid > 0 AND $taskid > 0) {
            $nfclass= new nexflow($processid,$userid);
            if ($actionopt == 'accept') {
                $statusmsg = "$formtype approved";
                nf_updateStatusLog($projectid,$prj_formid,$statusmsg);
                $status = DB_getItem($_TABLES['nfproject_forms'],'status', "id='$prj_formid'");
                $nfclass= new nexflow($processid,$userid);
                // Set Process Variable to true which may be checked in the workflow
                $nfclass->set_ProcessVariable('Review_Approval',0);
                $nfclass->complete_task($taskid);
                // If the form has not yet been rejected by another member then mark it accepted
                if ($status != 6) {
                    DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status='3' WHERE id='$prj_formid'");
                }

            } elseif ($actionopt == 'reject') {
                DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status='6' WHERE id='$prj_formid'");
                $statusmsg = "$formtype Rejected";
                nf_updateStatusLog($projectid,$prj_formid,$statusmsg);
                // Set Process Variable to false which may be checked in the workflow
                $nfclass->set_ProcessVariable('Review_Approval',1);
                $nfclass->cancel_task($taskid);
            } else {
                return "Need to check 'Reject' or 'Accept' to complete the task";
            }
        }
    }

}


/* Basic Review and Approval Form with Edit rights on form action */
function nf_approveEditForm ($taskrec,&$template,$rowid,$userid) {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $actionurl = $_CONF['site_url'] .'/nexflow/index.php';
    if (SEC_inGroup('nexFlow_Admin')) {
        $optLinkVars = "&taskuser=$userid";
    } else {
        $optLinkVars = '';
    }

    $reviewFormLink = $actionurl . '?op=edit&nomenu=1&formid=%s&result=%s&projectid=%s&singleuse=1'.$optLinkVars;

    $processid = $taskrec['processid'];
    $nfclass= new nexflow($processid);
    $project_id = $nfclass->get_processVariable('PID');

    // Check and see if process defined Result ID and Form ID are defined
    $rid = $nfclass->get_processVariable('RID');
    $fid = $nfclass->get_processVariable('FID');

    if (empty($rid) or empty($fid)) {   // If not defined look for them via the form(s) defined in the Task optional argument
        $projectFormResults = nf_getFormResult($taskrec['templateTaskid'],$project_id);
        $form_id = $projectFormResults['formid'];
        $prj_formid = $projectFormResults['prjformid'];
        $result_id = $projectFormResults['resultid'];
    } elseif ($fid > 0) {
        $prj_formid = DB_getItem($_TABLES['nfproject_forms'],'id',"project_id=$project_id AND form_id=$fid");
        $form_id = $fid;
        $result_id = $rid;
    }

    $template->set_file ("action{$rowid}",'application/basic_formapprove_action.thtml');
    $template->set_var ('function_handler','nf_approveEditForm_posthandler');
    if ($result_id > 0) {
        $link = sprintf($reviewFormLink,$form_id,$result_id,$project_id);
        $template->set_var ('review_link','href="#" onClick="nfNewWindow(\''.$link.'\');"');
    } else {
        $template->set_var ('review_link', 'href="#" onClick="alert(\'No Result on file found\');"');
    }
    $template->set_var ('form_id',$form_id);
    $template->set_var ('process_id',$processid);
    $template->set_var ('id',$taskrec['id']);
    $template->set_var ('rowid', $rowid);

    if (isset($prj_formid) and $prj_formid > 0)  {
        /* Retrieve the status if user has actioned this form */
        $sql  = "SELECT status FROM {$_TABLES['nfproject_approvals']} ";
        $sql .= "WHERE uid='{$userid}' AND form_id='$prj_formid' AND process_id='{$taskrec['processid']}'";
        $query = DB_query($sql);
        list ($status) = DB_fetchArray($query);

        // Determine if this task has recorded a comment yet
        $notes = DB_getItem($_TABLES['nfproject_comments'], 'comment', "project_id='$project_id' AND task_id='{$taskrec['id']}'");
        $template->set_var ('notes',$notes);
        if ($status == 3) {
            $template->set_var ('chk_accept', 'CHECKED');
        } elseif ($status == 6) {
            $template->set_var ('chk_reject', 'CHECKED');
        }
    }
    $template->parse('action_record',"action{$rowid}");

}


/* Basic Review Form action - task completes when action is closed */
function nf_approveEditForm_posthandler($processid,$taskid,$userid,$projectid) {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $nfclass= new nexflow($processid);
    if ($projectid == '' OR $projectid == 0) {
        $projectid = $nfclass->get_processVariable('PID');
    }

    $actionopt = COM_applyFilter($_POST['actionopt']);
    $taskid = COM_applyFilter($_POST['taskid']);
    $formid = COM_applyFilter($_POST['formid']);
    $processid = COM_applyFilter($_POST['processid']);
    if ($projectid > 0) {
        $prj_formid = DB_getItem($_TABLES['nfproject_forms'],'id', "project_id='$projectid' AND form_id='$formid'");
    }

    $status = DB_getItem($_TABLES['nfproject_forms'],'status', "id='$prj_formid'");

    if (DB_count($_TABLES['nfproject_approvals'],array('uid','form_id','process_id'), array($userid,$prj_formid,$processid)) == 0) {
        DB_query("INSERT INTO {$_TABLES['nfproject_approvals']} (process_id,form_id,uid) VALUES ('$processid','$prj_formid','{$userid}')");
    }
    if ($actionopt == 'accept') {
        DB_query("UPDATE {$_TABLES['nfproject_approvals']} SET status='3', date_updated=UNIX_TIMESTAMP() WHERE uid='{$userid}' AND form_id='$prj_formid'");
    } elseif ($actionopt == 'reject') {
        DB_query("UPDATE {$_TABLES['nfproject_approvals']} SET status='6', date_updated=UNIX_TIMESTAMP() WHERE uid='{$userid}' AND form_id='$prj_formid'");
    }

    if (trim($_POST['notes']) != '' ) {
        $notes =  ppPrepareForDB($_POST['notes']);
        if (DB_count($_TABLES['nfproject_comments'], array('project_id','task_id'), array($projectid,$taskid)) == 0) {
            $sql  = "INSERT INTO {$_TABLES['nfproject_comments']} (project_id, task_id, uid, timestamp, comment) ";
            $sql .= "VALUES ('$projectid','$taskid','{$userid}',UNIX_TIMESTAMP(),'$notes')";
        } else {
            $sql  = "UPDATE {$_TABLES['nfproject_comments']} SET comment='$notes', timestamp=UNIX_TIMESTAMP() ";
            $sql .= "WHERE project_id='$projectid' AND task_id='$taskid' ";
        }
        DB_query($sql);
    }

    $formtype = DB_getItem($_TABLES['nfproject_forms'],'formtype', "id='$prj_formid'");

    if ($_POST['taskaction'] == 'Complete Task') {
        if ($processid > 0 AND $taskid > 0) {
            $nfclass= new nexflow($processid,$userid);
            if ($actionopt == 'accept') {
                $statusmsg = "$formtype approved";
                nf_updateStatusLog($projectid,$prj_formid,$statusmsg);
                $status = DB_getItem($_TABLES['nfproject_forms'],'status', "id='$prj_formid'");
                $nfclass= new nexflow($processid,$userid);
                // Set Process Variable to true which may be checked in the workflow
                $nfclass->set_ProcessVariable('Review_Approval',0);
                $nfclass->complete_task($taskid);
                // If the form has not yet been rejected by another member then mark it accepted
                if ($status != 6) {
                    DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status='3' WHERE id='$prj_formid'");
                }

            } elseif ($actionopt == 'reject') {
                DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status='6' WHERE id='$prj_formid'");
                $statusmsg = "$formtype Rejected";
                nf_updateStatusLog($projectid,$prj_formid,$statusmsg);
                // Set Process Variable to false which may be checked in the workflow
                $nfclass->set_ProcessVariable('Review_Approval',1);
                $nfclass->cancel_task($taskid);
            } else {
                return "Need to check 'Reject' or 'Accept' to complete the task";
            }
        }
    }

}

?>