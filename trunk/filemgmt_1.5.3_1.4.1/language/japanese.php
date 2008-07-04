<?php

###############################################################################
# lang.php
# This is the english language page for the Geeklog File Mgmt Page Plug-in!
#
# Copyright (C) 2002 Blaine Lang
# blaine@portalparts.com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################
# Tranlated by mystral_kk 2006/03/23
// �ǽ�������2006/04/18 for Ver1.5.2
// Language variables used by the Plug-in API
# ����ϡ�Geeklog�ե���������ץ饰���������ܸ����ե�����Ǥ���
# plugins/filemgmt/language/japanese.php
# �⤷���쥨�󥳡��ɤμ��ब��euc�Ǥʤ����ϡ�euc���Ѵ����Ƥ���������
# Last Update 2007/06/16 by Ivy (Geeklog Japanese)

$LANG_FM00 = array (
    'access_denied'     => '������������',
    'access_denied_msg' => 'Root�桼�����������Υڡ����ˤϥ��������Ǥ��ޤ��󡣤��ʤ���̾����IP�ϵ�Ͽ����ޤ�����',
    'admin'             => '�ץ饰���������',
    'install_header'    => '�ץ饰����Υ��󥹥ȡ���/���󥤥󥹥ȡ���',
    'installed'         => '���Υץ饰����ȥ֥�å��ϥ��󥹥ȡ��뤵��Ƥ��ޤ���<p><i>�ڤ���Ǥ���������<br><a href="MAILTO:blaine@portalparts.com">Blaine</a></i>',
    'uninstalled'       => '���Υץ饰����ϥ��󥹥ȡ��뤵��Ƥ��ޤ���',
    'install_success'   => '���󥹥ȡ�����������ޤ�����<p><b>���줫�餹�٤����ϡ�</b>: 
        <ol><li>�ե���������򥯥�å����ơ��ץ饰����������Ԥ���</ol>
        <p>�ܺپ���ϡ�<a href="%s">Install Notes</a>�򻲾ȡ�',
    'install_failed'    => '���󥹥ȡ���˼��Ԥ��ޤ��������顼���򸫤Ƥ���������',
    'uninstall_msg'     => '���Υץ饰����ϥ��󥤥󥹥ȡ��뤵��ޤ�����',
    'install'           => '���󥹥ȡ���',
    'uninstall'         => '���󥤥󥹥ȡ���',
    'editor'            => '�ץ饰���󥨥ǥ���',
    'warning'           => '���󥤥󥹥ȡ������ηٹ�',
    'enabled'           => '<p style="padding: 15px 0px 5px 25px;">���Υץ饰����ϥ��󥹥ȡ��뤵�졤ͭ���ˤʤäƤ��ޤ���<br>���󥤥󥹥ȡ��뤹�����ˡ����Υץ饰�����̵���ˤ��Ƥ���������</p><div style="padding:5px 0px 5px 25px;"><a href="'.$_CONF['site_admin_url'].'/plugins.php">Plugin Editor</a></div',
    'WhatsNewLabel'    => '����ե�����',
    'WhatsNewPeriod'   => '(%s ������)'
);

// Admin Navbar
$LANG_FM02 = array(
    'nav1'  => '����',
    'nav2'  => '���ƥ���',
    'nav3'  => '�ե�������ɲ�',
    'nav4'  => '��������� (%s)',
    'nav5'  => '��»�ե����� (%s)'
);

