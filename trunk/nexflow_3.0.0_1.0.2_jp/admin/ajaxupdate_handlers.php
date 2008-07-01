<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate_handlers.php                                                   |
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

require_once("../../../lib-common.php"); // Path to your lib-common.php


if (!SEC_hasRights('nexflow.edit')) {
    print ('No access rights');
    exit();
}

$rec = COM_applyFilter($_GET['rec'],true);
$op = COM_applyFilter($_GET['op']);
$handler = COM_applyFilter($_GET['handler']);
$description = COM_applyFilter($_GET['description']);

if (!get_magic_quotes_gpc()) {   
    $handler = addslashes($handler);
    $description = addslashes($description);    
} 

// Main Control Section Begins

if ($op == 'add') {
    DB_query("INSERT into {$_TABLES['nfhandlers']} (handler,description) values('{$handler}','{$description}')");
    $handler_id = DB_insertID();
} elseif ($op == 'update') {
        if($rec != NULL){
            DB_query("UPDATE {$_TABLES['nfhandlers']} SET handler='{$handler}', description='{$description}' WHERE id='{$rec}'");
            $handler_id = $rec;
         } else {
             $handler_id = NULL;
         }
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$XML = "<result>";
$XML .= "<record>$rec</record>";
$XML .= "<operation>$op</operation>";
$XML .= "<id>$hander_id</id>";
$XML .= "</result>";
print $XML;  


?>