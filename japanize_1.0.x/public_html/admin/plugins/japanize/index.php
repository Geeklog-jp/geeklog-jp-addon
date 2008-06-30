<?php

/* Reminder: always indent with 4 spaces (no tabs). */

//
// $Id: index.php,v 1.37 2008/05/24 07:45:28 dhaun Exp $

define ('THIS_SCRIPT', 'index.php');
define ('THIS_PLUGIN', 'japanize');
//define ('THIS_SCRIPT', 'test.php');

include_once('japanize_functions.php');


// +---------------------------------------------------------------------------+
// | 機能  テーブル更新実行                                                    |
// | 書式 fncCmdExec ($no)                                                     |
// +---------------------------------------------------------------------------+
// | 引数 $no:                                                                 |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
function fncCmdExec ($no)
{
    global $_TABLES,$_CONF;
    $_SQL =array();
    require_once ("sql/sql_japanize_{$no}.php");

    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    $url=$_CONF['site_admin_url'] . "/plugins/".THIS_PLUGIN."/".THIS_SCRIPT;
    $url.="?msg={$no}";

    echo COM_refresh($url);

}

// +---------------------------------------------------------------------------+
// | 機能  初期画面表示                                                        |
// | 書式 fncEdit ()                                                           |
// +---------------------------------------------------------------------------+

function fncEdit ()
{
    global $_CONF;
    //global $_TABLES;
    //global $_USER;
    global $LANG04,$LANG_ADMIN;

    $retval = '';
    $T = new Template($_CONF['path'] . 'plugins/japanize/templates/admin');
    $T->set_file ('admin','index.thtml');

    //$T->set_var('site_url', $_MG_CONF['site_url']);
    //$T->set_var('site_admin_url', $_CONF['site_admin_url']);

    $T->set_var ( 'xhtml', XHTML );

    $this_script=$_CONF['site_admin_url']."/plugins/".THIS_PLUGIN."/".THIS_SCRIPT;
    $T->set_var ( 'this_script', $this_script );

    $T->set_var ('lang_submit', $LANG04[9]);
    $T->set_var ('lang_cancel',$LANG_ADMIN['cancel']);


    $T->parse('output', 'admin');
    $retval .= $T->finish($T->get_var('output'));

    return $retval;

}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
$mode="";
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}


$display = '';
$display .= COM_siteHeader ('menu', $LANG_JPN['pinameadmin']);
if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), 'japanize');
}

$display.=ppNavbar($navbarMenu,$LANG_JPN_admin_menu['1']);

if (substr($mode,0,3)=="cmd") {
    $no=substr($mode,-1);
    fncCmdExec($no);
}else{// 初期表示、一覧表示
    $display .=fncEdit();
    $display .= COM_siteFooter ();
}


echo $display;

?>
