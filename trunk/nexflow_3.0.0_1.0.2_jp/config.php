<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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

$CONF_NF = array();
$CONF_NF['debug'] = false;
$CONF_NF['version'] = '3.0.0';
$CONF_NF['TaskConsole_URL'] = $_CONF['site_url'] . '/nexflow/index.php';
$CONF_NF['RequestDetailLink_URL'] = $_CONF['site_url'] . '/nexflow/getproject.php';
$CONF_NF['export_dir'] = $_CONF['path_html'] . 'admin/plugins/nexflow/export/';
$CONF_NF['email_notifications_enabled'] = false;

// Set to true if you have setup the nexflow Orchestrator to be exectuted in the background by the server
$CONF_NF['orchestrator_using_cron'] = false;

// Enables Task Delete in the taskconsole - mytasks view
$CONF_NF['allow_task_delete'] = false;

// Set to true if only logged in members can access 'All Requests' page
$CONF_NF['allrequestsloginrequired'] = false;

// Set to true if only logged in members with nexflow.user right can access 'Task Console'
$CONF_NF['taskconsolepermrequired'] = false;

$CONF_NF['final_edit_tasks'] = array(188);


$CONF_NF['fileperms'] = '0755';  // Needs to be a string for the upload class use.


/* Coders should append any lookup lists that are being used to this array
 * Refer to function nexlist_delitem_nexflow in functions.inc
 * Array should be an associative array
 *   $NF_LISTS['workflow_roles'] = 5;
*/
$NF_LISTS = array();


/* There should be no reason to change these settings */

$_TABLES['nfprocess']               = $_DB_table_prefix . 'nf_process';
$_TABLES['nfqueue']                 = $_DB_table_prefix . 'nf_queue';
$_TABLES['nftemplate']              = $_DB_table_prefix . 'nf_template';
$_TABLES['nftemplatedata']          = $_DB_table_prefix . 'nf_templatedata';
$_TABLES['nftemplateassignment']    = $_DB_table_prefix . 'nf_templateassignment';
$_TABLES['nfhandlers']              = $_DB_table_prefix . 'nf_handlers';
$_TABLES['nfsteptype']              = $_DB_table_prefix . 'nf_steptype';
$_TABLES['nftemplatedatanextstep']  = $_DB_table_prefix . 'nf_templatedatanextstep';
$_TABLES['nfprocessvariables']      = $_DB_table_prefix . 'nf_processvariables';
$_TABLES['nftemplatevariables']     = $_DB_table_prefix . 'nf_templatevariables';
$_TABLES['nfifprocessarguments']    = $_DB_table_prefix . 'nf_ifProcessarguments';
$_TABLES['nfifoperators']           = $_DB_table_prefix . 'nf_ifoperators';
$_TABLES['nfqueuefrom']             = $_DB_table_prefix . 'nf_queuefrom';
$_TABLES['nfnotifications']         = $_DB_table_prefix . 'nf_notifications';
$_TABLES['nfproductionassignments'] = $_DB_table_prefix . 'nf_productionassignments';
$_TABLES['nfuseraway']              = $_DB_table_prefix . 'nf_userawayprefs';
$_TABLES['nfappgroups']             = $_DB_table_prefix . 'nf_appgroups';

/* Task Console Tables */
$_TABLES['nfprojects']              = $_DB_table_prefix . 'nfprojects';
$_TABLES['nfproject_forms']         = $_DB_table_prefix . 'nfproject_forms';
$_TABLES['nfproject_timestamps']    = $_DB_table_prefix . 'nfproject_timestamps';
$_TABLES['nfproject_comments']      = $_DB_table_prefix . 'nfproject_comments';
$_TABLES['nfproject_taskhistory']   = $_DB_table_prefix . 'nfproject_taskhistory';
$_TABLES['nfproject_approvals']     = $_DB_table_prefix . 'nfproject_approvals';

// Task Status values used in the nexflow Queue table
$CONF_NF['taskstatus'] = array(
    0   => 'Un-completed',
    1   => 'Completed',
    2   => 'On-hold',
    3   => 'Rejected',
    4   => 'If Condition False'
);

$CONF_NF['processstatus'] = array(
    0   => 'Active',
    1   => 'Completed',
    2   => 'Regenerated',
    3   => 'On Hold',
    4   => 'Cancelled'
);


$CONF_NF['NFProjectStatus'] = array(
    0   => 'Active',
    1   => 'Completed',
    2   => 'Regenerated',
    7   => 'On Hold',
    4   => 'Cancelled',
    5   => '',
    6   => 'Recycle'
);


$CONF_NF['formstatus'] = array (
    0       =>  'Draft',         // Not submitted
    1       =>  'Submitted',     // Submitted for approval
    2       =>  'Edit',          // Submitted form is open for edit
    3       =>  'Accepted',      // Form has been accepted
    4       =>  'Distributed',   // Form accepted and now distributed
    5       =>  'Archive',       // Form has been archived
    6       =>  'Rejected',      // Form was rejected
    7       =>  'Deleted'        // Marked for deletion
);


$CONF_NF['sortOptions'] = array(
    'cdate'             => 'Created Date',
    'taskname'          => 'Task name'
);




?>