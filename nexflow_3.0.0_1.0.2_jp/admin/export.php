<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | export.php                                                                |
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

include('../../../lib-common.php');
require_once( $_CONF['path_system'] . 'classes/downloader.class.php' );

//first do the template
$file=$CONF_NF['export_dir'] . "nexflow_export.sql";
$f2="nexflow_export.sql";
$downloadFileType =  array('sql' => 'text/plain');
$downloadDirectory = $CONF_NF['export_dir'];

$origTemplate=COM_applyFilter($_GET['templateid'],true);
$prefix='$_ARR';
$output="";
$newline= LB;


//begin by exporting the template entry
$sql="SELECT * from {$_TABLES['nftemplate']} where id={$origTemplate}";
$res=DB_query($sql);
list($id,$templateName,$useProject,$AppGroup)=DB_fetchArray($res);

$templateName=htmlspecialchars($templateName);
$output .="{$prefix}['template']=\"INSERT INTO {$_TABLES['nftemplate']} (templateName, useProject, appgroup) values ('{$templateName}',{$useProject},{$AppGroup})\";";
$output .=$newline;

//now, output the variables
$sql="SELECT * from {$_TABLES['nftemplatevariables']} where nf_templateID={$origTemplate}";
$res=DB_query($sql);
$cntr=0;
$id='';
while(list($id,$nf_templateID,$nf_variableTypeID,$variableName,$variableValue)=DB_fetchArray($res)){
    $output .="{$prefix}['variables'][{$cntr}]['origid']=\"{$id}\";";
    $output .=$newline;
    $output .="{$prefix}['variables'][{$cntr}]['SQL']=\"INSERT INTO {$_TABLES['nftemplatevariables']} (nf_templateID,nf_variableTypeID,variableName,variableValue  ) values ({templateID},{$nf_variableTypeID},'{$variableName}','{$variableValue}')\";";
    $output .=$newline;
    $output .="{$prefix}['variables'][{$cntr}]['newid']=\"\";";
    $output .=$newline;
    $cntr+=1;
}

//now output the templatedata
$sql="SELECT * from {$_TABLES['nftemplatedata']} where nf_templateID={$origTemplate}";
$res=DB_query($sql);
$cntr=0;
$tids='';
while(list($fid,$nf_templateID,$logicalID,$nf_stepType,$nf_handlerId,$firstTask,$taskname,$assignedByVariable,$argumentVariable,$argumentProcess,$operator,$ifValue,$regenerate,$regenAllLiveTasks,$isDynamicForm,$dynamicFormVariableID,$isDynamicTaskName,$dynamicTaskNameVariableID,$function,$formid,$optionalParm,$reminderInterval,$subsequentReminderInterval,$last_updated,$prenotify_message,$postnotify_message,$reminder_message,$numReminders,$escalateVariableID )=DB_fetchArray($res)){
    $output .="{$prefix}['templatedata'][{$cntr}]['origid']=\"{$fid}\";";
    $output .=$newline;
    $output .="{$prefix}['templatedata'][{$cntr}]['SQL']=\"INSERT INTO {$_TABLES['nftemplatedata']} (";
    $output .="nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,";
    $output .="operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,";
    $output .="function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,";
    $output .="reminder_message,numReminders,escalateVariableID";
    $output .=") values (";
    $output .="{templateID},$logicalID,$nf_stepType,$nf_handlerId,$firstTask,'$taskname', " . NXCOM_filterInt($assignedByVariable) . ",{argumentvariable:'$argumentVariable'},'$argumentProcess',";
    $output .="'$operator','$ifValue',$regenerate,$regenAllLiveTasks,$isDynamicForm, {dynamicformvariable:'$dynamicFormVariableID'},$isDynamicTaskName,{dynamictasknamevariable:'$dynamicTaskNameVariableID'},";
    $output .="'$function','$formid','$optionalParm',$reminderInterval,$subsequentReminderInterval, '$last_updated','$prenotify_message','$postnotify_message',";
    $output .="'$reminder_message',$numReminders,$escalateVariableID ";
    $output .=")\";";
    $output .=$newline;
    $output .="{$prefix}['templatedata'][{$cntr}]['newid']=\"\";";
    $output .=$newline;
    if($tids!=''){
        $tids.=",";
    }
    $tids.=$fid;
    $cntr+=1;
}
//during the import process, the argumentVariable field value will be updated with the ${$prefix}['variables'][xxx]['newid'] field 
//based on what the argumentVariable=${$prefix}['variables'][xxx]['origid'].. 'newid' is filled in by the import process and made available
//to the importing routine for this reason


