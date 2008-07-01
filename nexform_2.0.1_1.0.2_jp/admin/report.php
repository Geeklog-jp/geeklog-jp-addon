<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | report.php                                                                |
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

require_once("../../../lib-common.php"); // Path to your lib-common.php
require_once($_CONF['path'] . 'plugins/nexform/lib-uploadfiles.php');  // Functions for managing uploading of files
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code
require_once ($_CONF['path_system'] . 'classes/navbar.class.php');

$myvars = array('formid','id','op','result','show','page','noheader');
ppGetData($myvars,true);

if ($_GET['op'] == 'print')
    $noheader = 1;

$view_group = DB_getItem($_TABLES['formDefinitions'],'perms_view',"id='$formid'");
if (!SEC_inGroup($view_group) && !SEC_inGroup('Root')) {
    echo COM_siteHeader();
    echo COM_startBlock("Access Error");
    echo '<div style="text-align:center;padding-top:20px;">';
    echo "You do not have sufficient access.";
    echo "<p><button  onclick='javascript:history.go(-1)'>Return</button></p><br>";
    echo '</div>';
    echo COM_endBlock();
    echo COM_siteFooter();
    exit();
}

$editrights = false;
if (SEC_hasRights('nexform.edit')) {
    $edit_group = DB_getItem($_TABLES['formDefinitions'],'perms_edit',"id='$formid'");
    if (SEC_inGroup($edit_group)) {
        $editrights = true;
    }
}

if (!isset($show) OR $show == 0) {
    $show = 15;
}
// Check if this is the first page.
if (!isset($page) OR $page == 0) {
    $page = 1;
}




//$formID integer
//$fieldIDArray - blank array passed in by reference which will store the specific input field IDs form the form_fields table
//$labelArray - blank array passed in by reference which will store the specific form field's label
//$formIDArray - blank array passed in by reference which will store the specific form's ID.
//$ignoreFields - string that is a csv list of ignore-able form items (such as heading, hidden etc)

function nexform_getExportArrays($formid, &$fieldIDArray, &$labelArray, &$formIDArray, &$ignoreFields){
    nexform_export_recursive_GetToHead($formid, $fieldIDArray, $labelArray, $formIDArray, $ignoreFields, $formid, TRUE);
    nexform_export_recursive_GetToEnd($formid, $fieldIDArray, $labelArray, $formIDArray, $ignoreFields, $formid, TRUE);
}


/* Private function that is called to recurse to the HEAD of a potentially linked list of forms
 * Ceating the proper sorted output of form IDs, labels and formIDs
 * Needed for the report output mechanism
*/
function nexform_export_recursive_GetToHead($formID, &$fieldIDArray, &$labelArray, &$formIDArray, &$ignoreFields, $mainResultID, $isFirst=FALSE, $isDynamic=FALSE){
    global $_TABLES;
    $sql="select before_formID from {$_TABLES['formDefinitions']} where id=$formID";
    $res=DB_query($sql);
    list($beforeID)=DB_fetchArray($res);
    if($beforeID!='' and $beforeID!='0'){
        nexform_export_recursive_GetToHead($beforeID,$fieldIDArray, $labelArray, $formIDArray, $ignoreFields, $mainResultID);
        if(!$isFirst){
            $sql="SELECT id,label, type, field_values FROM {$_TABLES['formFields']} WHERE formid=$formID AND type NOT IN ($ignoreFields) ORDER BY fieldorder";
            $res=DB_query($sql);
            while (list ($field_id, $heading, $fieldType, $fieldValue) = DB_fetchArray($res)) {
                if($fieldType=='dynamic'){
                    $dynamicIDs=explode(",",$fieldValue);
                    nexform_export_recursive_GetToHead($dynamicIDs[0],$fieldIDArray, $labelArray, $formIDArray, $ignoreFields, $mainResultID,FALSE,TRUE);
                } else {
                    $fieldIDArray[] = $field_id;
                    $labelArray[]=$heading;
                    if($isDynamic){
                        $formIDArray[]=$formID;
                    }else{
                        $formIDArray[]=$mainResultID;
                    }

                    }
                }
            }
    } else {
        $sql="SELECT id,label, type, field_values FROM {$_TABLES['formFields']} WHERE formid=$formID AND type NOT IN ($ignoreFields) ORDER BY fieldorder";
        $res=DB_query($sql);
        while (list ($field_id, $heading, $fieldType, $fieldValue) = DB_fetchArray($res)) {
            if($fieldType=='dynamic'){
                $dynamicIDs=explode(",",$fieldValue);
                nexform_export_recursive_GetToHead($dynamicIDs[0],$fieldIDArray, $labelArray, $formIDArray, $ignoreFields, $mainResultID,FALSE,TRUE);
            } else{
                $fieldIDArray[] = $field_id;
                $labelArray[]=$heading;
                if($isDynamic) {
                        $formIDArray[]=$formID;
                    } else {
                        $formIDArray[]=$mainResultID;
                    }
            }
        }
    }
}


