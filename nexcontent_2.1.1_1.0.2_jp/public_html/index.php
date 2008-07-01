<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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

require_once("../lib-common.php"); // Path to your lib-common.php
require_once($_CONF['path'] . 'plugins/nexcontent/debug.php');  // Common Debug Code
require_once($_CONF['path'] . 'plugins/nexcontent/library.php');  // Common Debug Code

$SE_SHOWBLOCK = true;

$myvars = array('topic','page');
ppGetData($myvars,true);

/* Add .. check to see user has access to this page */
$sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$page' OR sid='$page' AND is_draft=0";
$sql .= COM_getPermSQL('AND');
$query = DB_query($sql);

if ($page != '' AND DB_numRows($query) > 0) {

    list ($page) = DB_fetchArray($query);
    $pageview = new Template($_CONF['path_layout'] . 'nexcontent');

    $query = DB_query("SELECT pid,type,name,heading,pagetitle,blockformat,heading,content,show_submenu,submenu_item, show_breadcrumbs FROM {$_TABLES['nexcontent_pages']} WHERE id='{$page}'");
    list($pid, $type,$title,$heading,$pagetitle,$blkformat,$heading,$content,$show_submenu,$submenu_item, $show_breadcrumbs) = DB_fetchArray($query);

    // Check if user has permissions to edit this page
    $sql = "SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE id='$page' ";
    $sql .= COM_getPermSQL('AND',0,3);
    $bquery = DB_query($sql);
    if (DB_numRows($bquery) > 0) {
        $editperms = true;
        $pageview->set_file ('editlink' , 'editlink.thtml');
    } else {
       $editperms = false;
    }

    if($type == 'category' ) {
        $pageview->set_file ('pageview', 'category.thtml');
    } else {
        $pageview->set_file ('pageview','page.thtml');
    }
    $pageview->set_file ('submenu' , 'submenu.thtml');
    $pageview->set_file ('breadcrumbs' , 'breadcrumbs.thtml');
    $pageview->set_file ('breadcrumb_link' , 'breadcrumb_link.thtml');
    $pageview->set_file ('centerblock' , 'centerblock.thtml');
    $pageview->set_file ('footerblock' , 'footerblock.thtml');

    switch ($blkformat) {
        case 'allblocks' :
            $siteheader = COM_siteHeader('menu',$pagetitle);
            $sitefooter = COM_siteFooter(true);
            break;
        case 'leftonly' :
            $siteheader = COM_siteHeader('menu',$pagetitle);
            $sitefooter = COM_siteFooter();
            break;
        case 'rightonly' :
            $siteheader = COM_siteHeader('none',$pagetitle);
            $sitefooter = COM_siteFooter(true);
            break;
        case 'customblocks' :
            $siteheader = COM_siteHeader( array('nexcontent_showBlocks',$content),$pagetitle ) ;
            $sitefooter = COM_siteFooter(true, array('nexcontent_showBlocks',$content),$pagetitle );
            break;
        case 'blankpage' :
            $header = new Template($_CONF['path_layout'] . 'nexcontent');
            $header->set_file (array ('header'=>'header.thtml'));
            $header->set_var( 'page_title', $heading );
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
            $siteheader = COM_siteHeader('none',$pagetitle);
            $sitefooter = COM_siteFooter();
            break;
    }

    if ( $editperms AND $_GET['preview'] != 1) {
        if($type == 'category' ) {
            $editlink = '<a href="'.$_CONF['site_admin_url'] . '/plugins/nexcontent/index.php?op=editCategory&mode=edit&pageid='.$page.'">[Edit]</a>';
        } else {
            $editlink = '<a href="'.$_CONF['site_admin_url'] . '/plugins/nexcontent/index.php?op=editPage&mode=edit&pageid='.$page.'">[Edit]</a>';
        }
        $pageview->set_var ('edit_link', '<span style="padding-left:10px;">'.$editlink.'</span>');
        $pageview->set_var ('top_editlink', '<td width="7%" nowrap>'.$editlink.'</td>');
        $pageview->parse ('bottom_editlink','editlink');

    } else {
        $pageview->set_var ('top_editlink', '');
        $pageview->set_var ('bottom_editlink', '');
    }

    DB_query("UPDATE {$_TABLES['nexcontent_pages']} SET hits=hits+1 WHERE id='{$page}'");

    /* Check for centerblock tag in page content */
    $start_pos = strpos($content,'[centerblock');
    if ($start_pos !== FALSE) {
        $end_pos = strpos (strtolower ($content), ']', $start_pos);
        $taglength = $end_pos - $start_pos + 1;
        $tag = substr ($content, $start_pos, $taglength);
        $parms = explode (':', $tag);
        $function = str_replace(']','',$parms[1]);
        if (function_exists($function)) {
            $blockContent = $function();
            $content = str_replace ($tag,'',$content);
            $pageview->set_var ('centerblock_content', $blockContent);
            $pageview->parse ('centerblock','centerblock');
        }
    }

    /* Check for footerblock tag in page content */
    $start_pos = strpos($content,'[footerblock');
    if ($start_pos !== FALSE) {
        $end_pos = strpos (strtolower ($content), ']', $start_pos);
        $taglength = $end_pos - $start_pos + 1;
        $tag = substr ($content, $start_pos, $taglength);
        $parms = explode (':', $tag);
        $function = str_replace(']','',$parms[1]);
        if (function_exists($function)) {
            $blockContent = $function();
            $content = str_replace ($tag,'',$content);
            $pageview->set_var ('footerblock_content', $blockContent);
            $pageview->parse ('footerblock','footerblock');
        }
    }

    /* Build the page submenu */
    if ($show_submenu > 0) {
        if ($type == 'category') {
            if ($show_submenu == 2) {
                $menupid = $pid;
            } else {
                $menupid = $page;
            }
        } elseif ($show_submenu == 2) {  // Check to see if parent submenu should be shown
            $menupid = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id='$pid'");
            if ($menupid == 0) {
                $menupid = $pid;
            }
        } else {
            $menupid = $pid;
        }
        $sql = "SELECT id,sid,name FROM {$_TABLES['nexcontent_pages']} WHERE (pid=$menupid or id=$menupid) AND submenu_item = '1' AND is_draft=0 ";
        $sql .= COM_getPermSQL('AND');
        $sql .=  " ORDER by type,pageorder ASC";
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            $navbarMenu = array();
            while (list ($id,$sid,$name) = DB_fetchArray($query)) {
                if ($sid != '') {
                    $page = $sid;
                } else {
                    $page = $id;
                }
                $navbarMenu[$name] = $CONF_SE['public_url'] ."/index.php?page=$page";
            }
            $pageview->set_var ('navbar', nexcontent_submenu($navbarMenu,$title));
            $pageview->parse ('submenu','submenu');
        }
   }

    if ($show_breadcrumbs) {
        /* Build the breadcrumb links */
        $pageview->set_var('LANG_where','Where you are:');
        $curid = $page;
        $curpid = $pid;
        $sid = DB_getItem($_TABLES['nexcontent_pages'], 'sid', "id='$page'");
        $pagelinks = array();
        while ($curpid > 0) {
            if ($sid != '') {
                $pagelinks[$sid] = $title;
            }
            else {
                $pagelinks[$curid] = $title;
            }
            $curid = $curpid;
            $curpid = DB_getItem($_TABLES['nexcontent_pages'], 'pid', "id='$curid'");
            $title = DB_getItem($_TABLES['nexcontent_pages'], 'name', "id='$curid'");
            $sid = DB_getItem($_TABLES['nexcontent_pages'], 'sid', "id='$curid'");
        }

        if ($sid != '') {
            $pagelinks[$sid] = $title;
        }
        else {
            $pagelinks[$curid] = $title;
        }
        $links = array_reverse($pagelinks,true);
        $breadcrumbs = '';
        $i = 1;
        foreach ($links as $pageid => $pagename) {
            if ($i > 1) {
                $pageview->set_var ('separator', $CONF_SE['breadcrumb_separator']);
            } else {
                $pageview->set_var ('separator', '');
            }
            $pageview->set_var ('public_url', $CONF_SE['public_url']);
            $pageview->set_var ('pageid', $pageid);
            $pageview->set_var ('pagename', $pagename);
            $pageview->parse ('breadcrumb_links','breadcrumb_link',true);
            $i++;
        }


    }
    $pageview->parse ('breadcrumbs','breadcrumbs');

    PLG_templateSetVars( 'nexcontent', $pageview );

    $pageview->set_var ('siteheader', $siteheader);
    $pageview->set_var ('heading', $heading);
    $pageview->set_var ('pagetitle',$pagetitle);
    $pageview->set_var ('content', nexcontent_formatPage($catid, $page, $content));
    $pageview->set_var ('sitefooter', $sitefooter);
    $pageview->parse ('output', 'pageview');
    echo $pageview->finish ($pageview->get_var('output'));
} else {
    echo COM_refresh($_CONF['site_url'] . '/index.php?msg=1&plugin=nexcontent');
    exit;
}

?>