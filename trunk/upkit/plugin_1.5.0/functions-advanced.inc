<?php
//
// +---------------------------------------------------------------------------+
// | Universal Plugin 1.0.1 for Geeklog - The Ultimate Weblog                  |
// +---------------------------------------------------------------------------+
// | functions-advanced.inc                                                    |
// | This file contains example code for the comment, stats, search anf        |
// | moderation APIs for Geeklog                                               |
// |                                                                           |
// | This is a generic functions.inc for Geeklog 1.3.6.  Edit this file with   |
// | your favorite editor and replace every instance of search with the name   |
// | of your plugin.  Modify them to fit your plugin and them copy these       |
// | routines to your plugins function.inc.                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002 by the following authors:                              |
// |                                                                           |
// | Author:                                                                   |
// | Constructed with the Universal Plugin                                     |
// | Copyright (C) 2002 by the following authors:                              |
// | Tom Willett                 -    twillett@users.sourceforge.net           |
// | Blaine Lang                 -    langmail@sympatico.ca                    |
// | Vincent Furia               -    http://furia.dyndns.org                  |
// | The Universal Plugin is based on prior work by:                           |
// | Tony Bibbs                  -    tony@tonybibbs.com                       |
// |                                                                           |
// | modified by mystral-kk      - geeklog AT mystral-k DOT net                |
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

/**
* User has reuested to create a comment for the plugin
* Redirects user to comment form if initial comment for plugin record or returns
* formated HTML including the Site footer and the comments
*
* @param        int      $comment_id      GL Comment table primary key.
* @param        string   $commentmode     Has user selected a new comment preview mode (Flat, Nested, Threaded, None)
* @param        string   $order           Has the user selected a different comment first (Newest or Oldest comment first)
* @param        string   $reply           Has the user used the reply button on the Comment Bar
* @return       string   Formated HTML containing site footer and the comments with the comment command bar
*                        or redirect user to create top level comment
*/
function plugin_commentform_{plugin}($comment_id, $commentmode = '', $order = '', $reply = '') {
    global $_CONF,$_TABLES,$_FM_TABLES;

    // Pass the comment type - use the plugin name
    $type="{plugin}";
    // Recommended Comment ID is to use the plugin_name and a ID that links back to your plugin record.
    // In this case, I need to parse out the Plugin Record ID
    $file_id = substr($comment_id,9, strlen($comment_id-9));
    // Determine if there are existing comments for this plugin record
    $commentCount = DB_count($_TABLES['comments'],'sid',$comment_id);
    $title = DB_getItem($_FM_TABLES['{plugin}'],'title',"lid=$file_id");
    // Was the comment mode passed - if not default it.
    if ($commentmode == "") {
        $commentmode = $_CONF['comment_mode'];
    }
    // Has the user selected a new comment order using the Comment Bar
    if ($order == "") {
        $order="ASC";
    }
    // If this is the first comment or someone used the reply button on the comment header bar
    if ($commentCount == 0 || $reply != "") {
        $story=$comment_id;
        $pid=0;
        $type="{plugin}";
        echo COM_refresh($_CONF['site_url'] . "/comment.php?sid=$comment_id&amp;pid=$pid&amp;type=$type");
    } else {
        $display = COM_siteHeader()
        // Optionally call a plugin function to return a formated view of the Plugin record so it's displayed above the comments
        $display .= plugin_commentparent_{plugin}($file_id);
        // Return the formated comment records with the Comment Header and active reply links
        $display .= COM_userComments($comment_id,$title,$type,$order,$commentmode);
        $display .= COM_siteFooter();
    }
    return $display;
}

/**
* Check to see if this Plugin uses comments
*
* Geeklog is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how geeklog
* does comments.
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_{plugin}()
{
    return false;
}

/**
* Shows the statistics for the plugin on stats.php.  If
* $showsitestats is 1 then we are to only print the overall stats in the 'site
* statistics' box otherwise we show the detailed stats for the plugin
*
* Universal Plugin:  Modify/uncomment use it
*
* @param    int showsitestate   Flag to let us know which stats to get
* @return   string  returns formatted HTML to insert in stats page
*
*/
function plugin_showstats_{plugin}($showsitestats)
{
    global $_CONF, $_TABLES;

    $stat_templates = new Template($_CONF['path_layout'] . 'stats');
    $stat_templates->set_file(array('itemstats'=>'itemstatistics.thtml',
                            'statrow'=>'singlestat.thtml'));
        $retval='';
    if ($showsitestats == 1) {
        // This shows in the summary box
        $total_pages=DB_count($_TABLES['upkit_name']);     // Fill this with count for summary
        $summary_label = "{plugin}";                   // Fill this with label displayed

        $retval = "<table border = '0' width='100%' cellspacing='0' cellpadding='0'>";
        $retval .= "<tr><td>$summary_label</td>";
        $retval .= "<td align='right'>" . $total_pages . "&nbsp;&nbsp </td></tr></table>";
    } else {
        // This fills the top the box  Use the following as a template
        $result = DB_query("SELECT id, title, hits from {$_TABLES['upkit_name'] WHERE hits > 0 ORDER BY hits desc LIMIT 10");
        $nrows  = DB_numRows($result);
        $retval .= COM_startBlock("Top Ten Static Pages");
        if ($nrows > 0) {
            $stat_templates->set_var('item_label',"Page Title");
            $stat_templates->set_var('stat_name',"Hits");
            for ($i = 0; $i < $nrows; $i++) {
                $A = DB_fetchArray($result);
                $stat_templates->set_var('item_url', $_CONF['site_url'] . '/{plugin}/index.php?item=' . $A['id']);
                $stat_templates->set_var('item_text', $A['title']);
                $stat_templates->set_var('item_stat', $A['hits']);
                $stat_templates->parse('stat_row','statrow',true);
            }
            $stat_templates->parse('output','itemstats');
            $retval .= $stat_templates->finish($stat_templates->get_var('output'));
        } else {
            $retval .= 'It appears that there are no {plugin}s on this site or no one has ever viewed them.';
        }
        $retval .= COM_endBlock();
    }
    return $retval;
}

