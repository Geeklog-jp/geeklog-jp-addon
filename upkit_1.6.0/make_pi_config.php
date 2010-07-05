<?php

//=============================================================================
// make_pi_config.php
//=============================================================================

$_UPKIT_CONF = array();

$_UPKIT_CONF['pi_name']          = 'example';                  // Plugin name
$_UPKIT_CONF['display_name']     = 'Example';                  // Plugin display name
$_UPKIT_CONF['conf_var_prefix']  = 'EXAMPLE';                  // Prefix of config var array
$_UPKIT_CONF['lang_var_postfix'] = 'EXAMPLE';                  // Postfix of lang var array
$_UPKIT_CONF['author_name']      = 'Author Name';              // Plugin author's Name
$_UPKIT_CONF['author_email']     = 'author AT example DOT jp'; // Plugin author's e-mail address
$_UPKIT_CONF['pi_url']           = 'http://www.example.com/';  // Plugin Homepage
$_UPKIT_CONF['gl_version']       = '1.6.0';                    // GL Version plugin for

// By setting the following options in 'true', this script moves the files of
// your plugin to the position that there should be.
$_UPKIT_CONF['move_files']       = false;

// This should point to the directory where your lib-common.php file resides.
$_UPKIT_CONF['path_html']        = '/path/to/public_html/';    // should end in a slash

?>