$LANG_FILEMGMT= array(
    'newpage' => "�����ڡ���",
    'adminhome' => "������HOME",
    'plugin_name' => "�ե��������",
    'searchlabel' => "�ե�����ꥹ��",
    'searchlabel_results' => "�ե�����ꥹ�ȷ��",
    'downloads' => "���������",
    'report' => "��������ɲ����¿�����",
    'usermenu1' => "���������",
    'usermenu2' => "&nbsp;&nbsp;���",
    'usermenu3' => "�ե����륢�åץ���",
    'admin_menu' => "�ե��������",
    'writtenby' => "���",
    'date' => "������",
    'title' => "�����ȥ�",
    'content' => "����",
    'hits' => "��������ɲ��",
    'Filelisting' => "�ե�����ꥹ��",
    'DownloadReport' => "�ե�������Υ������������",
    'StatsMsg1' => "��������ɲ���ξ��10��",
    'StatsMsg2' => "���Υ����Ȥˤϥե���������ץ饰�����Ѥ�����ե����뤬�ʤ���������ե�����˥������������ͤ����ʤ��褦�Ǥ���",
    'usealtheader' => "Alt. Header����Ѥ��Ƥ���������",
    'url' => "URL",
    'edit' => "�Խ�",
    'lastupdated' => "�ǿ��ե�����",
    'pageformat' => "�ڡ����ե����ޥå�",
    'leftrightblocks' => "�������֥�å�",
    'blankpage' => "����ڡ���",
    'noblocks' => "�֥�å��ʤ�",
    'leftblocks' => "���֥�å�",
    'addtomenu' => '��˥塼�ɲ�',
    'label' => '��٥�',
    'nofiles' => '�ե������ (���������)',
    'save' => '��¸',
    'preview' => '�ץ�ӥ塼',
    'delete' => '���',
    'cancel' => '����󥻥�',
    'access_denied' => '������������',
    'invalid_install' => '�ե���������ץ饰����Υ��󥹥ȡ���/���󥤥󥹥ȡ���ڡ����������˥����������褦�Ȥ����ͤ����ޤ����桼����ID: ',
    'start_install' => '�ե���������ץ饰����򥤥󥹥ȡ��뤷�褦�Ȥ��Ƥ��ޤ���',
    'start_dbcreate' => '�ե���������ץ饰�����Ѥ˥ơ��֥��������褦�Ȥ��Ƥ��ޤ���',
    'install_skip' => '... skipped as per filemgmt.cfg',
    'access_denied_msg' => '���ʤ��ϥե���������ץ饰�����admin�ڡ����������˥����������褦�Ȥ��Ƥ��ޤ��͡����Υڡ����ؤ������ʥ������������Ƶ�Ͽ����ޤ���',
    'installation_complete' => '���󥹥ȡ��봰λ',
    'installation_complete_msg' => 'Geeklog�ѥե���������ץ饰����Υǡ�����¤���ǡ����١����˥��󥹥ȡ��뤵��ޤ����������줳�Υץ饰����򥢥󥤥󥹥ȡ��뤹��ɬ�פ�����ʤ顤���Υץ饰������°��README���ɤߤ���������',
    'installation_failed' => '���󥹥ȡ��뼺��',
    'installation_failed_msg' => '�ե���������ץ饰����Υ��󥹥ȡ���˼��Ԥ��ޤ��������������ˤ�error.log�򤴤�󤯤�������',
    'system_locked' => '�����ƥ�ϥ�å�����Ƥ��ޤ�',
    'system_locked_msg' => '�ե���������ץ饰����ϥ��󥹥ȡ��뤵�졤��å�����Ƥ��ޤ������󥤥󥹥ȡ��뤹��ʤ顤��°��README���ɤߤ���������',
    'uninstall_complete' => '���󥤥󥹥ȡ��봰λ',
    'uninstall_complete_msg' => '�ե���������ץ饰���ѤΥǡ�����¤�ϥǡ����١�������������ޤ�������<br><br>�ե������֤���(repository)�ˤ���ե�����ϼ�ư�Ǻ������ɬ�פ�����ޤ���',
    'uninstall_failed' => '���󥤥󥹥ȡ���˼��Ԥ��ޤ�����',
    'uninstall_failed_msg' => '�ե���������ץ饰����Υ��󥤥󥹥ȡ���˼��Ԥ��ޤ��������������ˤ�error.log�򤴤�󤯤�������',
    'install_noop' => '�ץ饰���󥤥󥹥ȡ���',
    'install_noop_msg' => '�ե���������ץ饰����Υ��󥹥ȡ��뤬�¹Ԥ���ޤ����������٤����Ȥ�����ޤ���Ǥ�����<br><br>�ץ饰���������ե�������ǧ���Ƥ���������',
    'all_html_allowed' => 'HTML�����Ƶ��Ĥ���Ƥ��ޤ�',
    'no_new_files'  => '-',
    'no_comments'   => '-',
    'more'          => '<em>[��ʸɽ��]</em>'
);

