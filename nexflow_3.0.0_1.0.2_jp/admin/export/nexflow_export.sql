<?php
$_ARR['template']="INSERT INTO gl_nf_template (templateName, useProject, appgroup) values ('TEST FLOW 4 - OR Test',0,0)";
$_ARR['variables'][0]['origid']="3";
$_ARR['variables'][0]['SQL']="INSERT INTO gl_nf_templatevariables (nf_templateID,nf_variableTypeID,variableName,variableValue  ) values ({templateID},0,'INITIATOR','')";
$_ARR['variables'][0]['newid']="";
$_ARR['variables'][1]['origid']="9";
$_ARR['variables'][1]['SQL']="INSERT INTO gl_nf_templatevariables (nf_templateID,nf_variableTypeID,variableName,variableValue  ) values ({templateID},0,'TASKNAME','')";
$_ARR['variables'][1]['newid']="";
$_ARR['templatedata'][0]['origid']="20";
$_ARR['templatedata'][0]['SQL']="INSERT INTO gl_nf_templatedata (nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,reminder_message,numReminders,escalateVariableID) values ({templateID},3,7,0,0,'Dynamic Taskname', 1,{argumentvariable:'0'},'0','0','0',0,0,0, {dynamicformvariable:'0'},1,{dynamictasknamevariable:'9'},'nf_alertUserMessage','0','Branch 1 - Press complete to continue',0,0, '2007-02-28 22:45:54','','','',0,0 )";
$_ARR['templatedata'][0]['newid']="";
$_ARR['templatedata'][1]['origid']="21";
$_ARR['templatedata'][1]['SQL']="INSERT INTO gl_nf_templatedata (nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,reminder_message,numReminders,escalateVariableID) values ({templateID},4,7,0,0,'Branch 2 Test Task', 1,{argumentvariable:'0'},'0','0','0',0,0,0, {dynamicformvariable:'0'},0,{dynamictasknamevariable:'0'},'nf_alertUserMessage','0','Branch 2 - Press complete to continue',0,0, '2007-02-28 15:59:34','','','',0,0 )";
$_ARR['templatedata'][1]['newid']="";
$_ARR['templatedata'][2]['origid']="22";
$_ARR['templatedata'][2]['SQL']="INSERT INTO gl_nf_templatedata (nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,reminder_message,numReminders,escalateVariableID) values ({templateID},5,7,0,0,'End of test', 1,{argumentvariable:'0'},'0','0','0',0,0,0, {dynamicformvariable:'0'},0,{dynamictasknamevariable:'0'},'nf_alertUserMessage','0','Last task in workflow',0,0, '2007-02-28 16:00:33','','','',0,0 )";
$_ARR['templatedata'][2]['newid']="";
$_ARR['templatedata'][3]['origid']="58";
$_ARR['templatedata'][3]['SQL']="INSERT INTO gl_nf_templatedata (nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,reminder_message,numReminders,escalateVariableID) values ({templateID},2,6,0,0,'Set Task3 Taskname', 0,{argumentvariable:''},'','','',0,1,0, {dynamicformvariable:'0'},0,{dynamictasknamevariable:'0'},'nf_testsuiteSetTaskname','0','Branch 1 Test Task',0,0, '2007-02-28 22:47:46','','','',0,0 )";
$_ARR['templatedata'][3]['newid']="";
$_ARR['templatedata'][4]['origid']="55";
$_ARR['templatedata'][4]['SQL']="INSERT INTO gl_nf_templatedata (nf_templateID,logicalID,nf_stepType,nf_handlerId,firstTask,taskname, assignedByVariable,argumentVariable,argumentProcess,operator,ifValue,regenerate,regenAllLiveTasks,isDynamicForm, dynamicFormVariableID,isDynamicTaskName,dynamicTaskNameVariableID,function,formid,optionalParm,reminderInterval,subsequentReminderInterval, last_updated,prenotify_message,postnotify_message,reminder_message,numReminders,escalateVariableID) values ({templateID},1,7,0,1,'Start Test', 1,{argumentvariable:''},'','','',0,0,0, {dynamicformvariable:'0'},0,{dynamictasknamevariable:'0'},'nf_alertUserMessage','0','Press complete to launch 2 parallel tasks',0,0, '2007-03-01 00:07:57','','','',0,0 )";
$_ARR['templatedata'][4]['newid']="";
$_ARR['nextstep'][0]['origid']="159";
$_ARR['nextstep'][0]['SQL']="INSERT INTO gl_nf_templatedatanextstep (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse) values ({from:'20'},{to:'22'},{false:'NULL'} )";
$_ARR['nextstep'][0]['newid']="";
$_ARR['nextstep'][1]['origid']="95";
$_ARR['nextstep'][1]['SQL']="INSERT INTO gl_nf_templatedatanextstep (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse) values ({from:'21'},{to:'22'},{false:'NULL'} )";
$_ARR['nextstep'][1]['newid']="";
$_ARR['nextstep'][2]['origid']="163";
$_ARR['nextstep'][2]['SQL']="INSERT INTO gl_nf_templatedatanextstep (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse) values ({from:'58'},{to:'20'},{false:'NULL'} )";
$_ARR['nextstep'][2]['newid']="";
$_ARR['nextstep'][3]['origid']="167";
$_ARR['nextstep'][3]['SQL']="INSERT INTO gl_nf_templatedatanextstep (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse) values ({from:'55'},{to:'58'},{false:'NULL'} )";
$_ARR['nextstep'][3]['newid']="";
$_ARR['nextstep'][4]['origid']="166";
$_ARR['nextstep'][4]['SQL']="INSERT INTO gl_nf_templatedatanextstep (nf_templateDataFrom,nf_templateDataTo,nf_templateDataToFalse) values ({from:'55'},{to:'21'},{false:'NULL'} )";
$_ARR['nextstep'][4]['newid']="";
$_ARR['assignments'][0]['origid']="5";
$_ARR['assignments'][0]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'20'},2,0,{processvariable:'0'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][0]['newid']="";
$_ARR['assignments'][1]['origid']="6";
$_ARR['assignments'][1]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'21'},2,0,{processvariable:'0'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][1]['newid']="";
$_ARR['assignments'][2]['origid']="21";
$_ARR['assignments'][2]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'55'},0,0,{processvariable:'3'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][2]['newid']="";
$_ARR['assignments'][3]['origid']="22";
$_ARR['assignments'][3]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'22'},0,0,{processvariable:'3'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][3]['newid']="";
$_ARR['assignments'][4]['origid']="23";
$_ARR['assignments'][4]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'21'},0,0,{processvariable:'3'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][4]['newid']="";
$_ARR['assignments'][5]['origid']="24";
$_ARR['assignments'][5]['SQL']="INSERT INTO gl_nf_templateassignment (nf_templateDataID,uid,gid,nf_processVariable,nf_prenotifyVariable,nf_postnotifyVariable,nf_remindernotifyVariable) values ({templatedataid:'20'},0,0,{processvariable:'3'},{prenotifyvariable:'0'},{postnotifyvariable:'0'},{remindernotifyvariable:'0'})";
$_ARR['assignments'][5]['newid']="";


?>