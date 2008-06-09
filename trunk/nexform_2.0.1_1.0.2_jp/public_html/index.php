<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code
require_once($_CONF['path'] . 'plugins/nexform/lib-uploadfiles.php');  // Functions for managing uploading of files

$myvars = array('form_id','op','id');
ppGetData($myvars,true);

$fe_errmg = '';    // Form Editor Error Message - if errors occur during form processing

$returnURL = DB_getItem($_TABLES['formDefinitions'],"return_url", "id='{$form_id}'");
if (trim($returnURL) == '') {
    $returnURL = $_CONF['site_url'] . '/index.php';
}


// Check if CAPTCHA Field is enabled and form field exists
if ( function_exists('plugin_itemPreSave_captcha') AND isset($_POST['captcha']) ) {
$str = COM_applyFilter($_POST['captcha']);
list( $rc, $msg )  = CAPTCHA_checkInput( $type, $str );
    if ( $msg != '' ) {
        $retval = COM_siteHeader();
        $retval .= COM_startBlock ($LANG_FEMSG[2], '',
                     COM_getBlockTemplate ('_msg_block', 'header'))
                . $msg
                . COM_endBlock(COM_getBlockTemplate ('_msg_block', 'footer'));
        $retval .= COM_siteFooter();
        echo $retval;
        exit;
    }
}

/* Check to see if testing a post of the form */
if ($_POST['formhandler'] == 'dbsave') {
    $newform = COM_applyFilter($_REQUEST['newform'], true);

    /* Save results to Database */
    if ($newform == 1) {  //this form was already saved by the file uploader ajax
        $result_id = COM_applyFilter($_REQUEST['res_id'], true);
        nexform_dbupdate($form_id, $result_id);
    }
    else {
        nexform_dbsave($form_id);
    }
    /* Update the hit or results counter */
    DB_query("UPDATE {$_TABLES['formDefinitions']} SET responses = responses + 1 WHERE id='$form_id'");
    $completion_msg = DB_getItem($_TABLES['formDefinitions'], 'after_post_text', "id=$form_id");
    if ($completion_msg == '') {
        echo COM_refresh($returnURL . '?msg=1&plugin=nexform');
    }
    else {
        echo COM_refresh($CONF_FE['public_url'] . "/complete.php?id=$form_id");
    }
    exit();

} elseif ($_POST['formhandler'] == 'email') {

    /* Send results via email */
     nexform_emailresults();

    /* Update the hit or results counter */
    DB_query("UPDATE {$_TABLES['formDefinitions']} SET responses = responses + 1 WHERE id='$form_id'");
    $completion_msg = DB_getItem($_TABLES['formDefinitions'], 'after_post_text', "id=$form_id");
    if ($completion_msg == '') {
        echo COM_refresh($returnURL . '?msg=1&plugin=nexform');
    }
    else {
        echo COM_refresh($CONF_FE['public_url'] . "/complete.php?id=$form_id");
    }
    exit();

} elseif ($_POST['formhandler'] == 'email+dbsave') {
    /* Save results to Database */
    $newform = COM_applyFilter($_REQUEST['newform'], true);

    /* Save results to Database */
    if ($newform == 1) {  //this form was already saved by the file uploader ajax
        $result_id = COM_applyFilter($_REQUEST['res_id'], true);
        nexform_dbupdate($form_id, $result_id);
    }
    else {
        nexform_dbsave($form_id);
    }

    /* Send results via email */
     nexform_emailresults();

    /* Update the hit or results counter */
    DB_query("UPDATE {$_TABLES['formDefinitions']} SET responses = responses + 1 WHERE id='$form_id'");
    $completion_msg = DB_getItem($_TABLES['formDefinitions'], 'after_post_text', "id=$form_id");
    if ($completion_msg == '') {
        echo COM_refresh($returnURL . '?msg=1&plugin=nexform');
    }
    else {
        echo COM_refresh($CONF_FE['public_url'] . "/complete.php?id=$form_id");
    }
    exit();

} else {
    if (DB_count($_TABLES['formDefinitions'],'id',$id) == 1) {
        echo COM_siteHeader();
        if (isset($_GET['processid']) and isset($_GET['taskid'])) {
            $parms = array(
                'processid'  =>  $_GET['processid'],
                'taskid'     =>  $_GET['taskid']
            );
            echo nexform_showform($id,0,'view',$parms);
        } else {
            echo nexform_showform($id);
        }
        echo COM_siteFooter();
        exit;
    } else {
        echo COM_refresh($returnURL . '?msg=2&plugin=nexform');
        exit(); 
    }
}

?>