<?php
require_once('../../../lib-common.php');
require_once ($_CONF['path_system'] . 'lib-portalparts.php');


// 権限チェック
if (!SEC_hasRights('japanize.edit')) {
    $display="";
    $display .= COM_siteHeader('menu', $MESSAGE[30]);
    $display .= COM_startBlock ($MESSAGE[30], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $display .= $MESSAGE[35];
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the japanize administration screen.");

    echo $display;

    exit;
}

$navbarMenu = array(
    $LANG_JPN_admin_menu['1']   => $_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN.'/index.php',
    $LANG_JPN_admin_menu['2']   => $_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN.'/settings.php',
    $LANG_JPN_admin_menu['3']   => $_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN.'/information.php'
);


?>