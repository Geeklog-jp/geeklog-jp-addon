<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
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

$langfile = $_CONF['path'] . 'plugins/nexcontent/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexcontent/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexcontent/config.php');

/**
* This will put an option for forum admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexcontent()
{
    global $_CONF,$LANG_SE00;
    if (SEC_hasRights('nexcontent.edit')) {
        return array($LANG_SE00['adminmenupanel'],"{$_CONF['site_url']}/admin/plugins/nexcontent/index.php",plugin_geticon_nexcontent());
    }
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexcontent()
{
    global $_TABLES, $_CONF,$LANG_SE00;

    if (SEC_hasRights('nexcontent.edit')) {
        $result = DB_query ("SELECT count(*) AS cnt FROM {$_TABLES['nexcontent_pages']}" . COM_getPermSQL ());
        $A = DB_fetchArray ($result);
        $total_pages = $A['cnt'];
        return array($LANG_SE00['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexcontent/index.php', $total_pages);
    }

}


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexcontent()
{
    global $_CONF,$CONF_SE;

    return $_CONF['layout_url'] .'/nexcontent/images/admin/nexcontent.gif';
}


function plugin_templatesetvars_nexcontent($type,&$template) {
    global $_TABLES, $CONF_SE, $MESSAGE;
    if ($type == 'header') {
        if (isset($_GET['page']) OR $CONF_SE['frontpage']) {
            if ($CONF_SE['frontpage']) {
                $fp_catid = DB_getItem($_TABLES['nexcontent_pages'], 'id', "name = 'frontpage'");
                $q = DB_query("SELECT pagetitle, meta_description, meta_keywords FROM {$_TABLES['nexcontent_pages']} WHERE pid='{$fp_catid}' AND is_draft='0'");
            } else {
                $q = DB_query("SELECT pagetitle, meta_description, meta_keywords FROM {$_TABLES['nexcontent_pages']} WHERE id='{$_GET['page']}'");
            }
            $metatags = '';
            if (DB_numRows($q) == 1) {
                list ($pagetitle,$description, $keywords) = DB_fetchArray($q);
                if ($description == '' AND $CONF_SE['meta_description'] != '') {
                    $metatags  = '<META NAME="description" CONTENT="xx'.$CONF_SE['meta_description'] .'">' . LB;
                } elseif ($description != '') {
                    $metatags  = '<META NAME="description" CONTENT="'.$description.'">' . LB;
                }
                if ($keywords == '' AND $CONF_SE['meta_keywords'] != '') {
                    $metatags  .= '<META NAME="keywords" CONTENT="'.$CONF_SE['meta_keywords'] .'">' . LB;
                } elseif ($keywords != '') {
                    $metatags .= '<META NAME="keywords" CONTENT="'.$keywords.'">' . LB;
                }
                $template->set_var( 'metatags', $metatags);
                $template->set_var('page_title',($pagetitle == '') ? $CONF_SE['pagetitle'] : $pagetitle);
            } else {
                $template->set_var( 'metatags', '' );
            }
        } else {
            $template->set_var( 'metatags', '' );
        }

        if (isset($_GET['msg'])) {
            $msg = COM_applyFilter($_GET['msg']);
            if (isset ($_GET['plugin'])) {
                $plugin = COM_applyFilter ($_GET['plugin']);
            }
            if( !empty( $plugin )) {
                 $var = 'PLG_' . $plugin . '_MESSAGE' . $msg;
                 global $$var;
                 if( isset( $$var ))  {
                    $message = $$var;
                 }  else {
                    $message = sprintf( $MESSAGE[61], $plugin );
                    COM_errorLog ($MESSAGE[61]. ": " . $var,1);
                 }
            } else {
                $message = $MESSAGE[$msg];
            }

            $template->set_var('site_message',$message);
        } else {
           $template->set_var('show_sitemsg','hidden');
        }
    }

}


/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexcontent()
{
    global $_CONF,$_USER,$CONF_SE;

    $code = "\n\n<!-- Begin Nextide nexcontent  -->\n";

    $code .= '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'] .'/fckeditor/css/fck_editorarea.css">' .LB;
    if ($CONF_SE['load_HTMLTree']) {
        $code .= '<script language=JavaScript src="' . $_CONF['site_url'] . '/javascript/TreeMenu.js" type="text/javascript"></script>' ."\n";
    }
    if ($CONF_SE['load_FCKEditor']) {
       $code .= LB . '<script language=JavaScript src="' . $_CONF['site_url'] . '/fckeditor/fckeditor.js" type="text/javascript"></script>';
    }
    $code .= LB . '<script language=JavaScript src="' . $CONF_SE['public_url'] . '/include/nexcontent.js" type="text/javascript"></script>';

    $code .= "\n<!-- End Nextide nexcontent -->\n\n";
    return $code;
}



/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
*
*/

