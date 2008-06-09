<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxlib.php                                                               |
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

$rowid = COM_applyFilter($_GET['id'],true);
$taskid = COM_applyFilter($_GET['taskid'],true);
$project_id = COM_applyFilter($_GET['project_id'],true);
$op = COM_applyFilter($_GET['op']); 
$actionurl = $_CONF['site_url'] .'/nexflow/index.php';
$taskuser = COM_applyFilter($_GET['taskuser'],0);
$variableid = COM_applyFilter($_GET['variableid'],0);     // Used in the setowner function 
$cid = COM_applyFilter($_GET['cid'],true);

if ($taskuser > 0 AND SEC_hasRights('nexflow.admin')) {
    $usermodeUID = $taskuser;
} else {
    $usermodeUID = $_USER['uid'];
}
$nfclass = new nexflow();

if ($CONF_NF['debug']) {
    COM_errorLog("op:$op, Project:$project_id,taskuser:$usermodeUID,row:$rowid,cid:$cid,taskid:$taskid");
}

if ($op == 'starttask') {
    $startedDate = DB_getItem($_TABLES['nfqueue'], 'startedDate',"id='$taskid'");
    if ($startedDate <= 0) {
        DB_query("UPDATE {$_TABLES['nfqueue']} SET startedDate = NOW() WHERE id='$taskid'");
    }
    DB_query("UPDATE {$_TABLES['nfproject_taskhistory']} SET date_started = UNIX_TIMESTAMP() WHERE task_id='$taskid'");


} elseif ($op=='holdtask'){
        $status= DB_getItem($_TABLES['nfqueue'], 'status',"id='$taskid'");
        $status=NXCOM_filterInt($status);
        if($status!=2){
            $nfclass->hold_task($taskid);
        } else {
            $nfclass->unhold_task($taskid);
        }
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: text/xml");
        $XML = "<result>";
        $XML .= "</result>";
        print $XML;


} elseif ($op=='holdprocess'){
        $status= DB_getItem($_TABLES['nfprocess'], 'complete',"id='$taskid'");
        $status=NXCOM_filterInt($status);
        if($status==0){
            $nfclass->hold_process($taskid);
        } elseif($status==3) {
            $nfclass->unhold_process($taskid);
        }

        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: text/xml");
        $XML = "<result>";
        $XML .= "</result>";
        print $XML;

        
        
} elseif ($op == 'setowner') {

    if (SEC_hasRights('nexflow.admin')) {
        $proessessid = DB_getItem($_TABLES['nfqueue'],'nf_processID',"id=$taskid");
        $assigneduid = DB_getItem($_TABLES['nfproductionassignments'], 'uid', "task_id=$taskid");
        nf_reassign_task($taskid,$taskuser,$assigneduid,$variableid); 
        $sql = "SELECT a.id, b.taskname FROM {$_TABLES['nfqueue']} a LEFT JOIN {$_TABLES['nftemplatedata']} b ON a.nf_templateDataID=b.id WHERE a.id=$taskid;";
        $res = DB_query($sql);
        $A = DB_fetchArray($res);
        $comment = 'Task Owner change, was '.COM_getDisplayName($assigneduid).', now ';
        $comment .= COM_getDisplayName($taskuser) . " for task: {$A['taskname']}";
        $sql  = "INSERT INTO {$_TABLES['nfproject_comments']} (project_id, task_id, uid, timestamp, comment) ";
        $sql .= "VALUES ('$project_id',{$A['id']},'{$_USER['uid']}',UNIX_TIMESTAMP(),'$comment')";
        DB_query($sql);
    }

    require_once('libprocessdetails.php');

    $p = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
    $p->set_file (array (
        'outstanding'        =>     'project_detail_outstandingtasks.thtml',
        'outstandingtasks'   =>     'project_detail_outstandingtask_record.thtml'));

    $p->set_var('rowid',$rowid);
    $p->set_var('project_id',$project_id);

    processDetailGetOutstandingTasks($project_id,$p);
    $p->parse('output','outstanding');
    $output = $p->finish ($p->get_var('output'));
    $retval = htmlentities($output);
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("content-type: text/xml");
    $XML = "<result>";
    $XML .= "<record>$rowid</record>";
    $XML .= "<operation>$op</operation>";
    $XML .= "<html>$retval</html>";
    $XML .= "</result>";
    print $XML;


} elseif ($op == 'deleteproject') {
    if (SEC_hasRights('nexflow.admin') AND $project_id > 0 AND DB_Count($_TABLES['nfprojects'],id,$project_id) == 1) {
        $res = DB_query("SELECT description, project_num, wf_process_id FROM {$_TABLES['nfprojects']} WHERE id=$project_id");
        list ($projectTitle, $projectNum, $pid) = DB_fetchArray($res);

        nf_changeLog("Nexflow Admin {$_USER['username']} has deleted project id: $project_id. Project# $projectNum, Title:$projectTitle");

        $relatedProcesses = '';
        $sql = "SELECT related_processes from {$_TABLES['nfprojects']}";
        $res = DB_query($sql);
        while($B = DB_fetchArray($res)) {
            if($B['related_processes'] != '') {
                if($relatedProcesses == '') {
                    $relatedProcesses = $B['related_processes'];
                } else {
                    $relatedProcesses .= ','.$B['related_processes'];
                }
            }
        }
        if ( $relatedProcesses != '') {
            $sql = "SELECT id FROM {$_TABLES['nfqueue']} WHERE archived is NULL and nf_processID in ($relatedProcesses) ";
            $query = DB_query($sql);
            while (list($qid) = DB_fetchArray($query)) {
                nf_changeLog("Nexflow delete project related process queue record:$qid");
                DB_query("DELETE FROM {$_TABLES['nfqueue']} WHERE id=$qid");
            }
        }

        $nfclass->delete_process($pid);

        DB_query("DELETE FROM {$_TABLES['nfproject_forms']} WHERE project_id=$project_id");
        DB_query("DELETE FROM {$_TABLES['nfproject_timestamps']} WHERE project_id=$project_id");
        DB_query("DELETE FROM {$_TABLES['nfproject_comments']} WHERE project_id=$project_id");
        DB_query("DELETE FROM {$_TABLES['nfproject_taskhistory']} WHERE project_id=$project_id");
        DB_query("DELETE FROM {$_TABLES['nfprojects']} WHERE id=$project_id ");

        $html = '<div class="pluginAlert" style="margin:5px 20px 5px 20px ;padding:10px;">Project has been deleted';
        $html .= ' - <a href="'.$CONF_NF['TaskConsole_URL'] .'?op=allprojects">refresh</a> the page.</div>';
        $html =  htmlentities($html);
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: text/xml");
        $XML = "<result>";
        $XML .= "<record>$rowid</record>";
        $XML .= "<operation>$op</operation>";
        $XML .= "<html>$html</html>";
        $XML .= "</result>";
        print $XML;
    } else {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: text/xml");
        $XML = "<result>";
        $XML .= "<record>0</record>";
        $XML .= "<operation>$op</operation>";
        $XML .= "<html></html>";
        $XML .= "</result>";
        print $XML;
    }


} else {
    if (isset($taskid) AND $taskid > 0) {
        DB_query("UPDATE {$_TABLES['nfqueue']} SET startedDate = NOW() WHERE id='$taskid' AND startedDate is null");
        $processid = DB_getItem($_TABLES['nfqueue'], 'nf_processID',"id='$taskid'");
    } else {
        $processid = DB_getItem($_TABLES['nfprojects'], 'wf_process_id',"id='$project_id'");
    }

    $A['nf_processID'] = $processid;

    if (DB_count($_TABLES['nfprojects'],'id',$project_id) == 1) {
        include('projectdetails.php');
    } else {
        $projectdetails = '<p class="pluginAlert">Error => Project record: '.$project_id.' does not exist</p>';
    }

    $retval .= '<td colspan="5" style="padding:10px;"><div id="projectdetail_rec' .$rowid.'">';
    $retval .=  $projectdetails;
    $retval .= '</div></td>';
    $retval = htmlentities(($retval));
    $retval = str_replace('–', '&#45;', $retval);

    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("content-type: text/xml");
    $XML = "<result>";
    $XML .= "<record>$rowid</record>";
    $XML .= "<operation>$op</operation>";
    $XML .= "<html>$retval</html>";
    $XML .= "</result>";
    print $XML;
    
}
 
  
?>