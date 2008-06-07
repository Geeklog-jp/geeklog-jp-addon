<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

/* Show the list of nexlist List Definitions */
function nexlistShowDefinitions($plugin='',$category='')
{
    global $_USER,$_CONF,$_TABLES,$actionurl;

    $p = new Template($_CONF['path_layout'] . 'nexlist');
    $p->set_file (array (
        'page' => 'viewdefinitions.thtml',
        'javascript' => 'javascript/definitions.thtml',
        'def_rec'   => 'definition_record.thtml',
        'fields'    => 'definition_fields.thtml',
        'field_rec' => 'definition_field_record.thtml'));

    $p->set_var('actionurl',$actionurl);
    $p->set_var('LANG_DELCONFIRM', 'Are you sure you want to delete this definition?');
    if (!SEC_hasRights('nexlist.edit')) {
        $p->set_var('hide_adddef','none');
    }
    if ($GLOBALS['errmsg'] != '') {
        $p->set_var('error_msg',$GLOBALS['errmsg']);
    } else {
        $p->set_var('hide_errormsg','none');
    }

    $linkoptions = '';
    $imgset = $_CONF['layout_url'] . '/nexlist/images/admin/';
    $view_definition_icon = $imgset .'view.gif';
    $edit_definition_icon = $imgset .'edit.gif';
    $del_definition_icon = $imgset .'delete.gif';
    $copy_definition_icon = $imgset .'copy.gif';
    $editDefLink = '<a id="edefinition_%s" href="#" onClick="return false;"><img src="'.$imgset.'edit.gif" border="0" TITLE="Edit Definition"></a>';
    $copyDefLink = '<a href="'.$actionurl.'?op=copy_def&listid=%s"><img src="'.$copy_definition_icon.'" border="0" TITLE="Copy Definition"></a>';
    $delDefLink = '<a href="'.$actionurl.'?op=delete_def&listid=%s"><img src="'.$del_definition_icon.'" border="0" TITLE="Delete Definition"></a>';
    
    $sql = "SELECT * FROM {$_TABLES['nexlist']} ";
    if ($plugin != '') {
        $sql .= " WHERE plugin='$plugin' ";
        $p->set_var('show_plugin','none');
        $p->set_var('pluginmode',$plugin);
        $linkoptions = "&pluginmode=$plugin";
        if ($category != '') {
            $sql .= "AND category='$category'";
            $p->set_var('show_category','none');
            $p->set_var('new_category',$category);
            $p->set_var('catmode',$category);
            $linkoptions .= "&catmode=$category";
        }
    } elseif ($category != '') {
        $sql .= "WHERE category='$category'";
        $p->set_var('show_category','none');
        $p->set_var('new_category',$category);
        $p->set_var('catmode',$category);
        $linkoptions = "&catmode=$category";
    }

    $chk_editperms = true;
    if( !SEC_inGroup( 'Root', $uid )) {
        $GROUPS = SEC_getUserGroups( $_USER['uid'] );
        if ($plugin != '') {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        }
        $sql .= "view_perms IN (" . implode( ',', $GROUPS ) . ") ";
    }
    $sql .= " ORDER BY name";
    $DEF_query = DB_query($sql);
    $new_plugin_options = '<option value="all">All Plugins</option>';
    $query = DB_query("SELECT pi_name FROM {$_TABLES['plugins']}");
    while (list($pi_name) = DB_fetchArray($query)) {
        $new_plugin_options .= '<option value="'.$pi_name.'">'.$pi_name.'</option>';
    }
    $p->set_var ('new_editperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name'));
    $p->set_var('new_plugin_options',$new_plugin_options);

    $i = 1;
    $max_numfields = 0;
    $p->set_var('num_records', DB_numRows($DEF_query));
    while ($DEF = DB_fetchArray($DEF_query)) {
        $plugin_options = '<option value="all">All Plugins</option>';
        $query = DB_query("SELECT pi_name FROM {$_TABLES['plugins']}");
        while (list($pi_name) = DB_fetchArray($query)) {
            if ($pi_name == $DEF['plugin']) {
                $plugin_options .= '<option value="'.$pi_name.'" SELECTED=selected>'.$pi_name.'</option>';
            } else {
                $plugin_options .= '<option value="'.$pi_name.'">'.$pi_name.'</option>';
            }
        }        
        $p->set_var('rowid', $i);
        $p->set_var('cssid',$i%2+1);
        $p->set_var('definition_id',$DEF['id']);
        $p->set_var('definition_name',$DEF['name']);
        $p->set_var('plugin',$DEF['plugin']);
        $p->set_var('plugin_options',$plugin_options);
        $p->set_var('category',$DEF['category']);
        $p->set_var('description',nl2br($DEF['description']));
        
        $view_definition_url  = $actionurl.'?op=list_def&listid='.$DEF['id'];
        $p->set_var('view_definition_url',$view_definition_url . $linkoptions);
        $p->set_var('view_definition_icon',$view_definition_icon);
        
        if (SEC_inGroup($DEF['edit_perms'])) {
            
            $p->set_var('editperms_link',sprintf($editDefLink,$i));
            $p->set_var('copyperms_link',sprintf($copyDefLink,$DEF['id']));
            $p->set_var('delperms_link',sprintf($delDefLink,$DEF['id']));
            
            $p->set_var('edit_description',$DEF['description']);
            $p->set_var ('viewperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name',$DEF['view_perms']));          
            $p->set_var ('editperms',COM_optionList($_TABLES['groups'],'grp_id,grp_name',$DEF['edit_perms']));
            $p->set_var('editdef_link','<a href="#" onClick="editDefinition('.$i.');return false;">Edit Details</a>');

        } else {
            $p->set_var('editperms_link','');
            $p->set_var('copyperms_link','');
            $p->set_var('delperms_link','');                           
            $p->set_var('editdef_link','');
        }

        $sql = "SELECT * FROM {$_TABLES['nexlistfields']} WHERE lid='{$DEF['id']}' ORDER BY id";
        $FLD_query = DB_Query($sql);
        $numfields = DB_numrows($FLD_query);
        $max_numfields = ($numfields > $max_numfields) ? $numfields : $max_numfields;

        if ($numfields > 0) {
            $j=1;
            $p->set_var('show_fields','');
            $p->set_var('num_fields', $numfields);

            while ( $FLD = DB_fetchArray($FLD_query,false) ) {
                $edit_link = "&nbsp;[<a href=\"#\" onClick='editListField({$i},{$j});return false;'>Edit</a>&nbsp;]";
                $del_link = "&nbsp;[<a href=\"#\" onClick='ajaxUpdateDefinition(\"deleteField\",$i,$j);'\">Delete</a>&nbsp;]";
                $p->set_var('field_recid',$FLD['id']);
                $p->set_var('field_name',$FLD['fieldname']);
                $p->set_var('field_value',$FLD['value_by_function']);
                $p->set_var('field_width',$FLD['width']);
                $p->set_var('field_id',$j);

                if ($FLD['predefined_function'] == 1) {
                    $checked = 'CHECKED';
                    $display_ftext = 'none';
                    $display_fddown = '';
                    $p->set_var('function_dropdown_options', nexlist_getCustomListFunctionOptions($FLD['value_by_function']));
                }
                else {
                    $checked = '';
                    $display_ftext = '';
                    $display_fddown = 'none';
                    $p->set_var('function_dropdown_options', nexlist_getCustomListFunctionOptions());
                }

                $p->set_var('checked', $checked);
                $p->set_var('display_ftext', $display_ftext);
                $p->set_var('display_fddown', $display_fddown);

                $p->set_var('edit_link',$edit_link);
                $p->set_var('delete_link',$del_link);
                if ($j == 1) {
                    $p->parse('definition_field_records','field_rec');
                } else {
                    $p->parse('definition_field_records','field_rec',true);
                }
                $j++;
            }
            $p->parse('definition_fields','fields');

        } else {
            $p->set_var('show_fields','none');
            $p->set_var('definition_field_records','');
            $p->parse('definition_fields','fields');

        }

        $p->parse('definition_records','def_rec',true);
        $i++;
    }
    $p->set_var('max_numfields', $max_numfields);
    $p->parse ('javascript_code', 'javascript');
    $p->parse ('output', 'page');
    return $p->finish ($p->get_var('output'));

}

