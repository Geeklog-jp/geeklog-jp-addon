<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | import.php                                                                |
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

include ('../../../lib-common.php');
$importsql = $_CONF['path_html'] . 'admin/plugins/nexform/export/importform_data.sql';
$LANG_NAVBAR = $LANG_FRM_ADMIN_NAVBAR;

function nexform_importForm($_SQL,$cntr) {
    global $CONF_FE,$_TABLES;

    DB_query($_SQL[0],'1');
    if (DB_error ()) {
        COM_errorLog("nexform SQL error importing form: {$_SQL[0]}");
    }
    $newformid = DB_insertID();

    /* Delete any previous imported form field definition records 
       New field definition records will have a formid of '99999' assigned
       Insert the new records and then update to match the new form definition
    */

    DB_query("DELETE FROM {$_TABLES['formFields']} WHERE formid='$cntr'");
    next($_SQL);  // Increment to the field definition records

    for ($i = 1; $i < count($_SQL); $i++) {
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("executing " . current($_SQL));
            COM_errorLog("Error executing SQL",1);
            exit;
        }
        next($_SQL);
    }

    DB_query("UPDATE {$_TABLES['formFields']} set formid='$newformid' WHERE formid='$cntr'");

    // Need to cycle thru the fields now and update any fieldnames if auto fieldname used
    $query = DB_query("SELECT id,type FROM {$_TABLES['formFields']} WHERE formid='$newformid' AND field_name LIKE '%_frm%'");
    while (list ($fieldid, $fieldtype) = DB_fetchArray($query)) {
       $fieldname = "{$CONF_FE['fieldtypes'][$fieldtype][0]}{$newformid}_{$fieldid}";
       DB_query("UPDATE {$_TABLES['formFields']} set field_name='$fieldname' WHERE id='$fieldid'"); 
    }
}



if (!isset($_POST['op']) AND $_POST['op'] != 'import') {
    echo COM_siteHeader();
    echo COM_startBlock('nexform - Import new definition');

    require_once ($_CONF['path_system'] . 'classes/navbar.class.php');
    $navbar = new navbar();
    $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
    $navbar->add_menuitem($LANG_NAVBAR['2'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=add');
    $navbar->add_menuitem($LANG_NAVBAR['12'], $_CONF['site_admin_url'] .'/plugins/nexform/import.php');
    $navbar->set_selected($LANG_NAVBAR['12']);

    $p = new Template($_CONF['path_layout'] . 'nexform/admin');
    $p->set_file ('page' ,'import.thtml');
    $p->set_var ('alertmsg','');
    $p->set_var ('show_alert','none');
    $p->set_var ('show_msg','');
    $p->set_var ('helpmsg','Upload the exported form definition that you want to import.');

    $p->set_var ('navbar',$navbar->generate());

    $action_url = $_CONF['site_admin_url'] .'/plugins/nexform/import.php';
    $p->set_var('action_url',$action_url);
    $p->parse ('output', 'page');
    echo $p->finish ($p->get_var('output'));
    echo COM_endBlock();
    echo COM_siteFooter();

} else {

   if (strlen($_FILES['sqlfile']['name']) > 0) {
        include_once($_CONF['path_system'] . 'classes/upload.class.php');
        $upload = new upload();
        $upload->setPath($_CONF['path_html'] . 'admin/plugins/nexform/export');
        $upload->setPerms( $CONF_FE['fileperms'] );
        $upload->setAllowedMimeTypes(array(
                'text/plain' => '.phps, .php, .txt, .sql',
                'application/octet-stream' => '.sql'));
        $upload->setFileNames('importform_data.sql');
        $upload->uploadFiles();
        if ($upload->areErrors()) {
            $message = 'Upload Error: ' . $upload->printErrors(false);
            echo COM_siteHeader();
            echo COM_startBlock('Upload Error');
            echo $message;
            echo COM_endBlock();
            echo COM_siteFooter();
            COM_errorLog($message);
            exit;
        } else {

            // Successfully uploaded file that has the import form SQL
            // The first SQL record is for the new form defintion

            require_once($importsql);
            $formCnt = count($_SQL);
            for ($i = 1; $i <= $formCnt; $i++) {
                $id = "900{$i}";
                nexform_importForm($_SQL[$id],$id);
            }

            echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexform/index.php');
            exit;
        }
   } else {
       echo "<br>Error - no file";
   }

}

?>