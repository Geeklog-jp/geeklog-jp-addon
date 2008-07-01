<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | display.class.php                                                         |
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

class display_format {

    function format_report(&$reportobj) {
        global $_CONF;

        $p = new Template($_CONF['path_layout'] . 'nexreport');
        $p->set_file (array (
            'report'            =>      'report.thtml',
            'sortableheading'   =>      'report_sortableheadingfield.thtml',
            'currentheading'    =>      'report_currentheadingfield.thtml',             
            'heading'           =>      'report_headingfield.thtml',
            'record'            =>      'reportline.thtml',
            'field'             =>      'reportline_field.thtml'));

        $p->set_var('layout_url',$_CONF['layout_url']);
        $p->set_var('sortorder',$reportobj->_sortorder);
                
        if ($reportobj->_title != '') {
            // Modify Report title to include date filter selection
            if ($_REQUEST['searchdate1'] !='' AND $_REQUEST['searchdate2'] != '') {
                $reportobj->_title .= " between {$_REQUEST['searchdate1']} and {$_REQUEST['searchdate2']}";
            } elseif ($_REQUEST['searchdate1'] != '') {
                $reportobj->_title .= " > {$_REQUEST['searchdate1']}";
            } elseif ($_REQUEST['searchdate2'] != '') {
                $reportobj->_title .= " < {$_REQUEST['searchdate2']}"; 
            }            
            
            $p->set_var('startblock', COM_startBlock($reportobj->_title));
            $p->set_var('endblock',COM_endBlock());
        }
       
        if ($reportobj->_message == '') {
            $p->set_var('show_message','none');
        } else {
            $p->set_var('report_message',$reportobj->_message);
        }
        
        $p->set_var('record_count',$reportobj->_recordcount);

        if(isset($_REQUEST['searchdate1'])) $p->set_var('date1',$_REQUEST['searchdate1']);
        if(isset($_REQUEST['searchdate2'])) $p->set_var('date2',$_REQUEST['searchdate2']);
        
        if ($reportobj->_sortableheadings) {
            $order = 1;
            if ($reportobj->_actionurl != '') {
                $p->set_var('actionurl',$reportobj->_actionurl);
            } else {
                $p->set_var('actionurl',$_SERVER['PHP_SELF']);
            }
            $p->set_var('prevorder',$_GET['order']);
            $p->set_var('page',$reportobj->_page);
            $p->set_var('reportid',$reportobj->_reportid);
            $p->set_var('dir',$reportobj->_sortdirection);
            if ($reportobj->_sortdirection == SORT_DESC) {
                $base_url = "{$_SERVER['PHP_SELF']}?id={$reportobj->_reportid}&order={$reportobj->_sortorder}&reversesort=1";
            } else {
                $base_url = "{$_SERVER['PHP_SELF']}?id={$reportobj->_reportid}&order={$reportobj->_sortorder}";
            }
        } else {
            $base_url = "{$_SERVER['PHP_SELF']}?id={$reportobj->_reportid}&order={$reportobj->_sortorder}";
        }


        // Support for an array and/or string to set extra parms. Extra parms need to be passed in all report links
        if (is_array($reportobj->_extraparms) AND count($reportobj->_extraparms) > 0) {
            $filter = '';
            $resetparms = '';
            $hidden_vars = '';
            foreach ($reportobj->_extraparms as $parm => $value) {
                $hidden_vars .= '<input type="hidden" name="'.$parm.'" value="'.$value.'">' . LB;
                $filter .= "&{$parm}={$value}";
                $base_url .= "&{$parm}={$value}";
            }
            $p->set_var('base_url',$base_url);
            $p->set_var('hidden_variables',$hidden_vars);
            $p->set_var('filter',$filter);
        }

        if ($reportobj->_filterparms != '') {
            $base_url .= '&' . $reportobj->_filterparms;
            $filter .= "&{$reportobj->_filterparms}";
            $p->set_var('filter', $filter);  
        }

        if ($reportobj->_logging) {
            if ($reportobj->_sortdirection == SORT_ASC) {
                if ($reportobj->_logging) {
                    $message = "report class -> Sort report on field {$reportobj->_sortfield} Ascending Order, ";
                    $message .= "Page: {$reportobj->_page}, Page Count: {$reportobj->_numpages}";
                    COM_errorLog($message);
                }
            } else {
                if ($reportobj->_logging) {
                    $message = "report class -> Sort report on field {$reportobj->_sortfield} Descending Order, ";
                    $message .= "Page: {$reportobj->_page}, Page Count: {$reportobj->_numpages}";
                    COM_errorLog($message);
                }
            }
        }

        if ($reportobj->_hidedatefilter) {
            $p->set_var('showfilter','none');
        }

        $p->set_var('page_navigation',COM_printPageNavigation($base_url,$reportobj->_page, $reportobj->_numpages));

        if ($reportobj->_showrownumbers) {
            $p->set_var('label','Row');
            $p->parse('heading_fields','heading',true);
        }

        foreach ($reportobj->_fields as $key => $showstate) {
            if ($showstate) {
                $p->set_var('label',$reportobj->_headings[$key][0]);
                $p->set_var('width'," width={$reportobj->_headings[$key][1]}%");
                if ($reportobj->_sortableheadings) {
                    $p->set_var('order',$order);
                    if ($order == $reportobj->_sortorder) {
                        if ($reportobj->_sortdirection == SORT_ASC) {
                            $p->set_var('column_imageurl',"{$_CONF['layout_url']}/nexreport/images/bararrowup.gif");
                        } else {
                            $p->set_var('column_imageurl',"{$_CONF['layout_url']}/nexreport/images/bararrowdown.gif");
                        }
                        $p->parse('heading_fields','currentheading',true);
                    } else {
                        $p->parse('heading_fields','sortableheading',true);
                    }
                    $order ++;
                } else {
                    $p->parse('heading_fields','heading',true);
                }
            }
        }

        $i = 1;
        $linecount = count($reportobj->_report);
        foreach ($reportobj->_report as $line) {
            $p->set_var('cssid',(($i %2) +1));
            $field = 1;
            if ($reportobj->_showrownumbers AND $field == 1) {
                // Check if this is the last row and if it's a line showing the page totals
                if ($i == $linecount AND count($reportobj->_totalfields) > 0) {
                    $p->set_var('field_value','Total');
                } else {
                    $p->set_var('field_value',$i);
                }
                $p->parse('reportline','field');
                $field ++;
            }

            foreach ($line as $fieldvalue) {
                if ($fieldvalue > 0 OR $fieldvalue != '') {
                    $p->set_var('field_value',$fieldvalue);
                } elseif (is_numeric ($fieldvalue) and $fieldvalue == 0) {
                    $p->set_var('field_value',$fieldvalue);                    
                } else {
                    $p->set_var('field_value','&nbsp;');
                }
                if ($field == 1) {
                    $p->parse('reportline','field');
                } else {
                    $p->parse('reportline','field',true);
                }
                $field++;
            }
            $p->parse('report_lines','record',true);
            $i++;
        }

        $p->parse ('output', 'report');
        return $p->finish ($p->get_var('output'));

    }

}

?>