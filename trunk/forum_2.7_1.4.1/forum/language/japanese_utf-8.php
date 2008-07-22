<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.0 for Geeklog - The Ultimate Weblog
// | Official release date: Feb 7,2003
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php ★もし万一エンコードがutf-8でなければ変換してください
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000,2001 by the following authors: 
// | Geeklog Author: Tony Bibbs       - tony@tonybibbs.com
// +---------------------------------------------------------------------------+
// | FORUM Plugin Authors 
// | Prototype & Concept    :  Mr.GxBlock of www.gxblock.com 
// | Co-Developed by Matthew and Blaine
// | Matthew DeWyer, contact: matt@mycws.com          www.cweb.ws
// | Blaine Lang,    contact: geeklog@langfamily.ca   www.langfamily.ca
// +---------------------------------------------------------------------------+
// |  
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License
// | as published by the Free Software Foundation; either version 2 
// | of the License, or (at your option) any later version.
// | 
// | This program is distributed in the hope that it will be useful, 
// | but WITHOUT ANY WARRANTY; without even the implied warranty of
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// | GNU General Public License for more details.
// | 
// | You should have received a copy of the GNU General Public License
// | along with this program; if not, write to the Free Software Foundation,
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// | 
// +---------------------------------------------------------------------------+
//
# Tranlated by Geeklog Japanese group SaY and Ivy
//@@@@@20050628 2.3用から　2.3.2_1.3.9用に改定
//@@@@@20051220 2.5RC1.3_1.3.11用に改定
//@@@@@20060327 $LANG_GF00['adminmenu'] 日本語版用に復活
//@@@@@20060427 $LANG_GF93['addmoderator']
//@@@@@20070104 2.6RC3用に更新(mystral-kk)
# Last Update 2007/02/05 by Ivy (Geeklog Japanese)
//@@@@@20070319 2.6RC4用に更新
//@@@@@20070326 2.6RC5(final)用に更新
//@@@@@20070925 2.7用に更新 $LANG_GF01['admin'],$LANG_GF93['vieworder'] 追加
//@@@@@20080721 2.7.1用に更新 $LANG_GF02['msg202']  追加

$LANG_GF00 = array (
    'admin_only'        => '管理者のみです。もしあなたが管理者なら，先にログインしてください。',
    'plugin'            => 'プラグイン',
    'pluginlabel'       => '掲示板',
    'searchlabel'       => '掲示板',
    'statslabel'        => '全掲示板投稿',
    'statsheading1'     => '掲示板上位10位閲覧数',
    'statsheading2'     => '掲示板上位10位書き込み数',
    'statsheading3'     => '投稿はありません。',
    'searchresults'     => '掲示板検索結果 %s',
    'useradminmenu'     => '掲示板の機能',
    'useradmintitle'    => '掲示板のユーザ設定',
    'access_denied'     => 'アクセスが拒否されました',
    'access_denied_msg' => 'Rootユーザのみこのページにアクセスできます。あなたの名前とIPアドレスは記録されました。',
    'admin'             => 'プラグイン管理者',
    'install_header'    => 'プラグインのインストール/アンインストール',
    'installed'         => 'プラグインとブロックがインストールされました。<p><i>楽しんでください<br><a href="mailto:langmail@sympatico.ca">Blaine</a></i>',
    'uninstalled'       => 'Forumプラグインはアンインストールされました。',
    'install_success'   => 'インストールは<p><b>次のステップへ</b>:   <ol><li>掲示板管理者は，新しく掲示板を開設してください。<li>掲示板の設定と個人の設定を再設定してください。 <li>少なくとも掲示板を1つ，カテゴリを1つ，作成してください。</ol> <p><a href="%s">インストールの注意</a> を再度ご覧ください。',





    'install_failed'    => 'インストール失敗です。 -- エラーログ(error.log)を見て原因を確かめてください。',
    'uninstall_msg'     => 'Forumプラグインをアンインストールしました。',
    'install'           => 'インストール',
    'uninstall'         => 'アンインストール',
    'enabled'           => '<br>プラグインはインストールされ，有効になっています。<p>',
    'warning'           => '掲示板アンインストール警告',
    'uploaderr'         => 'ファイルアップロードエラー'
);


$PLG_forum_MESSAGE1 = '掲示板プラグインアップグレード: 成功しました。';
$PLG_forum_MESSAGE2 = '掲示板プラグインアップグレード: 自動インストール失敗。プラグインドキュメントをご覧ください。';

