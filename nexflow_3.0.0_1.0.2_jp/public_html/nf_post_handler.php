<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nf_post_handler.php                                                       |
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

include ('../lib-common.php');

/* Filter incoming variables */

$myvars = array (
    'form_id',
    'op',
    'mode',
    'id',
    'project_id',
    'project_formid',
    'processid',
    'taskid',
    'usermodeuid',
    'formtype',
    'autoclose',
    'singleuse'
);
$newform = COM_applyFilter($_REQUEST['newform'], true);

ppGetData($myvars,true);

// Check and see if this user has priviledge to post this form as another user
if ($usermodeuid > 0 AND SEC_hasRights('nexflow.admin')) {
    $postUID = $usermodeuid;
    $optReturnVars = "?taskuser=$postUID";
} else {
    $postUID = $_USER['uid'];
    $optReturnVars = '';
}

if (isset($autoclose) AND $autoclose == 'true') {
    if ($optReturnVars == '') {
        $optReturnVars  = '?autoclose=1';
    } else {
        $optReturnVars .= '&autoclose=1';
    }
}

// If user posting a form as draft - return the user back to editing the form
$form_draftreturn_url =  $CONF_NF['TaskConsole_URL'] . $optReturnVars . '&op=edit&formid=%s&result=%s&singleuse=' . $singleuse;

/* Update the Main Request/Project Table record key meta data */
/* Use this area for any custom handling routines that read from
/* the posted and saved data to update other tables */
function nf_updateRequestProjectInfo($id,$result_id) {
    global $_TABLES,$postUID;

}



