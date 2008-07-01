<?php
// +---------------------------------------------------------------------------+
// | テーブル構造とデータを日本語版化する                                      |
// |  SQL文データ1　テーブル構造とシステム用データ                             |
// +---------------------------------------------------------------------------+
// $Id: sql_japanize1.php
// もし万一エンコードの種類が  utf-8でない場合は、utf-8に変換してください。
// 最終更新日　2007/05/21 tsuchi AT geeklog DOT jp

// (01) en-gb →ja data (syndication)
$_SQL[] = "
    ALTER TABLE {$_TABLES['syndication']} MODIFY 
    language varchar(20) NOT NULL default 'ja'
    ";

$_SQL[] = "
    UPDATE   {$_TABLES['syndication']} SET 
    language = 'ja'
    ";

$_SQL[] = "
    UPDATE   {$_TABLES['syndication']} SET 
    charset = 'UTF-8'
    ";

// (06) username varchar(36) → carchar(108) gl_users
$_SQL[] = "
    ALTER TABLE {$_TABLES['users']} MODIFY username varchar(108) NOT NULL default ''
    ";




?>
