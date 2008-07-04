<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.0 for Geeklog - The Ultimate Weblog
// | Official release date: Feb 7,2003
// +---------------------------------------------------------------------------+
// | japanese.php ���⤷���쥨�󥳡��ɤ�euc�Ǥʤ�����Ѵ����Ƥ�������
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000,2001 by the following authors: 
// | Geeklog Author: Tony Bibbs       - tony@tonybibbs.com
// +---------------------------------------------------------------------------+
// | FORUM Plugin Authors 
// | Prototype & Concept    :  Mr.GxBlock of www.gxblock.com 
// | Co-Developed by Matthew and Blaine
// | Matthew DeWyer, contact: matt@mycws.com          www.cweb.ws
// | Blaine Lang,    contact: geeklog@langfamily.ca   www.langfamily.ca
// +---------------------------------------------------------------------------+
// |  
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License
// | as published by the Free Software Foundation; either version 2 
// | of the License, or (at your option) any later version.
// | 
// | This program is distributed in the hope that it will be useful, 
// | but WITHOUT ANY WARRANTY; without even the implied warranty of
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// | GNU General Public License for more details.
// | 
// | You should have received a copy of the GNU General Public License
// | along with this program; if not, write to the Free Software Foundation,
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// | 
// +---------------------------------------------------------------------------+
//
# Tranlated by Geeklog Japanese group SaY and Ivy
//@@@@@20050628 2.3�Ѥ��顡2.3.2_1.3.9�Ѥ˲���
//@@@@@20051220 2.5RC1.3_1.3.11�Ѥ˲���
//@@@@@20060327 $LANG_GF00['adminmenu'] ���ܸ����Ѥ�����
//@@@@@20060427 $LANG_GF93['addmoderator']
//@@@@@20070104 2.6RC3�Ѥ˹���(mystral-kk)
# Last Update 2007/02/05 by Ivy (Geeklog Japanese)
//@@@@@20070319 2.6RC4�Ѥ˹���
//@@@@@20070326 2.6RC5(final)�Ѥ˹���
//@@@@@20070925 2.7�Ѥ˹��� $LANG_GF01['admin'],$LANG_GF93['vieworder'] �ɲ�

$LANG_GF00 = array (
    'admin_only'        => '�����ԤΤߤǤ����⤷���ʤ��������Ԥʤ顤��˥����󤷤Ƥ���������',
    'plugin'            => '�ץ饰����',
    'pluginlabel'       => '�Ǽ���',
    'searchlabel'       => '�Ǽ���',
    'statslabel'        => '���Ǽ������',
    'statsheading1'     => '�Ǽ��ľ��10�̱�����',
    'statsheading2'     => '�Ǽ��ľ��10�̽񤭹��߿�',
    'statsheading3'     => '��ƤϤ���ޤ���',
    'searchresults'     => '�Ǽ��ĸ������ %s',
    'useradminmenu'     => '�Ǽ��Ĥε�ǽ',
    'useradmintitle'    => '�Ǽ��ĤΥ桼������',
    'access_denied'     => '�������������ݤ���ޤ���',
    'access_denied_msg' => 'Root�桼���Τߤ��Υڡ����˥��������Ǥ��ޤ������ʤ���̾����IP���ɥ쥹�ϵ�Ͽ����ޤ�����',
    'admin'             => '�ץ饰���������',
    'install_header'    => '�ץ饰����Υ��󥹥ȡ���/���󥤥󥹥ȡ���',
    'installed'         => '�ץ饰����ȥ֥�å������󥹥ȡ��뤵��ޤ�����<p><i>�ڤ���Ǥ�������<br><a href="mailto:langmail@sympatico.ca">Blaine</a></i>',
    'uninstalled'       => 'Forum�ץ饰����ϥ��󥤥󥹥ȡ��뤵��ޤ�����',
    'install_success'   => '���󥹥ȡ����<p><b>���Υ��ƥåפ�</b>:   <ol><li>�Ǽ��Ĵ����Ԥϡ��������Ǽ��Ĥ��ߤ��Ƥ���������<li>�Ǽ��Ĥ�����ȸĿͤ����������ꤷ�Ƥ��������� <li>���ʤ��Ȥ�Ǽ��Ĥ�1�ġ����ƥ����1�ġ��������Ƥ���������</ol> <p><a href="%s">���󥹥ȡ�������</a> ����٤�������������',





    'install_failed'    => '���󥹥ȡ��뼺�ԤǤ��� -- ���顼��(error.log)�򸫤Ƹ�����Τ���Ƥ���������',
    'uninstall_msg'     => 'Forum�ץ饰����򥢥󥤥󥹥ȡ��뤷�ޤ�����',
    'install'           => '���󥹥ȡ���',
    'uninstall'         => '���󥤥󥹥ȡ���',
    'enabled'           => '<br>�ץ饰����ϥ��󥹥ȡ��뤵�졤ͭ���ˤʤäƤ��ޤ���<p>',
    'warning'           => '�Ǽ��ĥ��󥤥󥹥ȡ���ٹ�',
    'uploaderr'         => '�ե����륢�åץ��ɥ��顼'
);


$PLG_forum_MESSAGE1 = '�Ǽ��ĥץ饰���󥢥åץ��졼��: �������ޤ�����';
$PLG_forum_MESSAGE2 = '�Ǽ��ĥץ饰���󥢥åץ��졼��: ��ư���󥹥ȡ��뼺�ԡ��ץ饰����ɥ�����Ȥ�������������';