/* MAIN CODE */
if ($form_id > 0 AND DB_count($_TABLES['formDefinitions'],'id',$form_id) == 1) {
        $form_name = DB_getItem($_TABLES['formDefinitions'], 'name',"id='$form_id'");

        // Results id is returned via the formhandler as $id
        // Check if this is an update of an existing form
        if (DB_count($_TABLES['formResults'],'id',$id) == 1 AND $newform != 1) {

            nexform_dbupdate($form_id,$id,$postUID);

            if ($mode == 'draft')  {   // User is not ready to submit it for approval
                $statusmsg = "$form_name Draft Updated";
                $form_return_url = sprintf($form_draftreturn_url,$form_id,$id);                 
                // Check if custom workflow handler function being requested
                if (isset($_POST['custom_handler']) AND function_exists($_POST['custom_handler'])) {
                    $custom_handler = COM_applyFilter($_POST['custom_handler']);
                    $prj_id = COM_applyFilter($_POST['projectid']);
                    $custom_handler($processid,$taskid,$usermodeuid,$prj_id);
                }
            } else {
                $statusmsg = "$form_name has been created";
                $status = 1;
                DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status = '$status' WHERE id='$project_formid'");

                // Update the Projects or Request main record
                nf_updateRequestProjectInfo($project_formid,$id);

                if (!isset($processid) OR $processid < 1) {
                    $processid = DB_getItem($_TABLES['nfprojects'],'wf_process_id', "id='$project_id'");
                }
                if (!isset ($taskid) OR $taskid < 1) {
                    $taskid = DB_getItem($_TABLES['nfproject_forms'],'created_by_taskid', "id='$project_formid'");
                }
                if ($CONF_NF['debug']) {
                    COM_errorLog("processid: $processid, taskid:$taskid, project_id:$project_id, formid: $project_formid,formtype:$formtype");
                }
                if ($processid > 0 AND $taskid > 0) {
                    // Check if custom workflow handler function being requested
                    if (isset($_POST['custom_handler']) AND function_exists($_POST['custom_handler'])) {
                        $custom_handler = COM_applyFilter($_POST['custom_handler']);
                        if ($CONF_NF['debug']) {
                            COM_errorLog("$form_name form submitted - using custom handler function $custom_handler");
                        }
                        $prj_id = COM_applyFilter($_POST['projectid']);
                        $custom_handler($processid,$taskid,$usermodeuid,$prj_id);
                    } else {
                        if ($CONF_NF['debug']) {
                            COM_errorLog("$form_name form submitted - completed taskid: $taskid");
                        }
                        $nfclass= new nexflow($processid,$postUID);
                        $nfclass->complete_task($taskid);
                        DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP() WHERE task_id='$taskid'");
                    }
                }
            }
            DB_query("INSERT INTO {$_TABLES['nfproject_timestamps']} (project_id,project_formid,statusmsg,timestamp,uid)
                VALUES ('$project_id','$project_formid','$statusmsg',UNIX_TIMESTAMP(),'{$postUID}') ");

        } else {   // New Record

            /* Save results to Database */
            /* Get formtype from posted form - should be a hidden field in form definition */
            $formtype = DB_getItem($_TABLES['formFields'],'field_values', "formid='$form_id' AND field_name='formtype'");
            $taskid = intval($taskid);
            $dup_form_check = DB_getItem($_TABLES['nfproject_forms'], 'id', "created_by_taskid=$taskid AND formtype='$formtype'");
            if ($dup_form_check !== NULL) {  //form already exists
                if ($mode == 'draft' AND !isset($_POST['custom_handler'])) {
                    echo COM_refresh($form_return_url);
                }
                else {
                    echo COM_refresh($_CONF['site_url'] .'/nexflow/index.php' .$optReturnVars);
                }
                exit;
            }
            DB_query("INSERT INTO {$_TABLES['nfproject_forms']} (formtype,created_by_taskid) VALUES ('$formtype', '$taskid');");
            $project_formid = DB_insertID();

            if ($newform == 1) {
                nexform_dbupdate( $form_id, $id);
                $result_id = $id;
            }
            else {
                $result_id = nexform_dbsave($form_id,$postUID);
            }
            $nfclass= new nexflow($processid,$postUID);

            /* Update the hit or results counter */
            DB_query("UPDATE {$_TABLES['formDefinitions']} SET responses = responses + 1 WHERE id='$form_id'");
            $newproject = false;
            if ($processid > 0 AND $taskid > 0) {
                $project_id = $nfclass->get_ProcessVariable('PID');
            }

            // Create new project tracking record if project does not yet exist
            if ($project_id < 1 OR DB_count($_TABLES['nfprojects'],'id',$project_id) == 0) {
                $processid = intval($processid);
                DB_query("INSERT INTO {$_TABLES['nfprojects']} (originator_uid,wf_process_id,wf_task_id,status)
                    VALUES ('{$postUID}','$processid','$taskid','1') ");
                $project_id = DB_insertID();
                $nfclass->set_ProcessVariable('PID',$project_id);
                $newproject = true;
                if ($CONF_NF['debug']) {
                    COM_errorLog("form_post_handler: Create new project_id: $project_id");
                }
                DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET project_id='$project_id' WHERE task_id='$taskid'");
            }

            // Create new form tracking record for this project
            /* Get formtype from posted form - should be a hidden field in form definition */
            $formtype = DB_getItem($_TABLES['formDefinitions'],'shortname', "id='$form_id'");

            DB_query("INSERT INTO {$_TABLES['nfproject_forms']} (project_id,form_id,formtype,results_id,created_by_taskid,created_by_uid)
                 VALUES ('$project_id','$form_id','$formtype','$result_id','$taskid','$postUID') ");
            $project_formid = DB_insertID();
            if ($CONF_NF['debug']) {
                COM_errorLog("nfform_post_handler: Create new nfproject_forms record: id: $project_formid");
            }

            // Create new form timestamp record - used to record stats
            DB_query("INSERT INTO {$_TABLES['nfproject_timestamps']} (project_id,project_formid,statusmsg,timestamp,uid)
                VALUES ('$project_id','$project_formid','$statusmsg',UNIX_TIMESTAMP(),'{$postUID}') ");

            if ($mode == 'draft') {    // User is not ready to submit it for approval - so don't complete task yet
                $form_return_url = sprintf($form_draftreturn_url,$form_id,$result_id);            
                $statusmsg = "$form_name Draft Updated";
                $status = 0;
            } else {
                $statusmsg = "$form_name has been created";
                $status = 1;
                if ($processid > 0 AND $taskid > 0) {

                    // Check if custom workflow handler function being requested - used for inline action forms
                    if (isset($_POST['custom_handler']) AND function_exists($_POST['custom_handler'])) {
                        $custom_handler = COM_applyFilter($_POST['custom_handler']);
                        if ($CONF_NF['debug']) {
                            COM_errorLog("$form_name form submitted - using custom handler function $custom_handler");
                        }
                        $prj_id = COM_applyFilter($_POST['projectid']);
                        $custom_handler($processid,$taskid,$usermodeuid,$prj_id);
                    } else {                    
                        $nfclass->complete_task($taskid);
                        if ($CONF_NF['debug']) {
                            COM_errorLog("$form_name form submitted - completed taskid: $taskid");
                        }
                        DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP() WHERE task_id='$taskid'");
                    }
                }
            }

            DB_query("UPDATE {$_TABLES['nfproject_forms']} SET status = '$status' WHERE id='$project_formid'");

            if ($newproject) {
                /* Update the project fields with information if known */
                $sql  = "SELECT results_id FROM {$_TABLES['nfproject_forms']} ";
                $sql .= "WHERE project_id='$project_id' AND form_id='$form_id' ";
                $query = DB_query($sql);
                if (DB_numRows($query) == 1) {
                    list ($newresult_id) = DB_fetchArray($query);
                    $sql  = "SELECT name as description FROM {$_TABLES['formDefinitions']} ";
                    $sql .= "WHERE id='$form_id'";
                    list ($description) = DB_fetchArray(DB_query($sql));
                    if(!get_magic_quotes_gpc() ) {
                        $description = addslashes($description);
                    }
                    $sql  = "UPDATE {$_TABLES['nfprojects']} SET description ='$description' ";
                    $sql .= "WHERE id='$project_id' ";
                    DB_query($sql);

                    // Update the Projects or Request main record
                    nf_updateRequestProjectInfo($project_formid,$newresult_id);
                }
            }
        }
}

/* Support for Save a Draft feature to having saved the form return the user to the form in edit mode. */
if ($mode == 'draft' AND !isset($_POST['custom_handler'])) {
    echo COM_refresh($form_return_url);
} else {
    echo COM_refresh($CONF_NF['TaskConsole_URL'] .$optReturnVars);
}

?>