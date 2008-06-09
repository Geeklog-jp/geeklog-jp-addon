<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | edit_ajax_update.php                                                      |
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

require_once('../../../lib-common.php');

//
// security check
///////////////////////////////////////////////

if (!SEC_hasRights('nexflow.admin')) {
    // Someone is trying to illegally access this page
    echo COM_siteHeader();
    echo COM_startBlock($LANG_NF01['access_denied']);
    echo $LANG_NF01['access_log_in'];
    echo COM_endBlock();
    echo COM_siteFooter(true);
    exit;
}

$op = COM_applyFilter($_POST['op']);

$retval = "<result>\n";
$retval .= "<status>200</status>\n";
$retval .= "<op>$op</op>\n";

switch ($op) {
case 'save_new_task':
    $steptype = COM_applyFilter($_POST['steptype']);
    $offsetLeft = intval($_POST['offsetleft']);
    $offsetTop = intval($_POST['offsettop']);
    $templateid = intval($_POST['templateid']);

    $stepid = DB_getItem($_TABLES['nfsteptype'], 'id', "stepType LIKE '$steptype'");
    $logicalid = intval(DB_getItem($_TABLES['nftemplatedata'], 'logicalID', "nf_templateID=$templateid ORDER BY logicalID DESC LIMIT 1"));
    $logicalid++;

    $sql  = "INSERT INTO {$_TABLES['nftemplatedata']} ";
    $sql .= "(nf_templateID, taskname, offsetLeft, offsetTop, logicalID, nf_stepType) ";
    $sql .= "VALUES ($templateid, 'New Task', $offsetLeft, $offsetTop, $logicalid, $stepid)";
    $res = DB_Query($sql);

    $taskid = intval(DB_insertId());

    $retval .= "<taskid>$taskid</taskid>\n";
    break;

case 'save_task_lines':
    $type = intval ($_POST['type']);
    $from = intval ($_POST['startid']);
    if ($type == 1) {
        $toTrue = intval ($_POST['endid']);
        $toFalse = 0;
    }
    else {
        $toTrue = 0;
        $toFalse = intval ($_POST['endid']);
    }

    //get current next steps for this task
    $sql  = "INSERT INTO {$_TABLES['nftemplatedatanextstep']} ";
    $sql .= "(nf_templateDataFrom, nf_templateDataTo, nf_templateDataToFalse) ";
    $sql .= "VALUES ($from, $toTrue, $toFalse)";
    DB_query($sql);
    break;

case 'clear_task_lines':
    $taskid = intval ($_POST['taskid']);

    $sql  = "DELETE FROM {$_TABLES['nftemplatedatanextstep']} WHERE ";
    $sql .= "nf_templateDataFrom=$taskid OR nf_templateDataTo=$taskid OR nf_templateDataToFalse=$taskid";
    DB_query($sql);
    break;

case 'save_task_position':
    $taskid = intval ($_POST['taskid']);
    $offsetLeft = intval ($_POST['offsetleft']);
    $offsetTop = intval ($_POST['offsettop']);

    $sql  = "UPDATE {$_TABLES['nftemplatedata']} SET offsetLeft=$offsetLeft, offsetTop=$offsetTop WHERE id=$taskid";
    DB_query($sql);
    break;

case 'delete_task':
    $taskid = intval ($_POST['taskid']);
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID=$taskid");
    DB_query("DELETE FROM {$_TABLES['nftemplatedata']} WHERE id=$taskid");
    break;

case 'get_panel_form':
    $taskid = intval ($_POST['taskid']);

    $sql  = "SELECT a.*, b.stepType, b.is_interactiveStepType FROM {$_TABLES['nftemplatedata']} a ";
    $sql .= "LEFT JOIN {$_TABLES['nfsteptype']} b ON a.nf_stepType=b.id ";
    $sql .= "WHERE a.id=$taskid";
    $res = DB_query($sql);
    $A = DB_fetchArray($res);

    $template = strtolower(str_replace(' ', '_', $A['stepType']));

    $p = new Template($_CONF['path_layout'] . 'nexflow/admin/task_edit_templates');
    $p->set_file('edit_template', $template . '.thtml');
    if ($A['is_interactiveStepType'] == 1) {
        $p->set_file('assignment', 'assignment.thtml');
        $p->set_file('notification', 'notification.thtml');
    }

    //set language vars
    NXCOM_set_language_vars($p, $LANG_NF03);
    NXCOM_set_common_vars($p);
    $p->set_var('message_help', $LANG_NF_MESSAGE['msg1']);
    $p->set_var('LANG_help1', $LANG_NF_MESSAGE['hlp1']);
    $p->set_var('LANG_help2', $LANG_NF_MESSAGE['hlp2']);
    $p->set_var('LANG_help3', $LANG_NF_MESSAGE['hlp3']);

    //generic set_vars
    $p->set_var('taskid', $taskid);
    $p->set_var('edit_task_name', $A['taskname']);
    if ($A['isDynamicTaskName'] == 1) {
        $p->set_var('chk_isDynamicName', ' checked="checked"');
        $p->set_var('show_dynamicnamevars', '');
    }
    else {
        $p->set_var('chk_isDynamicName', '');
        $p->set_var('show_dynamicnamevars', 'none');
    }
    $res2 = DB_query("SELECT id, variableName FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID={$A['nf_templateID']}");
    $options = "<option value=\"0\">{$LANG_NF03['select_variable']}</option>\n";
    $options .= ($A['dynamicTaskNameVariableID'] == 999) ? "<option value=\"999\" selected=\"selected\">TASK_OWNER</option>\n":"<option value=\"999\">TASK_OWNER</option>\n";
    while (list ($vid, $vname) = DB_fetchArray($res2)) {
        if ($A['dynamicTaskNameVariableID'] == $vid) {
            $options .= "<option value=\"$vid\" selected=\"selected\">$vname</option>\n";
        }
        else {
            $options .= "<option value=\"$vid\">$vname</option>\n";
        }
    }
    $p->set_var('available_taskvariablesOptions', $options);
    $p->set_var('task_handler_selection', nf_makeDropDownWithSelected("id", "handler", $_TABLES['nfhandlers'],$A['nf_handlerId']) );
    if ($A['regenerate'] == 1) {
        $p->set_var('chk_regenerate', ' checked="checked"');
    }
    else {
        $p->set_var('chk_regenerate', '');
    }
    if ($A['regenAllLiveTasks'] == 1) {
        $p->set_var('chk_regenerateAllLive', ' checked="checked"');
    }
    else {
        $p->set_var('chk_regenerateAllLive', '');
    }
    $p->set_var('optional_parm', $A['optionalParm']);

    //custom setvars for each different task
    switch ($A['nf_stepType']) {
    case 1: //Manual Web
        $prefix = 'mw_';
        break;

    case 2: //And
        $prefix = 'and_';
        break;

    case 4: //Batch
        $prefix = 'bat_';
        break;

    case 5: //If
        $prefix = 'if_';
        $ifRes = DB_query("SELECT id, variableName FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID={$A['nf_templateID']}");
        $select = "<select name=\"if_taskVariable\">\n<option value=\"0\"></option>\n";
        $select .= ($A['argumentVariable'] == 999) ? "<option value=\"999\" selected=\"selected\">TASK_OWNER</option>\n":"<option value=\"999\">TASK_OWNER</option>\n";
        while (list ($key, $value) = DB_fetchArray($ifRes)) {
            if ($A['argumentVariable'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('if_task_variables', $select);

        $ifRes = DB_query("SELECT id, label FROM {$_TABLES['nfifprocessarguments']}");
        $select = "<select name=\"if_taskStatus\">\n<option value=\"0\"></option>\n";
        while (list ($key, $value) = DB_fetchArray($ifRes)) {
            if ($A['argumentProcess'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('if_task_option', $select);

        $ifRes = DB_query("SELECT id, operator FROM {$_TABLES['nfifoperators']}");
        $select = "<select name=\"if_operator\">\n<option value=\"0\"></option>\n";
        while (list ($key, $value) = DB_fetchArray($ifRes)) {
            $value = htmlspecialchars($value);
            if ($A['operator'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('if_task_operator', $select);

        $p->set_var('if_option_value', $A['ifValue']);
        break;

    case 6: //batch function
        $prefix = 'bf_';

        $p->set_var('function_value', $A['function']);
        break;

    case 7: //interactive function
        $prefix = 'int_';

        $p->set_var('function_value', $A['function']);
        break;

    case 8: //nexform
        $prefix = 'nfm_';

        $ifRes = DB_query("SELECT id, name FROM {$_TABLES['formDefinitions']}");
        $select = "<select name=\"formid\">\n<option value=\"0\">{$LANG_NF03['choose_form']}</option>\n";
        while (list ($key, $value) = DB_fetchArray($ifRes)) {
            $value = htmlspecialchars($value);
            if ($A['formid'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('selectForm', $select);
        break;

    case 11: //set process variable
        $prefix = 'spv_';

        $spvRes = DB_query("SELECT id, variableName FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID={$A['nf_templateID']}");
        $select = "<select name=\"varToSet\">\n<option value=\"0\"></option>\n";
        $select .= ($A['argumentVariable'] == 999) ? "<option value=\"999\" selected=\"selected\">TASK_OWNER</option>\n":"<option value=\"999\">TASK_OWNER</option>\n";
        while (list ($key, $value) = DB_fetchArray($spvRes)) {
            if ($A['varToSet'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('variableToSet', $select);

        $spvRes = DB_query("SELECT id, name FROM {$_TABLES['formDefinitions']}");
        $select = "<select id=\"formValue\" name=\"formValue\" onChange=\"updateFieldList(this.value, 0);\">\n<option value=\"0\">{$LANG_NF03['choose_form']}</option>\n";
        while (list ($key, $value) = DB_fetchArray($spvRes)) {
            if ($A['formid'] == $key) {
                $select .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
            }
            else {
                $select .= "<option value=\"$key\">$value</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('selectForm', $select);

        $spvRes = DB_query("SELECT id, formid, label, field_name FROM {$_TABLES['formFields']}");
        $select = "<select id=\"fieldValueCpy\" name=\"fieldValueCpy\">\n<option value=\"0\">{$LANG_NF03['choose_field']}</option>\n";
        while (list ($key, $fid, $label, $value) = DB_fetchArray($spvRes)) {
            if ($label == '') {
                $label = $value;
            }
            if ($A['fieldid'] == $key) {
                $select .= "<option value=\"$key\" class=\"nfForm$fid\" selected=\"selected\">$label</option>\n";
            }
            else {
                $select .= "<option value=\"$key\" class=\"nfForm$fid\">$label</option>\n";
            }
        }
        $select .= "</select>\n";
        $p->set_var('selectFieldCpy', $select);

        $p->set_var('incValue', ($A['incValue'] == 0) ? '':$A['incValue']);
        $p->set_var('varValue', $A['varValue']);
        break;
    }

    $p->set_var('prefix', $prefix);

    if ($A['is_interactiveStepType'] == 1) {
        $p->set_var('prenotify_message', $A['prenotify_message']);
        $p->set_var('postnotify_message', $A['postnotify_message']);
        $p->set_var('reminder_message', $A['reminder_message']);
        $p->set_var('numReminders', $A['numReminders']);

        //build the option lists for reminders
        $options = '';
        for ($i = 0; $i <= 31; $i++ ) {
            if ($A['reminderInterval'] == $i) {
                $options .= "<option value=\"$i\" selected=\"selected\">$i</option>";
            } else {
                $options .= "<option value=\"$i\">$i</option>";
            }
        }
        $p->set_var ('notifyIntervalOptions', $options);

        $options = '';
        for ($i = 0; $i <= 31; $i++ ) {
            if ($A['subsequentReminderInterval'] == $i) {
                $options .= "<option value=\"$i\" selected=\"selected\">$i</option>";
            } else {
                $options .= "<option value=\"$i\">$i</option>";
            }
        }
        $p->set_var ('subsequentIntervalOptions', $options);

        $res2 = DB_query("SELECT id, variableName FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID={$A['nf_templateID']}");
        $options = "<option value=\"0\">{$LANG_NF03['no_escalation']}</option>\n<option value=\"999\">TASK_OWNER</option>\n";
        while (list ($vid, $vname) = DB_fetchArray($res2)) {
            if ($A['escalateVariableID'] == $vid) {
                $options .= "<option value=\"$vid\" selected=\"selected\">$vname</option>\n";
            }
            else {
                $options .= "<option value=\"$vid\">$vname</option>\n";
            }
        }
        $p->set_var('esc_user_options', $options);

        $sql  = "SELECT * FROM {$_TABLES['nftemplateassignment']} WHERE nf_templateDataID=$taskid";
        $res = DB_query($sql);

        $arrs['availableUserOptions'] = array();
        $arrs['assignedUserOptions'] = array();
        $arrs['availableVariableOptions'] = array();
        $arrs['assignedVariableOptions'] = array();
        $arrs['availablePrenotifyOptions'] = array();
        $arrs['availablePostnotifyOptions'] = array();
        $arrs['availableReminderOptions'] = array();
        $arrs['assignedPrenotifyOptions'] = array();
        $arrs['assignedPostnotifyOptions'] = array();
        $arrs['assignedReminderOptions'] = array();

        $assignedByVariable = DB_getItem($_TABLES['nftemplatedata'], 'assignedByVariable', "id=$taskid");
        $prenotifyFlag = 0;
        $postnotifyFlag = 0;
        $reminderFlag = 0;
        while ($B = DB_fetchArray($res)) {
            if ($A['assignedByVariable'] == 1) {
                if ($B['nf_processVariable'] != 0) {
                    $arrs['assignedVariableOptions'][$B['nf_processVariable']] = ($B['nf_processVariable'] == 999) ? 'TASK_OWNER':DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id={$B['nf_processVariable']}");
                }
            }
            else {
                if ($B['uid'] != 0) {
                    $arrs['assignedUserOptions'][$B['uid']] = COM_getDisplayName($B['uid']);
                }
            }

            if ($B['nf_prenotifyVariable'] != 0) {
                $arrs['assignedPrenotifyOptions'][$B['nf_prenotifyVariable']] = ($B['nf_prenotifyVariable'] == 999) ? 'TASK_OWNER':DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id={$B['nf_prenotifyVariable']}");
                $prenotifyFlag = 1;
            }
            if ($B['nf_postnotifyVariable'] != 0) {
                $arrs['assignedPostnotifyOptions'][$B['nf_postnotifyVariable']] = ($B['nf_postnotifyVariable'] == 999) ? 'TASK_OWNER':DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id={$B['nf_postnotifyVariable']}");
                $postnotifyFlag = 1;
            }
            if ($B['nf_remindernotifyVariable'] != 0) {
                $arrs['assignedReminderOptions'][$B['nf_remindernotifyVariable']] = ($B['nf_remindernotifyVariable'] == 999) ? 'TASK_OWNER':DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id={$B['nf_remindernotifyVariable']}");
                $reminderFlag = 1;
            }
        }

        $res = DB_query("SELECT uid FROM {$_TABLES['users']} WHERE uid!=1;");
        while (list ($uid) = DB_fetchArray($res)) {
            if ($arrs['assignedUserOptions'][$uid] == '') {
                $arrs['availableUserOptions'][$uid] = COM_getDisplayName($uid);
            }
        }

        if (!array_key_exists(999, $arrs['assignedVariableOptions'])) {
            $arrs['availableVariableOptions'][999] = 'TASK_OWNER';
        }
        if (!array_key_exists(999, $arrs['assignedPrenotifyOptions'])) {
            $arrs['availablePrenotifyOptions'][999] = 'TASK_OWNER';
        }
        if (!array_key_exists(999, $arrs['assignedPostnotifyOptions'])) {
            $arrs['availablePostnotifyOptions'][999] = 'TASK_OWNER';
        }
        if (!array_key_exists(999, $arrs['assignedReminderOptions'])) {
            $arrs['availableReminderOptions'][999] = 'TASK_OWNER';
        }
        $res = DB_query("SELECT id, variableName FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID={$A['nf_templateID']}");
        while (list ($varid, $varname) = DB_fetchArray($res)) {
            if ($arrs['assignedVariableOptions'][$varid] == '') {
                $arrs['availableVariableOptions'][$varid] = $varname;
            }
            if ($arrs['assignedPrenotifyOptions'][$varid] == '') {
                $arrs['availablePrenotifyOptions'][$varid] = $varname;
            }
            if ($arrs['assignedPostnotifyOptions'][$varid] == '') {
                $arrs['availablePostnotifyOptions'][$varid] = $varname;
            }
            if ($arrs['assignedReminderOptions'][$varid] == '') {
                $arrs['availableReminderOptions'][$varid] = $varname;
            }
        }

        //now build each list with the 9 arrays we have
        foreach ($arrs as $name => $arr) {
            $optionList = '';
            foreach ($arr as $key => $value) {
                $optionList .= "<option value=\"$key\">$value</option>";
            }
            $p->set_var($name, $optionList);
        }

        $p->parse('assignment_output', 'assignment');
        $p->set_var('assignment_template', $p->get_var('assignment_output'));
        $p->parse('notification_output', 'notification');
        $p->set_var('notification_template', $p->get_var('notification_output'));
    }

    $p->parse('output', 'edit_template');
    $html = $p->finish($p->get_var('output'));
    $html = htmlspecialchars($html);

    $retval .= "<prefix>$prefix</prefix>\n";
    $retval .= "<isinteractive>{$A['is_interactiveStepType']}</isinteractive>\n";
    $retval .= "<assignedbyvar>$assignedByVariable</assignedbyvar>\n";
    $retval .= "<prenotifyflag>$prenotifyFlag</prenotifyflag>\n";
    $retval .= "<postnotifyflag>$postnotifyFlag</postnotifyflag>\n";
    $retval .= "<reminderflag>$reminderFlag</reminderflag>\n";
    $retval .= "<html>$html</html>\n";
    break;

case 'assign_by_user':
    $taskid = intval ($_POST['taskid']);

    //first clear out the exisiting assignments, but leaving the notification records
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_prenotifyVariable=0 AND nf_postnotifyVariable=0 AND nf_remindernotifyVariable=0 AND nf_templateDataID=$taskid");
    //now reset any uid/nf_processVariables that still exist to 0
    DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET uid=0, nf_processVariable=0 WHERE nf_templateDataID=$taskid");

    //now update the assignment method
    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET assignedByVariable=0 WHERE id=$taskid");

    //now we are ready to add the new ones to the database
    $assignedTo = '';
    $i = 0;
    if (array_key_exists('assignedUsers', $_POST)) {
        foreach ($_POST['assignedUsers'] as $uid) {
            $uid = intval ($uid);
            DB_query("INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, uid) VALUES ($taskid, $uid)");
            if ($assignedTo != '') {
                $assignedTo .= ', ';
            }
            $assignedTo .= COM_getDisplayName($uid);
            $i++;
        }
    }
    if ($i == 0) {  //no users assigned
        $assignedTo = $LANG_NF02['nobody_assigned'];
    }
    else {
        $assignedTo = $LANG_NF02['assigned_to'] . ': ' . $assignedTo;
    }

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>$assignedTo</retval>\n";
    break;

case 'assign_by_variable':
    $taskid = intval ($_POST['taskid']);

    //first clear out the exisiting assignments, but leaving the notification records
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE nf_prenotifyVariable=0 AND nf_postnotifyVariable=0 AND nf_remindernotifyVariable=0 AND nf_templateDataID=$taskid");
    //now reset any uid/nf_processVariables that still exist to 0
    DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET uid=0, nf_processVariable=0 WHERE nf_templateDataID=$taskid");

    //now update the assignment method
    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET assignedByVariable=1 WHERE id=$taskid");

    //now we are ready to add the new ones to the database
    $assignedTo = '';
    $i = 0;
    if (array_key_exists('assignedVariables', $_POST)) {
        foreach ($_POST['assignedVariables'] as $vid) {
            $vid = intval ($vid);
            DB_query("INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_processVariable) VALUES ($taskid, $vid)");
            if ($assignedTo != '') {
                $assignedTo .= ', ';
            }
            $assignedTo .= ($vid == 999) ? 'TASK_OWNER':DB_getItem($_TABLES['nftemplatevariables'], 'variableName', "id=$vid");
            $i++;
        }
    }
    if ($i == 0) {  //no users assigned
        $assignedTo = $LANG_NF02['nobody_assigned'];
    }
    else {
        $assignedTo = $LANG_NF02['assigned_to'] . ': ' . $assignedTo;
    }

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>$assignedTo</retval>\n";
    break;

case 'notify_on_assign':
    $taskid = intval ($_POST['taskid']);
    $prenotifyMsg = NXCOM_filterText($_POST['prenotify_message']);

    //first clear out the exisiting assignment notifications, but leaving the other notification records
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE uid=0 AND nf_processVariable=0 AND nf_postnotifyVariable=0 AND nf_remindernotifyVariable=0 AND nf_templateDataID=$taskid");
    //now reset any nf_prenotifyVariables that still exist to 0
    DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET nf_prenotifyVariable=0 WHERE nf_templateDataID=$taskid");

    //now update prenotify message
    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET prenotify_message='$prenotifyMsg' WHERE id=$taskid");

    //now we are ready to add the new ones to the database
    if (array_key_exists('assignedVariables', $_POST)) {
        foreach ($_POST['assignedVariables'] as $vid) {
            $vid = intval ($vid);
            DB_query("INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_prenotifyVariable) VALUES ($taskid, $vid)");
        }
    }

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>0</retval>\n";
    break;

case 'notify_on_complete':
    $taskid = intval ($_POST['taskid']);
    $postnotifyMsg = NXCOM_filterText($_POST['postnotify_message']);

    //first clear out the exisiting assignment notifications, but leaving the other notification records
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE uid=0 AND nf_processVariable=0 AND nf_prenotifyVariable=0 AND nf_remindernotifyVariable=0 AND nf_templateDataID=$taskid");
    //now reset any nf_prenotifyVariables that still exist to 0
    DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET nf_postnotifyVariable=0 WHERE nf_templateDataID=$taskid");

    //now update postnotify message
    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET postnotify_message='$postnotifyMsg' WHERE id=$taskid");

    //now we are ready to add the new ones to the database
    if (array_key_exists('assignedVariables', $_POST)) {
        foreach ($_POST['assignedVariables'] as $vid) {
            $vid = intval ($vid);
            DB_query("INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_postnotifyVariable) VALUES ($taskid, $vid)");
        }
    }

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>0</retval>\n";
    break;

case 'notify_reminders':
    $taskid = intval ($_POST['taskid']);
    $reminderMsg = NXCOM_filterText($_POST['reminder_message']);
    $subsequentinterval = intval ($_POST['subsequentinterval']);
    $notifyinterval = intval ($_POST['notifyinterval']);
    $esc_user = intval ($_POST['esc_user']);
    $numReminders = intval ($_POST['numReminders']);

    //first clear out the exisiting assignment notifications, but leaving the other notification records
    DB_query("DELETE FROM {$_TABLES['nftemplateassignment']} WHERE uid=0 AND nf_processVariable=0 AND nf_prenotifyVariable=0 AND nf_postnotifyVariable=0 AND nf_templateDataID=$taskid");
    //now reset any nf_prenotifyVariables that still exist to 0
    DB_query("UPDATE {$_TABLES['nftemplateassignment']} SET nf_remindernotifyVariable=0 WHERE nf_templateDataID=$taskid");

    //now update reminder message, along with other variables
    $sql  = "UPDATE {$_TABLES['nftemplatedata']} ";
    $sql .= "SET reminder_message='$reminderMsg', reminderInterval=$notifyinterval, ";
    $sql .= "subsequentReminderInterval=$subsequentinterval, escalateVariableID=$esc_user, ";
    $sql .= "numReminders=$numReminders WHERE id=$taskid";
    DB_query($sql);

    //now we are ready to add the new ones to the database
    if (array_key_exists('assignedVariables', $_POST)) {
        foreach ($_POST['assignedVariables'] as $vid) {
            $vid = intval ($vid);
            DB_query("INSERT INTO {$_TABLES['nftemplateassignment']} (nf_templateDataID, nf_remindernotifyVariable) VALUES ($taskid, $vid)");
        }
    }

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>0</retval>\n";
    break;

case 'save_task':
    $taskid = intval ($_POST['taskid']);
    $taskname = NXCOM_filterText($_POST['taskName']);
    $regenerate = intval ($_POST['regenerate']);
    $regenAllLiveTasks = intval ($_POST['regenerateAllLive']);
    $optionalParm = NXCOM_filterText($_POST['optionalParm']);
    $isDynamicTaskName = intval ($_POST['isDynamicName']);
    $dynamicTaskNameVariableID = intval ($_POST['dynamicNameVariableSelector']);

    //get the task type
    $steptype = DB_getItem($_TABLES['nftemplatedata'], 'nf_stepType', "id=$taskid");

    //update the database with the changes
    $sql =  "UPDATE {$_TABLES['nftemplatedata']} SET ";

    //first update the general stuff
    $sql .= "taskname='$taskname', ";
    $sql .= "regenerate=$regenerate, ";
    $sql .= "regenAllLiveTasks=$regenAllLiveTasks, ";
    $sql .= "optionalParm='$optionalParm', ";
    $sql .= "isDynamicTaskName=$isDynamicTaskName, ";
    $sql .= "dynamicTaskNameVariableID=$dynamicTaskNameVariableID, ";

    //then update the specifics
    switch ($steptype) {
    case 1: //manual web
        $nf_handlerId = intval ($_POST['idhandler']);

        $sql .= "nf_handlerId=$nf_handlerId, ";
        break;

    case 2: //and
        break;

    case 4: //batch
        $nf_handlerId = intval ($_POST['idhandler']);

        $sql .= "nf_handlerId=$nf_handlerId, ";
        break;

    case 5: //if
        $argumentProcess = intval ($_POST['if_taskStatus']);
        $argumentVariable = intval ($_POST['if_taskVariable']);
        $operator = intval ($_POST['if_operator']);
        $ifValue = NXCOM_filterText($_POST['nfIfTaskArgumentValue']);

        $sql .= "argumentProcess=$argumentProcess, ";
        $sql .= "argumentVariable=$argumentVariable, ";
        $sql .= "operator=$operator, ";
        $sql .= "ifValue='$ifValue', ";
        break;

    case 6: //batch function
    case 7: //interative function
        $function = NXCOM_filterText($_POST['task_function']);

        $sql .= "function='$function', ";
        break;

    case 8: //nexform
        $formid = intval ($_POST['formid']);

        $sql .= "formid=$formid, ";
        break;

    case 11: //set process variable
        $formid = intval ($_POST['formValue']);
        $fieldid = intval ($_POST['fieldValue']);
        $varValue = NXCOM_filterText($_POST['varValue']);
        $varToSet = intval ($_POST['varToSet']);
        $incValue = intval ($_POST['incValue']);

        $sql .= "formid=$formid, ";
        $sql .= "fieldid=$fieldid, ";
        $sql .= "varValue='$varValue', ";
        $sql .= "varToSet=$varToSet, ";
        $sql .= "incValue=$incValue, ";
        break;
    }

    //and finish it off with the conditions
    $sql .= "last_updated=NOW() WHERE id=$taskid";
    DB_query($sql);

    $retval .= "<taskid>$taskid</taskid>\n";
    $retval .= "<retval>$taskname</retval>\n";
    break;
}

$retval .= "</result>";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo $retval;
?>
