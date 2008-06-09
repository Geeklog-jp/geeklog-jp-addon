<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | complete.php                                                              |
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

$fid = intval($_GET['id']);

echo COM_siteHeader();

$sql = "SELECT after_post_text FROM {$_TABLES['formDefinitions']} WHERE id=$fid;";
$q = DB_query($sql);
$res = DB_fetchArray($q);

if ($res != '') {
    echo COM_startBlock($PLG_nexform_MESSAGE1, '', 'blockheader-message.thtml');
    echo $res['after_post_text'];
    echo COM_endBlock('blockfooter-message.thtml');
}
else {
    echo COM_startBlock('An Error Has Occurred', '', 'blockheader-message.thtml');
    echo 'No Form Record Found';
    echo COM_endBlock('blockfooter-message.thtml');
}

echo COM_siteFooter();
?>
