<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
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

require_once("../../../lib-common.php");                        // Path to your lib-common.php
require_once($_CONF['path'] . 'plugins/nexmenu/debug.php');      // Common Debug Code

$folder_icon = 'folder.gif';
$baseurl = $_CONF['site_url']  .'/nexmenu';
$imagesdir = $_CONF['layout_url'] . '/nexmenu/images/admin';

if (!SEC_hasRights('nexmenu.edit')) {
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
$myvars = array('id','op','mode','menulocation','inactive','showdisabled');
ppGetData($myvars,true);

if ($id < 1) {
    $id = 0;
    $idCurrent = 0;
} else {
    $idCurrent = $id;
}
if ($showdisabled != 1) {
    $showdisabled = 0;
}

if ($menulocation != 'header') {
    $menulocation = 'block';
}


function getMenuGroupAccessOptions($selected) {
    global $_TABLES,$LANG_NEXMENU01;

    $query = DB_query("SELECT grp_id,grp_name FROM {$_TABLES['groups']} ");
    while (list ($grp_id,$grp_name) = DB_fetchArray($query)) {
        if ($selected == $grp_id) {
            $options .= '<option value="'.$grp_id.'" SELECTED=selected>'.$grp_name.'</option>';
        } else {
            $options .= '<option value="'.$grp_id.'">'.$grp_name.'</option>';
        }
    }
    if ($selected ==0) {
        $options .= '<option value="0" SELECTED=selected>'.$LANG_NEXMENU01['ANONYMOUS'].'</option>';
    } else {
        $options .= '<option value="0">'.$LANG_NEXMENU01['ANONYMOUS'].'</option>';
    }
    return $options;
}

function getMenuGroupAccessOption($selected) {
    global $_TABLES,$LANG_NEXMENU01;

    if ($selected == 0) {
        return $LANG_NEXMENU01['ANONYMOUS'];
    } else {
        return DB_getItem($_TABLES['groups'],'grp_name',"grp_id=$selected");
    }
}

function updateFolderLocation($id,$menu_location) {
    global $_TABLES;

    $prevloc = ($menu_location == 'header') ? 'block' : 'header';
    /* Retrieve all Menu Items for this level */
    $query = DB_query("SELECT id,pid,menutype FROM {$_TABLES['nexmenu']} WHERE pid='$id' and location='$prevloc'");
    while ( list($id,$pid,$menutype) = DB_fetchARRAY($query)) {
        DB_query("UPDATE {$_TABLES['nexmenu']} SET location = '$menu_location' WHERE id='$id'");
        if ($menutype == 3)  {
            updateFolderLocation($id,$menu_location);
        }
    }
}

function getMenuFolderList($selected='',$menu_location='block',$pid='0',$level='1',$selectlist='') { 
    global $_TABLES,$showdisabled;

    /* Retrieve all enabled TOP Level Menu Items for this level */
    if ($selected == '' or $selected == 0) {
        $sql  = "SELECT id,pid, menutype, label FROM {$_TABLES['nexmenu']} ";
        $sql .= "WHERE  pid='$pid' AND menutype=3 AND location='{$menu_location}' ";
        if ($showdisabled == 0) {
            $sql .= "AND is_enabled=1 ";
        }
        $sql .= "ORDER BY menuorder";
        $query = DB_query($sql);       
    } else {
        $sql  = "SELECT id,pid, menutype, label FROM {$_TABLES['nexmenu']} ";
        $sql .= "WHERE pid='$pid' AND pid != '$selected' AND menutype=3 AND location='{$menu_location}' ";
        if ($showdisabled == 0) {
            $sql .= "AND is_enabled=1 ";
        }
        $sql .= "ORDER BY menuorder";
        $query = DB_query($sql);
    }        
    while ( list($id,$pid,$menutype,$label) = DB_fetchARRAY($query)) {
        if ($menutype == 3)  {
            $selectlist .= '<option value="' . $id;
            $indent='';
            if ($level > 1) {
                for ($i=2; $i<= $level; $i++) {
                    $indent .= "--";
                }
            }
            if ($id == $selected) {
                $selectlist .= '" Selected>' .$indent .$label . '</option>' . LB;
            } else {
                $selectlist .= '">' . $indent .$label . '</option>' . LB;
            }
            $selectlist = getMenuFolderList($selected,$menu_location,$id,$level+1,$selectlist);
        } else { 
            $indent = '';
            if ($level > 1) {
                for ($i=2; $i<= $level; $i++) {
                    $indent .= "--";
                }
            }
            $selectlist .= '<option value="' . $id;
            if ($id == $selected) {
                $selectlist .= '" Selected>' . $indent . $label . '</option>' . LB;
            } else {
                $selectlist .= '">' . $indent . $label . '</option>' . LB;
            }
        }
    }
    return $selectlist;
}

function getMenuFolderItems($pid,$list) { 
    global $_TABLES;
    /* Retrieve all enabled TOP Level Menu Items for this level */
    $query = DB_query("SELECT id,pid, menutype FROM {$_TABLES['nexmenu']} WHERE pid='$pid'ORDER BY pid");
    while ( list($id,$pid,$menutype) = DB_fetchARRAY($query)) {
        $list[] = $id;
        if ($menutype == 3)  {
            $list = getMenuFolderItems($id,$list);
        }
    }
    return $list;
}

function getMenuNextOrder($id,$direction) {
    global $_TABLES;

    $itemquery = DB_query("SELECT * FROM {$_TABLES['nexmenu']} WHERE id=$id");
    $retval = 0;
    if (DB_numRows($itemquery) == 1) {
        $A = DB_fetchArray($itemquery);
        if ($direction == 'movedn') {
            $sql = "SELECT menuorder FROM {$_TABLES['nexmenu']} WHERE location='{$A['location']}' ";
            $sql .= "AND pid={$A['pid']} AND menuorder > {$A['menuorder']} AND is_enabled=1 ORDER BY menuorder ASC LIMIT 1";
            $nextquery = DB_query($sql);
            list($nextorder) = DB_fetchArray($nextquery);
            if ($nextorder > $A['menuorder']) {
                $retval = $nextorder + 5;
            } else {
                $retval = $A['menuorder'];
            }
        } else {
            $sql = "SELECT menuorder FROM {$_TABLES['nexmenu']} WHERE location='{$A['location']}' ";
            $sql .= "AND pid={$A['pid']} AND menuorder < {$A['menuorder']} AND is_enabled=1 ORDER BY menuorder DESC LIMIT 1 ";
            $nextquery = DB_query($sql);
            list($nextorder) = DB_fetchArray($nextquery);
            $retval = $nextorder - 5;
            if ($retval < 0) $retval = 0;
        }
    }
    return $retval;
}




function recursive_node(&$node,$id) {
    global $_CONF,$_TABLES,$CONF_NEXMENU, $showdisabled,$idCurrent,$menulocation;
    $query = DB_QUERY("SELECT id,pid,label,url,menuorder, menutype,is_enabled FROM {$_TABLES['nexmenu']} WHERE PID='$id' ORDER BY menuorder");
    $menuOrd = 10;
    $stepNumber = 10;
    while ( list($id,$pid,$label,$url,$order,$menutype,$enabled) = DB_fetchARRAY($query)) {
        if ($idCurrent == $id) {
           $label = '<span class="treeMenuSelected">' .$label . '</span>';
        } elseif ($enabled == '0') {
           $label = '<span class="treeMenuDisabled">' .$label . '</span>';
        }
        /* Re-order any menuitems that may have just been moved */
        if ($order != $menuOrd) {
            DB_query("UPDATE {$_TABLES['nexmenu']} SET menuorder = '$menuOrd' WHERE id = '$id'");
        }
        $menuOrd += $stepNumber;

        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['nexmenu'], 'pid', $id) > 0) {
            if ($enabled == '1' OR ($enabled == 0 AND $showdisabled == '1' )) {
                $subnode[$id] = new HTML_TreeNode(array('text' => $label ,'link' => $_CONF['site_admin_url'] ."/plugins/nexmenu/index.php?op=display&id=$id&showdisabled=$showdisabled&menulocation=$menulocation" ,'icon' => $folder_icon));
                recursive_node($subnode[$id], $id);
                $node->addItem($subnode[$id]);
            }
        } else {
            if ($enabled == '1' OR ($enabled == 0 AND $showdisabled == '1' )) {
                $icon = $CONF_NEXMENU['icons'][$menutype];
                $node->addItem(new HTML_TreeNode(array('text' => $label, 'link' => $_CONF['site_admin_url'] ."/plugins/nexmenu/index.php?op=display&id=$id&showdisabled=$showdisabled&menulocation=$menulocation" , 'icon' => $icon)));
            }
        }
    }
}



