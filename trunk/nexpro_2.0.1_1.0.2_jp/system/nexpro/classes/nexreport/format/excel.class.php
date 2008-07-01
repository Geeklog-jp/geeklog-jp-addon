<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | excel.class.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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

class excel_format {

    var $_filedata;     //data which stores the xls file

    function format_report(&$reportobj) {

        $x = 0;
        $y = 0;

        $this->_filedata = $this->xlsBOF();

        if ($reportobj->_title != '') {
            $filename = "{$reportobj->_title}.xls";
        } else {
            $filename = 'export.xls';
        }

        foreach ($reportobj->_fields as $key => $showstate) {
            if ($showstate) {
                $headingfield = str_replace('_', ' ', $reportobj->_headings[$key][0]);
                $headingfield = ucwords($headingfield);
                $this->xlsWriteLabel($y, $x, $headingfield);
                $x++;
            }
        }

        foreach ($reportobj->_report as $line) {
            $x = 0;
            $y++;
            foreach ($line as $fieldvalue) {
                if (is_int($fieldvalue)) {
                    $this->xlsWriteNumber($y, $x, $fieldvalue);
                }
                else {

                    $this->xlsWriteLabel($y, $x, strip_tags($fieldvalue));
                }
                $x++;
            }
        }

        $this->_filedata .= $this->xlsEOF();

        // Test if output file option set - if so then we don't want to set headers as output does not go to screen
        if ($reportobj->_outputfile == '') {
            header ("Expires: 0");
            header ("Pragma: no-cache");
            header ('Content-type: application/x-msexcel');
            header ("Content-Disposition: attachment; filename={$filename}");
        }

        return $this->_filedata;
    }
    
    

    /**
    * @desc function xlsBOF: Excel beginning of file header
    */
    function xlsBOF() { 
        return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
    }

    /**
    * @desc function xlsEOF: Excel end of file footer
    */
    function xlsEOF() { 
        return pack("ss", 0x0A, 0x00); 
    }

    /**
    * @desc function export_file: Function to write a Number (double) into Row, Col
    * @param int $Row = row number
    * @param int $Col = column number
    * @param double $Value = value to be written to cell ($Row, $Col)
    */
    function xlsWriteNumber($Row, $Col, $Value) {
        $str = ''; 
        $str .= pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
        $str .= pack("d", $Value);
        
        $this->_filedata .= $str; 
    } 

    /**
    * @desc funciton xlsWriteLabel: Function to write a label (text) into Row, Col
    * @param int $Row = row number
    * @param int $Col = column number
    * @param string $Value = value to be written to cell ($Row, $Col)
    */
    function xlsWriteLabel($Row, $Col, $Value ) {
        $str = ''; 
        $L = strlen($Value); 
        $str .= pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
        $str .= $Value;
         
        $this->_filedata .= $str; 
    }      


}

?>