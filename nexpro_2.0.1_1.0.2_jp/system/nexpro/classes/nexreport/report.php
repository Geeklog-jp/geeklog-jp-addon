<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | report.php                                                                |
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

/* Include in the main report formatter factory class */
include ('formatter.class.php');

class report  {

    // Private Properties  - Use the set Methods to change

    var $_reportid = 1;                 // Report id - used when class is being used in a script for multiple reports

    var $_fields = array();             // An array of fields for the report

    var $_headings = array();           // An array of headings - key'ed by fieldname

    var $_totalfields = array();        // Array of fields that are to be totaled and shown in page total line    
    
    var $_finalformatfields = array();  // Array of fields that need some final formatting or processing     

    var $_sqlstmt = '';                 // SQL Statment that will be used for the report

    var $_mode = '';                    // Report class mode. Mode of 'display' or 'export' initially supported
    
    var $_record = '';                  // Will hold the current fetched Record from query

    var $_logging = false;              // Set to true to enable extra COM_errorLog output
    
    var $_actionurl = '';               // URL where report script is running from      

    var $_report = array();             // An array of report line records for the generated report
    
    var $_reportline = array();         // Current report line

    var $_title = '';                   // Report Title
    
    var $_message = '';                 // Report Heading Message

    var $_showrownumbers = true;        // Set true to show Row Numbers in formatted report display

    var $_filterreport = false;         // Set true to activate the report sorting feature     

    var $_sortreport = false;           // Set true to activate the report sorting feature

    var $_sortfield = '';               // Field to sort if feature enabled

    var $_sortorder = 1;                // Field to sort if feature enabled - Field 1 matches first heading

    var $_sortdirection = SORT_ASC;     // Direction to sort report - SORT_ASC or SORT_DESC are valid PHP Defined Options

    var $_sortableheadings = true;      // Set to true to automatically enable sortable headings
    
    var $_hidedatefilter = false;       // Set to true to hide data filter in online report view

    var $_page = 1;                     // Current page if report more then 1 page in size

    var $_pagesize = 30;                // Page size to use - default can be set with this define - set to 0 for no pagenation

    var $_numpages = 0;                 // Number of pages in this report - depends on pagesize and number of lines in total report
    
    var $_recordcount = 0;              // Total number of reords in report
    
    var $_filterparms = '';             // String extra parms to be passed in display report links - like the pagination links
    
    var $_extraparms = '';              // Array of extra parms to be passed in display report links - like the pagination links
    
    var $_searchdate1 = 0;              // UNIX Timestamp equiv of the filter From Date entered in report heading
    
    var $_searchdate2 = 0;              // UNIX Timestamp equiv of the filter To Date entered in report heading

    var $_outputfile = '';              // Set to filename the formatted output should be sent to file instead of the screen.


    /**
    * Constructor
    *
    */
    function report()
    {

    }

    /**
     * Return an instance of a specific report.
     *
     * @param  string  $type     Name of the report
     * @param  array   $options  Options for the report class
     * @return mixed Instance of the report object.
     */
    function &factory($type='', $options = false)
    {
        if ($type == '') {
            $object = & new report(); 
            return $object;            
        } else {
            $classfile = "type/{$type}.php";
            if (include_once $classfile) {
                $class = "{$type}_report";
                if (class_exists($class)) {
                    $object = & new $class();
                    $object->_constructor($options);
                    return $object;
                } else {
                    COM_errorLog("report.class - Unable to instantiate class $class from $classfile");
                }
            } else {
                COM_errorLog("report.class - Unable to include file: $classfile");
            }
        }

    }


    function _construct($id=1)
    {
        $this->_reportid = $id;

    }

    function set_sqlstmt($sql) {

        $this->_sqlstmt = $sql;
    }
    
    function set_mode($mode) {

        $this->_mode = $mode;
    }    

    function set_actionurl($url) {
        $this->_actionurl = $url;
    }    
    function set_outputfile($filename) {
        $this->_outputfile = $filename;
    }

    function set_rownumbers($state) {
        if ($state == 'on' OR $state == 1) {
            $this->_showrownumbers = true;
        } else {
            $this->_showrownumbers = false;
        }
    }
    
    function set_filterreport($state) {
        if ($state == 'on' OR $state == 1) {
            $this->_filterreport = true;
        } else {
            $this->_filterreport = false;
        }
    }    

    function set_sortreport($state) {
        if ($state == 'on' OR $state == 1) {
            $this->_sortreport = true;
        } else {
            $this->_sortreport = false;
        }
    }

