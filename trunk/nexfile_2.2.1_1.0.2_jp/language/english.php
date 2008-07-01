<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
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

$LANG_FM00 = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'headerlabel'       => 'Document Library',
    'searchlabel'       => 'Documents',
    'statslabel'        => 'Total nexfile File Listings',
    'statsheading1'     => 'nexFile Top 10 accessed files',
    'searchresults'     => 'Document Respository Search Results',
    'useradminmenu'     => 'Document Management',
    'adminmenu'         => 'Document Management',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'                => 'Plugin Admin',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin and Block are now installed,<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide Inc.</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'    => 'Installation Successful<p><b>Next Steps</b>: 
        <ol><li>Use the nexFile Admin to configure your new nexFile
        <li>Review nexFile Settings and personalize
        <li>Create at least one nexFile Category</ol>
        <p>Review the <a href="%s">Install Notes</a> for more information.',
        
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'        => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'nexFile De-Install Warning'
);


/* New Navbar Definitions - to replace $LANG_FM01 */
$LANG_FM_NAVBAR = array (
    1       => 'Add File',
    2       => 'Latest Files',
    3       => 'Subscriptions (%s)',
    4       => 'Subscriptions',
    5       => 'File Listing',
    6       => 'Approve (%s)',
    7       => 'Return to Project',
    8       => 'File Detail',
    9       => 'Category Listing',
    10      => 'New Category',
    11      => 'Download File',
    12      => 'Download to Verify File',
    13      => 'Return',
    14      => 'Edit Category',
    15      => 'Edit Permissions',
    16      => 'Notify Me of Changes',
    17      => 'Remove subscription',
    18      => 'Exceptions (%s)',
    19      => 'Exceptions',
    20      => 'Unsubscribe to Changes'
);

$LANG_FM02 = array (
    'YES'                   => 'Yes',
    'NO'                    => 'No',
    'DELETE'                => 'Delete',
    'UPDATE'                => 'Update',
    'CANCEL'                => 'Cancel',
    'RETURN'                => 'Return',
    'DOWNLOAD'              => 'Download',
    'EDIT'                  => 'Edit',
    'APPROVE'               => 'Approve',
    'SIZE'                  => 'Size',
    'MAX'                   => 'Max',
    'LOCAL'                 => 'Local',
    'REMOTE'                => 'Remote',
    'CAT'                   => 'Category',
    'CATEGORY'              => 'Category Name',
    'AUTHOR'                => 'Author',
    'DETAILS'               => 'Details',
    'FILENAME'              => 'File Name',
    'TITLE'                 => 'Title',
    'FILETITLE'             => 'File Title',
    'TEMPNAME'              => 'Temp Name',
    'DATE'                  => 'Date',
    'DATEADDED'             => 'Date Added',
    'BYTES'                 => 'Bytes',
    'ACTION'                => 'Action',
    'SUBMITTER'             => 'Submitter',
    'ALLFILES'              => 'All Files',
    'ADDFILE'               => 'Add New File',
    'SELECT_CATEGORY'       => 'Select Category',
    'DESCRIPTION'           => 'Description',
    'UPLOADFILE'            => 'Upload File',
    'UPLOADURL'             => 'Remote File',
    'THUMBNAIL'             => 'Thumbnail',
    'VERSION_NOTE'          => 'Version Note',
    'ACCESSERROR'           => 'Access Error',
    'EMAIL_NOTIFICATION'    => 'Email Notification',
    'EMAIL_NOTIFICATION2'   => 'Email Notification of Approval',
    'PERMS_GROUP'           => 'Group',
    'PERMS_USER'            => 'User',
    'PERMS_VIEW'            => 'View',
    'PERMS_UPLOAD1'         => 'Upload with<br>Approval',
    'PERMS_UPLOAD2'         => 'Direct<br>Upload',
    'PERMS_UPLOAD3'         => 'Upload<br>Versions',
    'PERMS_UPLOAD4'         => 'Upload<br>Admin',
    'PERMS_ADMIN'           => 'Admin',
    'PERMS_ACTION'          => 'Action',
    'PARENT_CAT'            => 'Parent Category',
    'INHERIT_RIGHTS'        => 'Inherit Parent Access Rights',
    'REF_IMAGE'             => 'Reference Image',
    'CREATE_CAT'            => 'Create Category',
    'TOP_CAT'               => 'Top Level Category',
    'AUTO_NOTIFY'           => 'Auto-create User Notification Records ?'
);

