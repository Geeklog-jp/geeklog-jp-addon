<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | preview.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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
//

require_once("../../../lib-common.php"); // Path to your lib-common.php
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

$myvars = array('id','linkedforms');
ppGetData($myvars,true);

/* Show Form - Preview */

$retval = COM_siteHeader();
$LANG_NAVBAR = $LANG_FRM_ADMIN_NAVBAR;
$navbar = new navbar();
$navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
$navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$id);
$navbar->add_menuitem($LANG_NAVBAR['4'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=displayfields&id='.$id);
$navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?&formid='.$id);
$navbar->add_menuitem($LANG_NAVBAR['7'], $_CONF['site_admin_url'] .'/plugins/nexform/preview.php?id='.$id);
$navbar->set_selected($LANG_NAVBAR['7']);
$retval .= $navbar->generate();

// $linkedforms: used to optionally only show pre linked or post linked forms. Valid values are: all, none, beforeonly, afternonly
$retval .= nexform_showform($id,0,'view','',$linkedforms);
$retval .= COM_siteFooter();
echo $retval;

?>