    function set_sortheadings($state) {
        if ($state == 'on' OR $state == 1) {
            $this->_sortableheadings = true;
        } else {
            $this->_sortableheadings = false;
        }
    }

    /* Set the heading to sort on - pass in the field label name as the field */
    function set_sortfield($field) {
        $this->_sortfield = $field;
    }

    /* Pass in the field order to sort on - based on current enabled report headings starting at field 1 */
    function set_sortorder($fieldorder) {
        // Need to figure out which heading field this fieldorder parm relates to
        // Sort Report method uses the field label as the key
        $i = 1;
        foreach ($this->_fields as $field => $show) {
            if ($show) {
                if ($i == $fieldorder) {
                    $this->_sortfield = $field;
                    $this->_sortorder = $fieldorder;
                    break;
                }
                $i++;
            }
        }
    }

    function set_sortreverse() {

        // If sortable heading mode being used - then this link parm is set
        if (isset($_GET['lastdir'])) {
            if ($_GET['lastdir'] == SORT_DESC) {
                $this->_sortdirection = SORT_ASC;
            } else {
                $this->_sortdirection = SORT_DESC;
            }
        } else {
            if ($this->_sortdirection == SORT_ASC) {
                $this->_sortdirection = SORT_DESC;
            } else {
                $this->_sortdirection = SORT_ASC;
            }
        }

    }

    function set_pagesize($size) {
        $this->_pagesize = $size;
    }

    function set_currentpage($page) {
        $this->_page = $page;
    }

    function set_title($title) {
        $this->_title = trim($title);
    }
    
    function set_message($message) {
        $this->_message = trim($message);
    }
    
    function set_reportdatefilter ($state) {
        if ($state == 'on' OR $state == 1) {
            $this->_hidedatefilter = false;
        } else {
            $this->_hidedatefilter = true;
        }
    }
    
    function set_filterparms($parms) {
        $this->_filterparms .= $parms;
    }       

    function set_extraparms($parms) {
        $this->_extraparms = $parms;
    }    

    function set_searchdate1($value) {
        $this->_searchdate1 = $value;
    }     
    
    function set_searchdate2($value) {
        $this->_searchdate2 = $value;
    }    
    
    function add_field($field,$heading='',$width=0,$state=true) {

        if(!array_key_exists($field,$this->_fields)) {
            $newelement = array($field => $state);
            $this->_fields = array_merge($this->_fields,$newelement);
            if (trim($heading) == '') {
                $newheading = array($field => array(ucfirst($field),$width));
            } else {
                $newheading = array($field => array($heading,$width));
            }
            $this->_headings = array_merge($this->_headings,$newheading);
        }

    }

    function set_fieldon($field) {

        if(array_key_exists($field,$this->_fields)) {
            $this->_fields[$field] = true;
        }

    }

    function set_fieldoff($field) {

        if(array_key_exists($field,$this->_fields)) {
            $this->_fields[$field] = false;
        }

    }
    
    function add_totalfield($field) {

        if(!array_key_exists($field,$this->_totalfields)) {
            $newelement = array($field => 0);
            $this->_totalfields = array_merge($this->_totalfields,$newelement);
        }

    }
    
    function add_finalformatfield($field) {

        if(!array_key_exists($field,$this->_finalformatfields)) {
            $newelement = array($field => true);
            $this->_finalformatfields = array_merge($this->_finalformatfields,$newelement);
        }

    }       
    
    // Placeholder function which will typically be in subclass 
    function filterReport() {
        
    }
    
    // Run thru each report line and if there is a field set then add it to that column total
    // After the columns are totalled, add a final row to the report
    function totalReport() {

        $totalReportLine = array();
        foreach ($this->_report as $reportline) {
            foreach ($reportline as $field => $value) {
                if (array_key_exists($field,$this->_totalfields)) {
                    $this->_totalfields[$field] = $this->_totalfields[$field] + strip_tags($value);   
                }
            }
        }
        $totalReportLine = array();
        foreach ($this->_fields as $field => $showfield) {
            if ($showfield) {
                if (array_key_exists($field,$this->_totalfields)) {                  
                    $totalReportLine[$field] = $this->_totalfields[$field];
                } else {
                    $totalReportLine[$field] = '';
                }
            }               
        }
        array_push($this->_report,$totalReportLine);
    }
    
