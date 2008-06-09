<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-projects.php                                                          |
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
function prj_viewproject_details($id) {
    global $_PRJCONF, $_TABLES,$_CONF,$_USER,$pluginLangLabels,$progress,$priority,$status;
      
    $sql = "SELECT p.*, s.description 'statusdesc' FROM {$_TABLES['prj_projects']} p ";
    $sql .= "LEFT JOIN {$_TABLES['prj_statuslog']} s ON p.pid=s.pid WHERE p.pid='$id' order by s.slid desc";
    $result = DB_query($sql);
    $projectrec = DB_fetchArray($result);
    $p = new Template($_CONF['path_layout'] . 'nexproject');
    $p->set_file (array('project' => 'viewproject.thtml', 'tasklog' => 'tasklog_record.thtml'));
    $p->set_var($pluginLangLabels);
    $p->set_var('show_resources','none');
    prj_setTemplateVars($p,$projectrec);
    $project_owner = COM_getDisplayName(DB_getItem ($_TABLES['prj_users'], 'uid', "pid = $id AND role='o'"));
    $p->set_var('VALUE_project_owner',$project_owner);    
    $p->set_var('VALUE_progress_color',$progress[$projectrec['progress_id']]);
    $p->set_var('VALUE_status',$status[$projectrec['status_id']]);
    $p->set_var('VALUE_priority',$priority[$projectrec['priority_id']]);
    $p->set_var('VALUE_objective', nexlistValue( $_PRJCONF['nexlist_objective'],$projectrec['objective_id'],0)); 

    $query = DB_query("SELECT location_id FROM {$_TABLES['prj_location']} WHERE pid='$id'");
    while (list($location_id) = DB_fetchArray($query)) {
        $location=nexlistValue($_PRJCONF['nexlist_locations'],$location_id,0);
        if ($VALUE_location == '') {
            $VALUE_location = $location;
        } else {
            $VALUE_location .= ", $location";
        }
    }
    $p->set_var('VALUE_location',$VALUE_location);

    $query = DB_query("SELECT department_id FROM {$_TABLES['prj_department']} WHERE pid='$id'");
    while (list($department_id) = DB_fetchArray($query)) {
        $department=nexlistValue($_PRJCONF['nexlist_departments'],$department_id,0);
        if ($VALUE_department == '') {
            $VALUE_department = $department;
        } else {
            $VALUE_department .= ", $department";
        }
    }
    $p->set_var('VALUE_department',$VALUE_department);

    $query = DB_query("SELECT category_id FROM {$_TABLES['prj_category']} WHERE pid='$id'");
    while (list($category_id) = DB_fetchArray($query)) {
        //$category = DB_getItem($_TABLES['prj_site_category'], 'description', "category_id=$category_id");
        $category=nexlistValue($_PRJCONF['nexlist_category'],$category_id,0);
        if ($VALUE_category == '') {
            $VALUE_category = $category;
        } else {
            $VALUE_category .= ", $category";
        }
    }
    $p->set_var('VALUE_category',$VALUE_category);
    
    $sql  = "SELECT {$_TABLES['users']}.uid FROM {$_TABLES['users']},{$_TABLES['prj_users']} WHERE ";
    $sql .= "{$_TABLES['prj_users']}.pid=$id AND {$_TABLES['prj_users']}.uid={$_TABLES['users']}.uid ";            
    $query = DB_query($sql);
    while (list($resource) = DB_fetchArray($query)) {
        $resource_name = COM_getDisplayName($resource);
        if ($VALUE_resources == '') {
            $VALUE_resources = $resource_name;
        } else {
            $VALUE_resources .= ", $resource_name";
        }
    }
    $p->set_var('VALUE_resources',$VALUE_resources); 

    $VALUE_resources='';
    $sql="SELECT {$_TABLES['users']}.fullname FROM {$_TABLES['users']},{$_TABLES['prj_projPerms']} WHERE ";
    $sql .= "{$_TABLES['prj_projPerms']}.pid=$id AND {$_TABLES['prj_projPerms']}.taskid='0'  AND {$_TABLES['prj_projPerms']}.uid={$_TABLES['users']}.uid ";
    $query = DB_query($sql);
    while (list($resource) = DB_fetchArray($query)) {
        if ($VALUE_resources == '') {
            $VALUE_resources = $resource;
        } else {
            $VALUE_resources .= ",<BR> $resource";
        }
    }
    $p->set_var('project_resources',$VALUE_resources);

    $sql = "SELECT uid, description, updated FROM {$_TABLES['prj_statuslog']} WHERE pid=$id and tid=0 ORDER BY updated ASC";
    $query = DB_query($sql);
    if (DB_numRows($query) > 0) {
        while (list($user, $comment, $date) = DB_fetchArray($query)) {
            $p->set_var('member_name', DB_getItem($_TABLES['users'], 'fullname', "uid=$user"). ':&nbsp;');
            $p->set_var('log_date',strftime("%Y/%m/%d %H:%M", $date));
            if($comment == '') {
                $p->set_var('log_entry', '<br>Edit Task completed, no comment entered');
            } else {
                $p->set_var('log_entry', '<br>'.$comment);
            }
            $p->parse('task_log_entries','tasklog',true);
        }
    } else {
        $p->set_var('log_entry', $strings['noresults']);
        $p->parse('task_log_entries','tasklog');
    }

    $p->parse ('output', 'project');
    return $p->finish ($p->get_var('output'));   
}


function prj_edit_project_icons($pid,$mode='') {
    global $_CONF,$strings;
    ob_start();
    $block = new block();
    $block->form = "taX";
    $block->openForm($_CONF['site_url'] . "/nexproject/index.php");
    $block->openPaletteIcon();
    $block->paletteIcon(0,"add",$strings["add"]);
    $block->paletteIcon(1,"remove",$strings["delete"]);
    if ($mode != 'view') {
        $block->paletteIcon(2,"info",$strings["view"]);
    }
    if ($mode != 'edit') {
        $block->paletteIcon(3,"edit",$strings["edit"]);
    }
    $block->paletteIcon(4,"copy",$strings["copy"]);
    $block->closePaletteIcon();
    $block->openPaletteScript();
    $block->paletteScript(0,"add",$_CONF['site_url'] ."/nexproject/index.php?mode=add","true,true,false",$strings["add"]);     
    $block->paletteScript(1,"remove",$_CONF['site_url'] ."/nexproject/index.php?mode=delete&id=". $pid,"true,true,false",$strings["delete"]);
    if ($mode != 'view') {
        $block->paletteScript(2,"info",$_CONF['site_url'] ."/nexproject/viewproject.php?pid=". $pid,"true,true,false",$strings["view"]);
    }
    if ($mode != 'edit') {
        $block->paletteScript(3,"edit",$_CONF['site_url'] ."/nexproject/index.php?mode=edit&id=". $pid,"true,true,false",$strings["edit"]);
    }
    $block->paletteScript(4,"copy",$_CONF['site_url'] . "/nexproject/index.php?mode=copy&id=". $pid,"true,true,false",$strings["copy"]);
    $block->closePaletteScript(0,$pid);
    echo '</form>';
    $edit_icons = ob_get_contents();
    ob_end_clean();
    return $edit_icons;           
}

?>