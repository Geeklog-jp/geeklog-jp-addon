<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate.php                                                            |
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

require_once("../../../lib-common.php"); // Path to your lib-common.php

$mode = COM_applyFilter($_GET['mode']);
$did = COM_applyFilter($_GET['did'],true);         // nexlist Definition ID
$rid = COM_applyFilter($_GET['rid'],true);         // Row ID in the users listing
$fid = COM_applyFilter($_GET['fid'],true);         // Field ID
$itemid = COM_applyFilter($_GET['itemid'],true);   // ListItem ID
$op = COM_applyFilter($_GET['op']);                // Operation

/* Need special filtering of var1 - which is can be the multiple field values separated by a comma
 * COM_applyFilter was filtering everything after the comma! ie 2,8,10:11:12:13 became 2
*/
if(!get_magic_quotes_gpc()) {
    $var1 = addslashes(htmlspecialchars($_GET['var1']));   // Passed Var1 - usually the fieldname
    $var1 = str_replace('&amp;', '&', $var1);
}
else {
    $var1 = htmlspecialchars($_GET['var1']);       // Passed Var1 - usually the fieldname
    $var1 = str_replace('&amp;', '&', $var1);
}
$var2 = ppFilterText($_GET['var2']);            // Passed Var2 - usually the field value
$var3 = intval($_GET['var3']);          // Passed Var3 - field width
$var4 = intval($_GET['var4']);          // Passed Var4 - predefined_function (1 or 0)

// Check if user has edit access to this list
$GROUPS = SEC_getUserGroups( $_USER['uid'] );  // List of groups user is a member of
$sql = "SELECT id FROM {$_TABLES['nexlist']} WHERE edit_perms IN (" . implode( ',', $GROUPS ) . ") AND id=$did";
if (DB_numRows(DB_query($sql)) != 1) {
    COM_accessLog("WARNING: nexlist Admin- Invalid access to ajaxupdate.php by user: {$_USER['uid']}");
    exit();
}

if ($CONF_LL['debug']) {
    COM_errorLog("nexlist - ajaxupdate.php: did:$did, rid:$rid, fid:$fid, itemid:$itemid");
    COM_errorLog("nexlist - var1: $var1, var2: $var2");
}

$retval = '';

// Return a HTML formatted table of the nexlist list fields that will be replaced in the users browser
function generatenexlistFieldHTML($did,$row) {

    global $_CONF,$_TABLES;

    $p = new Template($_CONF['path_layout'] . 'nexlist');
    $p->set_file (array (
        'fields'    => 'definition_fields.thtml',
        'field_rec' => 'definition_field_record.thtml'));

    $p->set_var('definition_id',$did);
    $p->set_var('rowid',$row);

    $sql = "SELECT * FROM {$_TABLES['nexlistfields']} WHERE lid='{$did}' ORDER BY id";
    $FLD_query = DB_Query($sql);
    $numfields = DB_numrows($FLD_query);

    if ($numfields > 0) {
        $j=1;

        $p->set_var('show_fields','');
        while ( $FLD = DB_fetchArray($FLD_query,false) ) {
            $edit_link = "&nbsp;[<a href=\"#\" onClick='editListField({$row},{$j});'>Edit</a>&nbsp;]";
            $del_link = "&nbsp;[<a href=\"#\" onClick='ajaxUpdateDefinition(\"deleteField\",{$row},{$j});'\">Delete</a>&nbsp;]";
            $p->set_var('field_recid',$FLD['id']);
            $p->set_var('field_name',$FLD['fieldname']);
            $p->set_var('field_value',$FLD['value_by_function']);
            $p->set_var('field_width',$FLD['width']);
            $p->set_var('field_id',$j);
            $p->set_var('edit_link',$edit_link);
            $p->set_var('delete_link',$del_link);

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
    }
    $p->parse ('output', 'fields');
    $html = $p->finish ($p->get_var('output'));
    $html = htmlentities($html);
    return $html;

}

if ($op == 'updateField') {
    if (!empty($var1)) {
        if ($CONF_LL['debug']) {
            COM_errorLog("Ajaxupdate: UPDATE {$_TABLES['nexlistfields']} SET fieldname = '{$var1}', value_by_function = '{$var2}' WHERE id='{$fid}'");
        }
        DB_query("UPDATE {$_TABLES['nexlistfields']} SET fieldname = '{$var1}', value_by_function = '{$var2}', width = {$var3}, predefined_function = {$var4} WHERE id='{$fid}'");
    }
    $retval = 'record updated';
} elseif ($op == 'addField') {
    if (!empty($var1)) {
        DB_query("INSERT INTO {$_TABLES['nexlistfields']} (lid,fieldname,value_by_function,width,predefined_function) VALUES ('{$did}','{$var1}','{$var2}','{$var3}','{$var4}')");
    }
    $retval =  generatenexlistFieldHTML($did,$rid);
} elseif ($op == 'deleteField') {
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE id='{$fid}'");
    $retval =  generatenexlistFieldHTML($did,$rid);
} elseif ($op == 'updateItemField') {
    if ($CONF_LL['debug']) {
        COM_errorLog("Ajaxupdate: UPDATE {$_TABLES['nexlistitems']} SET value = '{$var1}' WHERE id='{$itemid}'");
    }
    DB_query("UPDATE {$_TABLES['nexlistitems']} SET value = '{$var1}' WHERE id='{$itemid}'");

    $values = explode(',',$var1);

    $query = DB_query("SELECT id, value_by_function FROM {$_TABLES['nexlistfields']} WHERE lid='{$did}' ORDER BY id");
    $retval = '';

    // Cycle through the fields and the passed in matching values as selections
    while (list($id, $function) = DB_fetchArray($query)) {
        if ( !empty($function) AND function_exists($function) ) {
            $fieldvalue = $function('read','',current($values));
            if ($CONF_LL['debug']) {
                COM_errorLog("Ajaxupdate: Function: $function AND value: $fieldvalue");
            }
            
        } elseif (strpos($function,'list:') > 0) {   // Check if list autotag is used
            // Autotag being used - need to extract it and append to it to activate the read mode
            $autotag = explode('list:',$function);
            $autotag_contents = str_replace(']','',$autotag[1]);
            $readautotag = "[list:{$autotag_contents}," . current($values).",read]";
            $fieldvalue = PLG_replacetags($readautotag,'nexlist');
            if ($CONF_LL['debug']) {
                COM_errorLog("Ajaxupdate: Tag: $readautotag AND value: $fieldvalue");
            }
    } else {
            $fieldvalue =  current($values);
        }
        $fieldvalue = htmlspecialchars($fieldvalue);

        if ($retval == '') {
            $retval = $fieldvalue;
        } else {
            $retval .= ':' . $fieldvalue;
        }
        next($values);
    }
    // If this field uses a function then we want to return the new value for the updated item

}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$XML = "<result>";
$XML .= "<mode>$mode</mode>";
$XML .= "<operation>$op</operation>";
$XML .= "<did>$did</did>";
$XML .= "<rid>$rid</rid>";
$XML .= "<fid>$fid</fid>";
$XML .= "<data>$retval</data>";
$XML .= "</result>";
print $XML;
?>