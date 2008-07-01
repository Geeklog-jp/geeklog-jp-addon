<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate_tasks.php                                                      |
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

require_once("../../../lib-common.php"); // Path to your lib-common.php

if (!SEC_hasRights('nexflow.admin')) {
    print ('No access rights');
    exit();
}
$op = COM_applyFilter($_GET['op']);
$taskid = COM_applyFilter($_GET['taskid'],true);
$parm1 = COM_applyFilter($_GET['parm1'],true);
$mode = COM_applyFilter($_GET['mode']);
//apr 12/06 COM_applyFilter filtering out everything after comma
if(!get_magic_quotes_gpc()) {
    $message = addslashes(htmlspecialchars($_GET['message']));
}
else {
    $message = htmlspecialchars($_GET['message']);
}
if ($CONF_NF['debug']) {
    COM_errorLog("ajaxupdate_tasks.php => op:$op,taskid:$taskid,parm1:$parm1,mode:$mode");
}
$retval = '';
// Main Control Section Begins


/* Function to generate replacement HTML SELECT Element that will be returned to client Ajax Handler */
/* Client Ajax Response Handler will then update the client page via DOM */
function ajaxhandler_assignedVariables($taskid) {
    global $_TABLES,$op;

    $options = '';
    $sql =  "SELECT b.id, b.variableName FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['nftemplatevariables']} b ";      
    if ($op == 'addAssignVar' OR $op == 'delAssignVar') {
            $sql .= "WHERE a.nf_processVariable=b.id AND a.nf_templateDataID='{$taskid}'";
            $fieldid = 'selvariableassignment';
            $fieldname = 'task_assignedVariables';
    } elseif ($op == 'addPreNotifyVariable' OR $op == 'delPreNotifyVariable' ) {
            $sql .= "WHERE a.nf_prenotifyVariable=b.id AND a.nf_templateDataID='{$taskid}'";
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($taskid,999)) == 1) {  
                $options = "<option value=\"999\">TASK_OWNER</option>";                 
            }            
            $fieldid = 'selprenotify';
            $fieldname = 'task_prenotify';                      
    } elseif ($op == 'addPostNotifyVariable' OR $op == 'delPostNotifyVariable' ) {
            $sql .= "WHERE a.nf_postnotifyVariable=b.id AND a.nf_templateDataID='{$taskid}'";
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_postnotifyVariable'),array($taskid,999)) == 1) {  
                $options = "<option value=\"999\">TASK_OWNER</option>";                 
            }            
            $fieldid = 'selpostnotify';
            $fieldname = 'task_postnotify';
    } elseif ($op == 'addReminderNotifyVariable' OR $op == 'delReminderNotifyVariable' ) {
            $sql .= "WHERE a.nf_remindernotifyVariable=b.id AND a.nf_templateDataID='{$taskid}'";
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($taskid,999)) == 1) {  
                $options = "<option value=\"999\">TASK_OWNER</option>";                 
            }
            $fieldid = 'selremindernotify';
            $fieldname = 'task_remindernotify';            
    }             
    $q = DB_query($sql);
    while (list($id, $label) = DB_fetchArray($q)) {
        $options .= "<option value=\"$id\">$label</option>";
        }
    $html .= '<select id="'.$fieldid.'" name="'.$fieldname.'" size="4" style="width:160px;"><option value="1">'.$options.'</option></select>';
    return htmlentities($html);    
    
}

/* Function to generate replacement HTML SELECT Element that will be returned to client Ajax Handler */
/* Client Ajax Response Handler will then update the client page via DOM */
function ajaxhandler_assignedUsers($taskid) {
    global $_TABLES;
    $sql  = "SELECT b.uid, b.fullname FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['users']} b ";
    $sql .= "WHERE a.uid=b.uid AND a.nf_templateDataID='{$taskid}' ORDER BY b.fullname";    
    $q = DB_query($sql);
    $options = '';
    while (list($id, $label) = DB_fetchArray($q)) {
        $options .= "<option value=\"$id\">$label</option>";
        }
    $html .= '<select id="seluserassignment" name="task_assignedUsers" size="4" style="width:160px;"><option value="1">'.$options.'</option></select>';
    return htmlentities($html);    
    
}

/* Main code to handle the AJAX Request depending on operation
 * Check if valid request - valid variables, or updateMessage Operation
 * Need to allow a null message string to allow message to be removed
*/

