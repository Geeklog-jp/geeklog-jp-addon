<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | base_batch_functions.php                                                  |
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

// Do nothing task - good task to use if you need a task in the workflow to test and will always return true
function nf_noOperation($taskid,$processID) {
    nf_changeLog("nf_noOperation Batch Task exectuted - queue id: $taskid");
    return true;
}


// Basic batch task that will take a message or string defined in the task optionalParm field and write it to the log file
function nf_logMessage($taskid,$processID) {

    $nfclass = new nexflow($processid);
    $nfclass->set_currentTaskid($taskid);
    $logfileMsg = $nfclass->get_taskOptionalParm();
    nf_changeLog($logfileMsg);
    return true;
}


/*
 * Customize this function to check if the lookuplist item is being deleted
 * When a lookuplist item is deleted, we have a chance to see if the list item is used.
*/
function nf_chkLookupListsbeforeDelete($listid, $id) {
    global $_TABLES,$NF_LISTS,$NF_MYAPP;

    $retval = '';
    // Depending on the list, you will need to customize this logic to test if it is used
    // Example: Check if role name (list field 1) in this list is used in any workflows
    if ($listid = $NF_MYAPP['lookuplists']['expense_request_roles']) {
        // Check and see if this role 'process variable name' is used in a workflows
        $variableName = nexlistValue($listid,$id,0);
        if (DB_count($_TABLES['nftemplatevariables'],'variableName',$variableName)) {
            $retval = 'Nexflow is using this list item to assign workflow tasks';
        }
    }
    return $retval;

}


/* Workflow function that can be used to pause a workflow for a pre-defined period.
 * Use the optional parm to pass in the interval: number
 * Exmaple: month:1, day:5, hr:24
*/
function nf_pauseWorkflow($taskid,$processID) {
    global $_TABLES;

    $nfclass = new nexflow($processid);
    $nfclass->set_currentTaskid($taskid);
    $parms = explode(':',$nfclass->get_taskOptionalParm());

    // $createdDate in format yyyy-mm-dd hh:mm:ss
    $createdDate = DB_getItem($_TABLES['nfqueue'],'createdDate',"id='$taskid'");

    $testdatetime=0;
    $now = time();
    if ($parms[1] > 0) {
        if ($parms[0] == 'month') {
            $query = DB_query("SELECT UNIX_TIMESTAMP(createdDate + INTERVAL {$parms[1]} MONTH) FROM {$_TABLES['nfqueue']} WHERE id=$taskid");
            list ($testdatetime) = DB_fetchArray($query);
        } elseif ($parms[0] == 'day') {
            $query = DB_query("SELECT UNIX_TIMESTAMP(createdDate + INTERVAL {$parms[1]} DAY) FROM {$_TABLES['nfqueue']} WHERE id=$taskid");
            list ($testdatetime) = DB_fetchArray($query);
        } elseif ($parms[0] == 'hr') {
            $query = DB_query("SELECT UNIX_TIMESTAMP(createdDate + INTERVAL {$parms[1]} HOUR) FROM {$_TABLES['nfqueue']} WHERE id=$taskid");
            list ($testdatetime) = DB_fetchArray($query);
        } elseif ($parms[0] == 'min') {
            $query = DB_query("SELECT UNIX_TIMESTAMP(createdDate + INTERVAL {$parms[1]} MINUTE) FROM {$_TABLES['nfqueue']} WHERE id=$taskid");
            list ($testdatetime) = DB_fetchArray($query);
        } elseif ($parms[0] == 'sec') {
            $query = DB_query("SELECT UNIX_TIMESTAMP(createdDate + INTERVAL {$parms[1]} SECOND) FROM {$_TABLES['nfqueue']} WHERE id=$taskid");
            list ($testdatetime) = DB_fetchArray($query);
        }

        $logmsg = "nf_pauseWorkflow ({$parms[0]}:{$parms[1]}): taskid:$taskid, createdDate:$createdDate ($now), testdatetime:$testdatetime";
        if ($now > $testdatetime) {
            //COM_errorLog("$logmsg -> time has passed");
            return true;
        } else {
            //COM_errorLog($logmsg);
            return false;
        }
    } else {
        return false;
    }

}


?>