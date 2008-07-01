<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-uploadfiles.php                                                       |
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

function nexform_check4files($result_id=0, $single_file='') {
    global $_CONF,$_TABLES,$CONF_FE,$LANG_FE_ERR;

    if ($CONF_FE['debug']) {
        COM_errorLog("Check4files - result_id:$result_id");
    }

    /* Check if custom hidden field is used on the form to specify allowable file types */
    if ($uploadFileTypesAllowed != '' and !is_array($allowablefiletypes)) {
         $formtypes = explode(',',$uploadFileTypesAllowed);
         $allowablefiletypes = array();
         foreach ($CONF_FE['allowablefiletypes'] as $key => $haystack) {
             foreach ($formtypes as $needle) {
                 if (strpos($haystack,$needle) !== false) {
                     $allowablefiletypes[$key] = $haystack;
                 } else {
                 }
             }
           }
     }

     if (!is_array($allowablefiletypes)) {
         $allowablefiletypes = $CONF_FE['allowablefiletypes'];
     }

    foreach ($_FILES as $var => $uploadfile) {
        if ($single_file != '' AND $single_file != $var) {
            continue;
        }
        if ($uploadfile['size'][0] <= 0 AND $single_file != '') {
            return false;
        }
        /* The variable names contain the fieldtype and fieldid */
        /* XXX_frm{formid}_{fieldid}    - where XXX is the fieldtype */
        $parts = explode('_',$var);
        $fieldtype = $parts[0];
        $field_id = (int) $parts[2];

        $is_dynamicfield_result = false;
        if (isset($parts[4])) {
             $dynamicFieldInstance = $parts['4'];
             $sfield_id = (int) $parts['2'];
             $field_id  = (int) $parts['3'];
             $instance  = (int) $parts['4'];
             $is_dynamicfield_result = true;
             $dynamicForm = DB_getItem($_TABLES['formFields'],'formid',"id='$field_id'");
             // Get the results currently recorded for the source form field
             $dynamicResults = explode('|',DB_getItem($_TABLES['formResData'],'field_data',"result_id='$result_id' AND field_id='$sfield_id'"));
             // Check if this instance of the dynamic form is already created as a result.
             if ((isset($dynamicResults[$instance])) AND ($dynamicResults[0] != '') AND (count($dynamicResults) > 0)) {
                 $dynamicResult = $dynamicResults[$instance];
             } else {
                 // User must be submitting the form with a new instance of this dynamic subform (field)
                 // Need to create a new result record and update relating fields with the new resultid
                DB_query("INSERT INTO {$_TABLES['formResults']} (form_id,uid,date)
                                VALUES ('$dynamicForm','$userid','$date') ");
                $dynamicResult = DB_insertID();
                $dynamicResults[$instance] = $dynamicResult;
                $relatedFieldResults = implode ('|',$dynamicResults);
                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$relatedFieldResults' WHERE result_id='$result_id' AND field_id='$sfield_id'");

                // Now need to update the related Results field in the main results records
             }
        } else {
            $field_id = (int) $parts['2'];
            $is_dynamicfield_result = false;
        }

        if (is_array($uploadfile[name]) ) {
            /* Skip if no files uploaded in the multi-file field */
            if ($uploadfile[name][0] != '' ) {

                for ($i=0; $i < count($uploadfile[name]); $i++) {

                    /* Upload class is not expecting an array of upload files - so pass a single associative array */
                    $upload_newfile = array (
                        'name'      => $uploadfile['name'][$i],
                        'type'      => $uploadfile['type'][$i],
                        'tmp_name'  => $uploadfile['tmp_name'][$i],
                        'error'     => $uploadfile['error'][$i],
                        'size'      => $uploadfile['size'][$i]
                    );
                    $uploadfilename =  ppRandomFilename();
                    $pos = strrpos($uploadfile['name'][$i],'.') + 1;
                    $ext = strtolower(substr($uploadfile['name'][$i], $pos));
                    $filename = "{$uploadfilename}.{$ext}";
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Mfile upload: Original file: {$uploadfile['name'][$i]} and new filename: $filename");
                    }
                    if ( nexform_uploadfile($filename,$upload_newfile,$allowablefiletypes) ) {
                        // Store both the created filename and the real file source filename
                        $realfilename = $filename;
                        $filename = "$filename:{$upload_newfile['name']}";
                        if ($is_dynamicfield_result) {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                VALUES ('$dynamicResult','$field_id','$filename',1) ");
                            if ($single_file != '') {
                                $retval = DB_insertID();
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                VALUES ('$result_id','$field_id','$filename') ");
                            if ($single_file != '') {
                                $retval = DB_insertID();
                            }
                        }
                    } else {
                        COM_errorLog("upload error:" . $GLOBALS['fe_errmsg']);
                        $errmsg = $GLOBALS['fe_errmsg'];
                        return false;
                    }
                }
            }

        } else {

            if ($uploadfile['size'] > 0 AND $uploadfile['name'] != '') {
                $uploadfilename =  ppRandomFilename();
                $pos = strrpos($uploadfile['name'],'.') + 1;
                $ext = strtolower(substr($uploadfile['name'], $pos));
                $filename = "{$uploadfilename}.{$ext}";

                if ($CONF_FE['debug']) {
                    COM_errorLog("Upload file - random name: $filename");
                }

                if ( nexform_uploadfile($filename,$uploadfile,$allowablefiletypes) ) {
                    // Store both the created filename and the real file source filename
                    $realfilename = $filename;
                    $filename = "$filename:{$uploadfile['name']}";
                    if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                        DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$filename' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                    } else {
                        if ($is_dynamicfield_result) {
                            if (DB_count($_TABLES['formResData'],array('result_id','field_id'), array($dynamicResult,$field_id)) > 0) {
                                DB_query("UPDATE {$_TABLES['formResData']} set field_data = '$filename' WHERE result_id='$dynamicResult' AND field_id='$field_id'");
                            } else {
                                DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data,is_dynamicfield_result)
                                    VALUES ('$dynamicResult','$field_id','$filename',1) ");
                            }
                        } else {
                            DB_query("INSERT INTO {$_TABLES['formResData']} (result_id,field_id,field_data)
                                VALUES ('$result_id','$field_id','$filename') ");
                        }
                    }

                } else {
                    COM_errorLog("upload error:" . $GLOBALS['fe_errmsg']);
                    $errmsg = $GLOBALS['fe_errmsg'];
                    return false;
                    break;
                }
            }
        }
    }

    if ($retval != 0) {
        return $retval;
    }
    else {
        return true;
    }

}


