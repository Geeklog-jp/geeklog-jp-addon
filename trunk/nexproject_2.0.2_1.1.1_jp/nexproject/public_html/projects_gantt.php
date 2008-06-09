<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | projects_gantt.php                                                        |
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
//error_reporting(E_ALL);
require_once("../lib-common.php");
include_once($_CONF['path_html']  . "nexproject/includes/jpgraph.php");
include_once($_CONF['path_html']  . "nexproject/includes/jpgraph_gantt.php");
include_once($_CONF['path_html']  . "nexproject/includes/library.php");

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

if (isset($_USER['uid'])) {
    $userid = $_USER['uid'];
} else {
    $userid = 1;
}

function prj_drawProjectGanttBar(&$graph, &$row, &$count, $pid=0, $nameIndent='', $sm, $stm) {
    global $_TABLES,$_CONF, $showTasksForExpandedProjects, $expandedCookie,  $userid, $_PRJCONF, $filterCSV, $proj_query_from, $proj_query_where;

    // Determine the expanded projects
    $expanded=split(',',$expandedCookie);    
    $sm=ppApplyFilter( $sm,  true ,true);
    $stm=ppApplyFilter( $stm,  true ,true);

    $filter = COM_applyFilter($_COOKIE['filter']);
    $category_string = substr("$filter", 0, 3);

    // Get a list of groups user is a member of and setup to be used in SQL to test user can view project
    $groups = SEC_getUserGroups($uid);
    foreach ($groups as $id) {
        $aGroups[] = $id;
    }
    $prjPermGroups = implode(',',$aGroups);


    if (SEC_inGroup('Root')) {
        $queryfrom    = "FROM {$_TABLES['prj_projects']} a";
        $querywhere = " WHERE 1=1 ";
    }
    else {
        $queryfrom    = "FROM {$_TABLES['prj_projects']} a, {$_TABLES['prj_projPerms']} b ";
        $querywhere  .= "WHERE b.pid =a.pid";
        $querywhere  .= " AND b.taskID=0 AND (b.uid=$userid OR b.gid in ($prjPermGroups)) ";
    }

    switch ($category_string) {
        case 'cat':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_category']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.category_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_category'], 0, $needle);
            break;

        case 'loc':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_location']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.location_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_locations'], 0, $needle);
            break;

        case 'dep':
            $needle = substr("$filter", 3, 3);
            $queryfrom = $queryfrom . ", {$_TABLES['prj_department']} c ";
            $querywhere = $querywhere . "AND c.pid=a.pid AND c.department_id=$needle ";
            $header = nexlistOptionList('view', '', $_PRJCONF['nexlist_departments'], 0, $needle);
            break;

        case 'pri':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.priority_id=$needle ";
            $header = $strings["filter_priority"] . $priority[$needle];
            break;

        case 'pro':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.progress_id=$needle ";
            $header = $strings["filter_progress"] . $progress[$needle];
            break;

        case 'sta':
            $needle = substr("$filter", 3, 3);
            $querywhere = $querywhere . " AND a.status_id=$needle ";
            $header = $strings["filter_status"] . $status[$needle];
            break;

        default:
            $needle = '';
            $customFilter = '';
            $header = '';
    }

    $sql  = "SELECT a.pid, a.name, a.start_date, a.estimated_end_date, a.parent_id, a.percent_completion as progress, progress_id ";
    $sql .= $queryfrom;
    $sql .= $querywhere;
    if ($pid == 0) {
        $sql .= "AND parent_id=0 AND a.pid=c.pid ";
    } else {
        $sql .= "AND parent_id='$pid' ";
    }
    if($filterCSV!=''){
        $sql .= "AND pid  in ({$filterCSV}) ";
    }

    $sql .=" ORDER BY lhs ASC";
    $result = DB_query($sql,true);
    $testrows=DB_numRows($result);
    if($testrows==0){
         $sql  = "SELECT a.pid, a.name, a.start_date, a.estimated_end_date, a.parent_id, a.percent_completion as progress, progress_id ";
        $sql .= $queryfrom;
        $sql .= $querywhere;
        if ($pid == 0) {
            $sql .= "AND parent_id=0 ";
        } else {
            $sql .= "AND parent_id='$pid' ";
        }
        $sql .=" ORDER BY lhs ASC";
        $result = DB_query($sql);
    }
    for ($j = 0; $j < DB_numrows($result); $j++) {
        list($pid, $name, $startdate, $enddate, $parent_task, $progress, $status) = DB_fetchArray($result);
        $permsArray=prj_getProjectPermissions($pid, $userid);
        $ownertoken = getProjectToken($pid,$userid,"{$_TABLES['prj_users']}");
        if($sm=='0' && $stm=='1'){//only show team members (my projects)
            if(  $permsArray['teammember']=='1' || $permsArray['full']=='1' || $ownertoken=='1' ){
                prj_paintProjectBar(false,$pid, $name, $startdate, $enddate, $parent_task, $progress, $status,$expanded, $userid, $nameIndent, $graph, $count, $row, $sm, $stm);
            }//end if for perms checking

        }elseif($sm=='1' && $stm=='1'){ //show everything you have monitor and upwards access to (all projects)
            if( $permsArray['monitor'] =='1' || $permsArray['teammember']=='1' || $permsArray['full']=='1' || $ownertoken=='1' ){
                prj_paintProjectBar(true,$pid, $name, $startdate, $enddate, $parent_task, $progress, $status,$expanded, $userid, $nameIndent, $graph, $count, $row, $sm, $stm);
            }
        }
        
        
        //if this project has no child projects AND it has tasks AND the expansion sign is empty
        if (DB_count($_TABLES['prj_projects'],'parent_id',$pid) == 0 && DB_count($_TABLES['prj_tasks'],'pid',$pid) > 0 && $sign == '') {
            if($showTasksForExpandedProjects == 'true') {
                prj_drawProjectTasksGanttBar($graph, $row, $count, $pid, $nameIndent . ' ',0,0, $sm, $stm);
            }
        }

        $tempPerms=prj_getProjectPermissions($pid, $userid);
        if ( (array_keys($expanded,$pid) != array() && DB_count($_TABLES['prj_projects'],'parent_id',$pid) > 0 ) || ($tempPerms['monitor']=='0' && $tempPerms['teammember']=='0')  ) {
            if($showTasksForExpandedProjects == 'true') {
                prj_drawProjectTasksGanttBar($graph, $row, $count, $pid, $nameIndent . ' ',0,0, $sm, $stm);
            }
            prj_drawProjectGanttBar($graph, $row,$count, $pid,$nameIndent . ' ', $sm, $stm);
            $activity=NULL;
        }        
    }    //end for
}  //end function



