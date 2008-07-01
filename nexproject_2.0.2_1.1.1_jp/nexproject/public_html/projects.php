<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | projects.php                                                              |
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
require_once($_CONF['path_html'] . "nexproject/includes/library.php");

if($_COOKIE['windowwidth']=='') setcookie('windowwidth',$_PRJCONF['min_graph_width']);
setcookie('screen', 'allprojects');
$_COOKIE['screen'] = 'allprojects';

$filter = COM_applyFilter($_GET['filter']);
if ($filter == 'all') {
    $filter = '';
}
else if ($filter == '') {
    $filter = COM_applyFilter($_COOKIE['filter']);
}

$mode = COM_applyFilter($_REQUEST['mode']);

setcookie('filter', $filter);
$_COOKIE['filter'] = $filter;
setcookie('showMonitor','1');
setcookie('showTeamMember','1');

include_once("includes/block.class.php");

if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

$mode = COM_applyFilter($_REQUEST['mode']);
$id = COM_applyFilter($_REQUEST['id'],true);       // Project ID
switch ($mode) {

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


/* Show Custom list of Blocks */
echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) ) ;

echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr style="vertical-align:top"><td nowrap>';

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink($_CONF['site_url'] . "/nexproject/index.php?", $strings["home"], in));
$blockPage->itemBreadcrumbs($strings["projects"]);
$blockPage->closeBreadcrumbs();
echo '</td></tr></table>';

/* BL: Oct 6, 2004: Not used currently */
if (isset($msg) AND $msg != "") {
    include("includes/messages.php");
    $blockPage->messagebox($msg);
} 

$blockPage->bornesNumber = "1";

/* Display the 'All Projects' Block */

prj_displayAllProjects( $blockPage);



//gantt chart display area starts here
$blockPage->bornesNumber = "4";
$blockg = new block();
$blockg->form = "gaP";

$headingTitle = 'Gantt Chart';
$headingStatusArea = '<span id="ajaxstatus_gantt" class="pluginInfo" style="display:none">&nbsp;</span>';
$blockg->headingToggle($headingTitle,$headingStatusArea);
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
if(isset($_COOKIE['gdate2'])){
    $gdate2 = $_COOKIE['gdate2'];
}
else{
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

echo '<div id="ganttChartDIV">';
//showTeamMember and showMonitor are variables used in the projectsGantt file to determine what to show programmatically
require_once( "projects_gantt.php");
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
$p->set_var('disableFilter','display:none');
$p->set_var('action_url',$_CONF['site_url'] . '/nexproject/index.php');
$p->set_var('layout_url',$_CONF['layout_url']);
$p->set_var('imgset',$_CONF['layout_url'] . '/nexproject/images');
$actionurl = $_CONF['site_url'] . '/nexproject/index.php';
$imgset = $_CONF['layout_url'] . '/nexproject/images';

$p->parse ('output', 'contextmenu');
echo $p->finish ($p->get_var('output'));
echo  COM_siteFooter();

?>