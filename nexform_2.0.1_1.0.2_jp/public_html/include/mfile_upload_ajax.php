<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | mfile_upload_ajax.php                                                     |
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

require_once('../../lib-common.php');
require_once($_CONF['path'] . 'plugins/nexform/lib-uploadfiles.php');
if (isset($_GET['action']) AND $_GET['action'] != '') {
    $action = COM_applyFilter($_GET['action']);
} else {
    $action = COM_applyFilter($_POST['action']);
}

function upload_file() {
    global $CONF_FE, $_TABLES, $GLOBALS, $_CONF;

    //upload the file
    $field_name = COM_applyFilter($_POST['current_upload_file']);
    $result_id = COM_applyFilter($_POST['res_id'], true);
    $form_id = COM_applyFilter($_POST['form_id'], true);
    $uploadfile = $_FILES[$field_name];
    $fieldID = COM_applyFilter($_REQUEST['field_id'], true);
    if ($result_id == 0) {  //form has not been saved yet
        $result_id = nexform_dbsave($form_id, 0, false);
    }
    if (($rec = nexform_check4files($result_id, $field_name)) != 0) {
        $retval = '';
        $retval .= "&nbsp;<a href=\"{$CONF_FE['public_url']}/download.php?id=$rec\" target=\"_new\">";
        $retval .= "<img src=\"{$CONF_FE['image_url']}/document_sm.gif\" border=\"0\">{$uploadfile['name'][0]}</a>&nbsp;";
        $edit_group = DB_getItem($_TABLES['formDefinitions'],'perms_edit',"id='$form_id'");
        if (SEC_inGroup($edit_group)) {
            $retval .= "<a href=\"#\" onClick='ajaxDeleteFile($fieldID,$rec,\"$field_name\"); return false;'>";
            $retval .= "<img src=\"{$CONF_FE['image_url']}/delete.gif\" border=\"0\"></a>&nbsp;";
        }
        $iserror = 'false';
    } else {
        //COM_fileLog("upload error:" . $GLOBALS['fe_errmsg']);
        $errmsg = $GLOBALS['fe_errmsg'];
        $err_fieldname = 'error_' . ppRandomFilename();
        $retval = '';
        if ($errmsg == '') {
            $errmsg = 'Your file could not be uploaded.';
        }
        $retval .= "<table id=\"tbl_{$err_fieldname}\"><tr id=\"{$err_fieldname}\"><td><img src=\"{$_CONF['layout_url']}/nexform/images/error.gif\"></td><td>$errmsg<br><center><font size=\"1\"><a href=\"#\" onClick=\"ajaxClearErrorMessage('{$err_fieldname}'); return false;\">[ Clear Message ]</a></font></center></td></tr></table>";
        $iserror = 'true';
    }

    return array($retval, $fieldID, $field_name, $form_id, $result_id, $iserror);
}

if (function_exists($action)) {
    $retval_array = $action();
    $html = $retval_array[0];
    $fieldID = $retval_array[1];
    $field_name = ($retval_array[2] != '') ? $retval_array[2]:' ';
    $formid = $retval_array[3];
    $resultid = $retval_array[4];
    if ($field_name == ' ') {
        $iserror = 'true';
    }
    else {
        $iserror = $retval_array[5];
    }
    if ($html != '') {
        $error = '&nbsp;';
    }
    else {
        $html = '&nbsp;';
        $error = 'invalid filename';
    }
}
else {
    $error = 'invalid action';
}

$html = htmlspecialchars($html);
$error = htmlspecialchars($error);
$retval = "<result>";
$retval .= "<status>200</status>";
$retval .= "<error>$error</error>";
$retval .= "<action>$action</action>";
$retval .= "<html>$html</html>";
$retval .= "<fieldid>$fieldID</fieldid>";
$retval .= "<formid>$formid</formid>";
$retval .= "<resultid>$resultid</resultid>";
$retval .= "<iserror>$iserror</iserror>";
$retval .= "<inputname>$field_name</inputname>";
$retval .= "</result>";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo $retval;
?>