/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search!
*
*/
function plugin_searchtypes_{plugin}()
{
    global $LANG_{lang_var_postfix};

    $tmp['{plugin}'] = $LANG_{lang_var_postfix}['externpages'];
    return $tmp;
}


/**
* this searches for pages matching the user query and returns an array of
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
function plugin_dopluginsearch_{plugin}($query, $datestart, $dateend, $topic, $type, $author)
{
    global $_TABLES, $_CONF, $LANG_{lang_var_postfix};

    if (empty($type)) {
        $type = 'all';
    }

    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type <> '{plugin}') {
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = '{plugin}';
        $plugin_results->searchlabel = $LANG_{lang_var_postfix}['externpages'] . $LANG_{lang_var_postfix}['results'];
        return $plugin_results;
    }

    // Build search SQL - Modified to exclude static PHP pages from search.
    $sql = "SELECT * from " . $_TABLES['{plugin}'];
    $result = DB_query($sql);


    // OK, now create new plugin object and insert table header labels
    require_once($_CONF['path_system'] . 'classes/plugin.class.php');
    $plugin_results = new Plugin();
    $plugin_results->plugin_name = '{plugin}';
    $plugin_results->searchlabel = $LANG_{lang_var_postfix}['externpages'] . $LANG_{lang_var_postfix}['results'];
    $plugin_results->addSearchHeading($LANG_{lang_var_postfix}['titlemsg']);
    $plugin_results->addSearchHeading($LANG_{lang_var_postfix}['urlmsg']);
    $plugin_results->addSearchHeading($LANG_{lang_var_postfix}['hitsmsg']);
    $mycount = DB_numRows($result);

    // NOTE if any of your data items need to be links then add them here!
    // make sure data elements are in an array and in the same order as your
    // headings above!
    for ($i = 1; $i <= $mycount; $i++) {
        $A = DB_fetchArray($result);

        if(SEC_hasAccess($A[owner_id],$A[group_id],$A[perm_owner],$A[perm_group],$A[perm_members],$A[perm_anon])){
            // Get text to search
            $cnts = implode('',file($pth));
            // Search it
            if (stristr($cnts,$query) != '') {
                $rcnt++;
                $A['title'] = stripslashes($A['title']);
                $row = array($A['title'],
                      '<a href="' . $url . '">' . $A['url'] . "</a>",
                     $A['hits']);
                $plugin_results->addSearchResult($row);
            }
        }

    }
    $plugin_results->num_searchresults = $rcnt;
    $plugin_results->num_itemssearched = DB_count($_TABLES['{plugin}']);

    return $plugin_results;
}

/**
*
* Example Plugin Moderation Functions
* authored by Vincent Furia, http://furia.dyndns.org
*
*/

/**
*
* Checks that the current user has the rights to moderate the
* plugin, returns true if this is the case, false otherwise
*
* @return        boolean       Returns true if moderator
*
*/
function plugin_ismoderator_lists() {
    global $_USER, $_TABLES;

    return SEC_hasRights('lists.admin');
}

/**
* Returns the number of submissions waiting moderation
*
* Calculates the number of submissions that are currently waiting
* to be moderated and returns that number.
*
* @return      int       number of submissions awaiting moderation
*/
function plugin_submissioncount_lists() {
    global $_TABLES;
    return DB_count($_TABLES['listssubmission']);
}

/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from <plugin>submission
* to <plugin> tables, within the function we handle all other approval
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationapprove_lists($id) {
    global $_TABLES, $_USER, $_CONF;

    $result = DB_query("SELECT * FROM " . $_TABLES['groups']
                     . " WHERE grp_name = 'lists Admin'");
    $group  = DB_fetchArray($result);
    if ($_CONF['listsarchive'] == 'optional') {
        $archive = $_CONF['listsarchivedefault'];
    } elseif ($_CONF['listsarchive'] == 'no') {
        $archive = 0;
    } else {     // $_CONF['listsarchive'] == 'yes'
        $archive = 1;
    }
    $sql = "UPDATE " . $_TABLES['lists'] . " SET owner_id = " . $_USER['uid']
         . ", group_id = " . $group['grp_id'] . ", html = " . $_CONF['listshtml']
         . ", archive = $archive WHERE ml_id = '$id'";
    $result = DB_query($sql);

    if (DB_error()) {
       return 'Error';
    }
    return '';
}

