<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | prjperms.php                                                              |
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
include("includes/library.php");
include("includes/lib-projects.php"); 

if (isset($_POST["pid"])) {
    $pid = COM_applyFilter($_POST['pid']);

} else {
    $pid = COM_applyFilter($_GET['pid']);

} 
include($_CONF['path_system'] . 'classes/navbar.class.php');  
include("includes/block.class.php");

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

echo $pluginLangLabels['add_project'];
echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );

$_CLEAN = array(); 
$mytextvars = array('showUsers','showGroups','showUsersVal','showGroupsVal','op','usersandgroups');
$_CLEAN = array_merge($_CLEAN,ppGetData($mytextvars,false,'POST','text'));
// Integer only Variables
if($_POST['pid']==''){
    $myintvars = array('pid', 'edit', 'del', 'checkView', 'checkWrite', 'checkFull', 'rid');
    $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'GET','int'));
    }
else{
    $myintvars = array('pid', 'edit', 'del', 'checkView', 'checkWrite', 'checkFull','rid');
    $_CLEAN = array_merge($_CLEAN,ppGetData($myintvars,false,'POST','int'));
    }


$pid=$_CLEAN['pid'];
if($_USER['uid']==''){
    $uid=1;
}else{
    $uid=$_USER['uid'];    
}

$ret=prj_getProjectPermissions($pid,$uid);
if($ret['full']==TRUE){
    //who cares about anything else, if you're not allowed to do anything, why bother showing it at all....
    //this is the main routine for those who should be here..
    $op=$_CLEAN['op'];
    switch($op){
        case 'add':
            //add a permission
            if ($_CLEAN['checkView'] == 1 OR $_CLEAN['checkWrite'] == 1 OR $_CLEAN['checkFull'] == 1) {
                prj_addProjectPermission($_POST['usersandgroups'],$pid, $_CLEAN['checkView'], $_CLEAN['checkWrite'], $_CLEAN['checkFull']);
                prj_pushDownNewPermissions($pid);
                prj_sendNotification($pid, '', 1);
            }
            break;
            
        case'edit':
            prj_editProjectPermission($_CLEAN['checkView'], $_CLEAN['checkWrite'], $_CLEAN['checkFull'], $_CLEAN['rid']);
            prj_pushDownNewPermissions($pid);
            break;
            
        case 'delete':
            $sql="delete from {$_TABLES['prj_projPerms']} where id={$_CLEAN['rid']}";
            DB_query($sql);
            prj_pushDownNewPermissions($pid);
            prj_sendNotification($pid, '', 1);
            break;
        }//end switch
    

    //*******************************************************************
    //main display routine...
    //*******************************************************************
    $p = new Template($_CONF['path_layout'] . 'nexproject/');
    $p->set_file (array (
        'page' => 'projectPermissions.thtml', 
        'perms' => 'projectPermissionRights.thtml', 
        'permrec' => 'projectPermRecord.thtml'));
        
    $p->set_var('breadcrumb_trail',prj_breadcrumbs(0,$pid,"Permissions","Permissions"));
    $p->set_var($pluginLangLabels);          
    $p->set_var('site_url',$_CONF['site_url']);
    $p->set_var('layout_url',$_CONF['layout_url']."/nexproject");     
    if($_CLEAN['showUsersVal']==''){
        $p->set_var('showUsersVal','true');
        $p->set_var('showUsersChecked',' checked ');
        $filterUser='1';
        }
    else{
        if($_CLEAN['showUsersVal']=='true'){
            $p->set_var('showUsersVal','true');
            $p->set_var('showUsersChecked',' checked ');
            $filterUser='1';
            }
        else{
            $p->set_var('showUsersVal','false');
            $p->set_var('showUsersChecked','  ');
            $filterUser='0';
            }    
        }
        
    if($_CLEAN['showGroupsVal']==''){
        $p->set_var('showGroupsVal','true');
        $p->set_var('showGroupsChecked',' checked ');
        $filterGroup='1';
        }
    else{
        if($_CLEAN['showGroupsVal']=='true'){
            $p->set_var('showGroupsVal','true');
            $p->set_var('showGroupsChecked',' checked ');
            $filterGroup='1';
            }
        else{
            $p->set_var('showGroupsVal','false');
            $p->set_var('showGroupsChecked','  ');
            $filterGroup='0';
            }    
    }   
     
    $p->set_var('monitor',$pluginLangLabels['LANG_perm_monitor']);
    $p->set_var('teammember',$pluginLangLabels['LANG_perm_Team_Member']);
    $p->set_var('projectmanager',$pluginLangLabels['LANG_perm_Project_Manager']);
    
    $sql  ="(select a.uid as ID, a.fullname as NAME, 'U' as TT ";
    $sql .="from {$_TABLES['users']} a ";
    $sql .="where 1=$filterUser and uid>1) ";
    $sql .="union ";
    $sql .="( ";
    $sql .="select b.grp_id, b.grp_name, 'G' as TT ";
    $sql .="from {$_TABLES['groups']} b ";
    $sql .="where 1=$filterGroup) ";
    $sql .="order by TT, ID ";
    
    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    $hO='';

    $res=DB_query($sql);
    $nrows=DB_numRows($res);
    $optionOutput='';
    for($cntr=0;$cntr<$nrows;$cntr++){
        list($id, $name, $type)=DB_fetchArray($res);
        if($hO==''){
            $hO=$id . "|" . $name . "|" . $type;
        }else{
               $hO.=";".$id . "|" . $name . "|" . $type;
        }
        
    }
    
    $p->set_var('usersandgroups',$optionOutput);
    $p->set_var('ugListing',$hO);   
    $p->set_var('pid',$pid);

    
    //********************************************
    //now show the users and groups piece
    //********************************************
   
    prj_displayPerms($p,$pid,0,false,$_COOKIE['permsOrderBy'] . $_COOKIE['prj_ascdesc']);
    
    $p->parse('project_permission_rights','perms');    
    $p->parse ('output', 'page');
    echo $p->finish ($p->get_var('output'));
    
    }
else{
    //this user shouldnt be here
    echo "Sorry, You do not have enough permissions to edit this project's permissions";
    }


echo COM_siteFooter();


?>