$LANG_GF01['LOGIN']          = '������';
$LANG_GF01['FORUM']          = '�Ǽ���';
$LANG_GF01['ALL']            = '���٤�'; 
$LANG_GF01['YES']            = '�Ϥ�';
$LANG_GF01['NO']             = '������';
$LANG_GF01['NEW']            = '����';
$LANG_GF01['PREV']           = '�ץ�ӥ塼';
$LANG_GF01['NEXT']           = '����';
$LANG_GF01['ERROR']          = '���顼!';
$LANG_GF01['CONFIRM']        = '��ǧ';
$LANG_GF01['UPDATE']         = '����';
$LANG_GF01['SAVE']           = '��¸';
$LANG_GF01['CANCEL']         = '���ä�';
$LANG_GF01['CLOSE']          = '�Ĥ���';
$LANG_GF01['ON']             = '�����: ';
$LANG_GF01['ON2']            = '&nbsp;&nbsp;<b>����: </b>';
$LANG_GF01['IN']             = '����: ';
$LANG_GF01['BY']             = '��Ƽ�: ';
$LANG_GF01['RE']             = '�񤭹���: ';
$LANG_GF01['NA']             = 'N/A';
$LANG_GF01['DATE']           = '����';
$LANG_GF01['VIEWS']          = '������';
$LANG_GF01['REPLIES']        = '�񤭹��߿�';
$LANG_GF01['NAME']           = '̾��:';
$LANG_GF01['DESCRIPTION']    = '����: ';
$LANG_GF01['TOPIC']          = '��̾';
$LANG_GF01['TOPICS']         = '���:';
$LANG_GF01['TOPICSUBJECT']   = '��̾';
$LANG_GF01['FROM']           = '����';
$LANG_GF01['REPLY']          = '�������񤭹���';
$LANG_GF01['PM']             = 'PM';
$LANG_GF01['HOME']           = '�Ǽ���ɽ��';
$LANG_GF01['HOMEPAGE']       = '�ۡ���';
$LANG_GF01['SUBJECT']        = '��̾';
$LANG_GF01['HELLO']          = '����ˤ��ϡ� ';
$LANG_GF01['MEMBERS']        = '���С�';
$LANG_GF01['MOVED']          = '��ư';
$LANG_GF01['REMOVE']         = '��ư&amp;���';
$LANG_GF01['CURRENT']        = '�ǿ�';
$LANG_GF01['STARTEDBY']      = '�ǽ����Ƽ�';
$LANG_GF01['POSTS']          = '��ƿ�';
$LANG_GF01['LASTPOST']       = '�ǿ����';
$LANG_GF01['POSTEDBY']       = '��Ƽ�';
$LANG_GF01['POSTEDON']       = '�����';
$LANG_GF01['PAGE']           = '�ڡ���';
$LANG_GF01['PAGES']          = '�ڡ���';
$LANG_GF01['ANONYMOUS']      = '�����ȥ桼��:';
$LANG_GF01['TODAY']          = '������';
$LANG_GF01['WELCOME']        = '�褦���� ';
$LANG_GF01['REGISTER']       = '��Ͽ';
$LANG_GF01['REGISTERED']     = '��Ͽ��';
$LANG_GF01['MOSTPOPULAR']    = '��äȤ�͵�';
$LANG_GF01['ORDERBY']        = '�¤Ӵ���:';
$LANG_GF01['ORDER']          = '����:';
$LANG_GF01['USER']           = '�桼��';
$LANG_GF01['GROUP']          = '���롼��';
$LANG_GF01['ANON']           = '�����ȥ桼��: ';
$LANG_GF01['ADMIN']          = '������';
$LANG_GF01['AUTHOR']         = '��Ƽ�';
$LANG_GF01['LOCATION']       = '���';
$LANG_GF01['WEBSITE']        = '�ۡ���ڡ���';
$LANG_GF01['EMAIL']          = '�᡼��';
$LANG_GF01['MOOD']           = '��ʬ';
$LANG_GF01['NOMOOD']         = '-��ʬ��������-';
$LANG_GF01['REQUIRED']       = '[�׵�]';
$LANG_GF01['OPTIONAL']       = '[���ץ����]';
$LANG_GF01['SUBMIT']         = '��¸';
$LANG_GF01['PREVIEW']        = '�ץ�ӥ塼';
$LANG_GF01['NOTIFY']         = '�����:';
$LANG_GF01['REMOVE']         = '���';
$LANG_GF01['KEYWORDS']       = '�������';
$LANG_GF01['EDIT']           = '�Խ�';
$LANG_GF01['DELETE']         = '���';
$LANG_GF01['MESSAGE']        = '��å�����:';
$LANG_GF01['OPTIONS']        = '���ץ����:';
$LANG_GF01['MISSINGSUBJECT'] = '��̾�ʤ�';
$LANG_GF01['MAY']            = '';
$LANG_GF01['IS']             = '��';
$LANG_GF01['FOR']            = '��';
$LANG_GF01['ARE']            = '';
$LANG_GF01['NOT']            = '��';
$LANG_GF01['YOU']            = '';
$LANG_GF01['HTML']           = 'HTML';
$LANG_GF01['FULLHTML']       = '���Ƥ�HTML';
$LANG_GF01['WORDS']          = 'ñ��';
$LANG_GF01['SMILIES']        = '���ޥ��꡼';
$LANG_GF01['MIGRATE_NOW']    = '����ݡ��ȼ¹�'; 
$LANG_GF01['FILTERLIST']     = '�ե��륿�ꥹ��';
$LANG_GF01['SELECTFORUM']    = '�Ǽ��Ĥ�����';
$LANG_GF01['DELETEAFTER']    = '�¹Ը�˺��';
$LANG_GF01['TITLE']          = '�����ȥ�';
$LANG_GF01['COMMENTS']       = '������'; 
$LANG_GF01['SUBMISSIONS']    = '��Ƥ������';

$LANG_GF01['HTML_FILTER_MSG']  = '������HTML�����';
$LANG_GF01['HTML_FULL_MSG']  = '���٤Ƥ�HTML�����';
$LANG_GF01['HTML_MSG']       = 'HTML����';
$LANG_GF01['CENSOR_PERM_MSG']  = '';
$LANG_GF01['ANON_PERM_MSG']    = '�����ȥ桼������Ƥ򸫤�';
$LANG_GF01['POST_PERM_MSG1']    = '��Ʋ�ǽ';
$LANG_GF01['POST_PERM_MSG2']    = '�����ȥ桼����Ʋ�ǽ';
$LANG_GF01['CENSORED']       = '����';
$LANG_GF01['ALLOWED']        = '����';
$LANG_GF01['GO']             = '�¹�';
$LANG_GF01['STATUS']         = '����:';
$LANG_GF01['ONLINE']         = '����饤��';
$LANG_GF01['OFFLINE']        = '���ե饤��';
$LANG_GF01['back2parent']    = '�Ƥ����';
$LANG_GF01['forumname']      = '';
$LANG_GF01['category']       = '���ƥ���: ';
$LANG_GF01['loginreqview']   = '<b>�Ǽ��Ĥ˻��ä��뤿��ˤϡ� %s ��Ͽ</a> �ޤ��� %s ������ </a> ���Ƥ���������</b>';
$LANG_GF01['loginreqpost']   = '<b>��Ƥ��뤿��ˤϡ���Ͽ�ޤ��ϥ����󤷤Ƥ���������</b>';
$LANG_GF01['searchresults']  = ' ������� <b>%s</b> %s �� <b>%s</b> ���:</b><br><br>';
$LANG_GF01['feature_not_on'] = '���ܵ���';
$LANG_GF01['nolastpostmsg']  = 'N/A';
$LANG_GF01['no_one']         = '1�ĤǤϤʤ���';
$LANG_GF01['popular']        = '�͵���ꥹ��';
$LANG_GF01['notify']         = '����';
$LANG_GF01['PM']             = 'PM\'s';
$LANG_GF01['NEW_PM']         = 'New PM';
$LANG_GF01['DELALL_PM']      = '���ƺ��';
$LANG_GF01['DELOLDER_PM']    = '�Ť�ȯ������';
$LANG_GF01['members']        = '���С��ꥹ��';
$LANG_GF01['save_sucess']    = '��¸����';
$LANG_GF01['trademark']      = '<br><center><b>Geeklog Forum Project version 2.0</b> &copy; 2002</b></center>';
$LANG_GF01['back2top']       = '�ȥåפ����';
$LANG_GF01['POSTMODE']       = '��ƥ⡼��:';
$LANG_GF01['TEXTMODE']       = '�ƥ����ȥ⡼��';
$LANG_GF01['HTMLMODE']       = 'HTML�⡼��';
$LANG_GF01['TopicPreview']   = '��ƥץ�ӥ塼';
$LANG_GF01['moderator']      = '��ǥ졼��';
$LANG_GF01['admin']          = '������';
$LANG_GF01['DATEADDED']      = '��Ͽ��';
$LANG_GF01['PREVTOPIC']      = '���Υȥԥå���';
$LANG_GF01['NEXTTOPIC']      = '���Υȥԥå���';
$LANG_GF01['CONTENT']        = '����';
$LANG_GF01['QUOTE_begin']    = '[����&nbsp;';
$LANG_GF01['QUOTE_by'   ]    = 'by:&nbsp;';
$LANG_GF01['RESYNC']         = "����";
$LANG_GF01['RESYNCCAT']      = "���ƥ���򹹿�";  
$LANG_GF01['PROFILE']        = "�ץ�ե�����";
$LANG_GF01['DELETECONFIRM']  = "������Ƥ褤�Ǥ���?";
$LANG_GF01['website']        = '�ۡ���ڡ���';
$LANG_GF01['EDITICON']       = '�Խ�';
$LANG_GF01['QUOTEICON']      = '���Ѥ��ƽ񤭹���';
$LANG_GF01['ProfileLink']    = '�ץ�ե�����';
$LANG_GF01['WebsiteLink']    = '�ۡ���ڡ���';
$LANG_GF01['PMLink']         = 'PM';
$LANG_GF01['EmailLink']      = '�᡼��';
$LANG_GF01['FORUMSUBSCRIBE'] = '���ηǼ��Ĥι�����᡼������Τ���褦���ꤹ��';
$LANG_GF01['FORUMUNSUBSCRIBE'] = '���ηǼ��Ĥι�����᡼������Τ��ʤ��褦���ꤹ��';
$LANG_GF01['NEWTOPIC']       = '�������';
$LANG_GF01['POSTREPLY']      = '���';
$LANG_GF01['SubscribeLink']  = '�᡼����������';
$LANG_GF01['unSubscribeLink'] = '�᡼������������';
$LANG_GF01['FORUMSUBSCRIBE'] = '�Ǽ��ĥ᡼����������';
$LANG_GF01['NEWTOPIC']       = '�ȥԥå�������Ͽ';
$LANG_GF01['POSTREPLY']      = '�������񤭹���';
$LANG_GF01['SUBSCRIPTIONS']  = '�᡼����������ꥹ��';
$LANG_GF01['TOP']            = '�ȥԥå��ȥå�';
$LANG_GF01['PRINTABLE']      = '�����ѥڡ���';
$LANG_GF01['ForumProfile']   = '�Ǽ��ĥ��ץ����';
$LANG_GF01['USERPREFS']      = '�Ǽ��ĤΥ桼������';
$LANG_GF01['SPEEDLIMIT']     = '"���ʤ��κǿ�����Ƥ� %s �����Ǥ�����<br>������Ƥޤǡ����� %s �ä��Ԥ�����������"';
$LANG_GF01['ACCESSERROR']    = '�����������顼';
$LANG_GF01['LEGEND']         = '����';
$LANG_GF01['ACTIONS']        = '���������';
$LANG_GF01['DELETEALL']      = '���٤Ƥ����򤷤��ǡ�������';
$LANG_GF01['DELCONFIRM']     = '���򤷤��ǡ����������Ƥ�����Ǥ�����';
$LANG_GF01['DELALLCONFIRM']  = '���٤ƤΥǡ����������Ƥ�����Ǥ�����';
$LANG_GF01['STARTEDBY']      = '������:';
$LANG_GF01['WARNING']        = '�����';
$LANG_GF01['MODERATED']      = '��ǥ졼��: %s';
$LANG_GF01['NOTIFYNOT']      = 'NOT!';
$LANG_GF01['LASTREPLYBY']    = '�ǿ��ν񤭹��߼�:&nbsp;%s';
$LANG_GF01['UID']            = 'UID';
$LANG_GF01['ANON_POST_BEGIN'] = '�����ȥ桼����ƥ�������';
$LANG_GF01['ANON_POST_END']   = '�����ȥ桼��������λ';
$LANG_GF01['INDEXPAGE']      = '�Ǽ����ܼ�';
$LANG_GF01['FEATURE']        = '��ǽ';
$LANG_GF01['SETTING']        = '����';
$LANG_GF01['MARKALLREAD']    = '���٤ƴ��ɤˤ���';

