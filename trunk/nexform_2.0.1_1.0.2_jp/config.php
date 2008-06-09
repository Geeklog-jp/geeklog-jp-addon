<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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

$CONF_FE['debug'] = false;
$CONF_FE['version'] = '2.0.1';

/* Setup Paths and URL's */
$CONF_FE['post_url'] = $_CONF['site_url'] .'/nxfrm';
$CONF_FE['public_url'] = $_CONF['site_url'] .'/nxfrm';
$CONF_FE['uploadpath']   = $_CONF['path_html'] . 'nxfrm/data/';
$CONF_FE['downloadURL']  = $_CONF['site_url'] . '/nxfrm/data';
$CONF_FE['image_url']  = $_CONF['layout_url'] . '/nexform/images';
$CONF_FE['export_dir'] = $_CONF['path_html'] . 'admin/plugins/nexform/export/';

/* Load the Javascript for the fValidate library */
$CONF_FE['load_fValidate'] = false;

/* Load the Javascript and CSS in the header for the DHTML Pop-UP Calendar */
$CONF_FE['load_calendar'] = false;

/* Load the Javascript for the sarissa library */
$CONF_FE['load_sarissa'] = false;

/* When editing a template - these variables are required  and checked that they are not removed */
$CONF_FE['mandatory_template_vars'] = array('form_id','form_action','form_handler');

/* When adding new fields - should the mandatory Option be set by default */
$CONF_FE['field_mandatory_default'] = false;

/* Default cell width parm if no value is entered in the field definition */
$CONF_FE['field_defaultspacing'] = '0';  // values: 40, 50 will be converted to a style="width:xx%;"

/* Default right padding to use for the form label/field pair*/
$CONF_FE['field_defaultrightpadding'] = '5';  // spacing in pixels only

/* Set Limit of the number of summary fields to show in result listing tool */
$CONF_FE['result_summary_fields'] = 5;

/* FCK Editor Toolbar to use in TextArea Field Settings
 * Load Javascrript for for Toolbar to work - set to true if for some reason GL or no other plugin is loading
*/
$CONF_FE['load_editor']  = false;
$CONF_FE['fckeditor_toolbar'] = 'Basic';    // Toolbar to use in the forms with textarea2 fields


/* Dynamic Select Feature is not full implemented. Need to determine now form will know which lists are inter-related.
*  Javascript needs to know the ID's for list1 and list2 - the ID's are set based on the field id
*  Need to manually add the calls to the required JS function per set of selectbox's
*  This is done in the template dselect_js.thtml - example: dynamicSelect('list19', 'list18');
*  The custom function like [aim1detail:category] need to handle setting the extra html tags 'class=xx' in the select options HTML
*/
$CONF_FE['dynamicSelect'] = true;        // Set to true in the code if current form is using the dynamicSelect feature


/* Set any fixed field options you want to have applied and used for all forms */
$CONF_FE['defaultattributes'] = array (
    'text'       =>  'size="60"',
    'file'       =>  'size="40"',
    'textarea1'  =>  'rows="10" cols="50"'
);

$CONF_FE['fieldstyles'] = array (
    '1'   => array('Default','frm_label1'),
    '2'   => array('Bold','frm_label2'),
    '3'   => array('Normal','frm_label3'),
    '4'   => array('Highlighted','frm_label4'),
    '5'   => array('Large Heading', 'frm_h1'),
    '6'   => array('Medium Heading1', 'frm_h2'),
    '7'   => array('Small Heading', 'frm_h3'),
    '8'   => array ('Boxed Look','frm_label4')
);


/* Define custom template files to be used to render the form - in the {theme_dir}/nexform directory */
$CONF_FE['templates'] = array (
    'defaultform.thtml'   => 'default',
    'minimalform.thtml'   => 'minimal'
);


$CONF_FE['fileperms'] = '0755';  // Needs to be a string for the upload class use.

