<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
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

$langfile = $_CONF['path'] . 'plugins/nexmenu/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexmenu/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexmenu/config.php');

if( empty( $LANG_CHARSET )) {
    $CONF_NEXMENU['charset'] = $_CONF['default_charset'];
    if( empty( $charset )) {
        $CONF_NEXMENU['charset'] = 'iso-8859-1';
    }
} else {
    $CONF_NEXMENU['charset'] = $LANG_CHARSET;
}

/**
* @desc Function called at the bottom of plugin config.php to intialize
* additional $CONF_NEXMENU variables based on actual menu config settings
*/
function init_nexmenuConf() {
    global $_TABLES,$CONF_NEXMENU;

    if (isset($_TABLES)) {
        $CONF_NEXMENU['installed_version'] = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexmenu'");

        if ($CONF_NEXMENU['installed_version'] >= 2.5) {
            $query = DB_query("SELECT * FROM {$_TABLES['nexmenu_config']}");
            $A = DB_fetchArray($query);
            if ($A['mode'] == 'Milonic') {
                $CONF_NEXMENU['milonicmode'] = true;
            } else {
                $CONF_NEXMENU['milonicmode'] = false;
            }

            $CONF_NEXMENU['blockmenustyle']     = $A['blockmenu_style'];
            $CONF_NEXMENU['blocksubmenustyle']  = $A['blocksubmenu_style'];
            $CONF_NEXMENU['headermenustyle']    = $A['headermenu_style'];
            $CONF_NEXMENU['headersubmenustyle'] = $A['headersubmenu_style'];
            if ($A['multilanguage'] == 1) {
                $CONF_NEXMENU['multilangmode'] = true;
            } else {
                $CONF_NEXMENU['multilangmode'] = false;
            }
        }
        if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'links'") == 1) {
            $CONF_NEXMENU['linksplugin'] = true;
        } else {
            $CONF_NEXMENU['linksplugin'] = false;
        }
        if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'staticpages'") == 1) {
            $CONF_NEXMENU['staticpagesplugin'] = true;
        } else {
            $CONF_NEXMENU['staticpagesplugin'] = false;
        }
    }

}