// Language for bbcode toolbar
$LANG_GF01['CODE']           = '������';
$LANG_GF01['FONTCOLOR']      = 'ʸ����';
$LANG_GF01['FONTSIZE']       = 'ʸ��������';
$LANG_GF01['CLOSETAGS']      = '�������Ĥ���';
$LANG_GF01['CODETIP']        = '�ҥ��: ���򤷤�ʸ����ˤ����˥��������Ŭ�ѤǤ��ޤ�';
$LANG_GF01['TINY']           = '������';
$LANG_GF01['SMALL']          = '������';
$LANG_GF01['NORMAL']         = 'ɸ��';
$LANG_GF01['LARGE']          = '�礭��';
$LANG_GF01['HUGE']           = '�礭��';
$LANG_GF01['DEFAULT']        = '����';
$LANG_GF01['DKRED']          = 'ǻ��';
$LANG_GF01['RED']            = '��';
$LANG_GF01['ORANGE']         = '�����';
$LANG_GF01['BROWN']          = '��';
$LANG_GF01['YELLOW']         = '��';
$LANG_GF01['GREEN']          = '��';
$LANG_GF01['OLIVE']          = '���꡼��';
$LANG_GF01['CYAN']           = '�忧';
$LANG_GF01['BLUE']           = '��';
$LANG_GF01['DKBLUE']         = 'ǻ��';
$LANG_GF01['INDIGO']         = '����';
$LANG_GF01['VIOLET']         = '��';
$LANG_GF01['WHITE']          = '��';
$LANG_GF01['BLACK']          = '��';

$LANG_GF01['b_help']         = "�����ˤ���: [b]text[/b]";
$LANG_GF01['i_help']         = "������å��Τˤ���: [i]text[/i]";
$LANG_GF01['u_help']         = "���������: [u]text[/u]";
$LANG_GF01['q_help']         = "���Ѥ���: [quote]text[/quote]";
$LANG_GF01['c_help']         = "�����ɤ�ɽ������: [code]code[/code]";
$LANG_GF01['l_help']         = "�����ʤ��ꥹ�Ȥˤ���: [list]text[/list]";
$LANG_GF01['o_help']         = "�����դ��ꥹ�Ȥˤ���: [olist]text[/olist]";
$LANG_GF01['p_help']         = "[img]http://������url[/img]  �ޤ���  [img w=100 h=200][/img]";
$LANG_GF01['w_help']         = "URL����������: [url]http://url[/url] �ޤ��� [url=http://url]URL�ƥ�����[/url]";
$LANG_GF01['a_help']         = "�Ĥ��Ƥ��ʤ�bbCode�Υ����򤹤٤��Ĥ���";
$LANG_GF01['s_help']         = "ʸ����: [color=red]text[/color]  �ҥ��: color=#FF0000 �Ȥ��������Ǥ����Ǥ��ޤ�";
$LANG_GF01['f_help']         = "ʸ��������: [size=x-small]�������ʸ��[/size]";
$LANG_GF01['h_help']         = "�ܺ٤򸫤�ˤϥ���å����Ƥ�������";


