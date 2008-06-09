<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | taskgantt.php                                                             |
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
require_once($_CONF['path_html'] . "nexproject/includes/jpgraph.php");
require_once($_CONF['path_html'] . "nexproject/includes/jpgraph_gantt.php");

$windowSize = explode(',',$_COOKIE['windowhw']);  // Window Size is being set via JS in header.thtml
$graphWidth = $windowSize[1] - 10;
if($graphWidth < $_PRJCONF['min_graph_width']) {
    $graphWidth = $_PRJCONF['min_graph_width'];
}

//RK - this small routine I have left in here so that any future customizations to the gantt size can be overridden by setting
//a client side cookie named "customGanttSize".
$customGanttSize = $_COOKIE['customGanttSize'];
if($customGanttSize != 0 && $customGanttSize > 0) {
    $graphWidth=$customGanttSize;
}
function prj_drawGanttBar(&$graph, $pid, $tid=0, &$row, &$count) {
    global $_TABLES,$_CONF, $_PRJCONF;
    
    $sql  = "SELECT tid,name,start_date, estimated_end_date,parent_task, progress, progress_id ";
    $sql .= "FROM {$_TABLES['prj_tasks']} ";
    if ($tid == 0) {
        $sql .= "WHERE pid=$pid AND parent_task=0 ORDER BY lhs ASC";
    } else {
        $sql .= "WHERE parent_task='$tid' ORDER BY lhs ASC";
    }
       
    $result = DB_query($sql);
    for ($j = 0; $j < DB_numrows($result); $j++) {
        list($tid, $name, $startdate, $enddate, $parent_task, $progress, $status) = DB_fetchArray($result);
        $name = html_entity_decode($name);
        $strdate = strftime("%Y/%m/%d", $startdate);
        $edate = strftime("%Y/%m/%d", $enddate);
        $sql = "SELECT fullname FROM {$_TABLES['users']}, {$_TABLES['prj_task_users']} ";
        $sql .= "WHERE {$_TABLES['prj_task_users']}.tid=$tid AND {$_TABLES['prj_task_users']}.uid={$_TABLES['users']}.uid";
        $result2 = DB_query($sql);
        list($owner) = DB_fetchArray($result2);
        $link = $_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id=" .$tid;
        $count = $count + 1;
        //echo "<br>Count:$count, row:$row";
        //$constrains[$j]=array($count, $parentcount, "CONSTRAIN_STARTEND");
        if(strlen($name)>$_PRJCONF['project_name_length'] ){
            $name=substr($name,0,$_PRJCONF['project_name_length'] );    
            $name .="...";
            }
        if ($strdate == $edate) {
            $milestone = new Milestone($row,$name, $strdate);
            $milestone->mark->SetType(MARK_DIAMOND);
            $graph->Add($milestone);
        } else {
            $activity = new GanttBar($count,"$name","$strdate","$edate","$owner");
            if ($status == 0 ) {
                // Yellow diagonal line pattern on a red background
                $activity->SetPattern(GANTT_SOLID,"darkgreen");
                $activity->progress->SetPattern(GANTT_RDIAG,"black");
                $activity->progress->SetFillColor("white");
            } elseif ($status == 1) {
                $activity->SetPattern(GANTT_SOLID,"yellow");
                $activity->progress->SetPattern(GANTT_RDIAG,"black");
                $activity->progress->SetFillColor("white");        
           } else {
                $activity->SetPattern(GANTT_SOLID,"red");
                $activity->progress->SetPattern(GANTT_RDIAG,"black");
                $activity->progress->SetFillColor("white");
           }

           // Set absolute height
           $activity->SetHeight(10);
           $activity->progress->Set($progress/100);

           // Specify progress        
           $activity->SetCSIMTarget ("$link" ); 
           $activity->SetCSIMAlt( $progress . "% completed"); 
           $activity->title->SetCSIMTarget("$link"); 
           $activity->title->SetCSIMAlt( $progress . "% completed");
           
            $qconstraints = DB_query("SELECT tid FROM {$_TABLES['prj_tasks']} WHERE parent_task='$tid' ORDER BY lhs ASC");
            $numconstraints = DB_numRows($qconstraints);
            for ($c = 1; $c <= $numconstraints; $c++ )  {
                $activity->SetConstrain($row+$c,CONSTRAIN_STARTSTART,"maroon4");
            }           
           
           // Add line to Gnatt Chart
           $graph->Add($activity);            
        }
        $row++;           
        if (DB_count($_TABLES['prj_tasks'],'parent_task',$tid) > 0) {
            prj_drawGanttBar($graph, $pid, $tid, $row,$count);
        }        

    }    
}    



