<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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

// Include any functions for custom nexform tags
if (file_exists("{$_CONF['path']}plugins/nexform/customtags.php")) {
    include ('customtags.php');
}

$forms_used = array();
$dynamicforms_used = array();

function nexform_getlinkedform($formid,&$list,$linkedforms) {
    global $_TABLES;

    $query = DB_query("SELECT id,before_formid,after_formid FROM {$_TABLES['formDefinitions']} WHERE id='$formid'");
    list ($id,$before,$after) = DB_fetchArray($query);
    if (!in_array($id, $list)) {
        if ($before > 0 AND ($linkedforms == 'all' OR $linkedforms == 'beforeonly') ) {
            $list = nexform_getlinkedform($before,$list,$linkedforms);
            if (!in_array($formid, $list)) {
                array_push($list,$formid);
            }
            if ($after > 0 AND ($linkedforms == 'all' OR $linkedforms == 'afteronly') ) {
                $list = nexform_getlinkedform($after,$list,$linkedforms);
            }
        } else {
          if (!in_array($formid, $list)) {
              array_push($list,$formid);
          }
          if ($after > 0 AND ($linkedforms == 'all' OR $linkedforms == 'afteronly') ) {
            $list = nexform_getlinkedform($after,$list,$linkedforms);
          }
      }
    }
    return $list;
}

function nexform_getdynamicforms($formid,&$list) {
    global $_TABLES;

    if (!in_array($formid, $list)) {
        $list[] = $formid;
    }

    $res = DB_query("SELECT field_values FROM {$_TABLES['formFields']} WHERE type='dynamic' AND formid=$formid");
    while (list ($value) = DB_fetchArray($res)) {
        $v_arr = explode (',', $value);
        $fid = $v_arr[0];

        if (!in_array($fid, $list)) {
            $list[] = $fid;
            $list = nexform_getdynamicforms($fid,$list);
        }
    }

    return $list;
}


/**
* nexform_showFormFields: Main function that generates the HTML for the fields
* Called from function nexform_showform()
*
* @param        string         $formid          Required: form id to generate html for
* @param        object         $template        Required: Passed by reference the template object being used
* @parm         string         $resultid        Optional: Result Record ID if we need to show the posted results
* @param        string         $mode            Optional: Valid values are 'view,edit','print'
* @param        string         $linked_formnum  Optional: Used when multiple forms are linked
*                                               Example: form 1 of 3, this function will be called 3 times, this parm will be 1 . 2 and then 3
*                                               Used to not show multiple submit buttons but allow a subform to be called on it's own if need.
* $param        boolean        $dynamic         Optional: Set to integer value to indicate dynamic form instance.
* @return       mixed          returns formatted HTML for all fields
*
**/