$LANG_GF02['msg01']    = '����������ޤ��󤬡��Ǽ��ؤλ��äˤ���Ͽ��ɬ�פǤ��� ';
$LANG_GF02['msg02']    = '����������ޤ��󤬡����ηǼ��Ĥؤλ��äˤ���Ͽ��ɬ�פǤ���';
$LANG_GF02['msg03']    = '';
$LANG_GF02['msg04']    = '';
$LANG_GF02['msg05']    = '<center><i>�ޤ���Ͽ����Ƥ��ޤ���</center></i>';
$LANG_GF02['msg06']    = '�Ǹ��ˬ��ʸ�����';
$LANG_GF02['msg07']    = '����饤��桼��:';
$LANG_GF02['msg08']    = '<br><b>���٤Ƥ���Ͽ�桼������Ͽ����:</b> %s';
$LANG_GF02['msg09']    = '<br><b>���٤Ƥ��������:</b> %s <br>';
$LANG_GF02['msg10']    = '<b>���٤Ƥ��������:</b> %s <br>';
$LANG_GF02['msg11']    = '�Ǽ��ĥ���ǥå��������';
$LANG_GF02['msg12']    = '�ᥤ��ۡ���ڡ��������';
$LANG_GF02['msg13']    = '��Ͽ��ɬ�פǤ���';
$LANG_GF02['msg14']    = '��Ͽ��ɬ�פǤ���<br>';
$LANG_GF02['msg15']    = '���顼���Ȼפ�줿�顤 <a href="mailto:%s?subject=Guestbook IP Ban">�Ǽ��Ĵ�����</A>�ޤǡ�';
$LANG_GF02['msg16']    = '�褯������ƤǤ���¾����Ƥ�񤭹��ߤ�������������';
$LANG_GF02['msg17']    = '��å������Խ�����λ������ޤ�����';
$LANG_GF02['msg18']    = '���顼! ɬ�ܹ��ܤ����Ϥ���Ƥ��ʤ����ޤ���û�����ޤ���';
$LANG_GF02['msg19']    = '��å���������Ͽ����ޤ���';
$LANG_GF02['msg20']    = '�񤭹��ߤ���Ͽ����ޤ�����';
$LANG_GF02['msg21']    = '�¹Ը��¤�����ޤ���<a href="javascript:history.back()">���</a>��<a href="%s/users.php?mode=login">������</a>���뤫���Ƥ���������<br><br>'; 
$LANG_GF02['msg22']    = '- �Ǽ����������';
$LANG_GF02['msg23a']   = "�Ǽ���[%s]��%s���󤫤鿷�����񤭹��ߤ�����ޤ�����\n�ʥȥԥå������ԡ�%s���󡡷Ǽ��ġ�%s��";
$LANG_GF02['msg23b']   = "�������ȥԥå� '%s' ��\n%s ����ˤ�ä� %s �Ǽ��Ĥ���Ƥ���ޤ�����\n�ʥ����ȡ�%s��\n\n%s/forum/viewtopic.php?showtopic=%s\n";
$LANG_GF02['msg23c']   = "%s/forum/viewtopic.php?showtopic=%s&amp;lastpost=true\n";
$LANG_GF02['msg24']    = '����åɤ򸫤ƽ񤭹���: ';
$LANG_GF02['msg25']    = "\n";
$LANG_GF02['msg26']    = "\n�����Υȥԥå��ϥ᡼�����Τ����ꤵ��Ƥ��ޤ���";
$LANG_GF02['msg27']    = "\n�᡼�����β��: \n%s\n";
$LANG_GF02['msg28']    = '���顼����̾������ޤ���';
$LANG_GF02['msg29']    = '���ʤ��ν�̾�Ϥ�����ɽ������ޤ���';
$LANG_GF02['msg30']    = '�ȥåפ����';
$LANG_GF02['msg31']    = '<b>�Խ��Ǥ��ޤ�:</b>';
$LANG_GF02['msg32']    = '<b>��å��������Խ�</b>';
$LANG_GF02['msg33']    = '��Ƽ�: ';
$LANG_GF02['msg34']    = '�᡼��:';
$LANG_GF02['msg35']    = '�ۡ���ڡ���:';
$LANG_GF02['msg36']    = '��ʬ��������:';
$LANG_GF02['msg37']    = '��å�����:';
$LANG_GF02['msg38']    = '�����񤭹��ߤ�᡼������Ρ�';
$LANG_GF02['msg39']    = '<br>������ƥ�ӥ塼�Ǥ��ޤ���';
$LANG_GF02['msg40']    = '<br>���˥᡼���������ꤵ��Ƥ��ޤ���<br><br>';
$LANG_GF02['msg41']    = '<br> %s �ؤ���Ƥϥ᡼���������ꤵ��ޤ�����<br><br>';
$LANG_GF02['msg42']    = '���Υȥԥå��Υ᡼����������������ޤ�����';
$LANG_GF02['msg43']    = '���Υ᡼����������������Ƥ褤�Ǥ���?.';
$LANG_GF02['msg44']    = '<p style="margin:0px; padding:5px;">�᡼���������ꤷ���ȥԥå��Ϥ���ޤ���</p>';
$LANG_GF02['msg45']    = '�Ǽ��Ĥθ���';
$LANG_GF02['msg46']    = '�Ǽ��ĥ�����ɸ���:';
$LANG_GF02['msg47']    = '��Ƽ�̾����ꤹ�뤳�Ȥ�Ǥ��ޤ�:';
$LANG_GF02['msg48']    = '<br>���Chatterblock�ץ饰����򥤥󥹥ȡ��뤷�Ƥ���������';
$LANG_GF02['msg49']    = '(���ȿ� %s��) ';
$LANG_GF02['msg50']    = '��̾ n/a';
$LANG_GF02['msg51']    = "%s\n\n<br>[�Խ� %s by %s]";
$LANG_GF02['msg52']    = '����:';
$LANG_GF02['msg53']    = '�ȥԥå������ޤ�..';
$LANG_GF02['msg54']    = '��Ƥ��Խ�����ޤ�����';
$LANG_GF02['msg55']    = '�������ޤ�����';
$LANG_GF02['msg56']    = 'IP���ɥ쥹�϶ػߤ���ޤ�����';
$LANG_GF02['msg57']    = '���ܥȥԥå�����';
$LANG_GF02['msg58']    = '���ܥȥԥå�������';
$LANG_GF02['msg59']    = '�̾�';
$LANG_GF02['msg60']    = '����';
$LANG_GF02['msg61']    = '���ܥȥԥå�';
$LANG_GF02['msg62']    = '�񤭹��ߤ�����Х᡼�����Τ���';
$LANG_GF02['msg63']    = '�ץ�ե�����';
$LANG_GF02['msg64']    = '�ȥԥå� %s ��̾: %s ���������˺�����Ƥ������Ǥ���?';
$LANG_GF02['msg65']    = '<br>����Ͽ���ƤǤ������Τ��ᤳ�Υȥԥå�����Τ��٤Ƥν񤭹��ߤ⤢�碌�ƺ������ޤ���';
$LANG_GF02['msg66']    = '��ƺ������';
$LANG_GF02['msg67']    = '�ե��顼������Խ�';
$LANG_GF02['msg68']    = '���: �ػߤ���տ����ԤäƤ��������������Ԥ������ػ߼Ԥ����Ǥ��ޤ���';
$LANG_GF02['msg69']    = '<br>�����ˤ���IP���ɥ쥹��ػߤ��ޤ���: %s?';
$LANG_GF02['msg70']    = '�ػ߳���';
$LANG_GF02['msg71']    = '��ǽ�����򤵤�Ƥ��ޤ�����Ƥ����򤷥�ǥ졼���ε�ǽ��¹Ԥ��Ƥ���������<br>���:���ʤ��ϥ�ǥ졼���ȤʤäƤ����ε�ǽ��ȤäƤ���������';
$LANG_GF02['msg72']    = '���Υ�å�����������饤��ʤ�����Ե�ǽ���������ޤ���';
$LANG_GF02['msg74']    = '�Ƕ����� %s ��';
$LANG_GF02['msg75']    = '�������ȥå� %s ��';
$LANG_GF02['msg76']    = '��ƿ��ȥå� %s ��';
$LANG_GF02['msg77']    = '<br><p style="padding-left: 10px;">����������ޤ�����˥����󤷤Ƥ�����������������Ȥ��ʤ���п�����Ͽ���Ƥ���������<p />';
$LANG_GF02['msg78']    = '<br>�����˷Ǽ��ĤϤ���ޤ���';
$LANG_GF02['msg81']    = '- ����Խ�����';
$LANG_GF02['msg82']    = '<p>���ʤ��Υ�å����� "%s" �ϡ���ǡ��졼�� %s �ˤ�ä��Խ�����ޤ�����<p>';
$LANG_GF02['msg83']    = '<br><br>�Ǽ��ĤΥ����ȥ�����Ϥ��Ƥ���������<p />';
$LANG_GF02['msg84']    = '���٤��ɤ�����Ȥˤ���';
$LANG_GF02['msg85']    = '�ڡ���:';
$LANG_GF02['msg86']    = '�ǿ� %s ��ơ���Ƽ�: ';
$LANG_GF02['msg87']    = '<br>�ٹ�:���Υȥԥå��ϥ�å�����Ƥ��ޤ���<br>�ɲä���ƤϤǤ��ޤ���';
$LANG_GF02['msg88']    = '�Ǽ�����Ƽԥꥹ��';
$LANG_GF02['msg88b']   = '�Ǽ���ȯ���ԤΤ�';
$LANG_GF02['msg89']    = '�᡼����������ꥹ��';
$LANG_GF02['msg100']   = '����:';
$LANG_GF02['msg101']   = '�롼��:';
$LANG_GF02['msg102']   = '��ƥơ���:';
$LANG_GF02['msg103']   = '�Ǽ��ĥ�����:';
$LANG_GF02['msg104']   = '��ƥ�å�����';
$LANG_GF02['msg105']   = '���ʤ�����Ƥ��Խ�';
$LANG_GF02['msg106']   = '�Ǽ��Ĥ�����';
$LANG_GF02['msg107']   = '�Ǽ��ĥơ���:';
$LANG_GF02['msg108']   = '������ƤΤ���Ǽ���';
$LANG_GF02['msg109']   = '��å����줿�ȥԥå�';
$LANG_GF02['msg110']   = '�Խ��ڡ����˰�ư��..';
$LANG_GF02['msg111']   = '̤�ɥꥹ��:';
$LANG_GF02['msg112']   = '̤�ɤ�ɽ������';
$LANG_GF02['msg113']   = '̤�ɤ�ɽ������';
$LANG_GF02['msg114']   = '��å���';
$LANG_GF02['msg115']   = '���ܥȥԥå�����';
$LANG_GF02['msg116']   = '��å��ѥȥԥå� ����';
$LANG_GF02['msg117']   = '�����ȸ���';
$LANG_GF02['msg118']   = '�Ǽ��ĸ���';
$LANG_GF02['msg119']   = '�������:';
$LANG_GF02['msg120']   = '�͵��� by';
$LANG_GF02['msg121']   = '���٤Ƥλ���� %s�����ߡ�%s';
$LANG_GF02['msg122']   = '�͵���ꥹ�ȷ��:';
$LANG_GF02['msg123']   = '�͵���ꥹ�Ȥ�ɽ��������';
$LANG_GF02['msg124']   = '1�ڡ������ȤΥ�å�������';
$LANG_GF02['msg125']   = '��ǥ졼���Τ�: ��å�������������';
$LANG_GF02['msg126']   = '�����饤��:';
$LANG_GF02['msg127']   = 'õ����̤�ɽ������饤��ο�';
$LANG_GF02['msg128']   = '��ƼԿ�/1�ڡ���:';
$LANG_GF02['msg129']   = '��Ƽԥꥹ�Ȥ�1�ڡ�����ɽ������Ϳ�';
$LANG_GF02['msg130']   = '�����ȥ桼�����ɽ��:';
$LANG_GF02['msg131']   = '�����ȥ桼����Ƥ����̤���';
$LANG_GF02['msg132']   = '�᡼����������⡼��:';
$LANG_GF02['msg133']   = '�񤭹��ߤ�����Х᡼�����Τ�����ͤˤ���';
$LANG_GF02['msg134']   = '��Ƥ��ɲä���ޤ�����';
$LANG_GF02['msg135']   = '���ʤ������Ƥ���Ƥ��Ǽ��Ĥ����Τ���ޤ�';
$LANG_GF02['msg136']   = '�����ηǼ��Ĥ�����Ǥ���������';
$LANG_GF02['msg137']   = '�񤭹��ߤ�����Х᡼������Τ���ޤ���';
$LANG_GF02['msg138']   = '<b>�Ǽ�������</b>';
$LANG_GF02['msg139']   = '%s ³�������<a href="%s">����å�</a>';
$LANG_GF02['msg140']   = '³����';
$LANG_GF02['msg141']   = '���Υڡ����ϼ�ưŪ�����ޤ������ʤ����� <a href="%s">������</a>';
$LANG_GF02['msg142']   = '�᡼����������⡼�ɤ��ѹ����ޤ�����';
$LANG_GF02['msg143']   = '����';
$LANG_GF02['msg144']   = '�ȥԥå���';
$LANG_GF02['msg145']   = '����åɻ���';
$LANG_GF02['msg146']   = '�᡼����������⡼�ɤϲ������ޤ�����';
$LANG_GF02['msg147']   = '�Ǽ��Ĥΰ���';
$LANG_GF02['msg148']   = '<a href="javascript:history.back()">���</a>';
$LANG_GF02['msg149']   = ' %s ���󥹥���ȥ�å����������äƤ���������';
$LANG_GF02['msg150']   = '���ʤ������ %s�����';
$LANG_GF02['msg151']   = '�ǿ������';
$LANG_GF02['msg152']   = '�͵������';
$LANG_GF02['msg153']   = '�͵��ν񤭹��ߥȥԥå�';
$LANG_GF02['msg154']   = '�ǿ������';
$LANG_GF02['msg155']   = '��Ƥʤ�';
$LANG_GF02['msg156']   = '��ƿ�';
$LANG_GF02['msg157']   = '�ǿ�10���';
$LANG_GF02['msg158']   = '�ǿ�10��Ƽ� ';
$LANG_GF02['msg159']   = '��ǥ졼���Υǡ����������˺�����Ƥ�褤�Ǥ�����';
$LANG_GF02['msg160']   = '��ƤκǸ�Υڡ����򸫤�';
$LANG_GF02['msg161']   = '���С��ꥹ�Ȥ����';
$LANG_GF02['msg162']   = '<a href="%s">������</a><br><p>�ؼ�ưŪ�����ޤ�������������ꤿ������ <a href="%s">������</a>';
$LANG_GF02['msg163']   = '��Ƥ���ư���ޤ���';
$LANG_GF02['msg164']   = '���٤��ɤ�����Ȥˤ���';
$LANG_GF02['msg165']   = '���顼��<p>�ޥå����� <b>����</b> ����������ޤ��󡣥ե����ޥåȤǤ��ޤ���</p>';
$LANG_GF02['msg166']   = '���顼: ���������줿�������Ĥ���ޤ���';
$LANG_GF02['msg167']   = '���Υ��ץ����';
$LANG_GF02['msg168']   = '�᡼�����Τ�̵���ˤ���';
$LANG_GF02['msg169']   = '���С��ꥹ�Ȥ����';
$LANG_GF02['msg170']   = '�ǿ������';
$LANG_GF02['msg171']   = '�Ǽ��ĥ����������顼';
$LANG_GF02['msg172']   = '��Ƥ��ʤ������������Ƥ��ޤ���';
$LANG_GF02['msg173']   = '��å�������ƥڡ����ذܤ�ޤ�...';
$LANG_GF02['msg174']   = 'BAN Member������ޤ��� - IP���ɥ쥹������';
$LANG_GF02['msg175']   = '�Ǽ��İ��������';
$LANG_GF02['msg176']   = '���С�����';
$LANG_GF02['msg177']   = '���٤ƤΥ��С�';
$LANG_GF02['msg178']   = '�Ƥ���ƤΤ�';
$LANG_GF02['msg179']   = '��������: %s ��';
$LANG_GF02['msg180']   = '�Ǽ�����Ʒٹ�';
$LANG_GF02['msg181']   = '���ʤ��ϷǼ��Ĵ����ԤȤ��ƥ��������Ǥ��ޤ���';
$LANG_GF02['msg182']   = '��ǥ졼����ǧ';
$LANG_GF02['msg183']   = '�������: %s';
$LANG_GF02['msg184']   = '1��Τ�����';
$LANG_GF02['msg185']   = '����ˬ�䤹��ޤǤˡ�ʣ������Ƥ����äƤ����Τ�1��Τߤ���';
$LANG_GF02['msg186']   = '����Ʒ�̾';
$LANG_GF02['msg187']   = '<a href="%s">��Ƥ����</a>';
$LANG_GF02['msg188']   = '����å�����Ⱥǿ���Ƥإ����פ��ޤ���';
$LANG_GF02['msg189']   = '���顼: �⤦������Ƥ��Խ��Ǥ��ޤ���';
$LANG_GF02['msg190']   = '���ä����Խ�';
$LANG_GF02['msg191']   = '�Խ��Ǥ��ޤ����Խ���ǽ���֤���λ����������ǥ졼�����¤�����ޤ���';
$LANG_GF02['msg192']   = '��λ���ޤ�����%s�ĤΥȥԥå��� %s�ĤΥ����Ȥ򥤥�ݡ��Ȥ��ޤ�����';
$LANG_GF02['msg193']   = '������Ǽ��Ĥ˥���ݡ��Ȥ���桼�ƥ���ƥ�';  
$LANG_GF02['msg194']   = '������ƤΤʤ��Ǽ���';
$LANG_GF02['msg195']   = '����å�����ȷǼ��Ĥإ����פ��ޤ�';
$LANG_GF02['msg196']   = '�Ǽ��Ĥ��ܼ��򸫤�';
$LANG_GF02['msg197']   = '���٤ƤΥ��ƥ���Υȥԥå�����ɤˤ���';
$LANG_GF02['msg198']   = '�Ǽ��Ĥ�����򹹿�����';
$LANG_GF02['msg199']   = '�Ǽ������Τ򸫤�/�������';
$LANG_GF02['msg200']   = '�����ȥ��С��Υ�ݡ��Ȥ򸫤�';
$LANG_GF02['msg201']   = '�͵��ȥԥå���ݡ��Ȥ򸫤�';


