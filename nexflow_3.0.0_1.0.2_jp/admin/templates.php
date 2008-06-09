<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | templates.php                                                             |
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
require('library.php');

// Only let users with nexflow.edit rights to access this page
if (!SEC_hasRights('nexflow.edit')) { 
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_NF00['access_denied']);
    $display .= $LANG_NF00['admin_access_error'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
} 

require_once ($_CONF['path'] . 'plugins/nexflow/config.php');
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

echo COM_siteHeader('menu');

$navbar = new navbar;
$navbar->add_menuitem('My Tasks',$CONF_NF['TaskConsole_URL'] );
if(SEC_hasRights('nexflow.admin')) {
    $navbar->add_menuitem('View Templates',$_CONF['site_admin_url'] .'/plugins/nexflow/templates.php');
    $navbar->set_selected('View Templates');
    $navbar->add_menuitem('Edit Handlers',$_CONF['site_admin_url'] .'/plugins/nexflow/handlers.php');
    $navbar->add_menuitem('Import Template',$_CONF['site_admin_url'] .'/plugins/nexflow/import.php');
}
echo $navbar->generate();

$userid = $_USER['uid']; 
$operation = COM_applyFilter($_GET['operation'],false);
$templateID = COM_applyFilter($_GET['templateID'],true);

if($templateID == 0) {
    $templateID = NULL;
}

$nfclass= new nexflow();

$errorMessage='';

switch(strtolower($operation)) {

    case 'save':
        if (!get_magic_quotes_gpc()) {
            $templateName = addslashes(COM_applyFilter($_GET['templateName']));     
        } else {
            $templateName = COM_applyFilter($_GET['templateName']);
        }
        if( $templateID != NULL ) {
            DB_query("UPDATE {$_TABLES['nftemplate']} SET templateName='{$templateName}' WHERE id='{$templateID}'");
        } else {
            nf_createNewTemplate($templateName);
        }
        break;

    case 'copy':
        nf_copyTemplate($templateID);
        break;

    case 'addappgroup':
        if (!get_magic_quotes_gpc()) {
            $appGroupName = addslashes(COM_applyFilter($_GET['appGroupName']));
        } else {
            $appGroupName = COM_applyFilter($_GET['appGroupName']);
        }
        if($appGroupName != ''){
            DB_query("INSERT into {$_TABLES['nfappgroups']} (AppGroup) values ('{$appGroupName}')");
        }
        break;

    case 'editappgroup':
        //going to delete the app group if possible
        $varID = COM_applyFilter($_GET['deleteAppGroup'],true);
        $res = DB_query("SELECT id from {$_TABLES['nftemplate']} where AppGroup='{$varID}'");
        if(DB_numRows($res) > 0){
            //warn the user that this can't be done.
            $errorMessage = $LANG_NF00['AppGroupError1'];
        } else {
            $errorMessage = '';
            DB_query("DELETE FROM {$_TABLES['nfappgroups']} where id='{$varID}'");
        }
        break;

    case 'delete':
        if( $templateID != NULL ) {
            nf_deleteTemplate($templateID);
        }
        break;
}

$actionurl = $_CONF['site_admin_url'] . '/plutins/nexflow/templates.php?operation=new';
$imgset = $_CONF['site_admin_url'] .'/plugins/nexflow/images';

$p = new Template($_CONF['path_layout'] . 'nexflow/admin');
$p->set_file (array ('page'=>'templates.thtml',
    'records'      => 'template_detail.thtml',
    'variables'    => 'template_variables.thtml',
    'variable_rec' => 'template_variable_record.thtml'));

$tquery = DB_query("SELECT id,templateName FROM {$_TABLES["nftemplate"]} ORDER BY id");

$p->set_var('num_records',DB_numRows($tquery));
$p->set_var('public_url',$_CONF['site_admin_url'] .'/plugins/nexflow');