function nexform_showFormFields($formid,$form_action,&$template,$resultid=0,$mode='view',$linked_formnum=1,$dynamic='') {
    global $_CONF,$_TABLES,$_STATES,$CONF_FE,$LANG_FEMSG;

    $lines = 5;
    $i = 1;     // Used to count the number of fields
    $fields  = 'id,formid,tfid,field_name,type,fieldorder,label,style,col_width,col_padding,';
    $fields .= 'is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,field_help,';
    $fields .= 'field_attributes,field_values,value_by_function,validation,javascript,is_internaluse,hidelabel';

    $fieldquery = DB_query("SELECT $fields FROM {$_TABLES['formFields']} WHERE formid='$formid' ORDER BY fieldorder");
    $prevnewline = false;
    $is_lastfield = false;
    $is_firstfield = true;
    $cssid = 1;

    $groupEditAccess = DB_getItem($_TABLES['formDefinitions'],'perms_edit', "id='$formid'");

    if ($mode != 'print' AND DB_getItem($_TABLES['formDefinitions'],'show_mandatory_note',"id='{$formid}'") == 1) {
        $show_mandatory = true;
    } else {
        $show_mandatory = false;
    }

    if ($CONF_FE['debug']) {
        COM_errorLog("Dynamic Field id: $dynamic");
    }

    /* Un-encode the fieldset definitions and display them as records if any exist */
    $ofieldset  = DB_getItem($_TABLES['formDefinitions'],'fieldsets',"id='{$formid}'");
    if (trim($ofieldset != '')) {
        $afieldsets = unserialize($ofieldset);  // Array of fieldset definitions
    }
    $fieldset_mode = false;

    while( list (
        $fieldID,$formID,$tfid,$field_name,$type,$fieldorder,$label,$style,$col_width,$col_padding,
        $is_vertical,$newline,$mandatory,$searchfield,$reportfield,$is_reverseorder,$field_help,$field_attributes,
        $field_value,$use_function,$validation,$javascript,$is_internaluse,$hidelabel)  = DB_fetchArray($fieldquery) )
    {
        if ( $is_internaluse == 0 OR ($is_internaluse == 1 AND SEC_inGroup($groupEditAccess)) ) {
            $javascript = nexform_replaceFieldTags($formid, $dynamic, $javascript);

            $label = stripslashes($label);
            $field_attributes = stripslashes($field_attributes);
            $nextfieldquery = DB_query("SELECT is_newline,type FROM {$_TABLES['formFields']} WHERE formid='$formid' AND fieldorder > $fieldorder  AND type NOT IN ('cancel','submit','hidden') ORDER BY fieldorder LIMIT 1");
            if (DB_numRows($nextfieldquery) == 0) {
                $is_lastfield = true;
            }
            list ($nextfield_linetype,$nextfield_type) = DB_fetchArray($nextfieldquery);
            //echo "<br>Field: $label, Order:$fieldorder => Current linetype:$newline and Next linetype:$nextfield_linetype";
            //echo "<br>Fieldvalue: $field_value, ResultID:$resultid";

            if ( $newline AND $nextfield_linetype OR ($parsedLastRecordline AND $is_lastfield)) {
                $recStyle = 'recstyle1';
            } else {
                $recStyle = 'recstyle2';
            }

            $template->set_var ("cssid", $cssid);
            if ($is_firstfield ) {
               $parsedLastRecordline = true;
            }

            if (is_numeric($col_width)) {
                $template->set_var('field_width', "width:{$col_width}%;");
            } elseif (isset($CONF_FE['field_defaultspacing']) AND $CONF_FE['fiel1_defaultspacing'] > 0) {
                $template->set_var('field_width', "width:{{$CONF_FE['field_defaultspacing']}}%;");
            } else {
                $template->set_var('field_width', '');
            }

            if (is_numeric($col_padding)) {
                $template->set_var('cell_padding', $col_padding);
            } else {
                $template->set_var('cell_padding', $CONF_FE['field_defaultrightpadding']);
            }

            if (trim($field_help) != '') {
                $template->set_var ('help_message',$field_help);
                $template->parse('field_help','fieldhelp');

            } else {
                $template->set_var('field_help','');
            }

            if ($is_lastfield or ($is_firstfield and $nextfield_linetype)) {
                $parseRecord = 1;
                $cssid = ($cssid == 2) ? 1: 2;
            } elseif ( (!$newline AND !$nextfield_linetype) OR ($newline AND !$nextfield_linetype) ) {
                $parseRecord = 0;
            } else {
                $parseRecord = 1;
                $cssid = ($cssid == 2) ? 1: 2;
            }

            // Check if this field is not part of a fieldset definition
            if (!$fieldset_mode) {
                //echo "<br>Field $tfid. Set fset template vars to blanks";
                $template->set_var('fset_begin','');
                $template->set_var('fset_end','');
            }

            if (is_array($afieldsets)) {
                $lines += 1;
                foreach ($afieldsets as $fset_id => $fieldset) {
                    $fset = explode('::',$afieldsets[$i]);
                    if ($mode != 'print') {
                        if ($tfid == $fieldset['begin']) {
                            $template->set_var('fset_label',$fieldset['label']);
                            $template->parse('fset_begin','fieldsetbegin');
                            $fieldset_mode = true;
                        }
                        if ($tfid == $fieldset['end']) {
                            $template->parse('fset_end','fieldsetend');
                        }
                    } else {
                        if ($tfid == $fieldset['begin']) {
                            $template->set_var('fset_label',$fieldset['label']);
                            $template->parse('fset_begin','printfieldsetbegin');
                            $fieldset_mode = true;
                        }
                        if ($tfid == $fieldset['end']) {
                            $template->parse('fset_end','printfieldsetend');
                        }
                    }
                }
            }

           if ($is_vertical == 1) {
                if ($recStyle == 'recstyle1') {
                    if ($is_reverseorder) {
                        $fieldStyle = 'fieldstyle2R';
                    } else {
                        $fieldStyle = 'fieldstyle2';
                    }
                } else {
                    if ($is_reverseorder) {
                        $fieldStyle = 'fieldstyle3R';
                    } else {
                        $fieldStyle = 'fieldstyle3';
                    }
                }
          } else {
                if ($is_reverseorder) {
                    $fieldStyle = 'fieldstyle1R';
                } else {
                    $fieldStyle = 'fieldstyle1';
                }
          }

          if ($type == 'mfile') {
            //$fieldStyle = 'fieldstyle4';
            if ($resultid == 0 OR $mode == 'edit') {
                $template->set_var('showfilectl', '');
            } else {
                $template->set_var('showfilectl', 'none');
            }
          } elseif ($type == 'mtxt') {
            if ($is_vertical == 1) {
                $fieldStyle = 'fieldstyle5R';
            } else {
                $fieldStyle = 'fieldstyle5';
            }
            if ($resultid == 0 OR $mode == 'edit') {
                $template->set_var('showfieldctl', '');
            } else {
                $template->set_var('showfieldctl', 'none');
            }
          }
           //echo "<br>FormID: $formid, Field: $fieldID, Name:$field_name, Label: $label,  Type:$type, value:$field_value,Next Fieldtype:$nextfield_type,parseRecord is: $parseRecord";
           //echo "<br>&nbsp;&nbsp;Template ID:$tfid, Type:$type, fieldStyle:$fieldStyle, recStyle:$recStyle, ";
           //echo " parseRecord is: $parseRecord, parsedLastRecord:$parsedLastRecordline, ReverseLabel:$is_reverseorder ";
           //echo "<br>&nbsp;&nbsp;FieldID:$fieldID, FieldAttributes: $field_attributes,fieldset_mode:$fieldset_mode";
           //echo "Labelclass:" . $CONF_FE['fieldstyles'][$style][1];
            /*if ($mode == 'print' AND !is_array($dynamic)) {
                $resultid = COM_applyFilter($_GET['result'], true);

                if ($type == 'file' OR $type == 'mfile') {
                    if ($mode == 'print') {
                        $tmpid = COM_applyFilter($_GET['rid'], true);
                        $resultid = ($tmpid != 0) ? $tmpid:$resultid;
                    }
                }
            }*/

            if ($type != 'hidden' AND $type != 'heading') {
                $template->set_var ('hidelabel','');
                if ($mandatory) {
                    if (!$hidelabel) {
                        if ($show_mandatory) {
                            $template->set_var ('label', "$label<span style=\"color:red;padding-left:5px;\">*</span>");
                        } else {
                            $template->set_var ('label', "$label");
                        }
                    } else {
                        $template->set_var ("label", '');
                        $template->set_var ('hidelabel','none');
                    }
                    if ($type == 'radio' OR $type == 'checkbox' OR $type == 'multicheck') {
                        $validatetag = "required=\"1\" Realname=\"$label\"";
                    } elseif ($type == 'select') {
                        $validatetag = "required=\"1\" exclude=\"-1\" $validation minlength=\"1\" Realname=\"$label\"";
                    } elseif ($type == 'mfile') {
                        $validatetag = "required=\"1\" callback=\"validate_mfile_field\"";
                    } else {
                        $validatetag = "required=\"1\" $validation minlength=\"1\" Realname=\"$label\"";
                    }
                } else {
                    $validatetag = '';
                    if (!$hidelabel) {
                        $template->set_var ('label', "$label");
                    } else {
                        $template->set_var ("label", '');
                        $template->set_var ('hidelabel','none');
                    }
                }
                $template->set_var ('labelclass',$CONF_FE['fieldstyles'][$style][1]);
                $field_attributes = str_replace(',',' ',$field_attributes);
            } else {
                $template->set_var ('labelclass','');
                $template->set_var ('label', '');
            }
                if ($mode == 'print' AND $CONF_FE['fieldstyles'][$style][1] == 'frm_label1') {
                    $template->set_var('cell_padding', '20');
                }

            if (DB_getItem($_TABLES['formDefinitions'], 'post_method', "id='$formid'") == 'posturl') {
                $customfieldmode = true;
            } else {
                $customfieldmode = false;
            }

            $field_html = '';
            if ($type != 'hidden' AND $resultid > 0) {   // Viewing detail of a previous posted form data - retrieve the field value
                switch($type){
                    case 'textarea1':
                    case 'textarea2':
                        $field_value = DB_getItem($_TABLES['formResText'], 'field_data', "result_id='$resultid' AND field_id='$fieldID'");
                        $field_value = stripslashes($field_value);
                        break;
                    case 'file':        // generate link to uploaded file
                        $field_value = DB_getItem($_TABLES['formResData'], 'field_data', "result_id='$resultid' AND field_id='$fieldID'");
                        $filename = explode(':',$field_value);
                        //echo "<br>fieldid:$fieldID, result id:$resultid, field_value:$field_value";
                        if (!empty($field_value)) {
                            $field_html = "<a href=\"{$CONF_FE['public_url']}/download.php?id=$fieldID&rid=$resultid\" target=\"_new\">{$filename[1]}</a>";
                        } else {
                            $field_html = 'N/A';
                        }
                        break;
                    case 'mfile':        // generate link to uploaded file
                        $mquery = DB_query("SELECT id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid' AND field_id='$fieldID'");
                        if ($CONF_FE['debug']) {
                            COM_errorLOG("Displaying form result:$resultid - field:$fieldID");
                        }
                        $usetable = false;
                        if (DB_numRows($mquery) > 0) {
                            if ($CONF_FE['debug']) {
                                COM_errorLog("Displaying form result:$resultid - found files");
                            }
                            $field_html = '<table border="0"><tr style="vertical-align:top;">';
                            $usetable = true;
                            $i = 0;
                        }

                        if (is_array($dynamic)) {
                            $inputfilename = "mfile_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $inputfilename = "{$field_name}";
                        } else {
                            $inputfilename = "mfile_frm{$formID}_{$fieldID}";
                        }
                        while (list ($rec,$field_value) = DB_fetchArray($mquery)) {
                            $field_html .= '<td align="left">';
                            $filename = explode(':',$field_value);
                            if (!empty($field_value)) {
                                $field_html .= '<table border="0"><tr><td align="left">&nbsp;';
                                $field_html .= "<a href=\"{$CONF_FE['public_url']}/download.php?id=$rec\" target=\"_new\">";
                                $field_html .= "<img src=\"{$CONF_FE['image_url']}/document_sm.gif\" border=\"0\">{$filename[1]}</a>&nbsp;";
                                if ($mode == 'edit') {
                                    $field_html .= "<a href=\"#\" onClick='ajaxDeleteFile($fieldID,$rec,\"$inputfilename\"); return false;'>";
                                    $field_html .= "<img src=\"{$CONF_FE['image_url']}/delete.gif\" border=\"0\"></a>&nbsp;";
                                }
                                $field_html .= "</td></tr></table>";
                            } else {
                                $field_html = 'N/A&nbsp;';
                            }
                            $field_html .= '</td>';
                            $i++;

                            $field_html .= '</tr><tr style="vertical-align:top;">';
                        }
                        $mfile_count = $i;
                        if ($usetable) $field_html .= '</tr></table>';

                        break;
                    case 'submit':
                    case 'cancel':
                        break;
                    default:
                        // Check if custom field - if so then data is in text field table
                        if (array_key_exists($type,$CONF_FE['customfieldmap'])) {
                            $field_value = DB_getItem($_TABLES['formResText'], 'field_data', "result_id='$resultid' AND field_id='$fieldID'");
                        } else {
                            $field_value = DB_getItem($_TABLES['formResData'], 'field_data', "result_id='$resultid' AND field_id='$fieldID'");
                        }

                        break;
                } // switch
            }

            /* Code to handle assigning a value to field by Function */
            $function_datavalues = '';
            $setresult = false;

            if ($use_function) {
                if ($resultid > 0) {
                    $fvalue = DB_getItem($_TABLES['formFields'], "field_values", "id='$fieldID'");
                    // Check if autotag is being used for value
                    if (strpos($fvalue,'[' === FALSE)) {
                        $function = explode(':',$fvalue);
                        if (function_exists($function[0])) {
                            $function_datavalues = $function[0]($function[1],$fieldID);
                        }
                    } else {   // - Assume autotag is being used = if [ is in value like the autotag format
                        $function_datavalues = nexform_getAutotagValues($fvalue,$type,$field_value,$fieldID);
                    }

                } else {
                    // Check if autotag is being used for value - assume so if [ is in value like the autotag format
                    if (strpos($field_value,'[') === FALSE) {
                        /* Not an autotag so assume a function has been defined with parms in ()
                         * Example: userprofile(name)           // Show the user's name
                         *          userprofile(location)       // Show the user's location
                         *          capitalreq(deptcodes)       // Show a list of department billing codes
                         * Any number of parms can be in the definition - up to your function to sort out
                        */
                        $function = explode('(',$field_value);
                        $function[1] = str_replace(')','',$function[1]);  // Strip out trailing ) in the parms value
                        if (function_exists($function[0])) {
                            $function_datavalues = $function[0]($function[1],$fieldID);
                        }
                    } else {  // - Assume autotag is being used = if [ is in value like the autotag format
                        $function_datavalues = nexform_getAutotagValues($field_value,$type,'',$fieldID);
                    }
                }
                if (!is_array($function_datavalues)) {
                    $function_datavalues = stripslashes($function_datavalues);
                }
            }

            $is_firstfield = false;
            if ($mode == 'print') {
                if (!is_array($dynamic)) {
                    $resultid = COM_applyFilter($_GET['result'], true);
                }
                //if not saved in database yet - show current values
                if ($resultid == 0) {
                    switch ($type) {
                        case 'date1':
                            if ($_POST[$field_name] == '') {
                                $field_name = str_replace('da1_ftm', 'da1_frm', $field_name);
                            }
                            $field_value = $_POST[$field_name];
                            break;

                        case 'mtxt':
                            $field_value = implode('|', $_POST[$field_name]);
                            break;

                        case 'multicheck':
                            if (get_magic_quotes_gpc()) {
                                if (is_array($_POST[$field_name])) {
                                    foreach ($_POST[$field_name] as $key => $value) {
                                        $_POST[$field_name][$key] = stripslashes($_POST[$field_name][$key]);
                                    }
                                }
                            }
                            break;

                        case 'file':
                        case 'mfile':
                            $tmpid = COM_applyFilter($_GET['rid'], true);
                            $resultid = ($tmpid != 0) ? $tmpid:$resultid;
                            break;

                        default:
                            if (strpos($field_name, 'cust') !== false) {
                                foreach ($_POST as $var => $value) {
                                    $parts = explode('_',$var);
                                    $fieldtype = $parts[0];
                                    $field_id = (int) $parts[2];

                                    if (is_array($value) AND $fieldtype == 'cust') {
                                        $subfield = $parts[3];
                                        foreach ($value as $subfield_value) {
                                            $subfield_value = stripslashes($subfield_value);
                                            $custom_fields[$field_id][$subfield][] = $subfield_value;
                                        }
                                    }
                                }
                                if (count($custom_fields) > 0)  {
                                    foreach ($custom_fields as $field_id => $value) {
                                        $_POST[$field_name] = serialize($value);
                                    }
                                }
                            }
                            else {
                                $field_value = $_POST[$field_name];
                            }
                            break;
                    }
                    $field_value = stripslashes($field_value);
                }
            }

            switch($type) {

                case 'hidden':
                    if (is_array($dynamic)) {
                        $fieldname = "hid_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = $field_name;
                    } else {
                        $fieldname = "hid_frm{$formID}_$fieldID";
                    }
                    $field_html = "<input type=\"hidden\" id=\"$fieldname\" name=\"$fieldname\" value=\"$field_value\" >";

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'date1':
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "da1_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "da1_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"text\" id=\"$fieldname\" name=\"$fieldname\" ";
                        if ($resultid == 0 AND $function_datavalues != '') {
                            $field_html .= "value=\"$function_datavalues\" ";
                        } else {
                            $field_html .= "value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['date1']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                        }
                        $field_html .= '>';
                    } else {
                        $field_html .= $field_value;
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'date2':    // Date Field with Popup DHTML Calendar
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "da2_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "da2_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"text\" id=\"$fieldname\" name=\"$fieldname\" ";
                        if ($resultid == 0 AND $function_datavalues != '') {
                            $field_html .= "value=\"$function_datavalues\" ";
                        } else {
                            $field_html .= "value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['date2']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                            $field_html .= '>';
                        }
                        else {
                            $field_html .= "onMouseOver=\"setupCalendar('$fieldname', '%m/%d/%Y', false);\" >";
                        }
                    } else {
                        $field_html .= $field_value;
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'datetime':    // Date Field with Popup DHTML Calendar
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "time_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "time_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"text\" id=\"$fieldname\" name=\"$fieldname\" ";
                        if ($resultid == 0 AND $function_datavalues != '') {
                            $field_html .= "value=\"$function_datavalues\" ";
                        } else {
                            $field_html .= "value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['date2']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                            $field_html .= '>';
                        }
                        else {
                            $field_html .= "onMouseOver=\"setupCalendar('$fieldname', '%m/%d/%Y %H:%M', true);\" >";
                        }
                    } else {
                        $field_html .= $field_value;
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'text':
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "txt_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "txt_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"text\" id=\"$fieldname\" name=\"$fieldname\" ";
                        if ($function_datavalues != '') {
                            $function_datavalues = trim($function_datavalues);
                            $field_html .= "value=\"$function_datavalues\" ";
                        } else {
                            $field_html .= "value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['text']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                        }
                        $field_html .= '>';
                    } else {
                        if (($function_datavalues != '') AND ($mode != 'print') AND ($resultid != 0)) {
                            $field_value = $function_datavalues;
                        }
                        $field_html = $field_value;
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;


                case 'mtxt':
                    $template->set_var ('field_id',$fieldID);  // Extra variables for mtext field template
                    $template->set_var ('sform_id',$formID);
                    $template->set_var ('sfield_id','0');
                    if (is_array($dynamic)) {
                        $fieldname = "mtxt_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}[]";
                        $mtxt_id = "mtxt_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        $template->set_var ('field_id',$fieldID);  // Extra variables for mtext field template
                        $template->set_var ('sform_id',$dynamic[0]);
                        $template->set_var ('sfield_id',$dynamic[1]);
                        $template->set_var ('mtxt_instance',$dynamic[2]);
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = "{$field_name}[]";
                        $mtxt_id = "{$field_name}";
                    } else {
                        $fieldname = "mtxt_frm{$formID}_{$fieldID}[]";
                        $mtxt_id = "mtxt_frm{$formID}_{$fieldID}";
                    }

                    // Need to create as many text fields as there are data elements in the varible text field value
                    $mtxt_values = explode('|',$field_value);
                    $field_html = '';
                    $i = 0;
                    $closetable = false;
                    foreach ($mtxt_values as $val) {
                        if ($i > 0) {
                            $field_html .= '<td width="120">';
                        }
                        $field_html .= "<input type=\"text\" id=\"{$mtxt_id}_{$i}\" name=\"$fieldname\" size=\"20\" ";
                        $field_html .= "value=\"$val\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['text']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                        }
                        $field_html .= '>';
                        if ($i > 0) {
                            $field_html .= '</td>';
                        }
                        if ($i%4 == 3) {
                            $field_html .= '</tr><tr>';
                            $closetable = true;
                        }
                        $i++;
                    }
                    if ($closetable) $field_html .= '</tr>';

                    $template->set_var ('mtxt_fieldname',$mtxt_id);
                    $template->set_var ('mtxt_counter',$i);
                    $template->set_var ('mtxt_counter_id',str_replace('_', '-', $mtxt_id));
                    $template->set_var ('field_id',$fieldID);  // Extra variables for mtext field template
                    $template->set_var ('form_id',$formID);

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;


                case 'select':
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "sel_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "sel_frm{$formID}_$fieldID";
                        }
                        $field_html = "<select id=\"$fieldname\" name=\"$fieldname\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['select']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' DISABLED';
                        }
                        $field_html .= '>';
                        $field_html .= '<option value="-1">'.$LANG_FEMSG[1].'</option>';

                        // Check if this field is using a function to generate the select options
                        // It could be a simple list or an array of options example [alist:xx]
                        if ($function_datavalues != '' AND is_array($function_datavalues)) {
                            // Check and see if passed in array contains a key to indicate the selected item
                            if (array_key_exists('selected',$function_datavalues)) {
                                $field_value = $function_datavalues['selected'];
                                // Now remove this item from the array of results to use an select options
                                unset($function_datavalues['selected']);
                                $setresult = true;
                            } else {
                                $setresult = false;
                            }
                            // Jan 9/2006 (BL) Updated to now add the class=xxx for the dynamic select feature
                            $dynamicSelect = false;
                            // Cycle thru the array of values to set the dropdown options
                            foreach ($function_datavalues as $optval => $optitem) {
                                $optitem = explode(',', $optitem);
                                $field_html .= '<option value="'.$optval.'"';
                                if (isset($optitem[1])) {
                                    $field_html .= ' ' .$optitem[1];
                                    $dynamicSelect = true;
                                }
                                if (($resultid > 0 OR $setresult) AND $field_value == $optval ) {
                                    $field_html .= ' SELECTED ';
                                }
                                $field_html .= '>'.$optitem[0].'</option>';

                            }
                            if ($dynamicSelect) {
                                $CONF_FE['dynamicSelect'] = true;
                                $template->parse('dynamic_select_function_calls','dselect_field',true);
                            }

                        } else { // List of options separated by commas
                            if ($function_datavalues != '') {
                                $options = explode(',',$function_datavalues);
                            } else {
                                $default_value = DB_getItem($_TABLES['formFields'], 'field_values', "id='$fieldID'");
                                $options = explode(',',$default_value);
                            }
                            foreach ($options as $option) {
                                if ($resultid > 0 AND $field_value == $option) {
                                    $field_html .= '<option value="'.$option.'" SELECTED>'.$option.'</option>';
                                } else {
                                    $field_html .= '<option value="'.$option.'">'.$option.'</option>';
                                }
                            }
                        }
                        $field_html .= '</select>';

                    } elseif (trim($field_value) != '') {
                        if ($function_datavalues != '' AND is_array($function_datavalues)) {
                            // Filter out class attribute in value if this is a dynamic select field
                            if (strpos($function_datavalues[$field_value],'class=') > 0) {
                                $fieldDisplayValue = explode(',',$function_datavalues[$field_value]);
                                $field_html .= $fieldDisplayValue[0];
                            } else {
                                $field_html .=  $function_datavalues[$field_value];
                            }
                        } else {
                            $field_html .= $field_value;
                        }

                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'passwd':
                    if (is_array($dynamic)) {
                        $fieldname = "pwd_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = $field_name;
                    } else {
                        $fieldname = "pwd_frm{$formID}_$fieldID";
                    }
                    $field_html = "<input type=\"password\" id=\"$fieldname\" name=\"$fieldname\" value=\"$field_value\" ";
                    if (!empty($field_attributes)) {
                        $field_html .= html_entity_decode($field_attributes);
                    } else {
                        $field_html .= html_entity_decode($CONF_FE['defaultattributes']['passwd']);
                    }
                    $field_html .= " $validatetag ";
                    $field_html .= html_entity_decode($javascript);
                    if ($mode == 'review') {
                        $field_html .= ' READONLY';
                    }
                    $field_html .= '>';

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'file':
                    if ($resultid == 0)  { // Don't show hidden fields when viewing results
                        if (is_array($dynamic)) {
                            $fieldname = "file_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "file_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"file\" id=\"$fieldname\" name=\"$fieldname\" value=\"$field_value\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['file']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript) . '>';
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                         $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'mfile':
                    if (is_array($dynamic)) {
                        $fieldname = "mfile_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}[]";
                        $file_id = "mfile_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = "{$field_name}[]";
                        $file_id = "{$field_name}";
                    } else {
                        $fieldname = "mfile_frm{$formID}_{$fieldID}[]";
                        $file_id = "mfile_frm{$formID}_{$fieldID}";
                    }
                    if ($resultid == 0)  {
                        if ($function_datavalues != '') {
                            $field_html = $function_datavalues;
                        }
                    }
                    $file_input1 = "<input type=\"file\" name=\"";
                    $file_input2 = $fieldname;
                    $file_input3 = "\" id=\"$file_id\" value=\"$field_value\" ";
                    if (!empty($field_attributes)) {
                        $file_input3 .= html_entity_decode($field_attributes);
                    } else {
                        $file_input3 .= html_entity_decode($CONF_FE['defaultattributes']['file']);
                    }
                    $file_input3 .= " $validatetag ";
                    $file_input3 .= html_entity_decode($javascript) . '>';

                    $mfile_count = ($mfile_count == '') ? 0:$mfile_count;

                    $template->set_var ('upload_field_name',str_replace('[]', '', $fieldname));
                    $template->set_var ('file_input1',$file_input1);
                    $template->set_var ('file_input2',$file_input2);
                    $template->set_var ('file_input3',$file_input3);
                    $template->set_var ('field_id',$fieldID);
                    $template->set_var ('mfile_count',$mfile_count);
                    $template->set_var ('mfile_count_name',str_replace('_', '-', $file_id));
                    $template->set_var ('form_id',$formID);
                    $template->set_var ('mfield_html', "$field_html");
                    if ($mode == 'print' OR $mode == 'review') {
                        $template->set_var ('show_addremove', 'none');
                    }
                    else {
                        $template->set_var ('show_addremove', '');
                    }
                    $template->parse('field','mfile_field');

                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'checkbox':
                    if (is_array($dynamic)) {
                        $fieldname = "chk_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = $field_name;
                    } else {
                        $fieldname = "chk_frm{$formID}_$fieldID";
                    }

                    //support pullform values
                    if ($function_datavalues != '') {
                        $field_value = $function_datavalues;
                    }

                    $default_value = DB_getItem($_TABLES['formFields'], 'field_values', "id='$fieldID'");
                    if ($default_value == '') {
                        $default_value = 1;
                    }
                    $field_html = "<input type=\"checkbox\" id=\"$fieldname\" name=\"$fieldname\" value=\"1\"";
                    if ($field_value == '1' OR $field_value == 'yes') {
                        $field_html .= ' CHECKED=CHECKED ';
                    }
                    $field_html .= " $validatetag ";
                    $field_html .= html_entity_decode($field_attributes).' '.html_entity_decode($javascript);
                    if ($mode == 'review') {
                        $field_html .= ' DISABLED';
                    }
                    $field_html .= '>';

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'multicheck':
                    if (is_array($dynamic)) {
                        $fieldname = "mchk_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}[]";
                        $mcheck_id = "mchk_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}_%s";
                    } elseif ($customfieldmode AND $field_name != '') {
                        if (strpos('[]',$field_name) == 0) {
                            $fieldname = "{$field_name}[]";
                            $mcheck_id = "{$field_name}_%s";
                        } else {
                            $fieldname = $field_name;
                            $tmp_fieldname = str_replace('[]', '', $field_name);
                            $mcheck_id = "{$tmp_fieldname}_%s";
                        }
                    } else {
                        $fieldname = "mchk_frm{$formID}_{$fieldID}[]";
                        $mcheck_id = "mchk_frm{$formID}_{$fieldID}_%s";
                    }
                    $setresult = false;
                    if ($function_datavalues != '') {
                        if (is_array($function_datavalues)) {
                            // Tested with AIM [pullform] autotag functions
                            // Array of Options and selected Checkboxes
                            $setresult = true;
                        } else {
                            $default_values = explode(',',$function_datavalues);
                        }
                    } else {
                        $default_values = explode(',',DB_getItem($_TABLES['formFields'], 'field_values', "id='$fieldID'"));
                    }
                    if ($resultid > 0) {
                        $result_values = explode(',',$field_value);
                    }
                    $field_html = '';
                    if ($setresult) {
                        $i = 0;
                        foreach ($function_datavalues as $chkoption => $seloption) {
                            $tmp_fieldname = sprintf($mcheck_id, $i);
                            $field_html .= "{$chkoption}&nbsp;<input type=\"checkbox\" id=\"$tmp_fieldname\" name=\"$fieldname\" value=\"$chkoption\" ";
                            if ($seloption == 1 OR (is_array($result_values) AND in_array($chkoption,$result_values)) ) {
                                $field_html .= 'CHECKED=CHECKED ';
                            }
                            $field_html .= " $validatetag ";
                            $field_html .= html_entity_decode($field_attributes).' '.html_entity_decode($javascript);
                            if ($mode == 'review') {
                                $field_html .= ' DISABLED';
                            }
                            $field_html .= '>&nbsp;';
                            $i++;
                        }

                    } else {
                        for ($a=0;$a < count($default_values);$a++) {
                            $tmp_fieldname = sprintf($mcheck_id, $a);
                            $field_html .= "{$default_values[$a]}&nbsp;<input type=\"checkbox\" id=\"$tmp_fieldname\" name=\"$fieldname\" value=\"{$default_values[$a]}\" ";
                            if ($mode == 'print' AND $resultid == 0) {
                                if ($seloption == 1 OR (is_array($_POST[$field_name]) AND in_array($default_values[$a],$_POST[$field_name])) ) {
                                    $field_html .= 'CHECKED=CHECKED ';
                                }
                            }
                            else {
                            if ($resultid > 0 and in_array($default_values[$a],$result_values)) {
                                $field_html .= 'CHECKED=CHECKED ';
                                }
                            }
                            $field_html .= " $validatetag ";
                            $field_html .= html_entity_decode($field_attributes).' '.html_entity_decode($javascript);
                            if ($mode == 'review') {
                                $field_html .= ' DISABLED';
                            }
                            $field_html .= '>&nbsp;';
                        }
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'radio':
                    if (is_array($dynamic)) {
                        $fieldname = "rad_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                    } elseif ($customfieldmode AND $field_name != '') {
                        $fieldname = $field_name;
                    } else {
                        $fieldname = "rad_frm{$formID}_$fieldID";
                    }
                    if ($function_datavalues != '') {
                        if (is_array($function_datavalues)) {
                            // Tested with AIM [pullform] autotag functions
                            // Array of Options and selected Checkboxes
                            $setresult = true;
                        } else {
                            $default_values = explode(',',$function_datavalues);
                        }
                    } else {
                        $default_values = explode(',',DB_getItem($_TABLES['formFields'], 'field_values', "id='$fieldID'"));
                    }
                    $field_html = '';
                    if ($setresult) {
                        $i = 0;
                        foreach ($function_datavalues as $chkoption => $seloption) {
                            $field_html .= "{$chkoption}&nbsp;<input type=\"radio\" id=\"{$fieldname}_{$i}\" name=\"$fieldname\" value=\"$chkoption\" ";
                            if ($seloption == 1 OR (is_array($result_values) AND in_array($chkoption,$result_values)) ) {
                                $field_html .= 'CHECKED=CHECKED ';
                            }
                            $field_html .= " $validatetag ";
                            $field_html .= html_entity_decode($field_attributes).' '.html_entity_decode($javascript);
                            if ($mode == 'review') {
                                $field_html .= ' DISABLED';
                            }
                            $field_html .= '>&nbsp;';
                            $i++;
                        }

                    } else {
                        for ($a=0;$a < count($default_values);$a++) {
                            $dynValue = trim($default_values[$a]);
                            $dynValue = str_replace (array('<br>','&nbsp;'),array('',''),$dynValue);
                            $field_html .= "<input type=\"radio\" id=\"{$fieldname}_{$a}\" name=\"$fieldname\" value=\"$dynValue\" ";
                            if ($resultid > 0 and ($dynValue == trim($field_value))) {
                                $field_html .= ' CHECKED=CHECKED ';
                            }
                            $field_html .= " $validatetag ";
                            $field_html .= html_entity_decode($field_attributes).' '.html_entity_decode($javascript);
                            if ($mode == 'review') {
                                $field_html .= ' DISABLED';
                            }
                            $field_html .= '>&nbsp;';
                            $field_html .= $default_values[$a];
                        }
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'textarea1':
                    if ($mode != 'print') {
                        if (is_array($dynamic)) {
                            $fieldname = "ta1_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "ta1_frm{$formID}_$fieldID";
                        }
                        $field_html = "<textarea id=\"$fieldname\" name=\"$fieldname\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['textarea1']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript);
                        if ($mode == 'review') {
                            $field_html .= ' READONLY';
                        }
                        $field_html .= '>';
                        if ($function_datavalues != '') {
                            $field_html .= $function_datavalues .'</textarea>';
                        } else {
                            $field_html .= $field_value .'</textarea>';
                        }
                    }
                    else {
                        $field_html = '<table style="border: solid #A5ACB2 1px;" bgcolor="#FFFFFF"><tr><td bgcolor="#FFFFFF" width=600 height=60 valign="top">';
                        $field_html .= $field_value;
                        $field_html .= '</td></tr></table>';
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;

                case 'textarea2':
                    if ($mode != 'print' AND $mode != 'review') {
                        if (is_array($dynamic)) {
                            $fieldname = "ta2_frm{$dynamic['0']}_{$dynamic['1']}_{$fieldID}_{$dynamic['2']}";
                        } elseif ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "ta2_frm{$formID}_$fieldID";
                        }
                        $field_html = "<textarea id=\"$fieldname\" name=\"$fieldname\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['textarea2']);
                        }
                        $field_html .= " $validatetag  ";
                        $field_html .= html_entity_decode($javascript) . '>';
                        if ($function_datavalues != '') {
                            $field_html .= $function_datavalues .'</textarea>';
                        } else {
                            $field_html .= $field_value .'</textarea>';
                        }

                    } elseif (trim($field_value) != '') {
                        $field_html = '<table style="border: solid #A5ACB2 1px;" bgcolor="#FFFFFF"><tr><td bgcolor="#FFFFFF" width=600 valign="top">';
                        $field_html .= $field_value;
                        $field_html .= '</td></tr></table>';
                    }

                    $template->set_var ('field', "$field_html");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;


                case 'captcha':
                    if ($resultid == 0 AND DB_getItem($_TABLES['plugins'],'pi_enabled',"pi_name='captcha'") == 1)  {              // Don't show field when viewing results
                        $fieldname = "captcha";         // Assume only 1 CAPTCHA Field per form
                        $field_html = "<input type=\"text\" name=\"$fieldname\" ";
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['captcha']);
                        }
                        $field_html .= " $validatetag ";
                        $field_html .= html_entity_decode($javascript) . '>';


                        global $_CP_CONF;
                        $sessid = CAPTCHA_sid();
                        $time    = time();
                        DB_save($_TABLES['cp_sessions'],"session_id,cptime,validation,counter","'$sessid','$time','','0'");
                        $field_html .= '<input type="hidden" name="csid" value="'.$sessid.'">';
                        $captchaImage = '<img src="'.$_CONF['site_url'] . '/captcha/captcha.php?csid='.$sessid.'&.' . $_CP_CONF['gfxFormat'] .'">';
                        $template->set_var('captcha_image',$captchaImage);
                        $template->set_var('verification_field',$field_html);
                        $template->parse ('field', 'captchafield');
                        if ($parsedLastRecordline) {
                            $template->parse('fields',$fieldStyle);
                        } else {
                             $template->parse('fields',$fieldStyle,true);
                        }
                        if ($parseRecord) {
                            $template->parse('form_records',$recStyle,true);
                            $parsedLastRecordline = true;
                        } else {
                            $parsedLastRecordline = false;
                        }
                    }

                    break;

                case 'submit':
                    if ($mode != 'review' AND $mode != 'print' AND $linked_formnum == 1) {
                        if ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "sub_frm{$formID}_$fieldID";
                        }
                        $field_html = "<input type=\"submit\" id=\"$fieldname\" name=\"$fieldname\" ";
                        // Disable the enterkey
                        $field_html .= "onFocus=\"this.form.action=document.getElementById('submit_url').value; this.form.target='';\"";
                        if (empty($field_value) AND !empty($label)) {
                            $field_html .= " value=\"$label\" ";
                        } else {
                            $field_html .= " value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['submit']);
                        }
                        $field_html .= ' ' . html_entity_decode($javascript) . '>';
                        $template->set_var ('submit_btn', "$field_html");
                        $template->parse ('submit','submit_button',true);
                    }
                    break;

                case 'cancel':
                    if ($mode != 'review' AND $mode != 'print') {
                        if ($customfieldmode AND $field_name != '') {
                            $fieldname = $field_name;
                        } else {
                            $fieldname = "btn_frm{$formID}_$fieldID";
                        }
                        if ($javascript == '') {
                             $javascript = 'onclick=\'javascript:history.go(-1)\'';
                        }
                        $field_html = "<input type=\"button\" id=\"$fieldname\" name=\"$fieldname\" ";
                        if (empty($field_value) AND !empty($label)) {
                            $field_html .= " value=\"$label\" ";
                        } else {
                            $field_html .= " value=\"$field_value\" ";
                        }
                        if (!empty($field_attributes)) {
                            $field_html .= html_entity_decode($field_attributes);
                        } else {
                            $field_html .= html_entity_decode($CONF_FE['defaultattributes']['cancel']);
                        }
                        $field_html .= html_entity_decode($javascript) . '>';
                        $template->set_var ('cancel',$field_html);
                    }
                    break;

                case 'heading':
                    $heading = "<div";
                    if ($style > 0) {
                        $heading .= ' class="'.$CONF_FE['fieldstyles'][$style][1] .'"';
                    }
                    if (!empty($field_attributes)) {
                        $heading .= " " . html_entity_decode($field_attributes);
                    } elseif (!empty($CONF_FE['defaultattributes']['heading'])) {
                        $heading .= " " .html_entity_decode($CONF_FE['defaultattributes']['heading']);
                    }
                    $heading .= ">";
                    $heading_label = '';
                    $heading_label .= ($field_value != '') ? $field_value : $label;
                    if ($heading_label == '') {
                        $heading_label = $function_datavalues;
                    }
                    $heading .= $heading_label;
                    $heading .= "</div>";

                    $template->set_var ('label', "$heading");
                    $template->set_var ('field', "&nbsp;");
                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }

                    break;


                case 'dynamic':
                    if ($resultid == 0) {
                        $dynamicFormID = $field_value;
                        $template->set_var('field',nexform_dynamicFormHTML($formID,$fieldID,$dynamicFormID,$mode));
                        $instance = 1;
                    } else {
                        $dynamicFormResults = explode('|',$field_value);
                        // Determine which form is to be used - in form definition
                        $dynamicFormID = DB_getItem($_TABLES['formFields'],'field_values',"id='$fieldID'");
                        $instance = 0;
                        $resultCount =  count($dynamicFormResults) -1;  // Want to show the [add/remove] field after the last form instance
                        foreach ($dynamicFormResults as $dynamicResult) {
                            if ($instance == 0) {
                                $template->set_var('field',nexform_dynamicFormHTML($formID,$fieldID,$dynamicFormID,$mode,false,$instance,$dynamicResult));
                            } else {
                                $template->set_var('field',nexform_dynamicFormHTML($formID,$fieldID,$dynamicFormID,$mode,true,$instance,$dynamicResult),true);
                            }
                            $instance++;
                        }
                    }
                    $values = DB_getItem($_TABLES['formFields'], 'field_values', "id=$fieldID");
                    $v_arr = explode(',', $values);
                    $v2 = $v_arr[1];
                    if (($mode == 'edit' || $mode == 'view') && $v2 != 1) {
                        $tmp_template = new Template($_CONF['path_layout'] . 'nexform');
                        $tmp_template->set_file('dynamicform_end', 'dynamicform_end.thtml');

                        $tmp_template->set_var('form_id', $formID );
                        $tmp_template->set_var('dynamic_field_id', $fieldID );
                        $tmp_template->set_var('dynamicform_id', $dynamicFormID );
                        $tmp_template->set_var('last_id', $instance - 1 );
                        $tmp_template->set_var('cell_padding', $template->get_var('cell_padding') );

                        $tmp_template->parse ('output', 'dynamicform_end');
                        $dynamicform_end = $tmp_template->finish ($tmp_template->get_var('output'));

                        $template->set_var('field',$dynamicform_end,true);
                    }

                    if (!$hidelabel) {
                        $template->set_var ('label', "$label");
                    }

                    if ($parsedLastRecordline) {
                        $template->parse('fields',$fieldStyle);
                    } else {
                        $template->parse('fields',$fieldStyle,true);
                    }
                    if ($parseRecord) {
                        $template->parse('form_records',$recStyle,true);
                        $parsedLastRecordline = true;
                    } else {
                        $parsedLastRecordline = false;
                    }
                    break;

                default:
                    // Check for any custom field definition types using custom templates
                    if (array_key_exists($type,$CONF_FE['customfieldmap'])) {

                        $template->set_file('custom_field','custom/' .$CONF_FE['customfieldmap'][$type]['form']);
                        if ( $mode == 'print') {
                            $printTemplateFiles = explode(',',$CONF_FE['customfieldmap'][$type]['print']);
                            if (count($printTemplateFiles) > 1) {
                                $template->set_file('custom_field','custom/' . $printTemplateFiles[0]);
                                $template->set_file('custom_field_rec','custom/' . $printTemplateFiles[1]);
                            } else {
                                $template->set_file('custom_field_rec','custom/' . $printTemplateFiles[0]);
                            }
                        } else {
                            $template->set_file('custom_field_rec','custom/' .$CONF_FE['customfieldmap'][$type]['record']);
                        }
                        $template->set_var ('field_id',$fieldID);
                        $template->set_var ('form_id',$formID);
                        if (($resultid > 0) OR ($mode == 'print')) {
                            if ($mode == 'print' AND $resultid == 0) {
                                $field_value = $_POST[$field_name];
                            }
                            $field_value = unserialize($field_value);
                            if ( $mode == 'edit') {
                                $template->set_file('custom_js','custom/' .$CONF_FE['customfieldmap'][$type]['javascript']);
                                $template->parse ('javascript_functions','custom_js');
                            } else {
                                $template->set_var ('showcustomfieldctl','none');
                            }
                            $i = 0;
                            do {
                                  $k = 1;
                                  $template->set_var('row',$i);
                                  if (is_array($field_value)) {
                                      $printCustomRec = false;
                                      foreach ($field_value as $custom_value) {
                                        if ($mode == 'print') $custom_value[$i] = nl2br($custom_value[$i]);
                                        $template->set_var("cust_value{$k}",$custom_value[$i]);
                                        $k++;
                                      }
                                  }
                                  if ($printCustomRec AND $i == 0) {
                                      $template->parse('custom_record','custom_field_rec');
                                  } elseif ($printCustomRec) {
                                      $template->parse('custom_record','custom_field_rec',true);
                                  }
                                  $i++;

                            } while(isset($custom_value[$i]));

                        } elseif ($function_datavalues != '') {
                            /* Form wants to use a function to format this field  */
                            $template->set_file('custom_js','custom/' .$CONF_FE['customfieldmap'][$type]['javascript']);
                            $template->parse ('javascript_functions','custom_js');
                            $template->set_var ('custom_record', $function_datavalues);
                        } else {
                            $template->set_file("custom_field{$fieldID}",'custom/' .$CONF_FE['customfieldmap'][$type]['form']);
                            $template->set_file("custom_field_rec{$fieldID}",'custom/' .$CONF_FE['customfieldmap'][$type]['record']);
                            $template->set_file("custom_js{$fieldID}",'custom/' .$CONF_FE['customfieldmap'][$type]['javascript']);
                            $template->parse ('javascript_functions',"custom_js{$fieldID}",true);
                            $template->parse('custom_record',"custom_field_rec{$fieldID}");
                        }

                        $template->parse('field',"custom_field{$fieldID}");
                        if (!$hidelabel) {
                            $template->set_var ('label', "$label");
                        }

                        if ($parsedLastRecordline) {
                            $template->parse('fields',$fieldStyle);
                        } else {
                            $template->parse('fields',$fieldStyle,true);
                        }
                        if ($parseRecord) {
                            $template->parse('form_records',$recStyle,true);
                            $parsedLastRecordline = true;
                        } else {
                            $parsedLastRecordline = false;
                        }
                    }
                    break;

            } // switch

            $template->set_var ('validatetag',$validatetag);
            $i++;
            if ($parsedLastRecordline ) {
                $fieldset_mode = false;
            }
        } // Check for internal_use fields

    } // while

    // Flush the last field form records if the template has not been parsed
    if (!$parsedLastRecordline ) {
        $template->parse('form_records',$recStyle,true);
    }
}

