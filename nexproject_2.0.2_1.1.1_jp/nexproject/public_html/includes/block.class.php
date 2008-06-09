<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | May 27, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | block.class.php                                                           |
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
class block {

    function block() {
        global $_CONF;
        $this->theme = THEME;
        $this->bgColor = "#5B7F93";
        $this->fgColor = "#C4D3DB";
        $this->filterColor = "#F5A0A0";
        $this->oddColor = "#F5F5F5";
        $this->evenColor = "#EFEFEF";
        $this->highlightOn = "#DEE7EB";
        $this->class = "odd";
        $this->highlightOff = $this->oddColor;
        $this->pathImg = "{$_CONF['layout_url']}/nexproject/images";

    }

    function note($content) {
        echo "<p class=\"note\">".$content."</p>\n\n";
    }

    function heading($title,$extra='') {
        echo '<div style="padding-bottom:5px;"><span class="heading">'.$title.'</span><span  style="padding:15px;">'.$extra.'</span></div>' . LB;
    }

    function headingToggle($title,$extra='') {
        if ($_COOKIE[$this->form] == "c" ) {
            $style = "none";
            $arrow = "closed";
        } else {
            $style = "block";
            $arrow = "open";
        }

        $display  = '<table cellspacing="0" cellpadding="0" border="0"><tr><td><a class="projectblocklinks" ';
        $display .= 'href="javascript:showHideModule(\''.$this->form.'\',\''.$this->theme.'\')" ';
        $display .= 'onMouseOver="javascript:showHideModuleMouseOver(\''.$this->form.'\'); return true;" ';
        $display .= 'onMouseOut="javascript:window.status=\'\'; return true;"><img name="'.$this->form.'Toggle" border="0" ';
        $display .= 'src="'.$this->pathImg.'/module_toggle_'.$arrow.'.gif" alt=""></img></a></td>';
        $display .= '<td><img width="10" height="10" name="'.$this->form.'tl" src="'.$this->pathImg.'/spacer.gif"';
        $display .= 'alt=""></td><td width="10%" nowrap><span class="heading">'.$title.'</span></td>';
        $display .= '<td width="90%" style="padding:5px;">'.$extra.'&nbsp;</td></tr></table>';
        $display .= '<div id="'.$this->form.'" style="display:'.$style.'">' . LB;
        echo $display;
    }

    function closeToggle() {
        echo "</div>\n\n";
    }

    function headingError($title) {
        echo "<h1 class=\"headingError\">".$title."</h1>\n";
    }

    function contentError($content) {
        echo "<table class=\"error\"><tr><td>".$content."</td></tr></table>\n";
    }

    function returnBorne($current) {
        global ${'borne'.$current};
        if (${'borne'.$current} == "") {
            $borneValue = "0";
        } else {
            $borneValue = ${'borne'.$current};
        }
        return $borneValue;
    }

    function bornesFooter($current,$total,$showall,$parameters) {
        global $strings;
        //COM_errorLog ("block->bornesFooter: rowlimit:$this->rowsLimit,record total:$this->recordsTotal,current:$current, total:$total");
        if ($this->rowsLimit < $this->recordsTotal) {
            echo "<table cellspacing=\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\"><tr><td nowrap class=\"footerCell\">&#160;&#160;&#160;&#160;";
            $nbpages = ceil($this->recordsTotal/ $this->rowsLimit);
            $j = "0";
            for($i = 1;$i <= $nbpages;$i ++) {
                if ($this->borne == $j) {
                    echo "<b>$i</b>&#160;";
                } else {
                    echo "<a href=\"$PHP_SELF?$transmitSid";
                    for ($k=1;$k<=$total;$k++) {
                        global ${'borne'.$k};
                        if ($k != $current) {
                            echo "&amp;borne$k=".${'borne'.$k};
                        } else if ($k == $current) {
                            echo "&amp;borne$k=$j";
                        }
                    }
                    echo "&amp;$parameters&amp;#".$this->form."Anchor\">$i</a>&#160;";
                }
                $j = $j + $this->rowsLimit;
            }
            echo "</td><td nowrap align=\"right\" class=\"footerCell\">";
            if ($showall != "") {
                echo "<a href=\"$showall&amp;".session_name()."=".session_id()."\">".$strings["show_all"]."</a>";
            }
            echo "&#160;&#160;&#160;&#160;&#160;</td></tr><tr><td height=\"5\" colspan=\"2\"><img width=\"1\" height=\"5\" border=\"0\" src=\"$this->pathImg/spacer.gif\" alt=\"\"></td></tr></table>";
        }
    }