function prj_paintProjectBar($testMonitor,$pid, $name, $startdate, $enddate, $parent_task, $progress, $status, $expanded, $userid, $nameIndent, &$graph,  &$count, &$row, $sm, $stm){
    global $_TABLES, $_CONF, $_PRJCONF;
    $name = html_entity_decode($name);

    if(strlen($name)>$_PRJCONF['project_name_length'] ){
        $name=substr($name,0,$_PRJCONF['project_name_length'] );    
        $name .="...";
        }

    $strdate = strftime("%Y/%m/%d", $startdate);
    $edate = strftime("%Y/%m/%d", $enddate);
    $sql  = 'SELECT  c.fullname ';
    $sql .= "FROM {$_TABLES['prj_users']} a ";
    $sql .= "INNER JOIN {$_TABLES['prj_projects']} b on a.pid=b.pid ";
    $sql .= "INNER JOIN {$_TABLES['users']} c on a.uid=c.uid ";
    $sql .= "WHERE a.role='o' AND a.pid=$pid";
    $result2 = DB_query($sql);
    list($owner) = DB_fetchArray($result2);
    $link = $_CONF['site_url'] . "/nexproject/viewproject.php?pid=" .$pid;
    $count = $count + 1;
    $doesAnyoneDependOnMe=DB_count($_TABLES['prj_projects'],'parent_id',$pid);
    if(array_keys($expanded,$pid)!= array()) {
        $sign = '[-]';
    } else {
        $sign = '[+]';
    }
    if($doesAnyoneDependOnMe==0) {
        $sign = '';
    }
    if ($strdate == $edate) {
        $milestone = new Milestone($row,"$nameIndent$name   $sign", $strdate);
        $milestone->mark->SetType(MARK_DIAMOND);
        //$milestone->title->SetFont(FF_ARIAL,FS_BOLD,10);
        if($sign != ''){
        $tempval2=$_SERVER['PHP_SELF'];
        $milestone->title->SetCSIMTarget("javascript:projectGanttClick('$pid','$sign','$tempval', '$gdate1', '$gdate2', '$tempval2');"); 
       }
        $graph->Add($milestone);
    } else {
        $activity = new GanttBar($count,"$nameIndent$name   $sign","$strdate","$edate","$owner");
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
       $tempval2=$_SERVER['PHP_SELF'];
       if($sign != ''){
           $activity->title->SetCSIMTarget("javascript:projectGanttClick('$pid','$sign','$tempval', '$gdate1', '$gdate2', '$tempval2');"); 
       }
       //$activity->title->SetFont(FF_ARIAL,FS_NORMAL,9);            
       $activity->title->SetCSIMAlt( $progress . "% completed" );
       $qconstraints = DB_query("SELECT pid FROM {$_TABLES['prj_projects']} WHERE parent_id='$pid' ORDER BY lhs ASC");
       $numconstraints = DB_numRows($qconstraints);
       if($sign == '[-]') {
           for ($c = 1; $c <= $numconstraints; $c++ ) {
               list($testingThisPID)=DB_fetchArray($qconstraints);
               $tempPerms=prj_getProjectPermissions($testingThisPID, $userid);
               $tempOwner=getProjectToken($testingThisPID,$userid,"{$_TABLES['prj_users']}");
               $buffer=0;
               if (array_keys($expanded,$pid) != array() && DB_count($_TABLES['prj_projects'],'parent_id',$pid) > 0) {
                   if($showTasksForExpandedProjects == 'true') {
                       if($testMonitor==false){//my projects
                           if(  $tempPerms['teammember']=='1' || ($tempPerms['full']=='1') ||  $tempOwner!=0){
                               prj_drawProjectTasksGanttBar($tmpg, $buffer, $tmpcount, $pid, $nameIndent , 0,1, $sm, $stm);
                           }
                       }else{//all projects
                           if(  $tempPerms['monitor']=='1' ||   $tempPerms['teammember']=='1' || ($tempPerms['full']=='1') ||  $tempOwner!=0){
                               prj_drawProjectTasksGanttBar($tmpg, $buffer, $tmpcount, $pid, $nameIndent , 0,1, $sm, $stm);
                           }
                       }
                   }
               }
               if($testMonitor==false){
                   if(  $tempPerms['teammember']=='1' || $tempPerms['full']=='1' || $tempOwner=='1'){                   
                       $activity->SetConstrain($row+$c+$buffer,CONSTRAIN_STARTSTART,"maroon4");
                   }
               }else{
                   if($tempPerms['monitor']=='1' ||  $tempPerms['teammember']=='1' || $tempPerms['full']=='1' || $tempOwner=='1'){                   
                       $activity->SetConstrain($row+$c+$buffer,CONSTRAIN_STARTSTART,"maroon4");
                   }
               }
           } //end for 
       }  //end if $sign==[-]
        
       // Add line to Gantt Chart
       $graph->Add($activity);
       
    }//end else
    $row++;
    
}


