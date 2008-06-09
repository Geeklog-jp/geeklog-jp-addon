<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | libconsole.php                                                            |
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

// Called to format and generate the Navbar
function taskconsoleShowNavbar($selected='My Tasks') {
    global $_USER,$_CONF,$optLinkVars,$usermodeUID;

    $retval = '<div id="navbar1" style="display:;">';
    $navbar = new navbar;
    if ($_USER['uid'] > 1) {
        $navbar->add_menuitem('My Tasks',$_CONF['site_url'] .'/nexflow/index.php?op=mytasks'. $optLinkVars);
        $navbar->add_menuitem('My Flows',$_CONF['site_url'] .'/nexflow/index.php?op=myprojects'. $optLinkVars);
    }
    $navbar->add_menuitem('All Flows',$_CONF['site_url'] .'/nexflow/index.php?op=allprojects' . $optLinkVars);
    if (SEC_inGroup('nexflow Admin')) {
        $navbar->add_menuitem('Outstanding Tasks',$_CONF['site_admin_url'] .'/plugins/nexflow/outstanding.php?taskuser='.$usermodeUID);
    }
    if ($_USER['uid'] > 1) {
        $navbar->add_menuitem('Start New Process',$_CONF['site_url'] .'/nexflow/newprocess.php?taskuser='.$usermodeUID);
    }      
    $navbar->set_selected($selected);
    $retval .= $navbar->generate();
    $retval .= '</div>';

    return $retval;
 
}

function nf_updateStatusLog($id,$formid,$message) {
    global $_TABLES,$_USER;

    $sql = "INSERT INTO {$_TABLES['nfproject_timestamps']} (project_id,project_formid,statusmsg,timestamp,uid) ";
    $sql .= "VALUES ('$id','$formid','$message',UNIX_TIMESTAMP(),'{$_USER['uid']}') ";
    DB_query($sql);
}


function nf_taskhistoryComplete($id,$status) {
    global $_TABLES;

    $sql = "UPDATE {$_TABLES['nfproject_taskhistory']} SET date_completed = UNIX_TIMESTAMP(), ";
    $sql .= "status='$status' WHERE task_id='$id'";
    DB_query($sql);
}

function count_project_records($cntarray,$state,$division,$product) {
    $B = array();    
    $projectcnt = 0;    
    foreach ($cntarray as $A) {
        if ($A['state'] == $state) {
            if ($A['division'] == $division) {
                if ($A['product'] == $product) {
                    if ($A['product'] == $product) {
                        $match = true;
                        $productcnt++;
                    } elseif ($match) {
                        return $projectcnt;
                    }
                    $projectcnt++;
                }
            }
        }
    }
    return $projectcnt;    
}

// Function used to search for a value in project forms based on defined forms and fields to searchoption
function nfCustomSearchProjects($project_id,$searchopt,$searchkey) {
    global $_TABLES,$NF_CONF;

     $sforms = implode(',',array_keys($NF_CONF['searchoptions'][$searchopt]));
     $sfields = implode(',',array_values($NF_CONF['searchoptions'][$searchopt]));
     $q1 = DB_query("SELECT results_id FROM {$_TABLES['nfproject_forms']} WHERE project_id='$project_id' AND form_id in ($sforms)");
     while (list ($results_id) = DB_fetchArray($q1)) {
        $sql = "SELECT field_data FROM {$_TABLES['formResData']} WHERE result_id='$results_id' AND field_id in ($sfields)";
        $q2 = DB_query($sql);
        if (DB_numRows($q2) > 0) {
            list ($field_data) = DB_fetchArray($q2);
            if (strpos($field_data,$searchkey) !== FALSE) {
                return true;
            }
        }
     }
     return false;

}


