<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.0.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | ajaxupdate.php                                                            |
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
require_once($_CONF['path'] . 'plugins/nexform/debug.php');  // Common Debug Code


if (!SEC_hasRights('nexform.edit')) {
    print ('No access rights');
    exit();
}

$mode = COM_applyFilter($_GET['mode']);
$rec = COM_applyFilter($_GET['rec'],true);
$op = COM_applyFilter($_GET['op']);
$setting = COM_applyFilter($_GET['setting']);

if ($mode == 'field') {

    if (DB_count($_TABLES['formFields'],'id',"$rec") == 1) {
        switch ($op) {
            case 'newline':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set newline to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_newline = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set newline to $setting for record: $rec");
                    }
                }
                break;

            case 'manditory':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field manditory to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_mandatory = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field manditory to $setting for record: $rec");
                    }
                }
                break;

            case 'vertical':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field vertical layout to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_vertical = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field vertical layout to $setting for record: $rec");
                    }
                }
                break;

            case 'reverse':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field reverse order to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_reverseorder = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field reverse order to $setting for record: $rec");
                    }
                }
                break;

            case 'report':
                $setting = (is_string($setting)) ?  strtolower($setting) : $setting;
                if ($setting == 'true' or $setting == 1) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield = '1' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field report mode to $setting for record: $rec");
                    }
                } elseif ($setting == 'false' OR $setting == 0) {
                    DB_query("UPDATE {$_TABLES['formFields']} SET is_resultsfield = '0' WHERE id='$rec'");
                    if ($CONF_FE['debug']) {
                        COM_errorLog("Set field report mode to $setting for record: $rec");
                    }
                }
                break;
        }
    }
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
$retval = "<result>";
$retval .= "<mode>$mode</mode>";
$retval .= "<record>$rec</record>";
$retval .= "<property>$op</property>";
$retval .= "<value>$setting</value>";
$retval .= "</result>";
print $retval;
?>