//now to output the templatedata next step table
//$tids holds the IDs of the nf_templateDataFrom items..
$cntr=0;
$arr=split(",",$tids);
$len=count($arr);
for($loop=0;$loop<$len;$loop++){
    $temp=$arr[$loop];
    $sql="SELECT * from {$_TABLES['nftemplatedatanextstep']} where nf_templateDataFrom={$temp}";
    $res=DB_query($sql);
    while(list($id,$nf_templateDataFrom,$nf_templateDataTo,$nf_templateDataToFalse)=DB_fetchArray($res)){
        //now have a row of data that we have to create an entry for
        $output .="{$prefix}['nextstep'][{$cntr}]['origid']=\"{$id}\";";
        $output .=$newline;
        $output .="{$prefix}['nextstep'][{$cntr}]['SQL']=\"INSERT INTO {$_TABLES['nftemplatedatanextstep']} (";
        $output .="nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse";
        $output .=") values (";
        if($nf_templateDataToFalse==''){
            $nf_templateDataToFalse="NULL";   
        }
        $output .="{from:'{$nf_templateDataFrom}'},{to:'{$nf_templateDataTo}'},{false:'{$nf_templateDataToFalse}'} ";
        $output .=")\";";
        $output .=$newline;
        $output .="{$prefix}['nextstep'][{$cntr}]['newid']=\"\";";
        $output .=$newline;
        $cntr+=1;
    }
}
//the from,to,false values are prefixed with the original IDs which we just parse out of the string and fetch within the array for the new values when insertting..

//now to do template Assignments
//$arr already holds our template IDs.
$cntr=0;
$sql="SELECT * FROM {$_TABLES['nftemplateassignment']}  WHERE `nf_templateDataID` IN ({$tids})";
$res=DB_query($sql);
while(list($id,$nf_templateDataID,$uid,$gid,$nf_processVariable,$nf_prenotifyVariable,$nf_postnotifyVariable,$nf_remindernotifyVariable)=DB_fetchArray($res)){
    $output .="{$prefix}['assignments'][{$cntr}]['origid']=\"{$id}\";";
    $output .=$newline;
    $output .="{$prefix}['assignments'][{$cntr}]['SQL']=\"INSERT INTO {$_TABLES['nftemplateassignment']} (";
    $output .="nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable";
    $output .=") values (";
    $output .="{templatedataid:'{$nf_templateDataID}'},$uid,$gid,{processvariable:'$nf_processVariable'},{prenotifyvariable:'$nf_prenotifyVariable'},{postnotifyvariable:'$nf_postnotifyVariable'},{remindernotifyvariable:'$nf_remindernotifyVariable'}";
    $output .=")\";";
    $output .=$newline;
    $output .="{$prefix}['assignments'][{$cntr}]['newid']=\"\";";
    $output .=$newline;
    $cntr+=1;   
}

$output ="<?php" . LB . $output . LB.LB. "?>";

   if (!$fp = @fopen($file, "w")) {
        COM_errorLog("Error exporting form definition - Unable to write to file: $exportfile");
    } else {
        fwrite($fp, $output);
        fclose($fp);

        // Send new file to user's browser
        $download = new downloader();
        $download->_setAvailableExtensions ($downloadFileType);
        $download->setAllowedExtensions ($downloadFileType);
        $download->setPath($downloadDirectory);
        $logfile = $_CONF['path'] .'logs/error.log';
        $download->setLogFile($logfile);
        $download->setLogging(true);
        $download->downloadFile($f2);
        if ($download->areErrors()) {
            COM_errorLog("Error downloading nexform Export SQL file: " . $download->printErrors());
        }
    }


?>