$CONF_FE['max_uploadimage_width']    = '2100';
$CONF_FE['max_uploadimage_height']   = '1600';
$CONF_FE['max_uploadfile_size']      = '6553600';     // 6.400 MB
$CONF_FE['allowablefiletypes']    = array(
        'application/x-gzip-compressed'     => '.tgz',
        'application/x-zip-compressed'      => '.zip',
        'application/x-tar'                 => '.tar',
        'text/plain'                        => '.php, .txt, .inc (etc)',
        'text/html'                         => '.html, .htm (etc)',
        'image/bmp'                         => '.bmp, .ico',
        'image/gif'                         => '.gif',
        'image/pjpeg'                       => '.jpg, .jpeg',
        'image/jpeg'                        => '.jpg, .jpeg',
        'image/png'                         => '.png',
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
        'application/octet-stream'          => '.vsd, .fla, .psd, .xls, .doc, .ppt, .pdf, .swf, .mpp, .txt, .dwg, .xls'
        );

// Custom Field type - mapping to template names to be used
//   custX          type of field, where X is an incremental number
//      form        form template
//      record      record template
//      print       print templates to use for the main container and records
//      javascript  javascript template

$CONF_FE['customfieldmap'] = array (
    'cust1' => array (
        'form'       => 'mycustomform_detail.thtml',
        'record'     => 'mycustomform_record.thtml',
        'print'      => 'mycustomform_print.thtml,mycustomform_print_record.thtml',
        'javascript' => 'mycustomform_js.thtml'
        )
);




/*******************************************************************************/
/*  Do not Edit anything below this line                                       */
/******************************************************************************/

$_TABLES['formDefinitions']       = $_DB_table_prefix . 'nxform_definitions';
$_TABLES['formFields']            = $_DB_table_prefix . 'nxform_fields';
$_TABLES['formResults']           = $_DB_table_prefix . 'nxform_results';
$_TABLES['formResData']           = $_DB_table_prefix . 'nxform_resdata';
$_TABLES['formResText']           = $_DB_table_prefix . 'nxform_restext';
$_TABLES['formResultsTmp']        = $_DB_table_prefix . 'nxform_results_tmp';
$_TABLES['formResDataTmp']        = $_DB_table_prefix . 'nxform_resdata_tmp';
$_TABLES['formResTextTmp']        = $_DB_table_prefix . 'nxform_restext_tmp';


$CONF_FE['fieldtypes'] = array (
    'text'       =>  array('txt_frm','Text'),
    'mtxt'       =>  array('mtxt_frm','Multiple Text Field'),
    'date1'      =>  array('da1_ftm','Date'),
    'date2'      =>  array('da2_frm','Date with popup Calendar'),
    'datetime'   =>  array('time_frm','Date/Time with popup Calendar'),
    'passwd'     =>  array('pwd_frm','Password'),
    'select'     =>  array('sel_frm','Select dropdown list'),
    'checkbox'   =>  array('chk_frm','Checkbox'),
    'multicheck' =>  array('mchk_frm','Multiple Checkboxes'),
    'textarea1'  =>  array('ta1_frm','Textarea'),
    'textarea2'  =>  array('ta2_frm','Textarea with Editor'),
    'radio'      =>  array('rad_frm','Radio Option'),
    'file'       =>  array('file_frm','File Upload'),
    'mfile'      =>  array('mfile_frm','Multiple File Upload'),
    'dynamic'    =>  array('dynm_frm','Dynamic Form'),
    'captcha'    =>  array('captcha_frm','Captcha'),
    'heading'    =>  array('heading','Form Heading'),
    'submit'     =>  array('sub_frm','Submit Button'),
    'cancel'     =>  array('btn_frm','Cancel Button'),
    'hidden'     =>  array('hid_frm','Hidden')
);

$CONF_FE['postmethods'] = array (
    'dbsave'            => 'Save to Database',
    'email'             => 'Email Results',
    'email+dbsave'      => 'Email and Save Results',
    'posturl'           => 'Post to URL'
);

?>