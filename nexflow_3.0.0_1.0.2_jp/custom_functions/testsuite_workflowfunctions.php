<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | testsuite_workflowfunctions.php                                           |
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
// |   Request Detail Functions for the Testsuite                                |
// +-----------------------------------------------------------------------------*/

function nf_AppGroupDisplay_testsuite($processid) {

    $retval = '<tr class="taskconsolesummary"><td>Custom Field1</td><td>Information about the workflow request</td></tr>';
    $retval .= '<tr class="taskconsolesummary"><td>Custom Field2</td><td>Custom data can be displayed as part of the request detail</td></tr>';
    return $retval;

}

/* +-----------------------------------------------------------------------------+
// |   Workflow Batch Functions for the Testsuite                                |
// +-----------------------------------------------------------------------------*/

function nf_testsuiteSetTaskname ($taskid, $processID) {

    $nfclass = new nexflow($processID);
    $nfclass->set_currentTaskid($taskid);
    $taskname = $nfclass->get_taskOptionalParm();
    $nfclass->set_processVariable('Taskname',$taskname);
    return true;
}

/* +-----------------------------------------------------------------------------+
// |   Workflow Interactive Functions for the Testsuite                          |
// +-----------------------------------------------------------------------------*/

function nf_testsuite_yesno($taskrec,&$template,$rowid,$userid) {

    $template->set_file("action{$rowid}",'application/testsuite/testsuite_yesno.thtml');
    $template->set_var('function_handler','nf_testsuite_yesno_posthandler');
    $template->parse('action_record',"action{$rowid}");
}


function nf_testsuite_yesno_posthandler($processid,$taskid,$userid,$projectid) {

    if ($processid > 0 AND $taskid > 0) {
        $nfclass = new nexflow($processid);
        $nfclass->set_currentTaskid($taskid);
        $msg = $nfclass->get_taskOptionalParm();

        if ($_POST['action'] == 'Accept') {
            $nfclass->complete_task($taskid);
            nf_changeLog("$msg - Accepted");
            return "Task: $taskid, Interactive Testsuite Function Completed - Accept Detected";
        } else if ($_POST['action'] == 'Reject') {
            $nfclass->cancel_task($taskid);
            nf_changeLog("$msg - Rejected");
            return "Task: $taskid, Interactive Testsuite Function Completed - Reject Detected";
        }
    }
}


function nf_testsuite_setvar1($taskrec,&$template,$rowid,$userid) {

    $template->set_file("action{$rowid}",'application/testsuite/testsuite_setvar1.thtml');
    $template->set_var('function_handler','nf_testsuite_setvar1_posthandler');
    $template->parse('action_record',"action{$rowid}");
}


function nf_testsuite_setvar1_posthandler($processid,$taskid,$userid,$projectid) {

    if ($processid > 0 AND $taskid > 0) {
        $nfclass = new nexflow($processid,$userid);
        $nfclass->set_currentTaskid($taskid);
        $msg = $nfclass->get_taskOptionalParm();
        $var1 = COM_applyFilter($_POST['var1'], true);
        $nfclass->set_processVariable('VAR1', $var1);
        $nfclass->complete_task($taskid);
        nf_changeLog("You set VAR1 to $var1.  Verify the following messages line up!");
    }
}


?>