/* Display Template Records and Template Variables - in hidden div's */
$cntr = 0;
while (list ($templateId, $templateName) = DB_fetchArray($tquery)) {

    $del_template_url  = $_CONF['site_admin_url'];
    $del_template_url .= '/plugins/nexflow/templates.php?operation=delete&templateID='.$templateId;
    $del_template_icon = $_CONF['layout_url'] .'/nexflow/images/admin/delete.gif';
        
    $edit_task_url  = $_CONF['site_admin_url'] .'/plugins/nexflow/edit.php?workflow_id='.$templateId;
    $edit_task_icon = $_CONF['layout_url'] .'/nexflow/images/admin/edit_tasks.gif';
    $edit_template_icon = $_CONF['layout_url'] .'/nexflow/images/admin/edit_properties.gif';

    $copy_template_url  = $_CONF['site_admin_url'];
    $copy_template_url .= '/plugins/nexflow/templates.php?operation=copy&templateID='.$templateId;
    $copy_template_icon = $_CONF['layout_url'] .'/nexflow/images/admin/copy.gif';

    $export_template_icon = $_CONF['layout_url'] .'/nexflow/images/admin/export.gif';
    
    $editname_link = "[&nbsp;<a href=\"#\" onClick='ajaxUpdateTemplateVar(\"editTemplateName\",{$templateId},{$cntr});'\">Edit</a>&nbsp;]";

    $useProject = DB_getItem($_TABLES['nftemplate'], 'useProject', "id='{$templateId}'");
    if(!empty($useProject)) {
        $useProject_check = 'CHECKED';
    } else {
        $useProject_check = '';
    }

    if($errorMessage != ''){
        $p->set_var('errorMessage',$errorMessage);
        $p->set_var('showErrorMessage','');

    } else {
        $p->set_var('showErrorMessage','none');
    }
    $p->set_var('cntr',$cntr);
    $p->set_var('template_id',$templateId);
    $p->set_var('template_name',$templateName);
    $p->set_var('edit_task_url',$edit_task_url);
    $p->set_var('edit_task_icon',$edit_task_icon);
    $p->set_var('edit_template_icon',$edit_template_icon);
    $p->set_var('del_template_url',$del_template_url);
    $p->set_var('del_template_icon',$del_template_icon);
    $p->set_var('copy_template_icon',$copy_template_icon);
    $p->set_var('copy_template_url',$copy_template_url);
    $p->set_var('editNeedPrj_check',$useProject_check);
    $p->set_var('export_template_icon',$export_template_icon);
    
    $thisAppGroupID = DB_getItem($_TABLES['nftemplate'],'AppGroup',"id='{$templateId}'");
    $appGroupDDL = nf_makeDropDownWithSelected('id', 'AppGroup', $_TABLES['nfappgroups'], $thisAppGroupID,'',1);
    $p->set_var('editUseApp',$appGroupDDL);

    $appGroupDDL = nf_makeDropDownWithSelected('id', 'AppGroup', $_TABLES['nfappgroups'], '','',1);
    $p->set_var('deleteAppGroup',$appGroupDDL);

    //$p->set_var('copy_template_url',$copy_template_url);
    //$p->set_var('copy_template_icon',$copy_template_icon);

    $p->set_var('editname_link',$editname_link);
    $p->set_var('LANG_DELCONFIRM', 'Are you sure you want to delete this definition?');

    $sql = "SELECT * FROM {$_TABLES['nftemplatevariables']} WHERE nf_templateID='{$templateId}' ORDER BY id";
    $query = DB_Query($sql);
    $numrows = DB_numrows($query);
    if ($numrows > 0) {
        $j=1;
        $p->set_var('show_vars','');
        $p->set_var('vdivid','');

        while ( $A = DB_fetchArray($query) ) {
            $edit_link = "&nbsp;[<a href=\"#\" onClick='ajaxUpdateTemplateVar(\"edit\",{$templateId},{$cntr},{$j});'>Edit</a>&nbsp;]";
            $del_link = "&nbsp;[<a href=\"#\" onClick='ajaxUpdateTemplateVar(\"delete\",{$templateId},{$cntr},{$j});'\">Delete</a>&nbsp;]";
            $p->set_var('variable_id',"[{$A['id']}]");
            $p->set_var('variable_name',$A['variableName']);
            $p->set_var('variable_value',$A['variableValue']);
            $p->set_var('var_id',$j);

            $p->set_var('edit_link',$edit_link);
            $p->set_var('delete_link',$del_link);
            if ($j == 1) {
                $p->parse('template_variable_records','variable_rec');
            } else {
                $p->parse('template_variable_records','variable_rec',true);
            }
            $j++;
        }
        $p->parse('template_variables','variables');

    } else {
        $p->set_var('show_vars','none');
        $p->set_var('vdivid',"vars{$cntr}");
        $p->set_var('template_variable_records','');
        $p->parse('template_variables','variables');

    }
    $p->set_var('cssid' , $cntr%2+1);
    $p->parse('template_records','records',true);
    $cntr++;
}

$p->parse ('output', 'page');
echo $p->finish ($p->get_var('output'));
$retval .= COM_siteFooter (false);
echo $retval;


?>