/* Show the list of List items for a selected List Definition */
function nexlistShowLists($listid,$page=0,$pluginmode='',$catmode='')
{
    global $_CONF,$_TABLES,$CONF_LL;

    $p = new Template($_CONF['path_layout'] . 'nexlist');
    $p->set_file (array (
        'page'           => 'viewitems.thtml',
        'javascript'     => 'javascript/listitems.thtml',
        'headingfield'   => 'listheading_field.thtml',
        'list_rec'       => 'list_record.thtml',
        'rec_field'      => 'list_record_field.thtml',
        'new_item'       => 'additem_record.thtml'));

    $actionurl = "{$_CONF['site_admin_url']}/plugins/nexlist/index.php";

    $query = DB_query("SELECT name,description FROM {$_TABLES['nexlist']} WHERE id='$listid'");
    list ($listname, $listdesc) = DB_fetchArray($query);
    $p->set_var('layouturl',$_CONF['layout_url']);
    $p->set_var('actionurl',$actionurl);
    $p->set_var('listid',$listid);
    $p->set_var('listname',$listname);
    $p->set_var('listdesc',$listdesc);
    $p->set_var('pluginmode',$pluginmode);
    $p->set_var('catmode',$catmode);
    if ($GLOBALS['errmsg'] != '') {
        $p->set_var('error_msg',$GLOBALS['errmsg']);
    } else {
        $p->set_var('hide_errormsg','none');
    }
    
    // Check if user has edit access to this list
    $GROUPS = SEC_getUserGroups( $_USER['uid'] );  // List of groups user is a member of
    $sql = "SELECT id FROM {$_TABLES['nexlist']} WHERE edit_perms IN (" . implode( ',', $GROUPS ) . ") AND id=$listid";
    if (DB_numRows(DB_query($sql)) != 1) {
        $editright = false; 
        $p->set_var('showhide_additem','hidden');
        $p->set_var('show_edit_actions','none');
    } else {
        $editright = true;
    }

    // Retrieve list of fields for this nexlist list and if a function is used for its value options
    $query = DB_query("SELECT id,fieldname,value_by_function,width FROM {$_TABLES['nexlistfields']} WHERE lid='$listid' ORDER BY id");
    $numfields = 0;
    while (list ($fieldid,$fieldname,$function,$width) = DB_fetchArray($query)) {
        $listfields[$fieldname] = $function;
        $listfieldwidths[$numfields] = $width;
        $numfields++;
    }

    if ($numfields == 0) {
        $p->set_var('help_msg', 'No fields have yet been defined for this definition');
        $p->set_var('showhide_additem','hidden');
    } else {
        $p->set_var('help_msg', 'The following are current list items. Click on [New Item] to add an new list item.');
        // Headings of list fields
        $p->set_var('heading_label','Order');
        $p->set_var('heading_cell_width', '');
        $p->parse('heading_fields','headingfield');
        $p->set_var('heading_label','ID');
        $p->set_var('heading_cell_width', '');
        $p->parse('heading_fields','headingfield',true);
        for ($i=0; $i < $numfields; $i++)  {
            $p->set_var('heading_label',key($listfields));
            $width = $listfieldwidths[$i];
            if ($width > 0) {
                $p->set_var('heading_cell_width',"width=\"{$width}%\"");
            }
            else {
                $p->set_var('heading_cell_width','');
            }
            $p->parse('heading_fields','headingfield',true);
            $p->set_var('newfield_name',key($listfields));
            $function = current($listfields);
            // If field uses a function and it exists - then return the display value
            if ( !empty($function) AND function_exists($function) ) {
                $edit_field_html = $function('edit',"field{$i}",$listvalues[$i],true);
                $p->set_var('newfield_html',$edit_field_html);
            } elseif (strpos($function,'list:') > 0) {   // Check if list autotag is used
                // Autotag being used - need to extract it and append to it to activate the read mode
                $autotag = explode('list:',$function);
                $autotag_contents = str_replace(']','',$autotag[1]);
                $varname = "field{$i}";
                $editautotag = "[list:{$autotag_contents},{$listvalues[$field]},edit,$varname]";
                $p->set_var('newfield_html',PLG_replacetags($editautotag,'nexlist'));
            } else {
                $p->set_var('newfield_html','<input type="text" name="field'.$i.'">');
            }
            $p->parse('additem_record','new_item',true);
            next($listfields);
        }
        if ($editright) {
            $p->set_var('heading_label','Actions');
            $p->set_var('heading_cell_width', '');
            $p->parse('heading_fields','headingfield',true);
        }
        $numRecords = DB_count($_TABLES['nexlistitems'],lid,$listid);
        $numpages = ceil($numRecords / $CONF_LL['pagesize']);
        if ($page > 0) {
            $offset = ($page - 1) * $CONF_LL['pagesize'];
        } else {
            $offset = 0;
            $page = 1;
        }

        // Retrieve the list records and field values - checking if field uses a function
        $query = DB_query("SELECT * FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' ORDER BY itemorder asc, id asc LIMIT $offset,{$CONF_LL['pagesize']}");
        $cssid = 1;
        $p->set_var('num_records', DB_numRows($query));
        $p->set_var('num_fields', $numfields);

        $rowid = 1;
        while ( $B = DB_fetchArray($query,false)) {
            $p->set_var('cssid',$rowid%2+1);
            $p->set_var('list_recid',$B['id']);
            $p->set_var('list_order',$B['itemorder']);
            $p->parse('listrec_fields','rec_field');
            $p->set_var('rowid', $rowid);

            $listvalues = explode(',',$B['value']);
            reset($listfields);
            for ($field = 0; $field < $numfields; $field++) {
                $fldname = "item_{$rowid}_field{$field}";
                $p->set_var('fieldid',$field);
                $function = current($listfields);
                // If field uses a function and it exists - then return the display value
                if ( !empty($function) AND function_exists($function) ) {
                    $fieldvalue = $function('read','',$listvalues[$field]);
                    $p->set_var('field_value',$fieldvalue);
                    $edit_field_html = $function('edit',$fldname,$listvalues[$field]);
                    $p->set_var('edit_field_html',$edit_field_html);
                } elseif (strpos($function,'list:') > 0) {   // Check if list autotag is used
                    // Autotag being used - need to extract it and append to it to activate the read mode
                    $autotag = explode('list:',$function);
                    $autotag_contents = str_replace(']','',$autotag[1]);
                    $readautotag = "[list:{$autotag_contents},{$listvalues[$field]},read]";
                    $fieldvalue = PLG_replacetags($readautotag,'nexlist');
                    $p->set_var('field_value',$fieldvalue);
                    $varname = "item_{$rowid}_field{$field}";
                    $editautotag = "[list:{$autotag_contents},{$listvalues[$field]},edit,$varname]";
                    $p->set_var('edit_field_html',PLG_replacetags($editautotag,'nexlist'));
                } else {
                    $fieldvalue = $listvalues[$field];
                    $p->set_var('field_value',$fieldvalue);
                    $p->set_var('edit_field_html','<input type="text" name="'.$fldname.'" value="'.$fieldvalue.'">');
                }
                next($listfields);
                if ($field == 0) {
                    $p->parse('listrec_fields','rec_field');
                } else {
                    $p->parse('listrec_fields','rec_field',true);
                }
            }

            $editlink = '[&nbsp;<a href="#" onClick="document.nexlist.op.value=\'edititem\';document.nexlist.item.value=\''.$B['id']. '\';nexlist.submit();">Edit</a>&nbsp;]';
            $deletelink = '&nbsp;[&nbsp;<a href="#">Delete</a>&nbsp;]';
            $p->set_var('edit_action',$editlink);
            $p->set_var('delete_action',$deletelink);
            $p->parse('list_records','list_rec',true);
            $rowid++;

            // For each list item - create the edit div and form

        }
    }
    $base_url = $_CONF['site_admin_url'] . '/plugins/nexlist/index.php?op=list_def&listid='.$listid;
    $p->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));
    $p->parse ('javascript_code', 'javascript');
    $p->parse ('output', 'page');
    $retval = $p->finish ($p->get_var('output'));
    return $retval;

}