    function messageBox($msgLabel) {
        echo '<br><table width="100%"><tr><td class="pluginInfo" width="100%">'.$msgLabel.'</td></tr></table>';
    }

    function openPaletteIcon() {
        echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"icons\"><tr>\n";
    }

    function closePaletteIcon($extraCellContent='&nbsp;') {
        echo "<td align=left width=\"1%\"><img height=\"26\" width=\"5\" src=\"$this->pathImg/spacer.gif\" alt=\"\"></td><td class=\"commandDesc\" align=\"left\" width=\"5%\" nowrap>";
        echo "<div id=\"".$this->form."tt\" class=\"rel\"><div id=\"".$this->form."tti\" class=\"abs\"><img height=\"1\" width=\"350\" src=\"$this->pathImg/spacer.gif\" alt=\"\"></div></div></td>";
        echo "<td width=\"95%\" style=\"text-align:left;\">$extraCellContent</td>";
        echo "</tr></table>\n";
    }

    function openPaletteScript() {
        echo "<script type=\"text/JavaScript\">
        <!--
        document.".$this->form."Form.buttons = new Array();\n";
    }

    function closePaletteScript($compt,$values) {
        echo "MM_updateButtons(document.".$this->form."Form, 0);document.".$this->form."Form.checkboxes = new Array();";
        for ($i=0;$i<$compt;$i++) {
            echo "document.".$this->form."Form.checkboxes[document.".$this->form."Form.checkboxes.length] = new MMCheckbox('$values[$i]',document.".$this->form."Form,'".$this->form."cb$values[$i]');";
        }
        echo "document.".$this->form."Form.tt = '".$this->form."tt';
        // -->
        </script>\n\n";
    }

    function sorting($sortingRef,$sortingValue,$sortingDefault,$sortingFields) {
        global $sortingOrders,$sortingFields,$sortingArrows,$sortingStyles,$explode;

        if ($sortingRef != "") {
            $this->sortingRef = $sortingRef;
        }
        if ($sortingValue != "") {
            $this->sortingValue = $sortingValue;
        }
        if ($sortingDefault != "") {
            $this->sortingDefault = $sortingDefault;
        }
        if ($sortingFields != "") {
            $this->sortingFields = $sortingFields;
        }
        if (isset($this->sortingValue) != "") {
            $explode = explode(" ",$this->sortingValue);
        } else {
            $this->sortingValue = $this->sortingDefault;
            $explode = explode(" ",$this->sortingValue);
        }

        for ($i=0;$i<count($sortingFields);$i++) {
            if ($sortingFields[$i] == $explode[0] && $explode[1] == "DESC") {
                $sortingOrders[$i] = "ASC";
                $sortingArrows[$i] = "&#160;<img border=\"0\" src=\"$this->pathImg/icon_sort_za.gif\" alt=\"\" width=\"11\" height=\"11\">";
                $sortingStyles[$i] = "projectblocklinks";
            } else if ($sortingFields[$i] == $explode[0] && $explode[1] == "ASC") {
                $sortingOrders[$i] = "DESC";
                $sortingArrows[$i] = "&#160;<img border=\"0\" src=\"$this->pathImg/icon_sort_az.gif\" alt=\"\" width=\"11\" height=\"11\">";
                $sortingStyles[$i] = "projectblocklinks";
            } else {
                $sortingOrders[$i] = "ASC";
                $sortingArrows[$i] = "";
                $sortingStyles[$i] = "projectblocklinks";
            }
        }
        if ($sortingOrders != "") {
            $this->sortingOrders = $sortingOrders;
        }
        if ($sortingArrows != "") {
            $this->sortingArrows = $sortingArrows;
        }
        if ($sortingStyles != "") {
            $this->sortingStyles = $sortingStyles;
        }

    }