function plugin_searchtypes_nexcontent()
{
    global $LANG_SE00;

    return array('nexcontent' => $LANG_SE00['searchlabel']);
}

/**
* this searches for files matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
*
*/

function plugin_dopluginsearch_nexcontent($query, $datestart, $dateend, $topic, $type, $author)
{
    global $LANG_SE00, $LANG_SE01,$CONF_SE,$_TABLES, $_CONF, $searchObj;

    if (empty($type)) {
        $type = 'all';
    }

    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type <> 'nexcontent') {
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = $LANG_SE00['plugin_name'];
        $plugin_results->searchlabel = $LANG_SE00['searchlabel'];
        return $plugin_results;
    }
    // Build search SQL to search the project and task name, details and keywords fields

    $sql  = "SELECT id ,name, type, hits FROM {$_TABLES['nexcontent_pages']} WHERE  is_draft='0' ";
    $sql .= COM_getPermSQL('AND');

    if (!empty ($query)) {
        $query = htmlspecialchars($query);
        if($searchObj->_keyType == 'phrase') {
            // do an exact phrase search (default)
            $mywords[] = $query;
            $mysearchterm = addslashes ($query);
            $sql .= "AND (content like '%$mysearchterm%'  ";
            $sql .= "OR heading like '%$mysearchterm%' ";
            $sql .= "OR name like '%$mysearchterm%')  ";
        } elseif($searchObj->_keyType == 'all') {
            // must contain ALL of the keywords
            $mywords = explode(' ', $query);
            $sql .= 'AND ';
            $tmp = '';
            foreach ($mywords AS $mysearchterm) {
                $mysearchterm = addslashes (trim ($mysearchterm));
                $tmp .= "(content like '%$mysearchterm%' OR ";
                $tmp .= "heading like '%$mysearchterm%' OR ";
                $tmp .= "name like '%$mysearchterm%') AND ";
            }
            $tmp = substr($tmp, 0, strlen($tmp) - 4);
            $sql .= $tmp;
        }
        elseif($searchObj->_keyType == 'any') {
            // must contain ANY of the keywords
            $mywords = explode(' ', $query);
            $sql .= 'AND ';
            $tmp = '';
            foreach ($mywords AS $mysearchterm) {
                $mysearchterm = addslashes (trim ($mysearchterm));
                $tmp .= "(content like '%$mysearchterm%' OR ";
                $tmp .= "heading like '%$mysearchterm%' OR ";
                $tmp .= "name like '%$mysearchterm%') OR ";
            }
            $tmp = substr($tmp, 0, strlen($tmp) - 3);
            $sql .= "($tmp)";
        } else {
            $mywords[] = $query;
            $mysearchterm = addslashes ($query);
            $sql .= "AND (content like '%$mysearchterm%'  ";
            $sql .= "OR heading like '%$mysearchterm%' ";
            $sql .= "OR name like '%$mysearchterm%')  ";
        }
    }

    $sql .= "ORDER BY id DESC";

    // Perform search
    $result = DB_query($sql);

    // OK, now return coma delmited string of table header labels
    // Need to use language variables
    require_once($_CONF['path_system'] . 'classes/plugin.class.php');
    $plugin_results = new Plugin();
    $plugin_results->plugin_name = $LANG_SE00['searchlabel'];
    $plugin_results->searchlabel = $LANG_SE00['searchresults'];
    $plugin_results->addSearchHeading($LANG_SE01['SEARCH_PAGE']);
    $plugin_results->addSearchHeading($LANG_SE01['SEARCH_TYPE']);
    $plugin_results->addSearchHeading($LANG_SE01['SEARCH_HITS']);
    $totalsearched = DB_numRows($result);
    $searchcount=0;

    for ($i = 1; $i <= $totalsearched; $i++) {

        list($id, $name, $type, $hits) = DB_fetchArray($result);
        $searchcount++;

        $row = array('<a href="'.$CONF_SE['public_url'] .'/index.php?page='.$id.'">'.$name.'</a>',$type,$hits, '');
        $plugin_results->addSearchResult($row);
    }
    $plugin_results->num_searchresults = $searchcount;
    $plugin_results->num_itemssearched = DB_count($_TABLES['nexcontent_pages']);
    return $plugin_results;
}



