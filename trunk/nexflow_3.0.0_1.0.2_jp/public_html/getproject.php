<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | getproject.php                                                            |
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

require_once ('../lib-common.php'); 

$usermodeUID = ($_USER['uid'] > 1) ? $_USER['uid'] : 1;
$project_id = COM_applyFilter($_GET['id'],true);
$actionurl = $_CONF['site_url'] .'/nexflow/index.php';
$rowid=1;

$fromprojectlink = true;

if ($nomenu == 1 OR $_GET['singleuse'] == 1) {
    echo COM_siteHeader('none');
} else {
    echo COM_siteHeader('menu');
}

$query  = DB_query("SELECT description,wf_process_id FROM {$_TABLES['nfprojects']} WHERE id='$project_id'");
if (DB_numRows($query) == 1) {
    list ($title,$processid) = db_fetchArray($query);
    echo COM_startBlock("View Project Detail for:  <span style=\"color:red;\">$title</span>",'','blockheader.thtml');

    $p = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
    $p->set_file ('javascript' , 'javascript/taskconsole.thtml');
    $p->set_var ('site_url',$_CONF['site_url']);
    $p->set_var ('layout_url',$_CONF['layout_url']);

    $p->set_var ('num_records',1);
    $p->parse ('output', 'javascript');

    echo $p->finish ($p->get_var('output'));
    echo LB . '<script type="text/javascript" src="'.$_CONF['site_url'] .'/nexflow/include/ajaxsupport.js"></script>';
    echo LB . '<div id="projectdetail_rec1">';
    include('projectdetails.php');
    echo '<table width="90%" style="margin-left:5px;margin-top:10px;">';
    echo '<tr id="taskdetail_rec1"><td class="pd_projects_row1">';
    echo $projectdetails;
    echo '<td></td></tr></table></div>';

    echo COM_endBlock();

} else {
    echo COM_startBlock('Display Project');
    echo '<div class="pluginAlert" style="text-align:center;padding:10px;">Error: Invalid Project Record<p><a href="#" onclick="javascript:history.go(-1);">Return to previous page</a></p></div>';
    echo COM_endBlock();
    COM_errorLog("Nexflow -getProject request - invalid record:$project_id");
}

if ($_GET['singleuse'] != 1) {
    echo COM_siteFooter(); 
}

?>