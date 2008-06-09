<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | export.php                                                                |
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

include ('../../../lib-common.php');
require_once( $_CONF['path_system'] . 'classes/downloader.class.php' );


/* Setup a list of forms to export or use the formid passed in to just export a single form */

/* Option 1: Single form - passed in */
$formid = COM_applyFilter($_GET['form'],true);
$exportforms = array($formid);

/* Option 2: List of forms to export */
//$exportforms = array(33,36,37,42,43,44,46,47,49,51,52,53,55,56,57,58,59,60,61,62,63);



$downloadFileType =  array('sql' => 'text/plain');
$downloadDirectory = $CONF_FE['export_dir'];
$file =  'importform_data.sql';

function generateSQL($t,$formid,$cntr) {
    global $_TABLES;

    if ($t == 'formDefinitions') {
        $table = $_TABLES['formDefinitions'];
        $query = DB_query("SELECT * from {$table} WHERE id='$formid'");
    } else {
        $table = $_TABLES['formFields'];
        $query = DB_query("SELECT * from {$table} WHERE formid='$formid'");
    }

    while ($A = DB_fetchArray($query)) {
        $numfields =  DB_numFields($query);
        $myvars = array();
        $sqlstmt .= '$_SQL['.$cntr.'][] = "INSERT INTO '. $table;
        $sqlstmtVars = '';
        $sqlstmtValues = '';

        // Need to skip the first field - which is an auto-increment primary key
        for ($i = 1; $i < $numfields; $i++) {
            $fieldname = DB_fieldName($query,$i);
            //echo "<br>$i:$fieldname and value:{$A[$fieldname]}";
            $myvars[$fieldname] = $A[$fieldname];
            if ($t == 'formFields' AND $fieldname == 'formid') {
                $value = $cntr;
            } else {
                $value= addslashes($A[$fieldname]);
                //$value = str_replace('"','\"',$A[$fieldname]);
                //$value = str_replace('\'','\\\'',$A[$fieldname]);
            }
            if ($i > 1) {
                $sqlstmtVars .= ",$fieldname";
                if (is_null($A[$fieldname])) {
                    $sqlstmtValues .= ",NULL";
                } else {
                    $sqlstmtValues .= ",'$value'";
                }
            } else {
                $sqlstmtVars .= "$fieldname";
                $sqlstmtValues .= "'$value'";
            }
        }
        $sqlstmt = $sqlstmt . ' (' . $sqlstmtVars . ') VALUES (' . $sqlstmtValues . ')";' . LB;
    }

    return $sqlstmt;
}


if (count($exportforms)  > 0) {
    // Create an array for all fieldnames and their values for this record.

    $date =  COM_getUserDateTimeFormat();
    $exportscript .= '<?php' . LB;
    $exportscript .= '// Export Form Defintion for: ' . DB_getItem($_TABLES['formDefinitions'], 'name', "id='$formid'") . LB;
    $exportscript .= '// Date: ' . $date[0] . LB . LB;

    $i = 1;
    foreach ($exportforms as $formid) {
        $exportscript .= LB . LB .'# Export Form Definitions ' . LB;
        $exportscript .= generateSQL('formDefinitions',$formid,"900{$i}");
        $exportscript .=  LB . '# Export Field Definitions ' . LB;
        $exportscript .= generateSQL('formFields',$formid,"900{$i}");
        $i++;
    }
    $exportscript .= LB . '?>';

    if (!$fp = @fopen($downloadDirectory.$file, "w")) {
        COM_errorLog("Error exporting form definition - Unable to write to file: $exportfile");
    } else {
        fwrite($fp, $exportscript);
        fclose($fp);

        // Send new file to user's browser
        $download = new downloader();
        $download->_setAvailableExtensions ($downloadFileType);
        $download->setAllowedExtensions ($downloadFileType);
        $download->setPath($downloadDirectory);
        $logfile = $_CONF['path'] .'logs/error.log';
        $download->setLogFile($logfile);
        $download->setLogging(true);
        $download->downloadFile($file);
        if ($download->areErrors()) {
            COM_errorLog("Error downloading nexform Export SQL file: " . $download->printErrors());
        }
    }
}

?>