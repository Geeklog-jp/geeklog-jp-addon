<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | import.php                                                                |
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

include ('../../../lib-common.php');
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

function doImport(){
    global $_TABLES, $_CONF;
    
    $_ARR=array();
    $importsql = $_CONF['path_html'] . 'admin/plugins/nexflow/export/nexflow_export.sql';
    require_once($importsql);
    
    //we now have the entire dump of data into a usable array for us to import.
    $sql=$_ARR['template'];
    $res=DB_query($sql);
    if (DB_error()) {
        COM_errorLog("executing " . current($_SQL));
        COM_errorLog("Error executing SQL",1);
        exit;
    }
    $newTemplateID=DB_insertId();

    //now do the variables
    $len=count($_ARR['variables']);
    for($cntr=0;$cntr<$len;$cntr++){
        $sql=$_ARR['variables'][$cntr]['SQL'];   
        $sql=str_replace('{templateID}',$newTemplateID,$sql);
        $res=DB_query($sql);
        if (DB_error()) {
            COM_errorLog("executing " . current($_SQL));
            COM_errorLog("Error executing SQL",1);
            exit;
        }
        $insertid=DB_insertId();
        $_ARR['variables'][$cntr]['newid']=$insertid;
    }

    //now do the template data
    $len=count($_ARR['templatedata']);
    for($cntr=0;$cntr<$len;$cntr++){
        $sql=$_ARR['templatedata'][$cntr]['SQL'];   
        $sql=str_replace('{templateID}',$newTemplateID,$sql);
        $pattern = '/{argumentvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{argumentvariable:'",'', $_ARR);
        $pattern = '/{dynamicformvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{dynamicformvariable:'",'', $_ARR);
        $pattern = '/{dynamictasknamevariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{dynamictasknamevariable:'",'', $_ARR);
        $res=DB_query($sql);
        if (DB_error()) {
            COM_errorLog("executing " . current($_SQL));
            COM_errorLog("Error executing SQL",1);
            exit;
        }
        $insertid=DB_insertId();
        $_ARR['templatedata'][$cntr]['newid']="{$insertid}";
    }

    //now do the nextStep values
    $len=count($_ARR['nextstep']);
    for($cntr=0;$cntr<$len;$cntr++){
        $sql=$_ARR['nextstep'][$cntr]['SQL'];   
        $pattern = '/{from:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{from:'",'templatedataid', $_ARR);
        $pattern = '/{to:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{to:'",'templatedataid', $_ARR);
        $pattern = '/{false:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{false:'",'templatedataid', $_ARR);
        $res=DB_query($sql);
        if (DB_error()) {
            COM_errorLog("executing " . current($_SQL));
            COM_errorLog("Error executing SQL",1);
            exit;
        }
        $insertid=DB_insertId();
        $_ARR['nextstep'][$cntr]['newid']="{$insertid}";
    }

    //now do the assignment values
    $len=count($_ARR['assignments']);
    for($cntr=0;$cntr<$len;$cntr++){
        $sql=$_ARR['assignments'][$cntr]['SQL'];   
        $pattern = '/{templatedataid:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{templatedataid:'",'templatedataid', $_ARR);
        $pattern = '/{processvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{processvariable:'",'', $_ARR);
        $pattern = '/{prenotifyvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{prenotifyvariable:'",'', $_ARR);
        $pattern = '/{postnotifyvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{postnotifyvariable:'",'', $_ARR);
        $pattern = '/{remindernotifyvariable:[^}]*./';
        $sql=performVariableMatch($sql,$pattern,"{remindernotifyvariable:'",'', $_ARR);
        $res=DB_query($sql);
        if (DB_error()) {
            COM_errorLog("executing " . current($_SQL));
            COM_errorLog("Error executing SQL",1);
            exit;
        }
        $insertid=DB_insertId();
        $_ARR['assignments'][$cntr]['newid']="{$insertid}";
    }
   
}