$LANG_GF01['LOGIN']          = 'ログイン';
$LANG_GF01['FORUM']          = '掲示板';
$LANG_GF01['ALL']            = 'すべて'; 
$LANG_GF01['YES']            = 'はい';
$LANG_GF01['NO']             = 'いいえ';
$LANG_GF01['NEW']            = '新着';
$LANG_GF01['PREV']           = 'プレビュー';
$LANG_GF01['NEXT']           = '次へ';
$LANG_GF01['ERROR']          = 'エラー!';
$LANG_GF01['CONFIRM']        = '確認';
$LANG_GF01['UPDATE']         = '更新';
$LANG_GF01['SAVE']           = '保存';
$LANG_GF01['CANCEL']         = '取り消し';
$LANG_GF01['CLOSE']          = '閉じる';
$LANG_GF01['ON']             = '投稿日: ';
$LANG_GF01['ON2']            = '&nbsp;&nbsp;<b>オン: </b>';
$LANG_GF01['IN']             = 'イン: ';
$LANG_GF01['BY']             = '投稿者: ';
$LANG_GF01['RE']             = '書き込み: ';
$LANG_GF01['NA']             = 'N/A';
$LANG_GF01['DATE']           = '日付';
$LANG_GF01['VIEWS']          = '閲覧数';
$LANG_GF01['REPLIES']        = '書き込み数';
$LANG_GF01['NAME']           = '名前:';
$LANG_GF01['DESCRIPTION']    = '説明: ';
$LANG_GF01['TOPIC']          = '件名';
$LANG_GF01['TOPICS']         = '投稿:';
$LANG_GF01['TOPICSUBJECT']   = '件名';
$LANG_GF01['FROM']           = 'から';
$LANG_GF01['REPLY']          = '新しく書き込む';
$LANG_GF01['PM']             = 'PM';
$LANG_GF01['HOME']           = '掲示板表示';
$LANG_GF01['HOMEPAGE']       = 'ホーム';
$LANG_GF01['SUBJECT']        = '件名';
$LANG_GF01['HELLO']          = 'こんにちは！ ';
$LANG_GF01['MEMBERS']        = 'メンバー';
$LANG_GF01['MOVED']          = '移動';
$LANG_GF01['REMOVE']         = '移動&amp;削除';
$LANG_GF01['CURRENT']        = '最新';
$LANG_GF01['STARTEDBY']      = '最初の投稿者';
$LANG_GF01['POSTS']          = '投稿数';
$LANG_GF01['LASTPOST']       = '最新投稿';
$LANG_GF01['POSTEDBY']       = '投稿者';
$LANG_GF01['POSTEDON']       = '投稿日';
$LANG_GF01['PAGE']           = 'ページ';
$LANG_GF01['PAGES']          = 'ページ';
$LANG_GF01['ANONYMOUS']      = 'ゲストユーザ:';
$LANG_GF01['TODAY']          = '今日の';
$LANG_GF01['WELCOME']        = 'ようこそ ';
$LANG_GF01['REGISTER']       = '登録';
$LANG_GF01['REGISTERED']     = '登録日';
$LANG_GF01['MOSTPOPULAR']    = 'もっとも人気';
$LANG_GF01['ORDERBY']        = '並び換え:';
$LANG_GF01['ORDER']          = '順番:';
$LANG_GF01['USER']           = 'ユーザ';
$LANG_GF01['GROUP']          = 'グループ';
$LANG_GF01['ANON']           = 'ゲストユーザ: ';
$LANG_GF01['ADMIN']          = '管理者';
$LANG_GF01['AUTHOR']         = '投稿者';
$LANG_GF01['LOCATION']       = '場所';
$LANG_GF01['WEBSITE']        = 'ホームページ';
$LANG_GF01['EMAIL']          = 'メール';
$LANG_GF01['MOOD']           = '気分';
$LANG_GF01['NOMOOD']         = '-気分アイコン-';
$LANG_GF01['REQUIRED']       = '[要求]';
$LANG_GF01['OPTIONAL']       = '[オプション]';
$LANG_GF01['SUBMIT']         = '保存';
$LANG_GF01['PREVIEW']        = 'プレビュー';
$LANG_GF01['NOTIFY']         = 'ご注意:';
$LANG_GF01['REMOVE']         = '削除';
$LANG_GF01['KEYWORDS']       = 'キーワード';
$LANG_GF01['EDIT']           = '編集';
$LANG_GF01['DELETE']         = '削除';
$LANG_GF01['MESSAGE']        = 'メッセージ:';
$LANG_GF01['OPTIONS']        = 'オプション:';
$LANG_GF01['MISSINGSUBJECT'] = '件名なし';
$LANG_GF01['MAY']            = '';
$LANG_GF01['IS']             = 'は';
$LANG_GF01['FOR']            = '：';
$LANG_GF01['ARE']            = '';
$LANG_GF01['NOT']            = '非';
$LANG_GF01['YOU']            = '';
$LANG_GF01['HTML']           = 'HTML';
$LANG_GF01['FULLHTML']       = '全てのHTML';
$LANG_GF01['WORDS']          = '単語';
$LANG_GF01['SMILIES']        = 'スマイリー';
$LANG_GF01['MIGRATE_NOW']    = 'インポート実行'; 
$LANG_GF01['FILTERLIST']     = 'フィルタリスト';
$LANG_GF01['SELECTFORUM']    = '掲示板を選択';
$LANG_GF01['DELETEAFTER']    = '実行後に削除';
$LANG_GF01['TITLE']          = 'タイトル';
$LANG_GF01['COMMENTS']       = 'コメント'; 
$LANG_GF01['SUBMISSIONS']    = '投稿したもの';