function nexlistGetDefinitions ( $plugin, $category='')
{
    global $_TABLES;

    $lists = array();
    $sql = "SELECT id,name FROM {$_TABLES['nexlist']} WHERE plugin='$plugin'";
    if ($category != '') {
        $sql .= " AND category='$category'";
    }
    $query = DB_query($sql);
    while (list ($id, $name) = DB_fetchArray($query)) {
        $lists[$id] = $name;
    }
    return $lists;
}

function nexlist_getCustomListFunctionOptions($selected='') {
    $fnary = nexlist_getCustomListFunctions();
    $options = '';

    foreach ($fnary as $fn) {
        if ($fn == $selected) {
            $options .= "\n<option value=\"$fn\" SELECTED>$fn</option>";
        }
        else {
            $options .= "\n<option value=\"$fn\">$fn</option>";
        }
    }

    return $options;
}

function nexlist_getCustomListFunctions() {
    global $_CONF;
    $retval = array();

    $stream = @fopen ($_CONF['path'] . 'plugins/nexlist/custom.php', 'r');
    if ($stream) {
        while ($line = fgets($stream)) {
            if (feof($stream)) {
                break;
            }

            $line = trim($line);
            $pos = strpos($line, '(');
            $line = substr($line, 0, $pos);  //by now line should be "function functionname"
            $line_arr = explode(' ', $line);
            if ($line_arr[0] = 'function') {
                $len = count($line_arr);
                $functionname = $line_arr[$len - 1];
                if (substr($functionname, 0, 7) == 'nexlist') {    //we have a valid custom function
                    $retval[] = $functionname;
                }
            }
        }

        fclose($stream);
    }

    return $retval;
}


