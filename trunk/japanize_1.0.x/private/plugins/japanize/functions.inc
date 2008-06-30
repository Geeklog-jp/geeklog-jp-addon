<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | 日本語化支援プラグイン                                                    |
// +---------------------------------------------------------------------------+
// $Id: plugins/japanize/functions.inc
//2008/06/29 16:56 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/



if (strpos ($_SERVER['PHP_SELF'], 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

$pi_name         = 'japanize';


//言語ファイル
$langfile = $_CONF['path'] . "plugins/{$pi_name}/language/"
          . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    require_once ($langfile);
} else {
    require_once ($_CONF['path'] . "plugins/{$pi_name}/english.php");
}

//設定ファイル
//現在設定項目がないのでコメントにしています
//require_once ($_CONF['path'] . "plugins/{$pi_name}/config.php");
//if (!isset($_JPN_CONF['xxxxxxxxxx'])) {
//    require_once $_CONF['path_system'] . 'classes/config.class.php';

//    $ca_config = config::get_instance();
//    $_CA_CONF = $ca_config->get_config($pi_name);
//}

$_JPN_CONF['version'] = '1.0.0test20080630';


// アドバンストエディタ用．管理ユーザーが使ってもよいHTMLタグと属性．
//user_htmlにマージされます．
if ($_CONF['advanced_editor']) {
    $_CONF['admin_html']['a'] = array('href' => 1, 'title' => 1, 'id' => 1, 'lang' => 1, 'name' => 1, 'type' => 1, 'rel' => 1);
    $_CONF['admin_html']['br'] = array('clear' => 1,'style' => 1);
    $_CONF['admin_html']['caption'] = array('style' => 1);
    $_CONF['admin_html']['div'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['embed'] = array('src' => 1, 'loop' => 1, 'quality' => 1, 'width' => 1, 'height' => 1, 'type' => 1, 'pluginspage' => 1, 'align' => 1);
    $_CONF['admin_html']['h1'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['h2'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['h3'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['h4'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['h5'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['h6'] = array('class' => 1, 'id' => 1, 'style' => 1);
    $_CONF['admin_html']['hr'] = array('class' => 1, 'id' => 1, 'align' => 1);
    $_CONF['admin_html']['img'] = array('src' => 1, 'width' => 1, 'height' => 1, 'vspace' => 1, 'hspace' => 1, 'dir' => 1, 'align' => 1, 'valign' => 1, 'border' => 1, 'lang' => 1, 'longdesc' => 1, 'title' => 1, 'id' => 1, 'alt' => 1, 'style' => 1);
    $_CONF['admin_html']['noscript'] = array();
    $_CONF['admin_html']['object'] = array('type' => 1,'data' => 1,'classid' => 1, 'codebase' => 1, 'width' => 1, 'height' => 1, 'align' => 1);
    $_CONF['admin_html']['ol'] = array('class' => 1, 'style' => 1);
    $_CONF['admin_html']['p'] = array('class' => 1, 'id' => 1, 'align' => 1, 'lang' => 1);
    $_CONF['admin_html']['param'] = array('name' => 1, 'value' => 1);
    $_CONF['admin_html']['script'] = array('src' => 1, 'language' => 1, 'type' => 1);
    $_CONF['admin_html']['span' ] = array('class' => 1, 'id' => 1, 'lang' => 1);
    $_CONF['admin_html']['table'] = array('class' => 1, 'id' => 1, 'width' => 1, 'border' => 1, 'cellspacing' => 1, 'cellpadding' => 1);
    $_CONF['admin_html']['tbody'] = array();
    $_CONF['admin_html']['td'] = array('class' => 1, 'id' => 1, 'align' => 1, 'valign' => 1, 'colspan' => 1, 'rowspan' => 1);
    $_CONF['admin_html']['th'] = array('class' => 1, 'id' => 1, 'align' => 1, 'valign' => 1, 'colspan' => 1, 'rowspan' => 1);
    $_CONF['admin_html']['tr'] = array('class' => 1, 'id' => 1, 'align' => 1, 'valign' => 1);
    $_CONF['admin_html']['ul'] = array('class' => 1, 'style' => 1);
}



// +-----------+
// | 専用関数  |
// +-----------+


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

// +---------------------------------------------------------------------------+
// | 自動タグ                                                                  |
// | 書式 plugin_autotags_japanize ($op, $content, $autotag)                   |
// +---------------------------------------------------------------------------+
//現在当該機能はないのでコメントにしています
//function plugin_autotags_japanize ($op, $content = '', $autotag = '')

// +---------------------------------------------------------------------------+
// | 現在インストールされているバージョン取得                                  |
// | 書式 plugin_chkVersion_japanize                                           |
// +---------------------------------------------------------------------------+
function plugin_chkVersion_japanize ()
{
    global $_JPN_CONF;

    return $_JPN_CONF['version'];
}

// +---------------------------------------------------------------------------+
// | 一覧取得                                                                  |
// | 書式 plugin_getListField_japanize                                         |
// +---------------------------------------------------------------------------+
//現在当該機能はないのでコメントにしています
//function plugin_getListField_japanize($fieldname, $fieldvalue, $A, $icon_arr)

// +---------------------------------------------------------------------------+
// | 機能  プラグイン更新:バージョンナンバーの更新                             |
// | 書式 plugin_upgrade_japanize                                              |
// +---------------------------------------------------------------------------+
function plugin_upgrade_japanize ()
{
    global $_TABLES, $_JPN_CONF;

    // the plugin needs these functions so complain when they doesn't exist
    if (!function_exists ('COM_truncate') ||
        !function_exists ('MBYTE_strpos')) {
        return 3002;
    }

    // no db changes - just update the version numbers
    DB_query ("UPDATE {$_TABLES['plugins']} SET pi_version = '{$_JPN_CONF['version']}', pi_gl_version = '" . VERSION . "' WHERE pi_name = 'japanize'");

    return true;
}

// +---------------------------------------------------------------------------+
// | 機能  プラグインアンインストール    1.5.0用                               |
// | 書式 plugin_getadminoption_japanize                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function plugin_autouninstall_japanize ()
{
    $out = array (
        //(1) give the name of the tables, without $_TABLES[] */
        'tables' => array('events','eventsubmission','personal_events'),
        //(2) give the full name of the group, as in the db */
        'groups' => array('Calendar Admin'),
        //(3)* give the full name of the feature, as in the db */
        'features' => array('calendar.edit', 'calendar.moderate', 'calendar.submit'),
        //(4)* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_calendar'),
        //(5) give all vars with their name */
        'vars'=> array()
    );

    // (1) 削除するテーブル
    $out['tables']=array();
    // (2) 削除するグループ名 
    $out['groups'][]='japanize Admin';
    // (3) 削除するfeatures 
    $out['features'][]='japanize.edit';
    $out['features'][]='japanize.moderate';
    $out['features'][]='japanize.submit';
    // (4) 削除するphp_blocks 
    $out['php_blocks']=array();
    // (5) 削除する vars */
    $out['vars']=array();
    
    return $out;
}

/**
* ☆★現在のユーザは権利があるのをチェック
*
* @return        boolean       Returns true if moderator
*
*/
function plugin_ismoderator_japanize()
{
    //return SEC_hasRights ('japanize.moderate');
    return true;
}





// +---------------------------------------------------------------------------+
// | 機能  プラグインのアイコン取得                                            |
// | 書式 plugin_geticon_japanize                                              |
// +---------------------------------------------------------------------------+

function plugin_geticon_japanize ()
{
    global $_CONF;

    return $_CONF['site_url'] . '/images/icons/plugins.gif';
}

// +---------------------------------------------------------------------------+
// | 機能  管理画面メニュ                                                      |
// | 書式 plugin_cclabel_japanize                                              |
// +---------------------------------------------------------------------------+
function plugin_cclabel_japanize()
{
    global $_CONF;

    if (SEC_hasRights('japanize.edit')) {
        return array(
            'japanize'
            ,$_CONF['site_admin_url'] . "/plugins/japanize/index.php"
            ,plugin_geticon_japanize ()
            );
    }

    return "";

}

// +---------------------------------------------------------------------------+
// | 機能  管理者メニュ表示 　                                                 |
// | 書式 plugin_getadminoption_japanize                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:管理者メニュリンク                                             |
// +---------------------------------------------------------------------------+
function plugin_getadminoption_japanize()
{
    global $_CONF, $_TABLES;
    global $LANG_JPN;

    if (SEC_hasRights ('japanize.edit') ){
        return array (
                      $LANG_JPN['piname']
                      ,$_CONF['site_admin_url'] . '/plugins/japanize/index.php'
                      );
    }
    return "";

}

// +---------------------------------------------------------------------------+
// | 機能  テンプレート変数設定                                                |
// | 書式 plugin_templatesetvars_japanize                                      |
// +---------------------------------------------------------------------------+
//  2006/10/16 Geeklog Japanese ivy added 
//  2007/05/14 Geeklog Japanese kino changed 
//  2007.8.26 Ivy added
//  2007/12/22 Geeklog Japanese Ivy changed 
//  2008/06/11 Geeklog Japanese Tsuchi modify custom_templatesetvars
function plugin_templatesetvars_japanize($templatename, &$template) {
    global  $LANG01, $LANG_JPN;
    global  $_USER,$_CONF;
    global  $topic;
    global  $page;

    switch ($templatename) {
        case 'header':

            //Frontpageの場合は 'sub' そうでなければ  'home' 
            if( COM_isFrontpage() ){
                $home_id='sub';
            } else {
                $home_id='home';
            }

            //ログインしている時
            if (isset($_USER['uid']) && $_USER['uid'] > 1) {
                $login_status='member';
                $prof_url = "{$_CONF['site_url']}/users.php?mode=profile&amp;uid={$_USER['uid']}";
                $welcome_msg_jp = "{$LANG01[67]}";
                $welcome_msg_jp .= ' ' . COM_getDisplayName()." ".$LANG_JPN['keisyo'];
                $memberlogin = "<a href='{$_CONF['site_url']}/users.php?mode=logout' >{$LANG01[19]}</a> ";
            //ログインしていない時
            } else {
                $login_status='guest';
                $prof_url = "{$_CONF['site_url']}/";
                $welcome_msg_jp = "";

                if (file_exists($_CONF['path_layout'] . 'custom-memberlogin.thtml')) {
                    $template->set_file(array('member_login' => 'custom-memberlogin.thtml'));
                    $template->parse( 'memberlogin', 'member_login' );
                } else {
                    $msg = "<a href='{$_CONF['site_url']}/users.php?mode=login'>{$LANG01[58]}</a> ";
                    $template->set_var( 'memberlogin', $msg);
                }

            }

            //記事ID:topic_id
            $template->set_var( 'topic_id', $topic );
            //静的ページID:sp_id
            $template->set_var( 'sp_id', $page );
            //home_id ('home','sub')
            $template->set_var( 'home_id', $home_id );
            //ログイン状態:login_status ('member','guest')
            $template->set_var( 'login_status', $login_status );
            //ようこそメッセージ:welcome_msg_jp
            $template->set_var( 'welcome_msg_jp', $welcome_msg_jp );
            //プロフィールのURL:prof_url_jp
            $template->set_var( 'prof_url_jp', $prof_url );
            //メンバーログイン:memberlogin　(ログインHTML,ログアウトHTML)
            $template->set_var( 'memberlogin', $memberlogin);

    }
}

?>