    function openForm($address) {
        echo '<div>';
        echo '<a name="'.$this->form.'Anchor"></a>' . LB;
        echo '<form accept-charset="UNKNOWN" method="POST" action="'.$address.'" name="'.$this->form.'Form" ';
        echo 'enctype="application/x-www-form-urlencoded" style="margin:0px;">' . LB;
    }

    function closeFormResults() {
        echo '<input name="sor_cible" type="HIDDEN" value="'.$this->sortingRef.'">';
        echo '<input name="sor_champs" type="HIDDEN" value="">';
        echo '<input name="sor_ordre" type="HIDDEN" value="">';
        echo '</form></div>' . LB;
    }


    function labels($labels,$published,$sorting="true",$sortingOff="") {
        global $sortingOrders,$sortingFields,$sortingArrows,$sortingStyles,$strings,$sitePublish;

        $sortingFields = $this->sortingFields;
        $sortingOrders = $this->sortingOrders;
        $sortingArrows = $this->sortingArrows;
        $sortingStyles = $this->sortingStyles;

        if ($sitePublish == "false" && $published == "true") {
            $comptLabels = count($labels) - 1;
        } else {
            $comptLabels = count($labels);
        }
        for ($i=0;$i<$comptLabels;$i++) {
            if ($sorting == "true") {
                switch ($labels[$i]) {

                case "Project":
                    echo "<th nowrap class=\"$sortingStyles[$i]\" width=\"150\">";
                    break;
                case "Task":
                    echo "<th nowrap class=\"$sortingStyles[$i]\" width=\"230\">";
                    break;
                case "Progress":
                    echo "<th nowrap class=\"$sortingStyles[$i]\" width=\"10\">";
                    $labels[$i] = $strings["ProgressLabel"];
                    break;
                default:
                    echo "<th nowrap class=\"$sortingStyles[$i]\">";
                    break;
                }
                if ($labels[$i] != $strings["ProgressLabel"] AND $labels[$i] != $strings["ProgressBlankLabel"]) {
                    echo "<a class=\"projectblocklinks\" href=\"javascript:document.".$this->form."Form.sor_cible.value='$this->sortingRef';document.".$this->form."Form.sor_champs.value='$sortingFields[$i]';document.".$this->form."Form.sor_ordre.value='$sortingOrders[$i]';document.".$this->form."Form.submit();\" onMouseOver=\"javascript:window.status='".$strings["sort_by"]." ".addslashes($labels[$i])."'; return true;\" onMouseOut=\"javascript:window.status=''; return true\">".$labels[$i]."</a>$sortingArrows[$i]</th>\n";
                } else {
                    echo "<a href=\"javascript:document.".$this->form."Form.sor_cible.value='$this->sortingRef';document.".$this->form."Form.sor_champs.value='$sortingFields[$i]';document.".$this->form."Form.sor_ordre.value='$sortingOrders[$i]';document.".$this->form."Form.submit();\" onMouseOver=\"javascript:window.status='".$strings["sort_by"]." ".addslashes($strings['progress'])."'; return true;\" onMouseOut=\"javascript:window.status=''; return true\">".$labels[$i]."</a>$sortingArrows[$i]</th>\n";
                }

            } else {
                //if ($sortingOff[1] == "ASC") {
                //    $sortingArrow = "&#160;<img border=\"0\" src=\"$this->pathImg/icon_sort_az.gif\" alt=\"\" width=\"11\" height=\"11\">";

                //} else if ($sortingOff[1] == "DESC") {
                //    $sortingArrow = "&#160;<img border=\"0\" src=\"$this->pathImg/icon_sort_za.gif\" alt=\"\" width=\"11\" height=\"11\">";
                //}
                //if ($i == $sortingOff[0]) {
                //    echo "<th nowrap class=\"active\">".$labels[$i]."$sortingArrow";
                //} else {
                    echo "<th nowrap class=\"$sortingStyles[$i]\">".$labels[$i];
                //}
            }
        }

        echo "</tr>\n";
    }

