<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | messages.php                                                              |
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
switch ($msg) {

    case permissiondenied:
    $msgLabel = $strings["no_permissions"];
    break;

    case removeTask:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["removeTask"];
    break;

    case removeTasks:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["removeTasks"];
    break;

    case noremoveTask:
    $msgLabel = "<b>".$strings["warning"]."</b> : ".$strings["noremoveTask"];
    break;

    case editTask:
    $msgLabel = "<b>".$strings["success"]."</b> : The edits to tasks \"".$_CLEAN['name']."\" were successfully saved."; break;

    case createTask:
    $msgLabel = "<b>".$strings["success"]."</b> : The task \"".$newrec['name']."\" was successfully created."; break;

    case copyTask:
    $msgLabel = "<b>".$strings["success"]."</b> : The task \"".$newrec['name']."\" were successfully copied."; break;

    case logout:
    $msgLabel = $strings["success_logout"];
    break;

    case noteOwner:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["note_owner"];
    break;

    case taskOwner:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["task_owner"];
    break;

    case projectOwner:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["project_owner"];
    break;

    case email_pwd:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["email_pwd"];
    break;

    case deleteTopic:
    $msgLabel = "<b>".$strings["success"]."</b> : $num of $num discussions were deleted.";
    break;

    case closeTopic:
    $msgLabel = "<b>".$strings["success"]."</b> : $num of $num discussions were closed.";
    break;

    case createProject:
    $msgLabel = "<b>".$strings["success"]."</b> : The project \"".$name."\" was successfully created."; break;

    case copyProject:
    $msgLabel = "<b>".$strings["success"]."</b> : The project was successfully copied."; break;

    case editProject:
    $msgLabel = "<b>".$strings["success"]."</b> : The edits to project were successfully saved."; break;

    case removeProject:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["project_site_deleted"];
    break;

    case addClientToSite:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["add_user_project_site"];
    break;

    case removeClientToSite:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["remove_user_project_site"];
    break;

    case deleteTeamOwnerMix:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["delete_teamownermix"];
    break;

    case deleteTeamOwner:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["delete_teamowner"];
    break;

    case deleteTeamResource:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["delete_teamresource"];
    break;

    case addToSite:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["add_project_site_success"];
    break;

    case removeToSite:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["remove_project_site_success"];
    break;

    case updateFile:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["update_comment_file"];
    break;

    case addFile:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["add_file_success"];
    break;

    case deleteFile:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["delete_file_success"];
    break;

    case add:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["addition_succeeded"];
    break;

    case delete:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["deletion_succeeded"];
    break;

    case addReport:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["report_created"];
    break;

    case deleteReport:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["deleted_reports"];
    break;

    case addAssignment:
    $tmpquery = $tableCollab["assignments"];
    last_id($tmpquery);
    $num = $lastId[0];
    unset($lastId);
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["addition_succeeded"]." ".$strings["add_optional"]." ".$blockPage->buildLink("assignmentcomment.php?task=".$taskDetail->tas_id[0]."&amp;id=$num","<b>".$strings["assignment_comment"]."</b>",in);
    break;

    case updateAssignment:
    $tmpquery = $tableCollab["assignments"];
    last_id($tmpquery);
    $num = $lastId[0];
    unset($lastId);
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["modification_succeeded"]." ".$strings["add_optional"]." ".$blockPage->buildLink("assignmentcomment.php?task=".$taskDetail->tas_id[0]."&amp;id=$num","<b>".$strings["assignment_comment"]."</b>",in);
    break;

    case update:
    $msgLabel = "<b>".$strings["success"]."</b> : ".$strings["modification_succeeded"];
    break;

    case blankUser:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["blank_user"];
    break;

    case blankClient:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["blank_organization"];
    break;

    case blankProject:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["blank_project"];
    break;

    case settingsNotwritable:
    $msgLabel = "<b>".$strings["attention"]."</b> : ".$strings["settings_notwritable"];
    break;
}


?>