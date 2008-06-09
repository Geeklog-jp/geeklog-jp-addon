<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | edit.php                                                                  |
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
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

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

//
// function definitions
///////////////////////////////////////////////

function display_main($workflow_id) {
    global $_TABLES, $_CONF, $NF_CONF, $LANG_NF02;

    $p = new Template($_CONF['path_layout'] . 'nexflow/admin');
    $p->set_file('workflow_view', 'workflow_view.thtml');
    $p->set_file('tasktemplate', 'task_template.thtml');

    //set language vars
    NXCOM_set_language_vars($p, $LANG_NF02);
    NXCOM_set_common_vars($p);

    $p->set_var('workflow_id', $workflow_id);

    $p->set_var('task_title', '%s');
    $p->set_var('task_id', '%d');
    $p->set_var('css_class', '%c');
    $p->set_var('task_info', '%i');
    $p->set_var('task_type', '%t');
    $p->set_var('task_type_underscored', '%u');

    $p->parse('task_template_output', 'tasktemplate');
    $p->set_var('task_template', $p->get_var('task_template_output'));

    $sql = "SELECT a.id, a.taskname, a.offsetLeft, a.offsetTop, b.stepType, b.is_interactiveStepType, a.assignedByVariable ";
    $sql .= "FROM {$_TABLES['nftemplatedata']} a ";
    $sql .= "LEFT JOIN {$_TABLES['nfsteptype']} b ON a.nf_stepType=b.id ";
    $sql .= "WHERE a.nf_templateID={$workflow_id} ORDER BY logicalID ASC";
    $res = DB_query($sql);

    $i = 0;
    $j = 0;
    $k = 0;
    $x = 50;
    $y = 0;
    $additional_js1 = '';
    $additional_js2 = '';
    while (list ($task_id, $task_title, $offsetLeft, $offsetTop, $step_type, $is_interactive, $assignedByVar) = DB_fetchArray($res)) {
        if (strlen($task_title) > 25) {
            $task_title = substr($task_title, 0, 25) . '...';
        }
        $p->set_var('task_title', $task_title);
        $p->set_var('task_id', $task_id);
        $fmtd_steptype = ucwords($step_type);
        $task_info = "{$LANG_NF02['task_type']}: $fmtd_steptype";
        $p->set_var('task_type', $fmtd_steptype);
        $p->set_var('task_type_underscored', str_replace(' ', '_', $fmtd_steptype));
        if ($is_interactive == 1) {
            //find out who is assigned:
            $sql2  = "SELECT b.uid FROM {$_TABLES['nftemplateassignment']} a, {$_TABLES['users']} b ";
            $sql2 .= "WHERE a.uid=b.uid AND a.nf_templateDataId=$task_id";

            $assignedUsers = DB_query($sql2);
            $numusers = DB_numRows($assignedUsers );
            if ($assignedByVar == 0 AND $numusers > 0 ) {
                $names = array();
                for($userCntr = 0;$userCntr < $numusers;$userCntr++ ) {
                    $rec = DB_fetchArray($assignedUsers);
                    $names[] = COM_getDisplayName($rec['uid']);
                }

                $task_assigned = $LANG_NF02['assigned_to'] . ': '. implode(', ',$names);
            } else {
                $variables = array();
                if ($assignedByVar == 1) {
                    $asql  = "SELECT variableName FROM {$_TABLES['nftemplateassignment']} a ";
                    $asql .= "INNER JOIN {$_TABLES['nftemplatevariables']} b ON a.nf_processVariable=b.id ";
                    $asql .= "WHERE a.nf_templateDataID=$task_id ";
                    $aquery = DB_query($asql);
                    while (list ($assignmentVariableName) = DB_fetchArray($aquery)) {
                        $variables[] = $assignmentVariableName;
                    }
                }
                if (count($variables) > 0) {
                    $task_assigned = $LANG_NF02['assigned_to'] . ': '. implode(', ',$variables);
                } else {
                    $task_assigned = $LANG_NF02['nobody_assigned'];
                }
            }
            $p->set_var('task_assignment', $task_assigned);
            $p->set_var('css_class', 'nf_interactive');
        }
        else {
            if ($step_type == 'If') {
                $p->set_var('css_class', 'nf_if');
            }
            else if ($step_type == 'Start') {
                $p->set_var('css_class', 'nf_start');
            }
            else if ($step_type == 'End') {
                $p->set_var('css_class', 'nf_end');
            }
            else {
                $p->set_var('css_class', 'nf_noninteractive');
            }
            $p->set_var('task_assignment', '');
        }

        $p->set_var('offsetLeft', $offsetLeft);
        $p->set_var('offsetTop', $offsetTop);
        $additional_js1 .= "existing_tasks[{$i}] = ['task{$task_id}', $offsetLeft, $offsetTop];\n";

        $res2 = DB_query("SELECT nf_TemplateDataTo, nf_templateDataToFalse FROM {$_TABLES['nftemplatedatanextstep']} WHERE nf_templateDataFrom=$task_id");
        while (list ($to, $toFalse) = DB_fetchArray($res2)) {
            $to = intval ($to);
            $toFalse = intval ($toFalse);
            if ($to != 0) {
                $additional_js2 .= "line_ids[{$j}] = ['task{$task_id}', 'task{$to}', true];\n";
                $j++;
            }
            if ($toFalse != 0) {
                $additional_js2 .= "line_ids[{$j}] = ['task{$task_id}', 'task{$toFalse}', false];\n";
                $j++;
            }
        }
        $p->parse('existing_tasks_output', 'tasktemplate', true);
        $i++;
    }
    $p->set_var('existing_tasks', $p->get_var('existing_tasks_output'));

    $additional_js3 = '';
    $i = 0;
    $res = DB_query("SELECT id, stepType, is_interactiveStepType FROM {$_TABLES['nfsteptype']} WHERE stepType!='Start' AND stepType!='End'");
    while (list ($step_id, $step_type, $interactive) = DB_fetchArray($res)) {
        $step_type = ucwords($step_type);
        $p->set_var('task_type', $step_type);
        $p->set_var('task_type_underscored', str_replace(' ', '_', $step_type));
        $additional_js3 .= "steptypes[{$i}] = [$step_id, '$step_type', $interactive];\n";
        $i++;
    }

    $p->set_var('additional_js', $additional_js1 . "\n" . $additional_js2 . "\n" . $additional_js3);

    $p->parse('output', 'workflow_view');
    return $p->finish($p->get_var('output'));
}