$LANG_GF01['HTML_FILTER_MSG']  = '一部のHTMLを許可';
$LANG_GF01['HTML_FULL_MSG']  = 'すべてのHTMLを許可';
$LANG_GF01['HTML_MSG']       = 'HTML許可';
$LANG_GF01['CENSOR_PERM_MSG']  = '';
$LANG_GF01['ANON_PERM_MSG']    = 'ゲストユーザの投稿を見る';
$LANG_GF01['POST_PERM_MSG1']    = '投稿可能';
$LANG_GF01['POST_PERM_MSG2']    = 'ゲストユーザ投稿可能';
$LANG_GF01['CENSORED']       = '検閲';
$LANG_GF01['ALLOWED']        = '許可';
$LANG_GF01['GO']             = '実行';
$LANG_GF01['STATUS']         = '状態:';
$LANG_GF01['ONLINE']         = 'オンライン';
$LANG_GF01['OFFLINE']        = 'オフライン';
$LANG_GF01['back2parent']    = '親の投稿';
$LANG_GF01['forumname']      = '';
$LANG_GF01['category']       = 'カテゴリ: ';
$LANG_GF01['loginreqview']   = '<b>掲示板に参加するためには， %s 登録</a> または %s ログイン </a> してください。</b>';
$LANG_GF01['loginreqpost']   = '<b>投稿するためには，登録またはログインしてください。</b>';
$LANG_GF01['searchresults']  = ' 検索結果 <b>%s</b> %s ： <b>%s</b> 結果:</b><br><br>';
$LANG_GF01['feature_not_on'] = '注目拒否';
$LANG_GF01['nolastpostmsg']  = 'N/A';
$LANG_GF01['no_one']         = '1つではない。';
$LANG_GF01['popular']        = '人気順リスト';
$LANG_GF01['notify']         = '通知';
$LANG_GF01['PM']             = 'PM\'s';
$LANG_GF01['NEW_PM']         = 'New PM';
$LANG_GF01['DELALL_PM']      = '全て削除';
$LANG_GF01['DELOLDER_PM']    = '古い発言を削除';
$LANG_GF01['members']        = 'メンバーリスト';
$LANG_GF01['save_sucess']    = '保存成功';
$LANG_GF01['trademark']      = '<br><center><b>Geeklog Forum Project version 2.0</b> &copy; 2002</b></center>';
$LANG_GF01['back2top']       = 'トップへ戻る';
$LANG_GF01['POSTMODE']       = '投稿モード:';
$LANG_GF01['TEXTMODE']       = 'テキストモード';
$LANG_GF01['HTMLMODE']       = 'HTMLモード';
$LANG_GF01['TopicPreview']   = '投稿プレビュー';
$LANG_GF01['moderator']      = 'モデレータ';
$LANG_GF01['admin']          = '管理者';
$LANG_GF01['DATEADDED']      = '登録日';
$LANG_GF01['PREVTOPIC']      = '前のトピックへ';
$LANG_GF01['NEXTTOPIC']      = '次のトピックへ';
$LANG_GF01['CONTENT']        = '内容';
$LANG_GF01['QUOTE_begin']    = '[引用&nbsp;';
$LANG_GF01['QUOTE_by'   ]    = 'by:&nbsp;';
$LANG_GF01['RESYNC']         = "更新";
$LANG_GF01['RESYNCCAT']      = "カテゴリを更新";  
$LANG_GF01['PROFILE']        = "プロフィール";
$LANG_GF01['DELETECONFIRM']  = "削除してよいですか?";
$LANG_GF01['website']        = 'ホームページ';
$LANG_GF01['EDITICON']       = '編集';
$LANG_GF01['QUOTEICON']      = '引用して書き込む';
$LANG_GF01['ProfileLink']    = 'プロフィール';
$LANG_GF01['WebsiteLink']    = 'ホームページ';
$LANG_GF01['PMLink']         = 'PM';
$LANG_GF01['EmailLink']      = 'メール';
$LANG_GF01['FORUMSUBSCRIBE'] = 'この掲示板の更新をメールで通知するよう設定する';
$LANG_GF01['FORUMUNSUBSCRIBE'] = 'この掲示板の更新をメールで通知しないよう設定する';
$LANG_GF01['NEWTOPIC']       = '新規投稿';
$LANG_GF01['POSTREPLY']      = '投稿';
$LANG_GF01['SubscribeLink']  = 'メール通知設定';
$LANG_GF01['unSubscribeLink'] = 'メール通知設定解除';
$LANG_GF01['FORUMSUBSCRIBE'] = '掲示板メール通知設定';
$LANG_GF01['NEWTOPIC']       = 'トピック新規登録';
$LANG_GF01['POSTREPLY']      = '新しく書き込む';
$LANG_GF01['SUBSCRIPTIONS']  = 'メール通知設定リスト';
$LANG_GF01['TOP']            = 'トピックトップ';
$LANG_GF01['PRINTABLE']      = '印刷用ページ';
$LANG_GF01['ForumProfile']   = '掲示板オプション';
$LANG_GF01['USERPREFS']      = '掲示板のユーザ設定';
$LANG_GF01['SPEEDLIMIT']     = '"あなたの最新の投稿は %s 秒前でした。<br>次の投稿まで，最低 %s 秒お待ちください。"';
$LANG_GF01['ACCESSERROR']    = 'アクセスエラー';
$LANG_GF01['LEGEND']         = '凡例';
$LANG_GF01['ACTIONS']        = 'アクション';
$LANG_GF01['DELETEALL']      = 'すべての選択したデータを削除';
$LANG_GF01['DELCONFIRM']     = '選択したデータを削除してよろしいですか？';
$LANG_GF01['DELALLCONFIRM']  = 'すべてのデータを削除してよろしいですか？';
$LANG_GF01['STARTEDBY']      = '初期投稿:';
$LANG_GF01['WARNING']        = 'ご注意';
$LANG_GF01['MODERATED']      = 'モデレータ: %s';
$LANG_GF01['NOTIFYNOT']      = 'NOT!';
$LANG_GF01['LASTREPLYBY']    = '最新の書き込み者:&nbsp;%s';
$LANG_GF01['UID']            = 'UID';
$LANG_GF01['ANON_POST_BEGIN'] = 'ゲストユーザ投稿スタート';
$LANG_GF01['ANON_POST_END']   = 'ゲストユーザ閲覧終了';
$LANG_GF01['INDEXPAGE']      = '掲示板目次';
$LANG_GF01['FEATURE']        = '機能';
$LANG_GF01['SETTING']        = '設定';
$LANG_GF01['MARKALLREAD']    = 'すべて既読にする';

// Language for bbcode toolbar
$LANG_GF01['CODE']           = 'コード';
$LANG_GF01['FONTCOLOR']      = '文字色';
$LANG_GF01['FONTSIZE']       = '文字サイズ';
$LANG_GF01['CLOSETAGS']      = 'タグを閉じる';
$LANG_GF01['CODETIP']        = 'ヒント: 選択した文字列にすぐにスタイルを適用できます';
$LANG_GF01['TINY']           = '小さい';
$LANG_GF01['SMALL']          = '小さめ';
$LANG_GF01['NORMAL']         = '標準';
$LANG_GF01['LARGE']          = '大きめ';
$LANG_GF01['HUGE']           = '大きい';
$LANG_GF01['DEFAULT']        = '既定';
$LANG_GF01['DKRED']          = '濃赤';
$LANG_GF01['RED']            = '赤';
$LANG_GF01['ORANGE']         = 'オレンジ';
$LANG_GF01['BROWN']          = '茶';
$LANG_GF01['YELLOW']         = '黄';
$LANG_GF01['GREEN']          = '緑';
$LANG_GF01['OLIVE']          = 'オリーブ';
$LANG_GF01['CYAN']           = '水色';
$LANG_GF01['BLUE']           = '青';
$LANG_GF01['DKBLUE']         = '濃青';
$LANG_GF01['INDIGO']         = '藍色';
$LANG_GF01['VIOLET']         = '紫';
$LANG_GF01['WHITE']          = '白';
$LANG_GF01['BLACK']          = '黒';

$LANG_GF01['b_help']         = "太字にする: [b]text[/b]";
$LANG_GF01['i_help']         = "イタリック体にする: [i]text[/i]";
$LANG_GF01['u_help']         = "下線を引く: [u]text[/u]";
$LANG_GF01['q_help']         = "引用する: [quote]text[/quote]";
$LANG_GF01['c_help']         = "コードを表示する: [code]code[/code]";
$LANG_GF01['l_help']         = "数字なしリストにする: [list]text[/list]";
$LANG_GF01['o_help']         = "数字付きリストにする: [olist]text[/olist]";
$LANG_GF01['p_help']         = "[img]http://画像のurl[/img]  または  [img w=100 h=200][/img]";
$LANG_GF01['w_help']         = "URLを挿入する: [url]http://url[/url] または [url=http://url]URLテキスト[/url]";
$LANG_GF01['a_help']         = "閉じていないbbCodeのタグをすべて閉じる";
$LANG_GF01['s_help']         = "文字色: [color=red]text[/color]  ヒント: color=#FF0000 という形式でも指定できます";
$LANG_GF01['f_help']         = "文字サイズ: [size=x-small]小さめの文字[/size]";
$LANG_GF01['h_help']         = "詳細を見るにはクリックしてください";


