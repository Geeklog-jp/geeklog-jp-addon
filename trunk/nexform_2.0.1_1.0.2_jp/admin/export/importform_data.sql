<?php
// Export Form Defintion for: Install Test
// Date: Wednesday, June 27 2007 @ 01:40 PM Eastern Daylight Time



# Export Form Definitions 
$_SQL[9001][] = "INSERT INTO gl_nxform_definitions (name,shortname,date,responses,template,post_method,post_option,fieldsets,before_formid,after_formid,show_as_tab,tab_label,metavalues,intro_text,after_post_text,on_submit,return_url,perms_view,perms_access,perms_edit,status,gid,admin_url,comments,show_mandatory_note) VALUES ('Install Test','Install Test','1121781422','33','default.thtml','dbsave','','','0','44','1','','0','This is a test of the plugin install - just some random fields.','','','','26','1','1','1','','','','0')";

# Export Field Definitions 
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','1','select','10','sel_frm1_1','Salutation','1','',NULL,NULL,'0','1','0','0','1','0','0','0','0','','','fe_getSalutations','1','','')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','2','text','20','txt_frm1_2','Name','2','',NULL,NULL,'0','1','0','0','1','0','0','0','0','','','','0','','')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','3','text','30','txt_frm1_3','Address1','1','',NULL,NULL,'0','1','1','0','1','0','0','0','0','size=\"60\" maxlength=\"40\"','','your address here','0','','realname=\"Full address is required\"')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','4','textarea1','40','ta1_frm1_5','Comments','1','',NULL,NULL,'0','1','0','0','1','0','0','0','0','cols=\"50\" rows=\"3\"','','This will clear when you click on field. Testing adding JS to the field','0','','onFocus=\'this.value=\"\"\'')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','7','text','50','txt_frm1_7','Age','1','',NULL,NULL,'0','1','0','0','0','0','0','0','0','size=&quot;3&quot;','','','0','','')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','9','checkbox','70','chk_frm1_6','Sign up for newsletter','1','',NULL,NULL,'0','1','0','0','0','0','0','0','0','','','yes','0','','')";
$_SQL[9001][] = "INSERT INTO gl_nxform_fields (formid,tfid,type,fieldorder,field_name,label,style,layout,col_width,col_padding,is_vertical,is_newline,is_mandatory,is_searchfield,is_resultsfield,is_reverseorder,is_htmlfiltered,is_internaluse,hidelabel,field_attributes,field_help,field_values,value_by_function,validation,javascript) VALUES ('9001','10','submit','80','sub_frm1_8','','1','',NULL,NULL,'0','1','0','0','0','0','0','0','0','','','Submit','0','','')";

?>