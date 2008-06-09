<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | wftrace.php                                                               |
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

require_once ('../../../lib-common.php'); 
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

if (!SEC_hasRights('nexflow.admin')) {
    echo COM_siteHeader();
    echo COM_startBlock("Access Error");
    echo '<div style="text-align:center;padding-top:20px;">';
    echo "You do not have sufficient access.";
    echo "<p><button  onclick='javascript:history.go(-1)'>Return</button></p><br>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit();
}

$actionURL = $_CONF['site_admin_url'] . '/plugins/nexflow/wftrace.php';

if (isset($_POST['sprocessid'])) {
    $process = COM_applyFilter($_POST['sprocessid'],true);
    $singletask = 1;
} else {
    $process = COM_applyfilter($_GET['process'],true);
    $singletask = COM_applyfilter($_GET['singletask'],true);
}

if (isset($_POST['staskid'])) {
    $task = COM_applyFilter($_POST['staskid'],true);
    $singletask = 1;
} else {
    $task = COM_applyfilter($_GET['task'],true);
    $singletask = COM_applyfilter($_GET['singletask'],true);
}
$rev = COM_applyfilter($_GET['rev'],true);
$op = COM_applyfilter($_GET['op']);
$singleuse = COM_applyfilter($_REQUEST['singleuse'],true);

$taskStatus = array(
    0   => 'Un-completed',
    1   => 'Completed',
    2   => 'On-hold',
    3   => 'Cancelled',
    4   => 'If Condition False'
);


// Retrieve the last workflow queue records
function wfBuildSQLstatement($taskid,$processid,$revdirection,$singletask,$op='') {
    global $_TABLES;
    
    $sql =  "SELECT a.id, a.status, a.archived, a.uid, a.nf_templateDataID, a.nf_processID, a.createdDate, a.completedDate, c.function, ";
    $sql .= "c.nf_templateID, c.taskname, c.nf_stepType, e.stepType as tasktype, a.nf_processID, b.pid, c.assignedByVariable, ";
    $sql .= "c.nf_handlerid, c.logicalID, c.assignedByVariable, c.function, c.formid,d.templateName ";
    $sql .= "FROM {$_TABLES['nfqueue']} a INNER JOIN {$_TABLES['nfprocess']} b ON a.nf_processId = b.id ";
    $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c ON a.nf_templateDataId = c.id ";
    $sql .= "INNER JOIN {$_TABLES['nftemplate']} d ON b.nf_templateId = d.id ";
    $sql .= "LEFT JOIN {$_TABLES['nfsteptype']} e on c.nf_stepType=e.id ";
    if ($op == '') {
        if ($processid > 0) {
            $sql .= "WHERE nf_processID = $processid ";
        } else {
            $sql .= "WHERE 1=1 ";
        }
        if ($taskid > 0) {
            if ($singletask == 1) {
                if ($revdirection == 1) {
                    $sql .= "AND a.id = $taskid ";
                    $sql .="ORDER BY a.id DESC LIMIT 1";         
                } else {
                    $sql .= "AND a.id = $taskid ";
                    $sql .="ORDER BY a.id ASC LIMIT 1";         
                }
            } else {
                if ($revdirection == 1) {
                    $sql .= "AND a.id < $taskid ";
                    $sql .="ORDER BY a.id DESC LIMIT 1";         
                } else {
                    $sql .= "AND a.id > $taskid ";
                    $sql .="ORDER BY a.id ASC LIMIT 1";         
                }
            }
        } else {
            $sql .="ORDER BY a.id DESC LIMIT 1";
        }
    } else {
        $sql .= "WHERE 1=1 ";        
        if ($op == 'start') {
            $sql .= "order by a.id ASC LIMIT 1";
        } elseif ($op == 'end') {
            $sql .= "order by a.id DESC LIMIT 1";            
        }
    }
    
    return $sql;
}

$sql = wfBuildSQLstatement($task,$process,$rev,$singletask,$op);
$query = DB_query($sql);

// Check if there is another task in the same direction as this task

if ($singleuse == 1) {
    echo COM_siteHeader('none');
} else {
    echo COM_siteHeader('menu');
}
echo COM_startBlock('WorkFlow Trace and Debug Tool');