/**
* Creates an <option> list from a database list for use in forms
*
* Creates option list form field using given arguments
*
* @param        string         $mode       modes are edit,read,options, or alist. 
*                                          'edit' returns a HTML selectbox,
*                                          'read' just the value
*                                          'fread' just read the value, but take into account any functions
*                                          'options' returns the select options without the <select> </select> HTML
*                                          'alist' an array of values.
* @param        string         $varname    Fieldname to use for the formated HTML
* @param        string         $listid     ID of the nexlist List Definition to get data from
* @param        string         $fieldnum   Fieldnum from the list to be used in for the selectbox options.
* @param        string/array   $selected   Value(s) (from $selection) to set to SELECTED or default
* @param        string         $where      Optional Value(s) to use in where clause.
*                                          Format 'fieldnum:match,fieldnum:match' - can be 1 match or multiple
* @param        integer        $classname  Option value used by Dynamic Selectbox Javascript to show a filtered list of options
$ @param        boolean        $noDefault  If set true then don't add the <option value=0>Select Value</option>

* @return   string  Formated HTML of option values
*
* Examples: $mktgMgrUid = nexlistOptionList('read','',43, 2, '',"0:$division,1:$product");
*           Read from list 43 - get list field 3 where field1 matches the division and field2 matches the product
*           Returns a single value
*
*/
function nexlistOptionList( $mode, $varname, $listid, $fieldnum=0, $selected='', $where='', $classvalue=-1, $noDefault=false, $sortOrder='')
{
    global $_TABLES;
    $retval = '';
    $options = array();

    //check to see if this field uses a function, and if so, which function
    $used_lists = array();  //keep track of lists used to stop possible infinate loop
    while (1) {
        if (!in_array("$listid,$fieldnum", $used_lists)) {
            $used_lists[] = "$listid,$fieldnum";
            $res = DB_query("SELECT value_by_function FROM {$_TABLES['nexlistfields']} WHERE lid=$listid");
            $i = -1;
            while (($i != $fieldnum) AND ($R = DB_fetchArray($res))) {
                $func = $R['value_by_function'];
                $i++;
            }
            if (strpos($func, '[list:') !== false) {
                $values = str_replace ('[list:', '', $func);
                $values = str_replace (']', '', $values);
                $v_arr = explode(',', $values);
                $listid = $v_arr[0];
                $fieldnum = $v_arr[1];
            }
            else {
                break;
            }
        }
        else {
            break;
        }
    }

    // Check if where option is passed in and valid - ie has 2 parms
    $whereoption = false;
    $whereoptions = explode(',',$where);
    if (count($whereoptions) > 0) {
        $whereparms = array();
        foreach ($whereoptions as $whereclause) {
            if ($whereclause != '' and strpos($whereclause,':') > 0) {
                // Break out the two parms in where clause - List Field ID and Value
                $parms = explode(':',$whereclause);
                if (count($parms) == 2) {
                    $whereparms[$parms['0']] = $parms['1'];
                    $whereoption = true;
                }
            }
        }
    }

    if ($CONF_LL['debug']) {
        COM_errorLOG("nexlistOptionList -> mode:$mode, varname:$varname,listid:$listid,fieldnum:$fieldnum, selected:$selected");
    }

    // Get field id for the selected field
    $q1 = DB_query("SELECT fieldname FROM {$_TABLES['nexlistfields']} WHERE lid='$listid' ORDER BY id");
    if (DB_numRows($q1) == 0) {
        return '';
    }

    $q2 = DB_query("SELECT id, value FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' ORDER BY itemorder asc, id asc");
    while (list ($id, $values) = DB_fetchArray($q2)) {
        $avalues = explode(',',$values);
        if ($whereoption) {
            $match = false;
            // Cycle thru the Where fields and look for a match - Array key is the list Field ID
            $match = 0;
            foreach ($whereparms as $key => $matchvalue) {
                if ($avalues[$key] == $matchvalue) {
                    $match++;
                }
            }
            if ($match == count($whereparms)) {
                if (function_exists($func) AND $mode != 'read') {
                    $options[$id] = $func('view', '', $options[$id]);
                }
                else {
                    $options[$id] = $avalues[$fieldnum];
                }
            }
        } else {
            if (function_exists($func) AND $mode != 'read') {
                $options[$id] = $func('view', '', $avalues[$fieldnum]);
            }
            else {
                $options[$id] = $avalues[$fieldnum];
            }
        }
    }

    //sort the array
    if ($sortOrder == 'asc') {
        asort($options);
    }
    else if ($sortOrder == 'desc') {
        arsort($options);
    }

    if ($mode == 'edit') {
        $retval = '<select id="'.$varname.'" name="'.$varname.'">';
        if (!$noDefault) $retval .= '<option value="0">Select Value</option>';
        foreach ($options as $key => $label) {
            if ($classvalue != -1) {
                $A = explode(',', DB_getItem($_TABLES['nexlistitems'], 'value', "id=$key"));
                $class = "class=\"{$A[$classvalue]}\" ";
            }
            if ($key == $selected) {
                $retval .= '<option '.$class.'value="'.$key.'" SELECTED>'.$label.'</option>';
            } else {
                $retval .= '<option '.$class.'value="'.$key.'">'.$label.'</option>';
            }
        }
        $retval .= '</select>';

    } elseif ($mode == 'options') {
        if (!$noDefault) $retval .= '<option value="0">Select Value</option>';
        foreach ($options as $key => $label) {
            if ($classvalue != -1) {
                $A = explode(',', DB_getItem($_TABLES['nexlistitems'], 'value', "id=$key"));
                $class = "class=\"{$A[$classvalue]}\" ";
            }
            $aselected = array();
            if (!is_array($selected)) {
                $aselected[] = $selected;
            } else {
                $aselected = $selected;
            }
            if (in_array($key,$aselected)) {
                $retval .= '<option '.$class.'value="'.$key.'" SELECTED>'.$label.'</option>';
            } else {
                $retval .= '<option '.$class.'value="'.$key.'">'.$label.'</option>';
            }
        }

    } elseif ($mode == 'alist') {
        if ($classvalue != -1) {
            $classOptions = array();
            foreach ($options as $key => $label) {
                $A = explode(',', DB_getItem($_TABLES['nexlistitems'], 'value', "id=$key"));
                $class = "class={$A[$classvalue]} ";
                $classOptions[$key] = "$label,$class";
            }
            return $classOptions;
        } else {
            return $options;
        }

    } else {
        if ($selected != '' AND count($options) > 1) {
            return $options[$selected];
        } else {
            return current($options);
        }
    }

    return $retval;
}