function displayMenuRecords() {
    global $_CONF,$_TABLES,$CONF_NEXMENU,$LANG_NEXMENU01,$LANG_NEXMENU02,$menulocation;
    global $statusmsg,$imagesdir,$folder_icon,$showdisabled,$idCurrent,$LANG_NEXMENU04;

    include ($_CONF['path_system'] . 'classes/navbar.class.php');
    require_once($_CONF['path_system'] . 'nexpro/classes/TreeMenu.php');
    $menu  = new HTML_TreeMenu();
    $mquery = DB_query("SELECT id,pid,label,menuorder, menutype, is_enabled from {$_TABLES['nexmenu']} WHERE pid='0' AND location='{$menulocation}' ORDER BY menuorder");
    $menuOrd = 10;
    $stepNumber = 10;
    while ( list($id,$pid,$label,$order,$menutype,$enabled) = DB_fetchARRAY($mquery)) {
        /* Re-order any Offer or Stories that may have just been moved */
        if ($order != $menuOrd) {
            DB_query("UPDATE {$_TABLES['nexmenu']} SET menuorder = '$menuOrd' WHERE id = '$id'");
        }
        $menuOrd += $stepNumber;

       if ($enabled == '1' OR ($showdisabled == '1' and $enabled == '0')) {
           $icon = $CONF_NEXMENU['icons'][$menutype];
           if ($idCurrent == $id) {
               $label = '<span class="treeMenuSelected">' .$label . '</span>';
            } elseif ($enabled == '0') {
               $label = '<span class="treeMenuDisabled">' .$label . '</span>';
            }
           $node[$id] = new HTML_TreeNode(array('text' => $label ,'link' => $_CONF['site_admin_url'] ."/plugins/nexmenu/index.php?op=display&id=$id&showdisabled=$showdisabled&menulocation=$menulocation" ,'icon' => $icon));
           recursive_node($node[$id], $id);
           $menu->addItem($node[$id]);
       }
    }
    $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $imagesdir ,'defaultClass' => 'treeMenuDefault','usePersistance' => true));

    $mainview = new Template($_CONF['path_layout'] . 'nexmenu/admin');
    $mainview->set_file (array ('mainview'=>'mainview.thtml'));
    $mainview->set_var('site_url',$_CONF['site_url']);
    if ($statusmsg != '') {
        $mainview->set_var ('showalert', '');
    } else {
        $mainview->set_var ('showalert', 'none');
    }
    $mainview->set_var ('statusmsg', $statusmsg);
    $mainview->set_var ('imgset',$_CONF['layout_url'] . '/nexmenu/images/admin');
    if ($showdisabled == '1') {
        $mainview->set_var ('chk_enabled', 'CHECKED');
    } else {
        $mainview->set_var ('chk_disabled', 'CHECKED');
    }
    if ($statusmsg != '') {
        $mainview->set_var ('statusmsg', $statusmsg);
    }
    if ($CONF_NEXMENU['load_HTMLTree']) {
        $javascript = "<script language=JavaScript src=\"{$_CONF['site_url']}/nexmenu/javascript/TreeMenu.js\" type=\"text/javascript\"></script>";
        $mainview->set_var('include_javascript',$javascript);
    }

    $scripturl = $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php';
    $navbar = new navbar;
    $navbar->add_menuitem($LANG_NEXMENU04['1'],$scripturl . "?menulocation=header&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['2'],$scripturl . "?menulocation=block&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['3'],$scripturl . "?op=addaction&id={$idCurrent}&menulocation={$menulocation}&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['6'],$scripturl . "?op=config&menulocation=block&showdisabled={$showdisabled}");

    if ($menulocation == 'block') {
        $navbar->set_selected($LANG_NEXMENU04['2']);
        $mainview->set_var ('navbar', $navbar->generate() );
        $mainview->set_var ('location','block');
    } else {
        $navbar->set_selected($LANG_NEXMENU04['1']);
        $mainview->set_var ('navbar', $navbar->generate() );
        $mainview->set_var ('location','header');
    }
    $mainview->set_var ('LANG_OPTIONS', $LANG_NEXMENU01['LANG_OPTIONS']);
    $mainview->set_var ('LANG_ADD', $LANG_NEXMENU01['LANG_ADD']);
    $mainview->set_var ('LANG_HEADING1', $LANG_NEXMENU01['LANG_HEADING1']);
    $mainview->set_var ('treemenu', $treeMenu->toHTML());


    $filteroptions = '<option value=\'block\' ';
    $filteroptions .= ($menulocation == 'block') ? 'SELECTED' : '';
    $filteroptions .= '>Block Menu Items</option><option value=\'header\' ';
    $filteroptions .= ($menulocation == 'header') ? 'SELECTED' : '';
    $filteroptions .= '>Header Menu Items</option>';
    $mainview->set_var ('filteroptions', $filteroptions);


    if ($idCurrent == 0 or DB_getItem($_TABLES['nexmenu'],"location", "id='$idCurrent'") != $menulocation)  {
        $mainview->set_var ('showdiv', 'none');
        $mainview->set_var ('LANG_edithelp', $LANG_NEXMENU01['LANG_EditHelp']);
    } else {
        $mainview->set_var ('show_itemhelp', 'none');
        $mainview->set_var ('LANG_HEADING2', $LANG_NEXMENU01['LANG_HEADING2']);
        $query = DB_query("SELECT pid, menutype, menuorder, label, url, grp_access, is_enabled FROM {$_TABLES['nexmenu']} WHERE id=$idCurrent");
        list ($pid,$menutype,$order,$label,$url,$grp_id, $is_enabled) = DB_fetchArray($query);
        $chk1 = ($is_enabled == 1) ? ' checked' : '';

        if ($pid == 0) {
            $parent = $LANG_NEXMENU01['LANG_TopLevelMenu'];
        } else {
            $parent = DB_getItem($_TABLES['nexmenu'],"label", "id={$pid}");
        }
        if (trim($url) == '') {
            $url = 'Not Defined';
        }
        $mainview->set_var ('showurl', ($menutype == 3) ? 'none': '');
        $mainview->set_var ('id', $idCurrent);
        $mainview->set_var ('location', $menulocation);
        $mainview->set_var ('label', stripslashes($label));
        $mainview->set_var ('parent', $parent);
        $mainview->set_var ('url', substr($url,0,40));
        $mainview->set_var ('full_url', $url);
        $mainview->set_var ('order', $order);
        $mainview->set_var ('menutype', $LANG_NEXMENU02[$menutype]);
        $mainview->set_var ('chk1', $chk1);
        $mainview->set_var ('chk2', $chk2);
        $mainview->set_var ('chk3', $chk3);
        $mainview->set_var ('showdisabled', $showdisabled);
        $mainview->set_var ('LANG_MenuItemAdmin', $LANG_NEXMENU01['LANG_MenuItemAdmin']);
        $mainview->set_var ('LANG_ParentMenu', $LANG_NEXMENU01['LANG_ParentMenu']);
        $mainview->set_var ('LANG_ORDER', $LANG_NEXMENU01['LANG_ORDER']);
        $mainview->set_var ('LANG_Enabled', $LANG_NEXMENU01['LANG_Enabled']);
        $mainview->set_var ('LANG_Submenu', $LANG_NEXMENU01['LANG_Submenu']);
        $mainview->set_var ('LANG_URLITEM', $LANG_NEXMENU01['LANG_URLITEM']);
        $mainview->set_var ('LANG_ACCESS', $LANG_NEXMENU01['LANG_ACCESS']);
        $mainview->set_var ('grp_access', getMenuGroupAccessOption($grp_id));
        $mainview->set_var ('LANG_EditRecord', $LANG_NEXMENU01['LANG_EditRecord']);
        $mainview->set_var ('LANG_DeleteRecord', $LANG_NEXMENU01['LANG_DeleteRecord']);
        $mainview->set_var ('LANG_MoveUp', $LANG_NEXMENU01['LANG_MoveUp']);
        $mainview->set_var ('LANG_MoveDn', $LANG_NEXMENU01['LANG_MoveDn']);
        $mainview->set_var ('LANG_DELCONFIRM', $LANG_NEXMENU01['LANG_DELCONFIRM']);
        $mainview->set_var ('chk1', $chk1);
    }
    $mainview->parse ('output', 'mainview');
    return $mainview->finish ($mainview->get_var('output'));
}