/**
* nexform_showform: Used to display Calls a form
* Function will return the full HTML for the requested form
* If a result id is passed in then the values will be retrieved
* and displayed in the form.
*
* @param        string         $formid       Required: form id to generate html for
* @param        string         $resultid     Optional: result id if posted results are to be shown
* @parm         string         $mode         Optional: Used to pass in 'edit' option of previous results
* @param        string/array   $parms        Optional: parms passed in will be converted to hidden fields
*                                            Used if posted form will be handled by custom form and
*                                            it optional variables are required for post processing
* @param        string         $linkedforms  Optional: used to optionally only show pre linked or post linked forms
*                                            Valid values are: all, none, beforeonly, afternonly
* @return       mixed         returns formatted form HTML
*
**/
function nexform_showform($formid,$resultid=0,$mode='view',$parms='',$linkedforms='all',$style='') {
    global $_CONF,$_TABLES,$CONF_FE,$forms_used;

    $forms_used[$formid] = 0;

    $groupAccess = DB_getItem($_TABLES['formDefinitions'],'perms_access', "id='$formid'");

    if (SEC_inGroup($groupAccess)) {  // Does user have access to this form
        $fields = 'name,post_method,post_option,intro_text,before_formid,after_formid,';
        $fields .= 'template,on_submit,show_mandatory_note';
        $formquery = DB_query("SELECT $fields FROM {$_TABLES['formDefinitions']} WHERE id='$formid'");
        list ($formname,$post_method,$post_option,$intro_text,$before_form,$after_form,$maintemplate,$onsubmit,$show_mandatory) = DB_fetchArray($formquery);

        // Check that template to be used exists - else use default
        $templatefile = "{$_CONF['path_layout']}nexform/$maintemplate";
        if (!file_exists($templatefile)) {
            $maintemplate = 'defaultform.thtml';
            COM_errorLog("nexform: Missing template $templatefile, using default. Form ID: $formid");
        }

        $page = new Template($_CONF['path_layout'] . 'nexform');
        $page->set_file (array (
                'page' => $maintemplate,
                'javascript'    => 'form_javascript.thtml',
                'formcontent'   => 'singleform_content.thtml',
                'fieldsetbegin' => 'fieldset_begin.thtml',
                'fieldsetend' => 'fieldset_end.thtml',
                'printfieldsetbegin' => 'print_fieldset_begin.thtml',
                'printfieldsetend' => 'print_fieldset_end.thtml',
                'recstyle1'=>'recstyle1.thtml',
                'recstyle2'=>'recstyle2.thtml',
                'fieldstyle1'=>'fieldstyle1.thtml',
                'fieldstyle1R'=>'fieldstyle1R.thtml',
                'fieldstyle2'=>'fieldstyle2.thtml',
                'fieldstyle2R'=>'fieldstyle2R.thtml',
                'fieldstyle3'=>'fieldstyle3.thtml',
                'fieldstyle3R'=>'fieldstyle3R.thtml',
                'mfile_field'=>'mfile_field.thtml',
                'fieldstyle5'=>'fieldstyle5.thtml',
                'fieldstyle5R'=>'fieldstyle5R.thtml',
                'captchafield' => 'captchafield.thtml',
                'editor' => 'advanced_editor.thtml',
                'mfilejs' => 'mfile_js.thtml',
                'mfieldjs' => 'mfield_js.thtml',
                'dselectjs' => 'dselect_js.thtml',
                'fieldhelp' => 'field_help.thtml',
                'submit_button'  => 'submit_button.thtml'));
        $page->set_var ('form_name',"glform_{$formid}");

        $page->set_var ('site_url',$_CONF['site_url']);
        $page->set_var ('layout_url',$_CONF['layout_url']);
        $page->set_var ('public_url',$CONF_FE['public_url']);
        $page->set_var ('res_id',$resultid);
        $page->set_var ('form_id',$formid);

        // This may get reset in the function nexFlow_showFormFields() if dynamic select detects a pre-selected option
        $page->set_var('setlists_onload','window.attachEvent("onload",initfilteredlist);');

         if(isset($parms) AND is_array($parms)) {
             $hidden_fields = '';
             foreach($parms as $key => $value) {
                 $hidden_fields .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
             }
             $page->set_var ('hidden_fields',$hidden_fields);
         }
        if ($resultid > 0) {

            $groupEditAccess = DB_getItem($_TABLES['formDefinitions'],'perms_edit', "id='$formid'");

            /* Need to add additional check for edit permissions for this form */
            // The customActionURL is a future use field - UI is not saving it currently
            //$customActionURL = DB_getItem($_TABLES['formDefinitions'],'admin_url', "id='$formid'");
            // if ($customActionURL == '' OR $post_method == 'posturl') {

            $currentURL = COM_getCurrentURL();
            if (strpos($currentURL,'admin/plugins/nexform/report.php') > 0) {
                $customActionURL = $_CONF['site_admin_url'] . '/plugins/nexform/report.php';
            } elseif ($post_method == 'posturl') {
                $customActionURL = $post_option;
            }

            if ($mode == 'edit') {
                if ($customActionURL == '') {
                    $form_action = $_CONF['site_admin_url'] .'/plugins/nexform/report.php?op=update&id='.$formid.'&result='.$resultid;
                    $page->set_var ('form_action',$form_action);
                } else {
                    $customActionURL = str_replace('[siteurl]', $_CONF['site_url'],$customActionURL);
                    $customActionURL = str_replace('[siteadminurl]', $_CONF['site_admin_url'],$customActionURL);
                    $form_action = $customActionURL .'?op=update&formid='.$formid.'&id='.$resultid;
                    $page->set_var ('form_action',$form_action);
                    $page->set_var('resultid', $resultid);
                }
                if ($parms['singleuse'] == 1) {
                    $page->set_var ('autoclose','true');
                } else {
                    $page->set_var ('autoclose','');
                }
                $page->set_var ('onsubmit','');
            } else {
                if ($customActionURL == '') {
                    $form_action = $_CONF['site_admin_url'] .'/plugins/nexform/report.php?id='.$formid;
                    $page->set_var ('form_action',$form_action);
                } else {
                    $customActionURL = str_replace('[siteurl]', $_CONF['site_url'],$customActionURL);
                    $customActionURL = str_replace('[siteadminurl]', $_CONF['site_admin_url'],$customActionURL);
                    $form_action = $customActionURL .'?op=view&id='.$formid.'&result='.$resultid;
                    if ($mode != 'print') {
                        $page->set_var ('form_action',$form_action);
                    }
                    $page->set_var ('feShowSubmitButtons','none');
                }
                $page->set_var ('onsubmit','');
            }
        } elseif ($post_method == 'posturl') {
            $form_action = str_replace('[siteurl]',$_CONF['site_url'],$post_option);
            $page->set_var ('form_action',$form_action);
            $page->set_var ('showadminmode', 'none');       // Hide the admin extra fields used when editing
            if (!empty($onsubmit)) {
                $onsubmit = $onsubmit;
            }
            $page->set_var ('onsubmit',$onsubmit);
        } else {
            $form_action = $CONF_FE['post_url'] .'/index.php?id='.$formid;
            $page->set_var ('form_action',$form_action);
            if (!empty($onsubmit)) {
                $onsubmit = $onsubmit;
            }
            $page->set_var ('onsubmit',$onsubmit);
        }
        $page->set_var ('form_handler',$post_method);

        /* Now show any linked forms - recursively but compare to see we don't go in a loop */
        $allforms = array();
        if (trim($linkedforms) == '') {  // Assume all linked forms if null passed in
            $allforms = nexform_getlinkedform($formid,$allforms,'all');
        } elseif ($linkedforms == 'none') {
            $allforms[] = $formid;
        } else {
            $allforms = nexform_getlinkedform($formid,$allforms,$linkedforms);
        }

        /* Determine if more then 1 linked form has the tabbed feature enabled */
        $formCntWithTabs = 0;
        foreach ($allforms as $chkformID) {
            if (DB_getItem($_TABLES['formDefinitions'],'show_as_tab',"id='$chkformID'")) $formCntWithTabs++;
        }

        $postmethod = '';
        $tab_active = false;
        $tabid = 0;
        $i = 1;
        $prediv_open = false;
        $CONF_FE['dynamicSelect'] = false;
        foreach ($allforms as $showform) {
            $fquery = DB_query("SELECT id,name,show_as_tab,tab_label FROM {$_TABLES['formDefinitions']} WHERE id=$showform");
            list ($linkid,$formname,$taboption,$tablabel) = DB_fetchArray($fquery);
            $groupAccess = DB_getItem($_TABLES['formDefinitions'],'perms_access', "id='$linkid'");

            if (SEC_inGroup($groupAccess)) {  // Does user have access to this form
                if (count($allforms) > 1 AND $mode != 'print' AND $formCntWithTabs > 1 AND $taboption == 1 AND !$tab_active) {
                    $tab_active = true;
                    $page->set_file(array('navbar' => 'form_tabnavbar.thtml',
                        'navtab' => 'tab.thtml',
                        'divbegin' => 'singleform_divbegin.thtml'));
                    $page->set_var('show_tab1','');
                }
                $page->set_var('form_comment1',"<!-- **BEGIN** Form: $formname **** -->");
                nexform_showFormFields($showform,$form_action,$page,$resultid,$mode,$i);
                $page->set_var('toolbar', $CONF_FE['fckeditor_toolbar']);
                $page->set_var('init_function_calls', $ta2init_function_calls);

                $page->set_var('form_comment2',"<!-- **END** Form: $formname **** -->");
                if (count($allforms) > 1 AND $mode != 'print' AND $taboption) {
                    $tabid++;
                    $page->set_var('tabid',$tabid);
                    $page->parse('div_begin', 'divbegin');
                    $page->set_var("show_tab{$tabid}",'none');
                    $page->set_var('tab_class',($tabid==1) ? 'navsubcurrent':'navsubmenu');
                    $page->set_var('tab_label',($tablabel == '') ? $formname : $tablabel);
                    $page->parse('tabs','navtab',true);
                    $page->parse('tab_navbar','navbar');
                    $nexformid = $allforms[$i];
                    /* Check if next form is also a tabbed form */
                    $nexformTabType = DB_getItem($_TABLES['formDefinitions'],'show_as_tab',"id='$nexformid'");
                    if ($nexformTabType == 1) {
                        $page->set_var('div_end','</div>');
                    } else {
                        $page->set_var('div_end','');
                        $prevdiv_open = true;  // Next form is not to be in a separate div
                    }
                } elseif ($prevdiv_open) {  // May need to close previous div if tabbed form option was used
                    $page->set_var('div_begin','');
                    $page->set_var('div_end','</div>');
                } else {
                    $page->set_var('div_begin','');
                    $page->set_var('div_end','');
                }
                $page->parse('form_contents','formcontent',true);
                $page->parse('form_records','');
                $i++;

                //Check if form has a field of type 'file' which needs a different posting method in the form HTML tag
                //also have to check through child forms
                $frms = array();
                $frms = nexform_getdynamicforms($showform, $frms);
                foreach ($frms as $frmid) {
                    $filequery = DB_query("SELECT * FROM {$_TABLES['formFields']} WHERE formid='$frmid' AND (type = 'file' OR type='mfile')");
                    if (DB_fetchArray($filequery) != '') {
                        $postmethod = "\"post\" enctype=\"multipart/form-data\"";
                        $page->parse('mfile_js_functions','mfilejs');
                    }
                    $filequery = DB_query("SELECT * FROM {$_TABLES['formFields']} WHERE formid='$frmid' AND (type = 'mtxt')");
                    if (DB_fetchArray($filequery) != '') {
                        $page->parse('mfield_js_functions','mfieldjs');
                    }
                    $filequery = DB_query("SELECT * FROM {$_TABLES['formFields']} WHERE formid='$frmid' AND (type = 'textarea2')");
                    if (DB_fetchArray($filequery) != '') {
                        $page->parse('advancededitor', 'editor');
                    }
                }
            }
        }

        if ($postmethod == '') {
            $page->set_var ('method','"post"');
        } else {
            $page->set_var ('method',$postmethod);
        }

        $page->set_var ('introtext',$intro_text);

        if ($mode != 'print' AND $show_mandatory AND DB_count($_TABLES['formFields'],array('formid','is_mandatory'),array($formid,'1')) > 1) {
            $page->set_var ('msg_mandatory','Note: * Indicates mandatory Field');
        } else {
            $page->set_var ('msg_mandatory','');
        }

        if ($CONF_FE['dynamicSelect']) {
            $page->parse('dynamic_select_js','dselectjs');
        } else {
            $page->set_var('dynamic_select_js','');
        }

        /* Check and see if Advanced Editor should be setup for textarea fields */
        if ($mode != 'print') {
            $page->set_var('formContainerClass','frm_maincontainer');
            if ($resultid > 0) {
                if ($mode == 'edit') {
                    $print_option  = '<a href="#" onClick="document.glform_'.$formid.'.className=document.glform_';
                    $print_option .= $formid.'.action; document.glform_'.$formid.'.action=\''.$CONF_FE['public_url'];
                    $print_option .= '/print.php?op=print&result='.$resultid.'&epm=1&id='.$formid;
                    $print_preview_option = '<a href="#" onClick="document.glform_'.$formid.'.className=document.glform_';
                    $print_preview_option .= $formid.'.action; document.glform_'.$formid.'.action=\''.$CONF_FE['public_url'];
                    $print_preview_option .= '/print.php?op=print&style=preview&result='.$resultid.'&epm=1&id='.$formid;
                } else {
                    $print_option  = '<a href="#" onClick="document.glform_'.$formid.'.className=document.glform_';
                    $print_option .= $formid.'.action; document.glform_'.$formid.'.action=\''.$CONF_FE['public_url'];
                    $print_option .= '/print.php?op=print&result='.$resultid.'&id='.$formid;
                    $print_preview_option  = '<a href="#" onClick="document.glform_'.$formid.'.className=document.glform_';
                    $print_preview_option .= $formid.'.action; document.glform_'.$formid.'.action=\'';
                    $print_preview_option .= $CONF_FE['public_url'].'/print.php?op=print&style=preview&result='.$resultid.'&id='.$formid;
                }
                $print_option .= '\'; document.glform_'.$formid.'.target=\'printwindow\'; document.glform_'.$formid;
                $print_option .= '.submit();" onBlur="document.glform_'.$formid.'.action=document.glform_'.$formid;
                $print_option .= 'action=document.glform_'.$formid.'.className; document.glform_'.$formid.'.target=\'\';">[print]</a>';

                $print_preview_option .= '\'; document.glform_'.$formid.'.target=\'printwindow\'; document.glform_'.$formid;
                $print_preview_option .= '.submit();" onBlur="document.glform_'.$formid.'.action=document.glform_'.$formid;
                $print_preview_option .= '.className; document.glform_'.$formid.'.target=\'\';">[print preview]</a>';

                if ($parms['noprint']) {
                    $page->set_var('print_option','');
                    $page->set_var('print_preview_option','');
                } else {
                    $page->set_var('print_option', $print_option);
                    $page->set_var('print_preview_option', $print_preview_option);
                }
            } else {
                $page->set_var('print_option','');
                $page->set_var('print_preview_option','');
            }

        } elseif ($mode == 'print') {
            if ($style != 'preview') {
                $page->set_var('print_instructions', '<script type="text/javascript">
                window.print();
                setTimeout(\'window.close()\', 1000);
                </script>');
            }
        }
        if ($CONF_FE['dynamicSelect']) {
            $page->parse('dynamic_select_js','dselectjs');
        } else {
            $page->set_var('dynamic_select_js','');
        }

        $page->set_var ('form_id',$formid);
        $page->parse('javascript','javascript');
        $page->parse ('output', 'page');
        if ($mode == 'edit' AND $customActionURL == '') {
            $page->set_var('editstatus_message','<h2 id="feHeadingEditMode" style="margin:0px;padding:10 5 10 50px;">Edit Mode</h2>');
        }
        $formhtml .= $page->finish ($page->get_var('output'));
    } else {
        $formhtml = '';
    }
    return $formhtml;

}


// Function called in the function: nexform_showFormFields() if a nexform custom tag used
// These are pre-defined tags but function can be extended
// If not one of the pre-defined tags - check if this is a lookuplist tag or custom function
// Plugins can provide custom lookup functions for tags
function nexform_getAutotagValues($autotag,$fieldtype,$selected='',$fieldID='') {
    global $_TABLES,$_USER,$_CONF;

    if (strpos($autotag,'list') === FALSE) {
        $autotag = str_replace('[','',$autotag);
        $autotag = str_replace(']','',$autotag);

        switch(strtolower($autotag)) {
            case 'username' :
                if ($fieldtype == 'select') {
                    $query = DB_query("SELECT uid,fullname FROM {$_TABLES['users']}");
                    $retval = array();
                    while ($A = DB_fetchArray($query)) {
                        $retval[$A['uid']] = $A['fullname'];
                    }
                } elseif ($selected != '') {
                    return $selected;
                } else {
                    $retval = $_USER['username'];
                }
                return $retval;
                break;
            case 'today' :
                return date("m/d/Y");
                break;
            default:  // Check and see if there is a custom function for this tag
                $atag = explode(':',$autotag);
                $function = $atag[0];
                if (function_exists($function)) {
                    return $function($atag[1],$fieldID);
                }
                break;
        } // switch

    } else {
        return PLG_replaceTags ($autotag,'nexlist');
    }

}


function nexform_dynamicFormHTML($id,$fieldid,$form,$mode='view',$fieldsonly=false,$instance=0,$result=0) {
    global $_CONF,$CONF_FE,$_TABLES,$forms_used,$dynamicforms_used;

    if (array_key_exists ($id, $forms_used) AND $forms_used[$id] == "$fieldid,$instance") {
        echo "Fatal Error: Dynamic form caused an infinate loop.";
        exit();
    } else {
        $forms_used[$id] = "$fieldid,$instance";
    }

    $page = new Template($_CONF['path_layout'] . 'nexform');

    // Check and see if the option for fieldsonly OR form id ($id) passed in contains the option to only show one copy of the dynamic form
     if ($fieldsonly OR strpos($form,',') > 0) {
        $page->set_file ('page' , 'dynamicform_fieldsonly.thtml');
    } else {
        $page->set_file ('page' , 'dynamicform.thtml');
    }
    $page->set_file (array (
            'formcontent'   => 'singleform_content.thtml',
            'fieldsetbegin' => 'fieldset_begin.thtml',
            'fieldsetend' => 'fieldset_end.thtml',
            'printfieldsetbegin' => 'print_fieldset_begin.thtml',
            'recstyle1'=>'recstyle1.thtml',
            'recstyle2'=>'recstyle2.thtml',
            'fieldstyle1'=>'fieldstyle1.thtml',
            'fieldstyle1R'=>'fieldstyle1R.thtml',
            'fieldstyle2'=>'fieldstyle2.thtml',
            'fieldstyle2R'=>'fieldstyle2R.thtml',
            'fieldstyle3'=>'fieldstyle3.thtml',
            'fieldstyle3R'=>'fieldstyle3R.thtml',
            'mfile_field'=>'mfile_field.thtml',
            'fieldstyle5'=>'fieldstyle5.thtml',
            'captchafield' => 'captchafield.thtml',
            'editor' => 'advanced_editor.thtml',
            'mfilejs' => 'mfile_js.thtml',
            'mfieldjs' => 'mfield_js.thtml',
            'fieldhelp' => 'field_help.thtml',
            'dynamic_js' => 'dynamicform_js.thtml'));

    $page->set_var('form_id',$id);
    $page->set_var('dynamic_field_id',$fieldid);
    $instance = intval($instance);

    $page->set_var ('last_id',$instance);
    $dynamic = array ($id,$fieldid,$instance);
    $page->set_var('dynamicform_id',$form);
    $page->set_var('fe_actionURL',$CONF_FE['public_url']);

    nexform_showFormFields($form,'',$page,$result,$mode,1,$dynamic);

    $page->parse('form_contents','formcontent');

    // Track the forms used and only add the common dynamic form JS if this is the first form and in form update/submission mode
    if (!in_array($form,$dynamicforms_used)) {
        $dynamicforms_used[] = $form;
        if (count($dynamicforms_used) == 1 AND ($mode == 'view' OR $mode == 'edit')) {
            $page->parse ('javascript_functions',"dynamic_js");
        }
    }

    $page->parse ('output', 'page');
    $html = $page->finish ($page->get_var('output'));
    return $html;
}



/* Common function called for saving form data */
function nexform_dbsave($form_id,$postUID=0,$check4files=true) {
    global $_USER,$_TABLES,$_CONF,$CONF_FE,$_POST;

    require_once('lib-uploadfiles.php');

    $uploadFileTypesAllowed = '';
    $allowablefiletypes = '';
    $date = time();
    $dynamicForms = array();    // Track form instances if Dynamic forms present

    if ($postUID > 0) {
        $userid = $postUID;
    } elseif (!isset($_USER['uid'])) {
        $userid = 1;
    } else {
        $userid = $_USER['uid'];
    }

    DB_query("INSERT INTO {$_TABLES['formResults']} (form_id,uid,date)
                    VALUES ('$form_id','$userid','$date') ");
    $result = DB_insertID();

    $relatedResults = array();

    // Hold data if there are any custom fields - as we want to treat this as one field.
    // And insert at the end as one record - an array of records in reality
    $custom_fields = array();
    foreach ($_POST as $var => $value) {
        // For all the form fields - other then form_id we need to process/save
        if ($var != 'form_id') {
            /* The variable names contain the fieldtype and fieldid */
            /* XXX_frm{formid}_{fieldid}    - where XXX is the fieldtype */
            $parts = explode('_',$var);
            $fieldtype = $parts[0];
            $is_dynamicfield_result = false;
            if (isset($parts[4])) {
                 $dynamicFieldInstance = $parts['4'];
                 $field_id = (int) $parts['3'];
                 $is_dynamicfield_result = true;
                 $dynamicForm = DB_getItem($_TABLES['formFields'],'formid',"id='$field_id'");
                 // Need to create a new result record for each instance of the dynamic form
                 if (!array_key_exists($parts['2'],$dynamicForms)) {
                    DB_query("INSERT INTO {$_TABLES['formResults']} (form_id,uid,date)
                                    VALUES ('$dynamicForm','$userid','$date') ");
                    $dynamicResult = DB_insertID();
                    $relatedResults[] = $dynamicResult;
                    $dynamicForms[$parts['2']] = array($dynamicFieldInstance => $dynamicResult);
                 } elseif (!array_key_exists($dynamicFieldInstance,$dynamicForms[$parts['2']])) {
                    DB_query("INSERT INTO {$_TABLES['formResults']} (form_id,uid,date)
                                    VALUES ('$dynamicForm','$userid','$date') ");
                    $dynamicResult = DB_insertID();
                    $relatedResults[] = $dynamicResult;
                    $dynamicForms[$parts['2']][$dynamicFieldInstance] = $dynamicResult;
                 }

            } else {
                $field_id = (int) $parts['2'];
                $is_dynamicfield_result = false;
            }

            // Only process fields that have a valid field id
            // Note: Using Field ID today - consider using the tfid field
            // Added logic to see if field is blank or select = -1 (no selection). If so don't create result record.
            if ($field_id > 0 AND trim($value) != '') {
                //echo "<br>Field ID: $field_id, Field type: $fieldtype, Value: $value";
                //* Check if this field is a textarea field
                if ($fieldtype == 'ta1' or $fieldtype == 'ta2') {
                    if ($fieldtype == 'ta1') {
                        //$value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                    } else {
                        //$value = COM_checkWords(COM_killJS($value));
                    }
                    $value = addslashes($value);
                    if ($is_dynamicfield_result) {
                        DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$result','$field_id','$value','0') ");
                    }

                } elseif ($fieldtype == 'hid') {
                    // check for custom use fields
                    if (DB_getItem($_TABLES['formFields'],'label', "id='$field_id'") == 'filetype' )  {
                        $uploadFileTypesAllowed  = DB_getItem($_TABLES['formFields'],'field_attributes', "id='$field_id'");
                    }
                    $value = addslashes($value);
                    if ($is_dynamicfield_result) {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$result','$field_id','$value','0') ");
                    }

                } elseif ($fieldtype == 'mchk') {
                    if (is_array($value)) {
                        $value = implode(',',$value);
                    }
                    if (!get_magic_quotes_gpc()) {
                        $value = addslashes($value);
                    }
                    if ($is_dynamicfield_result) {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$result','$field_id','$value','0') ");
                    }

                } elseif ($fieldtype == 'mtxt') {
                    if (is_array($value)) {
                        // Use the | character as the field deliminter as this is unlikly to be entered by user.
                        $value = implode('|',$value);
                    }
                    if (!get_magic_quotes_gpc()) {
                        $value = addslashes($value);
                    }
                    if ($is_dynamicfield_result) {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$result','$field_id','$value','0') ");
                    }

                } elseif ($fieldtype == 'sel') {
                    if ($value != -1) {
                        if (!get_magic_quotes_gpc()) {
                            $value = addslashes($value);
                        }
                        if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                            DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                        } else {
                            if ($is_dynamicfield_result) {
                                if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                    DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                                } else {
                                    DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                        VALUES ('$dynamicResult','$field_id','$value','1') ");
                                }
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                    VALUES ('$result','$field_id','$value') ");
                            }
                        }
                    }

                } elseif ($fieldtype == 'cust') {  // custom field type - an array of fields from a custom layout
                    // Extra part on fieldname is the sub-field name
                    // Add custom field values to an array which we will insert at end as 1 record
                    // Need to treat this custom field or set of fields as a single result record
                    // There could be multiple custom fields on the form
                    $subfield = $parts[3];
                    if (is_array($value)) {
                        foreach ($value as $subfield_value) {
                            $custom_fields[$field_id][$subfield][] = $subfield_value;
                        }
                    }

                } elseif ($fieldtype != 'sub' and $fieldtype != 'btn' and $fieldtype != 'formhandler') {
                    $value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                    $value = addslashes($value);
                    if ($is_dynamicfield_result) {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$result','$field_id','$value','0') ");
                    }
                }
            }
        }
    }

    if (count($dynamicForms) > 0) {
        foreach ($dynamicForms as $field_id => $value) {
            if (is_array($value)) {
                // Use the | character as the field deliminter as this is unlikly to be entered by user.
                $value = implode('|',$value);
            }
            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                VALUES ('$result','$field_id','$value','0') ");
        }
    }

    if (count($custom_fields) > 0)  {
        foreach ($custom_fields as $field_id => $value) {
            $value = serialize($value);
            if (!get_magic_quotes_gpc()) {
                $value = addslashes($value);
            }
            if ($is_dynamicfield_result) {
                DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                    VALUES ('$dynamicResult','$field_id','$value','1') ");
            } else {
                DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                    VALUES ('$result','$field_id','$value','0') ");
            }
        }
    }

    /* Check for any uploaded files */
    if ($check4files) {
        if (count($dynamicForms) > 0) {
            nexform_check4files($result, $dynamicForms);
        } else {
            nexform_check4files($result);
        }
    }

    // Update the related form_results field for the main results record
    $related_results = implode (',',$relatedResults);
    DB_query("UPDATE {$_TABLES['formResults']} set related_results='$related_results' WHERE id='$result'");

    return $result;

}

function nexform_dbupdate($id,$result,$postUID=0) {
    global $_TABLES,$_CONF,$CONF_FE,$_USER,$form_id;

    require_once('lib-uploadfiles.php');

    /* Identify all the checkmark fields */
    $sql = "SELECT id,label FROM {$_TABLES['formFields']} WHERE formid='$id' ";
    $sql .= "AND type IN ('checkbox') ";
    $q1 = DB_query($sql);

    $processed_fields = array();
    $checkmark_fields = array();

    $date = time();
    if ($postUID > 0) {
        $userid = $postUID;
    } elseif (!isset($_USER['uid'])) {
        $userid = 1;
    } else {
        $userid = $_USER['uid'];
    }

    while (list ($field_id, $heading) = DB_fetchArray($q1)) {
        $checkmark_fields[] = $field_id;
    }
    foreach ($_POST as $var => $value) {
        if ($var != 'form_id') {
            /* The variable names contain the fieldtype and fieldid */
            /* XXX_frm{formid}_{fieldid}    - where XXX is the fieldtype */
            $parts = explode('_',$var);
            $fieldtype = $parts[0];
            $is_dynamicfield_result = false;
            if (isset($parts[4])) {
                 $dynamicFieldInstance = $parts['4'];
                 $sfield_id = (int) $parts['2'];
                 $field_id  = (int) $parts['3'];
                 $instance  = (int) $parts['4'];
                 $is_dynamicfield_result = true;
                 $dynamicForm = DB_getItem($_TABLES['formFields'],'formid',"id='$field_id'");
                 // Get the results currently recorded for the source form field
                 $dynamicResults = explode('|',DB_getItem($_TABLES['formResData'],'field_data',"result_id='$result' AND field_id='$sfield_id'"));
                 // Check if this instance of the dynamic form is already created as a result.
                 if (isset($dynamicResults[$instance])) {
                     $dynamicResult = $dynamicResults[$instance];
                 } else {
                     // User must be submitting the form with a new instance of this dynamic subform (field)
                     // Need to create a new result record and update relating fields with the new resultid
                    DB_query("INSERT INTO {$_TABLES['formResults']} (form_id,uid,date)
                                    VALUES ('$dynamicForm','$userid','$date') ");
                    $dynamicResult = DB_insertID();
                    $dynamicResults[$instance] = $dynamicResult;
                    $relatedFieldResults = implode ('|',$dynamicResults);
                    DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$relatedFieldResults' WHERE result_id='$result' AND field_id='$sfield_id'");

                    // Now need to update the related Results field in the main results records
                 }
            } else {
                $field_id = (int) $parts['2'];
                $is_dynamicfield_result = false;
            }

            //echo "<br>Field ID: $field_id, Field type: $fieldtype, Value: $value";
            //* Check if this field is a textarea field
            // Added logic to see if field is blank or select = -1 (no selection). If so don't create result record.
            if ($field_id > 0 AND trim($value) != '') {
                if ($CONF_FE['debug']) {
                    COM_errorLog("Update field:$field_id, type:$fieldtype");
                }
                $processed_fields[] = $field_id;
                if ($fieldtype == 'ta1' or $fieldtype == 'ta2') {
                    if ($fieldtype == 'ta1') {
                        $value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                    } else {
                        $value = COM_checkWords(COM_killJS($value));
                    }
                    $value = addslashes($value);
                    if (DB_count($_TABLES['formResText'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResText']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                    } else {
                        if ($is_dynamicfield_result) {
                            if (DB_count($_TABLES['formResText'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                DB_query("UPDATE {$_TABLES['formResText']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                                    VALUES ('$dynamicResult','$field_id','$value','1') ");
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data)
                                VALUES ('$result','$field_id','$value') ");
                        }
                    }
                } elseif ($fieldtype == 'mchk') {
                    if (is_array($value)) {
                        $value = implode(',',$value);
                    }
                    if (!get_magic_quotes_gpc()) {
                        $value = addslashes($value);
                    }
                    if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                    } else {
                        if ($is_dynamicfield_result) {
                            if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                    VALUES ('$dynamicResult','$field_id','$value','1') ");
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                VALUES ('$result','$field_id','$value') ");
                        }
                    }

                } elseif ($fieldtype == 'sel') {
                    if ($value != '-1') {
                        if (!get_magic_quotes_gpc()) {
                            $value = addslashes($value);
                        }
                        if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                            DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                        } else {
                            if ($is_dynamicfield_result) {
                                if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                    DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                                } else {
                                    DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                        VALUES ('$dynamicResult','$field_id','$value','1') ");
                                }
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                    VALUES ('$result','$field_id','$value') ");
                            }
                        }
                    }

                } elseif ($fieldtype == 'mtxt') {
                    if (is_array($value)) {
                        // Use the | character as the field deliminter as this is unlikly to be entered by user.
                        $value = implode('|',$value);
                    }
                    if (!get_magic_quotes_gpc()) {
                        $value = addslashes($value);
                    }
                    if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                    } else {
                        if ($is_dynamicfield_result) {
                            if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                    VALUES ('$dynamicResult','$field_id','$value','1') ");
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                VALUES ('$result','$field_id','$value') ");
                        }
                    }

                } elseif ($fieldtype == 'cust') {  // custom field type - an array of fields from a custom layout
                    // Extra part on fieldname is the sub-field name
                    // Add custom field values to an array which we will insert at end as 1 record
                    // Need to treat this custom field or set of fields as a single result record
                    // There could be multiple custom fields on the form
                    $subfield = $parts[3];
                    if (is_array($value)) {
                        foreach ($value as $subfield_value) {
                            $custom_fields[$field_id][$subfield][] = $subfield_value;
                        }
                    }

                } elseif ($fieldtype != 'sub' and $fieldtype != 'btn') {
                    $value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                    $value = addslashes($value);
                    if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
                    } else {
                        if ($is_dynamicfield_result) {
                            if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                    VALUES ('$dynamicResult','$field_id','$value','1') ");
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                VALUES ('$result','$field_id','$value') ");
                        }
                    }
                }
            }
        }
    }

    /* Determine if any checkmark type fields on the form were not checked */
    /* Un-Checked checkmark fields are not returned in the $_POST array    */
    foreach ($checkmark_fields as $key) {
        if (!in_array($key, $processed_fields)) {
            /* Update results for this checkmark field - just enter a blank to indicate not checked */
            if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($result,$key)) > 0) {
                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '' WHERE result_id='$result' AND field_id='$key'");
            } else {
                if ($is_dynamicfield_result) {
                    if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$key'");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$key','$value','1') ");
                    }
                } else {
                    DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                        VALUES ('$result','$key','$value') ");
                }
            }
        }
    }

    if (count($custom_fields) > 0)  {
        foreach ($custom_fields as $field_id => $value) {
            $value = serialize($value);
            if (!get_magic_quotes_gpc()) {
                $value = addslashes($value);
            }
            if (DB_count($_TABLES['formResText'],array('result_id','field_id'), array($result,$field_id)) > 0) {
                DB_query("UPDATE {$_TABLES['formResText']} set field_data = '$value' WHERE result_id='$result' AND field_id='$field_id'");
            } else {
                if ($is_dynamicfield_result) {
                    if (DB_count($_TABLES['formResText'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$value' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                    } else {
                        DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data,is_dynamicfield_result)
                            VALUES ('$dynamicResult','$field_id','$value','1') ");
                    }
                } else {
                    DB_query("INSERT INTO {$_TABLES['formResText']} (result_id,field_id,field_data)
                        VALUES ('$result','$field_id','$value') ");
                }
            }
        }
    }

    /* Check for any uploaded files */
    nexform_check4files($result, $dynamicForms);

    /* Update the results date field - so it's lastupdated timestamp */
    DB_query("UPDATE {$_TABLES['formResults']} set last_updated_date=UNIX_TIMESTAMP(), last_updated_uid=$userid where id=$result");
}


/* @TODO: Has not been updated to support new field types */
function nexform_emailresults() {
    global $userid,$_TABLES,$_CONF,$_POST,$form_id;

   $date = time();
   if ($userid == 1) {
        $username = 'Anonymous';
    } else {
        $username = DB_getItem($_TABLES['users'],'fullname',"uid='$userid'");
    }
    $date = COM_getUserDateTimeFormat();
    $formname = DB_getItem($_TABLES['formDefinitions'],'name',"id='$form_id'");
    $heading =  'Results from submitted form => Form name: '.$formname;
    $page = new Template($_CONF['path_layout'] . 'nexform');
    $page->set_file (array ('page' => 'emailform.thtml', 'records'=>'emailrecords.thtml'));
    $page->set_var ('LANG_date','Date');
    $page->set_var ('date',$date[0]);
    $page->set_var ('heading',$heading);
    $page->set_var ('LANG_postedby','Submitted By');
    $page->set_var ('postedby_name',$username);
    $page->set_var ('begin_data','=============SUBMITTED DATA FROM FORM  =============');
    $page->set_var ('end_data','==================== END OF DATA ====================');

    foreach ($_POST as $var => $value) {
        if ($var != 'form_id' and $var != 'formhandler') {
            /* The variable names contain the fieldtype and fieldid */
            /* XXX_form{formid}_{fieldid}    - where XXX is the fieldtype */
            $parts = explode('_',$var);
            $fieldtype = $parts[0];
            $field_id = (int) $parts[2];
            /* Check if this field is a textarea field */
            if ($fieldtype == 'ta1' or $fieldtype == 'ta2') {
                if ($fieldtype == 'ta1') {
                    $value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                } else {
                    $value = COM_checkWords(COM_killJS($value));
                }
                $label = DB_getItem($_TABLES['formFields'],'label',"id='$field_id'");
                $page->set_var ('label',$label);
                $page->set_var ('field_value',$value);
                $page->parse('email_records','records',true);
            } elseif ($fieldtype == 'mchk') {
                if (is_array($value)) {
                    $value = implode(',',$value);
                }
                $label = DB_getItem($_TABLES['formFields'],'label',"id='$field_id'");
                $page->set_var ('label',$label);
                $page->set_var ('field_value',$value);
                $page->parse('email_records','records',true);
            } elseif ($fieldtype != 'sub' and $fieldtype != 'btn') {
                $value = COM_checkWords(COM_checkHTML(COM_killJS($value)));
                $label = DB_getItem($_TABLES['formFields'],'label',"id='$field_id'");
                $page->set_var ('label',$label);
                $page->set_var ('field_value',$value);
                $page->parse('email_records','records',true);
            }
        }
    }

    /* Check for any uploaded files */
    $filelinks = nexform_check4files();
    if ($filelinks != '') {
        $page->set_var ('label','Attachments');
        $page->set_var ('field_value',$filelinks);
        $page->parse('email_records','records',true);
    }

    $page->parse ('output', 'page');
    $message =  $page->finish ($page->get_var('output'));
    //echo "<br>Send message:<br>$message";

    $to = DB_getItem($_TABLES['formDefinitions'],'post_option',"id='$form_id'");

    COM_mail($to, $heading, $message);
}

function nexform_replaceFieldTags($formid, $dynamic, $string) {
    global $_TABLES, $CONF_FE;
    while (($pos = strpos($string, '[field:')) !== false) {
        $pos += 7;
        $tag = '';
        for ($i = 0; $string[$pos] != ']'; $i++, $pos++) {
            $tag[$i] = $string[$pos];
        }
        $tag = implode('', $tag);
        $params = explode(',', $tag);
        if (!array_key_exists(2, $params)) {
            $params[2] = -1;
        }

        $string = str_replace("[field:$tag]", nexform_getFieldName($formid, $params[0], $params[1], $params[2], $dynamic), $string);
    }

    return $string;
}


function nexform_getFieldName($pfid, $formid, $templateid, $instance=-1, $dynamic) {
    global $_TABLES, $CONF_FE;

    $q = DB_query("SELECT * FROM {$_TABLES['formFields']} WHERE formid='$formid' AND tfid='$templateid';");
    $R = DB_fetchArray($q);
    $len = DB_numRows($q); //should be 1

    if (is_array($dynamic)) {
        $retval = $CONF_FE['fieldtypes'][$R['type']][0];
        $retval .= "{$dynamic['0']}_{$dynamic['1']}_{$R['id']}_{$dynamic['2']}";
        if ($instance != -1) {
            $retval .= "_{$instance}";
        }
        return $retval;
    }
    else if ($pfid != $formid) {
        $dfsql = "SELECT id FROM {$_TABLES['formFields']} WHERE";
        $dfsql .= " formid=$pfid AND type='dynamic' AND";
        $dfsql .= " (field_values='$formid' OR field_values LIKE '$formid,%');";
        $dfq = DB_query($dfsql);
        $DFR = DB_fetchArray($dfq);
        $retval = $CONF_FE['fieldtypes'][$R['type']][0];
        $retval .= "{$pfid}_{$DFR['id']}_{$R['id']}_0";
        if ($instance != -1) {
            $retval .= "_{$instance}";
        }
        return $retval;
    }
    else {
        if ($len == 1) {
            $retval = $R['field_name'];
            if ($instance != -1) {
                $retval .= "_{$instance}";
            }
            return $retval;
        }
    }

    return false;
}

?>