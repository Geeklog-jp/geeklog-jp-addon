<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

require_once ('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/nexproject/config.php');
require_once ($_CONF['path'] . 'plugins/nexproject/functions.inc');

// Plugin information
$pi_display_name = 'nexProject';
$pi_name         = 'nexproject';
$pi_version      = $_PRJCONF['version'];
$gl_version      = '1.4.1';
$pi_url          = 'http://www.nextide.ca/';


// name of the Admin group
$pi_admin        = $pi_display_name . ' Admin';

// the plugin's groups - assumes first group to be the Admin group
$GROUPS = array();
//We assign rights and priviledges though the application in this instance.... Thus the admin group is not required
//root users have overall access to the plugin - project managers have per-project rights.

$FEATURES = array();
//we don't need any special privilidges as again, all assignments are done in the app!


$MAPPINGS = array();


// (optional) data to pre-populate tables with
// Insert table name and sql to insert default data for your plugin.
// Note: '#group#' will be replaced with the id of the plugin's admin group.
$DEFVALUES = array();
$fields = "is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon";
$values = "'0','projectFilter','phpblock','Projects Filter','all',0,0,'phpblock_projectFilter',2,2,3,3,2,2";
$DEFVALUES['blocks'] =  "INSERT INTO {$_TABLES['blocks']} ($fields) VALUES ($values)";

// more default data is in the install SQL file in the plugin's directory

/**
* Checks the requirements for this plugin and if it is compatible with this
* version of Geeklog.
*
* @return   boolean     true = proceed with install, false = not compatible
*
*/
function plugin_compatible_with_this_geeklog_version ()
{
    if (function_exists ('COM_showPoll') || function_exists ('COM_pollVote')) {
        // if these functions exist, then someone's trying to install the
        // plugin on Geeklog 1.3.11 or older - sorry, but that won't work
        return false;
    }

    if (!function_exists ('SEC_getGroupDropdown')) {
        return false;
    }

    return true;
}

