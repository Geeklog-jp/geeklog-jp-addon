<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
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

$langfile = $_CONF['path'] . 'plugins/nexpro/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexpro/language/english.php');
}
require_once ($_CONF['path'] . 'plugins/nexpro/config.php');
require_once ($_CONF['path_system'] . 'lib-portalparts.php');
require_once ($_CONF['path_system'] . 'lib-nextide.php');
require_once ($_CONF['path_html'] . 'jscalendar/calendar.php');

/**
* Returns the needed link to the menu csss- which will be included in the header
*/
function plugin_getHeaderCode_nexpro() {
    global $_CONF, $CONF_NEXPRO;

    $code = "\n\n" .'<!-- Begin Nextide nexpro -->' ."\n";

    if ($CONF_NEXPRO['load_treemenu']) {
        $code .= '<script src="' . $_CONF['site_url'] . '/javascript/TreeMenu.js" language="JavaScript" type="text/javascript"></script>';
    }

    if ($CONF_NEXPRO['load_yui']) {
        $code .= "\n\n<!-- Load Yahoo UI Javascript Libraries -->\n";
        $code .= '<link type="text/css" rel="stylesheet" href="' . $_CONF['site_url'] . '/javascript/yui/fonts/fonts.css">' .LB;
        $code .= '<link type="text/css" rel="stylesheet" href="' . $_CONF['site_url'] . '/javascript/yui/calendar/assets/calendar.css">' . LB;
        $code .= '<link rel="stylesheet" type="text/css" href="' . $_CONF['site_url'] . '/javascript/yui/container/assets/container.css">' . LB;
        $code .= '<link type="text/css" rel="stylesheet" href="' . $_CONF['site_url'] . '/javascript/yui/menu/assets/menu.css">' .LB;

        $code .= ' <script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/yahoo/yahoo.js"></script>' . LB;

        if ($CONF_NEXPRO['load_yui_dom']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/dom/dom.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_event']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/event/event.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_container']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/container/container.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_calendar']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/nexYUICal.js"></script>'. LB;
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/calendar/calendar.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_menu']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/menu/menu.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_animation']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/animation/animation.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_connection']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/connection/connection.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_dragdrop']) {
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/yui/dragdrop/dragdrop.js"></script>' . LB;
        }

        $code .= "<!-- End of Yahoo UI libraries -->\n";
    }

    if ($CONF_NEXPRO['load_sarissa']) {
        $code .= "\n<!-- Load Sarissa Library for AJAX Support  -->\n";
        $code .= '<script language=JavaScript src="' . $_CONF['site_url'] . '/javascript/sarissa.js" type="text/javascript"></script>' . LB;
    }

    if ($CONF_NEXPRO['load_calendar']) {
        $code .= "\n<!-- Begin PopUp Calendar Javascript includes -->\n";
        $calendar = new DHTML_Calendar($_CONF['site_url'] . '/jscalendar/', 'en', 'calendar-system', false);
        $code .= $calendar->load_files();
        $code .= "\n". '<!-- End PopUp Calendar Javascript includes -->' ."\n";
    }

    if ($CONF_NEXPRO['load_fvalidate']) {
        $code .= "\n" .'<!-- Begin fValifdate Javascript includes for nexform Plugin -->';
        $code .= "\n" .'<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/jsval.js"></script>';
        $code .= "\n". '<!-- End fValifdate Javascript includes for nexform Plugin -->' ."\n";
    }

    $code .= '<!-- End Nextide Nexpro -->' ."\n\n";
    return $code;
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexpro() {
    global $CONF_NEXPRO;
    return $CONF_NEXPRO['version'];
}

function plugin_templatesetvars_nexpro($type,&$template) {
    global $_TABLES,$_CONF,$CONF_NEXMENU;

    if ($type == 'header') {
        if ($_COOKIE['leftblocksmode'] == 'none') {
            $template->set_var( 'showhide', '<img src="'.$_CONF['layout_url'].'/images/showleftblocks.gif" onClick="toggleleftblocks(this);" ALT="Click to show Left Block" TITLE="Click to show Left Blocks" border="0">' );
        } else {
            $template->set_var( 'showhide', '<img src="'.$_CONF['layout_url'].'/images/hideleftblocks.gif" onClick="toggleleftblocks(this);" ALT="Click to hide Left Block" TITLE="Click to hide Left Blocks" border="0">' );
        }
        $template->set_var( 'leftblockmode', $_COOKIE['leftblocksmode']);

        if (!isset($CONF_NEXMENU)) $template->set_var('nexmenu','');

        if (file_exists("{$_CONF['path_layout']}/header.js")) {
            $code = '<script type="text/javascript">' . LB;
            $code .= '  var layout_url = "'. $_CONF['layout_url'] .'";' . LB;
            $code .= '</script>' .LB;
            $code .=  '<script type="text/javascript" src="'.$_CONF['layout_url'] .'/header.js"></script>';
            $template->set_var('header_javascript',$code);
        } else {
            $template->set_var('header_javascript','');
        }

    }
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexpro($installCheck='')
{
    global $_TABLES, $LANG_nexpro00;

    $pi_name='nexpro';

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_nexpro00['warning']);
        $display .= $LANG_nexpro00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the nexpro plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nexpro'",1);

    COM_errorLog('...success',1);
    return true;
}

