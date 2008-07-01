<?php
// +---------------------------------------------------------------------------+
// | コンフィギュレーションを日本語版化する                                    |
// |                                                                           |
// +---------------------------------------------------------------------------+
// $Id: sql_japanize2.php
// もし万一エンコードの種類が  utf-8でない場合は、utf-8に変換してください。
// 最終更新日　2007/06/24 tsuchi AT geeklog DOT jp


//【サイト】
//サイト･･･無効のメッセージまたはURL
$wk="\"{$_CONF['site_url']}/disabledmsg.html\";";
$wklen=strlen($wk)-3;
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:{$wklen}:{$wk}'
    WHERE name = 'site_disabled_msg'
    ";
//シンジケーション･･･フィードの言語
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:2:\"ja\";'
    WHERE name = 'rdf_language'
    ";
//【ブロック】
//管理者ブロック･･･リンクをソートする いいえ　0:表示しない
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'b:0;'
    WHERE name = 'sort_admin'
    ";
//話題ブロック･･･記事投稿数を表示する いいえ　0:表示しない
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'b:0;'
    WHERE name = 'showsubmissioncount'
    ";
//話題ブロック･･･Homeへのリンクを隠す はい　1:隠す
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'b:1;'
    WHERE name = 'hide_home_link'
    ";
//【ユーザと投稿】
//コメント･･･コメント形状 flat:一覧
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:4:\"flat\";'
    WHERE name = 'comment_mode'
    ";
//【画像】
//画像ライブラリ･･･画像ライブラリ:GDライブラリ
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:5:\"gdlib\";'
    WHERE name = 'image_lib'
    ";
//画像ライブラリ･･･記事の画像高さの最大値:120ピクセル
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'i:120;'
    WHERE name = 'max_image_height'
    ";
//【言語とロケール】
//ロケール･･･ロケール
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:11:\"ja_JP.UTF-8\";'
    WHERE name = 'locale'
    ";
//ロケール･･･日付
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:25:\"%Y年%B%e日(%a) %H:%M %Z\";'
    WHERE name = 'date'
    ";
//ロケール･･･日時
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:14:\"%m/%d %H:%M %Z\";'
    WHERE name = 'daytime'
    ";

//ロケール･･･日付短表記
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:12:\"%Y年%B%e日\";'
    WHERE name = 'shortdate'
    ";
//ロケール･･･日付けのみ
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:7:\"%B%e日\";'
    WHERE name = 'dateonly'
    ";
//ロケール･･･時間のみ
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 's:8:\"%H:%M %Z\";'
    WHERE name = 'timeonly'
    ";

//【その他】
//HTMLフィルタ･･･ユーザHTML 
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'a:18:{s:1:\"a\";a:3:{s:4:\"href\";i:1;s:5:\"title\";i:1;s:3:\"rel\";i:1;}s:1:\"b\";a:0:{}s:10:\"blockquote\";a:0:{}s:2:\"br\";a:1:{s:5:\"clear\";i:1;}s:4:\"code\";a:0:{}s:3:\"div\";a:1:{s:5:\"class\";i:1;}s:4:\"font\";a:1:{s:5:\"color\";i:1;}s:2:\"em\";a:0:{}s:1:\"h\";a:0:{}s:2:\"hr\";a:0:{}s:1:\"i\";a:0:{}s:2:\"li\";a:0:{}s:2:\"ol\";a:0:{}s:1:\"p\";a:1:{s:4:\"lang\";i:1;}s:3:\"pre\";a:0:{}s:6:\"strong\";a:0:{}s:2:\"tt\";a:0:{}s:2:\"ul\";a:0:{}}'
    WHERE name = 'user_html'
    ";
//HTMLフィルタ･･･管理者HTML
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'a:26:{s:1:\"a\";a:7:{s:4:\"href\";i:1;s:5:\"title\";i:1;s:2:\"id\";i:1;s:4:\"lang\";i:1;s:4:\"name\";i:1;s:4:\"type\";i:1;s:3:\"rel\";i:1;}s:2:\"br\";a:2:{s:5:\"clear\";i:1;s:5:\"style\";i:1;}s:7:\"caption\";a:1:{s:5:\"style\";i:1;}s:3:\"div\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:5:\"embed\";a:8:{s:3:\"src\";i:1;s:4:\"loop\";i:1;s:7:\"quality\";i:1;s:5:\"width\";i:1;s:6:\"height\";i:1;s:4:\"type\";i:1;s:11:\"pluginspage\";i:1;s:5:\"align\";i:1;}s:2:\"h1\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"h2\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"h3\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"h4\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"h5\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"h6\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"style\";i:1;}s:2:\"hr\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"align\";i:1;}s:3:\"img\";a:15:{s:3:\"src\";i:1;s:5:\"width\";i:1;s:6:\"height\";i:1;s:6:\"vspace\";i:1;s:6:\"hspace\";i:1;s:3:\"dir\";i:1;s:5:\"align\";i:1;s:6:\"valign\";i:1;s:6:\"border\";i:1;s:4:\"lang\";i:1;s:8:\"longdesc\";i:1;s:5:\"title\";i:1;s:2:\"id\";i:1;s:3:\"alt\";i:1;s:5:\"style\";i:1;}s:8:\"noscript\";a:0:{}s:6:\"object\";a:7:{s:4:\"type\";i:1;s:4:\"data\";i:1;s:7:\"classid\";i:1;s:8:\"codebase\";i:1;s:5:\"width\";i:1;s:6:\"height\";i:1;s:5:\"align\";i:1;}s:2:\"ol\";a:2:{s:5:\"class\";i:1;s:5:\"style\";i:1;}s:1:\"p\";a:4:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"align\";i:1;s:4:\"lang\";i:1;}s:5:\"param\";a:2:{s:4:\"name\";i:1;s:5:\"value\";i:1;}s:6:\"script\";a:3:{s:3:\"src\";i:1;s:8:\"language\";i:1;s:4:\"type\";i:1;}s:4:\"span\";a:3:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:4:\"lang\";i:1;}s:5:\"table\";a:6:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"width\";i:1;s:6:\"border\";i:1;s:11:\"cellspacing\";i:1;s:11:\"cellpadding\";i:1;}s:5:\"tbody\";a:0:{}s:2:\"td\";a:6:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"align\";i:1;s:6:\"valign\";i:1;s:7:\"colspan\";i:1;s:7:\"rowspan\";i:1;}s:2:\"th\";a:6:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"align\";i:1;s:6:\"valign\";i:1;s:7:\"colspan\";i:1;s:7:\"rowspan\";i:1;}s:2:\"tr\";a:4:{s:5:\"class\";i:1;s:2:\"id\";i:1;s:5:\"align\";i:1;s:6:\"valign\";i:1;}s:2:\"ul\";a:2:{s:5:\"class\";i:1;s:5:\"style\";i:1;}}'
    WHERE name = 'admin_html'
    ";

//HTMLフィルタ･･･RootユーザはHTMLフィルタを無効にする 
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'i:1;'
    WHERE name = 'skip_html_filter_for_root'
    ";



//バッドワードチェック･･･チェックモード いいえ
$_SQL[] = "
    UPDATE   {$_TABLES['conf_values']} SET 
    value = 'i:0;'
    WHERE name = 'censormode'
    ";


?>