$LANG_GF02['msg01']    = '申し訳ありませんが，掲示への参加には登録が必要です。 ';
$LANG_GF02['msg02']    = '申し訳ありませんが，この掲示板への参加には登録が必要です。';
$LANG_GF02['msg03']    = '';
$LANG_GF02['msg04']    = '';
$LANG_GF02['msg05']    = '<center><i>まだ登録されていません。</center></i>';
$LANG_GF02['msg06']    = '最後の訪問以後の投稿';
$LANG_GF02['msg07']    = 'オンラインユーザ:';
$LANG_GF02['msg08']    = '<br><b>すべての登録ユーザの登録日時:</b> %s';
$LANG_GF02['msg09']    = '<br><b>すべての投稿日時:</b> %s <br>';
$LANG_GF02['msg10']    = '<b>すべての投稿日時:</b> %s <br>';
$LANG_GF02['msg11']    = '掲示板インデックスに戻る';
$LANG_GF02['msg12']    = 'メインホームページに戻る';
$LANG_GF02['msg13']    = '登録が必要です。';
$LANG_GF02['msg14']    = '登録が必要です。<br>';
$LANG_GF02['msg15']    = 'エラーだと思われたら， <a href="mailto:%s?subject=Guestbook IP Ban">掲示板管理者</A>まで。';
$LANG_GF02['msg16']    = 'よくある投稿です。他の投稿や書き込みをご覧ください。';
$LANG_GF02['msg17']    = 'メッセージ編集が完了しされました。';
$LANG_GF02['msg18']    = 'エラー! 必須項目が入力されていないかまたは短すぎます。';
$LANG_GF02['msg19']    = 'メッセージが登録されました';
$LANG_GF02['msg20']    = '書き込みが登録されました。';
$LANG_GF02['msg21']    = '実行権限がありません。<a href="javascript:history.back()">戻る</a>か<a href="%s/users.php?mode=login">ログイン</a>するかしてください。<br><br>'; 
$LANG_GF02['msg22']    = '- 掲示板投稿通知';
$LANG_GF02['msg23a']   = "掲示板[%s]に%sさんから新しく書き込みがありました。\n（トピック作成者：%sさん　掲示板：%s）";
$LANG_GF02['msg23b']   = "新しいトピック '%s' が\n%s さんによって %s 掲示板に投稿されました。\n（サイト：%s）\n\n%s/forum/viewtopic.php?showtopic=%s\n";
$LANG_GF02['msg23c']   = "%s/forum/viewtopic.php?showtopic=%s&amp;lastpost=true\n";
$LANG_GF02['msg24']    = 'スレッドを見て書き込み: ';
$LANG_GF02['msg25']    = "\n";
$LANG_GF02['msg26']    = "\n※このトピックはメール通知が設定されています。";
$LANG_GF02['msg27']    = "\nメール通知解除: \n%s\n";
$LANG_GF02['msg28']    = 'エラー：件名がありません。';
$LANG_GF02['msg29']    = 'あなたの署名はここに表示されます。';
$LANG_GF02['msg30']    = 'トップへ戻る';
$LANG_GF02['msg31']    = '<b>編集できます:</b>';
$LANG_GF02['msg32']    = '<b>メッセージの編集</b>';
$LANG_GF02['msg33']    = '投稿者: ';
$LANG_GF02['msg34']    = 'メール:';
$LANG_GF02['msg35']    = 'ホームページ:';
$LANG_GF02['msg36']    = '気分アイコン:';
$LANG_GF02['msg37']    = 'メッセージ:';
$LANG_GF02['msg38']    = '新規書き込みをメールで通知　';
$LANG_GF02['msg39']    = '<br>新規投稿レビューできません。';
$LANG_GF02['msg40']    = '<br>既にメール通知設定されています。<br><br>';
$LANG_GF02['msg41']    = '<br> %s への投稿はメール通知設定されました。<br><br>';
$LANG_GF02['msg42']    = 'このトピックのメール通知設定を解除しました。';
$LANG_GF02['msg43']    = 'このメール通知設定を解除してよいですか?.';
$LANG_GF02['msg44']    = '<p style="margin:0px; padding:5px;">メール通知設定したトピックはありません。</p>';
$LANG_GF02['msg45']    = '掲示板の検索';
$LANG_GF02['msg46']    = '掲示板キーワード検索:';
$LANG_GF02['msg47']    = '投稿者名を指定することもできます:';
$LANG_GF02['msg48']    = '<br>先にChatterblockプラグインをインストールしてください。';
$LANG_GF02['msg49']    = '(参照数 %s回) ';
$LANG_GF02['msg50']    = '署名 n/a';
$LANG_GF02['msg51']    = "%s\n\n<br>[編集 %s by %s]";
$LANG_GF02['msg52']    = '確定:';
$LANG_GF02['msg53']    = 'トピックへ戻ります..';
$LANG_GF02['msg54']    = '投稿は編集されました。';
$LANG_GF02['msg55']    = '削除されました。';
$LANG_GF02['msg56']    = 'IPアドレスは禁止されました。';
$LANG_GF02['msg57']    = '注目トピック設定';
$LANG_GF02['msg58']    = '注目トピック設定解除';
$LANG_GF02['msg59']    = '通常';
$LANG_GF02['msg60']    = '新着';
$LANG_GF02['msg61']    = '注目トピック';
$LANG_GF02['msg62']    = '書き込みがあればメール通知する';
$LANG_GF02['msg63']    = 'プロフィール';
$LANG_GF02['msg64']    = 'トピック %s 件名: %s 　を本当に削除してもよろしいですか?';
$LANG_GF02['msg65']    = '<br>これは親投稿です。そのためこのトピックの中のすべての書き込みもあわせて削除されます。';
$LANG_GF02['msg66']    = '投稿削除確定';
$LANG_GF02['msg67']    = 'フォラーム投稿編集';
$LANG_GF02['msg68']    = '注意: 禁止は注意深く行ってください。管理者だけが禁止者を解除できます。';
$LANG_GF02['msg69']    = '<br>本当にこのIPアドレスを禁止しますか: %s?';
$LANG_GF02['msg70']    = '禁止確定';
$LANG_GF02['msg71']    = '機能が選択されていません。投稿を選択しモデレータの機能を実行してください。<br>注意:あなたはモデレータとなってこれらの機能を使ってください。';
$LANG_GF02['msg72']    = 'このメッセージがオンラインなら管理者機能は成功しません。';
$LANG_GF02['msg74']    = '最近の投稿 %s 件';
$LANG_GF02['msg75']    = '閲覧数トップ %s 件';
$LANG_GF02['msg76']    = '投稿数トップ %s 件';
$LANG_GF02['msg77']    = '<br><p style="padding-left: 10px;">申し訳ありません。先にログインしてください。アカウントがなければ新規登録してください。<p />';
$LANG_GF02['msg78']    = '<br>ここに掲示板はありません。';
$LANG_GF02['msg81']    = '- 投稿編集通知';
$LANG_GF02['msg82']    = '<p>あなたのメッセージ "%s" は，モデーレータ %s によって編集されました。<p>';
$LANG_GF02['msg83']    = '<br><br>掲示板のタイトルを入力してください。<p />';
$LANG_GF02['msg84']    = 'すべて読んだことにする';
$LANG_GF02['msg85']    = 'ページ:';
$LANG_GF02['msg86']    = '最新 %s 投稿　投稿者: ';
$LANG_GF02['msg87']    = '<br>警告:このトピックはロックされています。<br>追加の投稿はできません。';
$LANG_GF02['msg88']    = '掲示板投稿者リスト';
$LANG_GF02['msg88b']   = '掲示板発言者のみ';
$LANG_GF02['msg89']    = 'メール通知設定リスト';
$LANG_GF02['msg100']   = '情報:';
$LANG_GF02['msg101']   = 'ルール:';
$LANG_GF02['msg102']   = '投稿テーマ:';
$LANG_GF02['msg103']   = '掲示板ジャンプ:';
$LANG_GF02['msg104']   = '投稿メッセージ';
$LANG_GF02['msg105']   = 'あなたの投稿を編集';
$LANG_GF02['msg106']   = '掲示板を選択';
$LANG_GF02['msg107']   = '掲示板テーマ:';
$LANG_GF02['msg108']   = '新規投稿のある掲示板';
$LANG_GF02['msg109']   = 'ロックされたトピック';
$LANG_GF02['msg110']   = '編集ページに移動中..';
$LANG_GF02['msg111']   = '未読リスト:';
$LANG_GF02['msg112']   = '未読を表示する';
$LANG_GF02['msg113']   = '未読を表示する';
$LANG_GF02['msg114']   = 'ロック済';
$LANG_GF02['msg115']   = '注目トピック新着';
$LANG_GF02['msg116']   = 'ロック済トピック 新着';
$LANG_GF02['msg117']   = 'サイト検索';
$LANG_GF02['msg118']   = '掲示板検索';
$LANG_GF02['msg119']   = '検索結果:';
$LANG_GF02['msg120']   = '人気順 by';
$LANG_GF02['msg121']   = 'すべての時刻は %s。現在，%s';
$LANG_GF02['msg122']   = '人気順リスト件数:';
$LANG_GF02['msg123']   = '人気順リストに表示する件数';
$LANG_GF02['msg124']   = '1ページごとのメッセージ数';
$LANG_GF02['msg125']   = 'モデレータのみ: メッセージ一覧画面';
$LANG_GF02['msg126']   = '検索ライン:';
$LANG_GF02['msg127']   = '探索結果に表示するラインの数';
$LANG_GF02['msg128']   = '投稿者数/1ページ:';
$LANG_GF02['msg129']   = '投稿者リストの1ページに表示する人数';
$LANG_GF02['msg130']   = 'ゲストユーザ投稿表示:';
$LANG_GF02['msg131']   = 'ゲストユーザ投稿を選別する';
$LANG_GF02['msg132']   = 'メール通知設定モード:';
$LANG_GF02['msg133']   = '書き込みがあればメール通知を既定値にする';
$LANG_GF02['msg134']   = '投稿が追加されました。';
$LANG_GF02['msg135']   = 'あなたの全ての投稿が掲示板に通知されます';
$LANG_GF02['msg136']   = '投稿先の掲示板を選んでください。';
$LANG_GF02['msg137']   = '書き込みがあればメールで通知されます。';
$LANG_GF02['msg138']   = '<b>掲示板全体</b>';
$LANG_GF02['msg139']   = '%s 続ける場合は<a href="%s">クリック</a>';
$LANG_GF02['msg140']   = '続ける';
$LANG_GF02['msg141']   = 'このページは自動的に戻ります。戻らない場合は <a href="%s">こちら</a>';
$LANG_GF02['msg142']   = 'メール通知設定モードに変更しました。';
$LANG_GF02['msg143']   = '通知';
$LANG_GF02['msg144']   = 'トピックへ';
$LANG_GF02['msg145']   = 'スレッド参照';
$LANG_GF02['msg146']   = 'メール通知設定モードは解除されました。';
$LANG_GF02['msg147']   = '掲示板の印刷';
$LANG_GF02['msg148']   = '<a href="javascript:history.back()">戻る</a>';
$LANG_GF02['msg149']   = ' %s インスタントメッセージを送ってください。';
$LANG_GF02['msg150']   = 'あなたの投稿 %sの中で';
$LANG_GF02['msg151']   = '最新の投稿';
$LANG_GF02['msg152']   = '人気の投稿';
$LANG_GF02['msg153']   = '人気の書き込みトピック';
$LANG_GF02['msg154']   = '最新の投稿';
$LANG_GF02['msg155']   = '投稿なし';
$LANG_GF02['msg156']   = '投稿数';
$LANG_GF02['msg157']   = '最新10投稿';
$LANG_GF02['msg158']   = '最新10投稿者 ';
$LANG_GF02['msg159']   = 'モデレータのデータを本当に削除してもよいですか？';
$LANG_GF02['msg160']   = '投稿の最後のページを見る';
$LANG_GF02['msg161']   = 'メンバーリストへ戻る';
$LANG_GF02['msg162']   = '<a href="%s">こちら</a><br><p>へ自動的に戻りますが，すぐに戻りたい場合は <a href="%s">こちら</a>';
$LANG_GF02['msg163']   = '投稿が移動しました';
$LANG_GF02['msg164']   = 'すべて読んだことにする';
$LANG_GF02['msg165']   = 'エラー：<p>マッチする <b>引用</b> タグがありません。フォーマットできません。</p>';
$LANG_GF02['msg166']   = 'エラー: 記事が壊れたか，見つかりません。';
$LANG_GF02['msg167']   = '通知オプション';
$LANG_GF02['msg168']   = 'メール通知を無効にする';
$LANG_GF02['msg169']   = 'メンバーリストへ戻る';
$LANG_GF02['msg170']   = '最新の投稿';
$LANG_GF02['msg171']   = '掲示板アクセスエラー';
$LANG_GF02['msg172']   = '投稿がないか，削除されています。';
$LANG_GF02['msg173']   = 'メッセージ投稿ページへ移ります...';
$LANG_GF02['msg174']   = 'BAN Memberが見れません。 - IPアドレスが不正';
$LANG_GF02['msg175']   = '掲示板一覧へ戻る';
$LANG_GF02['msg176']   = 'メンバー選択';
$LANG_GF02['msg177']   = 'すべてのメンバー';
$LANG_GF02['msg178']   = '親の投稿のみ';
$LANG_GF02['msg179']   = '内容生成: %s 秒';
$LANG_GF02['msg180']   = '掲示板投稿警告';
$LANG_GF02['msg181']   = 'あなたは掲示板管理者としてアクセスできません。';
$LANG_GF02['msg182']   = 'モデレータ確認';
$LANG_GF02['msg183']   = '新規投稿: %s';
$LANG_GF02['msg184']   = '1回のみ通知';
$LANG_GF02['msg185']   = '次に訪問するまでに，複数の投稿があっても通知は1回のみする';
$LANG_GF02['msg186']   = '新投稿件名';
$LANG_GF02['msg187']   = '<a href="%s">投稿へ戻る</a>';
$LANG_GF02['msg188']   = 'クリックすると最新投稿へジャンプします。';
$LANG_GF02['msg189']   = 'エラー: もうこの投稿は編集できません。';
$LANG_GF02['msg190']   = 'こっそり編集';
$LANG_GF02['msg191']   = '編集できません。編集可能期間が終了したか，モデレータ権限がありません。';
$LANG_GF02['msg192']   = '完了しました。%s個のトピックと %s個のコメントをインポートしました。';
$LANG_GF02['msg193']   = '記事を掲示板にインポートするユーティリティ';  
$LANG_GF02['msg194']   = '新規投稿のない掲示板';
$LANG_GF02['msg195']   = 'クリックすると掲示板へジャンプします';
$LANG_GF02['msg196']   = '掲示板の目次を見る';
$LANG_GF02['msg197']   = 'すべてのカテゴリのトピックを既読にする';
$LANG_GF02['msg198']   = '掲示板の設定を更新する';
$LANG_GF02['msg199']   = '掲示板通知を見る/削除する';
$LANG_GF02['msg200']   = 'サイトメンバーのレポートを見る';
$LANG_GF02['msg201']   = '人気トピックレポートを見る';
$LANG_GF02['msg202']   = '新しい投稿がありません';//


