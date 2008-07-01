<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | custom.php                                                                |
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

function nexlistGetRights($mode,$var='',$selected='',$addBracketsToFieldName=false) {
    if ($mode == 'edit') {
        $retval .= '<select name="'.$var.'">';
        $retval .= '<option value="0">Select Value</option>';
        for ($i = 1; $i <= 5; $i++) {
            if ($i == $selected) {
                $retval .= '<option value="'.$i.'" SELECTED>Value '.$i.'</option>';
            } else {
                $retval .= '<option value="'.$i.'">Value '.$i.'</option>';
            }
        }
        $retval .= '</select>';
    } else {
        $retval = "Value{$selected}";
    }
    return $retval;
}

function nexlistGetUsers($mode,$var,$selected='',$addBracketsToFieldName=false) {
    global $_TABLES;

    if ($mode == 'edit') {
        $retval .= '<select name="'.$var.'">';
        $retval .= NXCOM_listUsers($selected);
        $retval .= '</select>';
    } else {
        $fullname = DB_getItem($_TABLES['users'],'fullname',"uid='$selected'");
        if (!empty($fullname)) {
            $retval = $fullname;
        } else {
            $retval = DB_getItem($_TABLES['users'],'username',"uid='$selected'");
        }        
    }
    return $retval;
}

function nexlistGetMultipleUsers($mode,$var,$selected='',$addBracketsToFieldName=false) {
    global $_TABLES;

    $retval = '';
    if ($mode == 'edit') {
        $selected = explode(':',$selected);
        if ($addBracketsToFieldName) {
            $var .= '[]';
        }
        $retval .= '<select name="'.$var.'" multiple size="10" style="width:110px;">';
        $retval .= NXCOM_listUsers($selected); 
        $retval .= '</select>';
    } else {
        $selected = explode(':',$selected);
        foreach ($selected as $user) {
          if ($retval != '') $retval .= ', ';
          $fullname = DB_getItem($_TABLES['users'],'fullname',"uid='$user'");
          if (!empty($fullname)) {
              $retval .= $fullname;
          } else {
              $retval .= DB_getItem($_TABLES['users'],'username',"uid='$user'");
          }
        }
    }
    return $retval;
}

?>