/**
* Generate an array of actual values for the passed in list
* Do not determine if a function is used for the field - return the raw values
* If a field contains multiple values, the result will be delimited by a colan.
* Example: a list record for a field which contains multiple users will have a value of 2:10:99:5 for the 4 user_id's
*
* Creates option list form field using given arguments
*
* @param        string   $listid     ID of the nexlist List Definition to get data from
* @param        string   $fieldnum   Fieldnum from the list to be used in for the selectbox options.
                         
* @return       array    array of values
*
*
*/
function nexlistRawValues( $listid, $fieldnum=0)
{
    global $_TABLES;
    $query = DB_query("SELECT id, value FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' ORDER BY itemorder asc, id asc");
    $retval = array();
    while (list ($id, $values) = DB_fetchArray($query)) {
        $avalues = explode(',',$values);
        if ($fieldnum > 0) {
            $retval[$id] = $avalues[$fieldnum];
        } else {
            $retval[$id] = $avalues;
        }
    }
    return $retval;
}


/* Return a specific field from a nexlist matching the record id 
* @param   string    $listid  ID of the nexlist List Definition to get data from
* @param   string    $id      Record ID of list records to return values for
* @param   string    $field   Field number to return from requested record
*/
function nexlistValue ($listid,$id,$field=0) {
    global $_TABLES;

    $query = DB_query("SELECT value FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' and id='$id'");
    list ($value) = DB_fetcharray($query);
    $avalue = explode (',',$value);
    return $avalue[$field];

}

/* Return the fieldID or Key from a nexlist matching a value */
function nexlistKey ($listid,$match) {
    global $_TABLES;

    $query = DB_query("SELECT id FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' and value='$match'");
    if (DB_numRows($query) == 0) {  // nexlist value may be part of a list with multiple fields
        $query = DB_query("SELECT id FROM {$_TABLES['nexlistitems']} WHERE lid='$listid' and value like '%$match%'");
    }
    list ($id) = DB_fetcharray($query);
    return $id;

}
?>