$LANG_GF02['StatusHeading']   = '情報';
$LANG_GF02['PostReply']   = '新しく書き込む';
$LANG_GF02['PostTopic']   = '新規投稿';
$LANG_GF02['EditTopic']   = '投稿編集';
$LANG_GF02['quietforum']  = '掲示板に新規投稿がありません。';

$LANG_GF03 = array (
    'welcomemsg'        => 'ようこそモデレータさん',
    'title'             => 'モデレータの機能:&nbsp;',
    'delete'            => '削除',
    'edit'              => '編集',
    'move'              => '移動',
    'ban'               => 'IPアドレス禁止',
    'stick'             => '注目トピックに設定する',
    'unstick'           => '注目トピックを取り消す',
    'movetopic'         => '移動&amp;削除',
    'movetopicmsg'      => '<br> 次の掲示板へ <b>%s</b> を移動します',
    'lockedpost'        => '書き込みを追加',
    'split'             => '投稿分割',
    'splittopicmsg'     => '<br>新規投稿: "<b>%s</b>"<br><em>投稿者:</em>&nbsp;%s&nbsp; <em>元の投稿:</em>&nbsp;%s',
    'selectforum'       => '新規掲示板選択:',
    'splitheading'      => 'スレッドオプション分割:',
    'splitopt1'         => 'ここからすべての投稿を移動',
    'splitopt2'         => '1投稿のみ移動'
);

