<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxAddForm.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

require_once("../lib-common.php"); // Path to your lib-common.php

$id = COM_applyFilter($_GET['id'],true);           // Form ID of current displayed form
$rid = COM_applyFilter($_GET['rid'],true);         // Row ID in Dynamic Field - user can have multiple
$form = COM_applyFilter($_GET['form']);            // New form definition to add
$instance = $rid + 1;

if ($CONF_FE['debug']) {
    COM_errorLog("ajaxAddForm: id:$id,rid:$rid,form:$form");
    COM_errorLog("instance = $instance");
}

$content = htmlentities(nexform_dynamicFormHTML($form,$id,$form,'edit',true,$instance));

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$XML = "<result>";
$XML .= "<id>$id</id>";
$XML .= "<rid>$rid</rid>";
$XML .= "<html>$content</html>";
$XML .= "</result>";
print $XML;



?>