$LANG_GF02['StatusHeading']   = '����';
$LANG_GF02['PostReply']   = '�������񤭹���';
$LANG_GF02['PostTopic']   = '�������';
$LANG_GF02['EditTopic']   = '����Խ�';
$LANG_GF02['quietforum']  = '�Ǽ��Ĥ˿�����Ƥ�����ޤ���';

$LANG_GF03 = array (
    'welcomemsg'        => '�褦������ǥ졼������',
    'title'             => '��ǥ졼���ε�ǽ:&nbsp;',
    'delete'            => '���',
    'edit'              => '�Խ�',
    'move'              => '��ư',
    'ban'               => 'IP���ɥ쥹�ػ�',
    'stick'             => '���ܥȥԥå������ꤹ��',
    'unstick'           => '���ܥȥԥå�����ä�',
    'movetopic'         => '��ư&amp;���',
    'movetopicmsg'      => '<br> ���ηǼ��Ĥ� <b>%s</b> ���ư���ޤ�',
    'lockedpost'        => '�񤭹��ߤ��ɲ�',
    'split'             => '���ʬ��',
    'splittopicmsg'     => '<br>�������: "<b>%s</b>"<br><em>��Ƽ�:</em>&nbsp;%s&nbsp; <em>�������:</em>&nbsp;%s',
    'selectforum'       => '�����Ǽ�������:',
    'splitheading'      => '����åɥ��ץ����ʬ��:',
    'splitopt1'         => '�������餹�٤Ƥ���Ƥ��ư',
    'splitopt2'         => '1��ƤΤ߰�ư'
);

