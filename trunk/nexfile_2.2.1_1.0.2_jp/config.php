<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
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

// Simply fill in the names of the your tables to have them included in the $_TABLES array
// set Plugin Table Prefix the Same as Geeklogs
// Add Forum Plugin tables to $_TABLES array

$_TABLES['fm_access']      = $_DB_table_prefix .'nxfile_access';
$_TABLES['fm_category']    = $_DB_table_prefix .'nxfile_categories';
$_TABLES['fm_files']       = $_DB_table_prefix .'nxfile_files';
$_TABLES['fm_detail']      = $_DB_table_prefix .'nxfile_filedetail';
$_TABLES['fm_versions']    = $_DB_table_prefix .'nxfile_fileversions';
$_TABLES['fm_notify']      = $_DB_table_prefix .'nxfile_notifications';
$_TABLES['fm_submissions'] = $_DB_table_prefix .'nxfile_filesubmissions';
$_TABLES['auditlog']       = $_DB_table_prefix . 'auditlog';

// Permissions used when category directories are auto created and files uploaded
define('FM_CHMOD_FILES', 0666);
define('FM_CHMOD_DIRS', 0777);
$_FMCONF['version'] = '2.2.1';          // Plugin Version
$_FMCONF['install_sql_file'] = 'nexfile_sql_install_'.$_FMCONF['version'].'.php';

// +---------------------------------------------------------------------------+
// | Set to false if this plugin should not load the Javascript for the        |
// | HTML_TreeMenu - if another plugin is already loading this JS Library      |
// +---------------------------------------------------------------------------+
$_FMCONF['load_HTMLTree'] = false;

$_FMCONF['useraccess']     = '1';       // 0: required to be member of nexfile.user, 1: logged in members group and 2: Public Access
$_FMCONF['debug']          = false;      // Set to true to see POST and GET VARS
$_FMCONF['msgdelay']       = 2000;      // Delay in milliseconds of status message, 5000 = 5 sec. Set to 0 to have it pause.
$_FMCONF['def_catimage']   = true;      // If no file image, show the Category image if it exists else icon image
$_FMCONF['def_view']       = 'latestfiles';      // Option: latestfiles or ''

// Defaut nexfile permissions - Set access rights for a new category to a GL GROUP
$_FMCONF['defCatGroup'] = '2';        // Group 2 is all-users (logged in or not)
// Define the default goup permissions from this list: 'view','upload','upload_direct','upload_ver','approval','admin'
$_FMCONF['defCatGroupRights'] = array('view','upload','upload_ver');

// Default Permissions to give owner when creating a new category
$_FMCONF['defOwnerRights'] = array('view','upload','upload_direct','upload_ver','approval','admin');

// Settings for Scottpaper to restrict view of Categories shown for marketing group
$_FM_CONF['restrictedAccessGroup'] = 'Marketing';
$_FM_CONF['restrictedAccessCategories'] = array('1','6','11');

// Set to true if remote URL for uploaded files should be checked at upload if it can be accessed
$_FMCONF['verifyRemoteURL'] = true;

/* If set this date format will be used else the user set preference will be */
$_FMCONF['dateformat'] = '%a %d %b %I:%M%p';

$_FMCONF['inconlib']    = array( 
        'php'  => "php.gif",
        'phps' => "php.gif",
        'bmp'  => "bmp.gif",
        'gif'  => "gif.gif",
        'jpg'  => "jpg.gif",
        'html' => "htm.gif",
        'htm'  => "htm.gif",
        'mov'  => "mov.gif",
        'mp3'  => "mp3.gif",
        'pdf'  => "pdf.gif",
        'ppt'  => "ppt.gif",
        'mht'  => "ppt.gif",
        'tar'  => "zip.gif",
        'gz'   => "zip.gif",
        'zip'  => "zip.gif",
        'txt'  => "txt.gif",
        'doc'  => "doc.gif",
        'xls'  => "xls.gif",
        'mpp'  => "mpp.gif",
        'exe'  => "exe.gif",
        'swf'  => "swf.gif",
        'vsd'  => "visio.gif",
        'none' => "none.gif"
        );

$_FMCONF['imagesurl']        = $_CONF['layout_url'] .'/nexfile/images/';

$_FMCONF['allowablefiletypes']    = array(
        'application/x-gzip-compressed'     => '.tar.gz, .tgz',
        'application/x-zip-compressed'      => '.zip',
        'application/x-tar'                 => '.tar',
        'text/plain'                        => '.php, .txt, .inc (etc)',
        'text/html'                         => '.html, .htm (etc)',
        'image/bmp'                         => '.bmp, .ico',
        'image/gif'                         => '.gif',
        'image/pjpeg'                       => '.jpg, .jpeg',
        'image/jpeg'                        => '.jpg, .jpeg',
        'image/png'                         => '.png',
        'message/rfc822'                    => '.mht',
        'image/x-png'                       => '.png',
        'audio/mpeg'                        => '.mp3 etc',
        'audio/wav'                         => '.wav',
        'application/pdf'                   => '.pdf',
        'application/x-shockwave-flash'     => '.swf',
        'application/msword'                => '.doc',
        'application/vnd.ms-excel'          => '.xls',
        'application/vnd.ms-powerpoint'     => '.ppt',
        'application/vnd.ms-project'        => '.mpp',
        'text/plain'                        => '.txt',
        'application/x-pangaeacadsolutions' => '.dwg',
        'application/vnd.visio'             => '.vsd',
        'application/octet-stream'          => '.vsd, .exe, .fla, .psd (etc)'
        );


$_FMCONF['downloadfiletypes'] =  array(
        'tgz' => 'application/x-gzip-compressed',
        'gz' =>  'application/x-gzip-compressed',
        'vsd' => 'application/x-gzip-compressed',
        'zip' => 'application/x-zip-compresseed',
        'tar' => 'application/x-tar',
        'php' => 'text/plain',
        'phps' => 'text/plain',
        'txt' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'mht' => 'text/html',
        'bmp' => 'image/bmp',
        'ico' => 'image/bmp',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/x-png',
        'mpp' => 'application/vnd.ms-project',
        'dwg' => 'application/x-pangaeacadsolutions',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'ppt' => 'application/powerpoint',
        'doc' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'vsd' => 'application/octet-stream',
        'exe' => 'application/octet-stream'
        );

$_FMCONF['allowableimagetypes']    = array(
        'image/gif'=>'.gif',
        'image/bmp'=>'.bmp',
        'image/jpeg'=>'.jpg,.jpeg',
        'image/pjpeg'=>'.jpg,.jpeg',
        'image/x-png'=>'.png',
        'image/png'=>'.png'
        );

// Max settings for thumbnails - both category and upload file thumbnails if used
$_FMCONF['max_thumbnail_width']      = '140';
$_FMCONF['max_thumbnail_height']     = '140';
$_FMCONF['max_thumbnail_size']       = '65536';     // 65536 = 64KB

// Max settings for uploaded files - possible that images may be uploaded as files 
$_FMCONF['max_uploadimage_width']    = '2100';
$_FMCONF['max_uploadimage_height']   = '1600';
$_FMCONF['max_uploadfile_size']      = '6553600';     // 6.400 MB

$_FMCONF['shownewlimit']             = '10';

// Used when assigning Permissions to category. Excludes Core GL Groups and ones listed in $_FMCONF['excludeGroups']
$_FMCONF['excludeGroups'] = "'nexfile Admin','forum Admin', 'messenger Admin','faqman Admin', 'Static Page Admin'";

?>