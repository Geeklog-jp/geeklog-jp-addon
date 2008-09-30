<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | public_html/admin/plugins/{plugin}/index.php                              |
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

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

$display = '';

if (!SEC_hasRights('{plugin}.edit')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30]);
    $display .= COM_startBlock($MESSAGE[30], '', COM_getBlockTemplate('_msg_block', 'header'));
    $display .= '';
    $display .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    COM_accessLog("User {$_USER['username']} tried to illegally access the {plugin} administration screen.");
    echo $display;
    exit;
}

/**
* Shows the {plugin} editor
*
*/
function edit{plugin}($up_id = '')
{
    global $_CONF, $_GROUPS, $_TABLES, $_USER, $_{conf_var_prefix}_CONF,
           $LANG_{lang_var_postfix}, $LANG_ACCESS, $LANG_ADMIN, $MESSAGE;

    $retval = '';

    $T = new Template($_CONF['path'] . 'plugins/{plugin}/templates/admin/');
    $T->set_file('editor', 'editor.thtml');
    $T->set_var('xhtml', XHTML);
    $T->set_var('site_url', $_CONF['site_url']);
    $T->set_var('icon_url', plugin_geticon_{plugin}());
    $T->set_var('site_admin_url', $_CONF['site_admin_url']);
    $T->set_var('layout_url', $_CONF['layout_url']);

    if (!empty($up_id)) {

        $result = DB_query("SELECT * FROM {$_TABLES['{plugin}']} WHERE up_id = '$up_id'");

        if (DB_numRows($result) !== 1) {
            $msg = COM_startBlock($LANG_{lang_var_postfix}['stats_no_value'], '',
                COM_getBlockTemplate('_msg_block', 'header'));
            $msg .= $LANG_{lang_var_postfix}['stats_no_value'];
            $msg .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
            return $msg;
        }

        $A = DB_fetchArray($result);

        $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
            $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']);
        if ($access == 0 OR $access == 2) {
            $retval .= COM_startBlock($LANG_{lang_var_postfix}['access_denied'], '',
                               COM_getBlockTemplate('_msg_block', 'header'));
            $retval .= $LANG_{lang_var_postfix}['access_denied_msg'];
            $retval .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
            COM_accessLog("User {$_USER['username']} tried to illegally submit or edit {plugin} $up_id.");
            return $retval;
        }

    } else {

        $A['up_id'] = '';
        $A['up_title'] = '';
        $A['up_value'] = '';
        $A['owner_id'] = $_USER['uid'];
        if (isset($_GROUPS['{display_name} Admin'])) {
            $A['group_id'] = $_GROUPS['{display_name} Admin'];
        } else {
            $A['group_id'] = SEC_getFeatureGroup('{plugin}.edit');
        }
        SEC_setDefaultPermissions($A, $_{conf_var_prefix}_CONF['default_permissions']);
        $access = 3;
    }
    $retval .= COM_startBlock($LANG_{lang_var_postfix}['{plugin}editor'], '',
                              COM_getBlockTemplate ('_admin_block', 'header'));

    $T->set_var('up_id', $A['up_id']);
    if (!empty($up_id) && SEC_hasRights('{plugin}.edit')) {
        $delbutton = '<input type="submit" value="' . $LANG_ADMIN['delete']
                   . '" name="mode"%s' . XHTML . '>';
        $jsconfirm = ' onclick="return confirm(\'' . $MESSAGE[76] . '\');"';
        $T->set_var('delete_option', sprintf($delbutton, $jsconfirm));
        $T->set_var('delete_option_no_confirmation', sprintf($delbutton, ''));
    }

    $T->set_var('lang_title', $LANG_{lang_var_postfix}['stats_title']);
    $T->set_var('lang_value', $LANG_{lang_var_postfix}['stats_value']);
    $T->set_var('val_title', htmlspecialchars(stripslashes ($A['up_title'])));
    $T->set_var('val_value', $A['up_value']);
    $T->set_var('lang_save', $LANG_ADMIN['save']);
    $T->set_var('lang_cancel', $LANG_ADMIN['cancel']);

    // user access info
    $T->set_var('lang_accessrights', $LANG_ACCESS['accessrights']);
    $T->set_var('lang_owner', $LANG_ACCESS['owner']);
    $ownername = COM_getDisplayName ($A['owner_id']);
    $T->set_var('owner_username', DB_getItem($_TABLES['users'], 'username', "uid = {$A['owner_id']}"));
    $T->set_var('owner_name', $ownername);
    $T->set_var('owner', $ownername);
    $T->set_var('{plugin}_ownerid', $A['owner_id']);
    $T->set_var('lang_group', $LANG_ACCESS['group']);
    $T->set_var('group_dropdown', SEC_getGroupDropdown($A['group_id'], $access));
    $T->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $T->set_var('lang_permissionskey', $LANG_ACCESS['permissionskey']);
    $T->set_var('permissions_editor',
        SEC_getPermissionsHTML($A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']));
    $T->set_var('lang_lockmsg', $LANG_ACCESS['permmsg']);
    $T->set_var('gltoken_name', CSRF_TOKEN);
    $T->set_var('gltoken', SEC_createToken());

    $T->parse('output', 'editor');
    $retval .= $T->finish($T->get_var('output'));

    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}


/**
* Save a data to the database
*
*/
function save{plugin}($up_id, $title, $value, $owner_id, $group_id,
                        $perm_owner, $perm_group, $perm_members, $perm_anon)
{
    global $_CONF, $_GROUPS, $_TABLES, $_USER, $LANG_{lang_var_postfix}, $_{conf_var_prefix}_CONF;

    $retval = '';

    // Convert array values to numeric permission values
    list($perm_owner, $perm_group, $perm_members, $perm_anon)
        = SEC_getPermissionValues($perm_owner, $perm_group, $perm_members, $perm_anon);

    $title = addslashes(COM_checkHTML(COM_checkWords($title)));
    $value = addslashes($value);

    if (empty($owner_id)) {
        // this is new link from admin, set default values
        $owner_id = $_USER['uid'];
        if (isset($_GROUPS['{display_name} Admin'])) {
            $group_id = $_GROUPS['{display_name} Admin'];
        } else {
            $group_id = SEC_getFeatureGroup ('{plugin}.edit');
        }
        $perm_owner   = 3;
        $perm_group   = 2;
        $perm_members = 2;
        $perm_anon    = 2;
    }

    $access = 0;
    $up_id = addslashes($up_id);
    if (DB_count($_TABLES['{plugin}'], 'up_id', $up_id) > 0) {
        $result = DB_query ("SELECT owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon "
                           ."FROM {$_TABLES['{plugin}']} WHERE up_id = '$up_id'");
        $A = DB_fetchArray($result);
        $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
                $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']);
    } else {
        $access = SEC_hasAccess($owner_id, $group_id,
                $perm_owner, $perm_group, $perm_members, $perm_anon);
    }

    if (($access < 3) || !SEC_inGroup($group_id)) {
        $display .= COM_siteHeader('menu', $MESSAGE[30]);
        $display .= COM_startBlock($LANG_{lang_var_postfix}['access_denied'], '',
                        COM_getBlockTemplate('_msg_block', 'header'));
        $display .= $LANG_{lang_var_postfix}['access_denied_msg'];
        $display .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
        $display .= COM_siteFooter();
        COM_accessLog("User {$_USER['username']} tried to illegally submit or edit {plugin} $up_id.");
        echo $display;
        exit;
    }

    if (!empty($title) && !empty($value)) {

        if (DB_count($_TABLES['{plugin}'], 'up_id', $up_id) > 0) {
            $sql = "UPDATE {$_TABLES['{plugin}']} "
                 . "SET up_title='$title', up_value=$value, owner_id=$owner_id, group_id=$group_id, "
                 . "perm_owner=$perm_owner, perm_group=$perm_group, perm_members=$perm_members, perm_anon=$perm_anon "
                 . "WHERE up_id = $up_id";
        } else {
            $sql = "INSERT INTO {$_TABLES['{plugin}']} "
                 . '(up_title, up_value, owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon) '
                 . "VALUES ('$title', $value, $owner_id , $group_id, $perm_owner, $perm_group, $perm_members, $perm_anon)";
        }
        DB_query($sql);

        return PLG_afterSaveSwitch(
            $_{conf_var_prefix}_CONF['aftersave'],
            $_CONF['site_url'] . '/{plugin}/index.php?up_id=' . $up_id,
            '{plugin}', 2);

    } else { // missing fields

        $retval .= COM_siteHeader('menu', $LANG_LINKS_ADMIN['manager']);
        $retval .= COM_errorLog($LANG_{lang_var_postfix}['missing_fields'], 2);
        if (DB_count($_TABLES['{plugin}'], 'up_id', $up_id) > 0) {
            $retval .= edit{plugin}($up_id);
        } else {
            $retval .= edit{plugin}();
        }
        $retval .= COM_siteFooter();

        return $retval;
    }
}

/**
* Delete a data from the database
*
*/
function delete{plugin}($up_id)
{
    global $_CONF, $_TABLES, $_USER;

    $result = DB_query("SELECT owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon "
                      ."FROM {$_TABLES['{plugin}']} WHERE up_id = '$up_id'");
    $A = DB_fetchArray($result);
    $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
            $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']);
    if ($access < 3) {
        COM_accessLog("User {$_USER['username']} tried to illegally delete {plugin} $up_id.");

        return COM_refresh($_CONF['site_admin_url'] . '/plugins/{plugin}/index.php');
    }

    DB_delete($_TABLES['{plugin}'], 'up_id', $up_id);

    return COM_refresh($_CONF['site_admin_url'] . '/plugins/{plugin}/index.php?msg=3');
}


function list{plugin}()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_{lang_var_postfix};

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array( // display 'text' and use table field 'field'
        array( 'text'  => $LANG_ADMIN['edit'],
               'field' => 'edit',
               'sort'  => false
        ),
        array( 'text'  => $LANG_{lang_var_postfix}['stats_title'],
               'field' => 'up_title',
               'sort'  => true
        ),
        array( 'text'  => $LANG_{lang_var_postfix}['stats_value'],
               'field' => 'up_value',
               'sort'  => true
        )
    );

    $defsort_arr = array(
        'field'     => 'up_title',
        'direction' => 'desc'
    );

    $menu_arr = array(
        array( 'url'  => $_CONF['site_admin_url'] . '/plugins/{plugin}/index.php?mode=edit',
               'text' => $LANG_ADMIN['create_new']
        ),
        array( 'url'  => $_CONF['site_admin_url'],
               'text' => $LANG_ADMIN['admin_home']
        )
    );

    $retval .= COM_startBlock($LANG_{lang_var_postfix}['manager'], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu($menu_arr, $LANG_{lang_var_postfix}['instructions'], plugin_geticon_{plugin}());

    $text_arr = array(
        'has_extras'   => true,
        'form_url'     => $_CONF['site_admin_url'] . "/plugins/{plugin}/index.php"
    );

    $query_arr = array(
        'table'          => '{plugin}',
        'sql'            => "SELECT * FROM {$_TABLES['{plugin}']} WHERE 1=1",
        'query_fields'   => array('up_title'),
        'default_filter' => COM_getPermSql ('AND')
    );

    $retval .= ADMIN_list('{plugin}', 'plugin_getListField_{plugin}',
                         $header_arr, $text_arr, $query_arr, $defsort_arr);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

// MAIN

$mode = (!empty($_REQUEST['mode'])) ? COM_applyFilter($_REQUEST['mode']) : '';

$up_id = (!empty($_REQUEST['up_id'])) ? COM_applyFilter($_REQUEST['up_id'], true) : '';

if (($mode == $LANG_ADMIN['save']) && !empty($LANG_ADMIN['save']) && SEC_checkToken()) { // save mode

    $display .= save{plugin}(
            $up_id,
            COM_applyFilter($_POST['title']),
            COM_applyFilter($_POST['value']),
            COM_applyFilter($_POST['owner_id'], true),
            COM_applyFilter($_POST['group_id'], true),
            $_POST['perm_owner'], $_POST['perm_group'],
            $_POST['perm_members'], $_POST['perm_anon']);

} else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete']) && SEC_checkToken()) { // delete mode

    $display .= delete{plugin}($up_id);

} else if ($mode == 'edit') { // edit mode

    $display .= COM_siteHeader('menu', $LANG_{lang_var_postfix}['{plugin}editor']);
    $display .= edit{plugin}($up_id);
    $display .= COM_siteFooter();

} else { // 'cancel' or no mode at all

    $display .= COM_siteHeader('menu', $LANG_{lang_var_postfix}['manager']);
    if (isset ($_REQUEST['msg'])) {
        $msg = COM_applyFilter($_REQUEST['msg'], true);
        if ($msg > 0) {
            $display .= COM_showMessage($msg, '{plugin}');
        }
    }
    $display .= list{plugin}();
    $display .= COM_siteFooter();
}

echo $display;

?>