function editMenuRecord($mode) {
    global $_CONF,$_TABLES,$LANG_NEXMENU01,$LANG_NEXMENU02,$LANG_NEXMENU03,$id,$inactive;
    global $menulocation,$CONF_NEXMENU,$LANG_NEXMENU04,$showdisabled,$idCurrent;

    include ($_CONF['path_system'] . 'classes/navbar.class.php');

    $editmenu = new Template($_CONF['path_layout'] . 'nexmenu/admin');
    $editmenu->set_file ('editmenu' , 'editmenu.thtml');
    $editmenu->set_file ('language_item', 'languageitem.thtml');

    $scripturl = $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php';
    $navbar = new navbar;
    $navbar->add_menuitem($LANG_NEXMENU04['1'],$scripturl . "?menulocation=header&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['2'],$scripturl . "?menulocation=block&showdisabled={$showdisabled}");
    if ($mode == 'add') {
        $navbar->add_menuitem($LANG_NEXMENU04['3'],$scripturl . "?op=addaction&id={$idCurrent}&menulocation={$menulocation}&showdisabled={$showdisabled}");
    } else {
        $navbar->add_menuitem($LANG_NEXMENU04['5'],$scripturl . "?op=editaction&id={$id}&menulocation={$menulocation}&showdisabled={$showdisabled}");
    }
    $navbar->add_menuitem($LANG_NEXMENU04['6'],$scripturl . "?op=config&menulocation=block&showdisabled={$showdisabled}");

    $chk1 = '';
    $chk2 = '';
    if ($mode == 'edit') {
        $sql = "SELECT pid, menutype, location, menuorder, label, url, grp_access, image, is_enabled FROM {$_TABLES['nexmenu']} WHERE id='{$id}'";
        $query = DB_query($sql);
        list ($parent, $menutype, $menu_location, $order,$label,$url,$grp_id,$image,$is_enabled) = DB_fetchArray($query);

        $helpmsg = $LANG_NEXMENU01['LANG_HELPMSG2'];
        $submit =  $LANG_NEXMENU01['LANG_UPDATE'];
        if ($menu_location == 'header') {
            $chk_block = '';
            $chk_header = 'CHECKED';
        } else {
            $chk_block = 'CHECKED';
            $chk_header = '';
        }
        $chk1 = ($is_enabled == 1) ? ' checked' : '';
        if ($menutype == 1 OR $menutype == 2) {
            $show1 = 'show';
            $show2 = 'none';
            $show3 = 'none';
        } elseif ($menutype == 4) {
            $show1 = 'none';
            $show2 = 'show';
            $show3 = 'none';
        } elseif ($menutype == 5) {
            $show1 = 'none';
            $show2 = 'none';
            $show3 = 'show';
        } else {
            $show1 = 'none';
            $show2 = 'none';
            $show3 = 'none';
        }
        $navbar->set_selected($LANG_NEXMENU04['5']);
        $editmenu->set_var ('navbar', $navbar->generate() );
        $editmenu->set_var('group_select', getMenuGroupAccessOptions($grp_id));
    } else {
        $actionName = '';
        $parent = 0;
        $order = 0;
        $label = '';
        $url = '';
        $option = 1;
        $chk1 = 'checked';
        $show1 = 'show';
        $show2 = 'none';
        $show3 = 'none';
        $helpmsg = $LANG_NEXMENU01['LANG_HELPMSG1'];
        $submit =  $LANG_NEXMENU01['LANG_ADD'];
        if ($menulocation == 'header') {
            $chk_block = '';
            $chk_header = 'CHECKED';
        } else {
            $chk_block = 'CHECKED';
            $chk_header = '';
        }
        if (isset($id) and $id > 0) {
            $menutype = DB_getItem($_TABLES['nexmenu'],'menutype', "id='$id'");
        }
        $navbar->set_selected($LANG_NEXMENU04['3']);
        $editmenu->set_var ('navbar', $navbar->generate() );
        $editmenu->set_var('group_select', getMenuGroupAccessOptions(2));
    }

    foreach ($LANG_NEXMENU02 as $key =>$value) {
        if ($key == $menutype) {
           $optiontypes .= "<option value='$key' selected>$value</option>";
        } else {
            $optiontypes .= "<option value='$key'>$value</option>";
        }
    }

    foreach ($LANG_NEXMENU03 as $key =>$value) {
        if ($url == $CONF_NEXMENU['coremenu'][$key]) {
            $coremenutypes .= "<option value='$key' selected>$value</option>";
        } else {
            $coremenutypes .= "<option value='$key'>$value</option>";
        }
    }

    foreach ($CONF_NEXMENU['languages'] as $key => $language) {
        $editmenu->set_var('language_option',$language);
        $editmenu->set_var('language_id',$key);
        if ($mode == 'edit') {
            $curlabel = DB_getItem($_TABLES['nexmenu_language'],'label',"menuitem=$id AND language=$key");
            $editmenu->set_var('language_label',$curlabel);
        } else {
            $editmenu->set_var('language_label','');
        }
        $editmenu->parse('language_options','language_item',true);
    }

    /* Generate Menu Folder indented list */
    $selparent = '<select name="menu_parent">';
    $selparent .= "<option value=\"0\" selected>Top Level Menu</option>";

    // Check and see if this category has any sub categories - where a category record has this cid as it's parent
    if ($mode == 'add' AND $menutype == '3') {
        $selparent .= getMenuFolderList($id,$menulocation) . '</select>';
    } else {
        $selparent .= getMenuFolderList($parent,$menulocation) . '</select>';
    }

    $editmenu->set_var ('phpself', $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php');
    $editmenu->set_var ('helpmsg', $helpmsg);
    $editmenu->set_var ('mode', $mode);
    $editmenu->set_var ('id', $id);
    $editmenu->set_var ('location', $menulocation);
    $editmenu->set_var ('optiontypes', $optiontypes);
    $editmenu->set_var ('coremenutypes', $coremenutypes);
    $editmenu->set_var ('showurl', ($menutype == 3) ? 'none': '');
    $editmenu->set_var ('LANG_Label', $LANG_NEXMENU01['LANG_Label']);
    $editmenu->set_var ('LANG_MenuItem', $LANG_NEXMENU01['LANG_MenuItem']);
    $editmenu->set_var ('LANG_ParentMenu', $LANG_NEXMENU01['LANG_ParentMenu']);
    $editmenu->set_var ('LANG_ORDER', $LANG_NEXMENU01['LANG_ORDER']);
    $editmenu->set_var ('LANG_Enabled', $LANG_NEXMENU01['LANG_Enabled']);
    $editmenu->set_var ('LANG_Submenu', $LANG_NEXMENU01['LANG_Submenu']);
    $editmenu->set_var ('LANG_URLITEM', $LANG_NEXMENU01['LANG_URLITEM']);
    $editmenu->set_var ('LANG_AlternateLabel', $LANG_NEXMENU01['LANG_Alternatelabel']);
    $editmenu->set_var ('LANG_LANGUAGES', $LANG_NEXMENU01['LANG_Languages']);
    $editmenu->set_var ('LANG_ImageHelp', $LANG_NEXMENU01['LANG_ImageHelp']);
    $editmenu->set_var ('LANG_IMAGE', $LANG_NEXMENU01['LANG_IMAGE']);
    $editmenu->set_var ('label', stripslashes($label));
    $editmenu->set_var ('menu_image',$image);
    $editmenu->set_var ('showdisabled', $showdisabled);
    $editmenu->set_var ('sel_parent', $selparent);
    $editmenu->set_var ('url', $url);
    $editmenu->set_var ('order', $order);
    $editmenu->set_var ('phpfunction', $url);
    $editmenu->set_var ('chk_block', $chk_block);
    $editmenu->set_var ('chk_header', $chk_header);
    $editmenu->set_var ('chk1', $chk1);
    $editmenu->set_var ('show1', $show1);
    $editmenu->set_var ('show2', $show2);
    $editmenu->set_var ('show3', $show3);
    $editmenu->set_var ('LANG_CANCEL', $LANG_NEXMENU01['LANG_CANCEL']);
    $editmenu->set_var ('cancel_url', $_CONF['site_admin_url'] .'/plugins/nexmenu/index.php?op=display&id='.$id.'&showdisabled='.$inactive.'&menulocation='.$menulocation);
    $editmenu->set_var ('LANG_SUBMIT', $submit);
    $editmenu->parse ('output', 'editmenu');
    return $editmenu->finish ($editmenu->get_var('output'));
}

function updateMenuRecord($mode) {
    global $_CONF, $CONF_NEXMENU, $_TABLES, $id, $idCurrent;

    $parent = ppPrepareForDB($_POST['menu_parent']);
    $order  = ppPrepareForDB($_POST['menu_order']);
    $label  = addslashes(ppPrepareForDB(htmlspecialchars($_POST['menu_label'],ENT_QUOTES,$CONF_NEXMENU['charset'])));
    $image  = ppPrepareForDB($_POST['menu_image']);
    $menutype = ppPrepareForDB($_POST['menutype']);
    $menu_location = ppPrepareForDB($_POST['menu_location']);
    $coremenutype = ppPrepareForDB($_POST['coremenutype']);
    $phpfunction = ppPrepareForDB($_POST['phpfunction']);

    $grp_access   = ppPrepareForDB($_POST['grp_access']);
    $is_enabled = (isset($_POST['menu_status'])) ? 1 : 0;
    if ($label == '') {
        $GLOBALS['statusmsg'] = 'Error adding or updating Record. Label can not be blank';
        return;
    }

    switch ($menutype) {
        case 1 :
            $url    = $_POST['menu_url'];
            break;
        case 2:
            $url    = $_POST['menu_url'];
            break;
        case 3:
            $url = '';
            break;
        case 4:
            $url = $CONF_NEXMENU['coremenu'][$coremenutype];
            break;
        case 5:
            $url = $phpfunction;
            break;
    }

    if ($mode == 'add') {
        if ($order < 1) {
            $query = DB_query("SELECT MAX(menuorder) FROM {$_TABLES['nexmenu']} WHERE pid={$parent}");
            list ($order) = DB_fetchArray($query);
            $order++;
        }
        $sql = "INSERT INTO {$_TABLES['nexmenu']} (pid,menutype,location,menuorder,label,url,grp_access,image,is_enabled) ";
        $sql .= "VALUES ('$parent','$menutype','$menu_location','$order','$label','$url','$grp_access','$image','$is_enabled')";
        DB_query($sql);
        $GLOBALS['id'] = DB_insertID();
        $GLOBALS['statusmsg'] = 'Record Added';
        $idCurrent = DB_insertID();     // Make the new record the current record
        foreach ($_POST['alternatelabel'] as $langid => $languagelabel) {
            if (trim($languagelabel) != '') {
                if (DB_count($_TABLES['nexmenu_language'],array('menuitem','language'), array($id,$langid))) {
                    DB_query("UPDATE {$_TABLES['nexmenu_language']} SET label = '$languagelabel' WHERE menuitem=$idCurrent AND language=$langid ");
                } else {
                    DB_query("INSERT INTO {$_TABLES['nexmenu_language']} (menuitem,language,label) VALUES ($idCurrent,$langid,'{$languagelabel}')");
                }
            }
        }
    } elseif (DB_count($_TABLES['nexmenu'],"id",$id) == 1) {
        if ($order < 1) {
            $query = DB_query("SELECT MAX(menuorder) FROM {$_TABLES['nexmenu']} WHERE pid={$parent}");
            list ($order) = DB_fetchArray($query);
            $order++;
        }

        /* Check if this is a menu and the location has changed (header or block location of menu */
        $curLocation = DB_getItem($_TABLES['nexmenu'],"location", "id='$id'");
        if ( $menutype == 3 AND ($menu_location != '$curlocation')) {
            /* update any menuitems or submenus as well - need to move them all */
            updateFolderLocation($id,$menu_location);
        }
        $sql = "UPDATE {$_TABLES['nexmenu']} SET pid='{$parent}',menutype='{$menutype}',location='{$menu_location}', image='{$image}', ";
        $sql .= "menuorder='{$order}',label='{$label}', url='{$url}',grp_access='{$grp_access}',is_enabled='{$is_enabled}' WHERE id='$id'";
        DB_query($sql);
        foreach ($_POST['alternatelabel'] as $langid => $languagelabel) {
            if (trim($languagelabel) != '') {
                if (DB_count($_TABLES['nexmenu_language'],array('menuitem','language'), array($id,$langid))) {
                    DB_query("UPDATE {$_TABLES['nexmenu_language']} SET label = '$languagelabel' WHERE menuitem=$id AND language=$langid ");
                } else {
                    DB_query("INSERT INTO {$_TABLES['nexmenu_language']} (menuitem,language,label) VALUES ($id,$langid,'{$languagelabel}')");
                }
            }
        }
        $GLOBALS['statusmsg'] = 'Record Updated';
    } else {
        COM_errorLOG("nexmenu Plugin: Admin Error updating Record");
        $GLOBALS['statusmsg'] = 'Error adding or updating Record';
    }

}

function deleteMenuRecord() {
    global $_CONF, $_TABLES, $id;

    /* Need to check if any linked records have used this ID. If so don't delete record, just set status inactive */
    if (DB_count($_TABLES['nexmenu'],"id",$id) == 1) {
        /* Check and see if this item is already disabled - if so delete it and any subitems */
        $query1 = DB_query("SELECT * FROM {$_TABLES['nexmenu']} WHERE pid='$id'");
        if (DB_numRows($query1) == 0 ) {
            DB_query("DELETE FROM {$_TABLES['nexmenu']} WHERE id='$id'");
            $GLOBALS['statusmsg'] = 'Record Deleted - no references found';
        } else {
            if (DB_getItem($_TABLES['nexmenu'], "is_enabled", "id='{$id}'") == 0) {
                $GLOBALS['statusmsg'] = 'Menu and all subitems were deleted';
                $deletelist = array($id);
                $deletelist = getMenuFolderItems($id,$deletelist);
                foreach($deletelist as $element=>$item) {
                    DB_query("DELETE FROM {$_TABLES['nexmenu']} WHERE id='$item'");
                }
            } else {
                DB_query("UPDATE {$_TABLES['nexmenu']} set is_enabled='0' WHERE id='$id'");
                $GLOBALS['statusmsg'] = 'Record disabled - Submenu items found on file';
            }
        }
    }
}

function menuConfig() {
    global $_CONF,$_TABLES,$LANG_NEXMENU01,$id,$inactive,$showdisabled;
    global $menulocation,$CONF_NEXMENU,$LANG_NEXMENU04,$LANG_NEXMENU05;

    $statusmsg = $_GET['statusmsg'];     
    
    include ($_CONF['path_system'] . 'classes/navbar.class.php');
    $tpl = new Template($_CONF['path_layout'] . 'nexmenu/admin');
    $tpl->set_file ('page' , 'menuconfig.thtml');
    $tpl->set_var('site_url',$_CONF['site_url'] );

    $query = DB_query("SELECT * FROM {$_TABLES['nexmenu_config']}");
    $A = DB_fetchArray($query);

    $scripturl = $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php';
    $navbar = new navbar;
    $navbar->add_menuitem($LANG_NEXMENU04['1'],$scripturl . "?menulocation=header&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['2'],$scripturl . "?menulocation=block&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['3'],$scripturl . "?op=addaction&id={$idCurrent}&menulocation={$menulocation}&showdisabled={$showdisabled}");
    $navbar->add_menuitem($LANG_NEXMENU04['6'],$scripturl . "?op=config&menulocation=block&showdisabled={$showdisabled}");

    $navbar->set_selected($LANG_NEXMENU04['6']);
    $tpl->set_var ('navbar', $navbar->generate() );
    if ($statusmsg != '') {
        $tpl->set_var ('showalert', '');
    } else {
        $tpl->set_var ('showalert', 'none');
    }
    $tpl->set_var ('statusmsg', $statusmsg);        

    $tpl->set_var('LANG_title',$LANG_NEXMENU05[6]);
    foreach ($CONF_NEXMENU['milonicmodes'] as $option) {
        if ($A['mode'] == $option) {
            $menuoptions .= '<option value="'.$option.'" SELECTED=selected>'.$option.'</option>';
        } else {
            $menuoptions .= '<option value="'.$option.'">'.$option.'</option>';
        }
    }
    $tpl->set_var('mode_options',$menuoptions);
    $tpl->set_var('headerbg',$A['headerbg']);
    $tpl->set_var('headerfg',$A['headerfg']);
    $tpl->set_var('blockbg',$A['blockbg']);
    $tpl->set_var('blockfg',$A['blockfg']);
    $tpl->set_var('onhover_headerbg',$A['onhover_headerbg']);
    $tpl->set_var('onhover_headerfg',$A['onhover_headerfg']);
    $tpl->set_var('onhover_blockbg',$A['onhover_blockbg']);
    $tpl->set_var('onhover_blockfg',$A['onhover_blockfg']);
    $tpl->set_var('header_properties',$A['headermenu_properties']);
    $tpl->set_var('block_properties',$A['blockmenu_properties']);

    $tpl->set_var('LANG_menumode',$LANG_NEXMENU05[0]);
    $tpl->set_var('LANG_langlabels',$LANG_NEXMENU05[1]);
    $tpl->set_var('LANG_newwindow',$LANG_NEXMENU05[2]);
    $tpl->set_var('LANG_reference',$LANG_NEXMENU05[3]);
    $tpl->set_var('LANG_enabled',$LANG_NEXMENU05[4]);
    $tpl->set_var('LANG_disabled',$LANG_NEXMENU05[5]);
    $tpl->set_var('LANG_miloniclabel1',$LANG_NEXMENU05[7]);
    $tpl->set_var('LANG_miloniclabel2',$LANG_NEXMENU05[8]);
    $tpl->set_var('LANG_miloniclabel3',$LANG_NEXMENU05[9]);
    $tpl->set_var('LANG_miloniclabel4',$LANG_NEXMENU05[10]);
    $tpl->set_var('LANG_miloniclabel5',$LANG_NEXMENU05[12]);
    $tpl->set_var('LANG_miloniclabel6',$LANG_NEXMENU05[13]);
    $tpl->set_var('LANG_miloniclabel7',$LANG_NEXMENU05[14]);
    $tpl->set_var('LANG_milonichelp1',$LANG_NEXMENU05[11]);
    $tpl->set_var('LANG_csslabel1',$LANG_NEXMENU05[15]);
    $tpl->set_var('LANG_csslabel2',$LANG_NEXMENU05[16]);
    $tpl->set_var('LANG_csslabel3',$LANG_NEXMENU05[17]);
    $tpl->set_var('LANG_csslabel4',$LANG_NEXMENU05[18]);
    $tpl->set_var('LANG_csslabel5',$LANG_NEXMENU05[19]);
    $tpl->set_var('LANG_csslabel6',$LANG_NEXMENU05[20]);
    $tpl->set_var('LANG_csslabel7',$LANG_NEXMENU05[21]);
    $tpl->set_var('LANG_csslabel8',$LANG_NEXMENU05[22]);
    $tpl->set_var('LANG_csslabel9',$LANG_NEXMENU05[23]);
    $tpl->set_var('LANG_yes',$LANG_NEXMENU05[24]);
    $tpl->set_var('LANG_no',$LANG_NEXMENU05[25]);

    if ($A['multilanguage'] == 1) {
        $tpl->set_var('chk_langon',"CHECKED=checked");
    } else {
        $tpl->set_var('chk_langoff',"CHECKED=checked");
    }

    if ($A['targetfeatures'] == '') {
        $tpl->set_var('targetfeatures','width=800,height=600,left=50,top=50,scrollbars=yes;');
    } else {
        $A['targetfeatures'] = str_replace('targetfeatures=','',$A['targetfeatures']);
        $tpl->set_var('targetfeatures',$A['targetfeatures']);
    }

    if ($_GET['writecss'] == 1) {
        $tpl->set_var('chk_writecss_yes','CHECKED=checked');
    } else {
        $tpl->set_var('chk_writecss_no','CHECKED=checked');
    }

    $menustyles = '';
    foreach ($CONF_NEXMENU['milonicstyles'] as $menustyle) {
        if ($A['blockmenu_style'] == $menustyle) {
            $menustyles .= '<option value="'.$menustyle.'" SELECTED=selected>'.$menustyle.'</option>';
        } else {
            $menustyles .= '<option value="'.$menustyle.'">'.$menustyle.'</option>';
        }
    }
    $tpl->set_var('style1_options',$menustyles);
    if ($A['mode'] == 'Milonic') {
        $tpl->set_var('show_milonicstyles','');
        $tpl->set_var('show_cssmenucolors','none');
    } else {
        $tpl->set_var('show_milonicstyles','none');
        $tpl->set_var('show_cssmenucolors','');
    }

    $menustyles = '';
    foreach ($CONF_NEXMENU['milonicstyles'] as $menustyle) {
        if ($A['blocksubmenu_style'] == $menustyle) {
            $menustyles .= '<option value="'.$menustyle.'" SELECTED=selected>'.$menustyle.'</option>';
        } else {
            $menustyles .= '<option value="'.$menustyle.'">'.$menustyle.'</option>';
        }
    }
    $tpl->set_var('style2_options',$menustyles);
        $menustyles = '';
    foreach ($CONF_NEXMENU['milonicstyles'] as $menustyle) {
        if ($A['headermenu_style'] == $menustyle) {
            $menustyles .= '<option value="'.$menustyle.'" SELECTED=selected>'.$menustyle.'</option>';
        } else {
            $menustyles .= '<option value="'.$menustyle.'">'.$menustyle.'</option>';
        }
    }
    $tpl->set_var('style3_options',$menustyles);
    $menustyles = '';
    foreach ($CONF_NEXMENU['milonicstyles'] as $menustyle) {
        if ($A['headersubmenu_style'] == $menustyle) {
            $menustyles .= '<option value="'.$menustyle.'" SELECTED=selected>'.$menustyle.'</option>';
        } else {
            $menustyles .= '<option value="'.$menustyle.'">'.$menustyle.'</option>';
        }
    }
    $tpl->set_var('style4_options',$menustyles);


    $tpl->set_var ('LANG_CANCEL', $LANG_NEXMENU01['LANG_CANCEL']);
    $tpl->set_var ('cancel_url', $_CONF['site_admin_url'] .'/plugins/nexmenu/index.php?op=display&id='.$id.'&showdisabled='.$inactive.'&menulocation='.$menulocation);
    $tpl->set_var ('LANG_SUBMIT', $LANG_NEXMENU01['LANG_UPDATE']);
    $tpl->parse ('output', 'page');
    return $tpl->finish ($tpl->get_var('output'));

}


function menuSaveConfig() {
    global $_TABLES;

    $mode = ppPrepareForDB($_POST['mode']);
    $style1 = ppPrepareForDB($_POST['style1']);
    $style2 = ppPrepareForDB($_POST['style2']);
    $style3 = ppPrepareForDB($_POST['style3']);
    $style4 = ppPrepareForDB($_POST['style4']);
    $headerbg = ppPrepareForDB($_POST['headerbg']);
    $headerfg = ppPrepareForDB($_POST['headerfg']);
    $blockbg = ppPrepareForDB($_POST['blockbg']);
    $blockfg = ppPrepareForDB($_POST['blockfg']);
    $onhover_headerbg = ppPrepareForDB($_POST['onhover_headerbg']);
    $onhover_headerfg = ppPrepareForDB($_POST['onhover_headerfg']);
    $onhover_blockbg = ppPrepareForDB($_POST['onhover_blockbg']);
    $onhover_blockfg = ppPrepareForDB($_POST['onhover_blockfg']);
    $header_properties = ppPrepareForDB($_POST['header_properties']);
    $block_properties = ppPrepareForDB($_POST['block_properties']);

    $targetfeatures = ppPrepareForDB($_POST['targetfeatures']);
    $multilang = COM_applyFilter($_POST['multilang'],true);
    $targetfeatures = 'targetfeatures='. $targetfeatures;

    if (DB_count($_TABLES['nexmenu_config']) == 0) {
        DB_query("INSERT INTO {$_TABLES['nexmenu_config']} (mode) VALUES ('CSS')");
    }
    $sql = " UPDATE {$_TABLES['nexmenu_config']} SET mode='$mode', multilanguage=$multilang, targetfeatures='$targetfeatures', ";
    $sql .= "blockmenu_style='$style1', blocksubmenu_style='$style2',headermenu_style='$style3', headersubmenu_style='$style4', ";
    $sql .= "headerbg='$headerbg', headerfg='$headerfg', blockbg='$blockbg',blockfg='$blockfg', ";
    $sql .= "onhover_headerbg='$onhover_headerbg', onhover_headerfg='$onhover_headerfg', onhover_blockbg='$onhover_blockbg',onhover_blockfg='$onhover_blockfg', ";
    $sql .= "headermenu_properties='$header_properties',blockmenu_properties='$block_properties' ";
    DB_query($sql);

    $err = '';
    // Re-Write the menu css stylesheet
    if ($_POST['writecss'] == 1) {
        $err = menu_updateStyleSheet();
    }
    
    if ($mode == 'Milonic') {
        $err = menu_updateBlockHeader();
    }
    return $err;

}

/* Update the css file that is used when the menu is in CSS mode */ 
function menu_updateStyleSheet() {
    global $_CONF,$_TABLES;

    $query = DB_query("SELECT * FROM {$_TABLES['nexmenu_config']}");
    $A = DB_fetchArray($query);

    $tpl = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
    $tpl->set_file ('cssfile' , 'menuconfig_css.thtml');
    $tpl->set_var('header_bgcolor',$A['headerbg']);
    $tpl->set_var('header_fgcolor',$A['headerfg']);
    $tpl->set_var('block_bgcolor',$A['blockbg']);
    $tpl->set_var('block_fgcolor',$A['blockfg']);
    $tpl->set_var('onhover_header_bgcolor',$A['onhover_headerbg']);
    $tpl->set_var('onhover_header_fgcolor',$A['onhover_headerfg']);
    $tpl->set_var('onhover_block_bgcolor',$A['onhover_blockbg']);
    $tpl->set_var('onhover_block_fgcolor',$A['onhover_blockfg']);

    $tpl->parse ('output', 'cssfile');

    $stylesheet = @fopen($_CONF['path_layout'] . 'nexmenu/cssmenu/menu.css', "w");
    if ($stylesheet === FALSE ) {
        COM_errorLog('nexmenu not able to open menu.css file for writting');
        return ('Error not able to open menu.css file for writting');     
    } else {      
        if (@fwrite($stylesheet, $tpl->finish ($tpl->get_var('output'))) === FALSE) {
            COM_errorLog('nexmenu not able to write to the file menu.css');
            return ('Error not able to write to the menu.css file');            
        }
        @fclose($stylesheet);
    }

}


/* Update the template used for the standalone blocks that use the milonic menu - like the forum menu */
function menu_updateBlockHeader() {
    global $_CONF,$_TABLES;

    $query = DB_query("SELECT blockmenu_style,blockmenu_properties FROM {$_TABLES['nexmenu_config']}");
    $A = DB_fetchArray($query);
    $tpl = new Template($_CONF['path_layout'] . 'nexmenu/milonicmenu');
    $tpl->set_file ('blockheader' , 'blockheader_config.thtml');
    $tpl->set_var('blockheader_menustyle',$A['blockmenu_style']);
    $tpl->set_var('menu_properties',$A['blockmenu_properties']);     
    $tpl->parse ('output', 'blockheader');

    $block = @fopen($_CONF['path_layout'] . 'nexmenu/milonicmenu/blockheader-blockmenu.thtml', "w");
    if ($block === FALSE ) {
        COM_errorLog('nexmenu not able to open template file: nexmenu/milonicmenu/blockheader-blockmenu.thtml');
        return ('Error not able to open nexmenu/milonicmenu/blockheader-blockmenu.thtml file for writting');         
    } else {                     
        if (@fwrite($block, $tpl->finish ($tpl->get_var('output'))) === FALSE) {
            COM_errorLog('nexmenu not able to write to template file nexmenu/milonicmenu/blockheader-blockmenu.thtml');
            return ('Error not able to open nexmenu/milonicmenu/blockheader-blockmenu.thtml file for writting');             
        }
        @fclose($block);
    }

}


/* MAIN CODE */

switch ($op) {

    case 'display' :
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();
        break;

    case 'addaction' :
        echo COM_siteHeader();
        echo COM_startBlock();
        echo editMenuRecord('add');
        break;

    case 'editaction' :
        echo COM_siteHeader();
        echo COM_startBlock();
        echo editMenuRecord('edit');
        break;

    case 'saveaction' :
        updateMenuRecord($mode);
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();
        break;

    case 'delaction' :
        deleteMenuRecord();
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();
        break;

    case 'setEnabled' :
        $is_enabled = (isset($_POST['menu_status'])) ? 1 : 0;
        DB_query("UPDATE {$_TABLES['nexmenu']} SET is_enabled = '$is_enabled' WHERE id = '$id'");
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();;
        break;

    case 'moveup' :
        $nextMenuOrder = getMenuNextOrder($id,'moveup');
        DB_query("UPDATE {$_TABLES['nexmenu']} SET menuorder = $nextMenuOrder WHERE id = '$id'");
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();;
        break;

    case 'movedn' :
        $nextMenuOrder = getMenuNextOrder($id,'movedn');
        DB_query("UPDATE {$_TABLES['nexmenu']} SET menuorder = $nextMenuOrder WHERE id = '$id'");
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();;
        break;

    case 'config' :
        echo COM_siteHeader();
        echo COM_startBlock();
        echo menuConfig();
        break;

    case 'saveconfig' :
        $statusmsg = menuSaveConfig();
        if ($statusmsg != '') {
            echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexmenu/index.php?op=config&writecss=' .$_POST['writecss'] . '&statusmsg='.$statusmsg);
        } else {
            echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexmenu/index.php?op=config&writecss=' .$_POST['writecss']);
        }
        exit;
        break;

    default:
        echo COM_siteHeader();
        echo COM_startBlock();
        echo displayMenuRecords();
        break;
    }


echo COM_endBlock();
echo COM_siteFooter();


?>