<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
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

$langfile = $_CONF['path'] . 'plugins/nexform/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexform/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexform/config.php');
require_once ($_CONF['path'] . 'plugins/nexform/library.php');

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexform()
{
    global $_TABLES, $_CONF,$LANG_FE01;

    if (SEC_hasRights('nexform.edit')) {
    $numrecords = DB_getITEM($_TABLES['formDefinitions'],"count(*)");
    return array($LANG_FE01['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexform/index.php', $numrecords);
    }

}

function plugin_cclabel_nexform()
{
    global $_CONF, $LANG_FE01;
    if (SEC_hasRights('nexform.edit')) {
        return array($LANG_FE01['adminmenupanel'],"{$_CONF['site_admin_url']}/plugins/nexform/index.php",plugin_geticon_nexform());
    }
}


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexform()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexform/images/admin/nexform.gif';
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexform()
{
    global $_CONF, $CONF_FE;

    $code ='';
    $code = "\n\n<!-- Begin nexform  -->\n";
    $code .= LB . '<script language=JavaScript src="' . $CONF_FE['public_url'] . '/include/nexform.js" type="text/javascript"></script>';

    if ($CONF_FE['load_sarissa']) {
        $code .= LB . '<script language=JavaScript src="' . $CONF_FE['public_url'] . '/include/sarissa.js" type="text/javascript"></script>' . LB;
    }
    if ($CONF_FE['load_editor']) {
        $code .= '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'] .'/fckeditor/css/fck_editorarea.css">' .LB;
        $code .= LB . '<script type="text/javascript" src="' . $_CONF['site_url'] . '/fckeditor/fckeditor.js" type="text/javascript"></script>';
    }
    if ($CONF_FE['load_fValidate']) {
        $code .= "\n" .'<!-- Begin fValifdate Javascript includes for nexform Plugin -->';
        $code .= "\n" .'<script type="text/javascript" src="' . $CONF_FE['public_url'] . '/include/jsval.js"></script>';
        $code .= "\n". '<!-- End fValifdate Javascript includes for nexform Plugin -->' ."\n";
    }
    if ($CONF_FE['load_calendar']) {
        $code .= "\n<!-- Begin PopUp Calendar Javascript includes -->\n";
        $calendar = new DHTML_Calendar($_CONF['site_url'] . '/jscalendar/', 'en', 'calendar-system', false);
        $code .= $calendar->load_files();

        /* Blaine - Jan 12/07 Testing with the zapatec version but could not get it working
        $code .= LB . '<link href="'. $_CONF['site_url'] .'/zapatec/zpcal/themes/aqua.css" rel="stylesheet" type="text/css">';
        $code .= LB . '<script type="text/javascript" src="'. $_CONF['site_url'] .'/zapatec/utils/zapatec.js"></script>';
        $code .= LB . '<script type="text/javascript" src="'. $_CONF['site_url'] .'/zapatec/zpcal/src/calendar.js"></script>';
        $code .= LB . '<script type="text/javascript" src="'. $_CONF['site_url'] .'/zapatec/zpcal/lang/calendar-en.js"></script>';
        */

        $code .= "\n". '<!-- End PopUp Calendar Javascript includes -->' ."\n";
    }
    $code .= "\n<script type=\"text/javascript\" src=\"{$CONF_FE['public_url']}/include/ajax_deletemfile_support.js\"></script>";
    $code .= "\n<!-- End nexform -->\n\n";
    return $code;
}


/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexform() {
    global $CONF_FE;
    return $CONF_FE['version'];
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexform($installCheck='')
{
    global $_TABLES, $LANG_GCA1;

    $pi_name='nexform';
    $FEATURES = array ('nexform.edit');
    $TABLES = array ('formDefinitions','formFields','formResults','formResData','formResText','formResultsTmp','formResDataTmp','formResTextTmp');

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_GCA1['warning']);
        $display .= $LANG_GCA1['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog("Attempting to unregister the $pi_name= plugin from Geeklog",1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = '$pi_name'",1);
    // Drop Plugun tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Admin Security for this plugin
    // name of the Admin group
    $pi_admin        = "{$pi_name} Admin";
    COM_errorLog("Removing $pi_admin Group", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_name = '$pi_admin'",1);
    COM_errorLog("Removing root users from admin of $pi_name");
    DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id",1);

    // Remove all the associated features - access rights. The feature ID's were stored in the vars table during install.
    foreach ($FEATURES as $feature) {
        COM_errorLog("Removing $feature feature and rights to it",1);
        $feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
        COM_errorLog("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id");
        DB_query("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id",1);
        DB_query("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id",1);
    }

    COM_errorLog('...success',1);
    return true;
}

function plugin_upgrade_nexform() {
    global $_TABLES,$CONF_FE;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexform'");

    switch ($curversion) {
    case "2.0.0":
        if (upgrade_201() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.0.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexform' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexform'") == $CONF_FE['version']) {
        return 11;
    } else {
        return 12;
    }
}



function plugin_autotags_nexform($op,$content='',$tag='') {
    global $_CONF,$_TABLES;

    if ($op == 'tagname' ) {
        return 'form';
    } elseif ($op == 'parse') {
       if (DB_count($_TABLES['formDefinitions'],'id',$tag['parm1']) == 1) {
           // Set the option to not show the print links with the form
           $parms = array(
                'noprint' => true
           );
           $formHTML = nexform_showform($tag['parm1'],0,'view',$parms);
           $retval = str_replace($tag['tagstr'],$formHTML,$content);
           return $retval;
        } else {
           return $content;
        }
    }
}

// Example of a custom function that could be used to show a form in a block
function phpblock_sampleform() {
    return nexform_showform(1);
}


// function to test permissions on directories
function plugin_chkPermissions_nexform(&$template) {
    global $CONF_FE;

    $display .= '<p>Testing <b>nexForm data</b> directory ' . $CONF_FE['uploadpath'] . ' ...<br>' . LB;
    if (!$file = @fopen ($CONF_FE['uploadpath'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_FE['uploadpath'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_FE['uploadpath'] . 'test.txt');
    }

    $display .= '<p>Testing <b>nexForm export</b> directory ' . $CONF_FE['export_dir'] . ' ...<br>' . LB;
    if (!$file = @fopen ($CONF_FE['export_dir'] . 'test.txt', 'w')) {
        $display .= '<font color="#ff0000">Could not write to <b>' . $CONF_FE['export_dir'] . '</b>.</font><br>Please make sure this directory exists and is set to <b>chmod 777</b>.<br>' . LB;
        $display .= 'Current permissions for <b>nexForm export</b>: ' . sprintf ("%3o", @fileperms ($CONF_FE['export_dir']) & 0777);
        $failed++;
    } else {
        fclose ($file);
        unlink ($CONF_FE['export_dir'] . 'test.txt');
        $display .= '<b>nexForm export</b> directory is okay.' . LB;
        $successful++;
    }

    return $display;
}


?>