function save_workflow() {
    global $_TABLES;

    foreach ($_POST['task_id'] as $i => $task_id) {
        $task_id = intval ($_POST['task_id'][$i]);
        $offsetLeft = intval ($_POST['task_left'][$i]);
        $offsetTop = intval ($_POST['task_top'][$i]);

        if ($task_id > 0) { //new tasks have a negative id... we will be storing them differently
            DB_query("UPDATE {$_TABLES['nftemplatedata']} SET offsetLeft=$offsetLeft, offsetTop=$offsetTop WHERE id=$task_id");
        }
    }
}

//
// main code
///////////////////////////////////////////////

$op = COM_applyFilter($_REQUEST['op']);
$workflow_id = intval ($_REQUEST['workflow_id']);

$navbar = new navbar();
$navbar->add_menuitem($LANG_NF02['my_tasks'], $_CONF['site_url'] . '/nexflow/index.php');
$navbar->add_menuitem($LANG_NF02['view_templates'], $_CONF['site_admin_url'] . '/plugins/nexflow/templates.php');
$navbar->add_menuitem($LANG_NF02['view_workflow'], $_CONF['site_admin_url'] . '/plugins/nexflow/edit.php?workflow_id=' . $workflow_id);
$navbar->set_selected($LANG_NF02['view_workflow']);

switch ($op) {
case 'save_workflow':
    save_workflow();
    $display = display_main($workflow_id);
    break;

default:
    $display = display_main($workflow_id);
    break;
}

echo COM_siteHeader('none');
//echo COM_siteFooter();
echo $navbar->generate();
echo $display;

?>