$PLG_filemgmt_MESSAGE1 = '�ե���������ץ饰����Υ��󥹥ȡ�������Ǥ��ޤ�����<br>�ե�����: plugins/filemgmt/filemgmt.php ���񤭹��߲ĤˤʤäƤ��ޤ���';
$PLG_filemgmt_MESSAGE3 = '���Υץ饰����ˤ�Geeklog Version 1.4 �ʹߤ�ɬ�פǤ������åץ��졼�ɤ����Ǥ��ޤ�����';
$PLG_filemgmt_MESSAGE4 = '���Υץ饰����� version 1.5 �ѤΥ����ɤ򸡽ФǤ��ޤ��󡣥��åץ��졼�ɤ����Ǥ��ޤ�����';
$PLG_filemgmt_MESSAGE5 = '�ե���������ץ饰����Υ��åץ��졼�ɤ����Ǥ��ޤ�����<br>���ߤΥץ饰����ΥС������� 1.3 �ǤϤ���ޤ���';


// Language variables used by the plugin - general users access code.

define("_MD_THANKSFORINFO","�����󶡤��꤬�Ȥ��������ޤ��������Τ����ꥯ�����Ȥ�Ĵ�٤Ƥߤޤ���");
define("_MD_BACKTOTOP","��������ɥȥåפ����");
define("_MD_THANKSFORHELP","���Υǥ��쥯�ȥ�η������ΰݻ��ˤ����Ϥ������������꤬�Ȥ��������ޤ���");
define("_MD_FORSECURITY","�������ƥ������ͳ�Ǥ��ʤ��Υ桼����̾��IP���ɥ쥹����Ū�˵�Ͽ����ޤ���");

define("_MD_SEARCHFOR","�����о�");
define("_MD_MATCH","����");
define("_MD_ALL","����");
define("_MD_ANY","�ɤ줫1�ĤǤ�");
define("_MD_NAME","̾��");
define("_MD_DESCRIPTION","����");
define("_MD_SEARCH","����");

define("_MD_MAIN","�ᥤ��");
define("_MD_SUBMITFILE","�ե�������");
define("_MD_POPULAR","���");
define("_MD_NEW","New");
define("_MD_TOPRATED","���");

define("_MD_NEWTHISWEEK","�����ο����ե�����");
define("_MD_UPTHISWEEK","�����������줿�ե�����");

define("_MD_POPULARITYLTOM","��������ɲ�� (���ʤ���ν�)");
define("_MD_POPULARITYMTOL","��������ɲ�� (¿����)");
define("_MD_TITLEATOZ","�����ȥ�(A to Z)");
define("_MD_TITLEZTOA","�����ȥ�(Z to A)");
define("_MD_DATEOLD","����(�ս�)");
define("_MD_DATENEW","����(�����)");
define("_MD_RATINGLTOH","ɾ��(�㤤��)");
define("_MD_RATINGHTOL","ɾ��(�⤤��)");

define("_MD_NOSHOTS","����͡�������ʤ�");
define("_MD_EDITTHISDL","��������ɥե������Խ�");

define("_MD_LISTINGHEADING","<b>�ե�����ꥹ��:  %s �濫��ޤ���</b>");
define("_MD_LATESTLISTING","<b>�ǿ��ꥹ��:</b>");
define("_MD_DESCRIPTIONC","����:");
define("_MD_EMAILC","Email: ");
define("_MD_CATEGORYC","���ƥ���: ");
define("_MD_LASTUPDATEC","�ǿ����åץǡ���: ");
define("_MD_DLNOW","��������ɤ��Ƥ���������");
define("_MD_VERSION","�С������");
define("_MD_SUBMITDATE","����");
define("_MD_DLTIMES","��������� %s ��");
define("_MD_FILESIZE","�ե����륵����");
define("_MD_SUPPORTEDPLAT","���ݡ��Ȥ���Ƥ���ץ�åȥե�����");
define("_MD_HOMEPAGE","�ۡ���ڡ���");
define("_MD_HITSC","��������ɲ��: ");
define("_MD_RATINGC","ɾ��: ");
define("_MD_ONEVOTE","1 ��ɼ");
define("_MD_NUMVOTES","(%s)");
define("_MD_NOPOST","�ʤ�");
define("_MD_NUMPOSTS","��ɼ��: %s");
define("_MD_COMMENTSC","������: ");
define ("_MD_ENTERCOMMENT", "�����Ⱥ���");
define("_MD_RATETHISFILE","���Υե������ɾ��");
define("_MD_MODIFY","�Խ�");
define("_MD_REPORTBROKEN","��»�ե�����");
define("_MD_TELLAFRIEND","ͧ�ͤ˶�����");
define("_MD_VSCOMMENTS","�����Ȥ򸫤�/����");
define("_MD_EDIT","�Խ�");