$LANG_GF04 = array (
    'label_forum'             => '�Ǽ��Ĥγ���',
    'label_location'          => '���',
    'label_aim'               => 'AIM�ϥ�ɥ�̾',
    'label_yim'               => 'YIM�ϥ�ɥ�̾',
    'label_icq'               => 'ICQ�ϥ�ɥ�̾',
    'label_msnm'              => 'MSN��å��󥸥㡼̾',
    'label_interests'         => '��̣',
    'label_occupation'        => '�Ż�',
);

/* Settings for Additional User profile - Instant Messenging links */
$LANG_GF05 = array (
    'aim_link'               => '&nbsp;<a href="aim:goim?screenname=',
    'aim_linkend'            => '>',
    'aim_hello'              => '&amp;message=Hi.+Are+you+there?',
    'aim_alttext'            => 'AIM:&nbsp;',
    'icq_link'               => '&nbsp;',
    'icq_alttext'            => 'ICQ #:&nbsp;',
    'msn_link'               => '&nbsp;<a href="javascript:MsgrApp.LaunchIMUI(',
    'msn_linkend'            => ')">',
    'msn_alttext'            => 'Messenger:&nbsp;',
    'yim_link'               => '&nbsp;<a href="ymsgr:sendIM?',
    'yim_linkend'            => '">',
    'yim_alttext'            => 'YIM:&nbsp;',
);


/* Admin Navbar */
$LANG_GF06 = array (
    1   => '����',
    2   => '����',
    3   => '�Ǽ��Ĵ���',
    4   => '��ǥ졼��',
    5   => '������Ǽ��Ĥ�',
    6   => '��å�����',
    7   => '�ػ�IP���ɥ쥹'
);

/* User Functions Navbar */
$LANG_GF07 = array (
    1   => '�Ǽ��Ĥ�ɽ��',
    2   => '�桼������',
    3   => '�񤭹��߿��͵���',
    4   => '�᡼����������ꥹ��',
    5   => '��Ƽԥꥹ��'
);



/* Forum User Features */
$LANG_GF08 = array (
    1   => '��Ƥ����',
    2   => '�Ǽ���Track �����',
    3   => '����㳰���'
);


$LANG_GF90 = array (
    'viewforums'        => '�ܼ�',
    'stats'             => '����',
    'settings'          => '����',
    'boardadmin'        => '�Ǽ���',
    'migrate'           => '����С���',
    'mods'              => 'Ĵ��',
    'messages'          => '��å�����',
    'ipman'             => '�ػ�IP���ɥ쥹'
);

$LANG_GF91 = array (
    'gfstats'            => '�Ǽ��Ĥ�����',
    'statsmsg'           => '����:',
    'totalcats'          => '��� ���ƥ��꡼��:',
    'totalforums'        => '��� �Ǽ��Ŀ�:',
    'totaltopics'        => '��� �ȥԥå���:',
    'totalposts'         => '��� ��ƿ�:',
    'totalviews'         => '��� ������:',
    'avgpmsg'            => 'ʿ����ƿ�:',
    'category'           => '���ƥ��꡼:',
    'forum'              => '�Ǽ���:',
    'topic'              => '�ȥԥå�:',
    'avgvmsg'            => 'ʿ�ѱ�����:'
);