/* Private function that is called to recurse to the END of a potentially linked list of forms
 * Ceating the proper sorted output of form IDs, labels and formIDs
 * Needed for the report output mechanism
*/
function nexform_export_recursive_GetToEnd($formID, &$fieldIDArray, &$labelArray, &$formIDArray, &$ignoreFields, $mainResultID, $isFirst=FALSE, $isDynamic=FALSE){
    global $_TABLES;
    $sql="select after_formID from {$_TABLES['formDefinitions']} where id=$formID";
    $res=DB_query($sql);
    list($afterID)=DB_fetchArray($res);
    if($afterID!='' and $afterID!='0'){
        $sql="SELECT id,label, type, field_values FROM {$_TABLES['formFields']} WHERE formid=$formID AND type NOT IN ($ignoreFields) ORDER BY fieldorder";
        $res=DB_query($sql);
        while (list ($field_id, $heading, $fieldType, $fieldValue) = DB_fetchArray($res)) {
             if($fieldType=='dynamic') {
                    $dynamicIDs=explode(",",$fieldValue);
                    nexform_export_recursive_GetToEnd($dynamicIDs[0],$fieldIDArray, $labelArray, $formIDArray, $ignoreFields,$mainResultID,FALSE,TRUE);
             } else {
                    $fieldIDArray[] = $field_id;
                    $labelArray[]=$heading;
                    if($isDynamic){
                        $formIDArray[]=$formID;
                    }else{
                        $formIDArray[]=$mainResultID;
                    }
             }
        }
        nexform_export_recursive_GetToEnd($afterID,$fieldIDArray, $labelArray, $formIDArray, $ignoreFields,$mainResultID);
    } else {
        $sql="SELECT id,label , type, field_values FROM {$_TABLES['formFields']} WHERE formid=$formID AND type NOT IN ($ignoreFields) ORDER BY fieldorder";
        $res=DB_query($sql);
        if(!$isFirst){
            while (list ($field_id, $heading, $fieldType, $fieldValue) = DB_fetchArray($res)) {
                if($fieldType=='dynamic'){
                    $dynamicIDs=explode(",",$fieldValue);
                    nexform_export_recursive_GetToEnd($dynamicIDs[0],$fieldIDArray, $labelArray, $formIDArray, $ignoreFields,$mainResultID,FALSE,TRUE);
                } else {
                    $fieldIDArray[] = $field_id;
                    $labelArray[]=$heading;
                    if($isDynamic){
                        $formIDArray[]=$formID;
                    } else {
                        $formIDArray[]=$mainResultID;
                    }
                }

            }
        }else{


        }
    }
}


function xlsBOF() {
    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    return;
}

// Excel end of file footer
function xlsEOF() {
    echo pack("ss", 0x0A, 0x00);
    return;
}

// Function to write a Number (double) into Row, Col
function xlsWriteNumber($Row, $Col, $Value) {
    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
    echo pack("d", $Value);
    return;
}

