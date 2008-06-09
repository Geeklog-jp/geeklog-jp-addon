<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | customtags.php                                                            |
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

/* Define functions for custom tags that will be used in the nexform Field Definitions
*
*  The name of the function needs to be defined in the field default value and "function by value" enabled
*  There are some pre-defined tags supported by the function fe_getAutoTagValues in library.php
*  Example: [username] to return the username for the user filling out the form
*           [today] to return a basic date for today
*
*  The function nexform_getAutotagValues() will also check for any custom tags - where a matching function exists
*  The format of a tag is typically [tag:option]
*  Where 'tag' is a custom tag with a matching function defined in this file or other file - possibly another plugin
*
*/

/* Function needs to accept 2 parms where:
*  Parm 1 is the anything that is passed in the field defintion beween the [] tags
*  Parm 2 is the fieldid
*  The saved result_id should be passed in as a GET Var so with the Fieldid, you can test for a previous result
*  The current saved result is used to set the current selection in user is viewing or editing the form
*/


// Example function to return a list of options for a dropdown select field
function nx_getSalutations($p,$fieldid='') {
    global $_TABLES;

    $resultsid = COM_applyFilter($_GET['result'],true);

    if ($resultsid > 0 AND $fieldid != '') {
        $value = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='$fieldid'");
        return $value;
    } else {
        return 'Mr,Mrs,Miss,Dr';
    }
}


/* Example function to return an array of users for a dropdown
 * The field defintion can be [nx_getusers] to get a list of users sorted by userid
 * or [nx_getusers:username] to have the list sorted by username
 * The current saved result is used to set the current selection in user is viewing or editing the form
 *
 * @param       string   $p1        sort option - 'uid','username','fullname'
 * @param       int      $fieldid   Form Field ID passed automatically by nexForm library code
 * @return      string   array of values that will be used to create form field list options
*/
function nx_getusers ($parms,$fieldid) {
    global $_USER, $_TABLES;

    $validparms = array('uid','username','fullname');

    if (in_array($parms,$validparms)) {
        $order = $parms;
    } else {
        $order = 'uid';
    }

    $resultsid = COM_applyFilter($_GET['result'],true);
    $curvalue = 0;
    if ($resultsid > 0 AND $fieldid != '') {
        $curvalue = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='$fieldid'");
    }
    $query = DB_query("SELECT uid FROM {$_TABLES['users']} order by $order");
    $retval = array();
    while (list($uid) = DB_fetchArray($query)) {
        if (!empty($curvalue) AND $curvalue == $uid) {
            $retval['selected'] = $uid;
        }
        $retval[$uid] = COM_getDisplayName($uid);
    }
    return $retval;
}


/* Example function to return an array of users for a group to be used in a dropdown
 * The field defintion would be [nx_getgroupusers:'Logged-in Users'] to get a list of users sorted by userid
 * Default if not group passed in is the All Users group
 * The current saved result is used to set the current selection in user is viewing or editing the form
 * This function will handle groups within groups - so recursive groups to get a full list of users
 *
 * @param       string   $p1        Group Name - set in form definition field value autotag as in: [nx_getgroupusers:'Logged-in Users']
 * @param       int      $fieldid   Form Field ID passed automatically by nexForm library code
 * @return      string   array of values that will be used to create form field list options
*/
function nx_getgroupusers ($p1,$fieldid) {
    global $_CONF,$_USER,$_TABLES;

    $grp_id = DB_getItem($_TABLES['groups'],'grp_id',"grp_name='$p1'");
    if ($grp_id == 0) {
        $grp_id = 2;    // Default to the group 'All Users'
    }
    $resultsid = COM_applyFilter($_GET['result'],true);
    $curvalue = 0;
    if ($resultsid > 0 AND $fieldid != '') {
        $curvalue = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='$fieldid'");
    }

    $to_check = array ();
    array_push ($to_check, $grp_id);
    $groups = array ();
    while (sizeof ($to_check) > 0) {
        $thisgroup = array_pop ($to_check);
        if ($thisgroup > 0) {
            $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $thisgroup");
            $numGroups = DB_numRows ($result);
            for ($i = 0; $i < $numGroups; $i++) {
                $A = DB_fetchArray ($result);
                if (!in_array ($A['ug_grp_id'], $groups)) {
                    if (!in_array ($A['ug_grp_id'], $to_check)) {
                        array_push ($to_check, $A['ug_grp_id']);
                    }
                }
            }
            $groups[] = $thisgroup;
        }
    }
    $groupList = implode (',', $groups);

    $sql = "SELECT DISTINCT {$_TABLES['users']}.uid "
          ."FROM {$_TABLES['group_assignments']},{$_TABLES['users']}  "
          ."WHERE {$_TABLES['users']}.uid > 1 "
          ."AND {$_TABLES['users']}.uid = {$_TABLES['group_assignments']}.ug_uid "
          ."AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList})) "
          ."ORDER BY {$_TABLES['users']}.username ";

    $query = DB_query($sql);
    $retval = array();
    while (list($uid) = DB_fetchArray($query)) {
        if (!empty($curvalue) AND $curvalue == $uid) {
            $retval['selected'] = $uid;
        }
        $retval[$uid] = COM_getDisplayName($uid);
    }
    return $retval;
}