function display_wfFlowsTabular($uid=0,$allflows=true){
    global $_TABLES, $_CONF, $_USER, $CONF_NF, $LANG_NF00, $formstatus_options, $op  ;

    $searchString=COM_applyFilter($_POST['srchText']);
    $srchFilter=COM_applyFilter($_POST['srchFilter']);
    $srchOrderBy=COM_applyFilter($_POST['srchOrderBy']);
    $srchOrderDir=COM_applyFilter($_POST['srchOrderDir']);
    $idForAppGroup=COM_applyFilter($_REQUEST['idAppGroup'],true);
    $srchStatus=COM_applyFilter($_REQUEST['srchStatus']);
    $projectProcesses = array();

    $holdTaskLink = '<a href="#" onclick="ajaxPutProcessOnHold(%s,%s);"><img src="' . $_CONF['layout_url'] . '/nexflow/images/onhold.png" border=0 alt="%s"></a>';

    $tmplt = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
    $tmplt->set_file (array (
        'page'              =>      'wfreport_layout.thtml',
        'projectRow'        =>      'wfreport_project_row.thtml',
        'regularRow'        =>      'wfreport_regular_row.thtml',
        'outstandingtasks'   =>     'wfreport_outstanding.thtml',
        'javascript'        =>      'javascript/taskconsole.thtml'));

    $tmplt->set_var('srchFilter',$LANG_NF00['srchFilter']);
    $tmplt->set_var('srchFilterTitle',$LANG_NF00['srchFilterTitle']);
    $tmplt->set_var('srchFilterReqDesc',$LANG_NF00['srchFilterReqDesc']);
    $tmplt->set_var('srchFilterPrjName',$LANG_NF00['srchFilterPrjName']);
    $tmplt->set_var('srchDoSearch',$LANG_NF00['srchDoSearch']);
    $tmplt->set_var('processFilter',$LANG_NF00['processFilter']);
    $tmplt->set_var('chooseAll',$LANG_NF00['chooseAll']);
    $tmplt->set_var('srchText',$searchString);
    $tmplt->set_var('srchOrderDir',$srchOrderDir);
    $tmplt->set_var ('srchProcessRow', 'visible');
    $tmplt->set_var('whichop', $op);
    $tmplt->set_var('userid',$uid);
    $tmplt->set_var('site_url',$_CONF['site_url']);
    $actionurl = $_CONF['site_url'] .'/nexflow/index.php';

    //search/filter area setup
    $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup');

    $tmplt->set_var('show_selectappfield','none');
    $tmplt->set_var('show_searchtextfield','');
    switch(strtolower($srchFilter)){
        case 'appgroup':
            $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup',$idForAppGroup);
            $tmplt->set_var('srchselappgroup','selected');
            $tmplt->set_var('show_selectappfield','');
            $tmplt->set_var('show_searchtextfield','none');
        case 'title':
            $tmplt->set_var('srchseltitle','selected');
            break;
        case 'desc':
            $tmplt->set_var('srchselreqdesc','selected');
            break;
    }
    $tmplt->set_var('srchApplicationGroups', $appGroupDDL);

    $relatedProcesses = '';
    $sql = "SELECT related_processes from {$_TABLES['nfprojects']}";
    $res = DB_query($sql);
    while($B = DB_fetchArray($res)) {
        if($B['related_processes'] != '') {
            if($relatedProcesses == '') {
                $relatedProcesses = $B['related_processes'];
            } else {
                $relatedProcesses .= ','.$B['related_processes'];
            }
        }
    }
    
    //this statement produces a view from which we can determine all of the rows this USER has initiated
    $sql  = "SELECT DISTINCT a.id as nf_processID, a.nf_templateID, a.complete, a.initiator_uid, a.initiatedDate, ";
    $sql .= "a.completedDate, b.templateName,  f.description as prjDescription,f.id as project_id, a.customFlowName ";
    $sql .= "FROM {$_TABLES['nfprocess']} a ";
    $sql .= "INNER JOIN {$_TABLES['nftemplate']} b ON a.nf_templateID = b.id ";
    $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c ON b.id = c.nf_templateID ";
    $sql .= "INNER JOIN {$_TABLES['nfqueue']} d ON (d.nf_templateDataId = c.id AND d.nf_processID = a.id) ";
     
    if($srchFilter=='appgroup'){
        $sql .= "INNER JOIN {$_TABLES['nfappgroups']} i on b.AppGroup=i.id ";
    }
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfprocessvariables']} e ON ( e.nf_processid = a.id AND c.argumentvariable = e.nf_templateVariableId ) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfprojects']} f on (f.wf_process_id = a.id) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nftemplatevariables']} g on (e.nf_templateVariableID=g.id) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfproject_taskhistory']} h on h.process_id=a.id ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfproductionassignments']} j ON (j.task_id = d.id ) ";
    $sql .= "WHERE  1=1 ";
    if (!$allflows) {
        $sql .= "AND (d.uid = '{$uid}' OR (e.variableValue = '{$uid}' AND g.variableName='INITIATOR') OR h.assigned_uid='{$uid}' OR j.uid='{$uid}')  ";
    }

    $chksql = '';
    //add in the filter items here:
    switch($srchStatus){
        case  1:        // Active
            $srchStatus=0;  //since the COM_applyfilter makes everything zero, we need to change this from 1 to 0 in code.
            $tmplt->set_var('srchselactive', 'selected');
            $chksql .= ' a.complete=0 ';
            break;
        case 2:         // On Hold
            $tmplt->set_var('srchselonhold', 'selected');
            $chksql .= ($chksql == '') ? ' a.complete=3 ' : ' OR a.complete=3 ';
            break;
        case 3:         //  Completed
            $tmplt->set_var('srchselcompleted', 'selected');
            $chksql .= ($chksql == '') ? ' a.complete=1 ' : ' OR a.complete=1 ';
            break;
        case 4:         // Regenerated
            $tmplt->set_var('srchselregenerated', 'selected');
            $chksql .= ($chksql == '') ? ' a.complete=2 ' : ' OR a.complete=2 ';
            break;
        case -1:
            $tmplt->set_var('srchselany', 'selected');
            break;
    }

    if($chksql!='') {
        $sql =$sql . ' AND ('. $chksql . ')';
    }

    //now to create the dynamic search string
    if($srchFilter == 'appgroup') {
            if($searchString != '') {
                $sql .= " AND (b.templateName like '%{$searchString}%' or c.taskname like '%{$searchString}%' or f.description like '%{$searchString}%') ";
            }
            $sql .= " AND (b.AppGroup='{$idForAppGroup}') ";
            $tmplt->set_var('srchselappgroup','selected');

    } elseif($searchString != '') {

        switch(strtolower($srchFilter)){
        case 'title':
            $sql .= " AND (b.templateName like '{$searchString}%' or c.taskname like '{$searchString}%') ";
            $tmplt->set_var('srchseltitle','selected');
            break;
        case 'desc':
            $sql .= " AND (f.description like '%{$searchString}%') ";
            $tmplt->set_var('srchselreqdesc','selected');
            break;

        default:
            $sql .= " AND (b.templateName like '%{$searchString}%' or c.taskname like '%{$searchString}%' or f.description like '%{$searchString}%') ";
            break;
        }
    }

    if($relatedProcesses!=''){
        $sql .= " AND a.id not in ({$relatedProcesses})";
    }

    if($srchOrderBy != '') {
        if($srchOrderBy == 'flow') {
            $sql .= " ORDER BY b.templateName, f.description";
        } else{
            $sql .= " ORDER BY {$srchOrderBy}";
        }
        if($srchOrderDir == 'ASC') {
            $tmplt->set_var('srchOrderDir',"DESC");
            $sql .= " DESC";
        } else {
            $tmplt->set_var('srchOrderDir',"ASC");
            $sql .= " ASC";
        }
    } else {
        $sql .= " ORDER BY project_id DESC ";
    }

    //cycle thru each of these determining if they have a project associated with them or not.
    //if there IS a project, use the original project type output.. else, just do a simple output of history
    $res = DB_query($sql);
    $nrows = DB_numRows($res);
    $i=0;
    while($A = DB_fetchArray($res))  {    //cycle thru the result set.

        if($A['complete'] != 2) { //don't show the item if its a regen'd process.
            $i++;
            $sql = "SELECT a.id ,a.wf_process_id , a.wf_task_id ,a.originator_uid ,a.description ,a.status ,a.prev_status ,a.related_processes ";
            $sql .= "FROM {$_TABLES['nfprojects']} a where a.wf_process_id='{$A['nf_processID']}' LIMIT 1";
                    
            $projRes = DB_query($sql);
            $rowid=$i;
            $project_id = $A['project_id'];
            
            if(DB_numRows($projRes) > 0) {
                //we have a row that contains a project entry.
                if (SEC_hasRights('nexflow.admin')) {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Re-activate'));
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Toggle On-Hold'));
                        $tmplt->set_var ('isOnHold','none');

                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                } else {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('isOnHold','none');

                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                }

                $tmplt->set_var ('hold_icon','onhold2.png');
                $tmplt->set_var ('imgset',$_CONF['layout_url'] . '/nexflow/images');
                $tmplt->set_var('project_id',$project_id);
                $tmplt->set_var('rowid',$rowid);
                
                $tmplt->set_var('whichID',$A['nf_processID']);
                $tmplt->set_var('flowStatus',$CONF_NF['processstatus'][$A['complete']]);
                if($A['pid'] != 0){
                    $tmplt->set_var('isRegenerated','<span style="color:red">[R]</span>');
                }

                $prjDesc = '';
                if($A['prjDescription'] == ''){
                    $prjDesc = $A['templateName'];
                } else {
                    $prjDesc = $A['prjDescription'];
                }
                if($A['customFlowName'] != ''){
                    $prjDesc = $A['customFlowName'];
                }
                $tmplt->set_var('prjDescription',$prjDesc);
                
                $tmplt->set_var('initiatedDate',$A['initiatedDate']);
                if($A['completedDate'] =='' || $A['completedDate'] == NULL){
                    $tmplt->set_var('completedDate','Active');
                } else {
                    $tmplt->set_var('completedDate',$A['completedDate']);
                }
                $tmplt->set_var('prjDetailsIMG','<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Details">');
                $actionurl = $_CONF['site_url'] .'/nexflow/index.php';

                $tmplt->parse ('results', 'projectRow',true);
            
            } else {
                //we have a row that does NOT have a project entry            
                if (SEC_hasRights('nexflow.admin')) {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Re-activate'));
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Toggle On-Hold'));
                        $tmplt->set_var ('isOnHold','none');
                        
                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');                    
                    }
                } else{
                    if($A['complete'] == 3){
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('isOnHold','none');

                    } else{
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                }

                $tmplt->set_var ('imgset',$_CONF['layout_url'] . '/nexflow/images');
                $tmplt->set_var('rowid',$rowid);

                $tmplt->set_var('whichID',$A['nf_processID']);
                $tmplt->set_var('flowStatus',$CONF_NF['processstatus'][$A['complete']]);
                if($A['pid'] != 0){
                    $tmplt->set_var('isRegenerated','<span style="color:red">[R]</span>');
                }

                $prjDesc = '';
                if($A['prjDescription'] == ''){
                    $prjDesc = $A['templateName'];
                } else {
                    $prjDesc = $A['prjDescription'];
                }
                if($A['customFlowName'] != ''){
                    $prjDesc = $A['customFlowName'];
                }
                $tmplt->set_var('prjDescription',$prjDesc);

                $tmplt->set_var('initiatedDate',$A['initiatedDate']);
                if($A['completedDate'] =='' || $A['completedDate'] == NULL){
                    $tmplt->set_var('completedDate','Active');
                } else {
                    $tmplt->set_var('completedDate',$A['completedDate']);
                }
                $tmplt->set_var('prjDetailsIMG','<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Details">');

                $A['nf_processID'] = NXCOM_filterInt($A['nf_processID']);

                $projectProcesses = explode(',',$relatedProcesses);
                nf_formatOutstandingTasks($A['nf_processID'],$projectProcesses,$tmplt);

                $tmplt->parse ('results', 'regularRow',true);
            }

        } else {
            $thisrec = $tmplt->get_var('outstandingtask_records');
            if($thisrec != '') {
                $tmplt->set_var('outstandingtasks','');
            }

        }
    }//end while there are result records

    $tmplt->set_var('beginCommentOut','/*');
    $tmplt->set_var('endCommentOut','*/');
    $tmplt->parse ('javascript_code', 'javascript');
    $tmplt->parse ('output', 'page',true);
    echo $tmplt->finish ($tmplt->get_var('output'));
      
}