function plugin_autotags_nexcontent($op,$content='',$autotag='') {
    global $_CONF,$_TABLES,$page;
    if ($op == 'tagname' ) {
        return array('showblock','phpfunction','pageindex');
    } elseif ($op == 'parse') {
       if ($autotag['tag'] == 'showblock') {
            $sql = "SELECT bid, name,type,title,content,rdfurl,phpblockfn,help FROM {$_TABLES['blocks']} WHERE name='{$autotag['parm1']}'";
            $result = DB_query($sql);
            if (DB_numRows($result) == 1) {
                $A = DB_fetchArray($result);
                $blockContent = COM_formatBlock($A);
                $content = str_replace ($autotag['tagstr'], $blockContent,$content);
            }
        } elseif ($autotag['tag'] == 'phpfunction') {
            $function = $autotag['parm1'];
            if (function_exists($function)) {
                $args = explode(',',$autotag['parm2']);
                switch (count($args)) {
                case 0:
                    $functionContent = $function();
                    break;
                case 1:
                    $functionContent = $function($args[0]);
                    break;
                case 2:
                    $functionContent = $function($args[0], $args[1]);
                    break;
                case 3:
                    $functionContent = $function($args[0], $args[1], $args[2]);
                    break;
                case 4:
                    $functionContent = $function($args[0], $args[1], $args[2], $args[3]);
                    break;
                case 5:
                    $functionContent = $function($args[0], $args[1], $args[2], $args[3], $args[4]);
                    break;
                default:
                    $functionContent = $function($args);
                    break;
                }

                $content = str_replace ($autotag['tagstr'], $functionContent,$content);
            }
        } elseif ($autotag['tag'] == 'pageindex') {
            $category = $autotag['parm1'];
            if (isset($page) AND $page > 0) {
            $type = DB_getItem($_TABLES['nexcontent_pages'], 'type', "id='$page'");
                if ($category == 'parent') {
                    $cat = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id='$page'");
                    if ($type == 'page') {
                        $catparent = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id='$cat'");
                    }
                    $catid = ($catparent != 0) ? $catparent: $cat;
                } elseif ($category == 'current' or $category == '') {
                if ($type == 'page') {
                        $catid = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id='$page'");
                    } else {
                        $catid = $page;
                    }
                } else {
                    $catid = $category;
                }
            } else {
                $catid = $category;
            }
            $limit = $autotag['parm2'];
            $functionContent = nexcontent_pageindex($catid,$autotag['parm2']);
            $content = str_replace ($autotag['tagstr'], $functionContent,$content);
        }
    }
    return $content;
}



