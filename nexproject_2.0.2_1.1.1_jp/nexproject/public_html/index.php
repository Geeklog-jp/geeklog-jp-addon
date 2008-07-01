<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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
require_once("../lib-common.php");
require_once($_CONF['path_html'] ."nexproject/includes/library.php");
require_once($_CONF['path_html'] ."nexproject/includes/lib-projects.php");

if($_COOKIE['windowwidth']=='') setcookie('windowwidth',$_PRJCONF['min_graph_width']);

setcookie('screen', 'myprojects');
$_COOKIE['screen'] = 'myprojects';
setcookie('showMonitor','0');
setcookie('showTeamMember','1');
$msg = '';



if (isset($_POST["taskf"])) {
    $taskf = COM_applyFilter($_POST["taskf"]);
    setcookie("taskf", $taskf);
} else {
    $taskf = COM_applyFilter($_COOKIE['taskf']);
}
require_once($_CONF['path_system'] . 'classes/navbar.class.php');
require_once($_CONF['path_html']  . "/nexproject/includes/block.class.php");

/* Load Plugins Language File and create an array of template keys and language text */
$pluginLangPath = $_CONF['path'] . 'plugins/nexproject/language/english.txt';
$pluginLangLines = @file($pluginLangPath);
if ($pluginLangLines === false){
    echo COM_refresh($_CONF['site_url'] . '/index.php?msg=1&plugin=projects');
    exit;
}

foreach($pluginLangLines as $line){
    if (trim($line) == '' ||
        substr($line, 0, 1) == '#') {
        continue;
    }
    $tokens = explode('=', $line);
    $key = 'LANG_' . trim($tokens[0]);
    array_shift($tokens);
    $val = implode('=', $tokens);
    $pluginLangLabels[$key] = trim($val);
}

// Main Code
$mode = COM_applyFilter($_REQUEST['mode']);
$id = COM_applyFilter($_REQUEST['id'],true);       // Project ID

