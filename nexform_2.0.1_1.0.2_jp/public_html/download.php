<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | download.php                                                              |
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

require_once ('../lib-common.php');

$rec = COM_applyFilter($_GET['id'], true);
$rid = COM_applyFilter($_GET['rid'], true);

if ($rid != 0) {    //file field
    $sql = "SELECT field_data FROM {$_TABLES['formResData']} WHERE result_id=$rid AND field_id=$rec;";
}
else {  //mfile field
    $sql = "SELECT field_data FROM {$_TABLES['formResData']} WHERE id=$rec;";
}
$res = DB_query($sql);
$A = DB_fetchArray($res);

if ($A === FALSE) {
    echo "Error: Cannot Display Selected File";
    COM_errorLog("Error: Cannot Display Selected File");
    return;
}

$filedata = explode(':', $A['field_data']);
$filename = $filedata[0];
$realname = $filedata[1];    
if ($CONF_FE['debug']) {
    COM_errorLog("$realname: $filename");
}
$filepath = "{$CONF_FE['uploadpath']}/$filename";

if ($fd = fopen ($filepath, "rb")) {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: inline; filename=\"{$realname}\"");
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    fpassthru($fd);
    fclose ($fd);
} else {
    echo "Error: Cannot Display Selected File, $realname";
    COM_errorLog("Error: Cannot Display Selected File, $realname");
}
?>