/* Custom tag function to pull a value from another form
*  Generic function that requires the tag to contain the formid and Template field id
*  Workflow needs to be using a tracking ID so that is maintained as a project
*
*  Tag is used in workflow that has several forms and subsequent forms need to pull initial values from a previous form and field
*  Example: [pullform:22,8]
*  This will pull the result of field 8 on form22 for this same project if the result exists
*/
function pullform($parms,$formFieldID='') {
    global $_GET,$_TABLES,$_CONF,$CONF_FE;

    /* Either the project_id (creating a new form response) or the result_id (editing) is passed as a GET Var */
    /* Use this to retrieve the project_id else the tag can not return any result */

    $projectid = COM_applyFilter($_GET['projectid'],true);
    $resultsid = COM_applyFilter($_GET['result'],true);
    $formid = COM_applyFilter($_GET['formid'],true);

    $aparms = explode(',',$parms);
    if ($resultsid == 0 AND $projectid > 0 ) {
        $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
    }

    $retval = '';
    // Retrieve the fieldid for this logical fieldid
    $query = DB_query("SELECT id,type From {$_TABLES['formFields']} WHERE  formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
    list ($fieldid, $field_type) = DB_fetchArray($query);

    if ($resultsid > 0) {
          if ($field_type == 'textarea1' OR $field_type == 'textarea2') {
              $retval = DB_getItem($_TABLES['formResText'], 'field_data',"result_id='$resultsid' AND field_id='{$formFieldID}'");
              if (trim($retval) == '') {
                //Form results has nothing for this field - pull from the source form referenced in lookup.
                $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                $retval = DB_getItem($_TABLES['formResText'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
              }
          } elseif ($field_type == 'multicheck') {
                $options = DB_getItem($_TABLES['formFields'], 'field_values',"formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
                $start = strpos($options, '[pullform:');
                if ($start !== FALSE) {
                    $len = strlen($options) - 11;
                    $newparms = substr($options, $start+10, $len);
                    $retval = pullform($newparms, $formFieldID);
                }
                else {
                    $aoptions = explode(',',$options);
                    // Retrieve the results from the source form referenced in lookup.
                    $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                    $selected = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
                    $aselected = explode(',',$selected);
                    $retval = array();
                    foreach ($aoptions as $option) {
                        if (in_array($option,$aselected)) {
                            $retval[$option] = 1;
                        } else {
                            $retval[$option] = 0;
                        }
                    }
                }
          } elseif ($field_type == 'radio') {
                $options = DB_getItem($_TABLES['formFields'], 'field_values',"formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
                $start = strpos($options, '[pullform:');
                if ($start !== FALSE) {
                    $len = strlen($options) - 11;
                    $newparms = substr($options, $start+10, $len);
                    $retval = pullform($newparms, $formFieldID);
                }
                else {
                    $aoptions = explode(',',$options);
                    // Retrieve the results from the source form referenced in lookup.
                    $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                    $selected = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
                    $aselected = explode(',',$selected);
                    $retval = array();
                    foreach ($aoptions as $option) {
                        if (in_array($option,$aselected)) {
                            $retval[$option] = 1;
                        } else {
                            $retval[$option] = 0;
                        }
                    }
                }

          } elseif ($field_type == 'select') {
                $options = DB_getItem($_TABLES['formFields'], 'field_values',"formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
                $start = strpos($options, '[pullform:');
                if ($start !== FALSE) {
                    $len = strlen($options) - 11;
                    $newparms = substr($options, $start+10, $len);
                    $retval = pullform($newparms, $formFieldID);
                }
                else {
                    $aoptions = explode(',',$options);
                    // Retrieve the results from the source form referenced in lookup.
                    $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                    $selected = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
                    $aselected = explode(',',$selected);
                    $retval = array();
                    foreach ($aoptions as $option) {
                        $option = htmlentities($option);
                        if (in_array($option,$aselected)) {
                            $retval['selected'] = $option;
                        }
                        $retval[$option] = $option;
                    }
                }

          } elseif ($field_type == 'mfile') {
                $mquery = DB_query("SELECT id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultsid' AND field_id='$fieldid'");
                $usetable = false;
                if (DB_numRows($mquery) > 0) {
                    $field_html = '<table><tr style="vertical-align:top;">';
                    $usetable = true;
                    $i = 0;
                }

                while (list ($rec,$field_value) = DB_fetchArray($mquery)) {
                    $field_html .= '<td align="left">';
                    $filename = explode(':',$field_value);
                    if (!empty($field_value)) {
                        $field_html .= LB . '<table><tr><td align="left">&nbsp;';
                        //$field_html .= "<a href=\"{$CONF_FE['downloadURL']}/{$filename[0]}\" target=\"_new\">";
                        $field_html .= "<a href=\"{$_CONF['site_url']}/fe/download.php?id=$rec\" target=\"_new\">";
                        $field_html .= "<img src=\"{$CONF_FE['image_url']}/document_sm.gif\" border=\"0\">{$filename[1]}</a>&nbsp;";
                        $field_html .= "<a href=\"#\" onClick='ajaxDeleteFile($formFieldID,$rec);'>";
                        $field_html .= "<img src=\"{$CONF_FE['image_url']}/delete.gif\" border=\"0\"></a>&nbsp;";
                        $field_html .= "<input type=\"hidden\" name=\"lfile_frm{$formid}_{$formFieldID}[]\" value=\"{$field_value}\">";
                        $field_html .= "</td>";
                        $field_html .= "</tr></table>" . LB;
                    } else {
                        $field_html = 'N/A&nbsp;';
                    }
                    $field_html .= '</td>';
                    $i++;
                    //if ($i%2 == 0 ) {
                        $field_html .= '</tr><tr style="vertical-align:top;">';
                    //}
                }
                if ($usetable) $field_html .= '</tr></table>';
                $retval = $field_html;


          } elseif (array_key_exists($field_type,$CONF_FE['customfieldmap'])) {
                $template = new Template($_CONF['path_layout'] . 'formEditor');
                $template->set_file('custom_field_rec','custom/' .$CONF_FE['customfieldmap'][$field_type]['record']);
                $template->set_var ('field_id',$formFieldID);  // Set this to the field ID for the form being generated
                $template->set_var ('form_id',$formid);

                $field_value = DB_getItem($_TABLES['formResText'], 'field_data', "result_id='$resultsid' AND field_id='$formFieldID'");
                if (trim($field_value) == '') {
                    //Form results has nothing for this field - pull from the source form referenced in lookup.
                     $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                     $field_value = DB_getItem($_TABLES['formResText'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
                }
                $field_value = unserialize($field_value);
                $i = 0;
                do {
                      $k = 1;
                      $template->set_var('row',$i);
                      if (is_array($field_value)) {
                          foreach ($field_value as $custom_value) {
                            $template->set_var("cust_value{$k}",$custom_value[$i]);
                            $k++;
                          }
                      }
                     $template->parse('product_detail_record','custom_field_rec');
                     $template->parse ('output', 'custom_field_rec');
                     $retval .= $template->finish ($template->get_var('output'));
                      $i++;

                } while(isset($custom_value[$i]));

          } else {
              $retval = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='{$formFieldID}'");
              if (trim($retval) == '') {
                //Form results has nothing for this field - pull from the source form referenced in lookup.
                $resultsid = DB_getItem($_TABLES['nfproject_forms'], 'results_id',"project_id='$projectid' AND form_id='{$aparms[0]}' ORDER BY id DESC");
                $retval = DB_getItem($_TABLES['formResData'], 'field_data',"result_id='$resultsid' AND field_id='{$fieldid}'");
              }
          }

    } elseif ($field_type == 'radio') {
        // No existing result but we want to show the default lables for thee options
        $options = DB_getItem($_TABLES['formFields'], 'field_values',"formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
        $aoptions = explode(',',$options);
        $retval = array();
        foreach ($aoptions as $option) {
            $retval[$option] = 0;
        }
    } elseif ($field_type == 'multicheck') {
        // No existing result but we want to show the default lables for thee options
        $options = DB_getItem($_TABLES['formFields'], 'field_values',"formid='{$aparms[0]}' AND tfid='{$aparms[1]}'");
        $aoptions = explode(',',$options);
        $retval = array();
        foreach ($aoptions as $option) {
            $retval[$option] = 0;
        }
    }

    if (trim($retval) == '') {
        // return a space so it will clear the field and not show the function in the field contents when form is displayed.
        $retval = ' ';
    }

    return $retval;

}



?>