<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | reclaimproject.php                                                        |
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

$project_id = COM_applyFilter($_POST['projectid'],true);
$taskuser = COM_applyFilter($_REQUEST['taskuser'],true);


if ($taskuser > 0 AND SEC_inGroup('nexflow Admin')) {
    $usermodeUID = $taskuser;
} else {
    $usermodeUID = $_USER['uid'];
}

if (DB_count($_TABLES['nfprojects'], 'id', $project_id) == 1) {

    if ($CONF_NF['debug']) {
        COM_errorLog("Reclaim Project:$project_id");
    }

    $status = DB_getItem($_TABLES['nfprojects'], 'status' , "id='$project_id'");
    $prev_status = DB_getItem($_TABLES['nfprojects'], 'prev_status' , "id='$project_id'");
    if ($prev_status < 1 OR ($status == $prev_status)) {
        $prev_status = 1;
    }
    if ($status == 6)  { // Currently in Recycled State
        DB_query("UPDATE {$_TABLES['nfprojects']} SET status='$prev_status', prev_status=6 WHERE id='$project_id'");

    } elseif ($status == 7)  { // Currently in On-Hold State
        DB_query("UPDATE {$_TABLES['nfprojects']} SET status='$prev_status', prev_status=7 WHERE id='$project_id'");
            
        $taskQuery = DB_query("SELECT * FROM {$_TABLES['nfproject_taskhistory']} WHERE project_id=$project_id AND date_completed=0 AND status = 2");
        while ($histrec = DB_fetchArray($taskQuery,false)) {            
            // Update the current outstanding task which have a status of on-hold - to now have a completed timestamp                 
            DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP() WHERE id='{$histrec['id']}'");
            // For these tasks, reset the assigned timestamp and flag the task as new
            $sql = "UPDATE {$_TABLES['nfproject_taskhistory']} SET date_assigned = UNIX_TIMESTAMP(), ";
            $sql .= "date_started=0 WHERE project_id=$project_id AND task_id='{$histrec['task_id']}' AND status=0";
            DB_query($sql);
            DB_query("UPDATE {$_TABLES['nfqueue']} SET createdDate = NOW() WHERE id={$histrec['task_id']}");            
        }        
    }

}
echo COM_refresh($_CONF['site_url'] .'/nexflow/index.php?taskuser='.$usermodeUID);
exit;

?>