define("_MD_THEREARE"," %s �ե����뤢��ޤ���");
define("_MD_LATESTLIST","�ǿ��ꥹ��");

define("_MD_REQUESTMOD","��������ɥե������Խ�");
define("_MD_FILE","�ե�����");
define("_MD_FILEID","�ե�����ID: ");
define("_MD_FILETITLE","�����ȥ�: ");
define("_MD_DLURL","���������URL: ");
define("_MD_HOMEPAGEC","�ۡ���ڡ���: ");
define("_MD_VERSIONC","�С������: ");
define("_MD_FILESIZEC","�ե����륵����: ");
define("_MD_NUMBYTES","%s �Х���");
define("_MD_PLATFORMC","�ץ�åȥե�����: ");
define("_MD_CONTACTEMAIL","Ϣ����E-mail: ");
define("_MD_SHOTIMAGE","����͡������: ");
define("_MD_SENDREQUEST","�����׵�");

define("_MD_VOTEAPPRE","��ɼ�˴��դ��ޤ���");
define("_MD_THANKYOU","%s ����ɼ���Ƥ������������꤬�Ȥ��������ޤ�����"); // %s is your site name
define("_MD_VOTEFROMYOU","���ʤ����Ȥ����Ϥ��Ƥ�������С�¾��ˬ��Ԥ���������ɤ��٤��ե���������Τ���Ω���ޤ���");
define("_MD_VOTEONCE","Ʊ���ե�����ˤ�1�󤷤���ɼ�Ǥ��ޤ���");
define("_MD_RATINGSCALE","ɾ������ 1 (�㤤)���� 10 (�⤤)�ޤǤǤ���");
define("_MD_BEOBJECTIVE","�Ҵ�Ū�ˤ��ꤤ���ޤ��������� 1 �� 10 ��ɾ�����������ʤ��ʤ顤ɾ���Ϥ��ޤ����Ω���ޤ���");
define("_MD_DONOTVOTE","��ʬ���Ȥ��󶡤����ե�����ˤ���ɼ�Ǥ��ޤ���");
define("_MD_RATEIT","ɾ�����Ƥ���������");

define("_MD_INTFILEAT","%s �Ǥ����ܥ��������"); // %s is your site name
define("_MD_INTFILEFOUND","%s �Ǹ��Ĥ������򤤤դ�����Ǥ���"); // %s is your site name

define("_MD_RECEIVED","��������ɾ���򤤤������ޤ��������꤬�Ȥ��������ޤ���");
define("_MD_WHENAPPROVED","��ǧ���줿��᡼�뤬�Ϥ��ޤ���");
define("_MD_SUBMITONCE","���٤����¹Ԥ��Ƥ���������");
define("_MD_APPROVED", "���ʤ��Υե�����Ͼ�ǧ����ޤ�����");
define("_MD_ALLPENDING","���٤ƤΥե���������̤���ھ��֤Ǥ���");
define("_MD_DONTABUSE","�桼����̾�� IP �ϵ�Ͽ����Ƥ��ޤ���");
define("_MD_TAKEDAYS","�ե�����/������ץȤ��ǡ����١�������Ͽ�����ޤǿ����������礬����ޤ���");

define("_MD_RANK","���");
define("_MD_CATEGORY","���ƥ���");
define("_MD_HITS","��������ɲ��");
define("_MD_RATING","ɾ��");
define("_MD_VOTE","��ɼ");

