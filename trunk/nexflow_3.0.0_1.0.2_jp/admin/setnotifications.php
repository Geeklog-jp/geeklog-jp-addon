<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | setnotifications.php                                                      |
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

// This script will add notification records using the process variable 'TASK-OWNER' which has the reserved value of 999

/* Setup Configuration Variables for selected interactive tasks  */

// Define specific templates if you do not want all templates effected
// Example: array(21,28,32), array(21) or array() for all templates
$wftemplates = array(21);

$deleteDefaultNotifications = true; // Set to true if all current task assignment notifications should be deleted
$deleteDefaultReminders = true;     // Set to true if all current default reminders should be deleted
$setDefaultAssignment = true;       // Set to true if default task assignment notification should be set 
$setDefaultReminder = false;         // Set to true if default reminder should be set for selected interactive tasks
$reminderInterval = 3;              // Number of days between reminder notifications

$setEscalation = true;              // Set to true if you want to set or reset the escalation settings 
$escalateAfterReminders = 0;        // Set to number of reminder Notification > 0 if you want to set default escalation

// Assumes the same Template Variable is available for all templates.
// Need to lookup variable ID as thats used to identify the user to escalate to
$escalateVariable = 'PD_Coordinator'; 

/* END of Setup Options */

echo COM_siteHeader();
if (!SEC_inGroup('Root')) {
    echo COM_startBlock('Invalid Access');
    echo "<br><blockquote>You don't have access to execute this program!</blockquote>";
} else {
    echo COM_StartBlock("Initialization Script");
    if ($_GET['op'] != 'initialize') {
        echo "<br><blockquote>This script will set default task notifications for all interactive tasks defined";
        echo "<p>Do you want to proceed? <a href=\"{$_SERVER['PHP_SELF']}?op=initialize\">Yes</a>&nbsp;&nbsp;<a href=\"{$_CONF['site_url']}\">No</a></p>";
        echo "</blockquote>";
    } else {
        echo "<br><h3>Update Begin .... </h3>";
        if ($deleteDefaultNotifications) DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_prenotifyVariable = 999");
        if ($deleteDefaultReminders) DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_remindernotifyVariable = 999");

        // Retrieve all interactive tasks - for all requested templates
        $sql  = "SELECT id,nf_templateID FROM {$_TABLES['nftemplatedata']} ";
        $sql .= "WHERE (nf_stepType=1 OR nf_stepType=7 OR nf_stepType=8) ";     // Interactive Task Types
        if (count($wftemplates) > 0) {
            $templates = implode(',',$wftemplates);
            $sql .= "AND nf_templateID in ($templates) ";    
        }
        $sql .= "ORDER BY id";
        $q1 = DB_query($sql);
       
        while ($A = DB_fetchArray($q1)) {   // Foreach interactive task 
            // Set a notification record for variable 'TASK OWNER' - using reserver variableID '999'
            // If multiple users are assigned this task, then they will all be notified.
            // Will work if task assignment is done via variable or by user
            if ($setDefaultAssignment AND 
                    DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_prenotifyVariable'),array($A['id'],999)) == 0) {
                $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_prenotifyVariable) ";
                $sql .= "values ('{$A['id']}','999')";
                DB_query($sql);
            }
            if ($setDefaultReminder and $reminderInterval > 0) {
                if (DB_count($_TABLES['nftemplateassignment'], array('nf_templateDataID', 'nf_remindernotifyVariable'),array($A['id'],999)) == 0) {
                    $sql = "INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_remindernotifyVariable) ";
                    $sql .= "values ('{$A['id']}','999')";
                    DB_query($sql);
                    
                    // Set Reminders for Task
                    $sql  = "UPDATE {$_TABLES['nftemplatedata']} SET reminderInterval=$reminderInterval WHERE id={$A['id']}";
                    DB_query ($sql);

                    // Get variable ID for this template matching the user to escalate too
                    if ($setEscalation) {
                        $escalateVariableID = DB_getItem($_TABLES['nftemplatevariables'],'id',"nf_templateID={$A['nf_templateID']} AND variableName='$escalateVariable'");
                        if ($escalateVariableID != NULL AND $escalateVariableID > 0) {
                            $sql  = "UPDATE {$_TABLES['nftemplatedata']} SET numReminders=$escalateAfterReminders,";
                            $sql .= "escalateVariableID=$escalateVariableID WHERE id={$A['id']}";
                            DB_query ($sql);
                        } else {
                            $sql  = "UPDATE {$_TABLES['nftemplatedata']} SET numReminders=$escalateAfterReminders,";
                            $sql .= "escalateVariableID=0 WHERE id={$A['id']}";
                            DB_query ($sql);
                        }
                    }
                }
            }
        }       
        echo "<br><h3>Default notifications enabled .... </h3>";

    }
}

echo COM_endblock();
echo COM_siteFooter();

?>