/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from <plugin>submission
* table, within this function we handle all other deletion
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationdelete_lists($id) {
    global $_TABLES;

    // these tables should not contain any rows with ml_id = $id
    // this is done 'just in case'
    DB_delete($_TABLES['listsubscriptions'], 'ml_id', $id);
    DB_delete($_TABLES['listarchive'], 'ml_id', $id);
    DB_delete($_TABLES['listpermissions'], 'ml_id', $id);
}

/**
* saves submission to listssubmission table
*
* takes data input by submission form and populates the <plugin>submission
* table.
*
* @param       array       $A       array (key => value) of form items
* @return      boolean     returns true if successful
*
*/
function plugin_savesubmission_lists($A) {
    global $_TABLES, $_USER, $_CONF;

    // check for missing fields
    if (empty($A['ml_name']) || empty($A['ml_descr'])) {
        return false;
    }

    if (empty($_USER['uid'])) {
        $owner_id = 1;
    } else {
        $owner_id = $_USER['uid'];
    }

    if (SEC_hasRights('links.admin')) {
        $result = DB_getItem($_TABLES['groups'], '*', "grp_name = 'lists Admin'");
        if ($_CONF['listsarchive'] == 'optional') {
            $archive = $_CONF['listsarchivedefault'];
        } elseif ($_CONF['listsarchive'] == 'no') {
            $archive = 0;
        } else {     // $_CONF['listsarchive'] == 'yes'
            $archive = 1;
        }
        DB_save($_TABLES['lists'], 'ml_id, ml_name, ml_descr, html, archive, owner_id, group_id',
                "'" . COM_makeSid() . "', '" . $A['ml_name'] . "', '" . $A['ml_descr']
              . "', " . $_CONF['listshtml'] . ", $archive, $owner_id, " . $result['grp_id']);
    } elseif ($_CONF['listssubmission'] == 1) {
        DB_save($_TABLES['listssubmission'],
                'ml_id, ml_name, ml_descr',
                "'" . COM_makeSid() . "', '" . $A['ml_name'] . "', '"
              . $A['ml_descr'] . "'");
    } else {
        return false;
    }

    if (DB_error()) {
        return false;
    }

    return true;
}

/**
* returns list of moderation values
*
* The array returned contains (in order): the row 'id' label, main plugin
* table, moderation fields (comma seperated), and plugin submission table
*
* @return       array        Returns array of useful moderation values
*
*/
function plugin_moderationvalues_lists() {
    global $_TABLES;

    return array('ml_id', $_TABLES['lists'], 'ml_id, ml_name, ml_descr'
                 , $_TABLES['listssubmission']);
}

/**
* show lists for moderation on submissions page
*
* Uses the Plugin class to return data required by moderation.php to list
* plugin objects that need to be moderated.
*
* @return       Plugin       return Plugin class containing submission data
*
*/
function plugin_itemlist_lists() {
    global $_TABLES;

    if (plugin_ismoderator_lists()) {
        $plugin = new Plugin();
        $plugin->submissionlabel = 'Mailing List Submissions';
        $plugin->getsubmissionssql = "SELECT ml_id as id, ml_name, ml_descr FROM "
                                   . $_TABLES['listssubmission'];
        $plugin->addSubmissionHeading('List Name');
        $plugin->addSubmissionHeading('List Description');

        return $plugin;
    }
}

/**
* Returns the plugins submission form
*
* Returns a string contain the HTML of the submission form for the plugin.
*
* @return       string       The HTML form
*
*/
function plugin_submit_lists() {
    global $_CONF, $LANG12, $HTTP_POST_VARS;

    if ($_CONF['listssubmission'] == 0 && !SEC_hasRights('lists.admin')) {
        return "Submission queue disabled for mailing lists";
    }

    if ($HTTP_POST_VARS['mode'] == $LANG12[32]) { // preview
        $A = $HTTP_POST_VARS;
    $ml_id = $A['ml_id'];
    } else {
        $ml_id = COM_makesid();
    }

    $template = new Template($_CONF['path'] . "plugins/lists/templates/public");
    $template->set_file(array('form' => 'submit_form.thtml'));
    $template->set_var('site_url', $_CONF['site_url']);
    $template->set_var('lang_name', 'List Name');
    $template->set_var('ml_name', $A['ml_name']);
    $template->set_var('lang_descr', 'Description');
    $template->set_var('ml_descr', $A['ml_descr']);
    $template->set_var('ml_id', $ml_id);
    $template->set_var('lang_preview', $LANG12[32]);
    $template->set_var('lang_save', $LANG12[8]);

    return $template->parse('output', 'form');
}


?>