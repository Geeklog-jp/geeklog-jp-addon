<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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

    function nf_deleteTemplate($templateID )
    {
        global $_TABLES;
        $templateID = NXCOM_filterInt($templateID);
        $sql = "SELECT * FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='{$templateID}'";
        $result = DB_Query($sql );

        while ($A = DB_fetchArray($result)) {
            DB_query("DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom = '{$A[0]}'");
            DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID='{$A[0]}'");
            DB_query("DELETE FROM {$_TABLES['nftemplatedata']} WHERE id='{$A[0]}'");
        }
        DB_query("DELETE FROM {$_TABLES['nftemplate']} WHERE id='{$templateID}'");
    }


    function nf_copyTemplate($templateID)  {
        global $_TABLES;
        $templateID = NXCOM_filterInt($templateID);
        $sql = "SELECT * FROM {$_TABLES['nftemplate']} WHERE id='$templateID'";
        $A = DB_fetchArray(DB_Query($sql));
        DB_query("INSERT INTO {$_TABLES['nftemplate']}(templateName,useProject,AppGroup) values('{$A['templateName']}-copy',{$A['AppGroup']},{$A['useProject']})");
        $newTemplateId = DB_insertID();

        // Need to copy all the Template Data records
        $sql = "SELECT * FROM {$_TABLES['nftemplatedata']} WHERE nf_templateID='$templateID'";
        $q1 = DB_Query($sql);

        $fields = 'nf_templateID, logicalID, nf_stepType, nf_handlerId, firstTask, taskname, ';
        $fields .= 'assignedByVariable, argumentVariable, argumentProcess, operator, ifValue, regenerate,' ;
        $fields .= 'regenAllLiveTasks, isDynamicForm, dynamicFormVariableID, isDynamicTaskName, dynamicTaskNameVariableID, ';
        $fields .= 'function, formid, optionalParm ';

        /* Need to update fields that are linked to template variables */
        while ($A = DB_fetchArray($q1)) {
            $values  = "{$newTemplateId}, {$A['logicalID']}, {$A['nf_stepType']}, ";
            $values .= "{$A['nf_handlerId']}, {$A['firstTask']}, '{$A['taskname']}', {$A['assignedByVariable']}, ";
            $values .= "'{$A['argumentVariable']}', '{$A['argumentProcess']}', '{$A['operator']}', ";
            $values .= "'{$A['ifValue']}', {$A['regenerate']}, {$A['regenAllLiveTasks']},{$A['isDynamicForm']},";
            $values .= "{$A['dynamicFormVariableID']}, {$A['isDynamicTaskName']}, {$A['dynamicTaskNameVariableID']},";
            $values .= "'{$A['function']}', {$A['formid']}, '{$A['optionalParm']}'";
            $sql = "INSERT INTO {$_TABLES['nftemplatedata']} ($fields) VALUES ($values) ";
            DB_Query($sql );
            $newTemplateDataId = DB_insertID();

                // Need to copy all the Template DataNextStep record matching the source templateData record
                $sql = "SELECT * FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom='{$A['id']}'";
                $q2 = DB_Query($sql);
                if (DB_numRows($q2) > 0) {
                    $B = DB_fetchArray($q2);
                    if($newTemplateDataId != 0){
                        $sql = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} (nf_templateDataFrom) VALUES ('$newTemplateDataId') ";
                        DB_query($sql);
                    }
                }
        }

        // Now we can cycle thru these tasks again and update the values for the fields linking the tasks
        $sql = "SELECT a.id, a.logicalID, b.nf_templateDataTo, b.nf_templateDataToFalse FROM {$_TABLES['nftemplatedata']} a ";
        $sql .= "INNER JOIN {$_TABLES['nftemplatedatanextstep']} b on a.id = b.nf_templateDataFrom ";
        $sql .= "WHERE nf_templateID='$templateID'";
        $q2 = DB_Query($sql);
        $test = DB_numRows($q2);
        while (list ($sourceTaskid, $sourceLogicalID, $sourceToTaskid, $sourceFalseTaskid) = DB_fetchArray($q2)) {
            // Get the ID of the matching source logical task in the new template
            $newFromTaskid = DB_getItem($_TABLES['nftemplatedata'],'id', "logicalID=$sourceLogicalID AND nf_templateID=$newTemplateId");

            // Get the ID of the matching source logical task in the new template
            $targetLogicalId = nfidtolid($sourceToTaskid);
            $test = nfidtolid($sourceToTaskid);
            // DB_getItem($_TABLES['nftemplatedata'],'logicalID', "id=$sourceToTaskid");
            $newToTaskid = DB_getItem($_TABLES['nftemplatedata'],'id', "logicalID=$targetLogicalId AND nf_templateID=$newTemplateId");

            // Update the record field nf_templateTo for the new template
            DB_query("UPDATE {$_TABLES['nftemplatedatanextstep']} SET nf_templateDataTo=$newToTaskid WHERE nf_templateDataFrom=$newFromTaskid");

            if ($sourceFalseTaskid > 0) {
                $targetFalseLogicalId = nfidtolid($sourceFalseTaskid);
                //DB_getItem($_TABLES['nftemplatedata'],'logicalID', "id=$sourceFalseTaskid");
                $newFalseTaskid = DB_getItem($_TABLES['nftemplatedata'],'id', "logicalID=$targetFalseLogicalId AND nf_templateID=$newTemplateId");
                DB_query("UPDATE {$_TABLES['nftemplatedatanextstep']} SET nf_templateDataToFalse=$newFalseTaskid WHERE nf_templateDataFrom=$newFromTaskid");
            }
        }

        // Need to now copy and update the template Variable records
        $query = DB_query("SELECT id, nf_variableTypeID, variableName, variableValue FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID=$templateID");
        $templateVariableMap = array();
        while (list($variableID, $variableTypeID, $variableName,$variableValue) = DB_fetchArray($query)) {
            $sql = "INSERT INTO {$_TABLES['nftemplatevariables']} (nf_templateID,nf_variableTypeID,variableName,variableValue) ";
            $sql .= "VALUES ($newTemplateId, $variableTypeID,'{$variableName}','$variableValue')";
            DB_query($sql);
            $newVariableID = DB_insertID();
            $sql  = "UPDATE {$_TABLES['nftemplatedata']} SET dynamicTaskNameVariableID=$newVariableID ";
            $sql .= "WHERE id=$newFromTaskid AND dynamicTaskNameVariableID=$variableID";
            DB_query($sql);

            // Create a mapping of the original to new template variable ID's so we can update the template task records
            $templateVariableMap[$variableID] = $newVariableID;
        }

        // Retrieve the assignment records for the interactive tasks and insert / update for the copied template
        $sql  = "SELECT a.id,a.logicalID,b.uid,b.gid,b.nf_processVariable,b.nf_prenotifyVariable,b.nf_postnotifyVariable,b.nf_remindernotifyVariable ";
        $sql .= "FROM {$_TABLES['nftemplatedata']} a INNER JOIN {$_TABLES['nftemplateassignment']} b on a.id=b.nf_templateDataID ";
        $sql .= "WHERE a.nf_templateID = $templateID ";
        $query = DB_query($sql);
        $fields = 'nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable';
        while ($A = DB_fetchArray($query)) {
            // Determine the matching taskid in the new template
            $newTaskid = DB_getItem($_TABLES['nftemplatedata'],'id', "logicalID={$A['logicalID']} AND nf_templateID=$newTemplateId");

            $sql  = "INSERT INTO {$_TABLES['nftemplateassignment']} ($fields) VALUES ";
            $sql .= "( $newTaskid,{$A['uid']},{$A['gid']},{$A['nf_processVariable']},{$A['nf_prenotifyVariable']},";
            $sql .= "{$A['nf_postnotifyVariable']},{$A['nf_remindernotifyVariable']} )";
            DB_query($sql);
            $newAssignmentRecord = DB_insertID();

            // Update field with matching template variable ID
            if ($A['nf_processVariable'] > 0) {
                $variableID = $templateVariableMap[$A['nf_processVariable']];
                DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET nf_processVariable=$variableID WHERE id=$newAssignmentRecord");
            }
        }



    }

    // Function to handle the proper creation of a new template
    // Insert the new name into the template table, Isert a first task into the template data table
    // and Insert the first nextstep value to allow for a first shell task to edit.
    function nf_createNewTemplate($templateName )
    {
        global $_TABLES;
        if ($templateName != null ) {
            $sql = "INSERT INTO {$_TABLES['nftemplate']} (templateName) VALUES ('{$templateName}')";
            $result = DB_Query($sql );

            $templateID = DB_insertID(); //get the last ID from the insert.  this is the new template ID

            $sql = "INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, taskname, offsetLeft, offsetTop, logicalID, nf_stepType, firstTask) VALUES ($templateID, 'Start', 50, 300, 1, 9, 1)";
            $result = DB_Query($sql );

            $sql = "INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, taskname, offsetLeft, offsetTop, logicalID, nf_stepType) VALUES ($templateID, 'End', 350, 300, 2, 10)";
            $result = DB_Query($sql );

            // Create default template variable INITIATOR and TASKOWNER and PID
            $sql = "INSERT INTO {$_TABLES['nftemplatevariables']} (nf_templateID,variableName) VALUES ($templateID,'INITIATOR')";
            $result = DB_Query($sql);
            $sql = "INSERT INTO {$_TABLES['nftemplatevariables']} (nf_templateID,variableName) VALUES ($templateID,'PID')";
            $result=DB_Query($sql);
            $sql = "INSERT INTO {$_TABLES['nftemplatevariables']} (nf_templateID,variableName) VALUES ($templateID,'TASKOWNER')";
            $result=DB_Query($sql);
        }
    }


?>