function display_wfFlowsStatus($uid=0,$allflows=true){
    global $_TABLES, $_CONF, $_USER, $CONF_NF, $LANG_NF00, $formstatus_options, $op;
    
    $searchString = COM_applyFilter($_POST['srchText']);
    $srchFilter = COM_applyFilter($_POST['srchFilter']);
    $srchOrderBy = COM_applyFilter($_POST['srchOrderBy']);
    $idForAppGroup = COM_applyFilter($_REQUEST['idAppGroup'],true);
    $srchOrderDir = COM_applyFilter($_POST['srchOrderDir']);

    $tmplt = new Template($_CONF['path_layout'] . 'nexflow/taskconsole');
    $tmplt->set_file (array (
        'page'              =>      'wfreport_layout.thtml',
        'pageSections'      =>      'wfreport_page_sections.thtml',
        'projectRow'        =>      'wfreport_project_row.thtml',
        'regularRow'        =>      'wfreport_regular_row.thtml',
        'outstandingtasks'  =>      'wfreport_outstanding.thtml',
        'javascript'        =>      'javascript/taskconsole.thtml'));

    $tmplt->set_var('srchFilter',$LANG_NF00['srchFilter']);
    $tmplt->set_var('srchFilterTitle',$LANG_NF00['srchFilterTitle']);
    $tmplt->set_var('srchFilterReqDesc',$LANG_NF00['srchFilterReqDesc']);
    $tmplt->set_var('srchFilterPrjName',$LANG_NF00['srchFilterPrjName']);
    $tmplt->set_var('srchDoSearch',$LANG_NF00['srchDoSearch']);
    $tmplt->set_var('processFilter',$LANG_NF00['processFilter']);
    $tmplt->set_var('chooseAll',$LANG_NF00['chooseAll']);
    $tmplt->set_var('srchTxt',$searchString);
    $tmplt->set_var('srchOrderDir',$srchOrderDir);
    $tmplt->set_var('srchProcessRow', 'hidden');
    $tmplt->set_var('whichop', $op);
    $tmplt->set_var('userid',$uid);
    $tmplt->set_var('site_url',$_CONF['site_url']);
    $tmplt->set_var('col2width','width="60%"');
    $tmplt->set_var('statusvisible','none');
    $tmplt->set_var('flowrecord_initialstate','none');

    //search/filter area setup
    $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup');

    $tmplt->set_var('show_selectappfield','none');
    $tmplt->set_var('show_searchtextfield','');
    switch(strtolower($srchFilter)){
        case 'appgroup':
            $appGroupDDL = COM_optionList($_TABLES['nfappgroups'],'id,AppGroup',$idForAppGroup);
            $tmplt->set_var('srchselappgroup','selected');
            $tmplt->set_var('show_selectappfield','');
            $tmplt->set_var('show_searchtextfield','none');
        case 'title':
            $tmplt->set_var('srchseltitle','selected');
            break;
        case 'desc':
            $tmplt->set_var('srchselreqdesc','selected');
            break;
    }
    $tmplt->set_var('srchApplicationGroups', $appGroupDDL);

    
    $holdTaskLink = '<a href="#" onclick="ajaxPutProcessOnHold(%s,%s);"><img src="' . $_CONF['layout_url'] . '/nexflow/images/onhold.png" border=0 alt="%s"></a>';
    $actionurl = $_CONF['site_url'] .'/nexflow/index.php';
    
    $projectProcesses = array();
    $relatedProcesses = '';
    $sql = "SELECT related_processes from {$_TABLES['nfprojects']} ";
    $res = DB_query($sql);
    while($B = DB_fetchArray($res)){
        if($B['related_processes'] != ''){
            if($relatedProcesses == ''){
                $relatedProcesses = $B['related_processes'];
            } else{
                $relatedProcesses .= ','.$B['related_processes'];
            }
        }
    }
    $uid = NXCOM_filterInt($uid);

    $sql  = "SELECT DISTINCT a.id as nf_processID, a.nf_templateID, a.complete, a.initiator_uid, a.initiatedDate, ";
    $sql .= "a.completedDate, b.templateName,  f.description as prjDescription,f.id as project_id, a.customFlowName ";
    $sql .= "FROM {$_TABLES['nfprocess']} a ";
    $sql .= "INNER JOIN {$_TABLES['nftemplate']} b ON a.nf_templateID = b.id ";
    $sql .= "INNER JOIN {$_TABLES['nftemplatedata']} c ON b.id = c.nf_templateID ";
    $sql .= "INNER JOIN {$_TABLES['nfqueue']} d ON (d.nf_templateDataId = c.id AND d.nf_processID = a.id) ";
     
    if($srchFilter == 'appgroup'){
        $sql .= "INNER JOIN {$_TABLES['nfappgroups']} i on b.AppGroup=i.id ";
    }
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfprocessvariables']} e ON ( e.nf_processid = a.id AND c.argumentvariable = e.nf_templateVariableId ) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfprojects']} f on (f.wf_process_id = a.id) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nftemplatevariables']} g on (e.nf_templateVariableID=g.id) ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfproject_taskhistory']} h on h.process_id=a.id ";
    $sql .= "LEFT OUTER JOIN {$_TABLES['nfproductionassignments']} j ON (j.task_id = d.id ) ";
    $sql .= "WHERE  1=1 ";
    if (!$allflows) {
        $sql .= "AND (d.uid = '{$uid}' OR (e.variableValue = '{$uid}' AND g.variableName='INITIATOR') OR h.assigned_uid='{$uid}' OR j.uid='{$uid}')  ";
    }
    $chksql = '';
    //now to create the dynamic search string
    if($srchFilter == 'appgroup'){
            if($searchString != ''){
                $sql .= " AND (b.templateName like '%{$searchString}%' or c.taskname like '%{$searchString}%' or f.description like '%{$searchString}%') ";
            }

            $sql .= " AND (b.AppGroup='{$idForAppGroup}') ";
            $tmplt->set_var('srchselappgroup','selected');
    } elseif($searchString!=''){
        switch(strtolower($srchFilter)){
        case 'title':
            $sql .= " AND (b.templateName like '{$searchString}%' or c.taskname like '{$searchString}%') ";
            $tmplt->set_var('srchseltitle','selected');
            break;
        case 'desc':
            $sql .= " AND (f.description like '{$searchString}%') ";
            $tmplt->set_var('srchselreqdesc','selected');
            break;
        default:
            $sql .= " AND (b.templateName like '%{$searchString}%' or c.taskname like '%{$searchString}%' or f.description like '%{$searchString}%') ";
            break;
        }
    }

    if($relatedProcesses != ''){
        $sql .= " AND a.id not in ({$relatedProcesses})";
    }

    $res = DB_query($sql);
    $nrows = DB_numRows($res);
    $i = 0;
    $timesThru = 0;
    $completedRecords = 0;
    $activeRecords = 0;

    if($nrows > 0)  {

        while($A = DB_fetchArray($res)) {     //cycle thru the Active result set.

            $i+=1;
            $rowid=$i;
            $project_id=$A['project_id'];

            if($A['project_id'] != '' && $A['project_id'] != NULL) {
                if (SEC_hasRights('nexflow.admin')) {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Re-activate'));
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Toggle On-Hold'));
                        $tmplt->set_var ('isOnHold','none');
                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                } else {
                    if($A['complete'] == 3) {
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('isOnHold','none');
                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                }
                $tmplt->set_var ('hold_icon','onhold2.png');
                $tmplt->set_var ('imgset',$_CONF['layout_url'] . '/nexflow/images');
                $tmplt->set_var('whichID',$A['nf_processID']);
                $tmplt->set_var('flowStatus',$CONF_NF['processstatus'][$A['complete']]);
                if($A['pid']!=0) {
                    $tmplt->set_var('isRegenerated','<span style="color:red">[R]</span>');
                }

                $tmplt->set_var('project_id',$project_id);
                $tmplt->set_var('rowid',$rowid);

                $prjDesc = '';
                if($A['prjDescription'] == ''){
                    $prjDesc = $A['templateName'];
                } else {
                    $prjDesc = $A['prjDescription'];
                }

                if($A['customFlowName'] != ''){
                    $prjDesc = $A['customFlowName'];
                }
                $tmplt->set_var('prjDescription',$prjDesc);

                $tmplt->set_var('initiatedDate',$A['initiatedDate']);
                if($A['completedDate'] == '' || $A['completedDate']==NULL){
                    $tmplt->set_var('completedDate','Active');
                } else {
                    $tmplt->set_var('completedDate',$A['completedDate']);
                }
                $tmplt->set_var('prjDetailsIMG','<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Details">');
                $actionurl = $_CONF['site_url'] .'/nexflow/index.php';

                if($A['complete'] == 0 || $A['complete'] == 3 ) {
                    $tmplt->set_var('state','active');
                    $tmplt->parse ('prjrowOutput', 'projectRow',true);
                    $activeRecords++;
                } else {
                    $tmplt->set_var('state','complete');
                    $tmplt->parse ('completedOutput', 'projectRow',true);
                    $completedRecords++;
                }

            } else {

                if (SEC_hasRights('nexflow.admin')) {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Re-activate'));
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('onHoldIMG', sprintf($holdTaskLink,$i,$A['nf_processID'],'Toggle On-Hold'));
                        $tmplt->set_var ('isOnHold','none');
                    } else{
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }

                } else {
                    if($A['complete'] == 3){
                        $tmplt->set_var ('isOnHold','');
                    } elseif($A['complete'] != 1){
                        $tmplt->set_var ('isOnHold','none');
                    } else {
                        $tmplt->set_var ('onHoldIMG', '');
                        $tmplt->set_var ('isOnHold','none');
                    }
                }
                $tmplt->set_var ('hold_icon','onhold2.png');
                $tmplt->set_var ('imgset',$_CONF['layout_url'] . '/nexflow/images');
                $tmplt->set_var('rowid',$i);

                //we have a row that does NOT have a project entry
                $tmplt->set_var('whichID',$A['nf_processID']);
                $tmplt->set_var('flowStatus',$CONF_NF['processstatus'][$A['complete']]);
                if($A['pid']!=0){
                    $tmplt->set_var('isRegenerated','<span style="color:red">[R]</span>');
                }
                $tmplt->set_var('prjDescription',$A['templateName']);
                $tmplt->set_var('initiatedDate',$A['initiatedDate']);
                if($A['completedDate'] =='' || $A['completedDate'] == NULL) {
                    $tmplt->set_var('completedDate','Active');
                } else {
                    $tmplt->set_var('completedDate',$A['completedDate']);
                }
                $tmplt->set_var('prjDetailsIMG','<img src="'.$_CONF['layout_url'].'/nexflow/images/details.png" border="0" TITLE="View Details">');

                $projectProcesses = explode(',',$relatedProcesses);
                nf_formatOutstandingTasks($A['nf_processID'],$projectProcesses,$tmplt);

                if($A['complete'] == 0 || $A['complete'] == 3 ) {
                    $tmplt->set_var('state','active');
                    $tmplt->parse ('prjrowOutput', 'regularRow',true);
                    $activeRecords++;
                } else {
                    if($A['complete'] != 2 ){ //this strips off the regen'd task
                        $tmplt->set_var('state','complete');
                        $tmplt->parse ('completedOutput', 'regularRow',true);
                        $completedRecords++;
                    }
                }
            }
        }
    }

    $tmplt->set_var('active_label',"Active&nbsp;($activeRecords)");
    $tmplt->set_var('completed_label',"Completed&nbsp;($completedRecords)");

    $tmplt->set_var ('showActive',$otpt.$tmplt->get_var('prjrowOutput'));
    $tmplt->set_var ('showCompleted',$otpt.$tmplt->get_var('completedOutput'));
    $tmplt->set_var ('layout_url',$_CONF['layout_url']);
    $tmplt->set_var ('open_icon','arrow-down.gif');
    $tmplt->set_var ('closed_icon','arrow-right.gif');

    $tmplt->parse ('mainFilterPage', 'pageSections');
    $tmplt->set_var('results',$tmplt->get_var('mainFilterPage'));

    $tmplt->set_var('beginCommentOut','/*');
    $tmplt->set_var('endCommentOut','*/');

    $tmplt->parse ('javascript_code', 'javascript');
    $tmplt->parse ('output', 'page',true);
    echo $tmplt->finish ($tmplt->get_var('output'));
}


