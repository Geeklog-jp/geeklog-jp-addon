<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

if (!SEC_hasRights('nexform.edit')) {
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

/* Filter incoming variables and set them as globals */
$myvars = array('id','op','mode','formid','fieldid','order','prevorder','page','selectedtab');
ppGetData($myvars,true);

switch ($order) {
    case 1:
        $orderby = 'id';
        break;
    case 2:
        $orderby = 'name';
        break;
    case 3:
        $orderby = 'date';
        break;
    default:
        $orderby = 'id';
        break;
}
if ($order == $prevorder) {
   $direction = ($direction == 'DESC') ? 'ASC' : 'DESC';
} else {
   $direction = 'ASC';
}

$LANG_NAVBAR = $LANG_FRM_ADMIN_NAVBAR;

function displayFormRecords() {
    global $_CONF,$_TABLES,$statusmsg,$LANG_NAVBAR;
    global $direction, $order, $orderby;

    $navbar = new navbar;
    $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
    $navbar->add_menuitem($LANG_NAVBAR['2'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=add');
    $navbar->add_menuitem($LANG_NAVBAR['12'], $_CONF['site_admin_url'] .'/plugins/nexform/import.php');

    $query = DB_query("SELECT id,name,date,responses,status,comments FROM {$_TABLES['formDefinitions']} ORDER BY $orderby $direction");

    $page = new Template($_CONF['path_layout'] . 'nexform/admin');
    $page->set_file (array ('page' => 'formdetail.thtml', 'msgline' => 'alertline.thtml', 'records'=>'formrecords.thtml'));

    $navbar->set_selected($LANG_NAVBAR['1']);
    $page->set_var ('navbar',$navbar->generate());

    if ($statusmsg != '') {
        $page->set_var ('alertmsg',$statusmsg);
        $page->set_var ('show_alert','');
        $page->set_var ('show_msg','none');
    } else {
        $page->set_var ('alertmsg','');
        $page->set_var ('show_alert','none');
        $page->set_var ('show_msg','');
    }
    $page->parse('alertline','msgline',true);
    $page->set_var ('helpmsg','Click on the form name to view or edit the form definition - or Select Action.');
    $page->set_var ('LANG_refresh','Refresh');
    $page->set_var ('LANG_add','Create new Form');
    $page->set_var ('prevorder',$order);
    $page->set_var ('direction',$direction);
    $page->set_var ('HEADING1','ID');
    $page->set_var ('HEADING2','Name');
    $page->set_var ('HEADING3','Last Edit');
    $page->set_var ('HEADING4','Hits');
    $page->set_var ('HEADING5','Active');
    $page->set_var ('HEADING6','Actions');
    $page->set_var ('imgset',$_CONF['site_url'] . '/nexform/images');
    $page->set_var ('site_url',$_CONF['site_url']);
    $page->set_var ('site_admin_url',$_CONF['site_admin_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);

    $i = 1;
    while ( list ($id,$name,$date,$responses,$status,$comments) = DB_fetchArray($query)) {
        $comments = str_replace('<br />', '', $comments);
        $link = '<a href="'.$_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=';
        $editlink_begin = $link . 'editform&id='.$id.'&mode=edit">';
        $editlink_end = '</a>';
        $LANG_CONFIRM = 'Please confirm that you want to delete this form and associated records';
        $deletelink = $link .'delform&id='.$id.'" onclick="return confirm(\''.$LANG_CONFIRM.'\');">Delete</a>';
        $fieldslink = $link . 'displayfields&id='.$id.'">Fields</a>';
        $previewlink = '<a href="'.$_CONF['site_admin_url'] .'/plugins/nexform/preview.php?id='.$id.'" TITLE="'.$comments.'">%s</a>';
        $reportlink = '<a href="'.$_CONF['site_admin_url'] .'/plugins/nexform/report.php?formid='.$id.'">Results</a>';
        if ($status == 1) {
            $page->set_var ('chk_status','checked');
        } else {
            $page->set_var ('chk_status','');
        }    
            
        $page->set_var ('id',$id);
        $page->set_var ('editicon','<img src="'.$_CONF['layout_url'] . '/images/edit.png" border="0">');
        $page->set_var ('name',$name);
        $page->set_var ('date',strftime("%Y-%m-%d", $date));
        $page->set_var ('responses',$responses);
        $page->set_var ('formaction_link',sprintf($previewlink,$name));
        $page->set_var ('fieldslink',$fieldslink);
        $page->set_var ('editlink_begin',$editlink_begin);
        $page->set_var ('editlink_end',$editlink_end);
        $page->set_var ('cssid',$i);
        $i = ($i==2? 1 : 2);
        $page->parse('form_records','records',true);
        
    }
    $page->parse ('output', 'page');
    $retval .=  $page->finish ($page->get_var('output'));
    return $retval;
}


function editFormRecord($mode) {
    global $_CONF,$_TABLES,$statusmsg,$id,$CONF_FE,$LANG_FE02,$LANG_NAVBAR;

    $page = new Template($_CONF['path_layout'] . 'nexform/admin');
    $page->set_file (array ('page' => 'editform.thtml',
            'fset_defintions' => 'formfieldset_definitions.thtml',
            'fset_record' => 'fieldset_record.thtml'));

    $page->set_var ('field1_options',
        COM_optionList($_TABLES['formFields'],'tfid,label,fieldorder','',2, 
            "formid='{$id}' AND type NOT IN ('submit','cancel','hidden')"));
    $page->set_var ('field2_options',
        COM_optionList($_TABLES['formFields'],'tfid,label,fieldorder','',2, 
            "formid='{$id}' AND type NOT IN ('submit','cancel','hidden')"));

    $page->set_var ('showtab3','none');
    $page->set_var ('classnavtab1','navsubcurrent');
    $page->set_var ('classnavtab2','navsubmenu');
    $page->set_var ('classnavtab3','navsubmenu');
    $page->set_var ('fsetid','');
    $page->set_var ('show_editfset','none');

    if ($mode == 'edit') {
        $fields  = 'id,name,shortname,date,responses,template,post_method,post_option,';
        $fields .= 'before_formid,after_formid,show_as_tab,tab_label,intro_text,after_post_text,';
        $fields .= 'on_submit,return_url,perms_view,perms_access,perms_edit,status,comments,';
        $fields .= 'show_mandatory_note';
        $query = DB_query("SELECT $fields FROM {$_TABLES['formDefinitions']} WHERE id='{$id}'");
        list (
            $id,$name,$shortname,$date,$responses,$template,$post_method,$post_option,
            $before_formid,$after_formid,$show_as_tab,$tab_label,$intro_text,$post_text,$on_submit,
            $return_url,$perms_view,$perms_access,$perms_edit,$status,$comments,$show_mandatory_note) = DB_fetchArray($query);

        /* Check if user wants to edit or delete a fieldset defintion */
        $editfsetid = COM_applyFilter($_GET['editfset'],true);
        $delfsetid = COM_applyFilter($_GET['delfset'],true);

        if ($delfsetid >= 0) {
            /* Un-encode the fieldset definitions and remove the requested one */
            $afieldset  = DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$id}'");
            if ($afieldset != '') {
                $fieldsets = unserialize($afieldset);  // Retrieve array of fieldsets
                unset($fieldsets[$delfsetid]);
                $fieldsets = serialize($fieldsets);
                DB_query("UPDATE {$_TABLES['formDefinitions']} SET fieldsets='{$fieldsets}' WHERE id='$id'");
            }
        }

        if ($editfsetid > 0) {
            /* Un-encode the fieldset definitions and display them as records if any exist */
            $afieldset  = DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$id}'");
            if ($afieldset != '') {
                $fieldsets = unserialize($afieldset);  // Retrieve array of fieldsets
                $page->set_var('fset_id',$editfsetid);
                $page->set_var ('show_addfsetbtn','none');
                $statusmsg = 'Edit Form Fieldset Details';
                $page->set_var('fsetid',$editfsetid);
                $page->set_var ('field1_options',
                    COM_optionList($_TABLES['formFields'],'tfid,label,fieldorder',
                        $fieldsets[$editfsetid]['begin'],2,"formid='{$id}' AND type NOT IN ('submit','cancel')"));
                $page->set_var ('field2_options',
                   COM_optionList($_TABLES['formFields'],'tfid,label,fieldorder',
                        $fieldsets[$editfsetid]['end'],2,"formid='{$id}' AND type NOT IN ('submit','cancel')"));
                $page->set_var('fieldset_label',$fieldsets[$editfsetid]['label']);
            }
        }

        if ($editfsetid > 0 OR $delfsetid > 0) {
            $page->set_var ('show_editfset','');
            $page->set_var ('showtab1','none');
            $page->set_var ('showtab2','none');
            $page->set_var ('showtab3','');
            $page->set_var ('classnavtab1','navsubmenu');
            $page->set_var ('classnavtab2','navsubmenu');
            $page->set_var ('classnavtab3','navsubcurrent');
        }
        if ($statusmsg != '') {
            $page->set_var ('helpmsg',$statusmsg);
        } else {
            $page->set_var ('helpmsg','Edit the Form Record Details');
        }
        $page->set_var ('show_formid','');
        $page->set_var ('formid',"glform_{$id}");
        $page->set_var ('LANG_submit','Update Record');
        if ($status == 1) {
            $page->set_var ('chkstatus','CHECKED=CHECKED');
        } else {
            $page->set_var ('chkstatus','');
        }

        if ($show_as_tab == 1) {
            $page->set_var ('chk_showastab','CHECKED=CHECKED');
        } else {
            $page->set_var ('chk_showastab','');
            $page->set_var ('show_tablabel','none');
        }
        if ($show_mandatory_note == 1) {
            $page->set_var ('chk_showmandatory','CHECKED=CHECKED');
        } else {
            $page->set_var ('chk_showmandatory','');
        }

        // Set the template options
        $templateoptions = '';
        foreach ($CONF_FE['templates'] as $key => $label) {
            if ($template == $key) {
                $templateoptions .= '<option value="'.$key.'" SELECTED=selected>'.$label.'</option>';
            } else {
                $templateoptions .= '<option value="'.$key.'">'.$label.'</option>';
            }
        }
        $page->set_var('template_options',$templateoptions);


        $page->set_var ('formlisting1_options',COM_optionList($_TABLES['formDefinitions'],'id,name',$before_formid,1,"name != '$name'"));
        $page->set_var ('formlisting2_options',COM_optionList($_TABLES['formDefinitions'],'id,name',$after_formid,1,"name != '$name'"));

        $navbar = new navbar();
        $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
        $navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$id);
        $navbar->add_menuitem($LANG_NAVBAR['4'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=displayfields&id='.$id);
        $navbar->add_menuitem($LANG_NAVBAR['7'], $_CONF['site_admin_url'] .'/plugins/nexform/preview.php?&id='.$id);
        $navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?&formid='.$id);

        /* Un-encode the fieldset definitions and display them as records if any exist */
        $afieldsets  = unserialize(DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$id}'"));
        if ($afieldsets != '') {
            $actionurl = $_CONF['site_admin_url'] . '/plugins/nexform/index.php';
            $actionurl .= '?op=editform&mode=edit&id='.$id;
            foreach ($afieldsets as $fset_id => $fieldset) {
                    $page->set_var('fset_id',$fset_id);
                    $page->set_var('fset_label',$fieldset['label']);
                    $page->set_var('fset_field1', DB_getItem($_TABLES['formFields'],'label',"tfid='{$fieldset['begin']}' AND formid='{$id}'") );
                    $page->set_var('fset_field2', DB_getItem($_TABLES['formFields'],'label',"tfid='{$fieldset['end']}' AND formid='{$id}'") );
                    $page->set_var('fset_editlink',"<a href=\"{$actionurl}&editfset={$fset_id}\">[Edit]</a>");
                    $page->set_var('fset_deletelink',"<a href=\"{$actionurl}&delfset={$fset_id}\">[Delete]</a>");
                    $page->set_var ('cssid',$i%2);
                    $page->parse('fieldset_records','fset_record',true);
            }
            $page->parse('fieldset_definitions','fset_defintions');
        }

        $page->set_var ('intro_text',$intro_text);
        $page->set_var ('post_text',$post_text);
        $page->set_var ('tab_label',$tab_label);
        $page->set_var ('comments',$comments);
        $page->set_var ('return_url',$return_url);
        $page->set_var ('on_submit',$on_submit);
        $navbar->set_selected($LANG_NAVBAR['3']);
        $page->set_var ('navbar',$navbar->generate());
        $page->set_var ('viewperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name',$perms_view));
        $page->set_var ('accessperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name',$perms_access));
        $page->set_var ('editperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name',$perms_edit));

    } else {
        $page->set_var ('helpmsg','Create new Form Definition - Complete all required fields');
        $page->set_var ('show_formid','none');
        $page->set_var ('show_tablabel','none');
        $page->set_var ('chkstatus','CHECKED=CHECKED');
        $page->set_var ('LANG_submit','Add Record');
        $post_method = 'dbsave';
        $page->set_var ('formlisting_options',COM_optionList($_TABLES['formDefinitions'],'id,name'));

        $navbar = new navbar();
        $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
        $navbar->add_menuitem($LANG_NAVBAR['2'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=add&id='.$id);
        $navbar->set_selected($LANG_NAVBAR['2']);
        $page->set_var ('navbar',$navbar->generate());
        $page->set_var ('viewperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name'));
        $page->set_var ('accessperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name'));
        $page->set_var ('editperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name'));
    }

    if ($statusmsg != '') {
        $page->set_var ('alertmsg',$statusmsg);
    } else {
        $page->set_var ('alertmsg','');
        $page->set_var ('show_alert','none');
    }

    $page->set_var ('imgset',$_CONF['site_url'] . '/nexform/images');
    $page->set_var ('site_url',$_CONF['site_url']);
    $page->set_var ('site_admin_url',$_CONF['site_admin_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);
    $page->set_var ('id',$id);
    $page->set_var ('mode',$mode);
    $page->set_var ('name',$name);
    $page->set_var ('shortname',$shortname);
    $page->set_var ('post_option',$post_option);

    // Set the template options
    $templateoptions = '';
    foreach ($CONF_FE['templates'] as $key => $label) {
        if ($template == $key) {
            $templateoptions .= '<option value="'.$key.'" SELECTED=selected>'.$label.'</option>';
        } else {
            $templateoptions .= '<option value="'.$key.'">'.$label.'</option>';
        }
    }
    $page->set_var('template_options',$templateoptions);

    $options = '';
    foreach ($CONF_FE['postmethods']    as $key => $olabel) {
        if ($key == $post_method) {
            $options .= "<option value=\"$key\" SELECTED>$olabel</option>";
        } else {
            $options .= "<option value=\"$key\">$olabel</option>";
        }
    }
    $page->set_var ('post_method_options',$options);
    $page->set_var ('LANG_help1','Required: Enter a descriptive name for the form - not shown to user');
    $page->set_var ('LANG_help2','Optional: Descriptive comments on form purpose for internal admin use only when editing forms.');
    $page->set_var ('LANG_help3','Optional: Descriptive message that will be shown to user before the form.');
    $page->set_var ('LANG_help4','Optional: Descriptive message that will be shown to user upon submitting the form.');
    $page->set_var ('LANG_help5','Form Permissions:<br>1) Rights to access form to submit data.<br>2) Rights to view results of posted information.<br>3) Rights to edit the form or edit previously posted results.');
    $page->set_var ('LANG_help6','Default is active. Uncheck if form should not be published or made available.');
    $page->set_var ('LANG_help7','Internal use only: ID form is know internally.');
    $page->set_var ('LANG_help8','Required: Method to use when posting the form results:<br>1) Save to Database: uses a default table for all results. Best method for basic forms or forms that don\'t need custom processing. Able to use built-in reporting facility to view results<br>2) Email Results: Summary of the posted form results will be formatted into an message and emailed to single user or list of users as defined in the post option field.<br>3) Post to URL: Allows you to use a custom script to handle the posted form results. When this option is enabled, you will be able to edit the field names.');
    $page->set_var ('LANG_help9','1) Email Method: Enter email address or multiple email addresses seperated by comma.<br><br>2) Post to URL: Enter URL for script to process form results.');
    $page->set_var ('LANG_help11','Select one of the defined template files to use when displaying form.');
    $page->set_var ('LANG_help12','Feature to have a form appear before this form - linked forms<br><br>Select a form that will display "before" this form definition.');
    $page->set_var ('LANG_help13','Feature to have a form appear after this form - linked forms.<br>Both the before and after options can be used at the same time. A linked list of forms are effectively supported.<br><br>SelectSelect a form that will appear "after" this form definition.');
    $page->set_var ('LANG_help14','Enter any Javascript to execute upon the form being submitted.');
    $page->set_var ('LANG_help16','Enter a custom URL if you have a custom handler script the user should return to after submitting the form.<br><br>The default will be the sites main index.php with a default message.');
    $page->set_var ('LANG_help17','If this form is linked as a sub-form - this form has will be displayed before or after another form.<br><br>Option if enabled will present this form then as a tab on a subnav menu.');
    $page->set_var ('LANG_help18','Label that will appear on form tab.');
    $page->set_var ('LANG_help19','Display Mandatory Message at bottom of form?');
    $page->set_var ('LANG_help20','Shortname that can be used by other plugins to identify form');
    $page->set_var ('LANG_help21','Select the field on the form where fieldset border should begin');
    $page->set_var ('LANG_help22','Select the field on the form where the fieldset border should end');
    $page->set_var ('LANG_help23','Enter the label to use for the fieldset legend');
    $page->set_var ('LANG_help24','Optional: URL to refresh the page to after the user submits a form.');

    $page->set_var ('LANG_postoption', 'Post Option');
    if ($post_method != 'dbsave') {
        $page->set_var('show_postoption', '');
    } else {
        $page->set_var('show_postoption', 'none');
    }
    
    $page->parse ('output', 'page');
    $retval .=  $page->finish ($page->get_var('output'));
    return $retval;
}


function updateFormRecord($mode) {
    global $_CONF, $_POST, $_TABLES, $id, $_DB_name;

    $name = ppPrepareForDB($_POST['name']);
    $shortname = ppPrepareForDB($_POST['shortname']);
    $comments = ppPrepareForDB($_POST['comments']);
    $location = ppPrepareForDB($_POST['location']);
    $template = ppPrepareForDB($_POST['template']);
    $post_method = ppPrepareForDB($_POST['post_method']);
    $post_option = ppPrepareForDB($_POST['post_option']);
    $before_formid = ppPrepareForDB($_POST['before_formid']);
    $after_formid = ppPrepareForDB($_POST['after_formid']);
    $intro_text = ppPrepareForDB($_POST['intro_text'],false);
    $post_text = ppPrepareForDB($_POST['post_text'],false);
    $return_url = ppPrepareForDB($_POST['return_url']);
    $status = ppPrepareForDB($_POST['status']);
    $show_as_tab = ppPrepareForDB($_POST['show_as_tab']);
    $tab_label = ppPrepareForDB($_POST['tab_label']);
    $perms_view = COM_applyFilter($_POST['perms_view'],true);
    $perms_access = COM_applyFilter($_POST['perms_access'],true);
    $perms_edit = COM_applyFilter($_POST['perms_edit'],true);
    $fsetid = COM_applyFilter($_POST['fsetid'],true);
    $field1 = COM_applyFilter($_POST['field1'],true);
    $field2 = COM_applyFilter($_POST['field2'],true);
    $fieldset_label = COM_applyFilter($_POST['fieldset_label']);
    $show_mandatory_note = COM_applyFilter($_POST['show_mandatory_note'],true);
    $show_as_tab = COM_applyFilter($show_as_tab, true);

    if (!get_magic_quotes_gpc()) {
        $on_submit = addslashes(htmlspecialchars($_POST['on_submit']));
    } else {
        $on_submit = htmlspecialchars($_POST['on_submit']);
    }
    $date = time();

    if ($mode == 'add') {
        $gid = uniqid($_DB_name,FALSE);
        $fields = 'gid,name,shortname,date,template,post_method,post_option,fieldsets,';
        $fields .= 'before_formid,after_formid,show_as_tab,tab_label,intro_text,after_post_text,';
        $fields .= 'on_submit,return_url,perms_view,perms_access,perms_edit,status,comments,show_mandatory_note';
        $sql = "INSERT INTO {$_TABLES['formDefinitions']} ($fields) VALUES (";
        $sql .= "'$gid','$name','$shortname','$date','$template','$post_method','$fieldset','$post_option',";
        $sql .= "'$before_formid','$after_formid','$show_as_tab','$tab_label','$intro_text','$post_text',";
        $sql .= "'$on_submit','$return_url','$perms_view','$perms_access','$perms_edit',";
        $sql .= "'$status','$comments','$show_mandatory_note')";
        DB_query($sql);

        $formid = $id = DB_insertID();

        $GLOBALS['statusmsg'] = 'Record Added';

    } elseif (DB_count($_TABLES['formDefinitions'],"id",$id) == 1) {
        $oname = DB_getItem($_TABLES['formDefinitions'],'name',"id='{$id}'");

        DB_query("UPDATE {$_TABLES['formDefinitions']} SET 
            name='{$name}', shortname='{$shortname}',date='{$date}', post_method='{$post_method}', post_option='{$post_option}',
            before_formid='{$before_formid}',after_formid='{$after_formid}',show_as_tab='{$show_as_tab}',
            tab_label='{$tab_label}',template='{$template}', intro_text='{$intro_text}',
            after_post_text='{$post_text}', on_submit='{$on_submit}', return_url='{$return_url}', 
            perms_view='{$perms_view}', perms_access='{$perms_access}', perms_edit='{$perms_edit}',
            status='{$status}', comments='{$comments}',show_mandatory_note='{$show_mandatory_note}' 
            WHERE id='$id'");

        if ($field1 != 0 AND $field2 != 0) {
            // Check if user wanted to update an existing fieldset definition
            if ($fsetid > 0) {

                // Retrieve original fieldset record and replace definition in array
                $fieldsets  = DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$id}'");
                if ($fieldsets != '') {
                    $afieldsets = unserialize($fieldsets);  // Retrieve array of fieldsets
                    $afieldsets[$fsetid] = array('begin' => "$field1",'end' => "$field2", 'label' => "$fieldset_label");
                }
            } else { // User wants to add a new defintion
                // Retrieve original fieldset record and replace definition in array
                $fieldsets  = DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$id}'");
                if ($fieldsets != '') {
                    $afieldsets = unserialize($fieldsets);  // Retrieve array of fieldsets
                    if (count($afieldsets) == 0 ) {
                        $afieldsets[1] = array('begin' => "$field1",'end' => "$field2", 'label' => "$fieldset_label");
                    } else {
                        $afieldsets[] = array('begin' => "$field1",'end' => "$field2", 'label' => "$fieldset_label");
                    }
                } else {   // No definition yet exists - create array
                    $afieldset = array();
                    $afieldsets[1] = array('begin' => "$field1",'end' => "$field2", 'label' => "$fieldset_label");
                }
            }
            $fieldset = serialize($afieldsets);
            DB_query("UPDATE {$_TABLES['formDefinitions']} SET fieldsets='{$fieldset}' WHERE id='$id'");
        }

        $GLOBALS['statusmsg'] = 'Record Updated';

    } else {
        COM_errorLog("nexform Plugin: Admin Error updating Form Record $id");
        $GLOBALS['statusmsg'] = 'Error adding or updating Record';
    }
}

function copyFormRecord() {
    global $_CONF, $_TABLES, $CONF_FE, $id, $_DB_name;
    $sourceform = $id;
    $query = DB_query("SELECT * FROM {$_TABLES['formDefinitions']} WHERE id='{$sourceform}'");
    if (DB_numRows($query) == 1) {
        $A = DB_fetchArray($query);
        $A['intro_text'] = addslashes($A['intro_text']);
        $A['after_post_text'] = addslashes($A['after_post_text']);
        $A['comments'] = addslashes($A['comments']);
        $A['on_submit'] = addslashes($A['on_submit']);
        $gid = uniqid($_DB_name,FALSE);
        $date = time();

        $sql  = "INSERT INTO {$_TABLES['formDefinitions']} ";
        $sql .= "( gid,name,date,template,post_method,post_option,before_formid,after_formid,fieldsets,intro_text,after_post_text,on_submit,status,comments,show_mandatory_note ) ";
        $sql .= "VALUES ( '$gid','{$A['name']} - copy', '$date', '{$A['template']}', '{$A['post_method']}', '{$A['post_option']}', '{$A['before_formid']}', '{$A['after_formid']}', '{$A['fieldsets']}','{$A['intro_text']}','{$A['after_post_text']}', '{$A['on_submit']}', '{$A['status']}','{$A['comments']}','{$A['show_mandatory_note']}' )";
        DB_query($sql);

        $targetform = DB_insertID();
        /* Now copy all the fields that are defined for this original form */
        $query = DB_query("SELECT * FROM {$_TABLES['formFields']} WHERE formid='{$sourceform}'");
        while( $B = DB_fetchArray($query)) {
            $B['javascript'] = addslashes($B['javascript']);
            $B['field_values'] = addslashes($B['field_values']);
	        $B['label'] = addslashes($B['label']);
            $B['fieldorder'] = COM_applyFilter($B['fieldorder'], true);
            DB_query("INSERT INTO {$_TABLES['formFields']}
            (
               formid, tfid,type, fieldorder,  label, style, layout, is_vertical, is_newline,
               is_mandatory, is_searchfield, value_by_function,is_resultsfield,
               is_reverseorder, field_attributes, field_help, field_values, validation, javascript
            )
            VALUES
            (
             '{$targetform}','{$B['tfid']}','{$B['type']}', '{$B['fieldorder']}', '{$B['label']}', '{$B['style']}', '{$B['layout']}', '{$B['is_vertical']}', '{$B['is_newline']}', '{$B['is_mandatory']}', '{$B['is_searchfield']}',
             '{$B['value_by_function']}','{$B['is_resultsfield']}',  '{$B['is_reverseorder']}', '{$B['field_attributes']}','{$B['field_help']}','{$B['field_values']}','{$B['validation']}','{$B['javascript']}')");

             $id = DB_insertID();
             $fieldname = "{$CONF_FE['fieldtypes'][$B['type']][0]}{$B['formid']}_{$id}";
             DB_query("UPDATE {$_TABLES['formFields']} SET field_name = '$fieldname' WHERE id=$id;");
        }

        $GLOBALS['statusmsg'] = 'Form copied successfully';
    }
}

function deleteFormRecord() {
    global $_CONF, $_TABLES, $id;

    /* Need to check if any linked records have used this ID. If so don't delete record, just set status inactive */
    if (DB_count($_TABLES['formDefinitions'],"id",$id) == 1) {
        DB_query("DELETE FROM {$_TABLES['formDefinitions']} WHERE id='$id'");
        DB_query("DELETE FROM {$_TABLES['formFields']} WHERE formid='$id'");

        $GLOBALS['statusmsg'] = 'Form Definition and Field Records Deleted';
   } else {
        $GLOBALS['statusmsg'] = 'Error: Form Record not found';
   }

}


function displayFieldRecords($formid,$lastfield='') {
    global $_CONF,$CONF_FE,$_TABLES,$statusmsg,$LANG_NAVBAR;

    $navbar = new navbar();
    $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
    $navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$formid);
    $navbar->add_menuitem($LANG_NAVBAR['4'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=displayfields&id='.$formid);
    $navbar->add_menuitem($LANG_NAVBAR['5'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editfield&formid='.$formid.'&mode=add');
    $navbar->add_menuitem($LANG_NAVBAR['7'], $_CONF['site_admin_url'] .'/plugins/nexform/preview.php?&id='.$formid);
    $navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?&formid='.$formid);

    $query = DB_query("SELECT id,tfid,fieldorder,label,type,field_name,field_values,is_mandatory,is_resultsfield,is_vertical,is_newline,is_reverseorder,is_internaluse FROM {$_TABLES['formFields']} WHERE formid='{$formid}' ORDER BY fieldorder");
    
    $page = new Template($_CONF['path_layout'] . 'nexform/admin');
    $page->set_file (array ('page' => 'formfields.thtml', 'msgline' => 'alertline.thtml', 'records'=>'fieldrecords.thtml'));
    if ($statusmsg != '') {
        $page->set_var ('alertmsg',$statusmsg);
    } else {
        $page->set_var ('alertmsg','');
        $page->set_var ('show_alert','none');
    }
    $navbar->set_selected($LANG_NAVBAR['4']);
    $page->set_var ('navbar',$navbar->generate());
    $page->parse('alertline','msgline',true);
    $page->set_var ('LANG_refresh','Refresh');
    $page->set_var ('LANG_add','Create new Field');
    $page->set_var ('LANG_formlisting','Form Listing');
    $page->set_var ('formid',$formid);
    $page->set_var ('HEADING1','Order');
    $page->set_var ('HEADING2','*');
    $page->set_var ('heading2_title','Field is required');
    $page->set_var ('HEADING3','Label');
    $page->set_var ('HEADING4','Type');
    $page->set_var ('HEADING5','Report');
    $page->set_var ('heading5_title','Selected field will be used for results report summary');
    $page->set_var ('HEADING6','Newline');
    $page->set_var ('heading6_title','When showing form, start a new line with this field');
    $page->set_var ('HEADING7','Vertical');
    $page->set_var ('heading7_title','Form layout for this field will be vertical');
    $page->set_var ('HEADING8','labelreverse');
    $page->set_var ('heading8_title','Reverse Label - show field first and then label if defined');
    $page->set_var ('HEADING9','Actions');
    $page->set_var ('imgset',$_CONF['site_url'] . '/nexform/images');
    $page->set_var ('site_url',$_CONF['site_url']);
    $page->set_var ('public_url',$CONF_FE['public_url']);
    $page->set_var ('site_admin_url',$_CONF['site_admin_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);
    $page->set_var('formrecord', "fieldrec_{$lastfield}");

    $i = 2;
    $nextOrder = 10;
    $stepNumber = 10;
    while ( list (
            $id,$tfid,$order,$label,$type,$field_name,$field_value,$is_mandatory,$is_resultsfield,
            $is_vertical,$is_newline,$is_reverseorder,$is_internaluse)
            = DB_fetchArray($query))
    {
        if ($order != $nextOrder) {
            DB_query("UPDATE {$_TABLES['formFields']} SET fieldorder = '$nextOrder' WHERE id = '$id'");
            $order = $nextOrder;
        }
        $nextOrder += $stepNumber;
    
        $link = '<a href="'.$_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=';
        $editlink = $link . 'editfield&fieldid='.$id.'&formid='.$formid.'&mode=edit">Edit</a>';
        $LANG_CONFIRM = 'Please confirm that you want to delete this field';
        $deletelink = $link .'delfield&fieldid='.$id.'&formid='.$formid.'" onclick="return confirm(\''.$LANG_CONFIRM.'\');">Delete</a>';
        $page->set_var ('id',$id);
        $page->set_var ('order',$order);
        if($is_internaluse == 1) {
            $field_summary = "Internal Use Field\nField ID: $tfid\nField Name:$field_name";
        } else {
            $field_summary = "Field ID: $tfid\nField Name:$field_name";
        }
        $page->set_var ('field_summary',$field_summary);

        if ($is_mandatory) {
            $page->set_var ('chk_mandatory','checked');
        } else {
            $page->set_var ('chk_mandatory','');
        }
        if ($label == '' AND $field_value != '') {
            $page->set_var ('label',stripslashes($field_value));
        } else {
            $page->set_var ('label',stripslashes($label));
        }
        $page->set_var ('type',$type);
        if ($is_resultsfield == 1) {
            $page->set_var ('chk_resultfield','checked');
        } else {
            $page->set_var ('chk_resultfield','');
        }
        if ($is_newline == 1) {
            $page->set_var ('chk_newline','checked');
        } else {
            $page->set_var ('chk_newline','');
        }
        if ($is_vertical == 1) {
            $page->set_var ('chk_vertical','checked');
        } else {
            $page->set_var ('chk_vertical','');
        }
        if ($is_reverseorder == 1) {
            $page->set_var ('chk_reverselabel','checked');
        } else {
            $page->set_var ('chk_reverselabel','');
        }

        $page->set_var ('editlink',$editlink);
        $page->set_var ('deletelink',$deletelink);
        $page->set_var ('cssid',$i);
        $i = ($i==2? 1 : 2);
        $page->parse('field_records','records',true);
    }
    $page->parse ('output', 'page');
    $retval .=  $page->finish ($page->get_var('output'));
    return $retval;
}


function editFieldRecord($mode,$selectedtab=1) {
    global $_CONF,$_TABLES,$formid,$fieldid,$CONF_FE,$LANG_NAVBAR;

    $formname = DB_getItem($_TABLES['formDefinitions'],"name","id='$formid'");
    $page = new Template($_CONF['path_layout'] . 'nexform/admin');
    $page->set_file (array ('page' => 'editfield.thtml'));

    if ($mode == 'edit') {
        $fields  = 'id,formid,tfid,type,field_name,fieldorder,label,style,layout,col_width,col_padding,is_vertical,';
        $fields .= 'is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,';
        $fields .= 'is_htmlfiltered, is_internaluse,hidelabel,';
        $fields .= 'field_attributes,field_help,field_values,value_by_function,validation,javascript';
        $query = DB_query("SELECT $fields FROM {$_TABLES['formFields']} WHERE id='{$fieldid}'");
        list ($id,$formid,$tfid,$type,$fieldname,$fieldorder,$label,$style,$layout,$col_width,$col_padding,$is_vertical,
              $is_newline,$is_mandatory,$is_searchfield,$is_resultsfield,$is_reverseorder,$is_filtered, $is_internaluse,$hidelabel,$field_attributes,$field_help,$field_values,$function_used,$validation,$javascript)
            = DB_fetchArray($query);
        $page->set_var ('fieldid',$id);
        $page->set_var ('helpmsg','Form: <b>'.$formname.'</b>. Edit Field #<b>'.$tfid.' '.$label.'</b>');
        $page->set_var ('show_fieldid','');
        $page->set_var ('tfid', $tfid);
        $page->set_var ('col_width', $col_width);
        $page->set_var ('col_padding', $col_padding);

        if ($type == 'select') {
            $page->set_var('show_helpfield','none');
        }
        $page->set_var ('LANG_submit1','Update Record');
        $page->set_var ('LANG_submit2','Update + Next');

        $onclick = 'onclick="document.frm_edit.op.value=\'editfield\';document.frm_edit.fieldid.value=\'%s\';document.frm_edit.submit();"';

        $qprev = DB_query("SELECT id,fieldorder FROM {$_TABLES['formFields']} WHERE formid='{$formid}' AND fieldorder < '{$fieldorder}' ORDER BY fieldorder DESC LIMIT 1");
        list ($previd, $prevorder) = DB_fetchArray($qprev);

        if ( $prevorder > 0 AND $prevorder < $fieldorder) {
            $page->set_var('prev','<a href="#" '.sprintf($onclick,$previd).' " TITLE="Select Previous Field" >Prev</a>');
        } else {
            $page->set_var ('prev','');
        }
        $qnext = DB_query("SELECT id,fieldorder FROM {$_TABLES['formFields']} WHERE formid='{$formid}' AND fieldorder > '{$fieldorder}' ORDER BY fieldorder ASC LIMIT 1");
        list ($nextid, $nextorder) = DB_fetchArray($qnext);
        if ( $nextorder > 0 AND $nextorder > $fieldorder) {
            $nextlink = '<a href="#" '.sprintf($onclick,$nextid).' " TITLE="Select Next Field">Next</a>';
            if ($prevorder == 0) {
                $page->set_var('next',"<span style=\"padding-left:30px;\">$nextlink</span>");
            } else {
                $page->set_var ('next',$nextlink);
            }
        } else {
            $page->set_var ('next','');
        }

        if (DB_getItem($_TABLES['formDefinitions'], 'post_method',"id='$formid'") == 'posturl') {
            $page->set_var ('enablefname','');
            if ($fieldname != '') {
                $page->set_var ('form_fieldname',$fieldname);
            } else {
                $page->set_var ('form_fieldname',"{$CONF_FE['fieldtypes'][$type][0]}{$formid}_{$id}");
            }
        } else {
            $page->set_var ('form_fieldname',"{$CONF_FE['fieldtypes'][$type][0]}{$formid}_{$id}");
            $page->set_var ('enablefname','disabled');
        }
        if ($type == 'checkbox') {
            $page->set_var ('visible_opt1','hidden');
        } else {
            $page->set_var ('visible_opt1','');
        }

        if ($type == 'submit' OR $type == 'button') {
            $page->set_var ('show_manditory','none');  
            $page->set_var ('show_labelstyle','none');
            $page->set_var ('show_searchopt','none');
            $page->set_var ('show_filteropt','none');
        } elseif ($type == 'textarea1' OR $type == 'textarea2') {
            $page->set_var ('show_manditory','');
            $page->set_var ('show_labelstyle','');
            $page->set_var ('show_searchopt','');
            $page->set_var ('show_filteropt','');
        } else {
            $page->set_var ('show_manditory','');
            $page->set_var ('show_labelstyle','');
            $page->set_var ('show_searchopt','');
            $page->set_var ('show_filteropt','none');
        }
        if ($is_vertical == 1) {
            $page->set_var ('chkradio1a', '');
            $page->set_var ('chkradio1b', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkradio1a', 'CHECKED=CHECKED');
            $page->set_var ('chkradio1b', '');
        }
        if ($is_reverseorder == 1) {
            $page->set_var ('chkradio2a', '');
            $page->set_var ('chkradio2b', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkradio2a', 'CHECKED=CHECKED');
            $page->set_var ('chkradio2b', '');
        }
        if ($is_newline == 1) {
            $page->set_var ('chknewline', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chknewline', '');
        }
        if ($is_mandatory == 1) {
            $page->set_var ('chkmandatory', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkmandatory', '');
        }
        if ($is_searchfield == 1) {
            $page->set_var ('chksearch', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chksearch', '');
        }
        if ($is_resultsfield == 1) {
            $page->set_var ('chkresults', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkresults', '');
        }
        if ($is_htmlfiltered == 1) {
            $page->set_var ('chkfilter', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkfilter', '');
        }
        if ($is_internaluse == 1) {
            $page->set_var ('chkinternal', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkinternal', '');
        }
        if ($hidelabel == 1) {
            $page->set_var ('chkhidelabel', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkhidelabel', '');
        }
        if ($function_used == 1) {
            $page->set_var ('chkfunctionused', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkfunctionused', '');
        }
        $navbar = new navbar();
        $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
        $navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$formid);
        $navbar->add_menuitem($LANG_NAVBAR['4'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=displayfields&id='.$formid);
        $navbar->add_menuitem($LANG_NAVBAR['6'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editfield&fieldid='.$fieldid.'&formid='.$formid.'&mode=edit');
        $navbar->add_menuitem($LANG_NAVBAR['7'], $_CONF['site_admin_url'] .'/plugins/nexform/preview.php?&id='.$formid);
        $navbar->set_selected($LANG_NAVBAR['6']);
        $page->set_var ('navbar',$navbar->generate());

    } else {
        $page->set_var ('fieldid',0);
        $page->set_var ('helpmsg','Form: <b>'.$formname.'</b>. Create new Field Definition - Complete all required fields');
        if (DB_getItem($_TABLES['formDefinitions'], 'post_method',"id='$formid'") == 'posturl') {
            $page->set_var ('show_fieldid','');
        } else {
            $page->set_var ('show_fieldid','none');
        }
        $page->set_var ('show_opt1','');
        $page->set_var ('show_opt2','');
        $page->set_var ('show_opt3','none');
        $page->set_var ('show_fieldid','none');
        if ($CONF_FE['field_mandatory_default']) {
            $page->set_var ('chkmandatory', 'CHECKED=CHECKED');
        } else {
            $page->set_var ('chkmandatory', '');
        }
        $page->set_var ('chknewline', 'CHECKED=CHECKED');
        $page->set_var ('LANG_submit1','Add Record');
        $page->set_var ('LANG_submit2','Add + Next');

        $navbar = new navbar();
        $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
        $navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$formid);
        $navbar->add_menuitem($LANG_NAVBAR['4'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=displayfields&id='.$formid);
        $navbar->add_menuitem($LANG_NAVBAR['5'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editfield&formid='.$formid.'&mode=add');
        $navbar->add_menuitem($LANG_NAVBAR['7'], $_CONF['site_admin_url'] .'/plugins/nexform/preview.php?&id='.$formid);
        $navbar->set_selected($LANG_NAVBAR['5']);
        $page->set_var ('navbar',$navbar->generate());

   }

    $page->set_var ('imgset',$_CONF['site_url'] . '/nexform/images');
    $page->set_var ('site_url',$_CONF['site_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);
    $page->set_var ('site_admin_url',$_CONF['site_admin_url']);
    $page->set_var ('layout_url',$_CONF['layout_url']);
    $page->set_var ('formid',$formid);
    $page->set_var ('mode',$mode);
    $page->set_var ('fieldorder',$fieldorder);
    $page->set_var ('label',htmlspecialchars(stripslashes($label)));
    $page->set_var ('style',$style);
    $page->set_var ('field_attributes',htmlspecialchars(stripslashes($field_attributes)));
    $page->set_var ('field_help',stripslashes($field_help));
    $page->set_var ('field_values',stripslashes($field_values));
    $page->set_var ('validation',htmlspecialchars(stripslashes($validation)));
    $page->set_var ('javascript',htmlspecialchars(stripslashes($javascript)));
    $page->set_var ('selectedtab',$selectedtab);
    $page->set_var ('showtab1',($selectedtab == 0 OR $selectedtab == 1) ? '' : 'none');
    $page->set_var ('showtab2',($selectedtab == 2) ? '' : 'none');
    $page->set_var ('showtab3',($selectedtab == 3) ? '' : 'none');
    $page->set_var ('showtab4',($selectedtab == 4) ? '' : 'none');

    $page->set_var ('classnavtab1',($selectedtab == 0 OR $selectedtab == 1) ? 'navsubcurrent' : 'navsubmenu');
    $page->set_var ('classnavtab2',($selectedtab == 2) ? 'navsubcurrent' : 'navsubmenu');
    $page->set_var ('classnavtab3',($selectedtab == 3) ? 'navsubcurrent' : 'navsubmenu');
    $page->set_var ('classnavtab4',($selectedtab == 4) ? 'navsubcurrent' : 'navsubmenu');

    $page->set_var ('LANG_help1','Select type of element from the available selection.');
    $page->set_var ('LANG_help2','Enter the label to be shown beside this field.<br>Multiple lables as in the case of a Multiple Checkbox element, can be entered separated by commas.<br><br>You can use the optional styles under the layout tab to style your form fields as well.');
    $page->set_var ('LANG_help3','Enter any needed element attributes separated by a space.<br>Example:&nbsp;size="20" maxlength="60"');
    $page->set_var ('LANG_help4','Enter any default value or list of values separated by commas<br>if this is a Select type or Multiple Checkboxes type field.<br>Example:Mr,Mrs,Miss,Dr<br><br>A function can be used as well.<br>Enter function name and check next field \'Use a fuction for values\' to enable.<br><br>Use a lookuplist to provide the field options in the case of a select field or multi-check<br>Example: [alist:16] to use list 16<br><br>Dynamic fields use this field to specify the form id<br>Example: 4 or 4,1 to limit to only one instance of the form 4');
    $page->set_var ('LANG_help5','Optional: A function can be used to generate default values for field.<br>Usefull for dropdown lists or dates.<br>Check this option and enter the function name in the Values field.<br>You can optionally pass in 1 parm to function (colan used as delimiter).<br>Example myapp_getname:books - or myapp_getstates');
    $page->set_var ('LANG_help6','Enable if this field should only be used for internal use.');
    $page->set_var ('LANG_help7','Enter any supported validation attributes - click on the help link for more info.<br>Examples: minlength=10 minvalue=1<br>maxvalue=99.99<br>regexp=JSVAL_RX_EMAIL.<br>realname="Password must be at least 6 characters."<br>You can combine more then one validation rule.');
    $page->set_var ('LANG_help8','Enter any javascript logic that you want to be triggered for this field.');
    $page->set_var ('LANG_help9','Enable if this field is mandatory.');
    $page->set_var ('LANG_help10','Field label and element pair be vertical or horizontal on form.');
    $page->set_var ('LANG_help11','Should the field element or label appear first.');
    $page->set_var ('LANG_help12','Select one of the pre-defined styles to use for the label.');
    $page->set_var ('LANG_help13','Enable if this field should start on a new row.');
    $page->set_var ('LANG_help14','Optional if using a custom script to process form.<br>Enter a unique field name to use or leave the default assigned name.');
    $page->set_var ('LANG_help15','Display order on form where this field will appear.');
    $page->set_var ('LANG_help16','Enable if this field will be shown in admin results listing summary view.');
    $page->set_var ('LANG_help17','Enable if this field should be included in search form.');
    $page->set_var ('LANG_help18','User help message that wll be displayed when they hover over field.<br>It will not appear unless user viewing the form has edit access.');
    $page->set_var ('LANG_help19','Width of field, if left blank - default of '.$CONF_FE['field1_defaultspacing'].' % will be used.');
    $page->set_var ('LANG_help20','Right padding in pixels to be used for field - will use default if blank.');
    $page->set_var ('LANG_help21','Hide field label - Do not show on the generated form.<br>The label should be used regardless as it describes the field purpose.');


    $options = '';

    /* Determine if Multiple File type field is already used - only one allowed per form */
    $mfileused = false;
    if (DB_count($_TABLES['formFields'],array('formid','type'), array($formid,'mfile')) > 0 ) {
        //$mfileused = true;
    }
    /* Set the field type options dropdown - not showing mfile type if already used and not editing that field */
    foreach ($CONF_FE['fieldtypes']    as $key => $olabel) {
        if (!$mfileused OR ($mfileused AND $type == 'mfile') OR ($mfileused AND $key != 'mfile')) {
            $fieldlabel = $olabel[1];
            if ($key == $type) {
                $options .= "<option value=\"$key\" SELECTED>$fieldlabel</option>";
            } else {
                $options .= "<option value=\"$key\">$fieldlabel</option>";
            }
        }
    }
    $page->set_var ('fieldtype_options',$options);

    $options = '';
    foreach ($CONF_FE['fieldstyles']    as $key => $olabel) {
        if ($key == $style) {
            $options .= "<option value=\"$key\" SELECTED>{$olabel[0]}</option>";
        } else {
            $options .= "<option value=\"$key\">{$olabel[0]}</option>";
        }
    }
    $page->set_var ('fieldstyle_options',$options);
    $page->parse ('output', 'page');
    $retval .=  $page->finish ($page->get_var('output'));
    return $retval;

}

function updateFieldRecord($mode) {
    global $_CONF, $_POST, $CONF_FE, $_TABLES, $formid,$fieldid;

    $fieldname = $_POST['fieldname'];
    $type = $_POST['type'];
    $label = $_POST['label'];
    $style = $_POST['style'];
    $fieldorder = $_POST['fieldorder'];
    $is_vertical = $_POST['is_vertical'];
    $is_reverseorder = $_POST['is_reverseorder'];
    $is_newline = COM_applyFilter($_POST['is_newline'],true);
    $is_mandatory = COM_applyFilter($_POST['is_mandatory'],true);
    $is_searchfield = COM_applyFilter($_POST['is_searchfield'],true);
    $is_resultsfield = COM_applyFilter($_POST['is_resultsfield'],true);
    $is_internaluse = COM_applyFilter($_POST['is_internaluse'],true);
    $hidelabel = COM_applyFilter($_POST['hidelabel'],true);
    $is_htmlfiltered = COM_applyFilter($_POST['is_htmlfiltered'],true);
    $function_used = COM_applyFilter($_POST['use_function'],true);
    $col_width = COM_applyFilter($_POST['col_width']);
    $col_padding = COM_applyFilter($_POST['col_padding']);

    $field_values = $_POST['field_values'];

    if(!get_magic_quotes_gpc() ) {
        $validation = addslashes($_POST['validation']);    
        $label = addslashes($label);
        $field_attributes = addslashes($_POST['field_attributes']);
        $javascript = addslashes($_POST['javascript']);
        $field_help = addslashes($_POST['field_help']);
    } else {
        $validation = $_POST['validation'];    
        $field_attributes = $_POST['field_attributes'];
        $javascript = $_POST['javascript'];
        $field_help = $_POST['field_help'];
    }

    if ($mode == 'add') {
        $fieldorder = COM_applyFilter($fieldorder, true);
        $is_vertical = COM_applyFilter($is_vertical, true);
        $is_reverseorder = COM_applyFilter($is_reverseorder, true);
        
        $fields  = 'formid,type,field_name,fieldorder,label,style,is_vertical,is_reverseorder,is_newline,';
        $fields .= 'is_mandatory,is_searchfield,is_resultsfield,is_htmlfiltered,is_internaluse,hidelabel,';
        $fields .= 'field_attributes,field_help,field_values,value_by_function,validation,javascript';

        $values = "'{$formid}','{$type}','{$fieldname}','{$fieldorder}',";
        $values .= "'{$label}','{$style}','{$is_vertical}','{$is_reverseorder}','{$is_newline}',";
        $values .= "'{$is_mandatory}','{$is_searchfield}','{$is_resultsfield}','{$is_htmlfiltered}',";
        $values .= "'{$is_internaluse}','{$hidelabel}','{$field_attributes}','{$field_help}','{$field_values}','{$function_used}',";
        $values .= "'{$validation}','{$javascript}'";

        DB_query("INSERT INTO {$_TABLES['formFields']}( $fields ) VALUES ( $values )");
        $fieldid = DB_insertID();

        $date = time();
        DB_query("UPDATE {$_TABLES['formDefinitions']} SET date='{$date}' WHERE id='$formid'");

        $GLOBALS['statusmsg'] = 'Record Added';

        // Set the template field id now - incremental id per form
        $query = DB_query("SELECT max(tfid) FROM {$_TABLES['formFields']} WHERE formid='$formid'");
        list ($maxtfid) = DB_fetchArray($query);
        $tfid = $maxtfid + 1;
        DB_query("UPDATE {$_TABLES['formFields']} SET tfid='{$tfid}' WHERE id='{$fieldid}'");

        if ($fieldname == '') {
            // BL Note: Use tfid to set fieldname
            $fieldname = "{$CONF_FE['fieldtypes'][$type][0]}{$formid}_{$fieldid}";
            DB_query("UPDATE {$_TABLES['formFields']} SET field_name='{$fieldname}' WHERE id='{$fieldid}'");
        }

        if ($fieldorder == '') {
            $query = DB_query("SELECT max(fieldorder) FROM {$_TABLES['formFields']} WHERE formid='$formid'");
            list ($maxorder) = DB_fetchArray($query);
            $order = $maxorder + 10;
            DB_query("UPDATE {$_TABLES['formFields']} SET fieldorder='{$order}' WHERE id='{$fieldid}'");
        }

    } elseif (DB_count($_TABLES['formFields'],"id",$fieldid) == 1) {
        // Set the template field id if it was not set (earlier bug) - incremental id per form
        if (DB_getItem($_TABLES['formFields'],'tfid',"id='{$fieldid}'") == 0) {
            $query = DB_query("SELECT max(tfid) FROM {$_TABLES['formFields']} WHERE formid='$formid'");
            list ($maxtfid) = DB_fetchArray($query);
            $tfid = $maxtfid + 1;
            DB_query("UPDATE {$_TABLES['formFields']} SET tfid='{$tfid}' WHERE id='{$fieldid}'");
        }
        if ($fieldname == '') {
            // BL Note: Use tfid to set fieldname
            $fieldname = "{$CONF_FE['fieldtypes'][$type][0]}{$formid}_{$fieldid}";
        } else {  // Check and see if fieldtype has changed
            if (DB_getItem($_TABLES['formFields'], 'type',"id='$fieldid'") != $type) {
                $fieldname = "{$CONF_FE['fieldtypes'][$type][0]}{$formid}_{$fieldid}";
            }
        }
        $data  = "type='{$type}',field_name='{$fieldname}',fieldorder='{$fieldorder}',";
        $data .= "label='{$label}',style='{$style}',is_vertical='{$is_vertical}',";
        $data .= "field_attributes='{$field_attributes}', field_help='{$field_help}',";
        $data .= "field_values='{$field_values}', value_by_function='{$function_used}',";
        $data .= "validation='{$validation}',javascript='{$javascript}',is_internaluse='{$is_internaluse}',";
        $data .= "is_vertical='{$is_vertical}',is_reverseorder='{$is_reverseorder}',";
        $data .= "is_newline='{$is_newline}',is_mandatory='{$is_mandatory}',";
        $data .= "is_searchfield='{$is_searchfield}',is_resultsfield='{$is_resultsfield}',";
        $data .= "hidelabel='{$hidelabel}'";
        //echo "UPDATE {$_TABLES['formFields']} SET $data  WHERE id='$fieldid'";
        DB_query("UPDATE {$_TABLES['formFields']} SET $data  WHERE id='$fieldid'");

        $date = time();
        DB_query("UPDATE {$_TABLES['formDefinitions']} SET date='{$date}' WHERE id='$formid'");

        $GLOBALS['statusmsg'] = 'Record Updated';
    } else {
        COM_errorLog("Form Editor Plugin: Admin Error updating Field Record: $id for Form:$formid");
        $GLOBALS['statusmsg'] = 'Error adding or updating Record';
    }

    if (is_numeric($col_width)) {
        DB_query("UPDATE {$_TABLES['formFields']} SET col_width = '$col_width' WHERE id='$fieldid'");
    } else {
        DB_query("UPDATE {$_TABLES['formFields']} SET col_width = NULL WHERE id='$fieldid'");
    }

    if (is_numeric($col_padding)) {
        DB_query("UPDATE {$_TABLES['formFields']} SET col_padding = '$col_padding' WHERE id='$fieldid'");
    } else {
        DB_query("UPDATE {$_TABLES['formFields']} SET col_padding = NULL WHERE id='$fieldid'");
    }

    /* Now check and verify that only a max of XX fields have option for report enabled */
    $q = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE formid='$formid' AND is_resultsfield='1' ORDER BY fieldorder");
    $i = 1;
    while(list($id) = DB_fetchArray($q)) {
        if ($i > $CONF_FE['result_summary_fields'])
            DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield='0' WHERE id='$fieldid'");
        $i++;
    } // while

}

function deleteFieldRecord() {
    global $_CONF, $_TABLES, $fieldid;

    /* Need to check if any linked records have used this ID. If so don't delete record, just set status inactive */
    if (DB_count($_TABLES['formFields'],"id",$fieldid) == 1) {
        DB_query("DELETE FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        $GLOBALS['statusmsg'] = 'Form Field Deleted';
   } else {
        $GLOBALS['statusmsg'] = 'Error: Form Editor plugin, unable to delete field: $fieldid';
   }

}


/* MAIN CODE */

$redirectActions = array ('preview','report');
/* Show Header except for those actions that will be redirected */

if (!in_array($op,$redirectActions)) {
    echo COM_siteHeader();
    $formname = DB_getItem($_TABLES['formDefinitions'],'name',"id='$id'");
    if ($formname != '') {
        echo COM_startBlock("Form Field Editor: \"$formname\" Administration",'','nexform/admin/pluginheader.thtml',true);
    } else {
       echo COM_startBlock("Form Administration",'','nexform/admin/pluginheader.thtml',true);
    }    
}

switch ($op) {

    case "preview" :
        echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexform/preview.php?id='.$id);
        exit;
        break;
        
    case "report" :
        echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexform/report.php?formid='.$id);
        exit;
        break;
        
    case "copy" :
        copyFormRecord();
        echo displayFormRecords();
        break;

    case "editform" :
        echo editFormRecord($mode);
        break;

    case "saveform" :
        updateFormRecord($mode);
        echo editFormRecord('edit');
        break;

    case "delform" :
        deleteFormRecord();
        echo displayFormRecords();
        break;

    case "exportform" :
        echo displayFormRecords();
        echo COM_endBlock('nexform/admin/pluginfooter.thtml');
        echo COM_siteFooter();
        echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexform/export.php?form='.$id);
        exit;
        break;

    case "displayfields" :
        echo displayFieldRecords($id);
        break;

    case "editfield" :
        echo editFieldRecord($mode,$selectedtab);
        break;

    case "savefield" :
        updateFieldRecord($mode);
        echo displayFieldRecords($formid);
        break;

    case "savefieldnext" :
        updateFieldRecord($mode);
        $curfieldorder = DB_getItem($_TABLES['formFields'],'fieldorder', "id='$fieldid'");
        $sql = "SELECT id FROM {$_TABLES['formFields']} WHERE formid='$formid' AND fieldorder > $curfieldorder ORDER BY fieldorder LIMIT 1";
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            list ($fieldid) = DB_fetchArray($query);
        }
        echo editFieldRecord($mode,$selectedtab);
        break;

    case "delfield" :
        $formid = DB_getItem($_TABLES['formFields'],'formid',"id='{$fieldid}'");
        deleteFieldRecord();
        echo displayFieldRecords($formid);
        break;
        
    case "moveup" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            DB_query("UPDATE {$_TABLES['formFields']} SET fieldorder = fieldorder -11 WHERE id = '$fieldid'");
        }
        echo displayFieldRecords($formid,$fieldid);
        break;

    case "movedn" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            DB_query("UPDATE {$_TABLES['formFields']} SET fieldorder = fieldorder +11 WHERE id = '$fieldid'");
        }
        echo displayFieldRecords($formid,$fieldid);
        break;

    case "setmandatory" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            if ($chkmandatory[$fieldid]) {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '1' WHERE id = '$fieldid'");
                $statusmsg = "Field validation set to mandatory";
            } else {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '0' WHERE id = '$fieldid'");
                $statusmsg = "Field set to be optional";
            }
        }
        echo displayFieldRecords($id,$fieldid);
        break;

    case "setreport" :
        $query = DB_query("SELECT formid FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            /* Now check and verify that only a max of XX fields have option for report enabled */
            $is_resultsfield = ($chkreport[$fieldid]) ? 1 : 0;
            if ($is_resultsfield) {
                $q = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE formid='$id' AND is_resultsfield='1' ORDER BY fieldorder");
                if (DB_numRows($q) <= $CONF_FE['result_summary_fields']) {
                        DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield='1' WHERE id='$fieldid'");
                        $statusmsg = "Field report status enabled";
                } else {
                    $statusmsg = "Maximum report fields set - status not changed";
                }
            } else {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield = '0' WHERE id = '$fieldid'");
                $statusmsg = "Field report status disabled";
            }
        }
        echo displayFieldRecords($id,$fieldid);
        break;

    case "setnewline" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            if ($chknewline[$fieldid]) {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '1' WHERE id = '$fieldid'");
                $statusmsg = "Field newline status enabled";
            } else {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '0' WHERE id = '$fieldid'");
                $statusmsg = "Field newline status disabled";
            }
        }
        echo displayFieldRecords($id,$fieldid);
        break;

    case "setvertical" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            if ($chkvertical[$fieldid]) {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = 1 WHERE id = '$fieldid'");
                $statusmsg = "Field orientation set to vertical";
            } else {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = 0 WHERE id = '$fieldid'");
                $statusmsg = "Field orientaton set to horizontal";
            }
        }
        echo displayFieldRecords($id,$fieldid);
        break;

    case "setreverselabel" :
        $query = DB_query("SELECT id FROM {$_TABLES['formFields']} WHERE id='$fieldid'");
        if (DB_numRows($query) > 0) {
            if ($chkreverse[$fieldid]) {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '1' WHERE id = '$fieldid'");
                $statusmsg = "Label will be shown before field";
            } else {
                DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '0' WHERE id = '$fieldid'");
                $statusmsg = "Label will be shown after field";
            }
        }
        echo displayFieldRecords($id,$fieldid);
        break;


    default:
        echo displayFormRecords();
        break;
    }

echo COM_endBlock('nexform/admin/pluginfooter.thtml');
echo COM_siteFooter();


?>