$LANG_GF04 = array (
    'label_forum'             => '掲示板の概要',
    'label_location'          => '場所',
    'label_aim'               => 'AIMハンドル名',
    'label_yim'               => 'YIMハンドル名',
    'label_icq'               => 'ICQハンドル名',
    'label_msnm'              => 'MSNメッセンジャー名',
    'label_interests'         => '趣味',
    'label_occupation'        => '仕事',
);

/* Settings for Additional User profile - Instant Messenging links */
$LANG_GF05 = array (
    'aim_link'               => '&nbsp;<a href="aim:goim?screenname=',
    'aim_linkend'            => '>',
    'aim_hello'              => '&amp;message=Hi.+Are+you+there?',
    'aim_alttext'            => 'AIM:&nbsp;',
    'icq_link'               => '&nbsp;',
    'icq_alttext'            => 'ICQ #:&nbsp;',
    'msn_link'               => '&nbsp;<a href="javascript:MsgrApp.LaunchIMUI(',
    'msn_linkend'            => ')">',
    'msn_alttext'            => 'Messenger:&nbsp;',
    'yim_link'               => '&nbsp;<a href="ymsgr:sendIM?',
    'yim_linkend'            => '">',
    'yim_alttext'            => 'YIM:&nbsp;',
);


/* Admin Navbar */
$LANG_GF06 = array (
    1   => '統計',
    2   => '設定',
    3   => '掲示板管理',
    4   => 'モデレータ',
    5   => '記事を掲示板へ',
    6   => 'メッセージ',
    7   => '禁止IPアドレス'
);

/* User Functions Navbar */
$LANG_GF07 = array (
    1   => '掲示板の表示',
    2   => 'ユーザ設定',
    3   => '書き込み数人気順',
    4   => 'メール通知設定リスト',
    5   => '投稿者リスト'
);



/* Forum User Features */
$LANG_GF08 = array (
    1   => '投稿の注意',
    2   => '掲示板Track の注意',
    3   => '投稿例外注意'
);


$LANG_GF90 = array (
    'viewforums'        => '目次',
    'stats'             => '統計',
    'settings'          => '設定',
    'boardadmin'        => '掲示板',
    'migrate'           => 'コンバート',
    'mods'              => '調整',
    'messages'          => 'メッセージ',
    'ipman'             => '禁止IPアドレス'
);

$LANG_GF91 = array (
    'gfstats'            => '掲示板の統計',
    'statsmsg'           => '現在:',
    'totalcats'          => '合計 カテゴリー数:',
    'totalforums'        => '合計 掲示板数:',
    'totaltopics'        => '合計 トピック数:',
    'totalposts'         => '合計 投稿数:',
    'totalviews'         => '合計 閲覧数:',
    'avgpmsg'            => '平均投稿数:',
    'category'           => 'カテゴリー:',
    'forum'              => '掲示板:',
    'topic'              => 'トピック:',
    'avgvmsg'            => '平均閲覧数:'
);