function nexform_uploadfile($filename,&$upload_file,$allowablefiletypes) {
    global $_FILES,$_CONF,$_TABLES,$CONF_FE,$LANG_FE_ERR;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $upload = new upload();
    $upload->setPath($CONF_FE['uploadpath']);
    $upload->setLogging(true);
    $upload->setAutomaticResize(false);
    $upload->setAllowedMimeTypes($allowablefiletypes);
    // Set max dimensions as well in case user is uploading a full size image
    $upload->setMaxDimensions ($CONF_FE['max_uploadimage_width'], $CONF_FE['max_uploadimage_height']);
    $upload->setMaxFileSize($CONF_FE['max_uploadfile_size']);

    if (strlen($upload_file['name']) > 0) {
        $upload->setFileNames($filename);
        $upload->setPerms( $CONF_FE['fileperms'] );

        $upload->_currentFile = $upload_file;

        // Verify file meets size limitations
        if (!$upload->_fileSizeOk()) {
            $upload->_addError('File, ' . $upload->_currentFile['name'] . ', is bigger than the ' . $upload->_maxFileSize . ' byte limit');
        }

        // If all systems check, do the upload
        if ($upload->checkMimeType() AND $upload->_imageSizeOK() AND !$upload->areErrors()) {
            if ($upload->_copyFile()) {
                $upload->_uploadedFiles[] = $upload->_fileUploadDirectory . '/' . $upload->_getDestinationName();
            }
        }

        $upload->_currentFile = array();

        if ($upload->areErrors() AND !$upload->_continueOnError) {
            $errmsg = "nexform: upload function error:" . $upload->printErrors(false);
            COM_errorLog($errmsg);
            $GLOBALS['fe_errmsg'] = $LANG_FE_ERR['upload1'] .':<BR>' . $upload->printErrors(false);
            return false;
        }
        return true;

    } else {
        return false;
    }

    return false;

}

?>