function nf_formatOutstandingTasks($process_id,$projectProcesses,&$tmplt) {
    global $_TABLES,$_USER,$action_url,$usermodeUID;

    $projectProcesses[] = $process_id;
    $timesThru = 1;
    if (count($projectProcesses > 0)) {
        foreach ($projectProcesses AS $process_id) {
            // Get tasks that have assignment by variable

            $tmplt->set_var ('taskuser', $usermodeUID);
            $tmplt->set_var ('user_options',nf_listUsers());

            if ($process_id > 0 ) {
                $sql  = "SELECT distinct a.id, a.nf_processID,d.taskname, d.nf_templateID, a.status, a.archived, ";
                $sql .= "a.createdDate, c.uid, c.nf_processVariable, a.nf_templateDataID FROM {$_TABLES['nfqueue']} a ";
                $sql .= "LEFT JOIN {$_TABLES['nftemplateassignment']} b ON a.nf_templateDataID = b.nf_templateDataID ";
                $sql .= "LEFT JOIN {$_TABLES['nfproductionassignments']} c ON c.task_id = a.id ";
                $sql .= "LEFT JOIN {$_TABLES['nftemplatedata']} d on a.nf_templateDataID = d.id ";
                $sql .= "WHERE a.nf_processID = '$process_id' AND (a.archived IS NULL OR a.archived = 0)";
                $sql .= "ORDER BY a.id";

                $q2 = DB_query($sql);
                while($B = DB_fetchArray($q2,false)) {
                    if ($B['nf_processVariable'] == '') {
                        continue;
                    }
                    $tmplt->set_var ('taskassign_mode', 'variable');
                    $tmplt->set_var ('otaskid',$B['id']);

                    if (SEC_hasRights('nexflow.edit')) {
                        $tmplt->set_var('otask_span',1);
                        $tmplt->set_var('show_otaskaction','');
                    } else {
                        $tmplt->set_var('otask_span',2);
                        $tmplt->set_var('show_otaskaction','none');
                    }

                    $tmplt->set_var ('otask_user', COM_getDisplayName($B['uid']));
                    $tmplt->set_var ('otask_name', $B['taskname']);
                    $tmplt->set_var ('otask_date',$B['createdDate']);
                    $tmplt->set_var ('otask_id',$B['id']);
                    $tmplt->set_var ('variable_id', $B['nf_processVariable']);

                    if ($timesThru == 1) {
                        $tmplt->parse('record_outstandingtasks','outstandingtasks');
                    } else {
                        $tmplt->parse('record_outstandingtasks','outstandingtasks',true);
                    }

                    $timesThru++;
                } // while
            }
        }

    }

    if($timesThru == 0){
        $tmplt->set_var('record_outstandingtasks','<tr><td colspan="4" style="padding-left:10px">No outstanding user tasks</td></tr>');
    } else {
        if ($tmplt->get_var('record_outstandingtasks') == ''){
            $tmplt->set_var('record_outstandingtasks','<tr><td colspan="4" style="padding-left:10px">No outstanding user tasks</td></tr>');
        }
    }

}



