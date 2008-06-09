<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | initialize.php                                                            |
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

require_once ('../../../lib-common.php');

$_NF_TABLES = array();
$_NF_TABLES['nfprojects']             = $_DB_table_prefix . 'nfprojects';
$_NF_TABLES['nfproject_forms']        = $_DB_table_prefix . 'nfproject_forms';
$_NF_TABLES['nfproject_timestamps']   = $_DB_table_prefix . 'nfproject_timestamps';
$_NF_TABLES['nfproject_comments']     = $_DB_table_prefix . 'nfproject_comments';
$_NF_TABLES['nfproject_taskhistory']  = $_DB_table_prefix . 'nfproject_taskhistory';
$_NF_TABLES['nfproject_approvals']    = $_DB_table_prefix . 'nfproject_approvals';

$_NF_TABLES['nfprocess']              = $_DB_table_prefix . 'nf_process';
$_NF_TABLES['nfqueue']                = $_DB_table_prefix . 'nf_queue';
$_NF_TABLES['nfprocessvariables']     = $_DB_table_prefix . 'nf_processvariables';
$_NF_TABLES['nfqueuefrom']            = $_DB_table_prefix . 'nf_queuefrom';
$_NF_TABLES['nfnotifications']        = $_DB_table_prefix . 'nf_notifications';  
$_NF_TABLES['nfproductionassignments'] = $_DB_table_prefix . 'nf_productionassignments'; 


echo COM_siteHeader();
if (!SEC_inGroup('Root')) {
    echo COM_startBlock('Invalid Access');
    echo "<br><blockquote>You don't have access to execute this program!</blockquote>";
} else {
    echo COM_StartBlock("nexFlow Initialization Script");
    if ($_GET['op'] != 'initialize') {
        echo "<br><blockquote>This script will remove all workflow application related records and intialize all the Nexflow Process Tables but not effect your workflow defintions.";
        echo "<p>Do you want to proceed? <a href=\"{$_SERVER['PHP_SELF']}?op=initialize\">Yes</a>&nbsp;&nbsp;<a href=\"{$_CONF['site_url']}\">No</a></p>";
        echo "</blockquote>";
    } else {
        echo "<br><h3>Initialize Begins .... </h3>";
        foreach ($_NF_TABLES as $table) {
            echo "<br>Removing all records in $table ...";
            DB_query("DELETE FROM $table");
            DB_query ("ALTER TABLE $table AUTO_INCREMENT = 1");
        }
       echo "<br><h3>Initialize Completed .... </h3>";

    }
}

echo COM_endblock();
echo COM_siteFooter();

?>