<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | menu_data.php                                                             |
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

include ('../lib-common.php');
// Save the current theme functions.php settings
$current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;

?>

<!--  Define Menus -->

<?php
/* Generate the JS Menu Functions that are now needed - one for each menu item that has a submenu */
$pquery = DB_query("SELECT DISTINCT id,location FROM {$_TABLES['nexmenu']} WHERE menutype=3 and is_enabled=1");
while (list($pid,$location) = DB_fetchArray($pquery)) {
    echo 'with(milonic=new menuname("nexmenu'.$pid.'")) {';
            if ($location == 'block') {
                echo 'style='.$CONF_NEXMENU['blocksubmenustyle'] .';';
            } else {
                echo 'style='.$CONF_NEXMENU['headersubmenustyle'] .';';
            }
            $query = DB_query("SELECT id,location, menutype,label,url,image,grp_access FROM {$_TABLES['nexmenu']} WHERE pid='{$pid}' AND is_enabled=1 ORDER BY menuorder");
            while (list ($id,$location,$menutype,$label,$url,$menuitemImage,$grp_id) = DB_fetchArray($query)) {
                $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
                $target = ($menutype == 2) ? 'target=_new;' : '';
                if ($menuitemImage != '') {
                    // Check and see if the full url is entered
                    if (strpos($menuitemImage,'http') === false) {
                        $menuitemImage = $_CONF['site_url'] . '/nexmenu/menuimages/' . $menuitemImage;
                    }
                }
                if (SEC_inGroup($grp_name)) {
                    $menudata = '';
                    if ($menutype == 3) {
                        if ($menuitemImage != '') {
                            $menudata = 'aI("image='.$menuitemImage.';text='.$label.';showmenu=nexmenu'.$id.';");';
                        } else {
                            $menudata = 'aI("text='.$label.';showmenu=nexmenu'.$id.';");';
                        }
                    } elseif ($menutype == 4 ) {
                        switch ($url) {
                          case "adminmenu" :
                            if ($_USER['uid'] > 1) {
                              $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                              $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                              $menudata = COM_adminMenu();
                            }
                            break;

                          case "usermenu" :
                            if ($_USER['uid'] > 1) {
                               $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                               $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                               $menudata = COM_userMenu();
                            }
                            break;

                          case "topicmenu" :
                            $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                            $menudata = COM_showTopics(''," sortnum < '50'" );
                            break;

                          case "linksmenu" :
                            $qcategories = DB_query("SELECT DISTINCT category FROM {$_TABLES['links']}");
                            while (list ($category) = DB_fetchArray ($qcategories)) {
                                if (DB_count($_TABLES['links'],"category",$category) > 0 ) {
                                    $menudata .= 'aI("text='.$category.';showmenu=links-'.$category.';url='.$_CONF['site_url'].'/links/index.php?category='.urlencode($category).';");';
                                } else {
                                    $menudata .= 'aI("text='.$category.';url='.$_CONF['site_url'].'/links/index.php?category='.urlencode($category).';");';
                                }
                            }
                            break;

                          case "spmenu" :
                           if ($CONF_NEXMENU['sp_labelonly']) {
                              $spquery = DB_query("SELECT sp_id,sp_title FROM {$_TABLES['staticpage']} WHERE sp_onmenu=1 ORDER BY sp_title");
                            } else {
                              $spquery = DB_query("SELECT sp_id,sp_title FROM {$_TABLES['staticpage']} ORDER BY sp_title");
                            }
                            while (list ($id, $title) = DB_fetchArray($spquery)) {
                                $menudata .= 'aI("text='.$title.';url='.$_CONF['site_url'].'/staticpages/index.php?page='.$id.';");';
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
                                    foreach($menuitems as $plugin_label => $plugin_link) {
                                        $menudata .= 'aI("text='.$plugin_label.';'.$target.'url='.$plugin_link.';");';
                                    }
                                }
                            }
                            break;

                        }  // End of Case 

                    } elseif ($menutype == 5) {
                        if (function_exists($url)) {
                            $menudata = $url();
                        }
                    } else {
                        $url = str_replace('[siteurl]',$_CONF['site_url'], $url);
                        $url = str_replace('[siteadminurl]',$_CONF['site_admin_url'], $url);
                        if ($menuitemImage != '') {
                            $menudata = 'aI("image='.$menuitemImage.';text='.$label.';url='.$url.';'.$target.';");';
                        } else {
                            $menudata = 'aI("text='.$label.';url='.$url.';'.$target.';");';
                        }
                    }

                    if ($menudata != '') {
                        echo $menudata;
                    }
                }
            }  // End of While
  echo '}';
}


/* NEXMENU API */
/* Check if any plugins have included a custom Menu function to generate any needed javascript for submenus */
$result = DB_query("SELECT pi_name FROM {$_TABLES['plugins']} WHERE pi_enabled = 1");
$nrows = DB_numRows($result);
for ($i = 1; $i <= $nrows; $i++) {
    $A = DB_fetchArray($result);
    $function = 'plugin_nexmenuCreateMenus_' . $A['pi_name'];
    if (function_exists($function)) {
        $jscode = $function();
        echo $jscode;
    }
}


/* If enabled, generate the JS Menu Functions that are needed for the Link category submenus */
if ($CONF_NEXMENU['linksplugin']) {
    $query = DB_query("SELECT location FROM {$_TABLES['nexmenu']} where url='linksmenu' AND is_enabled=1");
    while (list ($location) = DB_fetchArray($query)) {
        $linkmenus = '';
        $qcategories = DB_query("SELECT DISTINCT category FROM {$_TABLES['links']}");
        while (list ($category) = DB_fetchArray ($qcategories)) {
            $qlinks = DB_query("SELECT lid,title FROM {$_TABLES['links']} WHERE category = '$category'" . COM_getPermSQL( 'AND' ));
            if (DB_numRows($qlinks) > 0) {
                $linkmenus .= 'with(milonic=new menuname("links-'.$category.'")) {';
                        if ($location == 'block') {
                            $linkmenus .=  'style='.$CONF_NEXMENU['blocksubmenustyle'] .';';
                        } else {
                            $linkmenus .=  'style='.$CONF_NEXMENU['headersubmenustyle'] .';';
                        }
                while (list ($lid,$linkname) = DB_fetchArray ($qlinks)) {
                    $linkmenus .= 'aI("text='.$linkname.';url='.$_CONF['site_url'].'/portal.php?what=link&item='.$lid.';");';
                }
                $linkmenus .=  '}';
           }
        }
        echo $linkmenus;
    }
}


// Restore Template Setting
$_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;

?>