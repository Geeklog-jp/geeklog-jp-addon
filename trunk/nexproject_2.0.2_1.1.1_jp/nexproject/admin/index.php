<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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

if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    echo COM_siteHeader();
    echo COM_startBlock($LANG_PRJ['access_denied']);
    echo $LANG_PRJ['access_denied_msg'];
    echo COM_endBlock();
    echo COM_siteFooter();
    exit;
}

function main_display() {
    global $_CONF, $_TABLES, $LANG_PRJ_CONFIG, $LANG_PRJ_ADMIN;

    $retval = COM_startBlock($LANG_PRJ_ADMIN['nexproject_config']);
    $retval .= '<form method="post" action="'.$_CONF['site_admin_url'].'/plugins/nexproject/index.php"><table class="plugin"><tr><th>Option</th><th>Value</th>';

    $res = DB_query("SELECT config_param, config_value FROM {$_TABLES['prj_config']}");
    $i = 0;
    while (list ($name, $value) = DB_fetchArray($res)) {
        $display_name = ($LANG_PRJ_CONFIG[$name] != '') ? $LANG_PRJ_CONFIG[$name]:$name;
        $rowid = (($i++ % 2) + 1);
        $retval .= "<tr class=\"pluginRow$rowid\"><td>$display_name</td><td><input type=\"text\" name=\"$name\" value=\"$value\" size=\"4\"></td></tr>";
    }
    $rowid = (($i++ % 2) + 1);

    $retval .= '<tr class="pluginRow'.$rowid.'"><td colspan="2" style="text-align: center;"><input type="submit" value="Save Configuration"></td></tr>';
    $retval .= '</table><input type="hidden" name="op" value="save"></form>';
    $retval .= COM_endBlock();

    return $retval;
}

function save_values() {
    global $_CONF, $_TABLES, $LANG_PRJ_CONFIG, $LANG_PRJ_ADMIN;

    $res = DB_query("SELECT config_param FROM {$_TABLES['prj_config']}");
    $i = 0;
    while (list ($name) = DB_fetchArray($res)) {
        $value = intval ($_POST[$name]);
        DB_query("UPDATE {$_TABLES['prj_config']} SET config_value=$value WHERE config_param='$name'");
    }

    return;
}

$op = COM_applyFilter($_REQUEST['op']);
switch ($op) {
case 'save':
    save_values();
    echo COM_refresh($_CONF['site_admin_url'] . '/plugins/nexproject/index.php');
    break;

default:
    echo COM_siteHeader();
    echo main_display();
    echo COM_siteFooter();
    break;
}

?>