    function openResults($checkbox="true") {
        echo "<table class=\"listing\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
        <tr>\n";
        if ($checkbox == "true") {
            echo "<th width=\"1%\" align=\"center\"><a href=\"javascript:MM_toggleSelectedItems(document.".$this->form."Form,'$this->theme')\"><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/checkbox_off_16.gif\" alt=\"\" vspace=\"3\" hspace=\"3\"></a></th>\n";
        } else {
            echo "<th width=\"1%\" align=\"center\"><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/spacer.gif\" alt=\"\" vspace=\"3\"></th>\n";
        }
    }

    function closeResults() {
        echo '</table><hr style="margin-top:2px;" />' ."\n";
    }

    function noresults() {
        global $strings;
        echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr style=\"margin-top:2px;\" />";
    }

    function paletteIcon($num,$type,$text) {
        echo "<td width=\"30\" class=\"commandBtn\"><a href=\"javascript:var b = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (b) b.click();\" onMouseOver=\"var over = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (over) over.over(); return true; \" onMouseOut=\"var out = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (out) out.out(); return true; \"><img width=\"23\" height=\"23\" border=\"0\" name=\"".$this->form."$num\" src=\"$this->pathImg/btn_".$type."_norm.gif\" alt=\"$text\"></a></td>\n";
    }

    function paletteScript($num,$type,$link,$options,$text) {
        echo "document.".$this->form."Form.buttons[document.".$this->form."Form.buttons.length] = new MMCommandButton('".$this->form."$num',document.".$this->form."Form,'".$link."','$this->pathImg/btn_".$type."_norm.gif','$this->pathImg/btn_".$type."_over.gif','$this->pathImg/btn_".$type."_down.gif','$this->pathImg/btn_".$type."_dim.gif',$options,'','".$text."',false,'');\n";
    }

    function openContent() {
        echo "<table class=\"content\" cellspacing=\"0\" cellpadding=\"0\">";
    }

    function contentRow($left,$right,$altern="true") {
        if ($this->class == "") {
            $this->class = "odd";
        }
        if ($left != "") {
            echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\">".$left." :</td><td>".$right."&nbsp;</td></tr>\n";
        } else {
            echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$right."&nbsp;</td></tr>\n";
        }
        if ($this->class == "odd" && $altern == "true") {
            $this->class = "even";
        } else if ($this->class == "even" && $altern == "true") {
            $this->class = "odd";
        }
    }

    function contentProgress($left,$right,$altern="true") {
        if ($this->class == "") {
            $this->class = "odd";
        }
        if ($left != "") {
            echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\" bgcolor=$right>".$left." :</td><td bgcolor=$right><b>".$right."&nbsp;</b></td></tr>\n";
        } else {
            echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$right."&nbsp;</td></tr>\n";
        }
        if ($this->class == "odd" && $altern == "true") {
            $this->class = "even";
        } else if ($this->class == "even" && $altern == "true") {
            $this->class = "odd";
        }
    }


    function openRow($isFiltered=false) {
        $change = "true";
        if($isFiltered==true){
            echo "<tr style=\"background-color: $this->filterColor\"  onmouseover=\"this.style.backgroundColor='".$this->filterColor."'\" onmouseout=\"this.style.backgroundColor='".$this->filterColor."'\">\n";
        } else {
            echo "<tr class=\"$this->class\" onmouseover=\"this.style.backgroundColor='".$this->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$this->highlightOff."'\">\n";
            if ($this->class == "odd") {
                $this->class = "even";
                $this->highlightOff = $this->evenColor;
                $change = "false";
            } elseif ($this->class == "even" && $change != "false") {
                $this->class = "odd";
                $this->highlightOff = $this->oddColor;
            }
        }
    }

    function checkboxRow($ref,$checkbox="true") {
    if ($checkbox == "true") {
        echo "<td align=\"center\"><a href=\"javascript:MM_toggleItem(document.".$this->form."Form, '".$ref."', '".$this->form."cb".$ref."','$this->theme')\"><img name=\"".$this->form."cb".$ref."\" border=\"0\" src=\"$this->pathImg/checkbox_off_16.gif\" alt=\"\" vspace=\"3\"></a></td>";
    } else {
        echo "<td><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/spacer.gif\" alt=\"\" vspace=\"3\"></td>";
    }
    }