/**
* This will put an option for forum admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexmenu()
{
    global $_CONF,$LANG_NEXMENU00;
    if (SEC_hasRights('nexmenu.edit')) {
        return array($LANG_NEXMENU00['adminmenutitle'],"{$_CONF['site_url']}/admin/plugins/nexmenu/index.php",plugin_geticon_nexmenu());
    }
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexmenu()
{
    global $_TABLES, $_CONF,$LANG_NEXMENU00;

    if (SEC_hasRights('nexmenu.edit')) {
    return array($LANG_NEXMENU00['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php');
    }

}


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexmenu()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexmenu/images/admin/nexmenu.gif';
}



function plugin_templatesetvars_nexmenu($type,&$template) {
    if ($type == 'header') {
        $template->set_var( 'nexmenu', nexmenu_siteheader() );
    }
}

/**
* Returns the needed link to the menu csss- which will be included in the header
*/
function plugin_getHeaderCode_nexmenu() {
    global $_CONF, $CONF_NEXMENU;

    $code = "\n\n" .'<!-- Begin Nextide nexmenu -->' ."\n";
    if ($CONF_NEXMENU['milonicmode']) {
        $code .= '<SCRIPT language=JavaScript src="' . $_CONF['site_url'] . '/nexmenu/milonic/milonic_src.js" type=text/javascript></SCRIPT>' ."\n";
        $code .= '<script language=JavaScript>' ."\n";
        $code .= '    if(ns4)_d.write("<scr"+"ipt language=JavaScript src=' . $_CONF['site_url'] . '/nexmenu/milonic/mmenuns4.js><\/scr"+"ipt>");' ."\n";
        $code .= '    else _d.write("<scr"+"ipt language=JavaScript src=' . $_CONF['site_url'] . '/nexmenu/milonic/mmenudom.js><\/scr"+"ipt>");' ."\n";
        $code .= '</script>' ."\n";
        $code .= '<SCRIPT language=JavaScript src="' . $_CONF['layout_url'] . '/nexmenu/milonicmenu/menustyles.php" type=text/javascript></SCRIPT>' ."\n";
        $code .= '<SCRIPT language=JavaScript src="' . $_CONF['site_url'] . '/nexmenu/menu_data.php" type=text/javascript></SCRIPT>' ."\n";
    } else {
        $code = LB . '<link rel="stylesheet" type="text/css" href="'.$_CONF['layout_url'].'/nexmenu/cssmenu/menu.css">' . LB;
    }
    $code .= '<!-- End Nextide nexmenu -->' ."\n\n";
    return $code;
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexmenu() {
    global $CONF_NEXMENU;
    return $CONF_NEXMENU['version'];
}

/**
* Called by the plugin Editor to run the SQL Update for a plugin update
*/
function plugin_upgrade_nexmenu() {
    global $_TABLES,$CONF_NEXMENU;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexmenu'");

    switch ($curversion) {
    case '2.5':
    case '2.5.0':
    case '2.5.1':
        if (upgrade_252() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.5.2', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexmenu' LIMIT 1");
        }
        break;
    default:
        if (upgrade_25() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.5', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexmenu' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexmenu'") == $CONF_NEXMENU['version']) {
        return 11;
    } else {
        return 12;
    }
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexmenu($installCheck='')
{
    global $_TABLES, $LANG_NEXMENU00;

    $pi_name='nexmenu';
    $FEATURES = array ('nexmenu.edit');
    $TABLES = array ('nexmenu','nexmenu_language','nexmenu_config');

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_NEXMENU00['warning']);
        $display .= $LANG_NEXMENU00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the nexmenu plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nexmenu'",1);
    // Drop Plugun tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Admin Security for this plugin
    $grp_id = DB_getItem($_TABLES['vars'], 'value', "name = '{$pi_name}_admin'");
    COM_errorLog("Removing $pi_name Admin Group", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id",1);
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE name = '{$pi_name}_admin'");
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

    // Remove the plugin specific blocks that were added
    DB_query("DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn = 'phpblock_gltopicsmenu'",1);
    DB_query("DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn = 'phpblock_gluserlogin'",1);
    DB_query("DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn = 'phpblock_nexmenu'",1);

    COM_errorLog('...success',1);
    return true;
}


function nexmenu_getMenuLabel($id) {
    global $_USER,$_TABLES,$CONF_NEXMENU;

    $label = '';
    if (isset($_USER['language']) AND in_array($_USER['language'],$CONF_NEXMENU['languages'])) {
        $langid = array_search($_USER['language'],$CONF_NEXMENU['languages']);
        $label = DB_getItem($_TABLES['nexmenu_language'],'label',"menuitem=$id AND language=$langid");
    }
    if ($label == '') {
        $label = DB_getItem($_TABLES['nexmenu'],'label',"id=$id");
    }
    return $label;



}

function nexmenu_showitems($type,$pid=0) {
    global $_TABLES,$_CONF,$_USER,$CONF_NEXMENU,$_BLOCK_TEMPLATE;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;

    // Get list of groups member belongs to
    $groups = array ();
    $usergroups = SEC_getUserGroups();
    foreach ($usergroups as $group) {
        $groups[] = $group;
    }
    $groupAccessList = implode(',',$groups);
    if ($CONF_NEXMENU['installed_version'] >= 2.5) {
        $targetfeatures = DB_getItem($_TABLES['nexmenu_config'],"targetfeatures");
        $sql = "SELECT id,menutype,label,url,grp_access,image FROM {$_TABLES['nexmenu']} ";
    } else {
        $sql = "SELECT id,menutype,label,url,grp_access FROM {$_TABLES['nexmenu']} ";
         $targetfeatures = '';
    }
    $sql .= "WHERE pid=$pid AND is_enabled=1 AND location='{$type}' ";
    if (!isset($_USER['uid']) OR $_USER['uid'] < 2) {
        $sql .= "AND grp_access IN (0,2) ";
    } else {
        $sql .= "AND grp_access IN ($groupAccessList) ";
    }
    $sql .= "ORDER BY menuorder";
    $query = DB_query($sql);
    $menurows = DB_numRows($query);
    $i=1;

    while (list ($id,$menutype,$label,$url,$grp_id,$menuitemImage) = DB_fetchArray($query)) {

        if ($CONF_NEXMENU['multilangmode']) {
            $label = nexmenu_getMenuLabel($id);
        }
        $menuitemImage = trim($menuitemImage);
        $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
        $target = ($menutype == 2) ? 'target=newWindow;' . $targetfeatures : '';

        if ( $menuitemImage != '' ) {
            // Check and see if the full url is entered
            if (strpos($menuitemImage,'http') === false) {
                $menuitemImage = $_CONF['site_url'] . '/nexmenu/menuimages/' . $menuitemImage;
            }
        }

        // Only used in cssmenu mode to close of last item border
        if ($i == $menurows) {
            $lastitem = true;
        } else {
            $lastitem = false;
        }

        // Check and see if this item is a submenu
        // If it is - then it will link to a new JS Menu function
        if ($menutype == 3 ) {      // Type Submenu
            if ($type == 'header') {
                $menuitemimagecss = 'headermenuitemimage';
            } else {
                $menuitemimagecss = 'blocksubmenuitemimage';
            }
            if ($CONF_NEXMENU['milonicmode']) {
                if ($menuitemImage != '') {
                    $retval .= 'aI("image='.$menuitemImage.';text='.$label.';'.$target.'showmenu=nexmenu'.$id.';");';
                } else {
                    $retval .= 'aI("text='.$label.';'.$target.'showmenu=nexmenu'.$id.';");';
                }

            } else {
                $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                if ($type == 'header') {
                    $t->set_file('menu','headersubmenu.thtml');
                } else {
                    $t->set_file('menu','submenu.thtml');
                }
                $t->set_var('menuitem_url',$url);
                if ($menuitemImage != '') {
                    $image = '<img src="'.$menuitemImage.'" border="0">&nbsp;';
                    $label = "{$image}<span id=\"$menuitemimagecss\">{$label}</span>";
                    $t->set_var('menuitem_label',$label);
                } else {
                    $t->set_var('menuitem_label',$label);
                }
                if ($pid == 0) {
                    $t->set_var('imgclass','drop');
                } else {
                    $t->set_var('imgclass','fly');
                }
                if ($i == $menurows) {
                    $t->set_var('lastitemclass','class="enclose"');
                }
                $t->set_var('submenu_items',nexmenu_showitems($type,$id));
                $t->parse ('output', 'menu');
                $retval .= $t->finish ($t->get_var('output'));
            }

        } elseif ($menutype == 4 ) {      // Core Menu
            switch ($url) {
              case "adminmenu" :
                if ($_USER['uid'] > 1) {
                    if ($CONF_NEXMENU['milonicmode']) {
                        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                    } else {
                        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
                        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
                    }
                    $plugin_options .= PLG_getAdminOptions();
                    $nrows = count( $plugin_options );
                    if( SEC_isModerator() OR ($nrows > 0) OR
                        SEC_hasrights('story.edit,block.edit,topic.edit,link.edit,event.edit,poll.edit,user.edit,plugin.edit,user.mail','OR')) {
                        $retval .= COM_adminMenu();
                    }
                }
                break;

              case "usermenu" :
                if ($_USER['uid'] > 1) {
                    if ($CONF_NEXMENU['milonicmode']) {
                        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                    } else {
                        $_BLOCK_TEMPLATE['user_block'] = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
                        $_BLOCK_TEMPLATE['useroption'] = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
                    }
                    $retval .=  COM_userMenu();
                }
                break;

              case "topicmenu" :
                 if ($CONF_NEXMENU['milonicmode']) {
                    $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                 } else {
                    $_BLOCK_TEMPLATE['topicoption'] = 'nexmenu/cssmenu/menuitem2.thtml,nexmenu/cssmenu/menuitem2_on.thtml';
                 }
                 $retval .= COM_showTopics(''," sortnum < '{$CONF_NEXMENU['restricted_topics']}'" );
                 break;

              case "linksmenu" :
                 if ($CONF_NEXMENU['linksplugin']) {
                     $sql = "SELECT DISTINCT category FROM {$_TABLES['links']} WHERE category != '' ";
                     if ($CONF_NEXMENU['links_maxtoplevels'] > 0)  {
                         $sql .= "limit {$CONF_NEXMENU['links_maxtoplevels']}";
                     }
                     if ($CONF_NEXMENU['milonicmode']) {
                        $qcategories = DB_query($sql);
                        while (list ($category) = DB_fetchArray ($qcategories)) {
                            if (DB_count($_TABLES['links'],"category",$category) > 0 ) {
                                $retval .= 'aI("text='.$category.';showmenu=links-'.$category.';url='.$_CONF['site_url'].'/links/index.php?category='.urlencode($category).';");';
                            } else {
                                $retval .= 'aI("text='.$category.';url='.$_CONF['site_url'].'/links/index.php?category='.urlencode($category).';");';
                            }
                        }
                     } else {
                        $linkquery = DB_query($sql);
                        $numcategories = DB_numRows($linkquery);
                        $retval .= nexmenu_showlinks($pid,$type,'',$numcategories,0,$lastitem);
                     }
                 }
                 break;

              case "spmenu" :
                if ($CONF_NEXMENU['staticpagesplugin']) {
                    if ($CONF_NEXMENU['sp_labelonly']) {
                      $sql = "SELECT sp_id,sp_title,sp_label FROM {$_TABLES['staticpage']} WHERE sp_onmenu=1 ";
                      $sql .= COM_getPermSql ('AND');
                      $sql .= 'ORDER BY sp_title';
                      $spquery = DB_query($sql);
                    } else {
                      $sql = "SELECT sp_id,sp_title,sp_label FROM {$_TABLES['staticpage']} ";
                      $sql .= COM_getPermSql ('WHERE');
                      $sql .= 'ORDER BY sp_title';
                      $spquery = DB_query($sql);
                    }
                    while (list ($id, $title,$sp_label) = DB_fetchArray($spquery)) {
                        if (trim($sp_label) == '') {
                            $label = $title;
                        } else {
                            $label = $sp_label;
                        }
                        $url = "{$_CONF['site_url']}/staticpages/index.php?page=$id";
                        if ($CONF_NEXMENU['milonicmode']) {
                            $retval .= 'aI("text='.$label.';url='.$_CONF['site_url'].'/staticpages/index.php?page='.$id.';");';
                        } else {
                            $retval .= "<li><a href=\"$url\" $target>$label</a></li>" . LB;
                        }
                    }
                }
                break;

              case "pluginmenu" :
                $result = DB_query("SELECT pi_name FROM {$_TABLES['plugins']} WHERE pi_enabled = 1");
                $nrows = DB_numRows($result);
                $menu = array();
                for ($i = 1; $i <= $nrows; $i++) {
                    $A = DB_fetchArray($result);
                    $function = 'plugin_getmenuitems_' . $A['pi_name'];
                    if (function_exists($function)) {
                        $menuitems = $function();
                        if (is_array($menuitems) AND count($menuitems) > 0) {
                            foreach($menuitems as $plugin_label => $plugin_link) {
                                if ($CONF_NEXMENU['milonicmode']) {
                                    $retval .= 'aI("text='.$plugin_label.';'.$target.'url='.$plugin_link.';");';
                                } else {
                                    $retval .= "<li><a href=\"$plugin_link\" $target>$plugin_label</a></li>" . LB;
                                }
                            }
                        }
                    }
                }
                break;

              case "headermenu" :       // Only supported currenlty in CSS Menu mode
                if (!$CONF_NEXMENU['milonicmode']) {
                    $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                    $t->set_file(array( 'menu' => 'siteheader_menuitems.thtml',
                                        'menuitem' => 'headermenu_item.thtml',
                                        'menuitem_last' => 'headermenu_item.thtml'));
                    $plugin_menu = PLG_getMenuItems();
                    COM_renderMenu($t,$plugin_menu);
                    $t->parse ('output', 'menu');
                    $retval .= $t->finish ($t->get_var('output'));
                }
                break;

            }  // End of menutype == 4  (Core Menu)

        } elseif ($menutype == 5) {
            if (function_exists($url)) {
                /* Pass the type of menu to custom php function */
                $retval .= $url($type);
            }

        } else {
            $url = str_replace('[siteurl]',$_CONF['site_url'], $url);
            $url = str_replace('[siteadminurl]',$_CONF['site_admin_url'], $url);
            // what's our current URL?
            $thisUrl = COM_getCurrentURL();
            if ($CONF_NEXMENU['milonicmode']) {
                if ($menuitemImage != '') {
                    $retval .= 'aI("image='.$menuitemImage.';text='.$label.';url='.$url.';'.$target.';");';
                } else {
                    $retval .= 'aI("text='.$label.';url='.$url.';'.$target.';");';
                }
            } else {
                if ($menuitemImage != '') {
                    if ($type == 'header') {
                        $menuitemimagecss = 'headermenuitemimage';
                    } else {
                        $menuitemimagecss = 'blockmenuitemimage';
                    }
                    $image = '<img src="'.$menuitemImage.'" border="0">&nbsp;';
                    if ($i == 1 AND $pid > 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } elseif ($i == $menurows AND $pid == 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } elseif ($url == $thisUrl) {
                        $retval .= "<li id=\"menuitem_current\"><a href=\"$url\" $target>{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } else {
                        $retval .= "<li><a href=\"$url\" $target>{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    }
                } else {
                    if ($i == 1 AND $pid > 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$label</a></li>" . LB;
                    } elseif ($i == $menurows AND $pid == 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$label</a></li>" . LB;
                    } elseif ($url == $thisUrl) {
                        $retval .= "<li id=\"menuitem_current\"><a href=\"$url\" $target>$label</a></li>" . LB;
                    } else {
                        $retval .= "<li><a href=\"$url\" $target>$label</a></li>" . LB;
                    }
                }
            }
        }
        $i++;
    }

    // Restore Template Setting
    $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
    return $retval;
}

function phpblock_nexmenu() {
    global $_USER,$_TABLES,$_CONF,$CONF_NEXMENU;

    /* check if any items are defined and enabled */
    $menuitems = false;
    $retval = '';
    $query = DB_query("SELECT grp_access FROM {$_TABLES['nexmenu']} WHERE pid=0 AND is_enabled=1 AND location='block'");
    while (list ($grp_id) = DB_fetchArray($query)) {
        $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
        if (SEC_inGroup($grp_name)) {   // There is atleast 1 item - set true and break out of loop
           $menuitems = true;
           break;
        }
    }
    if ($CONF_NEXMENU['installed_version'] >= 2.5) {
        $query = DB_query("SELECT blockmenu_properties,blockmenu_style FROM {$_TABLES['nexmenu_config']}");
        list ($properties,$style) = DB_fetchArray($query);
    } else {
        $properties = '';
        $style = '';
    }

    if ($menuitems) {
        if ($CONF_NEXMENU['milonicmode']) {
            if ($menuitems) {
                $retval ='<script>
                            with(milonic=new menuname("Site Menu")) {' . LB;
                $retval .= $CONF_NEXMENU['blockmenu_default_styles'] . $properties . LB;
                $retval .= 'style='.$style.';';
                $retval .= nexmenu_showitems('block');
                $retval .= '} drawMenus(); </script>';
            }
        } else {
            $retval = COM_startBlock( '', '', 'nexmenu/cssmenu/blockheader.thtml');
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            $t->set_file('menu','blockmenu.thtml');
            $t->set_var('menuitems',nexmenu_showitems('block'));
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');
        }
    }

    if ($CONF_NEXMENU['loginform'] AND $_USER['uid'] < 2 AND DB_COUNT($_TABLES['nexmenu'],'url','usermenu') > 0 ) {
        // Now check the default useroptions block or gluserlogin block is not enabled
        $sql = "SELECT * FROM {$_TABLES['blocks']} WHERE is_enabled=1 AND (name='user_block' OR phpblockfn='phpblock_gluserlogin')";
        if (DB_numRows(DB_query($sql)) == 0) {
            $retval .= phpblock_glusermenu();
        }
    }

    return $retval;
}

function nexmenu_siteheader() {
    global $_TABLES,$_CONF,$CONF_NEXMENU;

    /* check if any items are defined and enabled */
    $headermenuItems = false;
    $retval = '';
    $query = DB_query("SELECT grp_access FROM {$_TABLES['nexmenu']} WHERE pid=0 AND is_enabled=1 AND location='header'");
    while (list ($grp_id) = DB_fetchArray($query)) {
        $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
        if (SEC_inGroup($grp_name)) {   // There is atleast 1 item - set true and break out of loop
           $headermenuItems = true;
           break;
        }
    }
    if ($CONF_NEXMENU['installed_version'] >= 2.5) {
        $query = DB_query("SELECT headermenu_properties,headermenu_style FROM {$_TABLES['nexmenu_config']}");
        list ($properties,$style) = DB_fetchArray($query);
    } else {
        $properties = '';
        $style = '';
    }

    if ($headermenuItems) {
        if ($CONF_NEXMENU['milonicmode']) {
            $retval = '<script> with(milonic=new menuname("Site Menu")) {';
            $retval .= $CONF_NEXMENU['headermenu_default_styles'] . $properties;
            $retval .= 'style='.$style.';';
            $retval .= nexmenu_showitems('header');
            $retval .=  '} drawMenus(); </script>';
        } else {
            $headerbg = DB_getItem($_TABLES['nexmenu_config'], 'headerbg');
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            $t->set_file('menu','headermenu.thtml');
            $t->set_file('menuitem','headermenu_item.thtml');
            $t->set_var('background-color', $headerbg);
            $t->set_var('menuitems',nexmenu_showitems('header'));
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
        }
    }
    return $retval;

}


function phpblock_gluserlogin() {
    global $_USER,$_CONF, $_TABLES, $LANG01,$_BLOCK_TEMPLATE;

    if ($_USER['uid'] < 2) {
        $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/blockheader.thtml,nexmenu/blockfooter.thtml';

        $t = new Template( $_CONF['path_layout'] );
        $t->set_file( 'form', 'loginform.thtml' );
        $t->set_var( 'site_url', $_CONF['site_url'] );
        $t->set_var( 'layout_url', $_CONF['layout_url'] );
        $t->set_var( 'lang_username', $LANG01[21] );
        $t->set_var( 'lang_password', $LANG01[57] );
        $t->set_var( 'lang_login', $LANG01[58] );
        $t->set_var( 'lang_signup', $LANG01[59] );
        $t->set_var('lang_forgetpassword',$LANG01[119]);
        $t->set_var('services','');
        $t->parse( 'output', 'form' );

        $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
        return $t->finish ($t->get_var('output'));
    }
}



function phpblock_glusermenu() {
    global $_USER,$_CONF,$LANG01,$LANG04,$_BLOCK_TEMPLATE,$CONF_NEXMENU;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($CONF_NEXMENU['milonicmode']) {
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blockmenu.thtml,nexmenu/milonicmenu/blockfooter-blockmenu.thtml';
        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
    } else {
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
    }

    if ($_USER['uid'] > 1) {
        if ($CONF_NEXMENU['milonicmode']) {
            $retval .= COM_userMenu();
        } else {
            $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            $t->set_file('menu','blockmenu.thtml');
            $t->set_var('menuitems',COM_userMenu());
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');
        }
    } else {
        $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/blockheader.thtml');
        $t = new Template( $_CONF['path_layout'] );
        $t->set_file( 'form', 'loginform.thtml' );
        $t->set_var( 'site_url', $_CONF['site_url'] );
        $t->set_var( 'layout_url', $_CONF['layout_url'] );
        $t->set_var( 'lang_username', $LANG01[21] );
        $t->set_var( 'lang_password', $LANG01[57] );
        $t->set_var( 'lang_login', $LANG01[58] );
        $t->set_var('lang_forgetpassword',$LANG01[119]);
        if( $_CONF['disable_new_user_registration'] == 1 )
        {
            $t->set_var( 'lang_signup', '' );
        }
        else
        {
            $t->set_var( 'lang_signup', $LANG01[59] );
        }
        if( $_CONF['remoteauthentication'] && !$_CONF['usersubmission'] ) {
            // Build select
            $select = '<select name="service" id="service"><option value="">' .
                            $_CONF['site_name'] . '</option>';
            if( is_dir( $_CONF['path_system'] . 'classes/authentication/' )) {
                $folder = opendir( $_CONF['path_system'] . 'classes/authentication/' );
                while(( $filename = @readdir( $folder )) !== false ) {
                    $strpos = strpos( $filename, '.auth.class.php' );
                    if( $strpos ) {
                        $service = substr( $filename, 0, $strpos );
                        $select .= '<option value="' . $service . '">'
                                . $service . '</option>';
                    }
                }
            }
            $select .= '</select>';
            $t->set_file( 'services', 'blockservices.thtml' );
            $t->set_var( 'lang_service', $LANG04[121] );
            $t->set_var( 'select_service', $select );
            $t->parse( 'output', 'services' );
            $t->set_var( 'services', $t->finish( $t->get_var( 'output' )));
        } else {
            $t->set_var( 'services', '' );
        }
        $t->parse( 'output', 'form' );
        $retval .= $t->finish ($t->get_var('output'));
        $retval .= COM_endBlock('nexmenu/blockfooter.thtml');
    }
    $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
    return $retval;
}

function phpblock_gladminmenu() {
    global $_CONF,$_USER,$_TABLES,$_BLOCK_TEMPLATE,$CONF_NEXMENU;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($CONF_NEXMENU['milonicmode']) {
        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blockmenu.thtml,nexmenu/milonicmenu/blockfooter-blockmenu.thtml';
        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
    } else {
        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
    }
    // Check if the admin menu is enabled
    $headermenuItems = false;
    $retval = '';
    if ($_USER['uid'] > 1) {
        $query = DB_query("SELECT grp_access FROM {$_TABLES['nexmenu']} WHERE url='adminmenu' AND is_enabled=1");
        while (list ($grp_id) = DB_fetchArray($query)) {
            $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
            if (SEC_inGroup($grp_name)) {
                if ($CONF_NEXMENU['milonicmode']) {
                    $retval .= COM_adminMenu();
                } else {
                    $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
                    $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                    $t->set_file('menu','blockmenu.thtml');
                    $t->set_var('menuitems', COM_adminMenu());
                    $t->parse ('output', 'menu');
                    $retval .= $t->finish ($t->get_var('output'));
                    $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');
                }
                break;
            }
        }
    }
    $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
    return $retval;
}


/* Recursive function used to generate any needed submenus and menuitems for the Links Plugin
 * Only used in CSS Menu mode
*/
function nexmenu_showlinks($pid,$type,$catname='',$numcategories=0,$curcategory=0,$lastitem) {
    global $_CONF,$_TABLES,$CONF_NEXMENU;

    $retval = '';
    $thisitemMenuorder = DB_getItem($_TABLES['nexmenu'],'menuorder',"pid=$pid AND url='linksmenu'");

    if ($catname == '')  {
        $sql = "SELECT DISTINCT category FROM {$_TABLES['links']} WHERE category != '' ";
        if ($CONF_NEXMENU['links_maxtoplevels'] > 0)  {
            $sql .= "limit {$CONF_NEXMENU['links_maxtoplevels']}";
        }
    } else {
         $sql = "SELECT title,lid FROM {$_TABLES['links']} WHERE category='$catname'";
    }
    $query = DB_query($sql);
    if ($numcategories == 0) {
        $numcategories = DB_numRows($query);
    }
    $curcategory++;
    $i=1;
    while (list ($title,$lid) = DB_fetchArray ($query)) {
        if ($catname == '' AND DB_count($_TABLES['links'],"category",$title) > 0 ) {
            $url = $_CONF['site_url'].'/links/index.php?category='.urlencode($title);
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu/');
            if ($type == 'header') {
                $t->set_file('menu','headersubmenu.thtml');
            } else {
                $t->set_file('menu','submenu.thtml');
            }
            if ($pid == 0) {
                $t->set_var('imgclass','drop');
            } else {
                $t->set_var('imgclass','fly');
            }
            if ($pid > 0 AND $i == 1 AND $thisitemMenuorder == 10) {
                $t->set_var('lastitemclass','class="enclose"');
            } elseif ($lastitem AND $pid == 0 AND $i == $numcategories) {
                $t->set_var('lastitemclass','class="enclose"');
            } else {
                $t->set_var('lastitemclass','');
            }
            $t->set_var('menuitem_url',$url);
            $t->set_var('menuitem_label',$title);
            $t->set_var('submenu_items',nexmenu_showlinks($pid,$type,$title,$numcategories,$curcategory,$lastitem));
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
        } else {
            $url = $_CONF['site_url'].'/links/portal.php?what=link&item='.$lid;
            if ($i == 1) {
                $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$title</a>" . LB;
            } else {
                $retval .= "<li><a href=\"$url\" $target>$title</a>" . LB;
            }

        }
        $i++;
    }
    return $retval;
}


function phpblock_gltopicsmenu() {
    global $_CONF,$_BLOCK_TEMPLATE,$CONF_NEXMENU;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($CONF_NEXMENU['milonicmode']) {
        if ($CONF_NEXMENU['installed_version'] >= 2.5) {
            $query = DB_query("SELECT blockmenu_properties,blockmenu_style FROM {$_TABLES['nexmenu_config']}");
            list ($properties,$style) = DB_fetchArray($query);
        } else {
            $properties = '';
            $style = '';
        }
        $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
        $retval ='<script>
            with(milonic=new menuname("Site Topics Menu")) {' . LB;
        $retval .= $CONF_NEXMENU['headermenu_default_styles'] . $properties . LB;
        $retval .= 'style='.$style.';';
        $retval .= COM_showTopics(''," sortnum < '50'" );
        $retval .=  '} drawMenus(); </script>';
    } else {
        $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/cssmenu/menuitem2.thtml,nexmenu/cssmenu/menuitem2_on.thtml';
        $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
        $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
        $t->set_file('menu','blockmenu.thtml');
        $t->set_var('menuitems',COM_showTopics(''," sortnum < '50'" ));
        $t->parse ('output', 'menu');
        $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
        $retval .=  $t->finish ($t->get_var('output'));
        $retval .= COM_endBlock('nexmenu/blockfooter.thtml');
    }
    return $retval;
}

function nexmenu_showtopics() {
    global $_CONF,$_TABLES,$CONF_NEXMENU;

    $retval = '';
    $qtopics = DB_query("SELECT DISTINCT topic FROM {$_TABLES['topics']}");
    $numtopics = DB_numRows($qtopics);
    $i = 1;
    while (list ($topic) = DB_fetchArray ($qtopics)) {
        $url="{$_CONF['site_url']}/index.php?topic=$topic";
        if ($CONF_NEXMENU['milonicmode']) {
            if (DB_count($_TABLES['topics'],"topic",$topic) > 0 ) {
                $retval .= 'aI("text='.$topic.';showmenu=topics-'.$topic.';url='.$url.';");';
            } else {
                $retval .= 'aI("text='.$topic.';url='.$url.';");';
            }
        } else {
            if ($i == $numtopics) {
                $retval .= "<li><a href=\"$url\" class=\"enclose\">$topic</a></li>" . LB;
            } else {
                $retval .= "<li><a href=\"$url\">$topic</a></li>" . LB;
            }
        }
        $i++;
    }
    return $retval;
}

?>