// Settings.php 
$LANG_GF92 = array (
    'gfsettings'         => '設定',
    'gensettings'        => '一般',
    'gensettings'        => '一般',
    'topicsettings'      => 'トピックの投稿',
    'blocksettings'      => 'サイドブロック(forum_newposts)',
    'ranksettings'       => '説明設定ランキング',
    'htmlsettings'       => 'HTML設定',
    'avsettings'         => 'アバター設定',
    'ranksettings'       => 'ランク',
    'savesettings'       => '    変更を保存する    ', 
    'allowhtml'          => 'HTML使用',
    'allowhtmldscp'      => '投稿時にHTMLの使用を許可する。NOに設定すると，プレーンテキストでしか投稿できないが，bbcodeは使用できる。',//'Enable HTML to be used in posts. If set to NO then users will only be able to post in TEXT Mode but still use bbcode'
    'glfilter'           => 'HTMLフィルタリング',
    'glfilterdscp'       => 'Geeklog本体のHTMLフィルタを使用する',
    'censor'             => '検閲',
    'censordscp'         => '検閲する（Geeklog本体の検閲機能を使用する)',
    'showmoods'          => '気分アイコン',
    'showmoodsdscp'      => '使用する',
    'allowsmilies'       => 'スマイリーアイコン',
    'allowsmiliesdscp'   => '使用する',
    'allownotify'        => '通知許可:',
    'allownotifydscp'    => '通知を許可する',
    'showiframe'         => 'トピックレビュー表示',
    'showiframedscp'     => 'トピックに新しく書き込む場合，下にトピックの内容を表示する',    'autorefresh'        => '自動再表示',
    'autorefresh'        => '自動再表示',
    'autorefreshdscp'    => '投稿後自動的に再表示',
    'refreshdelay'       => '再表示までの秒数',
    'refreshdelaydscp'   => '自動再表示を指定した場合，再表示までの秒数',
    'xtrausersettings'   => '掲示板のユーザ設定',
    'xtrausersettingsdscp'    => 'ユーザ設定を許可する',
    'titleleng'          => '件名の長さ',
    'titlelengdscp'      => '入力できる件名の最大バイト数',
    'topicspp'           => '1ページごとのトピック数',
    'topicsppdscp'       => '各掲示板でトピックの一覧を表示する場合の1ページに表示するトピックの数',
    'postspp'            => '1ページごとの投稿数',
    'postsppdscp'        => '各トピックで投稿メッセージを表示する場合の1ページ当に表示する投稿数',
    'regview'            => '閲覧許可',
    'regviewdscp'        => '投稿を見るためにはアカウントの登録が必要',
    'regpost'            => '投稿許可',
    'regpostdscp'        => '投稿するためにはアカウントの登録が必要',
    'imgset'             => '画像セット',
    'lev1'               => 'レベル 1',
    'lev1dscp'           => 'ランク 1',
    'lev2'               => 'レベル 2',
    'lev2dscp'           => 'ランク 2',
    'lev3'               => 'レベル 3',
    'lev3dscp'           => 'ランク 3',
    'lev4'               => 'レベル 4',
    'lev4dscp'           => 'ランク 4',
    'lev5'               => 'レベル 5',
    'lev5dscp'           => 'ランク 5',
    'setsave'            => '設定は保存されました',
    'setsavemsg'         => '設定は保存されました。',
    'allownotify'        => 'メール通知',
    'allownotifydscp'    => 'メールで通知する',
    'defaultmode'        => '既定の投稿モード',
    'defaultmodedscp'    => 'HTMLモードを既定にするには，Yesに設定する。<br>プレーンテキストモード（より安全）を既定にするには，Noに設定する。',
    'cbsettings'         => 'センターエリア',
    'cbenable'           => '表示',
    'cbenabledscp'       => 'センターエリアに表示',
    'cbhomepage'         => 'トップページのみ',
    'cbhomepagedscp'     => '1ページ目のみ表示する',
    'cbposition'         => '位置',
    'cbpositiondscp'     => '表示位置',
    'position'           => '位置 ', 
    'all_topics'         => 'すべて',
    'no_topic'           => 'ホームページのみ',
    'position_top'       => 'ページのトップ',
    'position_feat'      => '特集記事のあと',
    'position_bottom'    => 'ページのボトム',
    'messagespp'         => '1ページごとのメッセージ数',
    'messagesppdscp'     => 'メッセージ管理画面 - 1ページごとのメッセージ数',
    'searchespp'         => '検索結果',
    'searchesppdscp'     => '検索結果の1ページごとの表示数',
    'minnamelength'      => 'ゲストユーザ名の長さ',
    'minnamedscp'        => 'ゲストユーザの最小の名前の長さ',
    'mincommentlength'   => '最小本文長',
    'mincommentdscp'     => '投稿本文の最小の長さ',
    'minsubjectlength'   => '最小件名長',
    'minsubjectdscp'     => '件名の最小の長さ',
    'popular'            => '一般的な投稿',
    'populardscp'        => '閲覧回数',
    'convertbreak'       => '改行変換',
    'convertbreakdscp'   => '改行をHTMLタグ(&lt;br&gt;)に変換する',
    'speedlimit'         => '投稿間隔制限',
    'speedlimitdscp'     => '投稿間隔を制限する',
    'cb_subjectsize'     => '件名の長さ',
    'cb_subjectsizedscp' => '表示するトピックの件名のバイト数',
    'cb_numposts'        => '投稿数',
    'cb_numpostsdscp'    => 'センターエリアに表示する投稿数',
    'sb_subjectsize'     => '件名の長さ',
    'sb_subjectsizedscp' => '表示するメッセージの件名のバイト数',
    'sb_numposts'        => '投稿数',
    'sb_numpostsdscp'    => 'サイドブロックに表示する投稿数',
    'sb_latestposts'     => '最新投稿',
    'sb_latestpostsdscp' => '1トピックにつき1メッセージのみ表示する',
    'userdatefmt'        => '日付のフォーマット',
    'userdatefmtdscp'    => 'ユーザが設定した日付のフォーマットに従う',
    'spamxplugin'        => 'Spam-Xプラグイン',
    'spamxplugindscp'    => '投稿前にSpam-Xプラグインでスパムか判定する',
    'pmplugin'           => 'PMプラグイン',
    'pmplugindscp'       => 'プライベートメッセージプラグインは別途インストールされていて，それを有効にする',
    'smiliesplugin'      => 'スマイリープラグイン',
    'smiliesplugindscp'  => 'スマイリープラグインまたは外部関数を使う',
    'geshiformat'        => 'コード構文強調',
    'geshiformatdscp'    => 'GeSHiコード構文強調機能を使う',
    'edit_timewindow'    => 'タイムフレーム編集',
    'edit_timewindowdscp' => '編集を自分自身で編集させる時間を設定'

);

