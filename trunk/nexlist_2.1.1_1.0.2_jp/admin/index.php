<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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

require_once ('../../../lib-common.php');
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

$op = COM_applyFilter($_REQUEST['op']);
$page = COM_applyFilter($_REQUEST['page'],true);
$listid = COM_applyFilter($_REQUEST['listid'],true);
if ($_POST['defid'] > 0) {
    $listid = COM_applyFilter($_POST['defid'],true);
}
$itemid = COM_applyFilter($_REQUEST['itemid'],true);

/* Check and see if user has admin access to this list */
if ($op != 'list_def' AND $listid > 0 ) {
    $GROUPS = SEC_getUserGroups( $_USER['uid'] );  // List of groups user is a member of
    $sql = "SELECT id FROM {$_TABLES['nexlist']} WHERE edit_perms IN (" . implode( ',', $GROUPS ) . ") AND id=$listid";
    if (DB_numRows(DB_query($sql)) != 1) {
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
}

function update_list_itemorder($listid) {
    global $_TABLES;
    $new_order = 0;

    $listrecs = DB_query("SELECT * FROM {$_TABLES['nexlistitems']} WHERE lid=$listid ORDER BY itemorder asc, id asc;");
    while ($LIST_RES = DB_fetchArray($listrecs)) {
        $new_order += 10;
        DB_query("UPDATE {$_TABLES['nexlistitems']} SET itemorder=$new_order WHERE id={$LIST_RES['id']};");
    }
}

// Used if we want the nexlist lists to be resticted to a plugin or category default mode
$pluginmode = COM_applyFilter($_REQUEST['pluginmode']);
$catmode = COM_applyFilter($_REQUEST['catmode']);

if ($pluginmode != '') {
    $optionlink = "pluginmode=$pluginmode";
    if ($catmode != '') {
        $optionlink .= "&catmode=$catmode";
    }
} elseif ($catmode != '') {
    $optionlink = "catmode=$catmode";
} else {
    $optionlink = '';
}

$actionurl = $_CONF['site_admin_url'] .'/plugins/nexlist/index.php';

echo COM_siteHeader('menu');
echo COM_startBlock('','',"blockheader.thtml");

if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
    if (!empty ($msg)) {
        $plugin = '';
        if (isset ($_REQUEST['plugin'])) {
            $plugin = COM_applyFilter ($_REQUEST['plugin']);
        }
        echo COM_showMessage ($msg, $plugin);
    }
}

$navbar = new navbar;
$menuitem_url  = "{$_CONF['site_admin_url']}/plugins/nexlist/index.php?{$optionlink}";
$navbar->add_menuitem('List Definitions',$menuitem_url);

if ($listid > 0 AND ($op == 'list_def' OR $op == 'add_item' OR $op == 'delete_item')) {
    $listname = DB_getItem($_TABLES['nexlist'],'name', "id='{$listid}'");
    $menuitem_url = "{$_CONF['site_admin_url']}/plugins/nexlist/index.php?op=list_def&listid={$listid}&{$optionlink}";
    $navbar->add_menuitem($listname, $menuitem_url);
}
echo $navbar->generate();