/**
* When the install went through, give the plugin a chance for any
* plugin-specific post-install fixes
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_postinstall ()
{
    global $_CONF, $_TABLES;

    // fix nexproject block group ownership
    $blockAdminGroup = DB_getItem ($_TABLES['groups'], 'grp_id',
                                   "grp_name = 'Block Admin'");
    if ($blockAdminGroup > 0) {
        // set the block's permissions
        $A = array ();
        SEC_setDefaultPermissions ($A, $_CONF['default_permissions_block']);

        // ... and make it the last block on the right side
        $result = DB_query ("SELECT MAX(blockorder) FROM {$_TABLES['blocks']} WHERE onleft = 0");
        list($order) = DB_fetchArray ($result);
        $order += 10;

        DB_query ("UPDATE {$_TABLES['blocks']} SET group_id = $blockAdminGroup, blockorder = $order, perm_owner = {$A['perm_owner']}, perm_group = {$A['perm_group']}, perm_members = {$A['perm_members']}, perm_anon = {$A['perm_anon']} WHERE (type = 'phpblock') AND (phpblockfn = 'phpblock_nexproject')");

        return true;
    }

    return false;
}

// The code below should be the same for most plugins and usually won't
// require modifications.

$base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';
$langfile = $base_path . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    require_once ($langfile);
} else {
    require_once ($base_path . 'language/english.php');
}
require_once ($base_path . 'config.php');
require_once ($base_path . 'functions.inc');


// Only let Root users access this page
if (!SEC_inGroup ('Root')) {
    // Someone is trying to illegally access this page
    COM_accessLog ("Someone has tried to illegally access the {$pi_display_name} install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);

    $display = COM_siteHeader ('menu', $LANG_ACCESS['accessdenied'])
             . COM_startBlock ($LANG_ACCESS['accessdenied'])
             . $LANG_ACCESS['plugin_access_denied_msg']
             . COM_endBlock ()
             . COM_siteFooter ();

    echo $display;
    exit;
}


/**
* Puts the datastructures for this plugin into the Geeklog database
*
*/
function plugin_install_now()
{
    global $_CONF, $_TABLES, $_USER, $_DB_dbms,
           $GROUPS, $FEATURES, $MAPPINGS, $DEFVALUES, $base_path,
           $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    COM_errorLog ("Attempting to install the $pi_display_name plugin", 1);

    $uninstall_plugin = 'plugin_uninstall_' . $pi_name;

    //so lets test out to see if the nexPro,nexFile, nexList and forum plugins are installed.  If not, bail out with an error
    if (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexpro'") == '') {     //install nexpro first
        COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing', 1);
        echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=1&plugin='.$pi_name);
        exit(0);
    }

    if(!function_exists("nexlistValue")){
        COM_errorLog ('The nexList plugin is not installed.  Please install it before continuing', 1);
        echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=3&plugin='.$pi_name);
        exit(0);
    }
    $nexfile = true;
    if(!function_exists("fm_createCategory")){
        //COM_errorLog ('The nexFile plugin is not installed.  Please install it before continuing', 1);
        //echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=2&plugin='.$pi_name);
        //exit(0);
        $nexfile = false;
    }
    $forum = true;
    if(!function_exists("forum_addForum")){
        //COM_errorLog ('The forum plugin is not installed.  Please install it before continuing', 1);
        //echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=4&plugin='.$pi_name);
        //exit(0);
        $forum = false;
    }



    // create the plugin's groups
    $admin_group_id = 0;
    foreach ($GROUPS as $name => $desc) {
        COM_errorLog ("Attempting to create $name group", 1);

        $grp_name = addslashes ($name);
        $grp_desc = addslashes ($desc);
        DB_query ("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr) VALUES ('$grp_name', '$grp_desc')", 1);
        if (DB_error ()) {
            $uninstall_plugin ();

            return false;
        }

        // replace the description with the new group id so we can use it later
        $GROUPS[$name] = DB_insertId ();

        // assume that the first group is the plugin's Admin group
        if ($admin_group_id == 0) {
            $admin_group_id = $GROUPS[$name];
        }
    }

    // Create the plugin's table(s)
    $_SQL = array ();
    if (file_exists ($base_path . 'sql/' . $pi_name . '_' . $_DB_dbms . '_install_' . $pi_version . '.php')) {
        require_once ($base_path . 'sql/' . $pi_name . '_' . $_DB_dbms . '_install_' . $pi_version . '.php');
    }

    foreach ($_SQL as $sql) {
        $sql = str_replace ('#group#', $admin_group_id, $sql);
        DB_query ($sql, true);
        if (DB_error ()) {
            COM_errorLog ('Error creating table', 1);
            $uninstall_plugin ();

            return false;
        }
    }

    // Add the plugin's features
    COM_errorLog ("Attempting to add $pi_display_name feature(s)", 1);

    foreach ($FEATURES as $feature => $desc) {
        $ft_name = addslashes ($feature);
        $ft_desc = addslashes ($desc);
        DB_query ("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) "
                  . "VALUES ('$ft_name', '$ft_desc')", 1);
        if (DB_error ()) {
            $uninstall_plugin ();

            return false;
        }

        $feat_id = DB_insertId ();

        if (isset ($MAPPINGS[$feature])) {
            foreach ($MAPPINGS[$feature] as $group) {
                COM_errorLog ("Adding $feature feature to the $group group", 1);
                DB_query ("INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($feat_id, {$GROUPS[$group]})");
                if (DB_error ()) {
                    $uninstall_plugin ();

                    return false;
                }
            }
        }
    }

    // Add plugin's Admin group to the Root user group
    // (assumes that the Root group's ID is always 1)
    COM_errorLog ("Attempting to give all users in the Root group access to the $pi_display_name's Admin group", 1);

    DB_query ("INSERT INTO {$_TABLES['group_assignments']} VALUES "
              . "($admin_group_id, NULL, 1)");
    if (DB_error ()) {
        $uninstall_plugin ();

        return false;
    }

    // Pre-populate tables or run any other SQL queries
    COM_errorLog ('Inserting default data', 1);
    foreach ($DEFVALUES as $sql) {
        $sql = str_replace ('#group#', $admin_group_id, $sql);
        DB_query ($sql, 1);
        if (DB_error ()) {
            $uninstall_plugin ();

            return false;
        }
    }

    //And now, install the lookup lists and add nxprj config values to house the nexlist items

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Locations',    'List of locations', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $locID= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values ('all','nexPro','Departments','List of Departments', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $deptID= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values ('all','nexPro', 'Categories','List of Categories', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $catID= DB_insertId();

    $sql = "INSERT INTO {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    VALUES ('all', 'nexPro', 'Objectives', 'List of Project Objectives', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $objID= DB_insertId();

    /* create lookuplist Fields for list definitions */
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$locID}','Location' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$deptID}','Department' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$catID}','Department' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$objID}','Objective' )";

    /* create lookuplist list records for each definition */
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 10, 'Toronto',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 20, 'Hong Kong',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 30, 'Brisbane',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 40, 'Tokyo',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 50, 'New York',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 60, 'San Fransisco',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 70, 'London',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 10, 'Sales',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 20, 'Information Technology',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 30, 'Marketing',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 40, 'Finance',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 50, 'Operations',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 60, 'Legal',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 70, 'Revenue',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 10, 'Revenue',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 20, 'Safety',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 30, 'Environment',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 40, 'Training',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 50, 'Product Development',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 60, 'Branding',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 70, 'Investment',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 80, 'Capital Expenditure',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 90, 'Business Growth', 1);";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 100, 'Product Development', 1);";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 110, 'Objective 3', 1);";

    $_PRJSQL[] = "insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexlist_location',$locID)";
    $_PRJSQL[] = "insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexlist_department',$deptID)";
    $_PRJSQL[] = "insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexlist_categories',$catID)";
    $_PRJSQL[] = "insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexlist_objectives',$objID)";

    foreach ($_PRJSQL as $sql) {
            DB_query ($sql);
            if (DB_error ()) {
                $err=1;
            }
        }
    //we are assuming that nexfile and the forum are installed here.  We cannot get this far if they werent!
    //the first thing we do is create a new nexFile category which will be used as the base category ID to dump files into for projects

    if ($nexfile) {
        $arr=fm_createCategory(0,'nexProject Category','This base category is used by the nexProject plugin to create document repositories for each project.',true);
        DB_query("insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexfile_parent_cat',{$arr[0]})");
    }
    else {
        DB_query("insert into {$_TABLES['prj_config']} (config_param, config_value) values ('nexfile_parent_cat',0)");
    }

    //and now, we create a new forum category and dump that into the config database
    if ($forum) {
        $sql ="INSERT INTO {$_TABLES['gf_categories']} (cat_order,cat_name,cat_dscp) values (0,'nexProject Category','This base category is used by the nexProject plugin to create forum repositories for each project.') ";
        DB_query($sql);
        $catid=DB_insertId();
        DB_query("insert into {$_TABLES['prj_config']} (config_param, config_value) values ('forum_parent_cat',{$catid})");
    }
    else {
        DB_query("insert into {$_TABLES['prj_config']} (config_param, config_value) values ('forum_parent_cat',0)");
    }

    // Finally, register the plugin with Geeklog
    COM_errorLog ("Registering $pi_display_name plugin with Geeklog", 1);

    // silently delete an existing entry
    DB_delete ($_TABLES['plugins'], 'pi_name', $pi_name);

    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) VALUES "
        . "('$pi_name', '$pi_version', '$gl_version', '$pi_url', 1)");

    if (DB_error ()) {
        $uninstall_plugin ();

        return false;
    }

    // give the plugin a chance to perform any post-install operations
    if (function_exists ('plugin_postinstall')) {
        if (!plugin_postinstall ()) {
            $uninstall_plugin ();

            return false;
        }
    }

    COM_errorLog ("Successfully installed the $pi_display_name plugin!", 1);

    return true;
}

