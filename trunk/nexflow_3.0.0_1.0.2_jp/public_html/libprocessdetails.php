<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | libprocessdetails.php                                                     |
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

function processDetailGetOutstandingTasks($project_id,&$template) {
    global $_TABLES,$CONF_NF,$usermodeUID;

    // Retrieve any Outstanding Tasks
    // Determine the unique process id's for this project

    $sql = "SELECT wf_process_id,related_processes FROM {$_TABLES['nfprojects']} WHERE id='$project_id'";
    $query = DB_QUERY($sql);
    $A = DB_fetchArray($query);

    if ($A['related_processes'] != '') {
        $projectProcesses = explode(',',$A['related_processes']);
    } else {
        $projectProcesses = array();
    }
    array_push($projectProcesses, $A['wf_process_id']);
    // Check and see if there are any child process of this parent process - will if this is a regenerated process
    $A['wf_process_id'] = NXCOM_filterInt($A['wf_process_id']);
    $query = DB_query("SELECT id FROM {$_TABLES['nfprocess']} WHERE pid={$A['wf_process_id']}");
    while ($P = DB_fetchArray($query)) {
        array_push($projectProcesses, $P['id']);
    }

    $cid = 1;
    if (count($projectProcesses > 0)) {
        foreach ($projectProcesses AS $process_id) {
            // Get tasks that have assignment by variable

            $template->set_var ('taskuser', $usermodeUID);
            $template->set_var ('user_options',nf_listUsers());

            if ($process_id > 0 ) {
                $sql  = "SELECT distinct a.id, a.nf_processID,d.taskname, d.nf_templateID, a.status, a.archived, ";
                $sql .= "a.createdDate, c.uid, c.nf_processVariable, a.nf_templateDataID FROM {$_TABLES['nfqueue']} a ";
                $sql .= "LEFT JOIN {$_TABLES['nftemplateassignment']} b ON a.nf_templateDataID = b.nf_templateDataID ";
                $sql .= "LEFT JOIN {$_TABLES['nfproductionassignments']} c ON c.task_id = a.id ";
                $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} d on a.nf_templateDataID = d.id ";
                $sql .= "WHERE a.nf_processID = '$process_id' AND (a.archived IS NULL OR a.archived = 0)";
                $sql .= "ORDER BY a.id";

                $q2 = DB_query($sql);
                while($B = DB_fetchArray($q2,false)) {
                    if ($B['nf_processVariable'] == '') {
                        continue;
                    }
                    $template->set_var ('taskassign_mode', 'variable');
                    $template->set_var ('otaskid',$B['id']);

                    if (SEC_hasRights('nexflow.edit')) {
                        $template->set_var('otask_span',1);
                        $template->set_var('show_otaskaction','');
                    } else {
                        $template->set_var('otask_span',2);
                        $template->set_var('show_otaskaction','none');
                    }

                    $template->set_var ('otask_user', COM_getDisplayName($B['uid']));
                    $template->set_var ('otask_name', $B['taskname']);
                    $template->set_var ('otask_date',$B['createdDate']);
                    $template->set_var ('otask_id',$B['id']);
                    $template->set_var ('variable_id', $B['nf_processVariable']);

                    if ($cid == 1) {
                        $template->parse('outstandingtask_records','outstandingtasks');
                    } else {
                        $template->parse('outstandingtask_records','outstandingtasks',true);
                    }

                    $cid++;
                } // while
            }
        }

    }

}

function processDetailGetTasksHistory($project_id,&$template) {
    global $_TABLES,$CONF_NF;

    // Retrieve any Project Task History
    $sql  = "SELECT a.date_assigned,a.date_started, a.date_completed,a.status, b.username,d.taskname, ";
    $sql .= "d.isDynamicTaskName, d.dynamicTaskNameVariableID,c.nf_processID,c.id as qid FROM {$_TABLES['nfproject_taskhistory']} a ";
    $sql .= "LEFT JOIN {$_TABLES['users']} b on a.assigned_uid=b.uid ";
    $sql .= "LEFT JOIN {$_TABLES['nfqueue']} c on c.id=a.task_id ";
    $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} d on d.id=c.nf_templateDataID ";
    $sql .= "WHERE project_id='$project_id' AND (a.date_completed > 0 OR a.status=2)";
    $sql .= "ORDER BY date_assigned DESC";
    $query = DB_query($sql);
    if (DB_numRows($query) == 0) {
        $template->set_var ('task_records','');
    } else {
        $cid = 1;
        while($PD = DB_fetchArray($query)) {
            $template->set_var('qid',$PD['qid']);
            $template->set_var ('task_user', $PD['username']);
            if($PD['isDynamicTaskName'] == 1) {
                $sql  = "SELECT variableValue FROM {$_TABLES['nfprocessvariables']} where nf_processid='{$PD['nf_processID']}' ";
                $sql .= "AND nf_templateVariableID='{$PD['dynamicTaskNameVariableID']}'";
                $res=DB_query($sql);
                list($dynamicTaskName)=DB_fetchArray($res);
                if($dynamicTaskName==''){
                    $dynamicTaskName='Empty Dynamic Name';
                }
                $template->set_var ('taskhistory_name', $dynamicTaskName);
            } else {
                $template->set_var ('taskhistory_name', $PD['taskname']);
            }

            $template->set_var ('status',$CONF_NF['taskstatus'][$PD['status']]);
            if ($PD['date_assigned'] > 0) {
                $template->set_var ('task_date1',strftime("%b %d/%Y %H:%M", $PD['date_assigned']));
            } else {
                $template->set_var ('task_date1','N/A');
            }
            if ($PD['date_started'] > 0) {
                $template->set_var ('task_date2',strftime("%b %d/%Y %H:%M", $PD['date_started']));
            } else {
                $template->set_var ('task_date2','N/A');
            }
            if ($PD['date_completed'] > 0) {
                $template->set_var ('task_date3',strftime("%b %d/%Y %H:%M", $PD['date_completed']));
            } else {
                $template->set_var ('task_date3','N/A');
            }
            if ($cid == 1) {
                $template->parse('task_records','taskhistory');
            } else {
                $template->parse('task_records','taskhistory',true);
            }
            $cid++;
        } // while
    }

}


?>