if (DB_numRows($query) > 0) {
    if ($singleuse != 1) {
        echo '<div>';
        $navbar = new navbar;
        $navbar->add_menuitem('My Tasks',$_CONF['site_url'] .'/nexflow/index.php?op=mytasks');
        $navbar->add_menuitem('Outstanding Tasks',$_CONF['site_url'] .'/nexflow/outstanding.php');
        $navbar->add_menuitem('WF Trace',$_CONF['site_admin_url'] .'/plugins/nexflow/wftrace.php');
        $navbar->set_selected('WF Trace');
        echo $navbar->generate();
        echo '</div>';
    }
    
    $p = new Template($_CONF['path_layout'] . 'nexflow/admin');
    $p->set_file ('page' ,'traceview.thtml');
    $p->set_var('action_url',$actionURL);
    $p->set_var('singleuse',$singleuse);
    $p->set_var('sprocessid',$process);
    $p->set_var('queue_totalsize',DB_count($_TABLES['nfqueue']));
    $p->set_var('start_link',$_CONF['site_admin_url'] .'/plugins/nexflow/wftrace.php?op=start&singleuse='.$singleuse);
    $p->set_var('end_link',$_CONF['site_admin_url'] .'/plugins/nexflow/wftrace.php?op=end&singleuse='.$singleuse);
    
    while ($A = DB_fetchArray($query)) {


        $assigned_uids = nf_getAssignedUID($A['id']);        
        if (count($assigned_uids) > 0) {
            foreach ($assigned_uids as $uid) {
                $assignedNames[] = COM_getDisplayName($uid);
            }
            $assigned = implode(',',$assignedNames);
            
        } else {
            $assigned = 'Un-Assigned';
        }

        // Check if there is another task in the same direction as this task  and same process        
        $nsql = wfBuildSQLstatement($A['id'],$A['nf_processID'],1,0,'');        
        $nquery = DB_query($nsql);
        if (DB_numRows($nquery) == 0) { 
            $p->set_var ('sameprocess_prevtask_link','Previous Task');
        } else {
            $link = '<a href="'.$actionURL."?process={$A['nf_processID']}&task={$A['id']}&rev=1&singleuse=$singleuse\">Previous Task</a>";
            $p->set_var ('sameprocess_prevtask_link',$link);
        }
        
        $nsql = wfBuildSQLstatement($A['id'],$A['nf_processID'],0,0);
        $nquery = DB_query($nsql);
        if (DB_numRows($nquery) == 0) {
            $p->set_var ('sameprocess_nexttask_link','Next Task');        
        } else {
            $link = '<a href="'.$actionURL."?process={$A['nf_processID']}&task={$A['id']}&singleuse=$singleuse\">Next Task</a>";
            $p->set_var ('sameprocess_nexttask_link',$link);             
        }

        // Check if there is a previous task for this process
        $psql = wfBuildSQLstatement($A['id'],0,1,0);
        $pquery = DB_query($psql);     
        if (DB_numRows($pquery) == 0) { 
            $p->set_var ('prevtask_link','Previous Task');                
        } else {       
            $link = '<a href="'.$actionURL."?task={$A['id']}&rev=1&singleuse=$singleuse\">Previous Task</a>";
            $p->set_var ('prevtask_link',$link);             
        }
        // Check if there is a next task for this process
        $nsql = wfBuildSQLstatement($A['id'],0,0,0);
        $nquery = DB_query($nsql);          
        if (DB_numRows($nquery) == 0) {
            $p->set_var ('nexttask_link','Next Task');                      
        } else {
            $link = '<a href="'.$actionURL."?task={$A['id']}&singleuse=$singleuse\">Next Task</a>";
            $p->set_var ('nexttask_link',$link);             
        }
        
        $p->set_var ('qid',$A['id']);
        $p->set_var ('task_id',$A['nf_templateDataID']);  
        $p->set_var ('template_name',$A['templateName']);
        $p->set_var ('task_name',$A['taskname']);
        $p->set_var ('status',"{$A['status']} - {$taskStatus[$A['status']]}");
        if ($A['archived'] == 1) {
            if (isset($A['completedDate'])) {
                $p->set_var ('archived',"Yes&nbsp;/&nbsp;{$A['completedDate']}");
            } else {
                $p->set_var('archived','Yes');
            }
        } else {
            $p->set_var ('archived','No');              
        }

        // Show process state
        if ($A['processState'] == 1) {
            $processState = 'Completed';
        } elseif ($A['processState'] == 2) {
            $processState = 'Regenerated';
        } else {
            $processState = 'Active';
        }
        $p->set_var ('process_id',"{$A['nf_processID']}&nbsp;/&nbsp;{$processState}");
        $p->set_var ('parent_process_id',$A['pid']);
        if ($A['pid'] > 0) {
            $trace_parent_link = "<a href=\"{$actionURL}?process={$A['pid']}&singleuse=$singleuse\">Trace Parent Process</a>";
            $p->set_var('trace_parent_link',$trace_parent_link);
        } else {
            $p->set_var ('trace_parent_link','');
        }
        $p->set_var ('logical_task_id',$A['logicalID']);
        $p->set_var ('task_type',$A['tasktype']);
        $p->set_var ('task_date',$A['createdDate']);           
        
        $interactive_task = false;
        $nexrowclass = 1;
        switch  ($A['nf_stepType']) {
            case 1:     // Manual Web
                $handler_id = DB_getItem($_TABLES['nftemplatedata'], 'nf_handlerId', "id={$A['nf_templateDataID']}");              
                $handler = DB_getItem($_TABLES['nfhandlers'], 'handler', "id=$handler_id"); 
                $p->set_var ('task_related_information', "<tr class=\"pluginRow2\"><td width=\"40%\"><label>Handler:</label></td><td>$handler</td></tr>");
                break;              
            case 5:     // IF Task
                $B = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['nftemplatedata']} WHERE id={$A['nf_templateDataID']}")); 
                if ($B['argumentVariable'] > 0) {
                    $variableName = DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id='{$B['argumentVariable']}'");
                    $operator = DB_getItem($_TABLES['nfifoperators'], 'operator', "id='{$B['operator']}'");
                    $if_task_condition = "{$variableName} {$operator} {$B['ifValue']}";
                } else {
                    $if_task_condition = DB_getItem($_TABLES['nfifprocessarguments'],'label',"id='{$B['argumentProcess']}'");
                }
                $if_task_condition = "<tr class=\"pluginRow2\"><td width=\"40%\"><label>Condition:</label></td><td>$if_task_condition</td></tr>";
                $p->set_var ('task_related_information',$if_task_condition);            
                break;
            case 6:     // Batch Function
                $p->set_var ('task_related_information', "<tr class=\"pluginRow2\"><td width=\"40%\"><label>Function:</label></td><td>{$A['function']}</td></tr>");
                break;                
            case 7:     // Interactive Function
                $p->set_var ('task_related_information', "<tr class=\"pluginRow2\"><td width=\"40%\"><label>Function:</label></td><td>{$A['function']}</td></tr>");
                $interactive_task = true;                
                break;
            case 8:     // Form Function
                $task_formid = DB_getItem($_TABLES['nftemplatedata'], 'formid', "id={$A['nf_templateDataID']}");              
                $form_name = DB_getItem($_TABLES['formDefinitions'],'name',"id=$task_formid");
                $p->set_var ('task_related_information', "<tr class=\"pluginRow2\"><td width=\"40%\"><label>Form:</label></td><td>$form_name</td></tr>");
                $interactive_task = true;                
                break;
           default:
                $p->set_var ('task_related_information','');
                $nexrowclass = 2;                 
                break;
            
        }
        if ($interactive_task) {
            if ($A['assignedByVariable'] == 1) {
                $sql = "SELECT nf_processVariable FROM {$_TABLES['nftemplateassignment']} ";
                $sql .= "WHERE nf_templateDataID={$A['nf_templateDataID']} AND nf_processVariable IS NOT NULL";
                $vquery = DB_query($sql);
                $variables = array();
                while (list($variableID) = DB_fetchArray($vquery)) {                
                    $variables[] = DB_getItem($_TABLES['nftemplatevariables'],'variableName',"id='$variableID'");
                }
                $variableAssignment = implode(',',$variables);                                        
                $html .= "<tr class=\"pluginRow{$nexrowclass}\"><td width=\"40%\"><label>Assigned by Variable:</label></td><td>$variableAssignment</td></tr>";          
            } else {
                $html = "<tr class=\"pluginRow{$nexrowclass}\"><td width=\"40%\"><label>Assignment Type:</label></td><td>By User</td></tr>";
                $p->set_var ('task_assignment_info',$html);            
            }
            $nexrowclass = ($nexrowclass == 1) ? 2: 1;
            $html .= "<tr class=\"pluginRow{$nexrowclass}\"><td width=\"40%\"><label>Task Owner :</label></td><td>$assigned</td></tr>";
            $p->set_var ('task_assignment_info',$html);               
        } else {
            $p->set_var ('task_assignment_info','');
        }

        $p->parse ('output', 'page');
        echo $p->finish ($p->get_var('output'));

        // Get all related Process Variables 
        $sql =  "SELECT a.nf_templateVariableID, a.variableValue, b.variableName FROM {$_TABLES['nfprocessvariables']} a, {$_TABLES['nftemplatevariables']} b ";
        $sql .= "WHERE a.nf_templateVariableID=b.id AND a.nf_processID='{$A['nf_processID']}'";
        $process_query = DB_query($sql);
        echo '<table width="100%" class="plugin"><tr>';
        echo '<th>Variable ID</th><th>Variable Name</th><th>Value</th><th>User Name if applicable</th>';
        echo '</tr>';
        $cssid = 1;
        while ($B = DB_fetchArray($process_query,false)) {
            echo '<tr class="pluginRow'.$cssid.'">';
            echo "<td>{$B['nf_templateVariableID']}</td>";
            echo "<td>{$B['variableName']}</td>";
            echo "<td>{$B['variableValue']}</td>";
            if ($B['variableValue'] > 0) {
                echo '<td>' . COM_getDisplayName($B['variableValue']).'</td>';
            } else {
                echo '<td>N/A</td>';
            }
            echo LB;
            $cssid = ($cssid == 1) ? 2 : 1;
        }
        echo '</td></table>';
        
    }
    

} else {
    echo '<div class="pluginAlert" style="text-align:center;margin-top:10px;padding:20px;">No Workflow Queue Records Found';
    echo '<p><a href="'.$actionURL.'">Reset</a></p>';
    echo '</div>';
}

echo COM_endBlock();
echo COM_siteFooter();




?>