function show_prerequisites() {
    global $_CONF, $_TABLES, $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    $disabled = '';

    $p = new Template($_CONF['path'] . 'plugins/' . $pi_name . '/templates/');
    $p->set_file('prereq_form', 'prereq_form.thtml');

    $p->set_var('layout_url', $_CONF['layout_url']);
    $p->set_var('site_admin_url', $_CONF['site_admin_url']);
    $p->set_var('pi_name', $pi_name);
    $p->set_var('pi_display_name', $pi_display_name);
    $p->set_var('pi_image', PLG_getIcon($pi_name));

    $content_function = 'plugin_getinstallcontent_' . $pi_name;
    if (function_exists($content_function)) {
        $content = $content_function();
    }
    else if (function_exists('plugin_getinstallcontent_nexpro')) {
        $content = plugin_getinstallcontent_nexpro();
    }
    else {
        $content = '';
    }
    $p->set_var('content', $content);

    //test if nexpro plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexpro' && pi_enabled=1") != '') ? 'icon_check.png':'icon_fail.png';
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: nexPro is not installed or enabled");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexPro plugin is installed and enabled', true);

    //test if nexlist plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexlist' && pi_enabled=1") != '') ? 'icon_check.png':'icon_fail.png';
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: nexList is not installed or enabled");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexList plugin is installed and enabled', true);

    //test if forum plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='forum' && pi_enabled=1") != '') ? 'icon_check.png':'icon_caution.png';
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> Forum plugin is installed and enabled', true);

    //test if nexfile plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexfile' && pi_enabled=1") != '') ? 'icon_check.png':'icon_caution.png';
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexFile plugin is installed and enabled', true);

    $p->set_var('disabled', $disabled);

    $p->parse('output', 'prereq_form');

    return $p->finish($p->get_var('output'));
}


// MAIN
$action = $_REQUEST['action'];
$display = '';

switch ($action) {
case 'install':     //display page to check for requirements
    if (DB_count ($_TABLES['plugins'], 'pi_name', $pi_name) == 0) {
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . show_prerequisites()
                 . COM_siteFooter();
    } else {
        // plugin already installed
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . COM_startBlock ($LANG32[6])
                 . '<p>' . $LANG32[7] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter();
    }
    break;

case 'uninstall': //uninstall the plugin
    if ($_REQUEST['action'] == 'uninstall') {
        $uninstall_plugin = 'plugin_uninstall_' . $pi_name;
        if ($uninstall_plugin ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=45');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=73');
        }
    }

case 'install_now': //install the plugin
    if (plugin_compatible_with_this_geeklog_version ()) {
        if (plugin_install_now ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=44');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=72');
        }
    } else {
        // plugin needs a newer version of Geeklog
        $display .= COM_siteHeader ('menu', $LANG32[8])
                 . COM_startBlock ($LANG32[8])
                 . '<p>' . $LANG32[9] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter ();
    }
    break;
}

echo $display;

?>