define("_MD_SEARCHRESULT4","������� <b>%s</b>:");
define("_MD_MATCHESFOUND","%s ����פ��ޤ�����");
define("_MD_SORTBY","�����ȴ��:");
define("_MD_TITLE","�����ȥ�");
define("_MD_DATE","����");
define("_MD_POPULARITY","�͵�");
define("_MD_CURSORTBY","ɽ�������Ƚ�: ");
define("_MD_FOUNDIN","���Ĥ���ޤ���:");
define("_MD_PREVIOUS","����");
define("_MD_NEXT","����");
define("_MD_NOMATCH","�������˰��פ����ΤϤ���ޤ���");

define("_MD_TOP10","%s �ξ��10"); // %s is a downloads category name
define("_MD_CATEGORIES","���ƥ���");

define("_MD_SUBMIT","�¹�");
define("_MD_CANCEL","����󥻥�");

define("_MD_BYTES","Bytes");
define("_MD_ALREADYREPORTED","��»�ե�����˴ؤ����ݡ��Ȥ���Ф��ޤ�����");
define("_MD_MUSTREGFIRST","���Υ���������¹Ԥ���ѡ��ߥå���󤬤���ޤ���<br>��Ͽ���뤫�����󤷤Ƥ���������");
define("_MD_NORATING","ɾ�����ʤ���Ƥ��ޤ���");
define("_MD_CANTVOTEOWN","��ʬ���Ȥ��󶡤����ե�����ˤ���ɼ�Ǥ��ޤ���<br>��ɼ�����Ƶ�Ͽ���졤��Ƥ����Ƥ��ޤ���");

// Language variables used by the plugin - Admin code.

