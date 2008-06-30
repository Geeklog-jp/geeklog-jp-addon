<?php
// +---------------------------------------------------------------------------+
// | 日本語Pingサイトを追加する                                                |
// |                                                                           |
// +---------------------------------------------------------------------------+
// $Id: sql_japanize3.php
// もし万一エンコードの種類が  utf-8でない場合は、utf-8に変換してください。
// 最終更新日　2007/06/29 tsuchi AT geeklog DOT jp

//http://www.blogpeople.net/
$_SQL[] = "
    DELETE FROM {$_TABLES['pingservice']} Where 
    site_url = 'http://www.blogpeople.net/'
    ";
$_SQL[] = "
    INSERT INTO {$_TABLES['pingservice']} 
    (pid, name, site_url, ping_url, method, is_enabled)
    VALUES (NULL, 'BlogPeople',  'http://www.blogpeople.net/'
    ,'http://www.blogpeople.net/servlet/weblogUpdates', 'weblogUpdates.ping', 1)
    ";
//http://ping.bloggers.jp/
$_SQL[] = "
    DELETE FROM {$_TABLES['pingservice']} Where
    site_url = 'http://ping.bloggers.jp/'
    ";
$_SQL[] = "
    INSERT INTO {$_TABLES['pingservice']} 
    (pid, name, site_url, ping_url, method, is_enabled)
    VALUES (NULL, 'ping.bloggers.jp', 'http://ping.bloggers.jp/'
    , 'http://ping.bloggers.jp/rpc/', 'weblogUpdates.ping', 1)
    ";
//http://ping.rss.drecom.jp/
$_SQL[] = "
    DELETE FROM {$_TABLES['pingservice']} Where
    site_url = 'http://www.myblog.jp/'
    ";
$_SQL[] = "
    INSERT INTO {$_TABLES['pingservice']} 
    (pid, name, site_url, ping_url, method, is_enabled)
    VALUES (NULL, 'ドリコムRSS', 'http://www.myblog.jp/'
    , 'http://ping.rss.drecom.jp/', 'weblogUpdates.ping', 1)
    ";
//http://blog.goo.ne.jp/
$_SQL[] = "
    DELETE FROM {$_TABLES['pingservice']} Where
    site_url = 'http://blog.goo.ne.jp/'
    ";
$_SQL[] = "
    INSERT INTO {$_TABLES['pingservice']} 
    (pid, name, site_url, ping_url, method, is_enabled)
    VALUES (NULL, 'ｇoo　ブログ', 'http://blog.goo.ne.jp/'
    , 'http://blog.goo.ne.jp/XMLRPC', 'weblogUpdates.ping', 1)
    ";

?>
