<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate.php                                                            |
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

$mode = COM_applyFilter($_GET['mode']);
$rec = COM_applyFilter($_GET['rec'],true);
$op = COM_applyFilter($_GET['op']);
$var1 = COM_applyFilter($_GET['var1']);
$var2 = COM_applyFilter($_GET['var2']);

$var1 = 'test result';
$var2 = htmlentities('<table><tr><td><b>Label</b></td><td><input type="text" name="address"></td></tr></table>');

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$retval = "<result>";
$retval .= "<mode>$mode</mode>";
$retval .= "<record>$rec</record>";
$retval .= "<property>$op</property>";
$retval .= "<value1>$var1</value1>";
$retval .= "<value2>$var2</value2>";
$retval .= "</result>";
print $retval;
?>