function plugin_upgrade_nexpro() {
    global $_TABLES,$CONF_NEXPRO;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexpro'");

    switch ($curversion) {
    case "2.0.0":
        if (upgrade_201() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.0.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexpro' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexpro'") == $CONF_NEXPRO['version']) {
        return 11;
    } else {
        return 12;
    }
}

function plugin_chkPermissions_nexpro(&$template) {
    global $CONF_NEXPRO;

    if (!$file = @fopen ($CONF_NEXPRO['fckeditor_upload_dir'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_NEXPRO['fckeditor_upload_dir'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_NEXPRO['fckeditor_upload_dir'] . 'test.txt');
    }
}

/**
* Called on User Login to Add Any Plugin Specific User Details
*/
function plugin_user_login_nexpro($uid) {
    global $_CONF, $CONF_NEXPRO, $_TABLES;
    $username = DB_getItem($_TABLES['users'], 'username', "uid=$uid");

    //check to see if this is an ldap user, if so, populate the fullname
    $authtype = DB_getItem($_TABLES['users'], 'remoteservice', "uid=$uid");
    if ($authtype == 'LDAP') {
        //get the user's fullname from the ldap

        //connect to ldap server
        $ds=ldap_connect($CONF_NEXPRO['ldap_server']);

        if ($ds) { //if we connected
            if ($CONF_NEXPRO['ldap_type'] == 'Active Directory') {
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION,3);
                ldap_set_option($ds, LDAP_OPT_REFERRALS,0);
            }

            foreach ($CONF_NEXPRO['ldap_ous'] as $ou) {
                $bind_string = sprintf($CONF_NEXPRO['ldap_bind_string'], $CONF_NEXPRO['ldap_username'], $ou);

                // bind with appropriate dn to give update access
                $r=@ldap_bind($ds, $bind_string, $CONF_NEXPRO['ldap_password']);
                if ($r!==FALSE) {
                    //authenticated successfully
                    //now search through the ldap for an email address
                    $search_string = sprintf($CONF_NEXPRO['ldap_search_string'], $username);
                    foreach ($CONF_NEXPRO['ldap_ous'] as $lou) {
                        $organization_string = sprintf($CONF_NEXPRO['ldap_organization_string'], $lou);
                        $sr=ldap_search($ds, $organization_string, $search_string);
                        $count = ldap_count_entries($ds, $sr);

                        if ($count > 0) {
                            $info = ldap_get_entries($ds, $sr);
                            $fullname = $info[0][$CONF_NEXPRO['fullname_parm']][0];
                            ldap_close($ds);

                            DB_query("UPDATE {$_TABLES['users']} SET fullname='$fullname' WHERE uid=$uid");
                            return;
                        }
                    }
                    //if we reach this point in the code that means we weren't able to find a fullname
                    return;
                }
            } //end foreach $CONF_NEXPRO['ldap_ous']
            //if we reach this point in the code that means we weren't able to bind
            ldap_close($ds);

            return;
        }
        else {
            return;
        }//end if $ds
    }
}

function plugin_user_create_nexpro($uid){
    global $_CONF, $_TABLES,$CONF_NEXPRO;
    if($CONF_NEXPRO['enable_remote_service_set']){
            //we've been told to set the remote service.
            //only do this if this is a NEW user by determining if the last logged in date is not set
            $lastlogin=DB_getItem($_TABLES['userinfo'],"lastlogin","uid={$uid}");
            $username=DB_getItem($_TABLES['users'],"username","uid={$uid}");
            if(($lastlogin==0 || $lastlogin=='') && $CONF_NEXPRO['enable_remote_service_set']){//new!  do the ldap force here
                $sql ="UPDATE {$_TABLES['users']} set status=3,remoteusername='{$username}',remoteservice='{$CONF_NEXPRO['new_user_remote_service']}' where uid={$uid}";
                DB_query($sql);
                $remUsers = DB_getItem ($_TABLES['groups'], 'grp_id',"grp_name = 'Remote Users'");
                $sql = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid) VALUES ($remUsers, $uid)";
                DB_query ($sql);
            }
        }

}
function plugin_user_changed_nexpro ($uid) {
    global $_CONF, $_TABLES,$CONF_NEXPRO;

    //first check to see if this is an ldap user
    $authtype = DB_getItem($_TABLES['users'], 'remoteservice', "uid=$uid");
    if ($authtype == 'LDAP') {
        require_once ($_CONF['path_system'] . 'classes/authentication/LDAP.auth.class.php');
        if ($_POST['passwd'] == $_POST['passwd_conf']) {
            $md5passwd = md5($_POST['passwd']);
            $username = DB_getItem($_TABLES['users'], 'username', "uid=$uid AND passwd='$md5passwd'");

            if ($username != '') {  //if username IS '' then the old password does not match the current password
                $authclass = new LDAP();
                $authclass->set_password($username, $_POST['passwd'], $_POST['old_passwd']);
            }
        }
    }else{  //LDAP is not set...
        //ok, we need to see what the system settings are first
        if($CONF_NEXPRO['enable_remote_service_set']){

            //we've been told to set the remote service.
            //only do this if this is a NEW user by determining if the last logged in date is not set
            $lastlogin=DB_getItem($_TABLES['userinfo'],"lastlogin","uid={$uid}");
            if(($lastlogin==0 || $lastlogin=='') && $CONF_NEXPRO['enable_remote_service_set']){//new!  do the ldap force here
                $sql ="UPDATE {$_TABLES['users']} set status=3,remoteusername='{$username}',remoteservice='{$CONF_NEXPRO['new_user_remote_service']}' where uid={$uid}";
                DB_query($sql);
                $remUsers = DB_getItem ($_TABLES['groups'], 'grp_id',"grp_name = 'Remote Users'");
                $sql = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid) VALUES ($remUsers, $uid)";
                DB_query ($sql);
            }
        }



    }
    return;
}

?>