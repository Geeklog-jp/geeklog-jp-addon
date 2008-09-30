#!/usr/bin/php
<?php

//=============================================================================
// make_pi.php
//=============================================================================
// This is a PHP script that will replace {plugin}, {display_name},
// {conf_var_prefix}, {lang_var_postfix}, {author_name}, and {author_email}
// with the names of your plugin, config var array, lang var array,
// your name , and your email address in all the files
// in the Universal Plugin Kit.
// This script is based on the perl script written by Tony Williams.
//
// Copyright (C) 2002 Tony Williams
// tonyw@honestpuck.com   http://www.honestpuck.com/
//
// Copyright (C) 2006-2008 mystral-kk - geeklog AT mystral-kk DOT net
//
// modified by dengen - dengen AT mail DOT trybase DOT com
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//f
//=============================================================================

if (file_exists('make_pi_config.php')) {
    require_once 'make_pi_config.php';
} else {
    $_UPKIT_CONF['pi_name']          = 'example';
    $_UPKIT_CONF['conf_var_prefix']  = 'EXAMPLE';
    $_UPKIT_CONF['lang_var_postfix'] = 'EXAMPLE';
    $_UPKIT_CONF['display_name']     = 'Example';
    $_UPKIT_CONF['author_name']      = 'Author Name';
    $_UPKIT_CONF['author_email']     = 'author AT example DOT jp';
    $_UPKIT_CONF['pi_url']           = 'http://www.example.com/';
    $_UPKIT_CONF['gl_version']       = '1.5.0';
    $_UPKIT_CONF['move_files']       = false;
    $_UPKIT_CONF['path_html']        = './public_html/';
}

if (empty($_UPKIT_CONF['plugin_template'])) {
    $_UPKIT_CONF['plugin_template'] = 'plugin_1.5.0';
}

$lib_exists = file_exists($_UPKIT_CONF['path_html'] . 'lib-common.php');
if ($_UPKIT_CONF['move_files'] && $lib_exists) {
    require_once $_UPKIT_CONF['path_html'] . 'lib-common.php';
}

function file_write_contents($path, $contents)
{
    $fp = @fopen($path, "wb");
    if ($fp === false) {
        echo "Cannot open file: $path\n";
        return false;
    }

    $retval = fwrite($fp, $contents);
    if ($retval === false) {
        echo "Cannot write to file: $path\n";
        @fclose($fp);
    } else {
        $retval = fclose($fp);
        if ($retval === false) {
            echo "Cannot close file: $path\n";
        }
    }

    return $retval;
}

function copyDir($srcdir, $dstdir)
{
    if ( !is_dir($dstdir) ) mkdir($dstdir);
    if (!($curdir = @opendir($srcdir))) return;
    while ($file = readdir($curdir)) {
        if($file=='.' || $file=='..') continue;
        $srcfile = $srcdir . '/' . $file;
        $dstfile = $dstdir . '/' . $file;
        if (is_file($srcfile)) {
            if (copy($srcfile, $dstfile)) {
                touch($dstfile, filemtime($srcfile));
            }
        } else if (is_dir($srcfile)) {
            copyDir($srcfile, $dstfile);
        }
    }
    closedir($curdir);
}

function removeDir($dir)
{
    if (!($curdir = @opendir($dir))) return;
    while ($file = readdir($curdir)) {
        if ($file=='.' || $file=='..') continue;
        $file = $dir . '/' . $file;
        if (!@unlink($file)) removeDir($file);
    }
    closedir($curdir);
    @rmdir($dir);
}

function listFiles($dir)
{
    $retval = array();
    if (!($curdir = @opendir($dir))) return;
    while ($file = readdir($curdir)) {
        if($file=='.' || $file=='..') continue;
        $srcfile = $dir . '/' . $file;
        if (is_file($srcfile)) {
            $retval[] = $srcfile;
        } else if (is_dir($srcfile)) {
            $retval = array_merge($retval, listFiles($srcfile));
        }
    }
    closedir($curdir);

    return $retval;
}

/**
*  MAIN
*/

/*
// make_pi_config.php was introduced to have the setting of a more detailed condition.
// With this, the function of the setting of the condition by the command line parameter
// canceled support.

// Checks PHP version.  Prior to 4.1.0, the $_SERVER array doesn't exist
$v = explode( '.', phpversion() );
$phpv = 10000 * $v[0] + 100 * $v[1] + $v[2];
if ( $phpv < 40100 ) {
    $_SERVER = $HTTP_SERVER_VARS;
}

// If no argument is supplied, then bail
if ( count( $_SERVER['argv'] ) == 1 ) {
    die( "USAGE for MS-Windows users:\n"
       . "php.exe -f make_pi plugin_name [config_var_prefix [lang_var_postfix [display_name]]]\n"
       . "e.g.  c:\\php\\php.exe -f make_pi.php sample SMP SMP Sample\n"
       . "When lang_var_postfix is omitted, config_var_prefix is assumed.\n"
       . "When config_var_prefix is omitted, plugin_name is adopted as config_var_prefix.\n"
       . "When display_name is omitted, plugin_name is adopted as display_name.\n"
    );
}

array_shift( $_SERVER['argv'] );    // Drop "make_pi.php"
$_UPKIT_CONF['pi_name']          = strtolower( array_shift( $_SERVER['argv'] ) );
$_UPKIT_CONF['conf_var_prefix']  = array_shift( $_SERVER['argv'] );
$_UPKIT_CONF['lang_var_postfix'] = array_shift( $_SERVER['argv'] );
$_UPKIT_CONF['display_name']     = array_shift( $_SERVER['argv'] );
*/