// Board Admin
$LANG_GF93 = array (
    'gfboard'            => '掲示板管理',
    'vieworder'          => '順番を見る',
    'addcat'             => 'カテゴリーを追加',
    'addforum'           => '掲示板を追加',
    'order'              => '順番:',
    'catorder'           => 'カテゴリーの順番',
    'forumorder'         => '掲示板の順番',
    'catadded'           => 'カテゴリーが追加されました。',
    'catdeleted'         => 'カテゴリーが削除されました。',
    'catedited'          => 'カテゴリーが更新されました。',
    'forumadded'         => '掲示板が追加されました。',
    'forumaddError'      => '掲示板追加エラー',
    'forumdeleted'       => '掲示板が削除されました。',
    'forumedited'        => '掲示板が更新されました。',
    'forumordered'       => '掲示板の順番を編集',
    'transfer'           => '移動中..',
    'vieworder'          => 'View Order',
    'back'               => '戻る',
    'addnote'            => '注意: これらの変数を編集できます。',
    'editnote'           => '掲示板の詳細を編集: ',
    'editforumnote'      => '編集: <b>"%s"</b>',
    'deleteforumnote1'   => '<b>"%s"</b>&nbsp;削除してもよろしいですか ?',
    'editcatnote'        => '編集: <b>"%s"</b>',
    'deletecatnote1'     => '<b>"%s"</b>&nbsp;削除してもよろしいですか ?',
    'deletecatnote2'     => 'このカテゴリーのすべての掲示板とトピックも削除されます。',
    'undercat'           => 'カテゴリー:',
    'deleteforumnote2'   => 'その直下の投稿はすべて削除されます。',
    'groupaccess'        => 'グループ: ',
    'rebuild'            => '最新の投稿テーブルを修正',
    'action'             => 'アクション',
    'forumdescription'   => '掲示板の説明',
    'posts'              => '投稿数',
    'ordertitle'         => '順番',
    'ModDel'             => '削除',
    'ModEdit'            => '編集',
    'ModMove'            => '移動',
    'ModStick'           => '注目',
    'ModBan'             => '禁止',
    'addmoderator'       => "モデレータを追加",
    'delmoderator'       => "選択したモデレータを削除\n",
    'moderatorwarning'   => '<b>注意: 掲示板がみつかりません。</b><br><br>モデレータ追加の前に，掲示板カテゴリを作成して少なくとも掲示板を1つ作成してください。',
    'private'           => 'プライベート掲示板',
    'filtertitle'       => 'モデレータ情報表示',
    'addmessage'        => '新しいモデレータを追加',
    'allowedfunctions'  => '許可されている権限',
    'userrecords'       => 'ユーザレコード',
    'grouprecords'      => 'グループレコード',
    'filterview'        => 'フィルタービュー',
    'readonly'           => '閲覧掲示板',
    'readonlydscp'       => 'モデレータだけが投稿できる掲示板',
    'hidden'             => '隠された掲示板',
    'hiddendscp'         => '掲示板の目次を隠す',
    'hideposts'          => '新規投稿を隠す',
    'hidepostsdscp'      => '新規投稿ブロックとRSSフィードから投稿を隠す'

);

$LANG_GF94 = array (
    'mod_title'          => 'モデレータ',
    'createmod'          => 'モデレータ作成',
    'deletemod'          => 'モデレータ削除',
    'currentmods'        => '今のモデレータ:',
    'moderates'          => 'モデレート',
    'deletemsg'          => '(注意: このボタンをクリックするとすぐ削除されます。)',
    'username'           => 'ユーザ名:',
    'forforum'           => '掲示板用:',
    'modper'             => 'アクセス権限:',
    'candelete'          => '削除可能:',
    'canban'             => '禁止可能:',
    'canedit'            => '編集可能:',
    'canmove'            => '移動可能:',
    'canstick'           => 'Sticky作成:',
    'addsuc'             => 'モデレータ情報を追加しました。',
    'editsuc'            => 'モデレータ情報を変更しました。',
    'removesuc'          => 'モデレータ削除: ',
    'removesuc2'         => 'モデレータをすべての掲示板から削除されました。',
    'modexists'          => 'モデレータは存在します。',
    'modexistsmsg'       => 'エラー: このモデレータは既に登録済みです。',
    'transfer'           => '...',
    'removemodnote1'     => '次の掲示板のモデレータを解任しますか？ %s 掲示板：%s',
    'removemodnote2'     => '一度解任すると，その掲示板を管理できません。',
    'removemodnote3'     => 'すべての掲示板からこのモデレータを解任しますか？ %s',
    'removemodnote4'     => '一度解任すると，どの掲示板のモデレータにもなれません。',
    'allforums'          => 'すべての掲示板'
);


$LANG_GF95 = array (
    'header1'           => '投稿されたトピックを議論しましょう。',
    'header2'           => '投稿されたトピックの議論&nbsp;&raquo;&nbsp;%s',
    'notyet'            => 'この機能はまだ実装されていません。',
    'delall'            => 'すべて削除',
    'delallmsg'         => 'すべてのメッセージを削除しますか？: %s?',
    'underforum'        => '<b> %s (ID #%s)',
    'moderate'          => 'モデレートする',
    'nomess'            => 'まだメッセージは投稿されていません。'
);

$LANG_GF96 = array (
    'gfipman'            => '禁止IPアドレス',
    'ban'                => '禁止',
    'noips'              => '<p style="margin: 0px; padding: 5px;">禁止されているIPアドレスはありません!</p>',
    'unban'              => '禁止取消',
    'ipbanned'           => '禁止IPアドレス',
    'banip'              => '禁止IPアドレス確定',
    'banipmsg'           => '禁止したいですか？IP %s?',
    'specip'             => '禁止IP アドレスを指定してください!',
    'ipunbanned'         => '禁止は解除されました。'
);

// IM.php
$LANG_GF97 = array (
    'msgsent'            => 'メッセージは送られました!',
    'msgsave'            => 'あなたの %s へのメッセージは送られました。',
    'msgreturn'          => 'あなたのボックスへ',
    'msgerror'           => 'メッセージは送られませんでした。<a href="javascript:history.back()">戻る</a>をクリックしてすべてのフィールドを埋めてください。',
    'msgdelok'           => '削除されました。',
    'msgdelsuccess'      => 'メッセージが削除されました。',
    'msgdelerr'          => 'メッセージは削除されていません。<a href=\"javascript:history.back()\">戻る</a>をクリックしてひとつ選んでください。',
    'msgpriv'            => 'プライベートメッセージ',
    'msgprivnote1'       => ' %s プライベートメッセージがあります',
    'msgprivnote2'       => ' %s プライベートメッセージが複数あります',
    'msgto'              => 'ユーザ名へ:',
    'msgmembers'         => 'メンバーリスト：'
);


$PLG_forum_MESSAGE1 = '掲示板プラグインのアップグレードに成功しました';
$PLG_forum_MESSAGE5 = '掲示板プラグインのアップグレードに失敗しました。エラーログ(error.log)をご覧ください。';

?>