function prj_drawProjectTasksGanttBar(&$graph, &$row, &$count, $pid=0, $nameIndent='', $tid=0, $sampleCounting=0, $sm, $stm) {
    global $_TABLES,$_CONF, $showMonitor, $showTeamMember, $userid, $_PRJCONF, $filterCSV;

    $sql  = 'SELECT tid,name,start_date, estimated_end_date,parent_task, progress, progress_id ';
    $sql .= "FROM {$_TABLES['prj_tasks']} ";
    if ($pid == 0) {
        $sql .= 'WHERE pid=0 ';
    } else {
        $sql .= "WHERE pid='$pid' ";
    }
    $sql .= "and parent_task=$tid ";

    if($filterCSV!=''){
        $sql .= "AND  {$_TABLES['prj_tasks']}.pid  in ({$filterCSV}) ";
    }

    $sql .= ' ORDER BY lhs ASC';
    $result = DB_query($sql,true);
    $testnumrows=DB_numRows($result);
    if($testnumrows==0){    //this is to help overcome any COOKIE issues with the filtercsv
        $sql  = 'SELECT tid,name,start_date, estimated_end_date,parent_task, progress, progress_id ';
        $sql .= "FROM {$_TABLES['prj_tasks']} ";
        if ($pid == 0) {
            $sql .= 'WHERE pid=0 ';
        } else {
            $sql .= "WHERE pid='$pid' ";
        }
        $sql .= "and parent_task=$tid ";

        $sql .= ' ORDER BY lhs ASC';
        $result = DB_query($sql);
     }
    
    
    for ($j = 0; $j < DB_numrows($result); $j++) {
        list($tid,  $name, $startdate, $enddate, $parent_task, $progress, $status) = DB_fetchArray($result);
        $permsArray=prj_getProjectPermissions($pid, $userid, $tid);
        $ownertoken= getTaskToken($tid, $userid, "{$_TABLES['prj_task_users']}", "{$_TABLES['prj_tasks']}");
        if($sm=='1' && $stm=='1') {     // all projects
            if( ($permsArray['monitor']=='1' ) || ($permsArray['teammember']=='1' ) || $ownertoken!=0){
            $name = html_entity_decode($name);
            $strdate = strftime("%Y/%m/%d", $startdate);
            $edate = strftime("%Y/%m/%d", $enddate);

            $sql  = "SELECT c.fullname ";
            $sql .= "FROM {$_TABLES['prj_task_users']} a ";
            $sql .= "INNER JOIN {$_TABLES['prj_tasks']} b on a.tid=b.tid ";
            $sql .= "INNER JOIN {$_TABLES['users']} c on a.uid=c.uid ";
            $sql .= "WHERE a.role='o' AND a.tid=$tid ";

            $result2 = DB_query($sql);
            list($owner) = DB_fetchArray($result2);

            $link = $_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id=" .$tid;
            $count = $count + 1;
            if(strlen($name)>$_PRJCONF['project_name_length'] ){
                $name=substr($name,0,$_PRJCONF['project_name_length'] );    
                $name .="...";
                }
            $name = $nameIndent . $name;

            if ($strdate == $edate) {
                $milestone = new Milestone($row,$name, $strdate);
                $milestone->mark->SetType(MARK_DIAMOND);
                $graph->Add($milestone);
            } else {            
                $taskActivity = new GanttBar($count,$name,"$strdate","$edate","");
                if ($status == 0 ) {
                    // Yellow diagonal line pattern on a red background
                    $taskActivity->SetPattern(BAND_RDIAG,"green");
                    $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                    $taskActivity->progress->SetFillColor("white");
                } elseif ($status == 1) {
                    $taskActivity->SetPattern(BAND_RDIAG,"yellow");
                    $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                    $taskActivity->progress->SetFillColor("white");        
               } else {
                    $taskActivity->SetPattern(BAND_RDIAG,"red");
                    $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                    $taskActivity->progress->SetFillColor("white");
               }
               $taskActivity->caption->SetFont(FF_FONT1,FS_NORMAL,10);
               $taskActivity->caption->SetColor('black');
               $taskActivity->caption->Set($name);

               // Set absolute height
               $taskActivity->SetHeight(10);
               $taskActivity->progress->Set($progress/100);

               // Specify progress        
               $taskActivity->SetCSIMTarget ("$link" ); 
               $taskActivity->SetCSIMAlt( $progress . "% completed"); 
               $tempval=$_GET['expanded'];
               $tempval2=$_SERVER['PHP_SELF'];
               $taskActivity->title->SetCSIMTarget(""); 
               $taskActivity->title->SetCSIMAlt( $progress . "% completed" );
               $qconstraints = DB_query("SELECT tid FROM {$_TABLES['prj_tasks']} WHERE parent_task='$tid' ORDER BY lhs ASC");
               $numconstraints = DB_numRows($qconstraints);
               for ($c = 1; $c <= $numconstraints; $c++ )  {
                   //$taskActivity->SetConstrain($row+$c,CONSTRAIN_STARTSTART,"maroon4");
               }

               // Add line to Gantt Chart
               if(!$sampleCounting){
                    $graph->Add($taskActivity);
                }
            }
            $row++; 
            }
        } else {    // my projects
            if(  $ownertoken!=0){
                $name = html_entity_decode($name);
                $strdate = strftime("%Y/%m/%d", $startdate);
                $edate = strftime("%Y/%m/%d", $enddate);

                $sql  = "SELECT c.fullname ";
                $sql .= "FROM {$_TABLES['prj_task_users']} a ";
                $sql .= "INNER JOIN {$_TABLES['prj_tasks']} b on a.tid=b.tid ";
                $sql .= "INNER JOIN {$_TABLES['users']} c on a.uid=c.uid ";
                $sql .= "WHERE a.role='o' AND a.tid=$tid";

                $result2 = DB_query($sql);
                list($owner) = DB_fetchArray($result2);

                $link = $_CONF['site_url'] . "/nexproject/viewproject.php?mode=view&id=" .$tid;
                $count = $count + 1;
                if(strlen($name)>$_PRJCONF['project_name_length'] ){
                    $name=substr($name,0,$_PRJCONF['project_name_length'] );    
                    $name .="...";
                    }
                $name = $nameIndent . $name;

                if ($strdate == $edate) {
                    $milestone = new Milestone($row,$name, $strdate);
                    $milestone->mark->SetType(MARK_DIAMOND);
                    $graph->Add($milestone);
                } else {
                    $taskActivity = new GanttBar($count,$name,"$strdate","$edate","");
                    if ($status == 0 ) {
                        // Yellow diagonal line pattern on a red background
                        $taskActivity->SetPattern(BAND_RDIAG,"green");
                        $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                        $taskActivity->progress->SetFillColor("white");
                    } elseif ($status == 1) {
                        $taskActivity->SetPattern(BAND_RDIAG,"yellow");
                        $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                        $taskActivity->progress->SetFillColor("white");
                   } else {
                        $taskActivity->SetPattern(BAND_RDIAG,"red");
                        $taskActivity->progress->SetPattern(GANTT_RDIAG,"black");
                        $taskActivity->progress->SetFillColor("white");
                   }
                   $taskActivity->caption->SetFont(FF_FONT1,FS_NORMAL,10);
                   $taskActivity->caption->SetColor('black');
                   $taskActivity->caption->Set($name);

                   // Set absolute height
                   $taskActivity->SetHeight(10);
                   $taskActivity->progress->Set($progress/100);

                   // Specify progress        
                   $taskActivity->SetCSIMTarget ("$link" ); 
                   $taskActivity->SetCSIMAlt( $progress . "% completed"); 
                   $tempval=$_GET['expanded'];
                   $tempval2=$_SERVER['PHP_SELF'];
                   $taskActivity->title->SetCSIMTarget(""); 
                   $taskActivity->title->SetCSIMAlt( $progress . "% completed" );
                   $qconstraints = DB_query("SELECT tid FROM {$_TABLES['prj_tasks']} WHERE parent_task='$tid' ORDER BY lhs ASC");
                   $numconstraints = DB_numRows($qconstraints);
                   for ($c = 1; $c <= $numconstraints; $c++ )  {
                       //$taskActivity->SetConstrain($row+$c,CONSTRAIN_STARTSTART,"maroon4");
                   }
                   // Add line to Gantt Chart
                   if(!$sampleCounting){
                        $graph->Add($taskActivity);
                    }
                }
                $row++; 
            }
        }

        if (DB_count($_TABLES['prj_tasks'],'parent_task',$tid) > 0) {
             prj_drawProjectTasksGanttBar($graph, $row, $count, $pid, $nameIndent . " ", $tid,$sampleCounting, $sm, $stm);
        }        

    }    //end for

}  //end function



/*  Main Code   */
$showTasksForExpandedProjects=$_COOKIE['STFEP'];
$expandedCookie=$_COOKIE['expanded'];

$filterCSV=COM_applyFilter($_COOKIE['filterTasks']);
$filterCSV=html_entity_decode($filterCSV);
$filterCSV=str_replace( "|",",",$_COOKIE['filterTasks']);


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

$graph->scale->actinfo->SetColTitles(array('Projects'),array(100));

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

$row = 1;

// Draw all projects - recursive function
prj_drawProjectGanttBar($graph, $row, $count,0,'',$_COOKIE['showMonitor'], $_COOKIE['showTeamMember']);


// Draw final Chart
$_REQUEST['faketime']=time();  //this is added here to ensure that the image is NEVER cached by FF or IE
$graph->StrokeCSIM('projects_gantt.php');


?>