<?php

/* Reminder: always indent with 4 spaces (no tabs). */

//
// $Id: information.php

define ('THIS_SCRIPT', 'information.php');
define ('THIS_PLUGIN', 'japanize');

include_once('japanize_functions.php');


$display = '';
$display .= COM_siteHeader ('menu', $LANG_JPN['pinameadmin']);
if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), 'japanize');
}
$display.=ppNavbar($navbarMenu,$LANG_JPN_admin_menu['3']);

//
$T = new Template($_CONF['path'] . 'plugins/japanize/templates/admin');
$T->set_file ('admin','information.thtml');

$T->parse('output', 'admin');
$display.= $T->finish($T->get_var('output'));
//
$display.= COM_siteFooter ();

echo $display;

?>
