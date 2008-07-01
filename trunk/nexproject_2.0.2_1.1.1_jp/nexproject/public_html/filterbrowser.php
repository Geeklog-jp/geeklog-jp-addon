<?php 
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | filterbrowser.php                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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
global $_PRJCONF;
$retval = '<div style=" height:120%; border:0px; overflow-x:auto; overflow: auto;">';

$menu->addItem(new HTML_TreeNode(array('text' => $strings["all_projects"] , 'link' => $baseurl . '/nexproject/projects.php?filter=all', 'icon' => $folder_icon)));
$i = 1;

while ($i < 2) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_category"] , 'icon' => $folder_icon));
    
    $categories = nexlistOptionList('alist', '', $_PRJCONF['nexlist_category'], 0); 
    
    foreach ($categories as $cid=>$name) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$name" , 'link' => $baseurl . '/nexproject/projects.php?filter=cat' . $cid, 'icon' => $folder_icon)));
    }
    
    $menu->addItem($node[$i]);
    $i++;
} 

$i = 2;
while ($i < 3) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_location"] , 'icon' => $folder_icon));
    
    $locations = nexlistOptionList('alist', '', $_PRJCONF['nexlist_locations'], 0); 
    foreach ($locations as $cid=>$name) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => htmlentities ("$name", ENT_QUOTES) , 'link' => $baseurl . '/nexproject/projects.php?filter=loc' . $cid, 'icon' => $folder_icon)));
    }

    $menu->addItem($node[$i]);
    $i++;
} 

$i = 3;
while ($i < 4) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_department"] , 'icon' => $folder_icon));

    $departments = nexlistOptionList('alist', '', $_PRJCONF['nexlist_departments'], 0);
    foreach ($departments as $cid=>$name) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$name" , 'link' => $baseurl . '/nexproject/projects.php?filter=dep' . $cid, 'icon' => $folder_icon)));
    }
    
    $menu->addItem($node[$i]);
    $i++;
} 

$i = 4;
while ($i < 5) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_objective"] , 'icon' => $folder_icon));

    $objectives = nexlistOptionList('alist', '', $_PRJCONF['nexlist_objective'], 0);
    foreach ($objectives as $cid=>$name) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$name" , 'link' => $baseurl . '/nexproject/projects.php?filter=dep' . $cid, 'icon' => $folder_icon)));
    }
    
    $menu->addItem($node[$i]);
    $i++;
} 

$i = 5;
while ($i < 6) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_progress"] , 'icon' => $folder_icon));
    global $progress;
    for ($j = 0; $j < count($progress); $j++) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$progress[$j]" , 'link' => $baseurl . '/nexproject/projects.php?filter=pro' . $j, 'icon' => $folder_icon)));
    } 

    $menu->addItem($node[$i]);
    $i++;
} 

$i = 6;
while ($i < 7) {
    global $priority;
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_priority"] , 'icon' => $folder_icon));
    for ($j = 0; $j < count($priority); $j++) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$priority[$j]" , 'link' => $baseurl . '/nexproject/projects.php?filter=pri' . $j, 'icon' => $folder_icon)));
    } 

    $menu->addItem($node[$i]);
    $i++;
} 

$i = 7;
while ($i < 8) {
    global $status;
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_status"] , 'icon' => $folder_icon));
    for ($j = 0; $j < count($status); $j++) {
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => "$status[$j]" , 'link' => $baseurl . '/nexproject/projects.php?filter=sta' . $j, 'icon' => $folder_icon)));
    } 

    $menu->addItem($node[$i]);
    $i++;
} 

$i = 8;
while ($i < 9) {
    $node[$i] = new HTML_TreeNode(array('text' => $strings["by_custom"] , 'icon' => $folder_icon));
    $customFilters = DB_query("SELECT flid,name FROM " . $_TABLES['prj_filters'] . " WHERE uid = {$_USER['uid']}");
    $node[$i]->addItem(new HTML_TreeNode(array('text' => " Edit", 'link' => $baseurl . '/nexproject/filters.php')));

    for ($j = 0; $j < DB_numRows($customFilters); $j++) {
        $currFilter = DB_fetchArray($customFilters);
        $bar = &$node[$i]->addItem(new HTML_TreeNode(array('text' => $currFilter['name'] , 'link' => $baseurl . '/nexproject/projects.php?filter=ctm' . $currFilter['flid'], 'icon' => $folder_icon)));
    } 

    $menu->addItem($node[$i]);
    $i++;
} 

$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $imagesdir , 'defaultClass' => 'treeMenuDefault', 'usePersistence' => 'false'));
$retval .= $treeMenu->toHTML();

$retval .= '</div>';

?>