switch ($op) {

    case 'add_item':
        $numfields = COM_applyFilter($_POST['numfields']);
        $data = array();
        for ($i = 0; $i < $numfields ; $i++) {
            if (is_array ($_POST["field{$i}"])) {
                $data[] = COM_applyFilter(implode(':', $_POST["field{$i}"]));
            }
            else {
                $tmp = COM_applyFilter($_POST["field{$i}"]);
                $tmp = str_replace(',', '&#44;', $tmp);
                $tmp = str_replace(':', '&#58;', $tmp);
                $data[] = $tmp;
            }
        }
        if (count($data) > 0) {
            $values = implode(',',$data);
        }
        if (!empty($values)) {
            $order_value = DB_count($_TABLES['nexlistitems'], 'lid', $listid);
            $order_value++;
            $order_value *= 10;
            DB_query("INSERT INTO {$_TABLES['nexlistitems']} (lid,value,active,itemorder) VALUES ('{$listid}','{$values}',1,$order_value)");
        }
        echo nexlistShowLists($listid,0,$pluginmode,$catmode);
        break;

    case 'update_item':
        $numfields = COM_applyFilter($_POST['numfields']);

        $data = array();
        for ($i = 0; $i < $numfields ; $i++) {
            $data[] = COM_applyFilter($_POST["field{$i}"]);
        }
        if (count($data) > 0) {
            $values = implode(',',$data);
        }
        if (!empty($values)) {
            DB_query("UPDATE {$_TABLES['nexlistitems']} SET value = '{$values}' WHERE id='{$itemid}'");
        }
        echo nexlistShowLists($listid);
        break;

    case 'delete_item':
        // Check to see if any plugin is using this item
        $ret = nexlist_checkItemDependencies($itemid);
        $listid = DB_getItem($_TABLES['nexlistitems'], 'lid', "id=$itemid");
        if ($ret == '') {
            DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE id='{$itemid}'");
        } else {
            $GLOBALS['errmsg'] = "Warning Delete aborted:&nbsp;$ret";
        }
        update_list_itemorder($listid);
        echo nexlistShowLists($listid,0,$pluginmode,$catmode);
        break;

    case 'order':
        //collect information
        $recid = COM_applyFilter($_POST['activerec'], true);
        $order = COM_applyFilter($_POST['order' . $recid], true);
        $listid = COM_applyFilter($_POST['listid'], true);
        $new_order = 0;

        //update the record
        DB_query("UPDATE {$_TABLES['nexlistitems']} SET itemorder=$order WHERE id=$recid;");

        //now update all records, with their new order, incremented by 10
        update_list_itemorder($listid);

        echo nexlistShowLists($listid,0,$pluginmode,$catmode);
        break;

    case 'list_def':
        echo nexlistShowLists($listid,$page,$pluginmode,$catmode);
        break;

    case 'add_definition':
        $name = COM_applyFilter($_POST['definition_name']);
        $plugin = COM_applyFilter($_POST['definition_plugin']);
        $category = COM_applyFilter($_POST['definition_category']);
        $viewperm = COM_applyFilter($_POST['definition_viewperm']);
        $editperm = COM_applyFilter($_POST['definition_editperm']);
        $description = COM_applyFilter($_POST['definition_description']);
        if (!empty($name)) {
            $sql = "INSERT INTO {$_TABLES['nexlist']} (name,plugin,category,description,view_perms,edit_perms) VALUES ";
            $sql .= "('{$name}','{$plugin}','{$category}','{$description}','{$viewperm}','{$editperm}')";
            DB_query($sql);
        }
        else {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins/nexlist/index.php?msg=1&plugin=nexlist');
            exit();
        }
        echo nexlistShowDefinitions($pluginmode, $catmode);
        break;

    case 'copy_def':
        if (DB_count($_TABLES['nexlist'],'id',$listid) == 1) {
            $source = DB_query("SELECT * FROM {$_TABLES['nexlist']} WHERE id='$listid'");
            $A = DB_fetchArray($source);
            $A['name'] = "{$A['name']}-copy";
            $sql = "INSERT INTO {$_TABLES['nexlist']} (name,plugin,category,description,view_perms,edit_perms) VALUES ";
            $sql .= "('{$A['name']}','{$A['plugin']}','{$A['category']}','{$A['description']}','{$A['view_perms']}','{$A['edit_perms']}')";
            DB_query($sql);

            $newid = DB_insertID();

            // Retrieve all the field defintions and add them to the new copy definition
            $query = DB_query("SELECT * FROM {$_TABLES['nexlistfields']} WHERE lid='{$listid}'");
            while ($A = DB_fetchArray($query)) {
                $sql =  "INSERT INTO {$_TABLES['nexlistfields']} (lid,fieldname,value_by_function) VALUES ";
                $sql .= "('{$newid}','{$A['fieldname']}','{$A['value_by_function']}')";
                DB_query($sql);
            }

            // Retrieve all the list items for this defintion
            $query = DB_query("SELECT * FROM {$_TABLES['nexlistitems']} WHERE lid='{$listid}'");
            while ($A = DB_fetchArray($query)) {
                DB_query("INSERT INTO {$_TABLES['nexlistitems']} (lid,value,active) VALUES ('{$newid}','{$A['value']}','{$A['active']}')");
            }

        }
        echo nexlistShowDefinitions($pluginmode, $catmode);
        break;

    case 'update_definition':
        $listid = COM_applyFilter($_POST['defid'],true);
        $name = COM_applyFilter($_POST['definition_name']);
        $plugin = COM_applyFilter($_POST['definition_plugin']);
        $category = COM_applyFilter($_POST['definition_category']);
        $viewperm = COM_applyFilter($_POST['definition_viewperm']);
        $editperm = COM_applyFilter($_POST['definition_editperm']);          
        $description = COM_applyFilter($_POST['definition_description']);
        $sql = "UPDATE {$_TABLES['nexlist']} SET name='$name' ,plugin='$plugin' ,category='$category',";
        $sql .= "description='$description', view_perms='$viewperm', edit_perms='$editperm' WHERE id='$listid'";
        DB_query($sql);
        echo nexlistShowDefinitions($pluginmode, $catmode);
        break;

    case 'delete_def':
        if (DB_count($_TABLES['nexlist'], 'id', $listid)) {
            // Check to see if any plugin is using this item
            $ret = nexlist_checkListDependencies($listid);
            if ($ret == '') {
                DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid='$listid'");
                DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid='$listid'");
                DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id='$listid'");
            } else {
                $GLOBALS['errmsg'] = "Warning Delete aborted:&nbsp;$ret";
            }
        }
        echo nexlistShowDefinitions($pluginmode, $catmode);
        break;

    default:
        echo nexlistShowDefinitions($pluginmode, $catmode);
}


echo COM_endblock();
echo COM_siteFooter();

?>