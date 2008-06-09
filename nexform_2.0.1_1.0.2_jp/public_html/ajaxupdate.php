<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate.php                                                            |
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

require_once("../lib-common.php"); // Path to your lib-common.php
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code


$mode = COM_applyFilter($_GET['mode']);
$rec = COM_applyFilter($_GET['rec'],true);
$op = COM_applyFilter($_GET['op']);
$setting = COM_applyFilter($_GET['setting']);

if ($CONF_FE['debug']) {
    COM_errorLog("Ajaxupdate - nexform");
}

if ($mode == 'field') {

    if (DB_count($_TABLES['formFields'],'id',"$rec") == 1) {

        // Check and see if user has edit rights to this form
        $formid = DB_getItem($_TABLES['formFields'],'formid',"id='$rec'");
        $edit_group = DB_getItem($_TABLES['formDefinitions'],'perms_edit',"id='$formid'");
        if (!SEC_inGroup($edit_group)) {
            COM_accessLog("No access rights, attempt to edit form:$formid");
            print ('No access rights');
            exit();
        }

        switch ($op) {
            case 'newline':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set newline to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set newline to $setting for record: $rec");
                    }
                }
                break;

            case 'manditory':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field manditory to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field manditory to $setting for record: $rec");
                    }
                }
                break;

            case 'vertical':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field vertical layout to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field vertical layout to $setting for record: $rec");
                    }
                }
                break;

            case 'reverse':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field reverse order to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field reverse order to $setting for record: $rec");
                    }
                }
                break;

            case 'report':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field report mode to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field report mode to $setting for record: $rec");
                    }
                }
                break;
        }

        $retval = "<result>";
        $retval .= "<mode>$mode</mode>";
        $retval .= "<record>$rec</record>";
        $retval .= "<property>$op</property>";
        $retval .= "<value>$setting</value>";
        $retval .= "</result>";

    }
} elseif ($mode == 'file') {
    $field = COM_applyFilter($_GET['field']);
    $fieldname = COM_applyFilter($_GET['fieldname']);
    $filecount = COM_applyFilter($_GET['filecount']) - 1;
    $params = explode ('_', $fieldname);
    $formid = str_replace('frm', '', $params[1]);

    // Check and see if user has edit rights to this form
    $edit_group = DB_getItem($_TABLES['formDefinitions'],'perms_edit',"id='$formid'");
    if (!SEC_inGroup($edit_group)) {
        COM_accessLog("No access rights, attempt to edit form:$formid");
        print ('No access rights');
        exit();
    }
    $resultid = DB_getItem($_TABLES['formResData'],'result_id',"id='$rec'");
    $isDynamic = DB_getItem($_TABLES['formResData'],'is_dynamicfield_result',"id='$rec'");
    if ($isDynamic == 1) {
        $sourceFormid = DB_getItem($_TABLES['formFields'],'formid',"id='{$params[2]}'");
    }
    else {
        $sourceFormid = DB_getItem($_TABLES['formResults'],'form_id',"id='$resultid'");
    }

    
    // Unique condition where the form may contain temporary html representing images for files that
    // are from a [pullform] function and represent files from a source form.
    // Only a condition until the form is saved and result records created
    // In the meantime, a user may want to delete one of the represented files
    
    if ($sourceFormid == $formid) {
        $phantomRec = 0;
        DB_query("DELETE FROM {$_TABLES['formResData']} WHERE id='$rec'");
        $query = DB_query("SELECT id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid' AND field_id='$field'");
    } else {
        $phantomRec = $rec;
        // We are showing files for a different form - as a result of the [pullform] feature
        // Need to get the source form result information
        
        // Get original [pullform autotag and decode
        $autotag = DB_getItem($_TABLES['formFields'],'field_values',"id='$field'");
        $autotag = str_replace('[','',$autotag);
        $autotag = str_replace(']','',$autotag);
        $atag = explode(':',$autotag);
        $formparms = explode(',',$atag[1]);
        $sourceFieldId = DB_getItem($_TABLES['formFields'],'id',"formid={$formparms[0]} AND tfid={$formparms[1]}");
        if ($sourceFieldId > 0) {
            $query = DB_query("SELECT id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid' AND field_id='$sourceFieldId'");
        } else {
            // Hum .. Dont' expect this but should trap for an invalid source field id
            if ($CONF_FE['debug']) {
                COM_errorLog("Form update - delete mfile - of a result that does not yet exist - temp result from [pullform]. Form:$formid, field:$field, rec:$rec");
            }
            $query = DB_query("SELECT id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid' AND field_id='$field'");
        }
    }

    $template = new Template($_CONF['path_layout'] . 'nexform');
    $template->set_file ('mfile', 'mfile_field.thtml');
    $template->set_var('field_id',$field);
    $template->set_var('form_id',$formid);

    $field_html = '';
    if (DB_numRows($query) > 0) {
        $field_html = '<table border="0"><tr style="vertical-align:top;">';
        $usetable = true;
        $i = 0;
    }


    //notes: add security check for delete file, return fieldname in the ajax return,
    //add fieldname in 2 function calls ajaxupdate.php and mfile_upload_ajax.php
    while (list ($rec,$field_value) = DB_fetchArray($query)) {
        if ($rec != $phantomRec) {
            $field_html .= '<td align="left">';
            $filename = explode(':',$field_value);
            if (!empty($field_value)) {
                $field_html .= '<table border="0"><tr><td align="left">';
                $field_html .= "<a href=\"{$CONF_FE['public_url']}/download.php?id=$rec\" target=\"_new\">";
                $field_html .= "<img src=\"{$CONF_FE['image_url']}/document_sm.gif\" border=\"0\">{$filename[1]}</a>&nbsp;";
                $field_html .= "<a href=\"#\" onClick='ajaxDeleteFile($field,$rec,\"$fieldname\"); return false;'>";
                $field_html .= "<img src=\"{$CONF_FE['image_url']}/delete.gif\" border=\"0\"></a>&nbsp;";
                if ($sourceFormid != $formid) {
                    $field_html .= "<input type=\"hidden\" name=\"lfile_frm{$formid}_{$field}[]\" value=\"{$field_value}\">";
                }
                $field_html .= "</td></tr></table>";
            } else {
                $field_html = 'N/A&nbsp;';
            }
            $field_html .= '</td>';
            $i++;

            $field_html .= '</tr><tr style="vertical-align:top;">';
        }
    }
    if ($usetable) $field_html .= '</tr></table>';

    $file_input1 = "<input type=\"file\" id=\"{$fieldname}\" name=\"";
    $file_input2 = "{$fieldname}[]";
    $file_input3 = '">';
    $mfile_count_name = str_replace('_', '-', $fieldname);

    $template->set_var('mfield_html', $field_html);
    $template->set_var ('upload_field_name',$fieldname);
    $template->set_var ('mfile_count',$filecount);
    $template->set_var ('mfile_count_name',$mfile_count_name);
    $template->set_var ('file_input1',$file_input1);
    $template->set_var ('file_input2',$file_input2);
    $template->set_var ('file_input3',$file_input3);
    $template->parse ('output', 'mfile');
    $html = $template->finish ($template->get_var('output'));

    $html = htmlentities ($html);

    $retval = "<result>";
    $retval .= "<field>$field</field>";
    $retval .= "<fieldname>$fieldname</fieldname>";
    $retval .= "<content>$html</content>";
    $retval .= "</result>";

} else {
    $retval = "<result>";
    $retval .= "<mode></mode>";
    $retval .= "<record></record>";
    $retval .= "<property></property>";
    $retval .= "<value></value>";
    $retval .= "</result>";
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");

print $retval;
?>