// Function to write a label (text) into Row, Col
function xlsWriteLabel($Row, $Col, $Value ) {
    $L = strlen($Value);
    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
    echo $Value;
    return;
}


function show_formresults($formid) {
    global $_CONF,$_TABLES,$CONF_FE,$show,$page,$LANG_NAVBAR,$LANG_NAVBAR,$editrights;
    global $sdate,$sconvdate,$edate,$econvdate;

        $reportpage = new Template($_CONF['path_layout'] . 'nexform/admin');
        $reportpage->set_file (array ('page' => 'reportdetail.thtml',
                'headingfield'=>'report_headingfield.thtml',
                'records'=>'reportrecords.thtml',
                'field' => 'report_recordfield.thtml'));

        $sql = "SELECT count(*) as numpages FROM {$_TABLES['formResults']} WHERE form_id='$formid' ";
        if ($sdate > 0) {
            $sql .= "AND date >= '$sconvdate' ";
        }
        if ($edate > 0) {
            $sql .= "AND date <= '$econvdate' ";
        }
        $query = DB_query($sql);
        list ($numrecords) = DB_fetchArray($query);
        $numpages =  intval($numrecords / $show)+1;
        $offset = ($page - 1) * $show;
        $base_url = "{$_CONF['site_admin_url']}/plugins/nexform/report.php?formid={$formid}&show={$show}";
        if ($sdate > 0) {
            $base_url .= "&sdate={$sdate}";
        }
        if ($edate > 0) {
            $base_url .= "&edate={$edate}";
        }
        /* Retrieve the fields that are setup to be headings */
        $ignorefields = "'submit','cancel','file','mfile'";
        $sql = "SELECT id,label FROM {$_TABLES['formFields']} WHERE formid='$formid' AND is_resultsfield ";
        $sql .= "AND type NOT IN ($ignorefields) ORDER BY fieldorder";
        $q1 = DB_query($sql);

        $reportfields = array();
        while (list ($field_id, $heading) = DB_fetchArray($q1)) {
            $reportfields[] = $field_id;
            $reportpage->set_var ('HEADING',$heading);
            $reportpage->parse('heading_fields','headingfield',true);
        }

        $navbar = new navbar();
        if ($editrights) {
            $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
            $navbar->add_menuitem($LANG_NAVBAR['3'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php?op=editform&mode=edit&id='.$formid);
        }
        $navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?formid='.$formid);
        $excel_link = $_CONF['site_admin_url'] .'/plugins/nexform/report.php?op=excel&formid='.$formid;
        if ($sdate > 0) {
            $excel_link .= "&sdate={$sdate}";
        }
        if ($edate > 0) {
            $excel_link .= "&edate={$edate}";
        }
        $navbar->add_menuitem($LANG_NAVBAR['11'], $excel_link);

        $reportpage->set_var ('form_action',$_CONF['site_admin_url'] . '/plugins/nexform/report.php?formid='.$formid);
        $reportpage->set_var ('layout_url',$_CONF['layout_url']);
        $reportpage->set_var ('formid',$formid);
        $navbar->set_selected($LANG_NAVBAR['9']);
        $reportpage->set_var ('navbar',$navbar->generate());
        $reportpage->set_var ('page_navigation',COM_printPageNavigation($base_url,$page, $numpages));
        $reportpage->set_var ('sdate',$sdate);
        $reportpage->set_var ('edate',$edate);
        $reportpage->set_var ('LANG_DATE1','Created');
        $reportpage->set_var ('LANG_DATE2','Updated');
        $reportpage->set_var ('LANG_USER','User');
        $reportpage->set_var ('LANG_ACTION','Action');

        $sql = "SELECT id,uid,date,last_updated_date FROM {$_TABLES['formResults']} WHERE form_id='$formid' ";
        if ($sdate > 0) {
            $sql .= "AND date >= '$sconvdate' ";
        }
        if ($edate > 0) {
            $sql .= "AND date <= '$econvdate' ";
        }
        $sql .= "ORDER BY date DESC LIMIT $offset,$show";
        $query = DB_query($sql);
        $i = 2;
        while(list ($resultid,$uid,$created,$updated) = DB_fetchArray($query)) {
                $reportpage->set_var ('cssid',$i);
                if ($uid > 1) {
                    $username = DB_getItem($_TABLES['users'],"username","uid='$uid'");
                    $user_link = "<a href=\"{$_CONF['site_url']}/users.php?mode=profile&uid=$uid\">$username</a>";
                } else {
                    $user_link = "Anonymous";
                }
                $updated_date = ($updated != 0) ? strftime("%m/%d/%Y %H:%M", $updated):'N/A';
                $reportpage->set_var ('created_date',strftime("%m/%d/%Y %H:%M", $created));
                $reportpage->set_var ('updated_date',$updated_date);
                $reportpage->set_var ('user_link',$user_link);

                /* Need to do some extra work here to generate the results in the same field order as the form
                *  Since the data is in two tables and there can be multiple text area fields
                *  Retrieve all fields from both the Data and Textarea databases
                *  Combine the result data into a single array and then sort it on field_id
                *  Use the array reportfields then as the key to which data to report */

                $sorted_data=array();
                $sql = "SELECT field_id,field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid'";
                $q1 = DB_query($sql);
                while (list ($field_id, $field_data) = DB_fetchArray($q1)) {
                    $sorted_data[$field_id] = $field_data;
                }
                $sql = "SELECT field_id, field_data FROM {$_TABLES['formResText']} WHERE result_id='$resultid'";
                $q2 = DB_query($sql);
                while (list ($field_id, $field_data) = DB_fetchArray($q2)) {
                    $sorted_data[$field_id] = $field_data;
                }
                $k = 1;
                foreach ($reportfields as $key) {
                    if ($k == 1) {
                        $reportpage->set_var ('field_data',$sorted_data[$key]);
                        $reportpage->parse('record_fields','field');
                    } else {
                        $reportpage->set_var ('field_data',$sorted_data[$key]);
                        $reportpage->parse('record_fields','field',true);
                    }
                    $k++;
                }

                $detail_link = "<a href=\"{$_CONF['site_admin_url']}/plugins/nexform/report.php?op=view&formid=$formid&id=$resultid\">[View]</a>";
                $print_link = "&nbsp;<a href=\"{$CONF_FE['public_url']}/print.php?op=print&result=$resultid&id=$formid\" target=\"printwindow\">[Print]</a>";
                $delete_link = "&nbsp;<a href=\"{$_CONF['site_admin_url']}/plugins/nexform/report.php?op=delete&formid=$formid&id=$resultid\">[Delete]</a>";
                $edit_link = "&nbsp;<a href=\"{$_CONF['site_admin_url']}/plugins/nexform/report.php?op=edit&formid=$formid&id=$resultid\">[Edit]</a>";
                $reportpage->set_var ('print_link',$print_link);
                $reportpage->set_var ('detail_link',$detail_link);
                if ($editrights) {
                    $reportpage->set_var ('delete_link',$delete_link);
                    $reportpage->set_var ('edit_link',$edit_link);
                } else {
                    $reportpage->set_var ('delete_link','');
                    $reportpage->set_var ('edit_link','');
                }
                $i = ($i==2? 1 : 2);
                $reportpage->parse('report_records','records',true);
        } // while
        $reportpage->parse ('output', 'page');
        $retval =  $reportpage->finish ($reportpage->get_var('output'));

        return $retval;

}




/* Main Code begin */


$LANG_NAVBAR = $LANG_FRM_ADMIN_NAVBAR;

$formname = DB_getItem($_TABLES['formDefinitions'],'name',"id='$formid'");
$sdate = COM_applyfilter($_REQUEST['sdate']);
$edate = COM_applyfilter($_REQUEST['edate']);

/* TODO: Fix date convert. I changed the format to MM/DD/YYYY (Blaine: Aug 23/2005) */

if ($sdate != '') {
    $sconvdate = ppConvertDate($sdate);
}
if ($edate != '') {
    $econvdate = ppConvertDate($edate);
}

switch($op){
    case 'view':
        if ($noheader == 0) {
            $navbar = new navbar();
            if ($editrights) {
                $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
            }
            $navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?formid='.$formid);
            $navbar->add_menuitem($LANG_NAVBAR['10'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?op=view&formid='.$formid.'&id='.$id);
            $navbar->set_selected($LANG_NAVBAR['10']);
            $report_results .= $navbar->generate();
        }
        $report_results .= nexform_showform($formid,$id,'review');
        break;

    case 'print':
        $report_results  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">' . LB;
        $report_results .= '<html>' . LB;
        $report_results .= '<head>' . LB;
        $report_results .= "<link rel=\"stylesheet\"  type=\"text/css\" href=\"{$_CONF['layout_url']}/style.css\">" . LB;
        $report_results .= '</head><body>' . LB;
        $report_results .= nexform_showform($formid,$id,'print');
        $report_results .= '</body></html>' . LB;
        break;

    case 'edit':
        $navbar = new navbar();
        if ($editrights) {
            $navbar->add_menuitem($LANG_NAVBAR['1'], $_CONF['site_admin_url'] .'/plugins/nexform/index.php');
        }
        $navbar->add_menuitem($LANG_NAVBAR['9'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?formid='.$formid);
        $navbar->add_menuitem($LANG_NAVBAR['10'], $_CONF['site_admin_url'] .'/plugins/nexform/report.php?op=view&formid='.$formid.'&id='.$id);

        $navbar->set_selected($LANG_NAVBAR['10']);
        $report_results .= $navbar->generate();
        if ($editrights) {
            $report_results .= nexform_showform($formid,$id,'edit');
        } else {
            $report_results .= nexform_showform($formid,$id,'view');
        }
        break;

    case 'update':
        if ($editrights AND DB_count($_TABLES['formResults'],'id',$id) > 0) {
            nexform_dbupdate($formid,$id);
            $report_results = show_formresults($formid);
        } else {
            $report_results = show_formresults($formid);
        }
        break;

    case 'delete':
        if ($editrights) {
            DB_query("DELETE FROM {$_TABLES['formResults']} WHERE id='$id'");
        }
        $report_results = show_formresults($formid);
        break;

    case 'excel':

        header ("Expires: 0");
        header ("Pragma: no-cache");
        header ('Content-type: application/x-msexcel');
        header ("Content-Disposition: attachment; filename={$formname}.xls" );

        xlsBOF();   // begin Excel stream
        xlsWriteLabel(0,0,"Date");
        xlsWriteLabel(0,1,"User");

        $ignoreFields = "'submit','cancel','file','mfile','heading'";

        //we're going to use 3 control arrays to simplify the export mechanism.
        //the showForm routine's logic could be peared down to handle this, however
        //the simplicity of this routine makes it lean and reusable for row based output mechanisms.
        $fieldIDArray= array();
        $labelArray= array();
        $formIDArray= array();

        nexform_getExportArrays($formid, $fieldIDArray, $labelArray, $formIDArray, $ignoreFields);

        $i=2;
        foreach ($labelArray as $label){
            xlsWriteLabel(0,$i,$label);
            $i++;
            }
        $cntr=0;
        $sql = "SELECT id,uid, date,related_results FROM {$_TABLES['formResults']} WHERE form_id='$formid' ";
        if ($sdate > 0) {
            $sql .= "AND date >= '$sconvdate' ";
        }
        if ($edate > 0) {
            $sql .= "AND date <= '$econvdate' ";
        }
        $sql .= "ORDER BY date DESC";
        $query = DB_query($sql);
        $row = 1;
        //this outer while loop is responsible for creating our left pivot result IDs
        while(list ($resultid,$uid,$date,$relatedResults) = DB_fetchArray($query)) {
                $date = strftime("%Y-%m-%d %H:%M", $date);
                if ($uid > 1) {
                    $username = DB_getItem($_TABLES['users'],"username","uid='$uid'");
                } else {
                    $username = "Anonymous";
                }
                xlsWriteLabel($row,0,$date);
                xlsWriteLabel($row,1,$username);
                $col = 2;
                $cntr=0;
                foreach ($fieldIDArray as $fieldID){ //this foreach creates the PIVOT effect in the output
                    $label=$labelArray[$cntr];
                    $formID=$formIDArray[$cntr];

                    $sql="SELECT type from {$_TABLES['formFields']} where id={$fieldID}";
                    $typeRes=DB_query($sql);
                    list($typeOfField)=DB_fetchArray($typeRes);
                    if( $typeOfField!='textarea2' && $typeOfField!='textarea1' ){//if its not large text data
                        if($formID!=$formid and $relatedResults!=''){
                                $sql="SELECT id FROM {$_TABLES['formResults']} where form_id={$formID} and id in ({$relatedResults})";
                                $q3=DB_query($sql);
                                list($newResultID)=DB_fetchArray($q3);
                                $sql = "SELECT field_data FROM {$_TABLES['formResData']} WHERE result_id='$newResultID' and field_id='$fieldID'";
                        } else {
                            $sql = "SELECT field_data FROM {$_TABLES['formResData']} WHERE result_id='$resultid' and field_id='$fieldID'";
                        }
                        $q1 = DB_query($sql);
                        list($data)=DB_fetchArray($q1);
                        if($typeOfField=='mtxt'){
                            $data=str_replace('|',' ',$data);
                        }
                    } else {   //we're trying to report on large text data
                        if($formID!=$formid and $relatedResults!=''){
                            $sql="SELECT id FROM {$_TABLES['formResults']} where form_id={$formID} and id in ({$relatedResults})";
                            $q3=DB_query($sql);
                            list($newResultID)=DB_fetchArray($q3);
                            $sql = "SELECT field_data FROM {$_TABLES['formResText']} WHERE result_id='$newResultID' and field_id='$fieldID'";

                        } else {
                            $sql = "SELECT field_data FROM {$_TABLES['formResText']} WHERE result_id='$resultid' and field_id='$fieldID'";
                        }
                        $q2 = DB_query($sql);
                        list($data)=DB_fetchArray($q2);
                        }

                    $sql="SELECT field_values from {$_TABLES['formFields']} where id={$fieldID} and value_by_function=1";
                    $typeRes=DB_query($sql);
                    list($fieldValue)=DB_fetchArray($typeRes);
                    if($fieldValue!='') {   //we have an alist or custom function
                        $fieldValue=str_replace('[','',$fieldValue);
                        $fieldValue=str_replace(']','',$fieldValue);
                        $funcArray=explode(':',$fieldValue);
                        if(count($funcArray)>1){
                            if(strtolower($funcArray[0])=='alist'){
                                //fetch the lookup list value
                                $data = nexlistOptionList( 'read', '', 4, 0,$data);
                            } else {
                                if (function_exists($funcArray[0])) {
                                    $data = $funcArray[0]($funcArray[1],$fieldID, $data);
                                }

                            }
                        }
                    }

                    if (is_numeric($data)) {
                            xlsWriteNumber($row,$col,$data);
                    } else {
                            $data = strip_tags($data);
                            $data = str_replace("\r\n",'',$data);
                            xlsWriteLabel($row,$col,$data);
                    }
                    $col++;
                    $cntr++;
                }//end foreach
                $row++;
            }//end while
        xlsEOF(); // close the stream
        break;
        exit;


    default:
        $report_results = show_formresults($formid);
        break;
}

if ($noheader == 0) {
    echo COM_siteHeader();
    echo COM_startBlock('Form:&nbsp;"'.$formname.'" Summary Results for records posted','','nexform/admin/pluginheader.thtml');
}
echo $report_results;
if ($noheader == 0) {
    echo COM_endBlock('nexform/admin/pluginfooter.thtml');
    echo COM_siteFooter();
}

?>