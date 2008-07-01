<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | print.php                                                                 |
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

$myvars = array('id','op','style','result','epm');
ppGetData($myvars,true);

//$epm stands for edit print mode.  It is passed in from the url so we know
//that this print request came from an edit mode of the form, rather than the
//view mode.  If $epm is 1, then we want to store the results to the database
//in a temporary table, where and then we can pull them back for the print view,
//just as we would have normally.  This makes it simple for an edit-print as we
//modify the table names temporarily, so uses the temp tables.  This way the main
//function does all the same work as it would have done in the first place
if ($epm == 1) {
    $_TABLES['formResults'] .= '_tmp';
    $_TABLES['formResData'] .= '_tmp';
    $_TABLES['formResText'] .= '_tmp';
}

require_once($_CONF['path'] . 'plugins/nexform/library.php');  // Main nexform functions library
require_once($_CONF['path'] . 'plugins/nexform/lib-uploadfiles.php');  // Functions for managing uploading of files
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code


$view_group = DB_getItem($_TABLES['formDefinitions'],'perms_view',"id='$id'");
if (!SEC_inGroup($view_group)) {
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

$LANG_NAVBAR = $LANG_FRM_ADMIN_NAVBAR;
$formname = DB_getItem($_TABLES['formDefinitions'],'name',"id='$id'");

if ($epm == 1) {
    $resid = nexform_dbsave($id);
    $_GET['result'] = $resid;
    $result = $resid;
}

$report_results = "<link rel=\"stylesheet\" href=\"{$_CONF['layout_url']}/style.css\">\n";
$report_results .= nexform_showform($id,$result,'print','','',$style);
//now we can delete from the temporary tables now that we are done displaying them.
if ($epm == 1) {
    $tmpres = DB_getItem($_TABLES['formResults'], 'related_results', "id=$resid");
    DB_query("DELETE FROM {$_TABLES['formResults']} WHERE id=$resid;");
    DB_query("DELETE FROM {$_TABLES['formResData']} WHERE result_id=$resid;");
    DB_query("DELETE FROM {$_TABLES['formResText']} WHERE result_id=$resid;");
    
    if ($tmpres != '') {
        $resids = explode(',', $tmpres);            
        foreach ($resids as $resid) {
            DB_query("DELETE FROM {$_TABLES['formResults']} WHERE id=$resid;");
            DB_query("DELETE FROM {$_TABLES['formResData']} WHERE result_id=$resid;");
            DB_query("DELETE FROM {$_TABLES['formResText']} WHERE result_id=$resid;");
        }
    }
}

echo $report_results;

?>