    // Check if any fields have been flagged for final formatting
    // If so, run the finalformat function for that field and update that reportline    
    function finalFormatting() {

        $finalReport = array();
        foreach ($this->_report as $reportline) {
            $finalreportline = $reportline;
            foreach ($reportline as $field => $value) {
                if (array_key_exists($field,$this->_finalformatfields)) {
                    // Check if the formatting function for this field exists
                    $function = '_finalformat_' . $field;
                    if (method_exists($this,$function)) {
                        $finalreportline[$field] = $this->$function($value);
                    }
                }
            }
            array_push($finalReport,$finalreportline); 
        }
        $this->_report = $finalReport;
    }    


    function sortReport() {

        if (count($this->_report) > 0) {

            if (isset($this->_sortfield)) {
                foreach($this->_report as $reportline)
                    $sortAux[] = $reportline[$this->_sortfield];
                array_multisort($sortAux, $this->_sortdirection, $this->_report);
            } else {
                // No sortoptions enabled - sort by most recent first
                arsort($this->_report);
            }

            if ($this->_pagesize > 0) {
                if ($this->_page > 1) {
                    $offset = ($this->_page -1) * $this->_pagesize;
                } else {
                    $offset = 0;
                }
                $this->_report = array_slice($this->_report,$offset,$this->_pagesize);
            }
        }

    }


    function getReport() {
        global $_CONF,$_TABLES;

        if (isset($_POST['page'])) {
            $this->set_currentpage(COM_applyFilter($_POST['page'],true));
        }elseif (isset($_GET['page']) AND $_GET['page'] > 1 AND !isset($_GET['prevorder'])) {
            $this->set_currentpage(COM_applyFilter($_GET['page'],true));
        }
        if ($this->_sortableheadings) {
            if ((isset($_GET['order']) AND $_GET['order'] > 0) OR $this->_sortorder > 0) {
                if (isset($_GET['order']) AND $_GET['order'] > 0) {
                    $this->set_sortorder(COM_applyFilter($_GET['order'],true));
                }
                $this->set_sortreport('on');
                // Only set the variable 'prevorder' in the heading field links - so if used and same as order then reverse the sort
                if (isset($_GET['prevorder']) AND ($_GET['order'] == $_GET['prevorder'])) {
                    $this->set_sortreverse();
                } elseif ($_GET['reversesort'] == 1) {
                    $this->set_sortreverse();
                }
            }
        }

        $query = DB_query($this->_sqlstmt);
        $i = 0;
        while ($A = DB_fetchArray($query,false)) {
            $this->_reportline = array();
            $this->_record = $A;

            foreach ($this->_fields as $field => $showfield) {
                if ($field == 'project_id') {
                    $this->_projectid = $A[$field];
                }
                if ($field == 'process_id') {
                    $this->_processid = $A[$field];
                }

                if (array_key_exists($field, $A)) {
                    $value = $A[$field];
                    $lookupfunction = "_format_$field";
                    if (method_exists($this,$lookupfunction)) {
                        $value = $this->$lookupfunction($value);
                    }
                    if ($showfield) {
                        $this->_reportline[$field] = $value;
                    }
                } else {
                    // Field is not in the default SQL query - see if there is a method setup for this field
                    $value = $A[$field];
                    $fieldfunction = "_fielddata_$field";
                    if (method_exists($this,$fieldfunction)) {
                        $value = $this->$fieldfunction();
                    }
                    if ($showfield) {
                        $this->_reportline[$field] = $value;
                    }

                }
            }
            
            array_push($this->_report,$this->_reportline);
        }
                
        // If option to sort report was set - then run the report thru that method
        if ($this->_filterreport OR $this->_numpages > 1) {
            $this->filterReport();
        }         

        $this->_recordcount = count($this->_report);

        if ($this->_pagesize > 0) {
            $this->_numpages = ceil(count($this->_report) / $this->_pagesize);
        } else {
            $this->_numpages = 1;
        }      
            
        // If option to sort report was set - then run the report thru that method
        if ($this->_sortreport OR $this->_numpages > 1) {
            $this->sortReport();
        }

        // If there are any required total fields then calculate column total and show page total line
        if (count($this->_totalfields) > 0) { 
            $this->totalReport();
        }
        
        // Check if any fields need some special post formatting that you would have effecting the sorting or total methods
        if (count($this->_finalformatfields) > 0) { 
            $this->finalFormatting();
        }         
        

    }
 
    
    /**
    * @desc     Using the Strategy Design Pattern - to separate the formatting features into distinct and manageable classes
    * @param    object $formatter  Reference to the formatter 'strategy' class object to be used 
    * @return   mixed              Depending on formatter strategy, could be an formatted display, or excel export file
    */
    function generate_report($type) {
        $formatter = & formatter::factory($type);
        return $formatter->format_report($this);        
    }


}  // End of class


?>