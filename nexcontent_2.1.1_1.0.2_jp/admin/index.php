<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
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

require_once("../../../lib-common.php"); // Path to your lib-common.php
require ("imagelibrary.php");
require_once($_CONF['path'] . 'plugins/nexcontent/debug.php');  // Common Debug Code
require_once($_CONF['path'] . 'plugins/nexcontent/library.php');
require_once($_CONF['path_system'] . 'nexpro/classes/TreeMenu.php');

if (!SEC_hasRights('nexcontent.user')) {
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

$myvars = array('id','op','mode','catid','pageid','type');
ppGetData($myvars,true);

if (!isset($catid) OR $catid == '') {
    if ($pageid > 0) {
        $catid = DB_getItem($_TABLES['nexcontent_pages'],'pid',"id='{$pageid}'");
    } else {
        $catid = 0;
    }
}

if ((!isset($type) OR $type == '') AND $catid == 0) {
    $type = 'category';
} elseif (!isset($type) OR $type == '') {
    $type = 'page';
}

// Called to format and generate the Navbar
function nexcontent_showNavbar($op) {
    global $_USER,$_CONF,$LANG_SE02,$LANG_SE3,$catid,$pageid;

    require_once ($_CONF['path_system'] . 'classes/navbar.class.php');  
    $retval = '';
    $navbar = new navbar;
    $navbar->add_menuitem($LANG_SE02['6'],$_CONF['site_url']);
    if (isset($catid) AND $catid > 0) {
        $navbar->add_menuitem($LANG_SE02['1'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=categories&catid='.$catid);
    } else {
        $navbar->add_menuitem($LANG_SE02['1'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=categories');
    }        
    if ($op == 'Category Listing') { 
        if (isset($catid) AND $catid > 0) {
            $navbar->add_menuitem($LANG_SE02['2'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=editCategory&pageid='.$catid); 
            $navbar->add_menuitem($LANG_SE02['3'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory&catid='.$catid); 
            $navbar->add_menuitem($LANG_SE02['4'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addPage&catid='.$catid); 
            $navbar->add_menuitem($LANG_SE02['5'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addLink&catid='.$catid);
        } else {
            $navbar->add_menuitem($LANG_SE02['3'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory'); 
            $navbar->add_menuitem($LANG_SE02['4'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addPage'); 
            $navbar->add_menuitem($LANG_SE02['5'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addLink');
        }
        $navbar->set_selected($LANG_SE02['1']);        
    } elseif ($op == 'editCategory') {
        $navbar->add_menuitem($LANG_SE3['2'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory&catid='.$pageid);           
        $navbar->add_menuitem($LANG_SE3['5'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=editCategory&pageid='.$pageid);             
        $navbar->set_selected($LANG_SE3['5']);         
    } elseif ($op == 'editPage') {
        $navbar->add_menuitem($LANG_SE3['2'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory&catid='.$pageid);         
        $navbar->add_menuitem($LANG_SE3['3'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=editPage&pageid='.$pageid);
    } elseif ($op == 'addCategory') {
        $navbar->add_menuitem($LANG_SE3['2'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory&catid='.$catid);
        $navbar->set_selected($LANG_SE3['2']);                  
    } elseif ($op=='addPage') {
        $navbar->add_menuitem($LANG_SE3['2'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addCategory&catid='.$catid);         
        $navbar->add_menuitem($LANG_SE3['4'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addPage&catid='.$catid);
        $navbar->set_selected($LANG_SE3['4']);                 
    } elseif ($op=='addLink') {     
        $navbar->add_menuitem($LANG_SE02['5'],$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=addLink&catid='.$catid);
        $navbar->set_selected($LANG_SE02['5']);                 
    }
      
    $retval .= $navbar->generate();
    return $retval; 
}



/* Functions to Manage Site Pages  */

function displayPages($catid) {
    global $_CONF,$CONF_SE,$_TABLES,$statusmsg,$type,$LANG_SE02;
    $menu  = new HTML_TreeMenu();
    if ($catid != 0 ) {
        $parentCatid = DB_getItem($_TABLES['nexcontent_pages'],'pid',"id='$catid'");
        $node[0] = new HTML_TreeNode(array('text' => 'up one level' ,'link' => $_CONF['site_admin_url'] ."/plugins/nexcontent/index.php?catid=$parentCatid",'icon' => 'folder.gif'));
        $menu->addItem($node[0]);
        $label = DB_getItem($_TABLES['nexcontent_pages'],'name',"id='$catid'");
        $psql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='{$catid}' AND (type='page' OR type='link')";
        $psql .= COM_getPermSQL('AND');
        $psql .= ' ORDER BY pageorder, id asc';
        $pquery = DB_query($psql);
        $numpages = DB_numRows($pquery);
        if ($numpages > 0) {
            $label = $label .'&nbsp;('.$numpages.')';
        }
        $label = '<span class="treeMenuSelected">' .$label . '</span>';
        $node[$catid] = new HTML_TreeNode(array('text' => $label ,'link' => $_CONF['site_admin_url'] ."/plugins/nexcontent/index.php?catid=" .$catid ,'icon' => 'folder.gif'));
        nexcontent_recursiveView($node[$catid], $catid); 
        $menu->addItem($node[$catid]);
    } else {
        $msql = "SELECT id,pid,name,pageorder from {$_TABLES['nexcontent_pages']} WHERE pid='0' and type='category'";
        $msql .= COM_getPermSQL('AND');
        $msql .= ' ORDER BY pageorder, id asc';
        $mquery = DB_QUERY($msql);
        while ( list($id,$category,$name,$order) = DB_fetchARRAY($mquery)) {
            //echo "<br>id:$id, cat: $category, name:$name, order:$order";
            if ($catid != 0 AND $catid == $id) {
                $name = '<span class="treeMenuSelected">' .$name . '</span>';
            }
            $pquery = DB_query("SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='{$id}' AND (type='page' OR type='link')");
            $numpages = DB_numRows($pquery);
            if ($numpages > 0) {
                $name = $name .'&nbsp;('.$numpages.')';
            }
            $node[$id] = new HTML_TreeNode(array('text' => $name ,'link' => $_CONF['site_admin_url'] ."/plugins/nexcontent/index.php?catid=" .$id ,'icon' => 'folder.gif'));
            nexcontent_recursiveView($node[$id], $id); 
            $menu->addItem($node[$id]);
        }
    }
    $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $_CONF['layout_url'] .'/nexpro/images/treemenu' ,'defaultClass' => 'treeMenuDefault'));

    $q = DB_query("SELECT id,name from {$_TABLES['nexcontent_pages']} WHERE type='category' ORDER BY id");
    $selCategories = '<option value="0">Top Level</option>' . LB;
    $selCategories .= nexcontent_getFolderList($catid);


    /* Retrieve all the pages for the selected category */
    $sql = "SELECT id,sid,pageorder,name,hits,type,menutype,submenu_item,is_draft FROM {$_TABLES['nexcontent_pages']} WHERE pid='$catid' or id='$catid'";
    $sql .= COM_getPermSQL('AND');
    //$sql .= '  ORDER by type, pid,pageorder';
    $sql .= '  ORDER by pid,pageorder';
    $query = DB_query($sql);

    $mainview = new Template($_CONF['path_layout'] . 'nexcontent/admin');
    $mainview->set_file (array ('mainview' => 'pageview.thtml', 'msgline' => 'alertline.thtml', 'records'=>'pagerecords.thtml'));
    $mainview->set_var ('navbar', nexcontent_showNavbar($LANG_SE02['1']));

    $mainview->set_var ('type',$type);
    $mainview->set_var ('catid',$catid);
    $mainview->set_var ('folderview',$treeMenu->toHTML());

    $mainview->set_var ('phpself',$_SERVER['PHP_SELF']);
    if ($statusmsg != '') {
        $mainview->set_var ('alertmsg',$statusmsg);
    } else {
        $mainview->set_var ('alertmsg','');
        $mainview->set_var ('msgmode','none');
    }
    $mainview->set_var('filteroptions',$selCategories);
    $mainview->parse('alertline','msgline',true);
    $mainview->set_var ('HEADING1','ID');
    $mainview->set_var ('HEADING2','Name');
    $mainview->set_var ('HEADING3','Hits');
    $mainview->set_var ('HEADING4','Menu Type');
    $mainview->set_var ('HEADING5','Draft');
    $mainview->set_var ('HEADING6','Action');
    $mainview->set_var ('imgset',$CONF_SE['public_url'] . '/images');
    $mainview->set_var ('site_url',$_CONF['site_url']);
    $mainview->set_var ('site_admin_url',$_CONF['site_admin_url']);
    $mainview->set_var ('layout_url',$_CONF['layout_url']);
    $mainview->set_var ('nexcontent_url',$CONF_SE['public_url']);

    $i = 1;
    $currentCategory = '';
    $pageOrd = 10;
    $stepNumber = 10;

    while ( list ($id,$sid,$order,$name,$hits,$type,$menutype,$submenu,$is_draft) = DB_fetchArray($query)) {

        if ($type == 'page' AND $currentCategory != $category) {
            $pageOrd = 10;
            $currentCategory = $category;
       }

        if ($id != $catid AND$order != $pageOrd) {
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder = '$pageOrd' WHERE id = '$id'");
            $order = $pageOrd;
        }
        $pageOrd += $stepNumber;
        $check1 = ($submenu >= 1) ? ' CHECKED' : '';
        $check2 = ($is_draft == 1) ? ' CHECKED' : '';

        if ($type == 'category') {
            $mainview->set_var('pagelink', "{$_CONF['site_admin_url']}/plugins/nexcontent/index.php?catid=$id");
            $mainview->set_var('pageimage', '<img src="'.$_CONF['layout_url'] .'/nexcontent/images/admin/sitecategory.gif">');
            $editop = 'editCategory';
        } else if ($type == 'link') {
            $mainview->set_var('pagelink', "{$_CONF['site_admin_url']}/plugins/nexcontent/index.php?op=editLink&pageid=$id");
            $mainview->set_var('pageimage','<img src="'.$_CONF['layout_url'] .'/nexcontent/images/admin/sitelink.gif">');
            $editop = 'editLink';
        } else {
            $mainview->set_var('pagelink', "{$_CONF['site_admin_url']}/plugins/nexcontent/index.php?op=editPage&pageid=$id");
            $mainview->set_var('pageimage','<img src="'.$_CONF['layout_url'] .'/nexcontent/images/admin/sitepage.gif">');
            $editop = 'editPage';
        }

        $menuoptions = '';
        foreach ($CONF_SE['menuoptions'] as $value => $label ) {
            if ($name == 'frontpage') {
                if ($value == '0') {
                    $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
                }
            }elseif ($value == 0 OR ($type == 'page' AND $value == 3) OR ($type == 'link' AND $value == 3) ) {
                if ($value == $menutype) {
                    $menuoptions .= '<option value="'.$value.'" SELECTED=SELECTED>'.$label.'</option>';
                } else {
                    $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
                }
            } elseif ($type == 'category' AND ($catid > 0 OR $value <> 3) ) {
                if ($value == $menutype) {
                    $menuoptions .= '<option value="'.$value.'" SELECTED=SELECTED>'.$label.'</option>';
                } else {
                    $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
                }
            }
        }
        $mainview->set_var ('menuoptions',$menuoptions);

        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$id' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $pquery = DB_query($sql);
        if (DB_numRows($pquery) > 0) {
            $link = '&nbsp;<a href="'.$_CONF['site_admin_url'] .'/plugins/nexcontent/index.php?op=';
            $editimg = '<img src="'.$_CONF['layout_url']. '/nexcontent/images/admin/edit.gif" TITLE="Edit Page" border="0">';
            $editlink = $link . $editop.'&pageid='.$id.'">'.$editimg.'</a>';
            $copyimg = '<img src="'.$_CONF['layout_url']. '/nexcontent/images/admin/copy.gif" TITLE="Copy Page" border="0">';
            $copylink = $link . 'copyPage&pageid='.$id.'">'.$copyimg.'</a>';
            $LANG_CONFIRM = 'Please confirm that you want to delete this page and any associated images';
            $deleteimg = '<img src="'.$_CONF['layout_url']. '/nexcontent/images/admin/delete.gif" TITLE="Delete Page" border="0">';
            $deletelink = $link .'delPage&pageid='.$id.'" onclick="return confirm(\''.$LANG_CONFIRM.'\');">'.$deleteimg.'</a>';
        } else {
            $editlink = '';
            $deletelink = '';
        }
        $mainview->set_var('sid',$sid);
        $mainview->set_var ('cssid',$i);
        $mainview->set_var ('pageid',$id);
        $mainview->set_var ('pagename',$name);
        $mainview->set_var ('hits',$hits);
        $mainview->set_var ('order',$order);
        $mainview->set_var ('check1',$check1);
        $mainview->set_var ('check2',$check2);
        $mainview->set_var ('LANG_EDIT','Edit Page');
        $mainview ->set_var('editlink',$editlink);
        $mainview ->set_var('copylink',$copylink);
        $mainview->set_var ('LANG_DELETE','Delete Page');
        $mainview ->set_var('deletelink',$deletelink);
        $mainview->set_var ('LANG_MOVEUP','Move Page Up');
        $mainview->set_var ('LANG_MOVEDN','Move Page Down');
        $mainview->set_var ('LANG_PREVIEW','Preview this page');

        $mainview->parse('page_records','records',true);

        $i = ($i==2? 1 : 2);
    }

    $mainview->parse ('output', 'mainview');
    $retval .=  $mainview->finish ($mainview->get_var('output'));

    return $retval;
}

function editPage($mode,$type) {
    global $_CONF,$CONF_SE,$_TABLES,$catid,$pageid,$page,$op;
    global $_USER,$_POST,$_FILES,$LANG_ACCESS;
    global $LANG_SE01, $LANG_SE3, $LANG_SE05, $LANG_SE10;

    $blkformat_options = array('none','allblocks','leftonly', 'rightonly','customblocks','blankpage');
    if ($mode == 'edit') {
        $query = DB_query("SELECT * FROM {$_TABLES['nexcontent_pages']} WHERE id='{$pageid}'");
        $A = DB_fetchArray($query);
        for ($i = 1; $i <= $CONF_SE['max_num_images']; $i++) {
            $curimage = DB_getitem($_TABLES['nexcontent_images'], "imagefile", "page_id='$pageid' AND imagenum='$i'");
            $imageoption = DB_getitem($_TABLES['nexcontent_images'], "autoscale", "page_id='$pageid' AND imagenum='$i'");
            if ($curimage != '') {
                $images[]       = $CONF_SE['uploadpath'] ."/{$pageid}/{$curimage}";
                $imageopt[]     = $imageoption;
                $thumbnails[]   = $CONF_SE['public_url'] . "/images/{$pageid}/tn{$curimage}";
            } else {
                $images[]       = '';
                $imageopt[]     = '';
                $thumbnails[]   = '';
            }
        }
        $submit = 'Update';
        $saveandclose = 'Save and Close';
        $chk_rad1 = ($A['show_submenu'] == 0) ? ' CHECKED' : '';
        $chk_rad2 = ($A['show_submenu'] == 1) ? ' CHECKED' : '';
        $chk_rad3 = ($A['show_submenu'] == 2) ? ' CHECKED' : '';
        $chk_rad4 = ($A['show_blockmenu'] == 0) ? ' CHECKED' : '';
        $chk_rad5 = ($A['show_blockmenu'] == 1) ? ' CHECKED' : '';
        $chk_rad6 = ($A['show_blockmenu'] == 2) ? ' CHECKED' : '';

        $check1 = ($A['is_menu_newpage'] == 1) ? ' CHECKED' : '';
        $check2 = ($A['show_breadcrumbs'] == 1) ? ' CHECKED' : '';
        $check3 = ($A['is_draft'] == 1) ? ' CHECKED' : '';

        $pageid = $A['id'];
        $parentCatid = DB_getItem($_TABLES['nexcontent_pages'],'pid',"id='{$pageid}'");
        if ($type == 'category') {
            $catid = $A['id'];
        } else {
            $catid = $parentCatid;
        }

    } else {
        $categoryImage = '';
        $A['owner_id'] = $_USER['uid'];
        $A['group_id'] = DB_getItem ($_TABLES['groups'], 'grp_id', "grp_name = 'nexcontent Admin'");
        $A['perm_owner'] = 3;
        $A['perm_group'] = 2;
        $A['perm_members'] = 2;
        $A['perm_anon'] = 2;
        if ($type == 'category') {
            $submit = 'Update';
            $saveandclose = 'Create and Close';
            $parentCatid = $catid;
        } else {
            $submit = 'Update';
            $saveandclose = 'Create and Close';
            $parentCatid = $catid;
        }
        $chk_rad2 = ' CHECKED';
        $chk_rad5 = ' CHECKED';
        $check2 = ($CONF_SE['breadcrumbs'] == true) ? ' CHECKED' : '';
    }

    if ($mode == 'edit') {
        if ($type == 'category') {
            $check4children = DB_count($_TABLES['nexcontent_pages'], 'pid', $pageid);
            if ($check4children == 0) {
                $convert_page = '<input type="submit" name="convert_page" value="Convert">';
            }
        }
        else if ($type == 'page') {
            $convert_page = '<input type="submit" name="convert_page" value="Convert">';
        }
        else {
            $convert_page = '';
        }
    }

    $page = new Template($_CONF['path_layout'] . 'nexcontent/admin');
    if ($type == 'link') {
        $page->set_file ('page','editlink.thtml');
    }
    else {
        $page->set_file ('page','editpage.thtml');
    }
    $page->set_file (array('pagecategory' => 'editpage_category.thtml',
                                 'thumbnail'    => 'thumbnail.thtml',
                                 'enterimage'   => 'enterimage.thtml',
                                 'taghelp'      => 'taghelp_record.thtml'
                         ));

    $page->set_var('convert_page',$convert_page);
    $page->set_var('LANG_submit',$submit);
    $page->set_var('LANG_saveandclose',$saveandclose);
    $page->set_var('phpself',$_SERVER['PHP_SELF']);
    $page->set_var('site_url',$_CONF['site_url']);
    $page->set_var('layout_url',$_CONF['layout_url']);
    $page->set_var('mode',$mode);
    $page->set_var('type',$type);
    $page->set_var('catid',$catid);
    $page->set_var('pageid',$pageid);
    $page->set_var('sid',$A['sid']);
    $page->set_var('name',$A['name']);
    $page->set_var('heading',$A['heading']);
    $page->set_var('content',$A['content']);
    $page->set_var('page_title',($A['pagetitle'] == '') ? $CONF_SE['pagetitle'] : $A['pagetitle'] );
    $page->set_var('page_order',$A['pageorder']);
    $page->set_var('meta_description',($A['meta_description'] == '') ? $CONF_SE['meta_description'] : $A['meta_description'] );
    $page->set_var('meta_keywords',($A['meta_keywords'] == '') ? $CONF_SE['meta_keywords'] : $A['meta_keywords'] );
    if ($CONF_SE['loadImageUploader']) {
        $page->set_var('show_image_tab', 'show');
    }
    else {
        $page->set_var('show_image_tab', 'none');
    }

    $page->set_var('lang_title','Page Title');
    $page->set_var('lang_order','Page Order');
    $page->set_var('lang_metadescription','META Description<br><tt>200-250 words</tt>');
    $page->set_var('lang_metakeywords','META Keywords<br><tt>20-25 comma<br>separated keywords</tt>');

    $page->set_var('chk_rad1',$chk_rad1);
    $page->set_var('chk_rad2',$chk_rad2);
    $page->set_var('chk_rad3',$chk_rad3);
    $page->set_var('chk_rad4',$chk_rad4);
    $page->set_var('chk_rad5',$chk_rad5);
    $page->set_var('chk_rad6',$chk_rad6);
    $page->set_var('check1',$check1);
    $page->set_var('check2',$check2);
    $page->set_var('check3',$check3);

    $page->set_var('max_uploadsize',$CONF_SE['max_uploadfile_size']);
    $page->set_var('max_uploadwidth',$CONF_SE['max_upload_width']);
    $page->set_var('max_uploadheight',$CONF_SE['max_upload_height']);
    $page->set_var('thumbnail_size',$CONF_SE['auto_thumbnail_dimension']);

    $page->set_var('LANG_category','Parent Category');
    $q = DB_query("SELECT id,name from {$_TABLES['nexcontent_pages']} WHERE pid='0' ORDER BY id");
    $selCategories = '';

    //echo "<br>Type:$type, catid:$catid, parent:$parentCatid";

    if ($type == 'category' and $parentCatid == 0) {
        if (SEC_hasRights('nexcontent.edit')) {
            $selCategories .= '<option value="0" SELECTED>Top Level</option>' . LB;
        }
        $selCategories .= nexcontent_getFolderList($parentCatid,$mode,$catid);
    } elseif ($type == 'category') {
        if (SEC_hasRights('nexcontent.edit')) {
            $selCategories .= '<option value="0">Top Level</option>' . LB;
        }
        $selCategories .= nexcontent_getFolderList($parentCatid,$mode,$catid);
    } else {
        $selCategories .= nexcontent_getFolderList($parentCatid,$mode);
    }

    $page->set_var('sel_categories',$selCategories);
    $page->parse('select_category', 'pagecategory',true);
    $page->set_var('navbar',nexcontent_showNavbar($op));
    $selBlockType = '';
    foreach ($blkformat_options as $var) {
        if ($A['blockformat'] == $var) {
            $selBlockType .= '<option value="'.$var.'" selected>'.$var.'</option>';
        } else {
            $selBlockType .= '<option value="'.$var.'">'.$var.'</option>';
        }
    }
    $page->set_var('sel_blocktype',$selBlockType);

    $menuoptions = '';
    foreach ($CONF_SE['menuoptions'] as $value => $label ) {
        if ($A['name'] == 'frontpage') {
            if ($value == '0') {
                $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
            }
        }elseif ($value == 0 OR ($type == 'page' AND $value == 3) ) {
            if ($value == $A['menutype']) {
                $menuoptions .= '<option value="'.$value.'" SELECTED=SELECTED>'.$label.'</option>';
            } else {
                $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
            }
        } elseif ($type == 'category' AND ($catid > 0 OR $value <> 3) ) {
            if ($value == $A['menutype']) {
                $menuoptions .= '<option value="'.$value.'" SELECTED=SELECTED>'.$label.'</option>';
            } else {
                $menuoptions .= '<option value="'.$value.'">'.$label.'</option>';
            }
        }
    }
    $page->set_var ('sel_menutypes',$menuoptions);
    $page->set_var('LANG_SubmenuDescription', '');
    $page->set_var('LANG_BlockmenuDescription', '');
    $page->set_var('LANG_WindowDescription', 'Clicking on menuitem will launch a new browser');
    $page->set_var('LANG_DraftDescription', 'If enabled, page will not be published');
    $page->set_var('LANG_Breadcrumbs', 'If enabled, page will show breadcrumb links at top');

    for ($i = 0; $i < $CONF_SE['max_num_images']; $i++) {
        $imagenum = $i + 1;
        if (!empty($images[$i]) AND !is_dir($images[$i]) AND file_exists($images[$i])) {
            $page->set_var('imagenum',$imagenum);
            if ($imageopt[$i] == '0' OR $imageopt[$i] == '') {
                $page->set_var('chkscaleopt','');
            } else {
                $page->set_var('chkscaleopt','CHECKED=CHECKED');
            }
            $page->set_var('thumbnail_url',$thumbnails[$i]);
            $page->parse( 'thumbnail_image', 'thumbnail', true );

        } else {
            $page->set_var('imagenum',$imagenum);
            $page->parse( 'thumbnail_image', 'enterimage', true );
        }
    }

    $page->set_var('lang_accessrights',$LANG_ACCESS['accessrights']);
    $page->set_var('lang_owner', $LANG_ACCESS['owner']);
    $page->set_var ('owner_username', DB_getItem ($_TABLES['users'],
                               'username', "uid = {$A['owner_id']}"));
    $page->set_var('owner_id', $A['owner_id']);
    $page->set_var('lang_group', $LANG_ACCESS['group']);

    $usergroups = SEC_getUserGroups();
    $groupdd = '';

    for ($i = 0; $i < count($usergroups); $i++) {
        $groupdd .= '<option value="' . $usergroups[key($usergroups)] . '"';
        if ($A['group_id'] == $usergroups[key($usergroups)]) {
            $groupdd .= ' selected="selected"';
        }
        $groupdd .= '>' . key($usergroups) . '</option>';
        next($usergroups);
    }

    $page->set_var('group_dropdown', $groupdd);
    $page->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $page->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
    $page->set_var('permissions_editor', SEC_getPermissionsHTML($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']));
    $page->set_var('permissions_msg', $LANG_ACCESS['permmsg']);

    foreach ($LANG_SE05 as $tagname => $helpmessage) {
           $page->set_var('tagname',$tagname);
           $page->set_var('help_msg',$helpmessage);
           $page->parse('taghelp_lines','taghelp',true);
    }

    $query = DB_query("SELECT name,title FROM {$_TABLES['blocks']} ORDER BY name");
    while (list ($name,$title) = DB_fetchArray($query)) {
           $page->set_var('tagname',$title);
           $page->set_var('help_msg',$name);
           $page->parse('blockhelp_lines','taghelp',true);
    }

    $query = DB_query("SELECT help FROM {$_TABLES['nexcontent']}");
    list ($help) = DB_fetchArray($query);
    $page->set_var('customhelp',$help);

    $page->parse ('output', 'page');
    $retval =  $page->finish ($page->get_var('output'));

    return $retval;
}

function updatePage($mode,$type) {
    global $_CONF, $_TABLES, $_FILES, $_POST, $CONF_SE, $LANG_SE_ERR;
    global $_DB_name, $catid,$pageid;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $name      = substr(htmlentities($_POST['name']), 0, 32);
    $pid       = ppPrepareForDB($_POST['category']);
    $old_sid   = ppPrepareForDB($_POST['old_sid']);
    $sid       = ppPrepareForDB($_POST['sid'], true, 40);
    $pageorder = COM_applyFilter($_POST['pageorder'], true);
    if ($type == 'link') {
        $menutype = 3;
    }
    else {
        $menutype = COM_applyFilter($_POST['menu_type'], true);
    }
    $blkformat    = ppPrepareForDB($_POST['blk_format']);
    $heading = substr(htmlentities($_POST['heading']), 0, 255);
    $grp_access   = ppPrepareForDB($_POST['grp_access']);
    $imgdelete  = $_POST['imgdelete'];
    $chkscale  = $_POST['chkscale'];
    $submenutype = COM_applyFilter($_POST['rad_submenu'], true);
    $blockmenutype = COM_applyFilter($_POST['rad_blockmenu'], true);

    $is_menu_newpage   = ($_POST['chknewwindow'] == 1) ? 1:0;
    $is_draft          = ($_POST['chkdraft'] == 1) ? 1:0;
    $show_breadcrumbs  = ($_POST['chkbreadcrumbs'] == 1) ? 1:0;

    $owner_id   = ppPrepareForDB($_POST['owner_id']);
    $group_id   = ppPrepareForDB($_POST['group_id']);
    $perm_owner   = $_POST['perm_owner'];
    $perm_group   = $_POST['perm_group'];
    $perm_members   = $_POST['perm_members'];
    $perm_anon   = $_POST['perm_anon'];

    $pagetitle = substr(htmlentities($_POST['pagetitle']), 0, 255);
    $metadesc = ppPrepareForDB($_POST['metadesc']);
    $metakeywords = ppPrepareForDB($_POST['metakeywords']);

    // Convert array values to numeric permission values
    list($perm_owner,$perm_group,$perm_members,$perm_anon) = SEC_getPermissionValues($perm_owner,$perm_group,$perm_members,$perm_anon);

    // Allow full HTML in the introtext field
    if(!get_magic_quotes_gpc() ) {
        $content = addslashes($_POST['sitecontent']);
        $help = addslashes($_POST['help']);
    } else {
        $content = $_POST['sitecontent'];
        $help = $_POST['help'];
    }
    
    if ($sid != '') {
        $sid = COM_sanitizeID($sid);
    }
    if ($sid != '' AND DB_count ($_TABLES['nexcontent_pages'], 'sid', $sid) > 0) {
        if ($sid != $old_sid) {
            $duplicate_sid = true;
            if ($old_sid == '') {
                $sid = "{$sid}_{$pid}";
                $dupmsg = ' - Duplicate Page ID';
            } else {
                $sid = $old_sid;
                $dupmsg = ' - Duplicate Page ID, Page ID not changed.';
            }
        }
    } else {
        $duplicate_sid = false;
    }

    if ($mode == 'add') {
        $gid = uniqid($_DB_name,FALSE);
        $category = COM_applyFilter($category, true);
        if ($type == 'category') {
            // Create a new record - set the category value to 0
            DB_query("INSERT INTO {$_TABLES['nexcontent_pages']} (pid,gid,type) values ($category,'$gid','category')");
            $pageid = DB_insertID();
            $GLOBALS['statusmsg'] = 'New Category Added';
            $query = DB_query("SELECT max(pageorder) FROM {$_TABLES['nexcontent_pages']} WHERE type='category'");
            list ($maxorder) = DB_fetchArray($query);
            $order = $maxorder + 10;
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder='{$order}' WHERE id='{$pageid}'");
        } else {
            // Create a new record - need to get the record id for the category
            DB_query("INSERT INTO {$_TABLES['nexcontent_pages']} (pid,gid,type) values ('$category','$gid','$type')");
            $pageid = DB_insertID();
            $GLOBALS['statusmsg'] = 'New Page Added';
            $query = DB_query("SELECT max(pageorder) FROM {$_TABLES['nexcontent_pages']} WHERE pid='category'");
            list ($maxorder) = DB_fetchArray($query);
            $order = $maxorder + 10;
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder='{$order}' WHERE id='{$pageid}'");
        }
    } else {
        if ($type == 'category') {
            $GLOBALS['statusmsg'] = "$name Updated";
        } else {
            $GLOBALS['statusmsg'] = "$name Updated";
        }
        if ($duplicate_sid) {
            $GLOBALS['statusmsg'] .= $dupmsg;
        }
    }
    DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET name='{$name}', blockformat='{$blkformat}', pid='{$pid}', sid='{$sid}', heading='{$heading}',content='{$content}', menutype='$menutype', is_menu_newpage='$is_menu_newpage', show_submenu='$submenutype', show_blockmenu='$blockmenutype', show_breadcrumbs='$show_breadcrumbs', is_draft='$is_draft', owner_id='{$owner_id}', group_id='{$group_id}', perm_owner='{$perm_owner}', perm_group='{$perm_group}', perm_members='{$perm_members}', perm_anon='{$perm_anon}' , pagetitle='$pagetitle', meta_description='{$metadesc}', meta_keywords='{$metakeywords}' WHERE id='$pageid'");

    DB_query("UPDATE {$_TABLES['nexcontent']} SET help='{$help}'");

    //update the page order
    if ($pageorder != '' AND $pageid != '') {
        DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder=$pageorder WHERE id=$pageid;");
        $porder = DB_query("SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid=$pid ORDER BY pageorder ASC;");
        $i = 0;
        while ($ORDER = DB_fetchArray($porder)) {
            $i += 10;
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder=$i WHERE id={$ORDER['id']};");
        }
    }

    $pageImageDir = $CONF_SE['uploadpath'] . "/{$pageid}/";
    // Check and see if directories exist
    if (!file_exists($pageImageDir)) {
        $mkdir =@mkdir ($pageImageDir);
        $chmod =@chmod ($pageImageDir,$CONF_SE['imagedir_perms']);
    }

    // Delete any images if needed
    for ($i = 0; $i < count($imgdelete); $i++) {
        $curimage = DB_getitem($_TABLES['nexcontent_images'], "imagefile", "page_id='$pageid' AND imagenum='{$imgdelete[$i]}'");
        $fullimage = $pageImageDir . $curimage;
        if (!is_dir($fullimage) AND file_exists($fullimage)) {
            if (!unlink($fullimage)) {
                echo COM_errorLog("Unable to delete image $fullimage. Please check file permissions");
                $GLOBALS['statusmsg'] = "Unable to delete image $fullimage. Please check file permissions";
            }
        }
        $pos = strrpos($curimage,'.');
        $origimage = strtolower(substr($curimage, 0,$pos));
        $ext = strtolower(substr($curimage, $pos));
        $origimage .= "_original{$ext}";
        $fullimage = $pageImageDir . $origimage;
        if (!is_dir($fullimage) AND file_exists($fullimage)) {
            if (!unlink($fullimage)) {
                echo COM_errorLog("Unable to delete image $fullimage. Please check file permissions");
                $GLOBALS['statusmsg'] = "Unable to delete image $fullimage. Please check file permissions";
            }
        }
        $curthumbnail = $pageImageDir . 'tn'.$curimage;
        if (!is_dir($curthumbnail) AND file_exists($curthumbnail)) {
            if (!unlink($curthumbnail)) {
                echo COM_errorLog("Unable to delete thumbnail for $curthumbnail. Please check file permissions");
                $GLOBALS['statusmsg'] = "Unable to delete thumbnail for $curthumbnail. Please check file permissions";
            }
        }
        DB_query("DELETE FROM {$_TABLES['nexcontent_images']} WHERE page_id='$pageid' and imagenum='{$imgdelete[$i]}'");
        next($imgdelete);
    }


    $upload = new upload();
    $upload->setLogging(false);
    $upload->setDebug(false);
    $upload->setLogFile($_CONF['path_log'] .'error.log');
    $upload->setMaxFileUploads ($CONF_SE['max_num_images']);
    if ($_CONF['image_lib'] == 'imagemagick') {
        $upload->setMogrifyPath($_CONF['path_to_mogrify']);
    } else {
        $upload->setGDLib ();
    }
    $upload->setAllowedMimeTypes($CONF_SE['allowableImageTypes']);
    $upload->setMaxDimensions($CONF_SE['max_upload_width'], $CONF_SE['max_upload_height']);
    $upload->setMaxFileSize($CONF_SE['max_uploadfile_size']);
    $upload->setAutomaticResize(true);
    $upload->keepOriginalImage (true);

    $upload->setPerms($CONF_SE['image_perms']);
    if (!$upload->setPath($pageImageDir)) {
        $GLOBALS['statusmsg'] = $LANG_SE_ERR['upload1'] .':&nbsp;' . $upload->printErrors(false);
    }

    // OK, let's upload any pictures with this page
    if (DB_count($_TABLES['nexcontent_images'], 'page_id', $pageid) > 0) {
        $index_start = DB_getItem($_TABLES['nexcontent_images'],'max(imagenum)',"page_id = '$pageid'") + 1;
    } else {
        $index_start = 1;
    }
    $index_start = 1;

    $uniquename = time();

    $filenames = array();
    $imagenum = array();
    for ($z = 1; $z <= $CONF_SE['max_num_images']; $z++) {
        $curfile = current($_FILES);
        if (!empty($curfile['name'])) {
            $filenames[] = $uniquename . $z . '.jpg';
            $imagenum[] =  substr(key($_FILES),9,1);
        }
        next($_FILES);
    }
    $upload->setFileNames($filenames);
    reset($_FILES);
    $upload->setDebug(false);
    $upload->uploadFiles();
    if ($upload->areErrors()) {
        $GLOBALS['statusmsg'] = $LANG_SE_ERR['upload1'] .':&nbsp;' . $upload->printErrors(false);
        return false;
    }
    reset($filenames);
    reset($imagenum);

    if (DB_count($_TABLES['nexcontent_pages'],"id",$pageid) > 0 ) {
        foreach ($filenames as $pageImage) {
            $index = current($imagenum);
            if (file_exists($pageImageDir.$pageImage)) {
                $src =  $pageImageDir .$pageImage;
                $dest = $pageImageDir . 'tn' .$pageImage;
                makethumbnail($pageImage,$src,$dest);
                $iquery = DB_query("SELECT imagefile from {$_TABLES['nexcontent_images']} WHERE page_id='{$pageid}' AND imagenum='$index'");
                if (DB_numRows($iquery) == 0 ) {
                    DB_query("INSERT INTO {$_TABLES['nexcontent_images']} (page_id,imagenum,imagefile) values ('$pageid', '$index','$pageImage')");
                } elseif (DB_numRows($iquery) == 1) {
                    DB_query("UPDATE {$_TABLES['nexcontent_images']} SET imagefile='{$pageImage}' WHERE page_id='$pageid' and imagenum='$index'"); 
                }
            }
            next($imagenum);
        }
    } else {
        $GLOBALS['statusmsg'] = 'Error saving category';
    }

    // Update the image autoscale option for any images
    $query = DB_query("SELECT id,imagenum from {$_TABLES['nexcontent_images']} WHERE page_id='{$pageid}'");
    while (list ($imageid,$imagenum) = DB_fetchArray($query)) {
        if ($chkscale[$imagenum] == '1') {
            DB_query("UPDATE {$_TABLES['nexcontent_images']} SET autoscale = '1' WHERE id='{$imageid}' AND imagenum='{$imagenum}'");
        } else {
            DB_query("UPDATE {$_TABLES['nexcontent_images']} SET autoscale = '0' WHERE id='{$imageid}' AND imagenum='{$imagenum}'");
        }
    }
}

function deleteContentCategory($id) {
    global $_TABLES;

    $query = DB_query("SELECT id,pid FROM {$_TABLES['nexcontent_pages']} WHERE PID = '{$id}'");
    while ( list ($pageid,$category) = DB_fetchArray($query)) {
        if (DB_count($_TABLES['nexcontent_pages'],"pid",$pageid) > 0) {
            // Additional pages or category(s) - 
            deleteContentCategory($pageid);
        } else {
            deletePage($pageid);
        }
    }
    /* Delete the parent category that was requested */
    deletePage($id);
}

function deletePage($pageid) {
    global $_CONF, $_TABLES;
    /* Check and see if this page is a category that contains other pages */
    if (DB_count($_TABLES['nexcontent_pages'],"pid",$pageid) == 0) {
        if (DB_count($_TABLES['nexcontent_pages'],"id",$pageid) == 1) {
            $pageImageDir = $CONF_SE['uploadpath'] . "/{$pageid}/";
            $current_dir = @opendir($pageImageDir);
            while($entryname = @readdir($current_dir)) {
                if(is_dir("$pageImageDir/$entryname") and ($entryname != "." and $entryname!="..")){
                   @rmdir("${pageImageDir}/${entryname}");
                 } elseif($entryname != "." and $entryname!="..") {
                   @unlink("${pageImageDir}/${entryname}");
                 }
            }
            @closedir($current_dir);
            @rmdir(${pageImageDir});
            $type = DB_getItem($_TABLES['nexcontent_pages'],'type',"id='{$pageid}'");
            DB_query("DELETE FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid'");
            DB_query("DELETE FROM {$_TABLES['nexcontent_images']} WHERE page_id='$pageid'");
            if ($type == 'category') {
                $GLOBALS['statusmsg'] = 'Site Category page and images deleted';
            } else {
                $GLOBALS['statusmsg'] = 'Site page and images deleted';
            }
        }
    } else {
        echo COM_startBlock("Alert! Site category contains other pages");
        echo '<table width="100%" border="0" cellspacing="0" cellpadding="5"  bgcolor="#EFEFEF"><br><p />
            <form action="'.$_SERVER['PHP_SELF'].'" method="post">
            <input type="hidden" name="catid" value="'.$pageid.'">
            <input type="hidden" name="op" value="delCategory">
            <tr bgcolor="#FFFFFF">
                <td style="padding-left:20px;">Do you really want to delete this collection of pages and associated images?</td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td style="text-align:center;padding:20px 0px 10px 20px;"><input type="submit" name="submit" value="Delete">&nbsp;&nbsp;<input type="button" value="Cancel" onclick=\'javascript:history.go(-1)\'></td>
            </tr>
              </table>
              </form>';
        echo COM_endBlock();
        exit;
    }

}

function convertPage($pageid, $currentType) {
    global $_TABLES;
    $newType = ($currentType == 'category') ? 'page':'category';
    DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET type='$newType' WHERE id=$pageid;");
}

function nc_recursiveCopyChildren($pageid, $pid) {
    global $_TABLES;

    $query = DB_query("SELECT * FROM {$_TABLES['nexcontent_pages']} WHERE pid=$pageid;");
    while ($A = DB_fetchArray($query)) {
        $insID = nc_copyRecord($_TABLES['nexcontent_pages'], 'id', $A['id']);
        $name = DB_getItem($_TABLES['nexcontent_pages'], 'name', "id=$insID");
        $name = substr("$name - copy", 0, 32);
        DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET sid='', hits=0, pid=$pid, name='$name' WHERE id=$insID;");

        nc_recursiveCopyChildren($A['id'], $insID);
    }
}

/*  ---   MAIN CODE  ---  */

switch ($op) {

    case "categories" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        $type = 'category';
        echo displayPages($catid);
        break;

    case "addCategory" :
        echo COM_siteHeader('none');
        $heading = 'Add a New Category Page';
        echo COM_startBlock($heading,'','blockheader.thtml',true);
        echo editPage('add','category');
        break;

    case "editCategory" :
        echo COM_siteHeader('none');
        $heading = 'Edit Category Page';
        echo COM_startBlock($heading,'','blockheader.thtml',true);
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            echo editPage('edit','category');
        } else {
            echo "<br>You do not have permissions to edit this page";
        }
        break;

    case "delCategory" :
        echo COM_siteHeader('none');
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$catid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            deleteContentCategory($catid);
       } else {
            $statusmsg = "<br>You do not have permissions to Delete this category";
        }
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo displayPages('0');
        break;

    case "addPage" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo editPage('add','page');
        break;

    case "editPage" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo editPage('edit','page');
        break;

    case "savePage" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        updatePage($mode,$type);
        if ($_POST['save_and_close'] != '') {
            echo displayPages($catid);
        }
        else if ($_POST['convert_page'] != '') {
            convertPage($pageid,DB_getItem($_TABLES['nexcontent_pages'], 'type', "id=$pageid"));
            echo editPage('edit',DB_getItem($_TABLES['nexcontent_pages'], 'type', "id=$pageid"));
        }
        else {
            echo editPage('edit',DB_getItem($_TABLES['nexcontent_pages'], 'type', "id=$pageid"));
        }
        break;

    case "copyPage" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        $insID = nc_copyRecord($_TABLES['nexcontent_pages'], 'id', $pageid);
        $name = DB_getItem($_TABLES['nexcontent_pages'], 'name', "id=$insID");
        $name = substr("$name - copy", 0, 32);
        DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET sid='', hits=0, name='$name' WHERE id=$insID;");

        nc_recursiveCopyChildren($pageid, $insID);

        echo displayPages($catid);
        break;

    case "delPage" :
        echo COM_siteHeader('none');
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            deletePage($pageid);
        } else {
            $statusmsg = "You do not have permissions to Delete this page";
        }
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo displayPages($catid);
        break;

    case "addLink" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo editPage('add', 'link');
        break;

    case "editLink" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo editPage('edit', 'link');
        break;

    case "moveup" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder = pageorder -11 WHERE id = '$pageid'");
        }
        echo displayPages($catid);
        break;

    case "movedn" :
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET pageorder = pageorder +11 WHERE id = '$pageid'");
        }
        echo displayPages($catid);
        break;

    case "setMenu" :
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            $menutype  = ppPrepareForDB($_POST['menutype']);
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET menutype = '{$menutype}' WHERE id = '$pageid'");
        }
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo displayPages($catid);
        break;

    case "setSubMenu" :
        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            $menutype  = ppPrepareForDB($_POST['menutype']);
            $submenu = COM_applyFilter($submenu, true);
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET submenu_item = '{$submenu}' WHERE id = '$pageid'");
        }
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo displayPages($catid);
        break;

    case "setDraft" :

        $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$pageid' ";
        $sql .= COM_getPermSQL('AND',0,3);
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            if ($_POST['draftoption'] == 1) {
                $draftoption = 1;
            } else {
                $draftoption = 0;
            }
            DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET is_draft = '{$draftoption}' WHERE id = '$pageid'");
        }
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        echo displayPages($catid);
        break;

    default:
        echo COM_siteHeader('none');
        echo COM_startBlock("Site Content Management",'','blockheader.thtml',true);
        if ($catid > 0) {
            $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$catid' ";
            $sql .= COM_getPermSQL('AND');
            $query = DB_query($sql);
            if (DB_numRows($query) > 0) {
                echo displayPages($catid);
           } else {
                echo "<br>You do not have permissions to this page";
            }
        } else {
            echo displayPages($catid);
        }
        break;
    }


echo COM_endBlock();
echo COM_siteFooter();


?>