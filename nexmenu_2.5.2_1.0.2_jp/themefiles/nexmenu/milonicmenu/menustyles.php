<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | menustyles.php                                                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
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

include ('../../../../lib-common.php');
?>

_menuCloseDelay=500           // The time delay for menus to remain visible on mouse out
_menuOpenDelay=150            // The time delay before menus open on mouse over
_subOffsetTop=10              // Sub menu top offset
_subOffsetLeft=-10            // Sub menu left offset

menuwidth="100%";

<!-- Define Menu Style to be used --> 
with(menuStyle1=new mm_style()){
    onbgcolor="#4F8EB6";
    oncolor="#ffffff";
    offbgcolor="#DCE9F0";
    offcolor="#515151";
    bordercolor="#296488";
    borderstyle="solid";
    borderwidth=1;
    separatorcolor="#2D729D";
    separatorsize="1";
    padding=5;
    fontsize="95%";
    fontstyle="normal";
    fontfamily="Verdana, Tahoma, Arial";
    itemwidth="195";
    pagecolor="black";
    pagebgcolor="#82B6D7";
    headercolor="#000000";
    headerbgcolor="#ffffff";
    subimagepadding="2 10 2 2";
    overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color='#777777', Direction=135, Strength=5)";
    outfilter="randomdissolve(duration=0.2)";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    openonclick=0;
    closeonclick=1;
    rawcss="padding-left:5px;padding-right:5px";
    imagepadding="3";
}

with(menuStyle2=new mm_style()){ 
    onbgcolor="#443F5C";
    oncolor="#ffff00";
    offbgcolor="#4358E1";
    offcolor="#FFFFFF";
    bordercolor="#000000";
    borderstyle="solid";
    borderwidth=1;
    separatorcolor="yellow";
    separatorsize="1";
    padding=5;
    fontsize="12px";
    fontstyle="normal";
    fontweight="normal";
    fontfamily="comic sans ms,helvetica";
    high3dcolor="#66ccff";
    low3dcolor="#000099";
    subimagepadding="2 10 2 2";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    itemwidth="160";
} 

with(menuStyle3=new mm_style()){ 
    onbgcolor="#4F8EB6";
    oncolor="#ffffff";
    offbgcolor="#DCE9F0";
    offcolor="#515151";
    bordercolor="#296488";
    borderstyle="solid";
    borderwidth=1;
    separatorcolor="#2D729D";
    separatorsize="1";
    padding=5;
    fontsize="12px";
    fontstyle="normal";
    fontweight="normal";
    fontfamily="comic sans ms,helvetica";
    subimagepadding="2 10 2 2";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    itemwidth="130";
}

with(XPClassicMenuStyle=new mm_style()){
    bgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/clxp_back.gif";
    bordercolor="#55A1FF";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="70%";
    fontstyle="normal";
    fontweight="normal";
    image="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/clxp_app.gif";
    offcolor="#000000";
    oncolor="#000000";
    onsubimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/clxp_whitearrow.gif";
    outfilter="fade(duration=0.5)";
    overbgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/clxp_back_on.gif";
    overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color=#939393', Direction=145, Strength=4)";
    padding=7;
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/clxp_blackarrow.gif";
    subimagepadding=4;
    menubgcolor="#ffffff";

}

with(XPMainStyle=new mm_style()){
    styleid=1;
    bordercolor="#8A867A";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Tahoma,Helvetica,Verdana";
    fontsize="70%";
    fontstyle="normal";
    fontweight="normal";
    offbgcolor="#ECE9D8";
    offcolor="#000000";
    onbgcolor="#C1D2EE";
    onborder="1px solid #316AC5";
    oncolor="#000000";
    padding=3;
    rawcss="padding-left:5px;padding-right:5px";
}

with(XPMenuStyle=new mm_style()){
    bordercolor="#8A867A";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Tahoma,Helvetica,Verdana";
    fontsize="70%";
    fontstyle="normal";
    fontweight="normal";
    image="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/xpblank.gif";
    imagepadding=3;
    menubgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/winxp.gif";
    offbgcolor="transparent";
    offcolor="#000000";
    onbgcolor="#C1D2EE";
    onborder="1px solid #316AC5";
    oncolor="#000000";
    outfilter="randomdissolve(duration=0.3)";
    overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color=#999999, Direction=135, Strength=5)";
    padding=4;
    separatoralign="right";
    separatorcolor="#C5C2B8";
    separatorpadding=1;
    separatorwidth="80%";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    subimagepadding=3;
    menubgcolor="#ffffff";
}



with(corpMenuStyle=new mm_style()){
    styleid=1;
    bordercolor="#ffffff";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="8pt";
    fontstyle="normal";
    fontweight="bold";
    headerbgcolor="#ffffff";
    headercolor="#000000";
    image="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/orangedots.gif";
    imagepadding=6;
    offbgcolor="#1B2C85";
    offcolor="#ffffff";
    onbgcolor="#CC6600";
    oncolor="#ffffff";
    outfilter="randomdissolve(duration=0.3)";
    overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color=#777777', Direction=135, Strength=5)";
    overimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/whitedots.gif";
    padding=6;
    pagebgcolor="#82B6D7";
    pagecolor="black";
    separatorcolor="#ffffff";
    separatorsize=1;
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    subimagepadding=2;
}