switch ($mode) {

    case 'edit':
        echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );
        $uid = $_USER['uid'];
        $token = getProjectToken($id, $uid, "{$_TABLES['prj_users']}");

        $protoken = prj_getProjectPermissions($id, $uid);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");

        if ($protoken['full'] != 0) {
            $sql = "SELECT p.*, s.description 'statusdesc' FROM {$_TABLES['prj_projects']} p ";
            $sql .= "LEFT JOIN {$_TABLES['prj_statuslog']} s ON p.pid=s.pid WHERE p.pid='$id' order by s.slid desc";
            $result = DB_query($sql);
            $projectrec = DB_fetchArray($result);
            $edit_icons = prj_edit_project_icons($id,'edit');
            $p = new Template($_CONF['path_layout'] . 'nexproject');
            $p->set_file ('project', 'editproject.thtml');
            $p->set_var('breadcrumb_trail',prj_breadcrumbs(0,$id,$strings["edit_project"]));
            $p->set_var($pluginLangLabels);
            $p->set_var('site_url',$_CONF['site_url']);
            $p->set_var('layout_url',$_CONF['layout_url']);
            $p->set_var('mode','save');
            $p->set_var('id',$id);
            $p->set_var('LANG_heading',$pluginLangLabels['LANG_edit_project']);
            $p->set_var('show_resources','none');
            $p->set_var('edit_icons',$edit_icons);
            $p->set_var('linked_content_disabled', ($nexfile) ? '':'disabled="disabled"');
            $p->set_var('discussion_board_disabled', ($forum) ? '':'disabled="disabled"');

            prj_setTemplateVars($p,$projectrec);

            $p->set_var('priority_options',selectBox2($priority,$projectrec['priority_id']));
            $p->set_var('status_options',selectBox2($status,$projectrec['status_id']));
            $p->set_var('progress_options',selectBox2($progress,$projectrec['progress_id']));
            $p->set_var('objective_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_objective'], 0, $projectrec['objective_id']));

            $result1 = DB_query("SELECT location_id FROM {$_TABLES['prj_location']} WHERE pid='$id'");
            $nrows = DB_numRows($result1);
            for($i = 0; $i < $nrows; $i++) {
                $B = DB_fetchArray($result1);
                $selected[$i] = $B['location_id'];
            }
            $p->set_var('location_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_locations'], 0, $selected,'',-1,true));
            $selected=NULL;
            $result1 = DB_query("SELECT department_id FROM {$_TABLES['prj_department']} WHERE pid='$id'");
            $nrows = DB_numRows($result1);
            for($i = 0; $i < $nrows; $i++) {
                $B = DB_fetchArray($result1);
                $selected[$i] = $B['department_id'];
            }
            $p->set_var('department_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_departments'], 0, $selected,'',-1,true));
            $selected=NULL;

            $result1 = DB_query("SELECT category_id FROM {$_TABLES['prj_category']} WHERE pid='$id'");
            $nrows = DB_numRows($result1);
            for($i = 0; $i < $nrows; $i++) {
                $B = DB_fetchArray($result1);
                $selected[$i] = $B['category_id'];
            }
            $p->set_var('category_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_category'], 0, $selected,'',-1,true));
            $selected=NULL;

            $p->set_var('resource_options',COM_optionList( $_TABLES['users'], 'uid,fullname', $selected='', $sortcol=1, 'status = 0 and uid>1' ));

            $result1 = DB_query("SELECT {$_TABLES['users']}.uid FROM {$_TABLES['users']}, {$_TABLES['prj_users']} WHERE {$_TABLES['prj_users']}.pid=$id AND {$_TABLES['users']}.uid={$_TABLES['prj_users']}.uid AND {$_TABLES['prj_users']}.role='o'");
            list($user) = DB_fetchArray($result1);
            $p->set_var('owner_options', COM_optionList($_TABLES['users'], 'uid,username', $user, $sortcol = 1,"uid>1"));

            $p->parse ('output', 'project');
            echo $p->finish ($p->get_var('output'));

            /************************************************/
            //added this area to handle new permissions piece
            /************************************************/
            $pid=$id;
            $retperms=prj_getProjectPermissions($pid,$uid);
            $ownertoken = getProjectToken($pid,$uid,"{$_TABLES['prj_users']}");
            if( $retperms['full'] || SEC_ingroup('root') || $ownertoken!=0){
                $permissionsBlock =new block();
                $permissionsBlock->form = "permissions";
                $permissionsBlock->openForm($_CONF['site_url'] . "/nexproject/viewproject.php?pid={$pid}"."#".$permissionsBlock->form."Anchor");
                $permissionsBlock->headingToggle("Team Permissions");
                $permissionsBlock->openPaletteIcon();
                $permissionsBlock->paletteIcon(0,"edit",$strings["edit"]);
                $permissionsBlock->closePaletteIcon();
                $permissionsBlock->openPaletteScript();
                $permissionsBlock->paletteScript(0,"edit",$_CONF['site_url'] ."/nexproject/prjperms.php?mode=add&pid=". $pid,"true,false,false",$strings["edit"]);
                $permissionsBlock->closePaletteScript(0,0);
                $p = new Template($_CONF['path_layout'] . 'nexproject/');
                $p->set_file (array (
                    'perms' => 'projectPermissionRights.thtml',
                    'permrec' => 'projectPermRecord.thtml'));
                $p->set_var('layout_url',$_CONF['layout_url']);
                $p->set_var($pluginLangLabels);
                prj_displayPerms($p,$pid,0,true,$_COOKIE['permsOrderBy'] . $_COOKIE['prj_ascdesc']);
                $p->parse ('output', 'perms');
                echo $p->finish ($p->get_var('output'));
                $permissionsBlock->closeToggle();
                $permissionsBlock->closeFormResults();
                echo '<p />';
            }

        } else {
            if ($id > 0) {
                $result = DB_query("SELECT * FROM {$_TABLES['prj_projects']} WHERE pid=$id");
                $A = DB_fetchArray($result);
            }
            echo prj_breadcrumbs(0,$id,$strings["edit_project"]);
            $block = new block();

            $msg = 'permissiondenied';
            require_once("includes/messages.php");
            $block->messagebox($msgLabel);
            //$block->heading($strings["edit_project"]);
            $block->openContent();
            $block->contentRow("", "<input type=\"button\" name=\"goback\" value=\"" . $strings["back"] . "\" onClick=\"history.back();\">");
            $block->closeContent();
            $block->closeForm();

        }
        echo COM_siteFooter();
        exit;
        break;

    case 'save':
        // Filter Incoming Variables and make them global
        // Text Variables which may contain quote's or other special characters
        $_CLEAN = array();
        $mytextvars = array('name','description','keywords','changelog_entry');
        $_CLEAN = array_merge($_CLEAN,ppGetData($mytextvars,false,'POST','text'));
        // Integer only Variables
        $myintvars = array('priority','status','progress','objective','percent','owner');
        $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'POST','int'));
        // Basic Text Variables which should not contain quote's or other special characters
        $myvars = array('start_date','estimated_end_date','actual_end_date','notification','document','forum','private','template');
        $_CLEAN = array_merge($_CLEAN,ppGetData($myvars,false,'POST'));
        $myvars = array('location','department','category');
        $_CLEAN = array_merge($_CLEAN,ppGetData($myvars,false,'POST','array'));

        $_CLEAN['document'] = ($_CLEAN['document'] == 'Y') ? 'Y':'N';
        $_CLEAN['forum'] = ($_CLEAN['forum'] == 'Y') ? 'Y':'N';

        $uid = $_USER['uid'];
        $percent = ($percent < 0) ? 0 : ($percent > 100) ? 100 : $percent;

        $token = getProjectToken($id, $uid, "{$_TABLES['prj_users']}");
        //we have to determine if the name of the project has changed.
        //if it has, we have to change the name of the forum and the nexfile entries
        $currentName=DB_getItem($_TABLES['prj_projects'],"name","pid=$id");
        $forumflag=DB_getItem($_TABLES['prj_projects'],"is_using_forum_flag ","pid=$id");
        $documentflag=DB_getItem($_TABLES['prj_projects'],"is_using_docmgmt_flag ","pid=$id");
        $privateflag=DB_getItem($_TABLES['prj_projects'],"is_private_project_flag","pid=$id");

        if ($token != 0) {
            $lastupdated = pm_getdate();
            $startdate = pm_convertdate($_CLEAN['start_date']);
            $actualdate = pm_convertdate($_CLEAN['actual_end_date']);
            $estimateddate = pm_convertdate($_CLEAN['estimated_end_date']);

            if($actualdate<$startdate){
                $actualdate=$startdate+604800 ; //add 1 week to the start date...
            }
            if($estimateddate<$startdate){
                $estimateddate=$startdate+604800 ; //add 1 week to the start date...
            }

            $sql  = "UPDATE {$_TABLES['prj_projects']} SET name='{$_CLEAN['name']}', ";
            $sql .= "description='{$_CLEAN['description']}', is_private_project_flag='{$_CLEAN['private']}', ";
            $sql .= "is_template_project_flag='{$_CLEAN['template']}', keywords='{$_CLEAN['keywords']}', ";
            $sql .= "priority_id={$_CLEAN['priority']}, status_id={$_CLEAN['status']}, ";
            $sql .= "objective_id={$_CLEAN['objective']}, progress_id={$_CLEAN['progress']}, ";
            $sql .= "start_date=$startdate, estimated_end_date=$estimateddate, percent_completion={$_CLEAN['percent']}, ";
            $sql .= "last_updated_date=$lastupdated, actual_end_date=$actualdate, ";
            $sql .= "notification_enabled_flag='{$_CLEAN['notification']}' WHERE pid=$id";
            DB_query($sql);
            prj_statuslog ($id, $uid, '', $_CLEAN['changelog_entry'], $lastupdated, "{$_TABLES['prj_statuslog']}");
            // Instead of trying to determine what items have changed - delete all and create new ones
            if (is_array($_CLEAN['department'])) {
                DB_query("DELETE FROM {$_TABLES['prj_department']} WHERE pid=$id");
                for($i = 0; $i < count($_CLEAN['department']); $i++) {
                    DB_query("INSERT INTO {$_TABLES['prj_department']} (pid, department_id) VALUES ($id, {$_CLEAN['department'][$i]})");
                }
            }
            if (is_array($_CLEAN['location'])) {
                DB_query("DELETE FROM {$_TABLES['prj_location']} WHERE pid=$id");
                for($i = 0; $i < count($_CLEAN['location']); $i++) {
                    DB_query("INSERT INTO {$_TABLES['prj_location']} (pid, location_id) VALUES ($id, {$_CLEAN['location'][$i]})");
                }
            }
            if (is_array($_CLEAN['category'])) {
                DB_query("DELETE FROM {$_TABLES['prj_category']} WHERE pid=$id");
                for($i = 0; $i < count($_CLEAN['category']); $i++) {
                    DB_query("INSERT INTO {$_TABLES['prj_category']} (pid, category_id) VALUES ($id, {$_CLEAN['category'][$i]})");
                }
            }
            // Update Project Owner
            if ($_CLEAN['owner'] > 0) {
                $currentowner = DB_getItem ($_TABLES['prj_users'], 'uid', "pid = $id AND role='o'");
                if ($currentowner > 0) {
                    DB_query("UPDATE {$_TABLES['prj_users']} SET role='r' WHERE pid=$id AND uid=$currentowner");
                }
                $result = DB_query("SELECT uid FROM {$_TABLES['prj_users']} WHERE pid=$id and uid={$_CLEAN['owner']}");
                $nrows = DB_numRows($result);
                if ($nrows == 0) {
                    DB_query("INSERT INTO {$_TABLES['prj_users']} (pid, uid, role) VALUES ($id, {$_CLEAN['owner']}, 'o')");
                } else {
                    DB_query("UPDATE {$_TABLES['prj_users']} SET role='o' WHERE pid=$id AND uid={$_CLEAN['owner']}");
                }
                $cid = DB_getItem ($_TABLES['prj_projects'], 'cid', "pid=$id");
                if (DB_getItem($_TABLES['prj_projects'], "is_private_project_flag", "pid=$id") == 'Y' AND $private == 'Y') {
                    DB_query("DELETE FROM {$_TABLES['fm_access']} WHERE catid=$cid and grp_id=2");
                }
            }
            //now if the name has been changed......
            if(strcmp($currentName,$_CLEAN['name'])!=0){ //names are not alike..
                //update the forum name
                $fid=DB_getItem($_TABLES['prj_projects'],"fid","pid={$id}");
                $cid=DB_getItem($_TABLES['prj_projects'],"cid","pid={$id}");
                $sql="UPDATE {$_TABLES['gf_forums']} set forum_name='{$_CLEAN['name']}' where forum_id={$fid}";
                DB_query($sql);
                //now update the nexfile name
                $sql="UPDATE {$_TABLES['fm_category']} set name='{$_CLEAN['name']}' where cid={$cid}";
                DB_query($sql);
            }
            //now determine if the forum, file or private flag has been switched
            //'document','forum','private'
            //forum turned on?
            if(strcmp($forumflag,$_CLEAN['forum'])!=0 && $_CLEAN['forum']=='Y'){
                   //generate a forum.
                    $forumid = forum_addForum($_CLEAN['name'],$_PRJCONF['forum_parent'],$_CLEAN['description'],0);
                    DB_query("UPDATE {$_TABLES['prj_projects']} SET fid=$forumid, is_using_forum_flag ='Y' WHERE pid=$id");
                    $logentry = "Project ID-$id, FORUM created ID: $forumid";
                    prg_updateAuditLog($logentry);
            }

            if(strcmp($documentflag,$_CLEAN['document'])!=0 && $_CLEAN['document']=='Y'){
                //generate a file cateogory.
                $retchk = fm_createCategory($_PRJCONF['nexfile_parent'],$_CLEAN['name'],$_CLEAN['description'],true);
                $catid = $retchk[0];
                $retmsg = $retchk[1];
                DB_query("UPDATE {$_TABLES['prj_projects']} SET cid='$catid', is_using_docmgmt_flag='Y' WHERE pid='$id'");
                $logentry = "Project ID-id, nexfile Folder created ID: $catid. Msg: $retmsg";
                prg_updateAuditLog($logentry);

                $uid = $_USER['uid'];
                DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$catid','$uid','1','1','1','1','1','1')");
                if ($_CLEAN['private'] == 'N') {
                    DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,grp_id, view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$catid','2','1','0','0','0','0','0')");
                }


            }




        }// end if(token!=0)
        if ($notification == 'on') {
            prj_sendNotification($id, '', $action = 1);
        }
        $logentry = "Project ID-" . $id . " was editted";
        prg_updateAuditLog($logentry);
        echo COM_refresh($_CONF['site_url'] . "/nexproject/viewproject.php?pid=$id&msg=editProject");
        exit;
        break;

    case 'delete':
        echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );
        echo prj_breadcrumbs(0,$id,$strings["delete_project"]);
        $token = '';
        $uid = $_USER['uid'];

        $protoken = prj_getProjectPermissions($id, $uid);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        if ($protoken['full'] != 0) {
            $block = new block();
            $msg = 'permissiongranted';
            if ($msg != "") {
                require_once("includes/messages.php");
                $block->messagebox($msgLabel);
            }
            $result = DB_query("SELECT pid, name FROM {$_TABLES['prj_projects']} WHERE pid=$id");
            list($pid,$projectName) = DB_fetchArray($result);
            $block->form = "saP";
            $block->openForm("../nexproject/index.php?mode=erase" . "#" . $block1->form . "Anchor");
            $block->heading($strings["delete_project"]);
            $block->openContent();
            $block->contentTitle($strings["delete_following"]);
            $block->contentRow("#" . $pid, $projectName . "<input type=hidden name=id value=\"$pid\">");
            $block->contentRow("", "<input type=\"submit\" name=\"delete\" value=\"" . $strings["delete"] . "\"> <input type=\"button\" name=\"cancel\" value=\"" . $strings["cancel"] . "\" onClick=\"history.back();\">");
            $block->closeContent();
            $block->closeForm();
        } else {
            $block = new block();
            $msg = 'permissiondenied';
            require_once("includes/messages.php");
            $block->messagebox($msgLabel);
            $block->heading($strings["delete_project"]);
            $block->openContent();
            $block->contentRow("", "<input type=\"button\" name=\"goback\" value=\"" . $strings["back"] . "\" onClick=\"history.back();\">");
            $block->closeContent();
            $block->closeForm();
        }
        echo COM_siteFooter();
        exit;
        break;

    case 'copy':
        // Filter Incoming Variables and make them global
        // Text Variables which may contain quote's or other special characters
        $_CLEAN = array();
        $myintvars = array('id');
        if (isset ($_POST['pid'])) {
            $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'POST','int'));
        } elseif (isset ($_GET['id'])) {
           $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'GET','int'));
        } else {
            $id = '';
        }

        $uid = $_USER['uid'];
        $result = DB_query("SELECT create_date, last_updated_date, start_date, estimated_end_date, actual_end_date,planned_end_date, name, description, keywords, is_using_docmgmt_flag, is_using_forum_flag, is_private_project_flag, is_template_project_flag FROM {$_TABLES['prj_projects']} WHERE pid='$id'");
        $comptListTasks = DB_numrows($result);
        if ($comptListTasks != "0") {
            $createdate = pm_getdate();
            list($create_date, $last_updated_date, $start_date, $estimated_end_date, $actual_end_date,$planned_end_date,$name, $description, $keywords, $is_using_docmgmt_flag, $is_using_forum_flag, $is_private_project_flag, $is_template_project_flag) = DB_fetchArray($result);
            $description = prj_preparefordb($description);
            $name = prj_preparefordb($name);
            $keywords = prj_preparefordb($keywords);
            $name = "Copy of " . $name;
            $sql="INSERT INTO {$_TABLES['prj_projects']} ";
            $sql .="(name, description, keywords, create_date, last_updated_date, start_date, estimated_end_date, actual_end_date, ";
            $sql .="planned_end_date, progress_id, status_id, priority_id, is_using_docmgmt_flag, is_using_forum_flag, is_private_project_flag, ";
            $sql .="is_template_project_flag)  VALUES (";
            $sql .="'$name', '$description', '$keywords', '$create_date', '$last_updated_date', '$start_date', '$estimated_end_date', '$actual_end_date','$planned_end_date', 0, 0, 0, '$is_using_docmgmt_flag', ";
            $sql .=" '$is_using_forum_flag', '$is_private_project_flag', '$is_template_project_flag' ";
            $sql .=") ";

            DB_query($sql);
            $lastid = DB_insertId();
            $uid = $_USER['uid'];
            DB_query("INSERT INTO {$_TABLES['prj_users']} (pid, uid,role) VALUES ($lastid, $uid, 'o')");

            //copy all project permissions.. do the tasks later!
            $sql="insert into {$_TABLES['prj_projPerms']} (pid, taskid, uid, gid, viewread, writechange, fullaccess, seedetails) ";
            $sql .="(select '{$lastid}','0', uid, gid, viewread, writechange, fullaccess, seedetails  from {$_TABLES['prj_projPerms']} ";
            $sql .="where taskid='0' and pid='{$id}')";
            DB_query($sql);

            // Copy the Project Departments
            $results1 = DB_query("SELECT department_id FROM {$_TABLES['prj_department']} WHERE pid=$id");
            $nrows = DB_numRows($results1);
            if ($nrows != "0") {
                for($i = 0; $i < $nrows; $i++) {
                    list($department_id) = DB_fetchArray($results1);
                    DB_query("INSERT INTO {$_TABLES['prj_department']} (pid, department_id) VALUES ($lastid, $department_id)");
                }
            }
            // Copy the Project Locations
            $results1 = DB_query("SELECT location_id FROM {$_TABLES['prj_location']} WHERE pid=$id");
            $nrows = DB_numRows($results1);
            if ($nrows != "0") {
                for($i = 0; $i < $nrows; $i++) {
                    list($location_id) = DB_fetchArray($results1);
                    DB_query("INSERT INTO {$_TABLES['prj_location']} (pid, location_id) VALUES ($lastid, $location_id)");
                }
            }
            // Copy the Project Categories
            $results1 = DB_query("SELECT category_id FROM {$_TABLES['prj_category']} WHERE pid=$id");
            $nrows = DB_numRows($results1);
            if ($nrows != "0") {
                for($i = 0; $i < $nrows; $i++) {
                    list($category_id) = DB_fetchArray($results1);
                    DB_query("INSERT INTO {$_TABLES['prj_category']} (pid, category_id) VALUES ($lastid, $category_id)");
                }
            }
            // Create Document Repository if Necessary
            if ($is_using_docmgmt_flag == 'Y') {
                $catresult = fm_createCategory($_PRJCONF['nexfile_parent'], $name, $description,true);
                if ($catresult['0'] > 0) {
                    $newcid = $catresult['0'];
                    $uid = $_USER['uid'];
                    DB_query("UPDATE {$_TABLES['prj_projects']} SET cid=$newcid WHERE pid=$lastid");
                    DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$newcid','$uid','1','1','1','1','1','1')");
                    if ($is_private_project_flag == 'N') {
                        DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,grp_id, view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$newcid','2','1','0','0','0','0','0')");
                    }

                } else {
                    $errmsg = $catresult['1'];
                }
            }
            // Create Discussion Board
            if ($is_using_forum_flag == 'Y') {
                DB_query("INSERT INTO {$_TABLES['gf_forums']} (forum_order,forum_name,forum_dscp,forum_cat,grp_id) VALUES ('0','$name','$description','{$_PRJCONF['forum_parent']}','2')");
                $newfid = DB_insertId();
                $uid = $_USER['uid'];
                DB_query("UPDATE {$_TABLES['prj_projects']} SET fid=$newfid WHERE pid=$lastid");
                $modquery = DB_query("SELECT * FROM {$_TABLES['gf_moderators']} WHERE mod_username='{$_USER['username']}' AND mod_forum='$forumid'");
                if (DB_numrows($modquery) < 1) {
                    DB_query("INSERT INTO {$_TABLES['gf_moderators']} (mod_username,mod_forum,mod_delete,mod_ban,mod_edit,mod_move,mod_stick) VALUES ('$_USER[username]', '$forumid','1','1','1','1','1')");
                }
            }
            // Copy the Project Resources
            $results1 = DB_query("SELECT uid FROM {$_TABLES['prj_users']} WHERE pid=$id");
            $nrows = DB_numRows($results1);
            if ($nrows != "0") {
                for($i = 0; $i < $nrows; $i++) {
                    list($adduid) = DB_fetchArray($results1);
                    $currentuid = $_USER['uid'];
                    if ($adduid != $currentuid) {
                        DB_query("INSERT INTO {$_TABLES['prj_users']} (pid, uid, role) VALUES ($lastid, $adduid, 'r')");
                        if ($is_using_docmgmt_flag == 'Y') {
                            DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$newcid','$adduid','1','1','0','0','0','0')");
                        }
                    }
                }
            }
            // Copy the Tasks
          prj_copyProjectTasks($id, $lastid,0);

        }//end overall if
        $msg = 'copyProject';

        break;

    case 'erase':
        $token = '';
        $uid = $_USER['uid'];

        $protoken = prj_getProjectPermissions($id, $uid);
        $ownertoken= getTaskToken($id, $uid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        if ($protoken['full'] != 0) {

            $result = DB_query("SELECT cid, is_using_docmgmt_flag, is_using_forum_flag, fid FROM {$_TABLES['prj_projects']} WHERE pid='$id'");
            list($cid, $document, $discussion, $fid) = DB_fetchArray($result);
            DB_query("DELETE FROM {$_TABLES['prj_projects']} WHERE pid='$id'");
            $result1 = DB_query("SELECT tid FROM {$_TABLES['prj_tasks']} WHERE {$_TABLES['prj_tasks']}.pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_users']} WHERE pid=$id and uid='$uid'");
            DB_query("DELETE FROM {$_TABLES['prj_department']} WHERE pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_category']} WHERE pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_location']} WHERE pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_users']} WHERE pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_tasks']} WHERE pid='$id'");
            DB_query("DELETE FROM {$_TABLES['prj_projPerms']} WHERE pid='$id'");
            $nrows = DB_numRows($result1);
            if ($nrows != "0") {
                for ($i = 0; $i < $nrows; $i++) {
                    list($tid) = DB_fetchArray($result1);
                    DB_query("DELETE FROM {$_TABLES['prj_task_users']} WHERE tid='$tid'");
                }
            }
            if ($document == 'Y') {
                // Delete the nexfile stuff
                $results = DB_query("SELECT fid FROM {$_TABLES['fm_files']} WHERE cid='$cid'");
                DB_query("DELETE FROM {$_TABLES['fm_access']} WHERE catid='$cid'");
                DB_query("DELETE FROM {$_TABLES['fm_category']} WHERE cid='$cid'");
                DB_query("DELETE FROM {$_TABLES['fm_files']} WHERE cid='$cid'");
                DB_query("DELETE FROM {$_TABLES['fm_notify']} WHERE cid='$cid'");
                $nrows = DB_numRows($results);
                if ($nrows != "0") {
                    for ($i = 0; $i < $nrows; $i++) {
                        list($fid) = DB_fetchArray($results);
                        DB_query("DELETE FROM {$_TABLES['fm_submissions']} WHERE fid='$fid'");
                        DB_query("DELETE FROM {$_TABLES['fm_detail']} WHERE fid='$fid'");
                        DB_query("DELETE FROM {$_TABLES['fm_versions']} WHERE fid='$fid'");
                    }
                }
            }

            if ($discussion == 'Y') {
                // Delete the forum stuff
                DB_query("DELETE FROM {$_TABLES['gf_forums']} WHERE forum_id='$fid'");
                DB_query("DELETE FROM {$_TABLES['gf_topic']} WHERE forum='$fid'");
                DB_query("DELETE FROM {$_TABLES['gf_moderators']} WHERE mod_forum='$fid'");
            }

            $logentry = "Project ID-" . $id . " was deleted";
            prg_updateAuditLog($logentry);

            $msg = 'removeProject';
        }
        break;

    case 'create':
        // Filter Incoming Variables and make them global
        // Text Variables which may contain quote's or other special characters
        $_CLEAN = array();
        $mytextvars = array('name','description','keywords');
        $_CLEAN = array_merge($_CLEAN,ppGetData($mytextvars,false,'POST','text'));

        // Integer only Variables
        $myintvars = array('priority','status','progress','objective','percent','location','department','category','resources','department');
        $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'POST','int'));

        // Basic Text Variables which should not contain quote's or other special characters
        $myvars = array('start_date','estimated_end_date','actual_end_date','notification','document','forum','private','template');
        $_CLEAN = array_merge($_CLEAN,ppGetData($myvars,false,'POST'));

        $uid = $_USER['uid'];
        $_CLEAN['percent'] = ($_CLEAN['percent'] < 0) ? 0 : ($_CLEAN['percent'] > 100) ? 100 : $_CLEAN['percent'];
        $_CLEAN['createdate'] = pm_getdate();
        $_CLEAN['startdate'] = pm_convertdate($_CLEAN['start_date']);
        $_CLEAN['estimateddate'] = pm_convertdate($_CLEAN['estimated_end_date']);
        if($_CLEAN['actual_end_date']==''){
            $_CLEAN['actual_end_date'] = $_CLEAN['estimated_end_date'];
        }else{
            $_CLEAN['actual_end_date'] = pm_convertdate($_CLEAN['actual_end_date']);
        }

        if($_CLEAN['actual_end_date']<$_CLEAN['startdate']){
                $_CLEAN['actual_end_date']=$_CLEAN['startdate']+604800 ; //add 1 week to the start date...
            }
        if($_CLEAN['estimateddate']<$_CLEAN['startdate']){
            $_CLEAN['estimateddate']=$_CLEAN['startdate']+604800 ; //add 1 week to the start date...
        }

        $_CLEAN['document'] = ($_CLEAN['document'] == 'Y') ? 'Y':'N';
        $_CLEAN['forum'] = ($_CLEAN['forum'] == 'Y') ? 'Y':'N';

        //randy's comments:
        //we're inserting a new blank project with the appropriate Parent ID
        //in order to retrieve back the next useful LHS and RHS values.
        //If there is a parent, then the LHS and RHS need to have valid values
        //that fall between the parent's LHS and RHS values.

        $lastInsert=prj_insertProject($A['parent_id']);
        $sql="SELECT lhs, rhs FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
        $res=DB_query($sql);
        list($newLHS, $newRHS)=DB_fetchArray($res);
        $sql="DELETE FROM {$_TABLES['prj_projects']} WHERE pid='{$lastInsert}'";
        DB_query($sql);


        // Setup Insert SQL Statement
        $fields  = 'name, description, is_using_docmgmt_flag, is_using_forum_flag, is_private_project_flag,';
        $fields .= 'is_template_project_flag, keywords, priority_id, status_id, objective_id,';
        $fields .= 'progress_id, create_date, start_date, estimated_end_date, planned_end_date,';
        $fields .= 'actual_end_date, percent_completion, last_updated_date, notification_enabled_flag, lhs, rhs';
        $values  = "'{$_CLEAN['name']}','{$_CLEAN['description']}','{$_CLEAN['document']}','{$_CLEAN['forum']}',";
        $values .= "'{$_CLEAN['private']}','{$_CLEAN['template']}','{$_CLEAN['keywords']}',{$_CLEAN['priority']},";
        $values .= "{$_CLEAN['status']},{$_CLEAN['objective']},{$_CLEAN['progress']},{$_CLEAN['createdate']},";
        $values .= "{$_CLEAN['startdate']},{$_CLEAN['estimateddate']},{$_CLEAN['estimateddate']},{$_CLEAN['actual_end_date']},";
        $values .= "'{$_CLEAN['percent']}',{$_CLEAN['createdate']},'{$_CLEAN['notification']}', '{$newLHS}','{$newRHS}'";
        DB_query("INSERT INTO {$_TABLES['prj_projects']} ($fields) VALUES ($values) ");
        $lastid = DB_insertId();
        DB_query("INSERT INTO {$_TABLES['prj_users']} (pid, uid,role) VALUES ($lastid, $uid, 'o')");
        if (is_array($_CLEAN['department'])) {
            for($i = 0; $i < count($_CLEAN['department']); $i++) {
                DB_query("INSERT INTO {$_TABLES['prj_department']} (pid, department_id) VALUES ($lastid, {$_CLEAN['department'][$i]})");
            }
        }
        if (is_array($_CLEAN['location'])) {
            for($i = 0; $i < count($_CLEAN['location']); $i++) {
                DB_query("INSERT INTO {$_TABLES['prj_location']} (pid, location_id) VALUES ($lastid, {$_CLEAN['location'][$i]})");
            }
        }

        if (is_array($_CLEAN['resources'])) {
            for($i = 0; $i < count($_CLEAN['resources']); $i++) {
                if ($_CLEAN['resources'][$i] != $_USER['uid']) {
                    $sql  ="insert into {$_TABLES['prj_projPerms']} (pid, taskID, uid, gid, viewRead, writeChange, fullAccess, seeDetails) values ";
                    $sql .="(";
                    $sql .="'$lastid',";
                    $sql .="'0',";
                    $sql .="'{$_CLEAN['resources'][$i]}',";
                    $sql .="'0',";
                    $sql .="'0',";      //viewread
                    $sql .="'1',";      //writechange
                    $sql .="'0',";      //fullaccess
                    $sql .="'0'";       //seedetails
                    $sql .=")";
                    DB_query($sql);
                }
            }
        }
        if (is_array($_CLEAN['category'])) {
            for($i = 0; $i < count($_CLEAN['category']); $i++) {
                DB_query("INSERT INTO {$_TABLES['prj_category']} (pid, category_id) VALUES ($lastid, {$_CLEAN['category'][$i]})");
            }
        }

        if ($_CLEAN['document'] == 'Y') {
            $retchk = fm_createCategory($_PRJCONF['nexfile_parent'],$_CLEAN['name'],$_CLEAN['description'],true);
            $catid = $retchk[0];
            $retmsg = $retchk[1];
            DB_query("UPDATE {$_TABLES['prj_projects']} SET cid='$catid' WHERE pid='$lastid'");
            $logentry = "Project ID-$lastid, nexfile Folder created ID: $catid. Msg: $retmsg";
            prg_updateAuditLog($logentry);

            $uid = $_USER['uid'];
            DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,uid,view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$catid','$uid','1','1','1','1','1','1')");
            if ($_POST['private'] == 'N') {
                DB_query("INSERT INTO {$_TABLES['fm_access']} (catid,grp_id, view, upload, upload_direct, upload_ver, approval, admin) VALUES ('$catid','2','1','0','0','0','0','0')");
            }
        }

        if ($_CLEAN['forum'] == 'Y') {
            $forumid = forum_addForum($_CLEAN['name'],$_PRJCONF['forum_parent'],$_CLEAN['description'],0);
            DB_query("UPDATE {$_TABLES['prj_projects']} SET fid=$forumid WHERE pid=$lastid");
            $logentry = "Project ID-$lastid, FORUM created ID: $forumid";
            prg_updateAuditLog($logentry);
        }

        /*********************************************/
        //create new project default permissions here
        //whoever created it, gets full perms.
        /*********************************************/

        $sql  ="insert into {$_TABLES['prj_projPerms']} (pid, taskID, uid, gid, viewRead, writeChange, fullAccess, seeDetails) values ";
        $sql .="(";
        $sql .="'$lastid',";
        $sql .="'0',";
        $sql .="'{$_USER['uid']}',";
        $sql .="'0',";
        $sql .="'1',";      //viewread
        $sql .="'1',";      //writechange
        $sql .="'1',";      //fullaccess
        $sql .="'1'";       //seedetails
        $sql .=")";
        DB_query($sql);

        prj_sendNotification($lastid, '', $action = 2);
        $msg = "createProject";
        $logentry = "Project ID-" . $lastid . " was created";
        prg_updateAuditLog($logentry);
        break;

    case 'add':
        echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );
        $test = COM_optionList( $_TABLES['users'], 'uid,fullname', $selected='', $sortcol=1, 'status = 0 and uid>1' );
        $p = new Template($_CONF['path_layout'] . 'nexproject');
        $p->set_file ('project', 'editproject.thtml');
        $p->set_var('breadcrumb_trail',prj_breadcrumbs(0,0,$strings["add_project"]));
        $p->set_var($pluginLangLabels);     // Set template variable for all the language variable keys
        $p->set_var('site_url',$_CONF['site_url']);
        $p->set_var('layout_url',$_CONF['layout_url']);
        $p->set_var('mode','create');
        $p->set_var('show_owner','none');
        $p->set_var('show_changelog','none');
        $p->set_var('LANG_heading',$pluginLangLabels['LANG_add_project']);
        $p->set_var('priority_options',selectBox2($priority));
        $p->set_var('status_options',selectBox2($status));
        $p->set_var('progress_options',selectBox2($progress));
        $nexfile = prj_nexFileExists();
        $forum = prj_forumExists();
        $p->set_var('linked_content_disabled', ($nexfile) ? '':'disabled="disabled"');
        $p->set_var('discussion_board_disabled', ($forum) ? '':'disabled="disabled"');

        $p->set_var('objective_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_objective']));
        $p->set_var('location_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_locations'],0,'','',-1,true));
        $p->set_var('department_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_departments'],0,'','',-1,true));
        $p->set_var('category_options',nexlistOptionList( 'options', '',$_PRJCONF['nexlist_category'],0,'','',-1,true));
        $p->set_var('resource_options',COM_optionList( $_TABLES['users'], 'uid,fullname', $selected='', $sortcol=1,"uid>1" ));

        $p->set_var('VALUE_notification_enabled_flag','CHECKED');
        if ($nexfile) {
            $p->set_var('VALUE_is_using_docmgmt_flag_on', 'CHECKED');
            $p->set_var('VALUE_is_using_docmgmt_flag_off', '');
        }
        else {
            $p->set_var('VALUE_is_using_docmgmt_flag_on', '');
            $p->set_var('VALUE_is_using_docmgmt_flag_off', 'CHECKED');
        }
        if ($forum) {
            $p->set_var('VALUE_is_using_forum_flag_on', 'CHECKED');
            $p->set_var('VALUE_is_using_forum_flag_off', '');
        }
        else {
            $p->set_var('VALUE_is_using_forum_flag_on', '');
            $p->set_var('VALUE_is_using_forum_flag_off', 'CHECKED');
        }

        $p->set_var('VALUE_percent_completion','0');
        $p->set_var('VALUE_is_private_project_flag_off','CHECKED');
        $p->set_var('VALUE_is_template_project_flag_off','CHECKED');
        $p->set_var('VALUE_start_date',date("Y/m/d"));
        $p->set_var('VALUE_estimated_end_date',date("Y/m/d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"))));

        $p->parse ('output', 'project');
        echo $p->finish ($p->get_var('output'));
        echo COM_siteFooter();
        exit;
        break;

    case 'moveprojectlft':
        prj_moveProjectLeft($id);
        break;

    case 'moveprojectrht':
        prj_moveProjectRight($id);
        break;

    case 'moveprojectup':
        prj_moveProjectUp($id);
        break;

    case 'moveprojectdn':
        prj_moveProjectDown($id);
        break;

    default:

}