if (isset ($_POST['pid'])) {
    $testpid = $_POST['pid'];
} elseif (isset ($_GET['id'])) {
            if ($_GET['mode'] =='view' || $_GET['mode'] =='add') {
                $testpid = $_GET['id'];
            } else {
                $testpid = $_GET['id'];
            }
} elseif (isset ($_GET['pid'])) {
        $testpid = $_GET['pid'];
} else {
        $testpid='';
}

if (isset($_GET['gdate1']) AND $_GET['gdate1'] != '') {
    $gdate1 = $_GET['gdate1'];
} elseif (isset($_POST['gdate1']) AND $_POST['gdate1'] != '') {
    $gdate1 = $_POST['gdate1'];
} else {
    $gdate1 = strftime('%Y/%m/%d',strtotime('-2 weeks'));
}
if (isset($_POST['gdate2']) AND $_POST['gdate2'] != '') {
    $gdate2 = $_POST['gdate2'];
} elseif (isset($_GET['gdate2']) AND $_GET['gdate2'] != '') {
    $gdate2 = $_GET['gdate2'];
} else {
    $gdate2 = strftime('%Y/%m/%d',strtotime('+2 weeks'));
}


if (isset($_GET['pid']) and $_GET['pid'] > 0) {
    $pid = $_GET['pid'];
}

$result = DB_query("SELECT * FROM {$_TABLES['prj_projects']} WHERE pid=$pid");
$A = DB_fetchArray($result);

$count=0;
$adate1 = explode('-',$gdate1);
$epocdate1 = mktime(0,0,0, $adate1['1'],$adate1['2'],$adate1['0']);
$adate2 = explode('-',$gdate2);
$epocdate2 = mktime(0,0,0, $adate2['1'],$adate2['2'],$adate2['0']);

$graph = new GanttGraph($graphWidth);
$graph->SetMarginColor('lightskyblue3@0.5');
$graph->SetBox(true,'blue@0.8',2);
$graph->SetFrame(true,'darkgreen',4); 
$graph->scale->divider->SetColor('yellow:0.6'); 
$graph->scale->dividerh->SetColor('yellow:0.6');

$graph->scale->actinfo->SetColTitles( 
    array('Task'),array(100)); 

$graph->scale->actinfo->SetBackgroundColor('green:0.5@0.5'); 
$graph->scale->actinfo->SetFont(FF_VERA,FS_NORMAL,10); 
$graph->scale->actinfo->vgrid->SetStyle('solid'); 
$graph->scale->actinfo->vgrid->SetColor('gray');

if (($epocdate2 - $epocdate1) > 11000000) {
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );
    $graph->scale->month->grid->SetColor('gray');
    $graph->scale->month->grid->Show(true);
    $graph->scale->year->grid->SetColor('gray'); 
    $graph->scale->year->grid->Show(true); 
} elseif (($epocdate2 - $epocdate1) > 6000000) {
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK);
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
    $graph->scale->week->SetFont(FF_FONT1);
} else {
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
    $graph->scale->week->SetFont(FF_FONT1);
}
$graph->img->SetImgFormat('jpeg'); 
$graph->img->SetQuality(100);
$graph->SetShadow();
$graph->SetFrame(true,'black',2);

$graph->SetDateRange($gdate1, $gdate2);
$count=0;
$row = 1;
$sql1  = "SELECT tid,name,start_date, estimated_end_date,parent_task, progress, progress_id ";
$sql1 .= "FROM {$_TABLES['prj_tasks']} WHERE pid=$pid AND parent_task=0 ORDER BY lhs ASC";
$result1 = DB_query($sql1);

// Draw all tasks and subtasks - recursive function
prj_drawGanttBar($graph, $pid,$tid, $row,$count);
// Draw final Chart
$_REQUEST['faketime']=time();   //this is added here to ensure that the image is NEVER cached by FF or IE
$graph->StrokeCSIM('taskgantt.php');


?>