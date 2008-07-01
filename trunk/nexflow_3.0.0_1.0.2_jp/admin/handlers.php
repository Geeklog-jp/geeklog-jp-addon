<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | handlers.php                                                              |
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

// Only let users with nexflow.edit rights to access this page
if (!SEC_hasRights('nexflow.edit')) { 
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_NF00['access_denied']);
    $display .= $LANG_NF00['admin_access_error'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
} 

require_once ($_CONF['path'] . 'plugins/nexflow/config.php');
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

echo COM_siteHeader('menu');
$navbar = new navbar;
$navbar->add_menuitem('My Tasks',$_CONF['site_url'] .'/nexflow/index.php');
$navbar->add_menuitem('View Templates',$_CONF['site_admin_url'] .'/plugins/nexflow/templates.php');
$navbar->add_menuitem('Edit Handlers',$_CONF['site_admin_url'] .'/plugins/nexflow/handlers.php');
$navbar->set_selected('Edit Handlers');   
echo $navbar->generate();

$userid = $_USER['uid']; 
$operation = COM_applyFilter($_GET['operation'],false);
$handlerID = COM_applyFilter($_GET['handlerID'],true);
if (!get_magic_quotes_gpc()) { 
    $handler = addslashes($_GET['handler']);
    $description = addslashes($_GET['desc']);
} else {
    $handler = $_GET['handler'];
    $description = $_GET['desc'];
}
if($handlerID == 0){
    $handlerID = NULL;
}

switch(strtolower($operation)) {
    case 'add':
        DB_query("INSERT into {$_TABLES['nfhandlers']} (handler,description) values('{$handler}','{$description}')");
        break;

     case 'delete':
        DB_query("DELETE FROM {$_TABLES['nfhandlers']} WHERE id='{$handlerID}'");
        break;
}

$p = new Template($_CONF['path_layout'] . 'nexflow/admin');
$p->set_file (array ('page'=>'handlers.thtml',
        'records'      => 'handler_record.thtml'));
$public_url = $_CONF['site_admin_url'] .'/plugins/nexflow';
$p->set_var('public_url',$public_url);

$query = DB_Query("SELECT * FROM {$_TABLES['nfhandlers']}");
$cssid = 2;
while ($A = DB_fetchArray($query)) {
    $p->set_var('handler_id', $A['id']);
    $p->set_var('handler_name', $A['handler']);
    $p->set_var('vhandler_desc', nl2br($A['description']));    // Newlines are displayed as such in the textarea field
    $p->set_var('ehandler_desc',str_replace("<br />","\n",$A['description']));  // Convert newlines into br tags for displaying as text

    $p->set_var('edit_link',"[&nbsp;<a href=\"#\" onClick='ajaxUpdateHandler(\"editHandler\",{$A['id']});'\">Edit</a>&nbsp;]");

    $delete_link = "[&nbsp;<a href=\"#\" ";
    $delete_link .= "onClick='if(confirm(\"Are you sure you want to delete this handler?\")) ";
    $delete_link .= "document.location=\"{$public_url}/handlers.php?operation=delete&handlerID={$A['id']}\"'";
    $delete_link .= ">Delete</a>&nbsp;]";
    $p->set_var('delete_link',$delete_link);

    if ($i == 1) {
        $p->parse('handler_records','records');
    } else {
        $p->parse('handler_records','records',true);
    }
    $p->set_var('cssid' , $cssid);
    $cssid = ($cssid == 1) ? 2 : 1;
    $i++;
}

$p->parse ('output', 'page');
echo $p->finish ($p->get_var('output'));
echo COM_siteFooter (false);

?>