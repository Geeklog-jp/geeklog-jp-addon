<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | yes-no.php                                                                |
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

require_once('../../lib-common.php');
if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

$action = COM_applyFilter($_GET['action']);
$taskid = COM_applyFilter($_GET['taskid'], true);
$processid = COM_applyFilter($_GET['processid'], true);

switch ($action) {
    case 'Accept':
        // Attach to workflow instance - pass in process to attach to and the complete the current task
        $nfclass = new nexflow($processid);
        $nfclass->complete_task($taskid);  // Normal completion status - accept
        nf_changeLog("Manual Web Task, Process: $processid, Workflow Queue ID: $taskid. Accept Status Set");
        echo COM_refresh($_CONF['site_url'] .'/nexflow/index.php');
        exit();
        break;

    case 'Reject':
        // Attach to workflow instance - pass in process to attach to and the complete the current task
        $nfclass = new nexflow($processid);
        $nfclass->cancel_task($taskid);  // Cancel completion status - reject
        nf_changeLog("Manual Web Task, Process: $processid, Workflow Queue ID: $taskid. Reject Status Set");
        echo COM_refresh($_CONF['site_url'] .'/nexflow/index.php');
        exit();
        break;
}

echo COM_siteHeader();
echo COM_startBlock('Test suite basic handler script');
echo '<div class="pluginInfo" style="padding:20px;border:1px solid #CCC;"><fieldset><legend>Test suite Handler Script</legend>';
echo '<div><p>Test of a script used as extenal workflow handler. A basic form prompts the user with a question with two choices<ul style="margin-bottom:5px;margin-top:5px;"><li>accept or reject</ul></p><p>The workflow will then execute a different series of steps depending on the button pressed.</p><p>The external handler script can be a basic script or a complex application that returns control upon completion to the workflow engine. The next task can be basic conditional task like in this workflow example or someother task type like a batch task that checks external information or database query to determine the next task path to perform</p><p>A trace of the execution and the workflow path followed is captured in the error.log file<center>'; 

echo '<form method="get" action="'.$_CONF['site_url'].'/nexflow/testsuite/yes-no.php"><br><br>
    <input type="submit" name="action" value="Reject">
    <input type="submit" name="action" value="Accept">
    <input type="hidden" name="taskid" value="'.$taskid.'">
    <input type="hidden" name="processid" value="'.$processid.'">
    </form></fieldset></center></p></div>';
    
echo COM_endBlock();
echo COM_siteFooter();
    

?>