define("_MD_RATEFILETITLE","�ե�����ɾ����Ͽ���Ƥ���������");
define("_MD_ADMINTITLE","�ե���������������ԥ�˥塼");
define("_MD_UPLOADTITLE","�ե�������� - �ե�����Υ��åץ���");
define("_MD_CATEGORYTITLE","�ꥹ�� - ���ƥ���");
define("_MD_DLCONF","�������������");
define("_MD_GENERALSET","�ե������������");
define("_MD_ADDMODFILENAME","�ե�����Υ��åץ���");
define ("_MD_ADDCATEGORYSNAP", "����: <small>(���ץ���󡤥ȥåץ�٥륫�ƥ���Τ�)</small>");
define ("_MD_ADDIMAGENOTE", "(�����ι⤵���� 50)");
define("_MD_ADDMODCATEGORY","<b>���ƥ���:</b> ���ƥ�����ɲ�/����/���");
define("_MD_DLSWAITING","��������ɵ����Ԥ�");
define("_MD_BROKENREPORTS","��»�ե������ݡ���");
define("_MD_MODREQUESTS","��������ɾ������׵�");
define("_MD_EMAILOPTION","��ǧ���Υ᡼��ȯ����: ");
define("_MD_COMMENTOPTION","�����ȵ���:");
define("_MD_SUBMITTER","�󶡼�: ");
define("_MD_DOWNLOAD","���������");
define("_MD_FILELINK","��ʸɽ��");
define("_MD_SUBMITTEDBY","�ե�������: ");
define("_MD_APPROVE","��ǧ");
define("_MD_DELETE","���");
define("_MD_NOSUBMITTED","�������󶡤��줿��������ɥե�����Ϥ���ޤ���");
define("_MD_ADDMAIN","�祫�ƥ����ɲ�");
define("_MD_TITLEC","�����ȥ�: ");
define("_MD_CATSEC", "���ƥ���ؤΥ�������: ");
define("_MD_IMGURL","<br>�����ե�����̾ <font size='-2'> (filemgmt_data/category_snaps�ˤ���ޤ� - �����ι⤵���� 50)</font>");
define("_MD_ADD","�ɲ�");
define("_MD_ADDSUB","���֥��ƥ����ɲ�");
define("_MD_IN","in");
define("_MD_ADDNEWFILE","�����ե����륢�åץ���");
define("_MD_MODCAT","���ƥ��꽤��");
define("_MD_MODDL","��������ɾ����ѹ�");
define("_MD_USER","�桼����");
define("_MD_IP","IP���ɥ쥹");
define("_MD_USERAVG","�桼����ɾ����ʿ��");
define("_MD_TOTALRATE","��ɾ��");
define("_MD_NOREGVOTES","��Ͽ�Ѥߥ桼�����ˤ����ɼ�ʤ�");
define("_MD_NOUNREGVOTES","̤��Ͽ�Ѥߥ桼�����ˤ����ɼ�ʤ�");
define("_MD_VOTEDELETED","��ɼ�ǡ����Ϻ������Ƥ��ޤ���");
define("_MD_NOBROKEN","��»�ե�����Ϥ���ޤ���");
define("_MD_IGNOREDESC","̵��(��ݡ��Ȥ�̵�뤷�ơ���ݡ��ȤΤ��ä����ι��ܤ�����������)");
define("_MD_DELETEDESC","���(<b>��ݡ��ȤΤ��ä���������ɤΥǡ���</b>��������)");
define("_MD_REPORTER","��ݡ�����м�");
define("_MD_FILESUBMITTER","�ե������󶡼�");
define("_MD_IGNORE","̵��");
define("_MD_FILEDELETED","�ե�����Ϻ������ޤ�����");
define("_MD_FILENOTDELETED","��Ͽ�Ϻ������ޤ��������ե�����Ϻ������ޤ���Ǥ�����<p>ʣ���ε�Ͽ��Ʊ���ե������ؤ��Ƥ��ޤ���</p>");
define("_MD_BROKENDELETED","��»�ե�����Υ�ݡ��ȤϺ������ޤ�����");
define("_MD_USERMODREQ","�桼�����ˤ���������ɾ������׵�");
define("_MD_ORIGINAL","���ꥸ�ʥ�");
define("_MD_PROPOSED","���");
define("_MD_OWNER","��ͭ��: ");
define("_MD_NOMODREQ","��������ɽ����׵�Ϥ���ޤ���");
define("_MD_DBUPDATED","�ǡ����١����Ϲ�������ޤ�����");
define("_MD_MODREQDELETED","�����׵�Ϻ������ޤ�����");
define("_MD_IMGURLMAIN","����(�����ι⤵���� 50): ");
define("_MD_PARENT","��̥��ƥ���:");
define("_MD_SAVE","�ѹ�����¸");
define("_MD_CATDELETED","���ƥ��꤬�������ޤ�����");
define("_MD_WARNING","�ٹ�: ���Υ��ƥ���ȥ��ƥ���������ե�����/�����Ȥ������ޤ�����");
define("_MD_YES","Yes");
define("_MD_NO","No");
define("_MD_NEWCATADDED","���ƥ��꤬�������ɲä���ޤ�����");
define("_MD_CONFIGUPDATED","���꤬��¸����ޤ�����");
define("_MD_ERROREXIST","���顼: ���ʤ����󶡤�����������ɾ���ϴ��˥ǡ����١�������Ͽ����Ƥ��ޤ���");
define("_MD_ERRORNOFILE","���顼: �ե����뤬�ǡ����١����ε�Ͽ�ˤ���ޤ���");  
define("_MD_ERRORTITLE","���顼: �����ȥ�����Ϥ��Ƥ���������");
define("_MD_ERRORDESC","���顼: ���������Ϥ��Ƥ���������");
define("_MD_NEWDLADDED","��������ɥե����뤬�����˥ǡ����١������ɲä���ޤ�����");
define("_MD_NEWDLADDED_DUPFILE","�ٹ�: �ե����뤬��ʣ���Ƥ��ޤ�����������ɥե����뤬�����˥ǡ����١������ɲä���ޤ�����");
define("_MD_NEWDLADDED_DUPSNAP","�ٹ�: ���ʥåפ���ʣ���Ƥ��ޤ�����������ɥե����뤬�����˥ǡ����١������ɲä���ޤ�����");
define("_MD_HELLO","����ˤ��ϡ�%s ����");
define("_MD_WEAPPROVED","�󶡤��Ƥ�����������������ɥե�����Ͼ�ǧ����ޤ������ե�����̾: ");
define("_MD_THANKSSUBMIT","���󶡤��꤬�Ȥ��������ޤ�����");
define("_MD_UPLOADAPPROVED","���ʤ������åץ��ɤ����ե�����Ͼ�ǧ����ޤ�����");
define("_MD_DLSPERPAGE","1 �ڡ����������ɽ�����: ");
define("_MD_HITSPOP","��������ɷ����¿����ɽ�����: ");
define("_MD_DLSNEW","�����ڡ����� 1 �ڡ����������ɽ�����: ");
define("_MD_DLSSEARCH","���������Υ�������ɥե������: ");
define("_MD_TRIMDESC","�ꥹ�ƥ���: ");
define("_MD_DLREPORT","�����������¥�������ɥ�ݡ���");
define("_MD_WHATSNEWDESC","������������ɽ��");
define("_MD_SELECTPRIV","������桼�����Υ�������ɤ����: ");
define("_MD_ACCESSPRIV","�����󤷤Ƥ��ʤ��桼�����Υ�������ɤ����: ");
define("_MD_UPLOADSELECT","������桼�����Υ��åץ��ɤ����: ");
define("_MD_UPLOADPUBLIC","�����󤷤Ƥ��ʤ��桼�����Υ��åץ��ɤ����: ");
define("_MD_USESHOTS","���ƥ������ɽ��: ");
define("_MD_IMGWIDTH","��������: ");
define("_MD_MUSTBEVALID","����͡�������� %s �ǥ��쥯�ȥ����ͭ���ʲ����ե�����Ǥʤ���Фʤ�ޤ���(�� shot.gif)�������ե����뤬�ʤ���ж���ˤ��Ƥ����Ƥ���������");
define("_MD_REGUSERVOTES","��Ͽ�Ѥߥ桼�����ˤ����ɼ(��ɼ���: %s)");
define("_MD_ANONUSERVOTES","̤��Ͽ�桼�����ˤ����ɼ(��ɼ���: %s)");
define("_MD_YOURFILEAT","���ʤ��� %s ���󶡤����ե�����"); // this is an approved mail subject. %s is your site name
define("_MD_VISITAT","%s �Υ������������򤴤�󤯤�������");
define("_MD_DLRATINGS","���������ɾ��(��ɼ���: %s)");
define("_MD_CONFUPDATED","���꤬��������ޤ�����");
define("_MD_NOFILES","�ե����뤬����ޤ���");   