$LANG_nexfile = array (
    'msg01'        => 'Edit Category Details',
    'msg02'        => 'Edit Category Permissions',
    'msg03'        => 'Already Subscribed to this category',
    'msg04'        => 'Subscribe to this category',
    'msg05'        => 'Manage Categories',
    'msg06'        => 'Folder Listing',
    'msg07'        => '<b>Latest  %s&nbsp;Files</b>',
    'msg08'        => 'File Locked by: %s',
    'msg09'        => 'nexFile Delete File Admin',
    'msg10'        => 'nexFile Add File Admin',
    'msg11'        => 'Add New Version for: ',
    'msg12'        => 'nexFile Edit File Admin',
    'msg13'        => 'Edit File Properties',
    'msg14'        => 'File Detail View',
    'msg15'        => 'Return to Project',
    'msg16'        => 'File Listing',
    'msg17'        => 'Download File',
    'msg18'        => 'Edit File Details',
    'msg19'        => 'Delete File',
    'msg20'        => 'Lock File',
    'msg21'        => 'Unlock File',
    'msg22'        => 'New Version',
    'msg23'        => 'Notify Me',
    'msg24'        => 'Select Users',
    'msg25'        => 'Select Groups',
    'msg26'        => 'Access Rights',
    'msg27'        => 'View Category',
    'msg28'        => 'Upload Admin',
    'msg29'        => 'Category Admin',
    'msg30'        => 'Upload with Approval',
    'msg31'        => 'Upload Direct',
    'msg32'        => 'Upload New Versions',
    'msg33'        => 'Add Permissions',
    'msg34'        => 'User Access Records',
    'msg35'        => 'Group Access Records',
    'msg36'        => 'None Available',
    'msg37'        => 'nexFile Add new Catagory Admin',
    'msg38'        => 'Add New Category',
    'msg39'        => 'Edit Category => %s',
    'msg40'        => 'Edit Category',
    'msg41'        => 'Category Admin => %s',
    'msg42'        => 'nexFile Edit File Submission',
    'msg43'        => ' File Submission to Approve',
    'msg44'        => 'Uploads awaiting approval',
    'msg45'        => 'File submissions awaiting approval',
    'msg46'        => 'Your File and Category Subscriptions',
    'msg47'        => 'nexFile Status Message',
    'msg48'        => '%s Click <a href="%s">here</a> to continue.',
    'msg49'        => 'To Continue => ',
    'msg50'        => 'This page should return automatically. If you do not wish to wait, click <a href="%s">here</a>',
    'msg51'        =>  'Last Updated:%s<br>Version:%s<br>Size:%s',
    'msg52'        => 'Click to switch to Remote File',
    'msg53'        => 'Click to switch to Local File',
    'msg54'        => 'Click to add a Thumbnail to file details',
    'msg55'        => 'Click to hide Thumbnail details',
    'msg56'        => 'Are you sure you want to delete this category and ALL related files ?',
    'msg57'        => 'Delete Category: %s completed. All files removed and database updated',
    'msg58'        => 'Category and related files deleted',
    'msg59'        => 'Category: %s',
    'msg60'        => 'Category [%s] Permission Admin',
    'msg61'        => 'Click to Download this file',
    'msg62'        => 'File Last Updated: Click to see calendar',
    'msg63'        => 'Category Admin',
    'msg64'        => 'Unsubscribe Me'

);