$_UPKIT_CONF['pi_name'] = strtolower( $_UPKIT_CONF['pi_name'] );

if ( empty( $_UPKIT_CONF['conf_var_prefix'] ) ) {
    $_UPKIT_CONF['conf_var_prefix'] = $_UPKIT_CONF['pi_name'];
}
$_UPKIT_CONF['conf_var_prefix'] = strtoupper( $_UPKIT_CONF['conf_var_prefix'] );

if ( empty( $_UPKIT_CONF['lang_var_postfix'] ) ) {
    $_UPKIT_CONF['lang_var_postfix'] = $_UPKIT_CONF['conf_var_prefix'];
}
$_UPKIT_CONF['lang_var_postfix'] = strtoupper( $_UPKIT_CONF['lang_var_postfix'] );

if ( empty( $_UPKIT_CONF['display_name'] ) ) {
    $_UPKIT_CONF['display_name'] = ucfirst( $_UPKIT_CONF['pi_name'] );
}

$pattern = array(
    "/\{plugin\}/i",
    "/\{conf_var_prefix\}/i",
    "/\{lang_var_postfix\}/i",
    "/\{display_name\}/i",
    "/\{author_name\}/i",
    "/\{author_email\}/i",
    "/\{pi_url\}/i",
    "/\{gl_version\}/i"
);

$replace = array (
    $_UPKIT_CONF['pi_name'],
    $_UPKIT_CONF['conf_var_prefix'],
    $_UPKIT_CONF['lang_var_postfix'],
    $_UPKIT_CONF['display_name'],
    $_UPKIT_CONF['author_name'],
    $_UPKIT_CONF['author_email'],
    $_UPKIT_CONF['pi_url'],
    $_UPKIT_CONF['gl_version']
);

echo "Replacing \"{plugin}\" with \"{$_UPKIT_CONF['pi_name']}\"\n";
echo "Replacing \"{conf_var_prefix}\" with \"{$_UPKIT_CONF['conf_var_prefix']}\"\n";
echo "Replacing \"{lang_var_postfix}\" with \"{$_UPKIT_CONF['lang_var_postfix']}\"\n";
echo "Replacing \"{display_name}\" with \"{$_UPKIT_CONF['display_name']}\"\n";
echo "Replacing \"{author_name}\" with \"{$_UPKIT_CONF['author_name']}\"\n";
echo "Replacing \"{author_email}\" with \"{$_UPKIT_CONF['author_email']}\"\n";
echo "Replacing \"{gl_version}\" with \"{$_UPKIT_CONF['gl_version']}\"\n";
echo "Replacing \"{pi_url}\" with \"{$_UPKIT_CONF['pi_url']}\"\n";


copyDir($_UPKIT_CONF['plugin_template'], $_UPKIT_CONF['pi_name']);
unlink($_UPKIT_CONF['pi_name'] . '/functions-advanced.inc'); // Nonsupport

// Files to be processed

$FILES = listFiles($_UPKIT_CONF['pi_name']);

// Start procesccing input files

foreach ($FILES as $path) {
    echo "Processing $path\n";

    $contents = file_get_contents($path);
    if ($contents === false) {
        echo "Cannot open file: $path\n";
        continue;
    }

    $contents = preg_replace( $pattern, $replace, $contents );
    file_write_contents( $path, $contents );
}

echo "Finished replacing place-holders.\n";

// Renames the name of the icon file in public_html/images/plugin.gif

echo "Renaming the icon name ... ";

$result = @rename($_UPKIT_CONF['pi_name'] . '/public_html/images/plugin.gif',
                  $_UPKIT_CONF['pi_name'] . "/public_html/images/{$_UPKIT_CONF['pi_name']}.gif");

echo $result ? "succeeded" : "failed";

// Move files

if ($_UPKIT_CONF['move_files'] && $lib_exists) {
    copyDir($_UPKIT_CONF['pi_name'] . '/admin',
            $_CONF['path_html'] . 'admin/plugins/' . $_UPKIT_CONF['pi_name']);
    removeDir($_UPKIT_CONF['pi_name'] . '/admin');

    copyDir($_UPKIT_CONF['pi_name'] . '/public_html',
            $_CONF['path_html'] . $_UPKIT_CONF['pi_name']);
    removeDir($_UPKIT_CONF['pi_name'] . '/public_html');

    copyDir($_UPKIT_CONF['pi_name'],
            $_CONF['path'] . '/plugins/' . $_UPKIT_CONF['pi_name']);
    removeDir($_UPKIT_CONF['pi_name']);
}

echo "\nCompleted.\n";

exit;

?>