if ($taskid > 0 AND ($parm1 > 0 OR strpos($op,'Message') > 0) ) {

    switch ($op) {
        
        case 'addAssignVar':
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_processVariable'),array($taskid,$parm1)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_processVariable  ) ";
                $sql .= "values ('$taskid','$parm1')";
                DB_Query($sql );
            }
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;

        case 'delAssignVar':
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid' AND nf_processVariable='$parm1'");
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;

        case 'addAssignUser':
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'uid'),array($taskid,$parm1)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, uid  ) ";
                $sql .= "values ('$taskid','$parm1')";
                $result = DB_Query($sql );
            }
            $retval =  ajaxhandler_assignedUsers($taskid);
            break;

        case 'delAssignUser':
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid' AND uid='$parm1'");
            $retval =  ajaxhandler_assignedUsers($taskid);
            break;

        case 'addPreNotifyVariable':
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($taskid,$parm1)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_prenotifyVariable  ) ";
                $sql .= "values ('$taskid','$parm1')";
                DB_Query($sql );
            }
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;
            
        case 'delPreNotifyVariable':
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid' AND nf_prenotifyVariable='$parm1'");
            $retval =  ajaxhandler_assignedVariables($taskid); 
            break;
            
        case 'addPostNotifyVariable':
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_postnotifyVariable'),array($taskid,$parm1)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_postnotifyVariable  ) ";
                $sql .= "values ('$taskid','$parm1')";
                DB_Query($sql );
            }
            $retval =  ajaxhandler_assignedVariables($taskid); 
            break;
            
        case 'delPostNotifyVariable':
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid' AND nf_postnotifyVariable='$parm1'");
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;
              
        case 'addReminderNotifyVariable':
            if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($taskid,$parm1)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_remindernotifyVariable  ) ";
                $sql .= "values ('$taskid','$parm1')";
                DB_Query($sql );
            }            
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;
            
        case 'delReminderNotifyVariable':
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid' AND nf_remindernotifyVariable='$parm1'");
            $retval =  ajaxhandler_assignedVariables($taskid);
            break;                                                                     
            
        case 'setReminderNotifyVariable':
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET reminderInterval='$parm1' WHERE id='$taskid'");   
            $retval =  $parm1;
            break;
            
        case 'setSubsequentReminderVariable':
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET subsequentReminderInterval='$parm1' WHERE id='$taskid'");   
            $retval =  $parm1;
            break;
          
        case 'updatePreNotifyMessage':
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET prenotify_message='$message' WHERE id='$taskid'");                 
            $retval =  '';
            break;
            
        case 'updatePostNotifyMessage':
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET postnotify_message='$message' WHERE id='$taskid'");                 
            $retval =  '';
            break;

        case 'updateReminderMessage':
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET reminder_message='$message' WHERE id='$taskid'");                 
            $retval =  '';
            break;      
            
        case 'setDynamicFormVariable' :
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set dynamicFormVariableID='$parm1' where id='$taskid'");
            $varName=DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id=" . $parm1 );
            $retval = " " . $varName . "({$parm1})";
            break;
        case 'setDynamicNameVariable' :
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set dynamicTaskNameVariableID='$parm1' where id='$taskid'");
            $varName=DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id=" . $parm1 );
            $retval = " " . $varName . "({$parm1})";
            break;
            
    }
} elseif ($op == 'setAssignmentType' AND $taskid > 0 ) {
        if ($mode == 'user' ) {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set assignedByVariable=0 where id='$taskid'");
        } elseif ($mode == 'variable') {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set assignedByVariable=1 where id='$taskid'");
        }
        //clear out any existing records
        DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='$taskid';");

        $retval = $mode;
} elseif ($op == 'setRegenerateOption' AND $taskid > 0 ) {
        if ($mode == 1 ) {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set regenerate=1 where id='$taskid'");
        } else {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set regenerate=0 where id='$taskid'");
        }
        $retval = $mode;

} elseif ($op == 'setRegenerateAllOption' AND $taskid > 0 ) {
        if ($mode == 1 ) {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set regenAllLiveTasks=1 where id='$taskid'");
        } else {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set regenAllLiveTasks=0 where id='$taskid'");  
        }
        $retval = $mode;
        
} elseif ($op == 'setDynamicForm' AND $taskid > 0 ) {
        if ($mode == 1 ) {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set isDynamicForm=1 where id='$taskid'");
        } else {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set isDynamicForm=0 where id='$taskid'");  
        }
        $retval = $mode;
} elseif ($op == 'setDynamicName' AND $taskid > 0 ) {
        if ($mode == 1 ) {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set isDynamicTaskName=1 where id='$taskid'");
        } else {
            DB_query("UPDATE {$_TABLES['nftemplatedata']} set isDynamicTaskName=0 where id='$taskid'");  
        }
        $retval = $mode;
}  elseif ($op == 'setReminderNotifyVariable' AND $taskid > 0 ) {            
    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET reminderInterval='0' WHERE id='$taskid'");  
    $retval =  $parm1;                     
}elseif ($op == 'onhold' AND $taskid > 0 ) {    
    $current=DB_getItem($_TABLES['nfqueue'],"status","id='$taskid'");
    $nf=new nexflow();
    if($current==0){
        $nf->hold_task($taskid, true);
        $retval =  'onhold';                         
    }else{
        $nf->unhold_task($taskid,true);
        $retval =  'offhold';                             
    }
    
}


 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$XML = "<result>";
$XML .= "<operation>$op</operation>";
$XML .= "<record>$taskid</record>"; 
$XML .= "<value1>$retval</value1>"; 
$XML .= "</result>";
print $XML;

?>