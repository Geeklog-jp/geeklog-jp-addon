<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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
$_PRJCONF['debug'] = false;

$_PRJCONF['version'] = '2.0.2';

$_PRJCONF['notifications_enabled'] = true;  // set this to false to disable sending out of email notifications for the entire nexProject application

$_PRJCONF['fonts_directory'] = $_CONF['path'].'/plugins/nexproject/fonts/';  // this is set to the default fonts directory where the vera TTF is located
                                        //the default directory is the system/plugins/nexproject/fonts directory
                                        //change this to suit your installation.

/* Define which blocks to show on the all-projects page - projects.php */
$_PRJCONF['leftblocks'] = array ('projectFilter');
$_PRJCONF['leftblocks'] = ppGetUserBlocks($_PRJCONF['leftblocks']);

// Theme settings
define('THEME',$_CONF['theme']."/nexproject");
define('ROWLIMIT',1);

//this is the define for using our fonts directory above
DEFINE("TTF_DIR",$_PRJCONF['fonts_directory']);


/* Project Options */
$_PRJCONF['project_name_length']        = 30;
$_PRJCONF['lockduration']               = 10;   // Used to lock the Task Table when moving tasks - Set Max Lock Time (default is 10 seconds)
$_PRJCONF['min_graph_width']            = 700;  // Minimum width in pixels
$_PRJCONF['project_block_rows']         = 50;   // Number of records to show in the 'All Projects' and 'My Projects' Block
$_PRJCONF['task_block_rows']            = 10;   // Number of records to show in the 'Tasks' Block
$_PRJCONF['project_task_block_rows']    = 20;   // Number of records to show in the project detail view -'Tasks' Block

// HTML definitions for the image that will be used to show subtask names and their order indented
$subTaskImg       = '<img src="' . $_CONF['layout_url'] . '/nexproject/images/subtask.gif" BORDER="0">';
$subTaskOrderImg  = '<img src="' . $_CONF['layout_url'] . '/nexproject/images/subtask.gif" BORDER="0">';

// Database Definitions
$_TABLES['prj_category']             = $_DB_table_prefix . 'nxprj_category';
$_TABLES['prj_department']           = $_DB_table_prefix . 'nxprj_department';
$_TABLES['prj_location']             = $_DB_table_prefix . 'nxprj_location';
$_TABLES['prj_objective']            = $_DB_table_prefix . 'nxprj_objective';
$_TABLES['prj_permissions']               = $_DB_table_prefix . 'nxprj_permissions';
$_TABLES['prj_users']                     = $_DB_table_prefix . 'nxprj_users';
$_TABLES['prj_projects']                  = $_DB_table_prefix . 'nxprj_projects';
$_TABLES['prj_sorting']                   = $_DB_table_prefix . 'nxprj_sorting';
$_TABLES['prj_task_users']                = $_DB_table_prefix . 'nxprj_taskusers';
$_TABLES['prj_tasks']                     = $_DB_table_prefix . 'nxprj_tasks';
$_TABLES['prj_statuslog']                 = $_DB_table_prefix . 'nxprj_statuslog';
$_TABLES['prj_session']                   = $_DB_table_prefix . 'nxprj_sessions';
$_TABLES['prj_filters']                   = $_DB_table_prefix . 'nxprj_filters';
$_TABLES['prj_lockcontrol']               = $_DB_table_prefix . 'nxprj_lockcontrol';
$_TABLES['prj_projPerms']                 = $_DB_table_prefix . 'nxprj_projPerms';
$_TABLES['prj_taskSemaphore']             = $_DB_table_prefix . 'nxprj_taskSemaphore';
$_TABLES['prj_config']                    = $_DB_table_prefix . 'nxprj_config';

/* Setup nexList References */
$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='nexlist_location'",true);
list($_PRJCONF['nexlist_locations'])=DB_fetchArray($nxprj_config_res);

$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='nexlist_department'",true);
list($_PRJCONF['nexlist_departments'])=DB_fetchArray($nxprj_config_res);

$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='nexlist_categories'",true);
list($_PRJCONF['nexlist_category'])=DB_fetchArray($nxprj_config_res);

$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='nexlist_objectives'",true);
list($_PRJCONF['nexlist_objective'])=DB_fetchArray($nxprj_config_res);

/* nexFile reference */
$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='nexfile_parent_cat'",true);
list($_PRJCONF['nexfile_parent'])=DB_fetchArray($nxprj_config_res);

/* forum reference */
$nxprj_config_res=DB_query("select config_value from {$_TABLES['prj_config']} where config_param='forum_parent_cat'",true);
list($_PRJCONF['forum_parent'])=DB_fetchArray($nxprj_config_res);





?>