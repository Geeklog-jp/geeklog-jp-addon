<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | newprocess.php                                                            |
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
require_once ('libconsole.php');
require_once ($_CONF['path'] . 'plugins/nexflow/config.php');
require_once ($_CONF['path_system'] . 'classes/navbar.class.php' );

// Test if user should be able to access this script
$noaccess = false;
if ($_USER['uid'] < 2) {
    $noaccess = true;  
} elseif ($CONF_NF['taskconsolepermrequired'] AND !SEC_hasRights('nexflow.user')) {
    $noaccess = true;
}
if ($noaccess) {
    echo COM_siteHeader();
    echo COM_startBlock("Access Error");
    echo '<div style="text-align:center;padding-top:20px;">';
    echo "You do not have sufficient access.";
    echo "<p><button  onclick='javascript:history.go(-1)'>Return</button></p><br>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit();    
}


// See if we have priveledges to use the Select Task User Feature
$selectUser = COM_applyFilter($_REQUEST['taskuser'],true); 
$optLinkVars = '';

if ($selectUser > 0) {
    $usermodeUID = $selectUser;
    if (SEC_hasRights('nexflow.admin')) {
        $optLinkVars = "&taskuser=$usermodeUID";
    }
} else {
    $usermodeUID = ($_USER['uid'] > 1) ? $_USER['uid'] : 1;
}

$nfclass= new nexflow('',$usermodeUID);

$retval = '';
echo COM_siteHeader('menu' );
$username = COM_getDisplayName($usermodeUID);   
echo COM_startBlock("Workflow Task Console for: $username",'','blockheader.thtml');  
echo taskconsoleShowNavbar('Start Process');

$p = new Template($_CONF['path_layout'] . 'nexflow/admin');
$p->set_file (array (
    'page'      =>     'startprocesses.thtml',
    'record'    =>     'process_record.thtml'));
    
$p->set_var('site_url',$_CONF['site_url']);
$p->set_var('optional_parms',$optLinkVars);

$tquery = DB_query("SELECT id,templateName FROM {$_TABLES["nftemplate"]} ORDER BY id");
$i=1;
while (list ($templateId, $templateName) = DB_fetchArray($tquery)) {
    $p->set_var('template_id', $templateId);
    $p->set_var('template_name', $templateName);
    $p->set_var('csscode',($i%2)+1);
    $p->parse('template_records','record',true);
    $i++;
}

if($_GET['start'] != NULL) {
    $newProcess = $nfclass->newprocess($_GET['start']);
    if($newProcess != NULL) {
        $nfclass->set_ProcessVariable('INITIATOR',$usermodeUID);
        $p->set_var('message','Process Started');

    } else {
        $p->set_var('message','Error Starting Process');         
    }
} else {
    $p->set_var('showmsg','none');
}

$p->parse ('output', 'page');
echo $p->finish ($p->get_var('output'));

echo COM_endBlock();
echo COM_siteFooter();

?>