<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

$LANG_SE00 = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'headermenu'        => '',
    'useradmintitle'    => '',
    'adminmenutitle'    => 'Content Management',
    'adminmenupanel'    => 'Content<br>Management',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'             => 'Plugin Admin',
    'searchlabel'       => 'nexContent',
    'searchresults'     => 'Site Content Search Results',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin is now installed.<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide Support</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'Nextide Content Management Plugin installation was successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'newsletter Plugin De-Install Warning'
);

/* Plugin Messages - used by COM_showMessage */
$PLG_nexcontent_MESSAGE1 = 'Invalid access to Page';
$PLG_nexcontent_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexcontent_MESSAGE11 = 'nexContent Plugin Upgrade completed - no errors';
$PLG_nexcontent_MESSAGE12 = 'nexContent Plugin Upgrade failed - check error.log';
$PLG_nexcontent_MESSAGE99 = 'Error during site login, try logging in again';         // Used if a custom login handler being used



$LANG_SE01 = array (
    'LANG_CANCEL'           => 'Cancel',
    'LANG_UPDATE'           => 'Update Record',
    'LANG_ADD'              => 'Add Record',
    'LANG_OPTIONS'          => 'Options',
    'LANG_HEADING1'         => 'Menu Structure',
    'LANG_HEADING2'         => 'Menu Item Detail',
    'LANG_HELPMSG1'         => 'Add a new Menu Item. Choose "Top Level Menu" to make this a main menu item. It can then be made a submenu for other menu items to be assigned to it. Submenu\'s do not have a URL assigned.',
    'LANG_HELPMSG2'         => 'Edit this Menu Item. Choose "Top Level Menu" to make this a main menu item. If this is a Submenu, it will not have a URL assigned.',
    'LANG_MenuItem'         => 'Menu Item Label',
    'LANG_ParentMenu'       => 'Parent Menu',
    'LANG_ORDER'            => 'Order',
    'LANG_Enabled'          => 'Enabled',
    'LANG_Submenu'          => 'Submenu',
    'LANG_URLITEM'          => 'URL for Menu Item',
    'LANG_EditRecord'       => 'Edit Record',
    'LANG_DeleteRecord'     => 'Delete Record',
    'LANG_DELCONFIRM'       => 'Please confirm that you want to delete this record',
    'LANG_TopLevelMenu'     => 'Top Level Menu',
    'LANG_SubNoneReq'       => ' Submenu -- none required',
    'LANG_ACCESS'           => 'Access Rights',
    'SEARCH_PAGE'           => 'Site Page',
    'SEARCH_TYPE'           => 'Page/Category',
    'SEARCH_HITS'           => 'Page Views',
    'TAGHELP'               => 'Example TAGS that can be used'
);

/* Admin Navbar Setup */
$LANG_SE02 = array (
    1   => 'Category Listing',
    2   => 'Edit Category',
    3   => 'New Category',
    4   => 'New Page',
    5   => 'New Link',
	6	=> 'Home'
);
$LANG_SE3 = array (
    1   => 'Category Listing',
    2   => 'New Category',
    3   => 'Edit Page',
    4   => 'New Page',
    5   => 'Edit Category'
);

/* Edit Page Menu */
$LANG_SE03 = array (
    1   => 'Site Sections Index',
    2   => 'Edit Category Details',
    3   => 'Edit Category Images',
    4   => 'Edit Category Security'
);

/* Edit Page Menu */
$LANG_SE04 = array (
    1   => 'Site Page Index',
    2   => 'Edit Page Details',
    3   => 'Edit Page Images',
    4   => 'Edit Page Security'
);

$LANG_SE05 = array (
    '[block_<b>blockname</b>_left]'  => 'Show the block <b>blockname</b> on the left side. Example: [block_section_block_left]. Multiple blocks can be specified for both the left and right side.<br><b>Custom Block Mode only</b>',
    '[block_<b>blockname</b>_right]'  => 'Show the block <b>blockname</b> on the right side. Example: [block_poll_block_right]<br><b>Custom Block Mode only</b>.',
    '[categorymenu_left: <b>category id</b>]'  => 'Override the default category block menu feauture and specify a the block menu for a specific category. Can be optionally left or right. Examples: [categorymenu_right: 18] or [categorymenu_left: 1]<br><b>Custom Block Mode only</b>',
    '[centerblock:<b>blockname</b>]'  => 'Able to include one block to appear across the center at the top of the page<br>Example: [centerblock:plugin_centerblock_forum]',
    '[footerblock:<b>blockname</b>]'  => 'Able to include one block to appear across the bottom of the page<br>Example: [footerblock:plugin_centerblock_staticpages]',
    '[break]'   => 'Add a column break to your page',
    '[image1]'  => 'Show image 1 with default positioning',
    '[image1_center]' => 'Show image 1 in the center of the page and do not wrap text around image',      
    '[image1_left]'   => 'Float image 1 to the left side of the page and wrap text around image',
    '[image2_right]'  => 'Float image 1 to the right side of the page and wrap text around image',
    '[pageindex:<b>category id, limit</b>]'  => 'Display a page index (list of page links) for the page  contents of the specified category. You can optionally pass a numeric limit of the number of pages to show in the index. You need to pass in the category id or optional keywords <i>parent</i> or <i>current</i>.<br>Examples: [pageindex: 28,10], [pageindex: parent], [pageindex: current,5]',
    '[showblock: blockname]' => 'Display a block who\'s name is passed. This tag can be used anywhere on the page with or without using the customblocks mode for the page. This allows you to place block(s) even in the middle of the page',
    '[phpfunction:<b>name</b> parm1,parm2]'  => 'Allows you to call a custom php function or execute php code on the page. Supports passing any number of parameters (0,1,2,3 ...) to function <b>name</b>.<br>[phpfunction:timeofday] or [phpfunction:stockprice ibm,dell,hpq]',
    '[forum:102 <b>here</b>]'  => 'Create a link to the forum topic id # 102 with a link description of <b>here</b>. The link name can be multiple words.  Example: [forum:2451 How to upload files]',
    '[file:12 here]'  => 'Create a link to a file in your repository where the number is the file id and the link name can be one of multiple words'
);

$LANG_SE_ERR  = array(
    'upload1'       => 'File Upload Errors'

);

?>