function performVariableMatch($originalString,$pattern,$patternPrefix,$mode='variables', $_ARR){
    if($mode=='') {
        $mode='variables';
    }
    preg_match($pattern, $originalString, $matches, PREG_OFFSET_CAPTURE);
    $replacestr=$matches[0][0];
    $tempstr=str_replace($patternPrefix,"",$replacestr);
    $tempstr=str_replace("'}","",$tempstr);
    switch($mode){
        case 'variables':
            $tempval=NXCOM_filterInt(fetchNewVariableID($tempstr, $_ARR));
        break;
        
        case 'templatedataid':   
            $tempval=NXCOM_filterInt(fetchNewTemplateDataID($tempstr,$_ARR));
        break;
    }
    $retval=str_replace($replacestr,"'" . $tempval . "'",$originalString);
    return $retval;
}

function fetchNewVariableID($oldid, $_ARR){
    $len=count($_ARR['variables']);
    for($cntr=0;$cntr<$len;$cntr++){
        if($_ARR['variables'][$cntr]['origid']==$oldid){
            return $_ARR['variables'][$cntr]['newid'];
        }
    }
}

function fetchNewTemplateDataID($oldid, $_ARR){
    $len=count($_ARR['templatedata']);
    for($cntr=0;$cntr<$len;$cntr++){
       // echo $_ARR['templatedata'][$cntr]['origid'] . " - " . $oldid . " - " . $_ARR['templatedata'][$cntr]['newid'] . "<HR>";
        if($_ARR['templatedata'][$cntr]['origid']==$oldid){
            return $_ARR['templatedata'][$cntr]['newid'];
        }
    }
}



//MAIN CODE

if (!isset($_POST['op']) AND $_POST['op'] != 'import') {
    echo COM_siteHeader();
    echo COM_startBlock('nexFlow - Import template');

    $navbar = new navbar;
    $navbar->add_menuitem('My Tasks',$CONF_NF['TaskConsole_URL'] );
    if(SEC_hasRights('nexflow.admin')) {
        $navbar->add_menuitem('View Templates',$_CONF['site_admin_url'] .'/plugins/nexflow/templates.php');
        $navbar->add_menuitem('Edit Handlers',$_CONF['site_admin_url'] .'/plugins/nexflow/handlers.php');
        $navbar->add_menuitem('Import Template',$_CONF['site_admin_url'] .'/plugins/nexflow/import.php');
        $navbar->set_selected('Import Template');
    }
    $p = new Template($_CONF['path_layout'] . 'nexflow/admin');
    $p->set_file ('page' ,'import.thtml');
    $p->set_var('navbar', $navbar->generate());

    $p->set_var ('helpmsg','Upload the exported nexflow template that you want to import.');
    

    $action_url = $_CONF['site_admin_url'] .'/plugins/nexflow/import.php';
    $p->set_var('action_url',$action_url);
    $p->parse ('output', 'page');
    echo $p->finish ($p->get_var('output'));
    echo COM_endBlock();
    echo COM_siteFooter();

} else {

   if (strlen($_FILES['sqlfile']['name']) > 0) {
        include_once($_CONF['path_system'] . 'classes/upload.class.php');
        $upload = new upload();
        $upload->setPath($_CONF['path_html'] . 'admin/plugins/nexflow/export');
        $upload->setPerms( $CONF_NF['fileperms'] );
        $upload->setAllowedMimeTypes(array('text/plain' => '.phps,.php,.txt,.sql'));
        $upload->setFileNames('nexflow_export.sql');
        $upload->uploadFiles();
        if ($upload->areErrors()) {
            $message = 'Upload Error: ' . $upload->printErrors(false);
            COM_errorLog($message);
        } else {

            // Successfully uploaded file that has the import form SQL
            // The first SQL record is for the new form defintion

            doImport();

            echo COM_refresh($_CONF['site_admin_url'] .'/plugins/nexflow/templates.php');
            exit;
        }
   } else {
       echo "<br>Error - no file";
   }

}


?>