with(corpSubmenuStyle=new mm_style()){
    bordercolor="#ffffff";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="8pt";
    fontstyle="normal";
    headerbgcolor="#ffffff";
    headercolor="#000000";
    offbgcolor="#5871B3";
    offcolor="#ffffff";
    onbgcolor="#DC9B5B";
    oncolor="#ffffff";
    outfilter="randomdissolve(duration=0.3)";
    overfilter="Fade(duration=0.2);Alpha(opacity=100);Shadow(color=#777777', Direction=135, Strength=5)";
    padding=4;
    pagebgcolor="#82B6D7";
    pagecolor="black";
    separatorcolor="#758CC9";
    separatorsize=1;
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/arrow.gif";
    subimagepadding=2;
}

with(macMenuStyle=new mm_style()){
    bordercolor="#ABABAB";
    borderwidth=1;
    fontfamily="Arial";
    fontsize="80%";
    fontstyle="normal";
    fontweight="bold";
    itemheight=22;
    menubgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/mac_back.gif";
    offbgcolor="transparent";
    offcolor="#000000";
    onbgcolor="#3165B5";
    oncolor="#ffffff";
    outfilter="fade(duration=0.5)";
    overfilter="Fade(duration=0.5);Shadow(color=#ADAEAD,Direction=180,Strength=6";
    rawcss="padding-left:10px;padding-right:10px";
}

with(macSubmenuStyle=new mm_style()){
    styleid=1;
    bordercolor="#838383";
    borderwidth=1;
    fontfamily="Arial";
    fontsize="80%";
    fontstyle="normal";
    fontweight="bold";
    headercolor="#000000";
    image="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/mac_trans.gif";
    menubgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/mac_back.gif";
    offbgcolor="transparent";
    offcolor="#000000";
    onbgcolor="#3165B5";
    oncolor="#ffffff";
    onsubimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/macarrow_on.gif";
    outfilter="fade(duration=0.5)";
    overfilter="Fade(duration=0.5);Shadow(color=#ADAEAD,Direction=180,Strength=6";
    padding=2;
    rawcss="padding-left:5px;padding-right:5px;";
    separatorcolor="#D2D4D4";
    separatorpadding=5;
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/macarrow_off.gif";
    menubgcolor="#EBF0EC";
}

with(tabMenuStyle=new mm_style()){
    styleid=1;
    itemwidth="140";
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="10pt";
    fontweight="bold";
    headerbgcolor="#ffffff";
    headercolor="#000000";
    offbgcolor="#CCC";
    offcolor="#ffffff";
    oncolor="#000000";
    outfilter="fade(duration=0.5)";
    pagecolor="black";
    rawcss="padding-left:5px;padding-top:4px;padding-bottom:1px";
    menubgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/itemblue.gif;";
    bgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/itemblue.gif;";
    overbgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/itemcoral_on.gif";
    separatorcolor="#FFFFFF";
    separatorheight=16;
    separatorsize=1;
}

with(tabSubmenuStyle=new mm_style()){
    bordercolor="#CDCDCD";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="8pt";
    fontweight="normal";
    headercolor="#000000";
    offbgcolor="#E9E9E9";
    offcolor="#000000";
    onbgcolor="#ffffff";
    oncolor="#747A75";
    outfilter="fade(duration=0.5)";
    padding=5;
    pagecolor="black";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/9x6_rightbend_grey.gif";
    subimagepadding=8;
}


with(bwMenuStyle=new mm_style()){
    bordercolor="#000000";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="8pt";
    fontstyle="normal";
    fontweight="normal";
    headerbgcolor="#ffffff";
    headercolor="#000000";
    offbgcolor="#ffffff";
    offcolor="#000000";
    onbgcolor="#999999";
    oncolor="#000000";
    onsubimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/black_7x7.gif";
    padding=6;
    pagecolor="black";
    separatorcolor="#000000";
    separatorsize=1;
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/black_7x7.gif";
    subimagepadding=6;
}

with(bwSubmenuStyle=new mm_style()){
    styleid=1;
    bordercolor="#000000";
    borderstyle="solid";
    borderwidth=1;
    fontfamily="Verdana, Tahoma, Arial";
    fontsize="8pt";
    fontstyle="bold";
    headerbgcolor="#ffffff";
    headercolor="#000000";
    offbgcolor="#ffffff";
    offcolor="#000000";
    onbgcolor="#999999";
    oncolor="#000000";
    onsubimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/black_7x7.gif";
    padding=4;
    pagebgcolor="#82B6D7";
    pagecolor="black";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/menuimages/black_7x7.gif";
    subimagepadding=5;
}

with(background=new mm_style()){
    with(background=new mm_style()){
    bgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/xp_button_burnt.gif";
    borderstyle="solid";
    fontfamily="Helvetica";
    fontsize="75%";
    fontstyle="italic";
    fontweight="bold";
    image="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/transparent.gif";
    bgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/xp_button_green.gif";
    imagepadding=4;
    itemheight=25;
    itemwidth=140;
    offcolor="#333300";
    oncolor="#ffffff";
    overbgimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/xp_button_burnton.gif";
    subimage="<?php echo $_CONF['layout_url']; ?>/nexmenu/milonicmenu/images/black_7x7.gif";
    subimagepadding=8;
}

}