function nf_getProjectOutstandingTasks($uid) {
    global $_TABLES;

    // Retrieve any Outstanding Tasks that have been assigned to this user
    $sql  = "SELECT a.id, a.nf_processID as processid FROM {$_TABLES['nfqueue']} a ";
    $sql .= "LEFT JOIN {$_TABLES['nfproductionassignments']} b ON a.id=b.task_id WHERE b.uid=$uid";
    $query = DB_query($sql);

    $taskProcessIDs = array();
    while ($A = DB_fetchArray($query)) {
        $taskProcessIDs[] = $A['processid'];
    }
    $taskProcessIDs = array_unique($taskProcessIDs);
    return $taskProcessIDs;

}


function nf_getSortedTaskArray($tasks,$taskFilterOptions='',$taskSortOption,$srchText='',$appGroup='', $statusOption='',$sortDirection) {
    global $_CONF,$_TABLES,$CONF_NF;
    
    $sortedTasks = array();
    
    // Setup simple variable names for use in the function    
    $filterdate = $options['filterdate'];
    $pagesize = $options['pagesize'];
    $page = $options['page'];
   
    //there is only one filter option and only one sort option in our case
    //taskFilterOptions can be title, desc, appgroup

    foreach ($tasks as $taskrec) {
        $includeTask = false;

        $currentTask = array();     // Holds current task detail information
        $nfclass = new nexflow($taskrec['processid']);
        $templateID = DB_getItem($_TABLES['nfprocess'],'nf_templateID',"id='{$taskrec['processid']}'");

        $sql  = "SELECT a.id FROM {$_TABLES['nfappgroups']} a ";
        $sql .= "INNER JOIN {$_TABLES['nftemplate']} b ON b.AppGroup=a.id ";
        $sql .= "WHERE b.id='{$templateID}' LIMIT 1";
        $res = DB_query($sql);
        list($appname) = DB_fetchArray($res);

        $currentTask['taskname'] = $taskrec['taskname'];
        $currentTask['flowname'] = DB_getItem($_TABLES['nftemplate'],'templateName',"id='{$templateID}'");
        $currentTask['requestdescription'] = DB_getItem($_TABLES['nfprojects'],'description',"wf_process_id='{$taskrec['processid']}'");
        $currentTask['appgroup'] = NXCOM_filterInt($appname);
        $currentTask['startedDate'] = DB_getItem($_TABLES['nfqueue'], 'startedDate',"id='{$taskrec['id']}'");
        $currentTask['status'] = DB_getItem($_TABLES['nfqueue'], 'status',"id='{$taskrec['id']}'");

        if ($taskFilterOptions=='cdate') {
            if ($taskrec['cdate']  >= $filterdate) {
                $includeTask = true;
             } else {
                $includeTask = false;
            }
        } elseif ($taskFilterOptions=='title' || $taskFilterOptions=='') {
            if($srchText==''){
                $includeTask = true;
            } else{
                $pos1=stripos($currentTask['taskname'],$srchText);
                $pos2=stripos($currentTask['flowname'],$srchText);
                if ($pos1 === false ) {
                    $includeTask = false;
                 } else {
                    $includeTask = true;
                }
            }
        } elseif ($taskFilterOptions=='desc') {
            if ($srchText == '') {
                $includeTask = true;
            }
            else {
                if (strpos($currentTask['requestdescription'],$srchText)!==FALSE ) {
                    $includeTask = true;
                }
                else {
                    $includeTask = false;
                }
            }
        } elseif ($taskFilterOptions=='appgroup') {
            if ($currentTask['appgroup']==$appGroup ) {
                $includeTask = true;
             } else {
                $includeTask = false;
            }
        }

        if ($includeTask AND ($statusOption == 3 || $statusOption == 4)) {
            if ($statusOption == 3) {
                if (!isset($currentTask['startedDate'])) {
                    $includeTask = false;
                }
            } else {
                if (isset($currentTask['startedDate'])) {
                    $includeTask = false;
                }
            }
        } elseif ($currentTask['status'] == $statusOption || $statusOption=='-1' || $statusOption == '' ) {
            $includeTask = $includeTask && 1;
        } else {
            $includeTask = false;
        }
        

        if ($includeTask OR $includeAllTasks) {
            array_push($sortedTasks, $taskrec);
        }  
    }
    // Now sort the task record Multi-Dimensional Array using selected sort field
    if($sortDirection == 'asc') {
        $sortDirection=SORT_ASC;
    } else{
        $sortDirection=SORT_DESC;
    }

    if (count($sortedTasks) > 0) {
        if ($taskSortOption!='') {
            foreach($sortedTasks as $sortrec)
                $sortAux[] = $sortrec[$taskSortOption];
            array_multisort($sortAux, $sortDirection , $sortedTasks);
        } else {
            // No sortoptions enabled - sort by most recent first
            arsort($sortedTasks);
        }
        
        if ($pagesize > 0) {
            if ($page > 1) {
                $offset = ($page -1) * $pagesize;
            } else {
                $offset = 0;
            }               
            $sortedTasks = array_slice($sortedTasks,$offset,$pagesize);            
        }
    } 

    return $sortedTasks;
}



//generates a random string of characters
function nf_getRandomString($length) {
    $validCharacters = array();
    $len = 0;
    $i = 0;
    $char = 0;
    $string = '';
    for ($i = 48; $i < 123; $i++) {
        if ((($i >= 48) && ($i <= 57)) || (($i >= 65) && ($i <= 90)) || (($i >= 97) && ($i <= 122))) {
            $validCharacters[] = $i;
            $len++;
        }
    }
    for ($i = 0; $i < $length; $i++) {
        $char = rand(0, $len-1);
        $string .= chr($validCharacters[$char]);
    }

    return $string;
}

?>