<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxrefreshtasks.php                                                      |
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
require_once($_CONF['path_system'] . 'classes/navbar.class.php');  
require_once($_CONF['path_html']  . "/nexproject/includes/block.class.php");

$mode = COM_applyFilter($_REQUEST['op']);
$id = COM_applyFilter($_REQUEST['id'],true);       // task ID

COM_errorLog("ajaxrefreshtasks - op: $op, id:$id");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");

if ($id > 0) {
    $sql="SELECT pid FROM {$_TABLES['prj_tasks']} WHERE tid='{$id}'";
    $res=DB_query($sql);
    list($pid)=DB_fetchArray($res);
}

switch ($mode){
    
    case 'movetasklft':
        prj_moveTaskLeft($id);
        prj_displayMyTasks( $blockPage, $pid);
        break;
   
    case 'movetaskrht':
        prj_moveTaskRight($id);
        prj_displayMyTasks( $blockPage, $pid);
        break;

    case 'movetaskup':
        prj_moveTaskUp($id);
        prj_displayMyTasks( $blockPage, $pid);
        break;
        
    case 'movetaskdn':
        prj_moveTaskDown($id);
        prj_displayMyTasks( $blockPage, $pid);
        break;

    case 'myprj_refresh' :
        COM_errorLog('myprj_refresh');
        prj_displayMyProjectTasks($blockPage);
        break;

    case 'refresh' :
        prj_displayMyTasks( $blockPage, $pid);
        break;
}


?>