/* Show only projects and tasks for this user */
echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );
echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr style="vertical-align:top"><td>';
$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink($_CONF['site_url'] . "/nexproject/index.php?", $strings["home"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink($_CONF['site_url'] . "/nexproject/projects.php?", $strings["projects"], in));
$blockPage->closeBreadcrumbs();


echo '</td><td style="text-align:right;padding-right:15px;"></td></tr></table>';

if (!isset($_USER['uid']) OR $_USER['uid'] == "") {
        $uid = 1;
    } else {
        $uid = $_USER['uid'];
    }

$blockPage->bornesNumber = "1";

prj_displayMyProjects($blockPage);



prj_displayMyProjectTasks($blockPage);

//gantt chart display area starts here
$blockPage->bornesNumber = "4";
$blockg = new block();
$blockg->form = "gaP";

$headingTitle = 'Gantt Chart';
$headingStatusArea = '<span id="ajaxstatus_gantt" class="pluginInfo" style="margin-left:25px;display:none">&nbsp;</span>';
$blockg->headingToggle( $headingTitle,$headingStatusArea );

if(isset($_COOKIE['gdate1'])){
    $gdate1 = $_COOKIE['gdate1'];
}
else{
    if (isset($_GET['gdate1']) AND $_GET['gdate1'] != '') {
        $gdate1 = $_GET['gdate1'];
    } elseif (isset($_POST['gdate1']) AND $_POST['gdate1'] != '') {
        $gdate1 = $_POST['gdate1'];
    } else {
        $gdate1 = strftime('%Y/%m/%d',strtotime('-2 weeks'));
    }
}

if(isset($_COOKIE['gdate2'])) {
    $gdate2 = $_COOKIE['gdate2'];
} else {
    if (isset($_POST['gdate2']) AND $_POST['gdate2'] != '') {
        $gdate2 = $_POST['gdate2'];
    } elseif (isset($_GET['gdate2']) AND $_GET['gdate2'] != '') {
        $gdate2 = $_GET['gdate2'];
    } else {
        $gdate2 = strftime('%Y/%m/%d',strtotime('+2 weeks'));
    }
}

$sql = "SELECT min(start_date) as mindate, max( estimated_end_date ) as maxdate1 , ";
$sql .= "max( planned_end_date ) as maxdate2 , max( actual_end_date ) as maxdate3 ";
$sql .= "FROM {$_TABLES['prj_tasks']} WHERE pid='$pid' and YEAR(FROM_UNIXTIME(start_date)) > 1969";

$qdates = DB_query($sql);
list ($mindate,$maxdate1,$maxdate2,$maxdate3) = DB_fetchArray($qdates);

$maxdate1 = ($maxdate1 < 0) ? 0:$maxdate1;
$maxdate2 = ($maxdate2 < 0) ? 0:$maxdate2;
$maxdate3 = ($maxdate3 < 0) ? 0:$maxdate3;
$maxdate = max($maxdate1,$maxdate2,$maxdate3);

/* Convert to a string */
$str_mindate = strftime('%Y/%m/%d',$mindate);
$str_maxdate = strftime('%Y/%m/%d',$maxdate);
if($_COOKIE['STFEP']=='true'){
    $checked=" checked=true ";
}else{
    $checked='';
}


$p = new Template($_CONF['path_layout'] . 'nexproject');
$p->set_file ('ganttheader', 'ganttheaderprojects.thtml');
$p->set_var('siteurl',$_CONF['site_url']);
$p->set_var('gdate1',$gdate1);
$p->set_var('gdate2',$gdate2);
$p->set_var('windowwidth',$_COOKIE['windowwidth']);
$p->set_var('mingraphwidth',$_PRJCONF['min_graph_width']);
$p->set_var('checked',$checked);
$p->parse ('output', 'ganttheader');
echo $p->finish ($p->get_var('output'));

echo '<div id="ganttChartDIV" style="padding-bottom:20px;">';
include "projects_gantt.php";
echo '</div>';

$blockg->closeToggle();
$blockg->closeForm();

$p = new Template($_CONF['path_layout'] . 'nexproject/javascript');
$p->set_file ('projectajax', 'projectajax.thtml');
$p->parse ('output', 'projectajax');
echo $p->finish ($p->get_var('output'));

$p = new Template($_CONF['path_layout'] . 'nexproject/javascript');
$p->set_file ('contextmenu', 'projectblock_contextmenu.thtml');
$p->set_var('site_url',$_CONF['site_url']);
$p->set_var('action_url',$_CONF['site_url'] . '/nexproject/index.php');
$p->set_var('imgset',$_CONF['layout_url'] . '/nexproject/images');
$actionurl = $_CONF['site_url'] . '/nexproject/index.php';
$imgset = $_CONF['layout_url'] . '/nexproject/images';
$p->parse ('output', 'contextmenu');
echo $p->finish ($p->get_var('output'));


echo COM_siteFooter();

?>