/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexcontent() {
    global $CONF_SE;
    return $CONF_SE['version'];
}


/**
* Removes the datastructures for this plugin from the Geeklog database
* This may get called by the install routine to undue anything created during the install.
* Added check to see that plugin is first disabled.
*/
function plugin_uninstall_nexcontent($installCheck='')
{
    global $_TABLES, $LANG_SE00;

    $pi_name='nexcontent';
    $FEATURES = array ('nexcontent.edit','nexcontent.user');
    $TABLES = array ('nexcontent','nexcontent_pages','nexcontent_images');

     // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_SE00['warning']);
        $display .= $LANG_SE00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        exit;
    }

    // Ok to proceed and delete plugin - Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the glnexcontent plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nexcontent'",1);
    // Drop Plugun tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Admin Security for this plugin
    $grp_id = DB_getItem($_TABLES['vars'], 'value', "name = '{$pi_name} Admin'");
    COM_errorLog("Removing $pi_name Admin Group", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id",1);
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE name = '{$pi_name} Admin'");
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

function plugin_upgrade_nexcontent() {
    global $_TABLES,$CONF_SE;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexcontent'");

    switch ($curversion) {
    case "2.1.0":
        if (upgrade_211() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.1.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexcontent' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexcontent'") == $CONF_SE['version']) {
        return 11;
    } else {
        return 12;
    }
}


/**
* Display nexcontent defined frontpages on main site index page.
*
* @param   where   int      where the static page will be displayed (0..3)
*                           0 = fullpage
* @param   page    int      page number
* @param   topic   string   topic ID
* @return          string   HTML for the static page (can be empty)
*/
function plugin_centerblock_nexcontent ($where = 0, $page = 1, $topic ='')
{
    global $_CONF, $_TABLES, $CONF_SE, $_GET;

    $CONF_SE['frontpage'] = true;
    require_once ($_CONF['path'] . 'plugins/nexcontent/library.php');

    $retval = '';

    /* Only Full Page supported currently */
    if (isset($_GET['news']) OR $topic != '' OR $page > 1 OR  $where > 0) {
        return $retval; // Only the main site index page - page 1
    }

    /* Get folder id for the categoy called 'frontpage' */
    $fp_catid = DB_getItem($_TABLES['nexcontent_pages'], 'id', "name = 'frontpage'");

    $sql = "SELECT * FROM {$_TABLES['nexcontent_pages']} WHERE pid='$fp_catid' AND is_draft='0'";
    $sql .= COM_getPermSQL('AND');
    $sql .= ' ORDER BY pageorder,id';

    $query = DB_query($sql);
    $numrows = DB_numRows($query);

    if ($numrows == 0 OR $fp_catid == '' OR $fp_catid < 1) {
        return $retval; // No pages defined
    }

    $pageview = new Template($_CONF['path_layout'] . 'nexcontent');
    $pageview->set_file (array ('pageview'=>'frontpage.thtml'));

    // Only use the first enabled page in the folder 'frontpage'
    $A = DB_fetchArray($query);
        switch ($A['blockformat']) {
            case 'allblocks' :
                $siteheader = COM_siteHeader();
                $sitefooter = COM_siteFooter(true);
                break;
            case 'leftonly' :
                $siteheader = COM_siteHeader();
                $sitefooter = COM_siteFooter();
                break;
            case 'rightonly' :
                $siteheader = COM_siteHeader('none');
                $sitefooter = COM_siteFooter(true);
                break;
            case 'customblocks' :
                $siteheader = COM_siteHeader('custom',$A['content']);
                $sitefooter = COM_siteFooter(true,$A['content']);
                break;
            case 'blankpage' :
                $header = new Template($_CONF['path_layout'] . 'nexcontent');
                $header->set_file (array ('header'=>'header.thtml'));
                plugin_templatesetvars_nexcontent('header',$header);
                $header->set_var( 'page_title', $A['pagetitle']);
                $header->set_var( 'css_url', $_CONF['layout_url'] . '/style.css' );
                $header->set_var( 'theme', $_CONF['theme'] );
                $header->parse ('output', 'header');
                $siteheader =  $header->finish ($header->get_var('output'));
                $footer = new Template($_CONF['path_layout'] . 'nexcontent');
                $footer->set_file (array ('footer'=>'footer.thtml'));
                $footer->parse ('output', 'footer');
                $sitefooter =  $footer->finish ($footer->get_var('output'));
                break;
            default:
                $siteheader = COM_siteHeader('none');
                $sitefooter = COM_siteFooter();
                break;
        }

    if (SEC_hasRights('nexcontent.edit')) {
        $pageview->set_var ('editlink', '<a href="'.$_CONF['site_admin_url'] . '/plugins/nexcontent/index.php?op=editPage&mode=edit&pageid='.$A['id'].'">[Edit]</a>');
        DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET hits=hits+1 WHERE id='{$A['id']}'");
    } else {
        $pageview->set_var ('editlink', '');
    }
    $pageview->set_var ('siteheader', $siteheader);
    $pageview->set_var ('heading', $A['heading']);
    $pageview->set_var ('content', nexcontent_formatPage($A['pid'], $A['id'], $A['content']));
    $pageview->set_var ('sitefooter', $sitefooter);
    $pageview->parse ('output', 'pageview');
    $retval .= $pageview->finish ($pageview->get_var('output'));

    return $retval;

}



function nexcontent_showMenu($type='header') {
    global $_CONF,$CONF_SE,$_TABLES,$CONF_NEXMENU;

    $menutype_array = array (
        'header'        => 1,
        'block'         => 2,
        'single_block'  => 5
    );
    $menutype = $menutype_array[$type];

    if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexcontent'") == '1') {
        $sql = "SELECT id,sid,name,type FROM {$_TABLES['nexcontent_pages']} WHERE pid='0' AND is_draft='0' and menutype='$menutype'";
        $sql .= COM_getPermSQL('AND');
        $sql .= ' ORDER BY pageorder,id';
        $query = DB_query($sql);
        $url = '';
        while (list ($id, $sid, $title, $pagetype) = DB_fetchArray($query)) {
            if ($pagetype == 'link') {
                $url = DB_getItem($_TABLES['nexcontent_pages'], 'content', "id=$id");
                $url = str_replace('[siteurl]', $_CONF['site_url'], $url);
            }
            if ($sid != '') {
                $page = $sid;
            } else {
                $page = $id;
            }
            $sqlcount = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='$id' AND (menutype ='$menutype' OR menutype=3) AND is_draft=0";
            if ($CONF_NEXMENU['milonicmode'] == false) {
                if (DB_numRows(DB_query($sqlcount)) > 0) {
                    $retval .= nexcontent_recursiveBuildCSSMenu($id, $menutype);
                } else {
                    if ($pagetype == 'link') {
                        $retval .= "<li><a href=\"$url\">$title</a></li>";
                    }
                    else {
                        $retval .= "<li><a href=\"{$CONF_SE['public_url']}/index.php?page=$page\">$title</a></li>";
                    }
                }
            }
            else {
                if (DB_numRows(DB_query($sqlcount)) > 0) {
                    if ($pagetype == 'link') {
                        $retval .= 'aI("text='.$title.';showmenu=nexcontent'.$id.';url='.$url.';");';
                    }
                    else {
                        $retval .= 'aI("text='.$title.';showmenu=nexcontent'.$id.';url='.$CONF_SE['public_url'].'/index.php?page='.$page.';");';
                    }
                } else {
                    if ($pagetype == 'link') {
                        $retval .= 'aI("text='.$title.';url='.$url.';");';
                    }
                    else {
                        $retval .= 'aI("text='.$title.';url='.$CONF_SE['public_url'].'/index.php?page='.$page.';");';
                    }
                }
            }
        }
        return $retval;
    }
}


function nexcontent_recursiveBuildCSSMenu($id, $menutype, $pid='', $retval='') {
    global $_CONF, $CONF_SE, $_TABLES;

    if ($pid != '') {
        $sql = "SELECT id, sid, name, type FROM {$_TABLES['nexcontent_pages']} WHERE pid=$pid";
    }
    else {
        $sql = "SELECT id, sid, name, type FROM {$_TABLES['nexcontent_pages']} WHERE id=$id";
    }

    $sql .=  " AND is_draft='0' AND (menutype='$menutype' OR menutype=3)";
    $sql .= COM_getPermSQL('AND');
    $sql .= ' ORDER BY pageorder,id';
    $query = DB_query($sql);

    while ($A = DB_fetchArray($query)) {
        if ($sid != '') {
            $page = $A['sid'];
        } else {
            $page = $A['id'];
        }
        if ($A['type'] == 'category') {
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            if ($menutype == 1) {       //if menutype == headermenu
                $t->set_file('menu','headersubmenu.thtml');
            } else {
                $t->set_file('menu','submenu.thtml');
            }
            $t->set_var('menuitem_url',"{$CONF_SE['public_url']}/index.php?page=$page");
            $t->set_var('menuitem_label',$A['name']);
            $t->set_var('imgclass','fly');
            if ($i == $menurows) {
                $t->set_var('lastitemclass','class="enclose"');
            }

            $t->set_var('submenu_items',nexcontent_recursiveBuildCSSMenu($id, $menutype, $A['id'], ''));
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
        }
        else if ($A['type'] == 'page') {
            $retval .= "<li><a href=\"{$CONF_SE['public_url']}/index.php?page=$page\">{$A['name']}</a></li>";
        }
        else if ($A['type'] == 'link') {
            $url = DB_getItem($_TABLES['nexcontent_pages'], 'content', "id={$A['id']}");
            $url = str_replace('[siteurl]', $_CONF['site_url'], $url);
            $retval .= "<li><a href=\"$url\">{$A['name']}</a></li>";
        }
    }

    return $retval;
}


function nexcontent_recursiveBuildMilonicMenu($id, $menutype, $pid='', $retval='') {
    global $_CONF, $CONF_SE, $_TABLES;

    if ($pid != '') {
        $sql = "SELECT id, sid, name, type FROM {$_TABLES['nexcontent_pages']} WHERE pid=$pid";
    }
    else {
        $sql = "SELECT id, sid, name, type FROM {$_TABLES['nexcontent_pages']} WHERE id=$id";
    }

    $sql .=  " AND is_draft='0' AND (menutype='$menutype' OR menutype=3)";
    $sql .= COM_getPermSQL('AND');
    $sql .= ' ORDER BY pageorder,id';
    $query = DB_query($sql);

    while ($A = DB_fetchArray($query)) {
        if ($A['type'] == 'category') {
            $retval .= 'aI("text='.$A['name'].';showmenu=nexcontent'.$A['id'].';url='.$CONF_SE['public_url'].'/index.php?page='.$A['sid'].'");';
        }
        else if ($A['type'] == 'page') {
            $retval .= 'aI("text='.$A['name'].';url='.$CONF_SE['public_url'].'/index.php?page='.$A['sid'].'");';
        }
        else if ($A['type'] == 'link') {
            $url = DB_getItem($_TABLES['nexcontent_pages'], 'content', "id={$A['id']}");
            $url = str_replace('[siteurl]', $_CONF['site_url'], $url);
            $retval .= 'aI("text='.$A['name'].';url='.$url.'");';
        }
    }

    return $retval;
}

function nexcontent_getMenutype($pageid) {
    global $_TABLES;
    $pmenutype = 0;

    do {
        $pageid = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id=$pageid");
        if ($pageid <= 0) {
            break;
        }

        $pmenutype = DB_getItem($_TABLES['nexcontent_pages'], 'menutype', "id=$pageid");
    } while ($pmenutype != 3);

    return $pmenutype;
}


/* Note: Only shows links to pages */
function nexcontent_pageindex($category,$limit=0) {
    global $_CONF,$CONF_SE,$_TABLES;

    $block = new Template($_CONF['path_layout'] . 'nexcontent');
    $block->set_file ('pageindex', 'pageindex.thtml');
    $block->set_file ('records', 'pageindex_record.thtml');
    $sql = "SELECT id,sid,name FROM {$_TABLES['nexcontent_pages']} WHERE pid='$category' and type='page' AND is_draft=0";
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    $query = DB_query($sql);
    while (list ($id,$sid,$title) = DB_fetchArray($query)) {
        if ($sid != '') {
            $page = $sid;
        } else {
            $page = $id;
        }
        $block->set_var('url', $CONF_SE['public_url'] . "/index.php?page=$page");
        $block->set_var('title', $title);
        $block->parse ('pagelinks', 'records',true);
    }
    if (DB_numRows($query) > 0) {
        $block->parse ('output', 'pageindex');
        $retval = $block->finish ($block->get_var('output'));
        return $retval;
    } else {
        return '';
    }
}

/* Display Category Block */
function phpblock_nexcontentBlockmenu($pageid='') {
    global $page,$_CONF,$_TABLES,$CONF_SE,$CONF_NEXMENU,$nexmenuVersion;
    $retval = '';

    if ($pageid == '') {
        $pageid = $page;
    }

    $pageid = intval ($pageid);
    if ($pageid != 0) {
        $menutype = DB_getItem($_TABLES['nexcontent_pages'], 'menutype', "id=$pageid");
        if ($menutype == 3) {
            $menutype = nexcontent_getMenutype($pageid);
        }

        if ($menutype == 5) {
            $pid = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id=$pageid");
            $title = DB_getItem($_TABLES['nexcontent_pages'], 'heading', "id=$pageid");
            if ($CONF_NEXMENU['milonicmode']) {
                if ($nexmenuVersion >= 2.5) {
                    $query = DB_query("SELECT blockmenu_properties,blockmenu_style FROM {$_TABLES['nexmenu_config']}");
                    list ($properties,$style) = DB_fetchArray($query);
                } else {
                    $properties = '';
                    $style = '';
                }

                $retval  = COM_startBlock($title, '', 'blockheader-left.thtml');
                $retval .='<script>
                    with(milonic=new menuname("nexContent Menu")) {' . LB;
                $retval .= $CONF_NEXMENU['blockmenu_default_styles'] . $properties . LB;
                $retval .= 'style='.$style.';';

                $retval .= nexcontent_recursiveBuildMilonicMenu($pageid, 5, $pid, '');

                $retval .=  '} drawMenus(); </script>';
                $retval .= COM_endBlock('blockfooter-left.thtml');
            }
            else {
                $retval  = COM_startBlock($title, '', 'blockheader-left.thtml');
                $retval .= '<div class="menu"><ul>';
                $retval .= nexcontent_recursiveBuildCSSMenu($pageid, 5, $pid, '');
                $retval .= '</ul></div>';
                $retval .= COM_endBlock('blockfooter-left.thtml');
            }
        }
    }

    return $retval;
}


/* GL-Menu API function to generate requires Milonic Javascript functions */
function plugin_nexmenuCreateMenus_nexcontent() {
    global $_CONF,$_TABLES,$CONF_SE,$CONF_NEXMENU;

    /* Generate the JS Menu Functions that are needed for the Content Editor submenus */
    $location = 'header';
    $retval .= '';
    if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexcontent'") == '1') {
        $q1 = DB_query("SELECT id,pid,menutype FROM {$_TABLES['nexcontent_pages']} WHERE type='category'");
        while (list($category,$pid,$menutype) = DB_fetchArray($q1)) {
            if ($pid > 0 AND $menutype == 3) {       // Same as parent - need to determine parent type
                $parenttype = DB_getItem($_TABLES['nexcontent_pages'],"menutype", "id='$pid'");
                if ($parenttype != '' and $parenttype > 0) {
                    $menutype = $parenttype;
                }
            }
            //echo "<br>category:$category, pid:$pid, type:$menutype";
            //echo "<br>SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='$category' AND menutype > 0";
            $sqlcount = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='$category' AND menutype > 0";
            if (DB_numRows(DB_query($sqlcount)) > 0) {
               $retval .= LB . 'with(milonic=new menuname("nexcontent'.$category.'")) {' . LB . "\t";
                        if ($menutype == '1') {
                            $retval .=  'style='.$CONF_NEXMENU['headersubmenustyle'] .';';
                        } else {
                            $retval .=  'style='.$CONF_NEXMENU['blockmenustyle'] .';';
                        }
                        $sql = "SELECT id,sid,name,type,is_menu_newpage FROM {$_TABLES['nexcontent_pages']} WHERE pid='{$category}' AND menutype > 0 AND is_draft=0";
                        $sql .= COM_getPermSQL('AND');
                        $sql .= ' ORDER BY pageorder,id';
                        $q2 = DB_QUERY($sql);
                        $menudata = '';
                        while (list ($id,$sid, $label,$type,$is_newpage) = DB_fetchArray($q2)) {
                            $target = ($is_newpage) ? 'target=_new;' : '';
                            if ($sid != '') {
                                $page = $sid;
                            } else {
                                $page = $id;
                            }
                            if ($type == 'category') {
                                $check_sql  = "SELECT * FROM {$_TABLES['nexcontent_pages']} ";
                                $check_sql .= "WHERE pid=$id ";
                                $check_sql .= "AND is_draft != 1 ";
                                $check_sql .= "AND (menutype=3 OR menutype=$menutype);";
                                $subitem_count = DB_query($check_sql);
                                if (DB_numRows($subitem_count) > 0) {
                                    $menudata .= 'aI("text='.$label.';'.$target.'showmenu=nexcontent'.$id.';url='.$CONF_SE['public_url'].'/index.php?page='.$page.';");';
                                }
                                else {
                                    $menudata .= 'aI("text='.$label.';'.$target.'url='.$CONF_SE['public_url'].'/index.php?page='.$page.';");';
                                }
                            } else if ($type == 'page') {
                                $menudata .= 'aI("text='.$label.';'.$target.'url='.$CONF_SE['public_url'].'/index.php?page='.$page.';");';
                            } else if ($type == 'link') {
                                $url = DB_getItem($_TABLES['nexcontent_pages'], 'content', "id=$id");
                                $url = str_replace('[siteurl]', $_CONF['site_url'], $url);
                                $menudata .= 'aI("text='.$label.';'.$target.'url='.$url.';");';
                            }
                        }
                        if ($menudata != '') {
                            $retval .=  $menudata;
                        }

              $retval .=  '}';
            }
        }
    }
    return $retval;

}


// function to test permissions on directories
function plugin_chkPermissions_nexcontent(&$template) {
    global $CONF_SE;

    if (!$file = @fopen ($CONF_SE['uploadpath'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_SE['uploadpath'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_SE['uploadpath'] . 'test.txt');
    }

    return $display;
}


/* Test Function */
function myfunction($parm1='',$parm2='', $parm3='') {
    $retval = "<hr>This is a custom function example. The parms passed are:";
    $retval .=  "<br>Parm1: $parm1";
    $retval .=  "<br>Parm2: $parm2";
    $retval .=  "<br>Parm3: $parm3";
    return $retval;
}

?>