$LANG_FMERR = array (
    'err1'          => 'Error: Invalid Operation attempt',
    'err2'          => 'Error: Invalid Category ID',
    'err3'          => '<B>Sorry you must %s register</A> or %s login </A> to use these forums</B>',
    'err4'          => '<br><br>You need to be signed in to use this site feature.<p />',
    'err5'          => 'Error: Syntax problems with file URL',
    'err6'          => 'Error: Checking add file URL. Host: %s not responding',
    'err7'          => 'Error: Remote URL: %s is not responding',
    'err8'          => 'Warning - Category has Sub Folders. Found %s Sub-Categories<br>',
    'err9'          => 'Warning - Can not delete the category as there are still %s files<br>Remove all files from %s first<br>',
    'err10'         => 'Warning Can not find the Category Directory %s<br>Removing the category record',
    'err11'         => 'ERROR: Invalid Access to DELETE File Category<br>Attempt will be logged',
    'err12'         => 'Error - Unable to create directory: %s<br>',
    'err13'         => 'Error - Unable to create directory: %s/images<br>',
    'err14'         => 'Error - Unable to create directory: %s/submissions<br>',
    'err15'         => 'Error - Unable to create directory: %s/submmissions/images<br>',
    'err16'         => 'ERROR: Invalid Access to CREATE File Category',
    'upload1'       => '<br><blockquote>You must select a category and input a file title.</blockquote><center><button  onclick=\'javascript:history.go(-1)\'>return</button></center><br>',
    'upload2'       => 'Error: Upload File form data incorrect',
    'upload3'       => 'Error no file was added - incomplete information',
    'upload4'       => 'Error: Upload File form data incorrect - thumbnail input',
    'upload5'       => 'Error: Upload File form data incorrect',
    'upload6'       => 'Error no file was added - incomplete information',
    'upload7'       => 'File Upload Errors',
    'upload8'       => 'Error: Upload File Failed: Could Not Find Selected File',
    'upload9'       => 'Error: Invalid file type:',
    'notify1'       => 'You have already subscribed to updates for this category',
    'notify2'       => 'You will be notified of any new files for Category: %s',
    'notify3'       => 'Deleted your notification record ID: %s',
    'notify4'       => 'ERROR: Unable to delete notification Record',
    'download1'     => 'File Download Errors:<BR>',
    'download2'     => 'Error! Download.php script => No file specified',
    'download3'     => 'Error! Download.php script => Could not open file for read',
    'download4'     => 'Error! Download.php script => Invalid parameters',
    'perms1'        => 'ERROR: Invalid Access to DELETE Category Permissions',
    'perms2'        => 'No permission records assigned',
    'admin1'        => 'ERROR: Invalid Access to UPDATE File Category',
    'admin2'        => 'ERROR: Invalid Access to EDIT File Category',
    'access1'       => 'Documemt Admin Access Error',
    'access2'       => 'You need to be a signed in user to access this program area'

);

// Messages used for Notification Feature - 4 messages defined
// Msg1: New File Notifcation
$LANG_FM10 = array ('1','2','3','4');
$LANG_FM10['1'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New File Management Update",
    'LINE1'          => "Site member %s: has submitted a new file in category: %s\n",
    'LINE2'          => 'The file: %s can be accessed at %s',
    'LINE3'          => "\n\nYou are receiving this because you requested to be notified of updates.\n",
    'LINE4'          => "\nHave a great day!\n"
);
// Msg2: File Submission Approval Notifcation
$LANG_FM10['2'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New File Submission Approved",
    'LINE1'          => "Site member %s: your file in category: %s\n",
    'LINE2'          => 'The file: %s has been approved and can be accessed at %s',
    'LINE3'          => "\n\nYou are receiving this because you requested to be notified.\n",
    'LINE4'          => "\nHave a great day!\n"
);
// Msg3: File Submission Declined Notifcation
$LANG_FM10['3'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New File Submission Cancelled",
    'LINE1'          => "Your recent file submission: %s, was not accepted\n\n",
    'LINE2'          => "You are receiving this because you requested to be notified.\n",
    'LINE3'          => "\nHave a great day!\n"
);

// Msg4: File Submission awaiting approval Notifcation
$LANG_FM10['4'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New File Submission requires Approval",
    'LINE1'          => "Site member %s: has submitted a new file in category: %s\n",
    'LINE2'          => 'The file: %s can be approved via this link %s',
    'LINE3'          => "\n\nYou are receiving this because you requested to be notified.\n",
    'LINE4'          => "\nHave a great day!\n");

$LANG_FM11 = array (
    'FILE'          => "File",
    'DATE'          => "Date",
    'AUTHOR'        => "Author",
    'DOWNLOADS'     => "Downloads"
);

$PLG_nexfile_MESSAGE1 = 'Invalid download request';
$PLG_nexfile_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexfile_MESSAGE11 = 'nexFile Plugin Upgrade completed - no errors';
$PLG_nexfile_MESSAGE12 = 'nexFile Plugin Upgrade failed - check error.log';


?>