// Settings.php 
$LANG_GF92 = array (
    'gfsettings'         => '����',
    'gensettings'        => '����',
    'gensettings'        => '����',
    'topicsettings'      => '�ȥԥå������',
    'blocksettings'      => '�����ɥ֥�å�(forum_newposts)',
    'ranksettings'       => '���������󥭥�',
    'htmlsettings'       => 'HTML����',
    'avsettings'         => '���Х�������',
    'ranksettings'       => '���',
    'savesettings'       => '    �ѹ�����¸����    ', 
    'allowhtml'          => 'HTML����',
    'allowhtmldscp'      => '��ƻ���HTML�λ��Ѥ���Ĥ��롣NO�����ꤹ��ȡ��ץ졼��ƥ����ȤǤ�����ƤǤ��ʤ�����bbcode�ϻ��ѤǤ��롣',//'Enable HTML to be used in posts. If set to NO then users will only be able to post in TEXT Mode but still use bbcode'
    'glfilter'           => 'HTML�ե��륿���',
    'glfilterdscp'       => 'Geeklog���Τ�HTML�ե��륿����Ѥ���',
    'censor'             => '����',
    'censordscp'         => '���ܤ����Geeklog���Τθ��ܵ�ǽ����Ѥ���)',
    'showmoods'          => '��ʬ��������',
    'showmoodsdscp'      => '���Ѥ���',
    'allowsmilies'       => '���ޥ��꡼��������',
    'allowsmiliesdscp'   => '���Ѥ���',
    'allownotify'        => '���ε���:',
    'allownotifydscp'    => '���Τ���Ĥ���',
    'showiframe'         => '�ȥԥå���ӥ塼ɽ��',
    'showiframedscp'     => '�ȥԥå��˿������񤭹����硤���˥ȥԥå������Ƥ�ɽ������',    'autorefresh'        => '��ư��ɽ��',
    'autorefresh'        => '��ư��ɽ��',
    'autorefreshdscp'    => '��Ƹ弫ưŪ�˺�ɽ��',
    'refreshdelay'       => '��ɽ���ޤǤ��ÿ�',
    'refreshdelaydscp'   => '��ư��ɽ������ꤷ����硤��ɽ���ޤǤ��ÿ�',
    'xtrausersettings'   => '�Ǽ��ĤΥ桼������',
    'xtrausersettingsdscp'    => '�桼���������Ĥ���',
    'titleleng'          => '��̾��Ĺ��',
    'titlelengdscp'      => '���ϤǤ����̾�κ���Х��ȿ�',
    'topicspp'           => '1�ڡ������ȤΥȥԥå���',
    'topicsppdscp'       => '�ƷǼ��Ĥǥȥԥå��ΰ�����ɽ���������1�ڡ�����ɽ������ȥԥå��ο�',
    'postspp'            => '1�ڡ������Ȥ���ƿ�',
    'postsppdscp'        => '�ƥȥԥå�����ƥ�å�������ɽ���������1�ڡ�������ɽ��������ƿ�',
    'regview'            => '��������',
    'regviewdscp'        => '��Ƥ򸫤뤿��ˤϥ�������Ȥ���Ͽ��ɬ��',
    'regpost'            => '��Ƶ���',
    'regpostdscp'        => '��Ƥ��뤿��ˤϥ�������Ȥ���Ͽ��ɬ��',
    'imgset'             => '�������å�',
    'lev1'               => '��٥� 1',
    'lev1dscp'           => '��� 1',
    'lev2'               => '��٥� 2',
    'lev2dscp'           => '��� 2',
    'lev3'               => '��٥� 3',
    'lev3dscp'           => '��� 3',
    'lev4'               => '��٥� 4',
    'lev4dscp'           => '��� 4',
    'lev5'               => '��٥� 5',
    'lev5dscp'           => '��� 5',
    'setsave'            => '�������¸����ޤ���',
    'setsavemsg'         => '�������¸����ޤ�����',
    'allownotify'        => '�᡼������',
    'allownotifydscp'    => '�᡼������Τ���',
    'defaultmode'        => '�������ƥ⡼��',
    'defaultmodedscp'    => 'HTML�⡼�ɤ����ˤ���ˤϡ�Yes�����ꤹ�롣<br>�ץ졼��ƥ����ȥ⡼�ɡʤ������ˤ����ˤ���ˤϡ�No�����ꤹ�롣',
    'cbsettings'         => '���󥿡����ꥢ',
    'cbenable'           => 'ɽ��',
    'cbenabledscp'       => '���󥿡����ꥢ��ɽ��',
    'cbhomepage'         => '�ȥåץڡ����Τ�',
    'cbhomepagedscp'     => '1�ڡ����ܤΤ�ɽ������',
    'cbposition'         => '����',
    'cbpositiondscp'     => 'ɽ������',
    'position'           => '���� ', 
    'all_topics'         => '���٤�',
    'no_topic'           => '�ۡ���ڡ����Τ�',
    'position_top'       => '�ڡ����Υȥå�',
    'position_feat'      => '�ý������Τ���',
    'position_bottom'    => '�ڡ����Υܥȥ�',
    'messagespp'         => '1�ڡ������ȤΥ�å�������',
    'messagesppdscp'     => '��å������������� - 1�ڡ������ȤΥ�å�������',
    'searchespp'         => '�������',
    'searchesppdscp'     => '������̤�1�ڡ������Ȥ�ɽ����',
    'minnamelength'      => '�����ȥ桼��̾��Ĺ��',
    'minnamedscp'        => '�����ȥ桼���κǾ���̾����Ĺ��',
    'mincommentlength'   => '�Ǿ���ʸĹ',
    'mincommentdscp'     => '�����ʸ�κǾ���Ĺ��',
    'minsubjectlength'   => '�Ǿ���̾Ĺ',
    'minsubjectdscp'     => '��̾�κǾ���Ĺ��',
    'popular'            => '����Ū�����',
    'populardscp'        => '�������',
    'convertbreak'       => '�����Ѵ�',
    'convertbreakdscp'   => '���Ԥ�HTML����(&lt;br&gt;)���Ѵ�����',
    'speedlimit'         => '��ƴֳ�����',
    'speedlimitdscp'     => '��ƴֳ֤����¤���',
    'cb_subjectsize'     => '��̾��Ĺ��',
    'cb_subjectsizedscp' => 'ɽ������ȥԥå��η�̾�ΥХ��ȿ�',
    'cb_numposts'        => '��ƿ�',
    'cb_numpostsdscp'    => '���󥿡����ꥢ��ɽ��������ƿ�',
    'sb_subjectsize'     => '��̾��Ĺ��',
    'sb_subjectsizedscp' => 'ɽ�������å������η�̾�ΥХ��ȿ�',
    'sb_numposts'        => '��ƿ�',
    'sb_numpostsdscp'    => '�����ɥ֥�å���ɽ��������ƿ�',
    'sb_latestposts'     => '�ǿ����',
    'sb_latestpostsdscp' => '1�ȥԥå��ˤĤ�1��å������Τ�ɽ������',
    'userdatefmt'        => '���դΥե����ޥå�',
    'userdatefmtdscp'    => '�桼�������ꤷ�����դΥե����ޥåȤ˽���',
    'spamxplugin'        => 'Spam-X�ץ饰����',
    'spamxplugindscp'    => '�������Spam-X�ץ饰����ǥ��ѥफȽ�ꤹ��',
    'pmplugin'           => 'PM�ץ饰����',
    'pmplugindscp'       => '�ץ饤�١��ȥ�å������ץ饰��������ӥ��󥹥ȡ��뤵��Ƥ��ơ������ͭ���ˤ���',
    'smiliesplugin'      => '���ޥ��꡼�ץ饰����',
    'smiliesplugindscp'  => '���ޥ��꡼�ץ饰����ޤ��ϳ����ؿ���Ȥ�',
    'geshiformat'        => '�����ɹ�ʸ��Ĵ',
    'geshiformatdscp'    => 'GeSHi�����ɹ�ʸ��Ĵ��ǽ��Ȥ�',
    'edit_timewindow'    => '������ե졼���Խ�',
    'edit_timewindowdscp' => '�Խ���ʬ���Ȥ��Խ���������֤�����'

);