    function cellRow($content) {
        echo "<td>$content</td>";
    }

    function cellProgress($content) {
        global $_CONF;

        if ($content == 'Red') {
            $image = 'redstate.gif';
        } elseif ($content == 'Yellow') {
            $image = 'yellowstate.gif';
        } else {
            $image = 'greenstate.gif';
        }
        echo '<td style="padding:1px 0px 1px 0px;"><img src="'.$_CONF['layout_url'] .'/nexproject/images/'.$image.'"></td>';
    }

    function closeRow() {
        echo "\n</tr>\n";
    }

    function contentTitle($title) {
        echo "<tr><th colspan=\"2\">".$title."</th></tr>";
    }

    function closeContent() {
        echo '</table>' . LB . '<hr style="margin-top:2px;" />' . LB;
    }

    function closeForm() {
        echo "</form>\n";
    }

    function openBreadcrumbs() {
        echo "<p class=\"breadcrumbs\">";
    }

    function itemBreadcrumbs($content) {
        if ($this->breadcrumbsTotal == "") {
            $this->breadcrumbsTotal = "0";
        }
        $this->breadcrumbs[$this->breadcrumbsTotal] = $content;
        $this->breadcrumbsTotal = $this->breadcrumbsTotal + 1;
    }

    function closeBreadcrumbs() {

        $items = $this->breadcrumbsTotal;
        for ($i=0;$i<$items;$i++) {
            echo $this->breadcrumbs[$i];
            if ($items-1 != $i) {
                echo " / ";
            }
        }

        echo "</p>\n\n";
    }

    function openNavigation() {
        echo "<p id=\"navigation\">";
    }

    function itemNavigation($content) {
    }

    function closeNavigation() {
        echo "</p>\n\n";
    }

    function openAccount() {
        echo "<p id=\"account\">";
    }

    function itemAccount($content) {
    }

    function closeaccount() {
         echo "</p>\n\n";
    }

    function buildLink($url,$label,$type,$title='',$pid='',$tid='') {
        if ($type == "in") {
            return "<a class=\"projectblocklinks\" href=\"$url\" TITLE=\"$title\">$label</a>";
        } else if ($type == "icone") {
            return "<a href=\"$url&amp;".session_name()."=".session_id()."\"><img src=\"../interface/icones/$label\" border=\"0\" alt=\"\"></a>";
        } else if ($type == "inblank") {
            return "<a class=\"projectblocklinks\" href=\"$url&amp;".session_name()."=".session_id()."\" target=\"_blank\">$label</a>";
        } else if ($type == "powered") {
            return "Powered by <a href=\"$url\" target=\"_blank\">$label</a>";
        } else if ($type == "out") {
            return "<a class=\"projectblocklinks\" href=\"$url\" target=\"_blank\">$label</a>";
        } else if ($type == "mail") {
            return "<a href=\"mailto:$url\">$label</a>";
        } else if ($type == 'context') {
            if ($tid != '') {
                return '<a id="aaa'.$tid.','.$pid.'" href="'.$url.'" onmouseover="loadContextMenu(this.id,'.$tid.','.$pid.');" oc="prj_movetaskMenu('.$tid.','.$pid.'); return false;" TITLE="'.$title.'">'.$label.'</a>';
            } else {
                return '<a id="bbb'.$pid.'" href="'.$url.'" onmouseover="loadContextMenu(this.id,0,'.$pid.');" oc="prj_movetaskMenu('.$pid.'); return false;" TITLE="'.$title.'">'.$label.'</a>';
            }
        } elseif ($type == 'mytaskcontext') {
            if($pid!=''){
                return '<a id="ccc'.$tid.'" href="'.$url.'" onmouseover="loadContextMenu(this.id,'.$tid.','.$pid.');" oc="prj_mytaskMenu('.$tid.'); return false;" TITLE="'.$title.'">'.$label.'</a>';
            }else{
                 return '<a id="ccc'.$tid.'" href="'.$url.'" onmouseover="loadContextMenu(this.id,'.$tid.');" oc="prj_mytaskMenu('.$tid.'); return false;" TITLE="'.$title.'">'.$label.'</a>';
            }
        }
    }

}

?>