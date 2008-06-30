<?php
###############################################################################
# plugins/japanize/language/japanese_utf-8.php
# もし万一エンコードの種類が　UTF-8でない場合は、utf-8に変換してください。
# Last Update 2008/06/20 16:39


###############################################################################



###############################################################################
$LANG_JPN= array();
$LANG_JPN['piname']= '日本語化支援';
$LANG_JPN['pinameadmin']= '日本語化支援管理';
$LANG_JPN['keisyo']= ' さん';

###############################################################################
## 管理画面
$LANG_JPN_admin_menu = array();
$LANG_JPN_admin_menu['1']= '更新';
$LANG_JPN_admin_menu['2']= '設定';
$LANG_JPN_admin_menu['3']= '情報';



###############################################################################
# COM_showMessage()

// Messages for the plugin upgrade
$PLG_JAPANIZE_MESSAGE3002 = $LANG32[9];

$PLG_japanize_MESSAGE1 = "テーブル構造を日本語化しました。";
$PLG_japanize_MESSAGE2 = "コンフィギュレーションを日本語化しました。";
$PLG_japanize_MESSAGE3 = "日本語pingサイトを追加しました。";
$PLG_japanize_MESSAGE4 = "ゲストユーザに変更しました。";

$PLG_japanize_MESSAGE1001 = "更新しました。";


###############################################################################
## コンフィギュレーション
$LANG_configsections['japanize'] = array();
$LANG_configsections['japanize']['label']='japanize';
$LANG_configsections['japanize']['title']='japanizeの設定';

//
$LANG_confignames['japanize'] = array();


//
$LANG_configsubgroups['japanize'] = array();
$LANG_configsubgroups['japanize']['sg_main'] = 'メイン';
//
$LANG_fs['japanize'] = array();
$LANG_fs['japanize']['fs_main'] = '既定値の設定';

//
$LANG_configselects['japanize'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE)
);

?>