// Board Admin
$LANG_GF93 = array (
    'gfboard'            => '�Ǽ��Ĵ���',
    'vieworder'          => '���֤򸫤�',
    'addcat'             => '���ƥ��꡼���ɲ�',
    'addforum'           => '�Ǽ��Ĥ��ɲ�',
    'order'              => '����:',
    'catorder'           => '���ƥ��꡼�ν���',
    'forumorder'         => '�Ǽ��Ĥν���',
    'catadded'           => '���ƥ��꡼���ɲä���ޤ�����',
    'catdeleted'         => '���ƥ��꡼���������ޤ�����',
    'catedited'          => '���ƥ��꡼����������ޤ�����',
    'forumadded'         => '�Ǽ��Ĥ��ɲä���ޤ�����',
    'forumaddError'      => '�Ǽ����ɲå��顼',
    'forumdeleted'       => '�Ǽ��Ĥ��������ޤ�����',
    'forumedited'        => '�Ǽ��Ĥ���������ޤ�����',
    'forumordered'       => '�Ǽ��Ĥν��֤��Խ�',
    'transfer'           => '��ư��..',
    'vieworder'          => 'View Order',
    'back'               => '���',
    'addnote'            => '���: �������ѿ����Խ��Ǥ��ޤ���',
    'editnote'           => '�Ǽ��Ĥξܺ٤��Խ�: ',
    'editforumnote'      => '�Խ�: <b>"%s"</b>',
    'deleteforumnote1'   => '<b>"%s"</b>&nbsp;������Ƥ������Ǥ��� ?',
    'editcatnote'        => '�Խ�: <b>"%s"</b>',
    'deletecatnote1'     => '<b>"%s"</b>&nbsp;������Ƥ������Ǥ��� ?',
    'deletecatnote2'     => '���Υ��ƥ��꡼�Τ��٤ƤηǼ��Ĥȥȥԥå���������ޤ���',
    'undercat'           => '���ƥ��꡼:',
    'deleteforumnote2'   => '����ľ������ƤϤ��٤ƺ������ޤ���',
    'groupaccess'        => '���롼��: ',
    'rebuild'            => '�ǿ�����ƥơ��֥����',
    'action'             => '���������',
    'forumdescription'   => '�Ǽ��Ĥ�����',
    'posts'              => '��ƿ�',
    'ordertitle'         => '����',
    'ModDel'             => '���',
    'ModEdit'            => '�Խ�',
    'ModMove'            => '��ư',
    'ModStick'           => '����',
    'ModBan'             => '�ػ�',
    'addmoderator'       => "��ǥ졼�����ɲ�",
    'delmoderator'       => "���򤷤���ǥ졼������\n",
    'moderatorwarning'   => '<b>���: �Ǽ��Ĥ��ߤĤ���ޤ���</b><br><br>��ǥ졼���ɲä����ˡ��Ǽ��ĥ��ƥ����������ƾ��ʤ��Ȥ�Ǽ��Ĥ�1�ĺ������Ƥ���������',
    'private'           => '�ץ饤�١��ȷǼ���',
    'filtertitle'       => '��ǥ졼������ɽ��',
    'addmessage'        => '��������ǥ졼�����ɲ�',
    'allowedfunctions'  => '���Ĥ���Ƥ��븢��',
    'userrecords'       => '�桼���쥳����',
    'grouprecords'      => '���롼�ץ쥳����',
    'filterview'        => '�ե��륿���ӥ塼',
    'readonly'           => '�����Ǽ���',
    'readonlydscp'       => '��ǥ졼����������ƤǤ���Ǽ���',
    'hidden'             => '�����줿�Ǽ���',
    'hiddendscp'         => '�Ǽ��Ĥ��ܼ��򱣤�',
    'hideposts'          => '������Ƥ򱣤�',
    'hidepostsdscp'      => '������ƥ֥�å���RSS�ե����ɤ�����Ƥ򱣤�'

);

$LANG_GF94 = array (
    'mod_title'          => '��ǥ졼��',
    'createmod'          => '��ǥ졼������',
    'deletemod'          => '��ǥ졼�����',
    'currentmods'        => '���Υ�ǥ졼��:',
    'moderates'          => '��ǥ졼��',
    'deletemsg'          => '(���: ���Υܥ���򥯥�å�����Ȥ����������ޤ���)',
    'username'           => '�桼��̾:',
    'forforum'           => '�Ǽ�����:',
    'modper'             => '������������:',
    'candelete'          => '�����ǽ:',
    'canban'             => '�ػ߲�ǽ:',
    'canedit'            => '�Խ���ǽ:',
    'canmove'            => '��ư��ǽ:',
    'canstick'           => 'Sticky����:',
    'addsuc'             => '��ǥ졼��������ɲä��ޤ�����',
    'editsuc'            => '��ǥ졼��������ѹ����ޤ�����',
    'removesuc'          => '��ǥ졼�����: ',
    'removesuc2'         => '��ǥ졼���򤹤٤ƤηǼ��Ĥ���������ޤ�����',
    'modexists'          => '��ǥ졼����¸�ߤ��ޤ���',
    'modexistsmsg'       => '���顼: ���Υ�ǥ졼���ϴ�����Ͽ�ѤߤǤ���',
    'transfer'           => '...',
    'removemodnote1'     => '���ηǼ��ĤΥ�ǥ졼�����Ǥ���ޤ����� %s �Ǽ��ġ�%s',
    'removemodnote2'     => '���ٲ�Ǥ����ȡ����ηǼ��Ĥ�����Ǥ��ޤ���',
    'removemodnote3'     => '���٤ƤηǼ��Ĥ��餳�Υ�ǥ졼�����Ǥ���ޤ����� %s',
    'removemodnote4'     => '���ٲ�Ǥ����ȡ��ɤηǼ��ĤΥ�ǥ졼���ˤ�ʤ�ޤ���',
    'allforums'          => '���٤ƤηǼ���'
);


$LANG_GF95 = array (
    'header1'           => '��Ƥ��줿�ȥԥå���������ޤ��礦��',
    'header2'           => '��Ƥ��줿�ȥԥå��ε���&nbsp;&raquo;&nbsp;%s',
    'notyet'            => '���ε�ǽ�Ϥޤ���������Ƥ��ޤ���',
    'delall'            => '���٤ƺ��',
    'delallmsg'         => '���٤ƤΥ�å������������ޤ�����: %s?',
    'underforum'        => '<b> %s (ID #%s)',
    'moderate'          => '��ǥ졼�Ȥ���',
    'nomess'            => '�ޤ���å���������Ƥ���Ƥ��ޤ���'
);

$LANG_GF96 = array (
    'gfipman'            => '�ػ�IP���ɥ쥹',
    'ban'                => '�ػ�',
    'noips'              => '<p style="margin: 0px; padding: 5px;">�ػߤ���Ƥ���IP���ɥ쥹�Ϥ���ޤ���!</p>',
    'unban'              => '�ػ߼��',
    'ipbanned'           => '�ػ�IP���ɥ쥹',
    'banip'              => '�ػ�IP���ɥ쥹����',
    'banipmsg'           => '�ػߤ������Ǥ�����IP %s?',
    'specip'             => '�ػ�IP ���ɥ쥹����ꤷ�Ƥ�������!',
    'ipunbanned'         => '�ػߤϲ������ޤ�����'
);

// IM.php
$LANG_GF97 = array (
    'msgsent'            => '��å������������ޤ���!',
    'msgsave'            => '���ʤ��� %s �ؤΥ�å������������ޤ�����',
    'msgreturn'          => '���ʤ��Υܥå�����',
    'msgerror'           => '��å������������ޤ���Ǥ�����<a href="javascript:history.back()">���</a>�򥯥�å����Ƥ��٤ƤΥե�����ɤ����Ƥ���������',
    'msgdelok'           => '�������ޤ�����',
    'msgdelsuccess'      => '��å��������������ޤ�����',
    'msgdelerr'          => '��å������Ϻ������Ƥ��ޤ���<a href=\"javascript:history.back()\">���</a>�򥯥�å����ƤҤȤ�����Ǥ���������',
    'msgpriv'            => '�ץ饤�١��ȥ�å�����',
    'msgprivnote1'       => ' %s �ץ饤�١��ȥ�å�����������ޤ�',
    'msgprivnote2'       => ' %s �ץ饤�١��ȥ�å�������ʣ������ޤ�',
    'msgto'              => '�桼��̾��:',
    'msgmembers'         => '���С��ꥹ�ȡ�'
);


$PLG_forum_MESSAGE1 = '�Ǽ��ĥץ饰����Υ��åץ��졼�ɤ��������ޤ���';
$PLG_forum_MESSAGE5 = '�Ǽ��ĥץ饰����Υ��åץ��졼�ɤ˼��Ԥ��ޤ��������顼��(error.log)��������������';

?>