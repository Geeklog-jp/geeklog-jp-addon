<?php

/* Reminder: always indent with 4 spaces (no tabs). */

//

define ('THIS_SCRIPT', 'settings.php');
define ('THIS_PLUGIN', 'japanize');
//define ('THIS_SCRIPT', 'test.php');

include_once('japanize_functions.php');


// +---------------------------------------------------------------------------+
// | 機能  テーブル更新実行                                                    |
// | 書式 fncCmdExec ()                                                        |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
function fncCmdExec ()
{
    global $_TABLES,$_CONF;

    $Anonymous = COM_applyFilter($_POST['Anonymous']);

    $url=$_CONF['site_admin_url'] . "/plugins/".THIS_PLUGIN."/".THIS_SCRIPT;
    if ($Anonymous!==""){
        $sql ="UPDATE {$_TABLES['users']} ";
        $sql .=" SET `username` = '{$Anonymous}',";
        $sql .="     `fullname` = '{$Anonymous}' ";
        $sql .=" WHERE `uid` =1 ";
        $rt = DB_query($sql);
        $url.="?msg=1001";
    }
    echo COM_refresh($url);

}

// +---------------------------------------------------------------------------+
// | 機能  初期画面表示                                                        |
// | 書式 fncEdit ()                                                           |
// +---------------------------------------------------------------------------+

function fncEdit ()
{
    global $_CONF;
    global $_TABLES;
    global $LANG04,$LANG_ADMIN;

    $retval = '';
    $T = new Template($_CONF['path'] . 'plugins/japanize/templates/admin');
    $T->set_file ('admin','settings.thtml');

    $T->set_var ( 'xhtml', XHTML );

    $this_script=$_CONF['site_admin_url']."/plugins/".THIS_PLUGIN."/".THIS_SCRIPT;

    $T->set_var ( 'this_script', $this_script );

    $val=DB_getItem($_TABLES['users'],"username","uid=1");
    $T->set_var ('Anonymous_value', $val);

    $T->set_var ('lang_submit', $LANG04[9]);
    $T->set_var ('lang_cancel',$LANG_ADMIN['cancel']);



    $T->parse('output', 'admin');
    $retval .= $T->finish($T->get_var('output'));

    return $retval;

}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+

$display = '';
$display .= COM_siteHeader ('menu', $LANG_JPN['pinameadmin']);
if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), 'japanize');
}

$display.=ppNavbar($navbarMenu,$LANG_JPN_admin_menu['2']);

if($_POST['savesettings'] == 'yes'){
    fncCmdExec();
}else{// 初期表示、一覧表示
    $display .=fncEdit();
    $display .= COM_siteFooter ();
}


echo $display;

?>