// Additional Geeklog Defines
define("_MD_NOVOTE","�ޤ�ɾ������Ƥ��ޤ���");
define("_IFNOTRELOAD","�ڡ�������ưŪ�˥���ɤ���ʤ��ʤ顤<a href='%s'>����</a>�򥯥�å����Ƥ���������");
define("_GL_ERRORNOACCESS","���顼: �ɥ����������Υե������֤���˥��������Ǥ��ޤ���");
define("_GL_ERRORNOUPLOAD","���顼: �ե�����򥢥åץ��ɤ��븢�¤�����ޤ���");
define("_GL_ERRORNOADMIN","���顼: ���ε�ǽ�����¤���Ƥ��ޤ���");
define("_GL_NOUSERACCESS","�ɥ����������Υե������֤���˥��������Ǥ��ޤ���");
define("_MD_ERRUPLOAD","�ե��������: ���åץ��ɤǤ��ޤ���Ǥ������ե��������¸����ǥ��쥯�ȥ�Υѡ��ߥå������ǧ���Ƥ���������");
define("_MD_DLFILENAME","�ե�����̾: ");
define("_MD_REPLFILENAME","����ؤ��ѥե�����: ");
define("_MD_SCREENSHOT","�����򸫤�");
define("_MD_SCREENSHOT_NA","&nbsp;");
define("_MD_COMMENTSWANTED","�����ȴ���");
define("_MD_CLICK2SEE","����å����Ƥ�������: ");
define("_MD_CLICK2DL","����å����ơ���������ɤ��Ƥ�������: ");
define("_MD_ORDERBY","������: ");
define("_MD_ORDERBY","������: ");
define("_MD_ORDERBY","������: ");

?>