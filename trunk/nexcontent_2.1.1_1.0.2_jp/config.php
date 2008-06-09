<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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

$_TABLES['nexcontent']               = $_DB_table_prefix . 'nxcontent';
$_TABLES['nexcontent_pages']         = $_DB_table_prefix . 'nxcontent_pages';
$_TABLES['nexcontent_images']        = $_DB_table_prefix . 'nxcontent_images';

$CONF_SE['debug'] = false;
$CONF_SE['version'] = '2.1.1';

/* Setup Paths and URL's */
$CONF_SE['post_url']     = $_CONF['site_url'] .'/nc/index.php';
$CONF_SE['public_url']   = $_CONF['site_url'] .'/nc';
$CONF_SE['uploadpath']   = $_CONF['path_html'] . 'nc/images/';

// Relative Directory to $_CONF['site_url'] where the Editor Image Library store
$CONF_SE['imagelibrary']  = '/nc/library';

// +---------------------------------------------------------------------------+
// | Set defaults values for Pages served by nexcontent                        |
// | Default values will be used if set. Settings can be defined per page      |
// | in the editor.                                                            |
// +---------------------------------------------------------------------------+

/* Default Page Title if set to use for all pages */
/* Will override the page title set by Geeklog for any nexcontent pages if set */
$CONF_SE['pagetitle']  = 'Nextide Solutions';

/* Default value if set to use for all page META Tag "Description" value */
/* Can be 200 - 250 words */
$CONF_SE['meta_description']  = '';

/* Default value to be used for page META Tag "Keywords" value*/
/* List of comma separated keywords - 20 to 25 words is normal
/* Note: Not many search engines use this tag anymore or place much empahsis on it */
$CONF_SE['meta_keywords']  = '';

/* NOT USED YET: Default Page favicon */
$CONF_SE['favicon'] = $CONF_SE['public_url'] .'/images/favion.ico';


// +---------------------------------------------------------------------------+
// | Miscelaneous settings                                                     |
// +---------------------------------------------------------------------------+

// Permissions that will be used for directories used to store uploaded images.
$CONF_SE['imagedir_perms'] = (int) 0755;

// Permissions that will be used for uploaded images.
$CONF_SE['image_perms'] = (int) 0755;

/* Number of images that can be uploaded at one time to a content category page */
$CONF_SE['max_num_images'] = 10;

$CONF_SE['convert_tool'] = 'gd';

/* Enable Breadcrumbs */
$CONF_SE['breadcrumbs'] = true;

/* Character to be used in the breadcrumb URL listing as the separator */
$CONF_SE['breadcrumb_separator'] = '>&nbsp;';


$CONF_SE['max_uploadfile_size'] = 2048000;

$CONF_SE['max_upload_width'] = 370;
$CONF_SE['max_upload_height'] = 300;
$CONF_SE['image_quality'] = 90;
$CONF_SE['auto_thumbnail_dimension'] = 75;
$CONF_SE['auto_thumbnail_resize_type'] = 1;
$CONF_SE['auto_thumbnail_quality'] = 90;

// +---------------------------------------------------------------------------+
// | Set to false if this plugin should not load the Javascript for the        |
// | HTML_TreeMenu - if another plugin is already loading this JS Library      |
// +---------------------------------------------------------------------------+
$CONF_SE['load_HTMLTree'] = false;

// +---------------------------------------------------------------------------+
// | Set to false if this plugin should not load the Javascript for the        |
// | FCK Editor - The Rich Text Editor Component                               |
// +---------------------------------------------------------------------------+
$CONF_SE['load_FCKEditor'] = false;

$CONF_SE['menuoptions'] = array (
    0   => 'None',
    1   => 'Header Menu',
    2   => 'Block Menu',
    3   => 'Same as Parent',
    4   => 'New Block',
    5   => 'Single Block'
);

$CONF_SE['loadImageUploader']    = false;   //should users be able to upload images from nexcontent or use only the FCK uploader
$CONF_SE['allowableImageTypes']    = array(
    'image/bmp'     => '.bmp, .ico',
    'image/gif'     => '.gif',
    'image/pjpeg'   => '.jpg, .jpeg',
    'image/jpeg'    => '.jpg, .jpeg',
    'image/png'     => '.png'
);


?>
