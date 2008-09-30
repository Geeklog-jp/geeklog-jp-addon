<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | public_html/{plugin}/index.php                                            |
// +---------------------------------------------------------------------------|
// | Copyright (C) 2008 by {author_name} - {author_email}                      |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// | Copyright (C) 2002 by the following authors:                              |
// | Tom Willett               -    twillett AT users DOT sourceforge DOT net  |
// | Blaine Lang               -    langmail AT sympatico DOT ca               |
// | The Universal Plugin is based on prior work by:                           |
// | Tony Bibbs                -    tony AT tonybibbs DOT com                  |
// | Modified by:                                                              |
// | mystral-kk                -    geeklog AT mystral-kk DOT net              |
// | dengen                    -    taharaxp AT gmail DOT com                  |
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

require_once '../lib-common.php';

if (!in_array('{plugin}', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

//// Check user has rights to access this page
//if (!SEC_hasRights('{plugin}.edit, {plugin}.view, {plugin}.admin','OR')) {
//    // Someone is trying to illegally access this page
//    COM_errorLog("Someone has tried to illegally access the {plugin} page.  "
//               . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR", 1);
//    $display  = COM_siteHeader();
//    $display .= COM_startBlock($LANG_{lang_var_postfix}['access_denied']);
//    $display .= $LANG_{lang_var_postfix}['access_denied_msg'];
//    $display .= COM_endBlock();
//    $display .= COM_siteFooter( true );
//    echo $display;
//    exit;
//}

if (empty($_USER['username']) &&
    (($_CONF['loginrequired'] == 1) || ($_{conf_var_prefix}_CONF['{plugin}loginrequired'] == 1))) {
    $display = COM_siteHeader('');
    $display .= COM_startBlock($LANG_LOGIN[1], '', COM_getBlockTemplate('_msg_block', 'header'));
    $login = new Template($_CONF['path_layout'] . 'submit');
    $login->set_file(array('login' => 'submitloginrequired.thtml'));
    $login->set_var('xhtml', XHTML);
    $login->set_var('login_message', $LANG_LOGIN[2]);
    $login->set_var('site_url', $_CONF['site_url']);
    $login->set_var('lang_login', $LANG_LOGIN[3]);
    $login->set_var('lang_newuser', $LANG_LOGIN[4]);
    $login->parse('output', 'login');
    $display .= $login->finish($login->get_var('output'));
    $display .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    echo $display;
    exit;
}

/**
* Shows all {plugin} data
*
*/
function {plugin}list ()
{
    global $_CONF, $_TABLES, $LANG_{lang_var_postfix};

    $retval = '';

    require_once($_CONF['path_system'] . 'lib-admin.php');
    $header_arr = array( // display 'text' and use table field 'field'
        array(
            'text'  => $LANG_{lang_var_postfix}['stats_title'],
            'field' => 'up_title',
            'sort'  => true
        ),
        array(
            'text'  => $LANG_{lang_var_postfix}['stats_value'],
            'field' => 'up_value',
            'sort'  => true
        )
    );

    $defsort_arr = array(
        'field'     => 'up_title',
        'direction' => 'desc'
    );

    $text_arr = array(
        'has_menu'     => false,
        'instructions' => '',
        'icon'         => '',
        'form_url'     => ''
    );

    $query_arr = array(
        'table'          => '{plugin}',
        'sql'            => "SELECT * FROM {$_TABLES['{plugin}']} WHERE 1=1",
        'query_fields'   => array('up_title'),
        'default_filter' => COM_getPermSql('AND')
    );

    $retval .= ADMIN_list('{plugin}', 'plugin_getListField_{plugin}',
                         $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

// MAIN

$display = COM_siteHeader();

$T = new Template($_CONF['path'] . 'plugins/{plugin}/templates');
$T->set_file('page', 'index.thtml');
$T->set_var('header', $LANG_{lang_var_postfix}['plugin']);
$T->set_var('site_url', $_CONF['site_url']);
$T->set_var('icon_url', $_CONF['site_url'] . '/{plugin}/images/{plugin}.gif');
$T->set_var('plugin', '{plugin}');
$T->set_var('xhtml', XHTML);
$T->set_var('blockheader', COM_startBlock($LANG_{lang_var_postfix}['display_name']));
$T->set_var('blockfooter',COM_endBlock());

// your code goes here

$T->set_var('infotext','Sample Public Page of Universal Plugin for Geeklog 1.5 .');
$T->set_var('listdata',{plugin}list());

$T->parse('output', 'page');
$display .= $T->finish($T->get_var('output'));

$display .= COM_siteFooter();

echo $display;

?>