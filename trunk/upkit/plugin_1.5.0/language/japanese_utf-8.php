<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | {display_name} Plugin 1.0.0 for Geeklog - The Ultimate Weblog             |
// +---------------------------------------------------------------------------|
// | plugins/{plugin}/language/japanese_utf-8.php                              |
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

/**
* the {plugin} plugin's lang array
*
*/
$LANG_{lang_var_postfix} = array(
    'display_name'      => '{display_name}',
    'menu_title'        => '{display_name}',
    'plugin'            => '{display_name}プラグイン',
    'access_denied'     => 'アクセスは拒否されました。',
    'access_denied_msg' => 'このページにアクセスできるのは、Rootユーザだけです。あなたのユーザ名とIPアドレスは記録されました。',
    'admin'             => '{display_name}プラグイン管理',
    'install_header'    => '{display_name}プラグインのインストール/アンインストール',
    'installed'         => '{display_name}プラグインはインストールされています。',
    'uninstalled'       => '{display_name}プラグインはインストールされていません。',
    'install_success'   => '{display_name}プラグインのインストールに成功しました。',
    'install_failed'    => '{display_name}プラグインのインストールに失敗しました。詳細はエラーログ(error.log)をご覧ください。',
    'uninstall_msg'     => '{display_name}プラグインはアンインストールされました。',
    'install'           => 'インストール',
    'uninstall'         => 'アンインストール',
    'warning'           => '警告！　{display_name}プラグインは有効なままです。',
    'enabled'           => 'アンインストールする前に、{display_name}プラグインを無効にしてください。',
    'readme'            => 'ちょっと待って！　「インストール」をクリックする前に、お読みください：',
    'installdoc'        => 'インストール手順書',

    // for stats
    'stats_headline'    => '{display_name}(上位10件)',
    'stats_title'       => '項目名',
    'stats_value'       => '値',
    'stats_no_value'    => 'データがありません。',

    // for admin
    '{plugin}editor'  => '{display_name}の編集',
    'manager'           => '{display_name}管理',
    'instructions'      => 'データを修正、削除する場合は各データの「編集」アイコンをクリックしてください。新規作成は「新規作成」をクリックしてください。',
    'missing_fields'    => '入力値に誤りがあります。',
);

// Messages for COM_showMessage the submission form

$PLG_{plugin}_MESSAGE2 = '{display_name}のデータは保存されました。';
$PLG_{plugin}_MESSAGE3 = '{display_name}のデータは削除されました。';

// Messages for the plugin upgrade
$PLG_{plugin}_MESSAGE3001 = 'プラグインのアップグレードはサポートされていません。';
$PLG_{plugin}_MESSAGE3002 = $LANG32[9];


// Localization of the Admin Configuration UI
$LANG_configsections['{plugin}'] = array(
    'label' => '{display_name}',
    'title' => '{display_name}の設定'
);

$LANG_confignames['{plugin}'] = array(
    '{plugin}loginrequired' => 'ログインを要求する',
    '{plugin}ubmission' => '{display_name}の投稿を管理者が承認する',
    'new{plugin}interval' => '新規{display_name}と見なす期間',
    'hidenew{plugin}' => '新着情報ブロックに表示しない',
    'hide{plugin}menu' => 'メニューに表示しない',
    '{plugin}perpage' => 'ページあたりの{display_name}数',
    'show_top10' => '{display_name}のトップ10を表示する',
    'delete_{plugin}' => '所有者の削除と共に削除する',
    'aftersave' => '{display_name}保存後の画面遷移',
    'default_permissions' => 'パーミッション'
);

$LANG_configsubgroups['{plugin}'] = array(
    'sg_main' => 'メイン'
);

$LANG_fs['{plugin}'] = array(
    'fs_main' => '{display_name}のメイン設定',
    'fs_public' => '{display_name}の表示',
    'fs_permissions' => '{display_name}のデフォルトパーミッション（[0]所有者 [1]グループ [2]メンバー [3]ゲスト）'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['{plugin}'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    9 => array('{display_name}のアイテムを表示する' => 'item', '{display_name}管理を表示する' => 'list', '公開{display_name}リストを表示する' => 'plugin', 'Homeを表示する' => 'home', '管理画面TOPを表示する' => 'admin'),
    12 => array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3)
);

?>