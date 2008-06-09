<?php
//
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | filters.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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
include ('../lib-common.php');
include("includes/block.class.php");

echo COM_siteHeader( array('custom_showBlocks',$_PRJCONF['leftblocks']) );
echo COM_startBlock("Project - Personalized Filter Admin");

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink($_CONF['site_url'] . "/nexproject/index.php?", $strings["home"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink($_CONF['site_url'] . "/nexproject/projects.php?", $strings["projects"], in));
$blockPage->closeBreadcrumbs();

if (isset($_POST['submit'])) {

    $name = COM_applyFilter($_POST["name"]);
    $flid  = COM_applyFilter($_POST['selFilter'],true);

    if ($_POST['submit'] == "New" AND $name != '') {
        $resultT = DB_query("INSERT INTO {$_TABLES['prj_filters']} (name, uid) VALUES ('".$name."', '{$_USER['uid']}' )");
        $selFilter = DB_insertID();
    } elseif ($_POST['submit'] == "Delete") {
        $resultT = DB_query("DELETE FROM {$_TABLES['prj_filters']} WHERE flid = '".$flid."' AND uid = '{$_USER['uid']}' ");

    } elseif ($_POST['submit'] == "Save") {

        if (count($_POST["selProject"]) > 0 ) {
            $projects = implode(",", $_POST["selProject"]);
        } else {
            $projects = '';
        }
        if (count($_POST["selMembers"]) > 0 ) {
            $employees = implode(",", $_POST["selMembers"]);
        } else {
            $employees = '';
        }
        if (count($_POST["selDepartment"]) > 0 ) {
            $department = implode(",", $_POST["selDepartment"]);
        } else {
            $department = '';
        }
        if (count($_POST["selCategory"]) > 0 ) {
            $category = implode(",", $_POST["selCategory"]);
        } else {
            $category = '';
        }
        if (count($_POST["selLocation"]) > 0 ) {
            $location = implode(",", $_POST["selLocation"]);
        } else {
            $location = '';
        }
        if (count($_POST["selObjective"]) > 0 ) {
            $objective = implode(",", $_POST["selObjective"]);
        } else {
            $objective = '';
        }

        $sql = "UPDATE {$_TABLES['prj_filters']} SET name = '$name',projects = '$projects',
                    employees = '$employees', department = '$department',
                    category = '$category', location = '$location', objective = '$objective' 
                    WHERE flid = '$flid' AND uid = '{$_USER['uid']}' ";
        $resultT = DB_query($sql);
    }

}

$selFilter = intval ($_POST['selFilter']);
$query =DB_query("SELECT * FROM {$_TABLES['prj_filters']} WHERE flid = '".$selFilter."' AND uid = '{$_USER['uid']}'");
$filter = DB_fetchArray($query);

$selProjects = COM_optionList( $_TABLES['prj_projects'], "pid,name", explode(",", $filter['projects']) );

$selMembers = COM_optionList( $_TABLES['users']." user LEFT JOIN ".$_TABLES['userinfo']." info ON info.uid = user.uid ", "user.uid, user.fullname, user.username, info.location", explode(",", $filter['employees']), 2 );

$locations = nexlistOptionList('alist', '', $_PRJCONF['nexlist_locations']);
$selectedLocations = explode(",", $filter['location']);
$selLocation = '';
foreach ($locations as $key=>$value) {
    $selected = (in_array($key, $selectedLocations) === true) ? ' selected':'';
    $selLocation .= "<option value=\"$key\"$selected>$value</option>";
}

$departments = nexlistOptionList('alist', '', $_PRJCONF['nexlist_departments']);
$selectedDepartments = explode(",", $filter['department']);
$selDepartment = '';
foreach ($departments as $key=>$value) {
    $selected = (in_array($key, $selectedDepartments) === true) ? ' selected':'';
    $selDepartment .= "<option value=\"$key\"$selected>$value</option>";
}

$categories = nexlistOptionList('alist', '', $_PRJCONF['nexlist_category']);
$selectedCategories = explode(",", $filter['category']);
$selCategory = '';
foreach ($categories as $key=>$value) {
    $selected = (in_array($key, $selectedCategories) === true) ? ' selected':'';
    $selCategory .= "<option value=\"$key\"$selected>$value</option>";
}

$objectives = nexlistOptionList('alist', '', $_PRJCONF['nexlist_objective']);
$selectedObjectives = explode(",", $filter['objective']);
$selObjective = '';
foreach ($objectives as $key=>$value) {
    $selected = (in_array($key, $selectedObjectives) === true) ? ' selected':'';
    $selObjective .= "<option value=\"$key\"$selected>$value</option>";
}


// Display filter selection section
$sqlFilter = "SELECT flid, name FROM {$_TABLES['prj_filters']} WHERE uid = {$_USER['uid']}";
$result = DB_query($sqlFilter);
$numFilters = DB_numrows($result);

echo '<table width="100%" border="0" cellpadding="1" cellspacing="1"><tr><td class="heading2" colspan="2" align="left">Select by your Saved Filters</TD></tr><tr>';
if ($numFilters != "0") {

    echo '<td><form action="'.$PHP_SELF.'" method="post"><select name="selFilter" Style="width:200px">';
    while ( list($flid, $fname) = DB_fetchARRAY($result)) {
        echo '<option value="' . $flid . '"';
        if ($flid == $selFilter )
            echo ' SELECTED';
        echo '>' .  $fname . '</option>';
        }
    echo '</select><input type="submit" name="submit" value="Edit"><input type="submit" name="submit" value="Delete"></form></td>';
    }

echo '<td><form action="'.$PHP_SELF.'" method="post">New: <Input Type="text" Name="name"><input type="submit" name="submit" value="New"></form></td></tr></table>';

if ((intval($selFilter) > 0)) {
echo '<form action="'.$PHP_SELF.'" method="post">

<table width="100%" border="0" cellpadding="1" cellspacing="1">
<TR>
    <TD>Name:<Input Name="name" Type="text" Value="'.$filter['name'].'" style="width: 200px"></TD>
    <TD align="right"><Input Type="hidden" Name="selFilter" Value="'.$selFilter.'"><INPUT TYPE="submit" name="submit" value="Save"></TD></TR>
<TR>
    <TD ColSpan="3">Use the Control key to select multiple entries. If a list has no selected entires, then all entries will be matched.</TD>
</TR>
<TR valign="top">
    <TD>Select Project:<BR><SELECT NAME="selProject[]" multiple style="width: 200px">' . $selProjects . '</SELECT></TD>
    <TD>Employees:<BR><SELECT NAME="selMembers[]" multiple style="width: 200px">' . $selMembers. '</SELECT></TD>
    <TD>Filter Location<BR><SELECT NAME="selLocation[]" multiple style="width: 200px">' . $selLocation. '</SELECT></TD>
</TR>
<TR valign="top">
    <TD>Filter Department<BR><SELECT NAME="selDepartment[]" multiple style="width: 200px">' .$selDepartment. '</SELECT></TD>
    <TD>Filter Category:<BR><SELECT NAME="selCategory[]" multiple style="width: 200px">' .$selCategory. '</SELECT></TD>
    <TD>Filter Objective:<BR><SELECT NAME="selObjective[]" multiple style="width: 200px">' . $selObjective. '</SELECT></TD>

</TR>
<TR>
    <TD colspan="3"><br></TD>
</TR>
</table></form>';
}

echo COM_endBlock();
echo COM_siteFooter();

?>