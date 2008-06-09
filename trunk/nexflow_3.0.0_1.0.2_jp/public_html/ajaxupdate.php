<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate.php                                                            |
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

require_once("../lib-common.php"); // Path to your lib-common.php

$id = COM_applyFilter($_GET['id'],true);           // Item ID of the list of users to add
$rid = COM_applyFilter($_GET['rid'],true);         // Form record id
$op = COM_applyFilter($_GET['op']);                // Operation
$list = COM_applyFilter($_GET['var1']);            // Passed Var1 - List of current select users

if ($CONF_NF['debug']) {
    COM_errorLog("nexFlow - ajaxupdate.php: id:$id, rid:$rid, op:$op, var1:$list");
}

if (DB_count($_TABLES['lookuplistitems'],'id',$id) >= 1) {
    $sql  = "SELECT item.value,item.lid,listdef.fieldname,listdef.value_by_function FROM {$_TABLES['lookuplistitems']} item ";
    $sql .= "LEFT JOIN {$_TABLES['lookuplistfields']} listdef ON item.lid=listdef.lid ";
    $sql .= "LEFT JOIN {$_TABLES['lookuplists']} list ON item.lid=list.id WHERE item.id='$id' AND list.plugin='all' ORDER BY listdef.id";
    $query = DB_query($sql);
    $newusers = array();
    $i =0;

    // Loop thru the fields for this list looking for one of the expected function names
    while (list ($values,$lid,$fieldname,$function) = DB_fetchArray($query)) {
        if ($CONF_NF['debug']) {
            COM_errorLog("Loop:$i, Values: $values, function:$function");
        }
        $avalue = explode(',',$values);     // convert into array to make referencing easier
        if ($function == 'lookupGetUsers') {
            $user = DB_getItem($_TABLES['users'],'username',"uid='{$avalue[$i]}'");
            if (!in_array($user,$newusers) AND strpos($list,$user) === FALSE) {
                $newusers[] = DB_getItem($_TABLES['users'],'username',"uid='{$avalue[$i]}'");
                if ($CONF_NF['debug']) {
                    COM_errorLog("lid:$lid, fieldname:$fieldname, value:{$avalue[$i]}");
                }
            }
        } elseif ($function == 'lookupGetMultipleUsers') {
            // Value returned from list item will have an list of user id's separated by a ':'
            $ausers = explode(':',$avalue[$i]);
            foreach ($ausers as $uid) {
                $user = DB_getItem($_TABLES['users'],'username',"uid='$uid'");
                if (!in_array($user,$newusers) AND strpos($list,$user) === FALSE) {
                    $newusers[] = DB_getItem($_TABLES['users'],'username',"uid='$uid'");
                    if ($CONF_NF['debug']) {
                        COM_errorLog("Multiple Users, lid:$lid, fieldname:$fieldname, value:$uid");
                    }
                }
            }
        }
        $i++;
    }
    if (trim($list) != '' AND count($newusers) > 0) {
        $list = $list . ',' . implode(',',$newusers);
    } else {
        $list = implode(',',$newusers);
    }
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$XML = "<result>";
$XML .= "<mode>$mode</mode>";
$XML .= "<operation>$op</operation>";
$XML .= "<id>$id</id>";
$XML .= "<rid>$rid</rid>";
$XML .= "<list>$list</list>";
$XML .= "</result>";
print $XML;



?>