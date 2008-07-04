<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.0 for Geeklog - The Ultimate Weblog
// | Official release date: Feb 7,2003
// +---------------------------------------------------------------------------+
// | korean.php
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
// | Tranlated by IvySOHO Ivy(KOMMA Tetsuko/Kim Younghie)
# Last Update 2007/02/16 by Ivy (Geeklog Japanese)

$LANG_GF00 = array (
    'admin_only'        => '관리자 전용입니다.  만약 귀하께서 관리자라면 먼저 로그인 해 주시기 바랍니다.',
    'plugin'            => '플러그인',
    'pluginlabel'       => '게시판',
    'searchlabel'       => '게시판',
    'statslabel'        => '게시판 전체덧글',
    'statsheading1'     => '게시판 최다조회 톱 10',
    'statsheading2'     => '게시판 최다덧글달기 톱 10 ',
    'statsheading3'     => '덧글은 없습니다',
    'searchresults'     => '게시판 검색결과 %s',
    'useradminmenu'     => '게시판의 기능',
    'useradmintitle'    => '게시판의 사용자 설정',
    'access_denied'     => '접속에 실패 하였습니다',
    'access_denied_msg' => ' 고정(Root) 사용자만이 이 페이지에 접속할 수 있습니다.  귀하의 이름과 개인주소는 기록 되었습니다.',
    'admin'             => '플러그인 관리자',
    'install_header'    => '플러그인의 인스트롤/언인스트롤',
    'installed'         => '플러그인과 블로그가 인스트롤 되었습니다. <p><i> 를 즐겨보시기 바랍니다<br><a href="MAILTO:langmail@sympatico.ca">Blaine</a></i>',
    'uninstalled'       => '포럼 플러그인은 언인스트롤 되었습니다',
    'install_success'   => '인스트롤은 <p><b> 다음 단계로</b>: <ol><li> 게시판 관리자는 새게시판을 개설해 주시기 바랍니다. <li> 게시판의 설정과 개인의 설정을 재설정해 주시기 바랍니다. <li> 적어도 게시판을 하나, 카테고리를 하나 작성해 주시기 바랍니다. </ol><p> <a href="%s"> 인스트롤의 주의</a> 를 다시 한번 살펴보시기 바랍니다.',





    'install_failed'    => '인스트롤 실패입니다 ? 에러 로그(error. Log) 를 보시고 원인을 확인 하시기 바랍니다.',
    'uninstall_msg'     => '포럼 플로그인을 언인스트롤 하셨습니다',
    'install'           => '인스트롤',
    'uninstall'         => '언인스트롤',
    'enabled'           => '<br> 플러그인은 인스트롤 되어 유효로 되었습니다.<p>',
    'warning'           => '게시판 언인스트롤 경고',
    'uploaderr'         => '파일 업로드 에러'
);


$PLG_forum_MESSAGE1 = '게시판 플러그인 업그레이드: 성공하였습니다.';
$PLG_forum_MESSAGE2 = '게시판 플러그인 업그레이드:  자동인스트롤 실패. 플러그인 도큐멘트를 살펴보시기 바랍니다.';

$LANG_GF01['LOGIN']          = '로그인';
$LANG_GF01['FORUM']          = '게시판';
$LANG_GF01['ALL']            = '전부'; 
$LANG_GF01['YES']            = '예';
$LANG_GF01['NO']             = '아니오';
$LANG_GF01['NEW']            = '신착';
$LANG_GF01['PREV']           = '미리보기';
$LANG_GF01['NEXT']           = '다음으로';
$LANG_GF01['ERROR']          = '에러!';
$LANG_GF01['CONFIRM']        = '확인';
$LANG_GF01['UPDATE']         = '갱신';
$LANG_GF01['SAVE']           = '보존';
$LANG_GF01['CANCEL']         = '취소';
$LANG_GF01['CLOSE']          = '닫음';
$LANG_GF01['ON']             = '글쓴날: ';
$LANG_GF01['ON2']            = '&nbsp;&nbsp;<b>켜짐: </b>';
$LANG_GF01['IN']             = '들어옴: ';
$LANG_GF01['BY']             = '글쓴이: ';
$LANG_GF01['RE']             = '글달기: ';
$LANG_GF01['NA']             = 'N/A';
$LANG_GF01['DATE']           = '날짜';
$LANG_GF01['VIEWS']          = '조회수';
$LANG_GF01['REPLIES']        = '글달기 수';
$LANG_GF01['NAME']           = '이름:';
$LANG_GF01['DESCRIPTION']    = '설명: ';
$LANG_GF01['TOPIC']          = '토픽이름';
$LANG_GF01['TOPICS']         = '덧글:';
$LANG_GF01['TOPICSUBJECT']   = '토픽주제';
$LANG_GF01['FROM']           = '부터';
$LANG_GF01['REPLY']          = '새로 글달기';
$LANG_GF01['PM']             = 'PM';
$LANG_GF01['HOME']           = '게시판 표시';
$LANG_GF01['HOMEPAGE']       = '홈';
$LANG_GF01['SUBJECT']        = '주제';
$LANG_GF01['HELLO']          = '안녕하세요';
$LANG_GF01['MEMBERS']        = '회원';
$LANG_GF01['MOVED']          = '이동';
$LANG_GF01['REMOVE']         = '이동&amp;삭제';
$LANG_GF01['CURRENT']        = '최신';
$LANG_GF01['STARTEDBY']      = '최초의 글쓴이';
$LANG_GF01['POSTS']          = '덧글 수';
$LANG_GF01['LASTPOST']       = '최신덧글';
$LANG_GF01['POSTEDON']       = '덧글쓴 날';
$LANG_GF01['POSTEDBY']       = '글쓴이';
$LANG_GF01['PAGE']           = '페이지';
$LANG_GF01['PAGES']          = '페이지';
$LANG_GF01['ANONYMOUS']      = '손님:';
$LANG_GF01['TODAY']          = '오늘의';
$LANG_GF01['WELCOME']        = '어서오세요';
$LANG_GF01['REGISTER']       = '등록';
$LANG_GF01['REGISTERED']     = '등록일';
$LANG_GF01['MOSTPOPULAR']    = '최고 인기';
$LANG_GF01['ORDERBY']        = '동시교체;';
$LANG_GF01['ORDER']          = '차례:';
$LANG_GF01['USER']           = '사용자';
$LANG_GF01['GROUP']          = '그룹';
$LANG_GF01['ANON']           = '손님: ';
$LANG_GF01['ADMIN']          = '관리자';
$LANG_GF01['AUTHOR']         = '글쓴이';
$LANG_GF01['LOCATION']       = '장소';
$LANG_GF01['WEBSITE']        = '홈페이지';
$LANG_GF01['EMAIL']          = '메일';
$LANG_GF01['MOOD']           = '기분';
$LANG_GF01['NOMOOD']         = '-기분 아이콘';
$LANG_GF01['REQUIRED']       = '[요구]';
$LANG_GF01['OPTIONAL']       = '[옵션]';
$LANG_GF01['SUBMIT']         = '보존';
$LANG_GF01['PREVIEW']        = '미리보기';
$LANG_GF01['NOTIFY']         = '요주의:';
$LANG_GF01['REMOVE']         = '삭제';
$LANG_GF01['KEYWORDS']       = '키워드';
$LANG_GF01['EDIT']           = '편집';
$LANG_GF01['DELETE']         = '삭제';
$LANG_GF01['MESSAGE']        = '메세지:';
$LANG_GF01['OPTIONS']        = '옵션:';
$LANG_GF01['MISSINGSUBJECT'] = '주제없슴';
$LANG_GF01['MAY']            = '';
$LANG_GF01['IS']             = '는(은)';
$LANG_GF01['FOR']            = '위하여';
$LANG_GF01['ARE']            = '';
$LANG_GF01['NOT']            = '아닌';
$LANG_GF01['YOU']            = '';
$LANG_GF01['HTML']           = 'HTML';
$LANG_GF01['FULLHTML']       = '모든 HTML';
$LANG_GF01['WORDS']          = '단어';
$LANG_GF01['SMILIES']        = '스마일';
$LANG_GF01['MIGRATE_NOW']    = '인포트 실행'; 
$LANG_GF01['FILTERLIST']     = '필터목록';
$LANG_GF01['SELECTFORUM']    = '게시판을 선택';
$LANG_GF01['DELETEAFTER']    = '실행후 삭제';
$LANG_GF01['TITLE']          = '제목';
$LANG_GF01['COMMENTS']       = '제안 및 의견'; 
$LANG_GF01['SUBMISSIONS']    = '덧글 쓴 것';

$LANG_GF01['HTML_FILTER_MSG']  = '일부 HTML을 허가';
$LANG_GF01['HTML_FULL_MSG']  = '모든 HTML을 허가';
$LANG_GF01['HTML_MSG']       = 'HTML 허가';
$LANG_GF01['CENSOR_PERM_MSG']  = '';
$LANG_GF01['ANON_PERM_MSG']    = '손님의 덧글보기';
$LANG_GF01['POST_PERM_MSG1']    = '덧글쓰기 가능';
$LANG_GF01['POST_PERM_MSG2']    = '손님도 덧글가능';
$LANG_GF01['CENSORED']       = '검열';
$LANG_GF01['ALLOWED']        = '허가';
$LANG_GF01['GO']             = '실행';
$LANG_GF01['STATUS']         = '상태:';
$LANG_GF01['ONLINE']         = '온라인';
$LANG_GF01['OFFLINE']        = '오프라인';
$LANG_GF01['back2parent']    = '부모의 덧글';
$LANG_GF01['forumname']      = ''; 
$LANG_GF01['category']       = '카테고리: ';
$LANG_GF01['loginreqview']   = '<b> 게시판에 참가하기 위해서는  %s 등록</a> 혹은 %s 로그인 </a> 하시기 바랍니다</b>';
$LANG_GF01['loginreqpost']   = '<b> 덧글을 쓰기 위해서는 등록 혹은 로그인 해 주시기 바랍니다</b>';
$LANG_GF01['searchresults']  = '검색결과  <b>%s</b> %s : <b>%s</b> 결과:</b><br><br>';
$LANG_GF01['feature_not_on'] = '주목 거부';
$LANG_GF01['nolastpostmsg']  = 'N/A';
$LANG_GF01['no_one']         = '한가지는 아님.';
$LANG_GF01['popular']        = '인기순 목록';
$LANG_GF01['notify']         = '통지';
$LANG_GF01['PM']             = 'PM\'s';
$LANG_GF01['NEW_PM']         = 'New PM';
$LANG_GF01['DELALL_PM']      = '모두 삭제';
$LANG_GF01['DELOLDER_PM']    = '지난 글을 삭제';
$LANG_GF01['members']        = '회원목록';
$LANG_GF01['save_sucess']    = '보존성공';
$LANG_GF01['trademark']      = '<BR><CENTER><B>Geeklog Forum Project version 2.0</B> &copy; 2002</B></CENTER>';
$LANG_GF01['back2top']       = '첫머리로 돌아가기';
$LANG_GF01['POSTMODE']       = '덧글모드:';
$LANG_GF01['TEXTMODE']       = '택스트모드:';
$LANG_GF01['HTMLMODE']       = 'HTML 모드:';
$LANG_GF01['TopicPreview']   = '덧글 미리보기';
$LANG_GF01['moderator']      = '모더레이터';
$LANG_GF01['DATEADDED']      = '등록일';
$LANG_GF01['PREVTOPIC']      = '이전 토픽으로';
$LANG_GF01['NEXTTOPIC']      = '다음 토픽으로';
$LANG_GF01['CONTENT']        = '내용';
$LANG_GF01['QUOTE_begin']    = '[인용&nbsp;';
$LANG_GF01['QUOTE_by'   ]    = 'by:&nbsp;';
$LANG_GF01['RESYNC']         = "갱신";
$LANG_GF01['RESYNCCAT']      = "카테고리를 갱신";  
$LANG_GF01['PROFILE']        = "프로파일";
$LANG_GF01['DELETECONFIRM']  = "삭제해도 괜찮겠습니까";
$LANG_GF01['website']        = '홈페이지';
$LANG_GF01['EDITICON']       = '편집';
$LANG_GF01['QUOTEICON']      = '인용한 글';
$LANG_GF01['ProfileLink']    = '프로파일';
$LANG_GF01['WebsiteLink']    = '홈페이지';
$LANG_GF01['PMLink']         = 'PM';
$LANG_GF01['EmailLink']      = '메일';
$LANG_GF01['FORUMSUBSCRIBE'] = '이 게시판의 갱신을 메일로 통지하도록 설정하기';
$LANG_GF01['FORUMUNSUBSCRIBE'] = '이 게시판의 갱신을 메일로 통지하지 않도록 설정하기';
$LANG_GF01['NEWTOPIC']       = '신규덧글';
$LANG_GF01['POSTREPLY']      = '덧글쓰기';
$LANG_GF01['SubscribeLink']  = '메일통지 설정';
$LANG_GF01['unSubscribeLink'] = '메일통지 설정해제';
$LANG_GF01['FORUMSUBSCRIBE'] = '게시판 메일통지 설정';
$LANG_GF01['NEWTOPIC']       = '토픽 신규등록';
$LANG_GF01['POSTREPLY']      = '새 글달기';
$LANG_GF01['SUBSCRIPTIONS']  = '메일통지 설정목록';
$LANG_GF01['TOP']            = '토픽 첫머리';
$LANG_GF01['PRINTABLE']      = '인쇄용 페이지';
$LANG_GF01['ForumProfile']   = '게시판 옵션';
$LANG_GF01['USERPREFS']      = '게시판의 사용자설정';
$LANG_GF01['SPEEDLIMIT']     = '"귀하의 최신 덧글은 %s 초 전이었습니다. <br> 다음의 덧글쓰기 까지 최소한 %s 초 이상 기다려 주시기 바랍니다."';
$LANG_GF01['ACCESSERROR']    = '접속 에러';
$LANG_GF01['LEGEND']         = '예를 들면';
$LANG_GF01['ACTIONS']        = '동작';
$LANG_GF01['DELETEALL']      = '선택한 데이터를 전부 삭제';
$LANG_GF01['DELCONFIRM']     = '선택한 데이터를 전부 삭제해도 괜찮겠습니까?';
$LANG_GF01['DELALLCONFIRM']  = '데이터를 전부 삭제해도 괜찮겠습니까?';
$LANG_GF01['STARTEDBY']      = '초기덧글:';
$LANG_GF01['WARNING']        = '요주의';
$LANG_GF01['MODERATED']      = '모더레이터: %s';
$LANG_GF01['NOTIFYNOT']      = 'NOT!';
$LANG_GF01['LASTREPLYBY']    = '마지막 글쓴이:&nbsp;%s';
$LANG_GF01['UID']            = 'UID';
$LANG_GF01['ANON_POST_BEGIN'] = '손님의 덧글시작';
$LANG_GF01['ANON_POST_END']   = '손님의 조회완료';
$LANG_GF01['INDEXPAGE']      = '게시판 목차';
$LANG_GF01['FEATURE']        = '기능';
$LANG_GF01['SETTING']        = '설정';
$LANG_GF01['MARKALLREAD']    = '읽은 글 전부모음';

// Language for bbcode toolbar
$LANG_GF01['CODE']           = '코드';
$LANG_GF01['FONTCOLOR']      = '글자색';
$LANG_GF01['FONTSIZE']       = '글자크기';
$LANG_GF01['CLOSETAGS']      = '태그 닫기';
$LANG_GF01['CODETIP']        = '힌트: 선택한 글자순서에 대해 즉각 스타일을 적용할 수 있습니다';
$LANG_GF01['TINY']           = '작다';
$LANG_GF01['SMALL']          = '작은';
$LANG_GF01['NORMAL']         = '표준';
$LANG_GF01['LARGE']          = '큰';
$LANG_GF01['HUGE']           = '커다란';
$LANG_GF01['DEFAULT']        = '기정';
$LANG_GF01['DKRED']          = '진한 빨강';
$LANG_GF01['RED']            = '빨강';
$LANG_GF01['ORANGE']         = '오랜지';
$LANG_GF01['BROWN']          = '갈색';
$LANG_GF01['YELLOW']         = '노랑';
$LANG_GF01['GREEN']          = '초록';
$LANG_GF01['OLIVE']          = '올리브';
$LANG_GF01['CYAN']           = '물색';
$LANG_GF01['BLUE']           = '파랑';
$LANG_GF01['DKBLUE']         = '진한 파랑';
$LANG_GF01['INDIGO']         = '남색';
$LANG_GF01['VIOLET']         = '바이올렛';
$LANG_GF01['WHITE']          = '하양';
$LANG_GF01['BLACK']          = '까망';

$LANG_GF01['b_help']         = "굵은 글씨로 하기 : [b]text[/b]";
$LANG_GF01['i_help']         = "이태릭체로 하기: [i]text[/i]";
$LANG_GF01['u_help']         = "밑줄치기: [u]text[/u]";
$LANG_GF01['q_help']         = "네모로 둘러쌓기: [quote]text[/quote]";
$LANG_GF01['c_help']         = "코드를 표시하기: [code]code[/code]";
$LANG_GF01['l_help']         = "숫자없는 목록으로: [list]text[/list]";
$LANG_GF01['o_help']         = "숫자달린 목록으로: [olist]text[/olist]";
$LANG_GF01['p_help']         = "[img]http://동영상의_url[/img]  혹은 [img w=100 h=200][/img]";
$LANG_GF01['w_help']         = "URL 끼우기: [url]http://url[/url] 혹은 [url=http://url]URL 텍스트[/url]";
$LANG_GF01['a_help']         = "닫지 않은bbCode의 태그를 전부 닫기";
$LANG_GF01['s_help']         = "글자색: [color=red]text[/color]  힌트:  color=#FF0000의 형식에서도 지정할 수 있습니다"; 
$LANG_GF01['f_help']         = "문자크기: [size=x-small] 작은 글씨 [/size]";
$LANG_GF01['h_help']         = "더 자세한 것을 보기 위해서는 클릭하시기 바랍니다";


$LANG_GF02['msg01']    = 'Sorry you must register to use these forums';
$LANG_GF02['msg02']    = '등록';
$LANG_GF02['msg03']    = '';
$LANG_GF02['msg04']    = '';
$LANG_GF02['msg05']    = '<CENTER><I> 또는 등록되지 않았습니다.</CENTER></I>';
$LANG_GF02['msg06']    = ' 마지막 방문 이후의 덧글';
$LANG_GF02['msg07']    = '온라인 사용자:';
$LANG_GF02['msg08']    = '<br><B> 등록자 전체 등록일시:</B> %s';
$LANG_GF02['msg09']    = '<br><B> 전체 덧글일시:</B> %s <br>';
$LANG_GF02['msg10']    = '<B> 전체 덧글일시:</B> %s <br>';
$LANG_GF02['msg11']    = '게시판 목차로 돌아가기';
$LANG_GF02['msg12']    = '홈페이지 중앙으로 돌아가기';
$LANG_GF02['msg13']    = '등록이 필요합니다.';
$LANG_GF02['msg14']    = '등록이 필요합니다.<br>';
$LANG_GF02['msg15']    = '에러 라고 생각 하시면, <a href="mailto:%s?subject=Forum IP Ban"> 게시판 관리자 </A>까지.';
$LANG_GF02['msg16']    = '흔히 있는 덧글입니다. 다른 덧글이나 쓴 글들을 살펴보시기 바랍니다.';
$LANG_GF02['msg17']    = '메세지 편집이 완료 되었습니다.';
$LANG_GF02['msg18']    = '에러! 필수 기입항목에 입력 되지 않은 부분이 있거나 너무 짧습니다.';
$LANG_GF02['msg19']    = '메세지는 등록 되었습니다.';
$LANG_GF02['msg20']    = '글쓰기는 등록 되었습니다. Returning to Forum';
$LANG_GF02['msg21']    = '실행권한이 없습니다. <a href="javascript:history.back()"> 돌아 가시거나</a> 또는 <a href="%s/users.php?mode=login">로그인 하시기 바랍니다</a><br><br>'; 
$LANG_GF02['msg22']    = '- Forum Post Notification';
$LANG_GF02['msg23a']   = "게시판 [%s] 에 '%s' 씨의 글달기가 있었습니다. \n\n (덧글쓴이 %s 씨 게시판: %s) ";
$LANG_GF02['msg23b']   = "새 덧글 '%s'는  %s 씨에 의해서 %s 게시판에 덧글을 쓰셨습니다. (사이트 : %s) :\n%s/forum/viewtopic.php?showtopic=%s\n";
$LANG_GF02['msg23c']   =    "n%s/forum/viewtopic.php?showtopic=%s&lastpost=true 에서 열람할 수 있습니다. \n";
$LANG_GF02['msg24']    = '스랫을 보고 글달기 :';
$LANG_GF02['msg25']    = "\n";
$LANG_GF02['msg26']    = "\n* 이 덧글에 메일통지를 지정하고 있으므로 메일을 보내시기 바랍니다. ";
$LANG_GF02['msg27']    = "메일통지 지정을 해제하기 위해서는 <a href=  <%s>> 여기를 클릭 </a> 하시기 바랍니다.\n";
$LANG_GF02['msg28']    = '에러: 제목이 없습니다';
$LANG_GF02['msg29']    = '귀하의 서명은 여기에 표시 되었습니다.';
$LANG_GF02['msg30']    = '첫머리로 돌아가기';
$LANG_GF02['msg31']    = '<b> 편집할 수 있습니다:</b>';
$LANG_GF02['msg32']    = '<b> 메세지 편집</b>';
$LANG_GF02['msg33']    = '글쓴이: ';
$LANG_GF02['msg34']    = '메일:';
$LANG_GF02['msg35']    = '홈페이지:';
$LANG_GF02['msg36']    = '기분:';
$LANG_GF02['msg37']    = '메세지:';
$LANG_GF02['msg38']    = '신규 덧글달기를 메일로 알림';
$LANG_GF02['msg39']    = '<br> 신규덧글 훑어보기는 할 수 없습니다.';
$LANG_GF02['msg40']    = '<br> 이미 메일통지 설정이 되었습니다.<br><br>';
$LANG_GF02['msg41']    = '<br> %s에 대한 덧글은 메일통지 설정이 되었습니다 .<br><br>';
$LANG_GF02['msg42']    = '이 덧글 메일통지 설정을 해제 하였습니다.';
$LANG_GF02['msg43']    = '이 메일통지 설정을 해제해도 괜찮겠습니까?.';
$LANG_GF02['msg44']    = '<p style="margin:0px; padding:5px;">메일통지 설정을 한 덧글은 없습니다.</p>';
$LANG_GF02['msg45']    = '게시판 검색';
$LANG_GF02['msg46']    = '게시판 키워드 검색:';
$LANG_GF02['msg47']    = '글쓴이를 지정하는 것도 가능합니다:';
$LANG_GF02['msg48']    = '<br> 먼저 Chatterblock 플러그인을 인스트롤 하시기 바랍니다.';
$LANG_GF02['msg49']    = '(참조수 %s 회) ';
$LANG_GF02['msg50']    = '서명 n/a';
$LANG_GF02['msg51']    = "%s\n\n<br>[편집 %s by %s]";
$LANG_GF02['msg52']    = '확정:';
$LANG_GF02['msg53']    = '토픽으로 돌아가기.';
$LANG_GF02['msg54']    = '덧글은 편집 되었습니다.';
$LANG_GF02['msg55']    = '삭제 되었습니다.';
$LANG_GF02['msg56']    = 'IP 주소는 금지 되었습니다.';
$LANG_GF02['msg57']    = '주목 토픽설정.';
$LANG_GF02['msg58']    = '주목 토픽설정 해제.';
$LANG_GF02['msg59']    = '통상';
$LANG_GF02['msg60']    = '신착';
$LANG_GF02['msg61']    = '주목토픽';
$LANG_GF02['msg62']    = '글달기 있으면 메일로 알리기';
$LANG_GF02['msg62']    = '글달기 있으면 메일로 알리기';
$LANG_GF02['msg63']    = '프로파일';
$LANG_GF02['msg64']    = '토픽 %s  제목: %s 을 정말 삭제해도 괜찮겠습니까?';
$LANG_GF02['msg65']    = '<br> 이것은 부모덧글입니다.  그러므로 이 토픽 의 모든 글달기도 함께 삭제 됩니다.';
$LANG_GF02['msg66']    = '덧글삭제 확정';
$LANG_GF02['msg67']    = '포럼 덧글편집';
$LANG_GF02['msg68']    = '주의: 금지는 주의깊게 행하시기 바랍니다. 관리자만이 금지자를 해제 할 수 있습니다.';
$LANG_GF02['msg69']    = '<br>정말 이 IP 주소를 금지 하시겠습니까: %s씨?';
$LANG_GF02['msg70']    = '금지확정';
$LANG_GF02['msg71']    = '기능이 선택되지 않습니다. 덧글을 선택하여 모더레이터의 기능을 실행 하시기 바랍니다.<br>주의: 직접 모더레이터가 되셔서 이 기능을 사용하시기 바랍니다.';
$LANG_GF02['msg72']    = '이 메세지는 온라인에서는 관리자 기능이 성공하지 않습니다.';
$LANG_GF02['msg74']    = '최신 덧글 %s 건';
$LANG_GF02['msg75']    = '조회수 톱 %s 건';
$LANG_GF02['msg76']    = '덧글수 Top %s 건';
$LANG_GF02['msg77']    = '<br><p style="padding-left:10px;"> 죄송합니다만, 먼저 로그인 하시기 바랍니다.  어카운트가 없다면 신규등록을 하시기 바랍니다.<p />';
$LANG_GF02['msg78']    = '<br>여기에 게시판은 없습니다.';
$LANG_GF02['msg81']    = '- 덧글편집 알림';
$LANG_GF02['msg82']    = '<p> 귀하의 메세지  "%s"는 모더레이터 %s 씨에 의해 편집 되었습니다.<p>';
$LANG_GF02['msg83']    = '<br><br>게시판 제목을 입력하시기 바랍니다.<p />';
$LANG_GF02['msg84']    = '전부 읽은 것으로 하기';
$LANG_GF02['msg85']    = '페이지:';
$LANG_GF02['msg86']    = '최신 덧글 10  글쓴이;';
$LANG_GF02['msg87']    = '<br>경고: 이 토픽은 잠겨져 있습니다.<br> 추가 덧글은 할 수 없습니다.';
$LANG_GF02['msg88']    = '게시판의 덧글쓴이 목록';
$LANG_GF02['msg88b']   = '게시판 발언자 전용';
$LANG_GF02['msg89']    = '메일통지 설정목록';
$LANG_GF02['msg100']   = '정보:';
$LANG_GF02['msg101']   = '규칙:';
$LANG_GF02['msg102']   = '덧글 테마:';
$LANG_GF02['msg103']   = '게시판 점프:';
$LANG_GF02['msg104']   = '덧글 메세지';
$LANG_GF02['msg105']   = '귀하의 덧글을 편집';
$LANG_GF02['msg106']   = '게시판 선택';
$LANG_GF02['msg107']   = '게시판 테마:';
$LANG_GF02['msg108']   = '신규덧글 있는 게시판';
$LANG_GF02['msg109']   = '잠겨진 토픽';
$LANG_GF02['msg110']   = '편집페이지로 이동 중.';
$LANG_GF02['msg111']   = '안읽은 목록';
$LANG_GF02['msg112']   = '안읽음을 표시';
$LANG_GF02['msg113']   = '안읽음을 표시';
$LANG_GF02['msg114']   = '잠겨진 토픽';
$LANG_GF02['msg115']   = '주목토픽 신착';
$LANG_GF02['msg116']   = '잠겨진 토픽 신착';
$LANG_GF02['msg117']   = '사이트 검색';
$LANG_GF02['msg118']   = '게시판 검색';
$LANG_GF02['msg119']   = '검색결과:';
$LANG_GF02['msg120']   = '인기순 by';
$LANG_GF02['msg121']   = '전부의 시각은 %s. 현재 %s.';
$LANG_GF02['msg122']   = '인기순 목록수:';
$LANG_GF02['msg123']   = '인기순 목록에 표시된 건수';
$LANG_GF02['msg124']   = '각 페이지의 메세지:';
$LANG_GF02['msg125']   = '모더레이터 전용: 메세지 전체보기 화면';
$LANG_GF02['msg126']   = '검색라인:';
$LANG_GF02['msg127']   = '검색결과로 표시되는 라인 수';
$LANG_GF02['msg128']   = '각 페이지 별 글쓴이 수:';
$LANG_GF02['msg129']   = '글쓴이 목록의 각 페이지에 표시된 사람 수';
$LANG_GF02['msg130']   = '손님 덧글표시:';
$LANG_GF02['msg131']   = '손님 덧글 선별하기';
$LANG_GF02['msg132']   = '메일통지 설정모드:';
$LANG_GF02['msg133']   = '글달기가 있으면 메일통지를 기정치로 하기';
$LANG_GF02['msg134']   = '덧글이 추가 되었습니다';
$LANG_GF02['msg135']   = '귀하의 덧글 전부가 게시판에 통지 됩니다.';
$LANG_GF02['msg136']   = '덧글을 쓰고자 하는 게시판을 선택해 주시기 바랍니다.';
$LANG_GF02['msg137']   = '글달기가 있으면 메일로 통지 됩니다';
$LANG_GF02['msg138']   = '<b>게시판 전체</b>';
$LANG_GF02['msg139']   = '%s 계속할 경우는 <a href="%s">클릭</a>';
$LANG_GF02['msg140']   = '계속하기';
$LANG_GF02['msg141']   = '이 페이지는 자동으로 돌아갑니다. 돌아가지 않을 경우<a href="%s">여기를</a>';
$LANG_GF02['msg142']   = '메일통지 설정모드로 변경 하였습니다.';
$LANG_GF02['msg143']   = '통지';
$LANG_GF02['msg144']   = '토픽으로';
$LANG_GF02['msg145']   = '스랫 참조';
$LANG_GF02['msg146']   = '메일통지 설정모드는 해제 되었습니다';
$LANG_GF02['msg147']   = '게시판 인쇄';
$LANG_GF02['msg148']   = '<a href="javascript:history.back()"> 돌아가기</a>';
$LANG_GF02['msg149']   = '%s씨 인스탄트 메시지를 보내시기 바랍니다.';
$LANG_GF02['msg150']   = '귀하의 덧글 %s 중에서';
$LANG_GF02['msg151']   = '최신 덧글';
$LANG_GF02['msg152']   = '인기 덧글';
$LANG_GF02['msg153']   = '인기 글달기 토픽';
$LANG_GF02['msg154']   = '최신 덧글';
$LANG_GF02['msg155']   = '덧글 없슴';
$LANG_GF02['msg156']   = '덧글 수';
$LANG_GF02['msg157']   = '최신 덧글 10 ';
$LANG_GF02['msg158']   = '최신 글쓴이 10 ';
$LANG_GF02['msg159']   = '모더레이터의 데이터를 정말 삭제해도 괜찮겠습니까?';
$LANG_GF02['msg160']   = '덧글의 마지막 페이지 보기';
$LANG_GF02['msg161']   = '회원 목록으로 돌아가기';
$LANG_GF02['msg162']   = '<a href="%s">여기로</a><br><p /> 자동적으로 돌아가지만, 지금 당장 돌아가고 싶을 때에는<a href="%s">여기를e</a>';
$LANG_GF02['msg163']   = '덧글은 이동 되었습니다';
$LANG_GF02['msg164']   = '전부 읽은것으로 하기';
$LANG_GF02['msg165']   = '에러:<p />서로 어울리는 <b>인용</b> 태그는 없습니다. 포멧 할 수 없습니다.<p />';
$LANG_GF02['msg166']   = '에러: 기사가 깨졌거나 찾을 수 없습니다';
$LANG_GF02['msg167']   = '통지 옵션';
$LANG_GF02['msg168']   = '메일통지를 무효로 하기';
$LANG_GF02['msg169']   = '회원 목록으로 돌아가기';
$LANG_GF02['msg170']   = '최신 덧글';
$LANG_GF02['msg171']   = '게시판 접속에러';
$LANG_GF02['msg172']   = '덧글이 없거나 삭제 되었습니다';
$LANG_GF02['msg173']   = '메세지 투고 페이지로 이동하기.';
$LANG_GF02['msg174']   = '금지회원(BAN Member)을 찾을 수 없습니다. ‘IP 주소가 부적절합니다:Address';
$LANG_GF02['msg175']   = '게시판 전체보기로 돌아가기';
$LANG_GF02['msg176']   = '회원 선택';
$LANG_GF02['msg177']   = '회원 전체';
$LANG_GF02['msg178']   = '부모의 덧글전용';
$LANG_GF02['msg179']   = '내용생성: %s 초';
$LANG_GF02['msg180']   = '게시판 덧글경고';
$LANG_GF02['msg181']   = '게시판 관리자로서 접속할 수 없습니다';
$LANG_GF02['msg182']   = '모더레이터 확인';
$LANG_GF02['msg183']   = '신규덧글: %s';
$LANG_GF02['msg184']   = '한번만 알림';
$LANG_GF02['msg185']   = '다음 방문까지, 덧글이 다수 있어도 통지는 한번만 합니다.';
$LANG_GF02['msg186']   = '새 덧글 제목';
$LANG_GF02['msg187']   = '<a href="%s">덧글로 돌아가기</a>';
$LANG_GF02['msg188']   = '클릭하면 최신 덧글로 점프';
$LANG_GF02['msg189']   = '에러: 이제 이 덧글은 편집할 수 없습니다';
$LANG_GF02['msg190']   = '조용히 편집';
$LANG_GF02['msg191']   = '편집 불가능. 편집가능한 기간이 지났거나 모더레이터 권한이 없습니다.';
$LANG_GF02['msg192']   = '완료 되었습니다...  %s개의 토픽과  %s개의 코멘트를 인포트 하였습니다.';
$LANG_GF02['msg193']   = '기사를 게시판에 인포트 가능한 유용성 STORY&nbsp;&nbsp;TO&nbsp;&nbsp;FORUM&nbsp;&nbsp;MIGRATION&nbsp;&nbsp;';
$LANG_GF02['msg194']   = '신규덧글이 없는 게시판';
$LANG_GF02['msg195']   = '클릭하면 게시판으로 점프';
$LANG_GF02['msg196']   = '게시판 목차보기';
$LANG_GF02['msg197']   = '모든 카테고리 토픽을 읽은것으로 하기';
$LANG_GF02['msg198']   = '게시판 설정을 갱신하기';
$LANG_GF02['msg199']   = '게시판통지 읽기/삭제하기 ';
$LANG_GF02['msg200']   = '게시판 사이트 회원의 레포트 읽기';
$LANG_GF02['msg201']   = '인기토픽 레포트 읽기';

$LANG_GF02['StatusHeading']   = '정보';
$LANG_GF02['PostReply']   = '새로 글달기';
$LANG_GF02['PostTopic']   = '신규덧글';
$LANG_GF02['EditTopic']   = '덧글편집';
$LANG_GF02['quietforum']  = '게시판에 신규덧글은 없습니다';

$LANG_GF03 = array (
    'welcomemsg'        => '어서오세요, 모더레이터씨',
    'title'             => '모더레이터 기능:&nbsp;',
    'delete'            => '삭제',
    'edit'              => '편집',
    'move'              => '이동',
    'split'             => '투고분할',
    'ban'               => 'IP 주소금지',
    'stick'             => '주목 토픽에 설정하기',
    'unstick'           => '주목 토픽을 지우기',
    'movetopic'         => '이동&map;삭제',
    'movetopicmsg'      => '<br> 다음 게시판으로"<b>%s</b>를 이동하기"',
    'splittopicmsg'     => '<br> 신규덧글 :"<b>%s</b>"<br><em> 글쓴이 :</em>&nbsp;%s&nbsp <em>원래 덧글:</em>&nbsp;%s',
    'selectforum'       => '신규게시판 선택:',
    'lockedpost'        => '글달기 추가',
    'splitheading'      => '스랫옵션 분할:',
    'splitopt1'         => '여기부터 덧글 전부를 이동',
    'splitopt2'         => '덧글 하나만 이동'
);

$LANG_GF04 = array (
    'label_forum'             => '게시판 개요',
    'label_location'          => '장소',
    'label_aim'               => 'AIM 핸들 이름',
    'label_yim'               => 'YIM 핸들 이름',
    'label_icq'               => 'ICQ 핸들 이름',
    'label_msnm'              => 'MSN 메센저 이름',
    'label_interests'         => '취미',
    'label_occupation'        => '직업',
);

/* Settings for Additional User profile - Instant Messenging links */
$LANG_GF05 = array (
    'aim_link'               => '&nbsp;<a href="aim:goim?screenname=',
    'aim_linkend'            => '>',
    'aim_hello'              => '&message=Hi.+Are+you+there?',
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
    1   => '통계',
    2   => '설정',
    3   => '게시판 관리',
    4   => '모더레이터',
    5   => '기사를 게시판으로',
    6   => '메세지',
    7   => '금지IP 주소'
);


/* User Functions Navbar */
$LANG_GF07 = array (
    1   => '게시판 표시',
    2   => '사용자 설정',
    3   => '덧글달기 인기순위',
    4   => '메일통지 설정목록',
    5   => '글쓴이 목록'
);


/* Forum User Features */
$LANG_GF08 = array (
    1   => '덧글 쓸 때의 주의 ',
    2   => '게시판 트랙의 주의',
    3   => '덧글 예외 시의 주의'
);


$LANG_GF90 = array (
    'viewforums'        => '목차',
    'stats'             => '통계',
    'settings'          => '설정',
    'boardadmin'        => '게시판',
    'migrate'           => 'Convert',
    'mods'              => '조정',
    'messages'          => '메세지',
    'ipman'             => '금지IP 주소'
);

$LANG_GF91 = array (
    'gfstats'            => '게시판 통계',
    'statsmsg'           => '현재:',
    'totalcats'          => '카테고리 총계:',
    'totalforums'        => '게시판 총수:',
    'totaltopics'        => '토픽 총수:',
    'totalposts'         => '덧글 총수:',
    'totalviews'         => '조회 총수:',
    'avgpmsg'            => '평균 덧글 수:',
    'category'           => '카테고리:',
    'forum'              => '게시판:',
    'topic'              => '토픽:',
    'avgvmsg'            => '평균 조회수:'
);

// Settings.php 
$LANG_GF92 = array (
    'gfsettings'         => '설정',
    'gensettings'        => '일반',
    'gensettings'        => '일반',
    'topicsettings'      => '토픽에 대한 투고',
    'blocksettings'      => '인접블러그(forum_newposts)',
    'ranksettings'       => '설명설정 랭킹',
    'htmlsettings'       => 'HTML 설정',
    'avsettings'         => '아바타 설정',
    'ranksettings'       => '랭킹',
    'savesettings'       => '    변경을 보존하기    ',
    'allowhtml'          => 'HTML 사용',
    'allowhtmldscp'      => '덧글쓰기를 할 때 HTML 사용을 허가합니다. 만약 NO 를 설정하면 프렌택스트에서만 투고 할 수 없습니다.',
    'glfilter'           => 'HTML 필터링',
    'glfilterdscp'       => 'Geeklog 본체의 HTML 필터를 사용합니다',
    'censor'             => '검열',
    'censordscp'         => '검열하기 (Geeklog 본체의 검열기능을 사용합니다)',
    'showmoods'          => '기분아이콘',
    'showmoodsdscp'      => '사용하기',
    'allowsmilies'       => '스마일아이콘',
    'allowsmiliesdscp'   => '사용하기',
    'allownotify'        => '통지허가',
    'allownotifydscp'    => '통지를 허가하기',
    'showiframe'         => '토픽 미리보기',
    'showiframedscp'     => '토픽에 새 글달기를 할 경우 다음의 토픽 내용을 표시하기',
    'autorefresh'        => '자동 재표시',
    'autorefreshdscp'    => '글달기 후 자동적으로 재표시하기 (No일 경우에는 「여기」를 클릭해서 재표시)',
    'refreshdelay'       => '재표시 까지의 초 수',
    'refreshdelaydscp'   => '자동의 재표시를 지정한 경우, 재표시 까지의 초 수',
    'xtrausersettings'   => '게시판의 사용자 설정',
    'xtrausersettingsdscp'    => '사용자 설정을 허가하기',
    'titleleng'          => '제목의 길이',
    'titlelengdscp'      => '입력 가능한 제목의 최대 바이트 수',
    'topicspp'           => '각 페이지 별 토픽 수',
    'topicsppdscp'       => '각 게시판 별 토픽 전체보기를 표시 할 경우, 페이지 마다 표시되는 토픽 수',
    'postspp'            => '각 페이지 별 덧글 수',
    'postsppdscp'        => '각 토픽마다 덧글메세지를 표시 할 경우 페이지당 표시되는 덧글 수',
    'regview'            => '조회허가',
    'regviewdscp'        => '덧글을 보기 위해서는 어카운트의 등록이 필요',
    'regpost'            => '덧글허가',
    'regpostdscp'        => '덧글을 쓰기 위해서는 어카운트의 등록이 필요',
    'imgset'             => '동영상 세트',
    'lev1'               => '레벨 1',
    'lev1dscp'           => '순위 1',
    'lev2'               => '레벨 2',
    'lev2dscp'           => '순위 2',
    'lev3'               => '레벨 3',
    'lev3dscp'           => '순위 3',    
    'lev4'               => '레벨 4',
    'lev4dscp'           => '순위 4',    
    'lev5'               => '레벨 5',
    'lev5dscp'           => '순위 5',
    'setsave'            => '설정은 보존 되었습니다',
    'setsavemsg'         => '설정은 보존 되었습니다.',
    'allownotify'        => '메일통지',
    'allownotifydscp'    => '메일로 통지하기',
    'defaultmode'        => '기정의 덧글모드',
    'defaultmodedscp'    => 'HTML 모드를 기정으로 할 경우에는 Yes로 설정합니다. <br>플렌 텍스트 모드(보다 안전)를 기정으로 할 경우에는 No로 설정합니다.',
    'cbsettings'         => '중심영역',
    'cbenable'           => '표시',
    'cbenabledscp'       => '중심영역에 표시',
    'cbhomepage'         => '톱페이지에만',
    'cbhomepagedscp'     => '첫 페이지에만 표시하기',
    'cbposition'         => '위치',
    'cbpositiondscp'     => '표시위치',
    'position'           => '위치',
    'all_topics'         => '전부',
    'no_topic'           => '홈페이지일 뿐',
    'position_top'       => '페이지 머리부분',
    'position_feat'      => '특집기사 끝나고',
    'position_bottom'    => '페이지 단추',
    'messagespp'         => '각 페이지 별 메세지 수',
    'messagesppdscp'     => '메세지 관리화면 ? 각 페이지 별 표시수',
    'searchespp'         => '검색결과',
    'searchesppdscp'     => '검색결과에 대한 각 페이지 별 표시 수',
    'minnamelength'      => '손님 사용자의 이름길이',
    'minnamedscp'        => '손님 사용자의 최소한의 이름길이',
    'mincommentlength'   => '최소의 덧글본문 길이',
    'mincommentdscp'     => '덧글본문의 최소한의 길이',
    'minsubjectlength'   => '최소 제목의 길이',
    'minsubjectdscp'     => '제목의 최소한의 길이',
    'popular'            => '일반적인 덧글',
    'populardscp'        => '조회횟수',
    'convertbreak'       => '행바꾸기 변환',
    'convertbreakdscp'   => '행바꾸기를 HTML 태그(&lt;BR&gt;)로 변환하기',
    'speedlimit'         => '덧글 간격제한',
    'speedlimitdscp'     => '덧글간격을 제한하기',
    'cb_subjectsize'     => '제목의 길이',
    'cb_subjectsizedscp' => '표시한 토픽제목의 바이트 수',
    'cb_numposts'        => '덧글 수',
    'cb_numpostsdscp'    => '중심영역에 표시한 덧글 수',
    'sb_subjectsize'     => '제목의 길이',
    'sb_subjectsizedscp' => '표시한 메세지 제목의 바이트 수',
    'sb_numposts'        => '덧글 수',
    'sb_numpostsdscp'    => '인접블러그에 표시한 덧글 수',
    'sb_latestposts'     => '최신덧글',
    'sb_latestpostsdscp' => '토픽 하나에 메세지 하나씩 만을 표시하기',
    'userdatefmt'        => '날짜 적힌 포멧',
    'userdatefmtdscp'    => '사용자가 설정한 날짜적힌 포멧에 따르기',
    'spamxplugin'        => '스팸 X 플러그인',
    'spamxplugindscp'    => '투고 전에 스팸-X 플러그인으로 스팸을 판정하기',
    'pmplugin'           => 'PM 플러그인',
    'pmplugindscp'       => '개인정보 메세지 플러그인은 별도로 인스트롤 되어 있으므로 그것에 유효토록 합니다',
    'smiliesplugin'       => '스마일 플러그인',
    'smiliesplugindscp'  => '스마일 플러그인 혹은 외부관수를 사용하기',
    'geshiformat'        => '코드의 구문강조',
    'geshiformatdscp'    => 'Geshi 코드의 구문강조 기능을 사용하기',
    'edit_timewindow'    => '타임프레임 편집',
    'edit_timewindowdscp' => '자기 자신이 편집 할 수 있는 시간 설정'

);

// Board Admin
$LANG_GF93 = array (
    'gfboard'            => '게시판 관리',
    'vieworder'          => '순위보기',
    'addcat'             => '카테고리 추가',
    'addforum'           => '게시판 추가',
    'order'              => '순위:',
    'catorder'           => '카테고리 순위',
    'forumorder'         => '게시판의 순위',
    'catadded'           => '카테고리가 추가 되었습니다.',
    'catdeleted'         => '카테고리가 삭제 되었습니다.',
    'catedited'          => '카테고리가 갱신 되었습니다.',
    'forumadded'         => '게시판이 추가 되었습니다.',
    'forumaddError'      => '게시판 추가 에러.',
    'forumdeleted'       => '게시판이 삭제 되었습니다',
    'forumedited'        => '게시판이 갱신 되었습니다',
    'forumordered'       => '게시판 순위를 편집',
    'transfer'           => '이동중..',
    'back'               => '돌아가기',
    'addnote'            => '주의: 이들의 변수를 편집 할 수 있습니다.',
    'editnote'           => '게시판의 설명편집: ',
    'editforumnote'      => '편집: <b>"%s"</b>',
    'deleteforumnote1'   => '<b>"%s"</b>&nbsp;삭제해도 괜찮겠습니까?',
    'editcatnote'        => '편집: <b>"%s"</b>',
    'deletecatnote1'     => '<b>"%s"</b>&nbsp;삭제해도 괜찮겠습니까?',
    'deletecatnote2'     => '이 카테고리의 모든 게시판과 토픽이 함께 삭제 됩니다.',
    'undercat'           => '카테고리',
    'deleteforumnote2'   => '이 바로 아래의 덧글은 전부 삭제 됩니다.',
    'groupaccess'        => '그룹: ',
    'rebuild'            => '최신 덧글테이블을 수정',
    'action'             => '실시',
    'forumdescription'   => '게시판 설명',
    'posts'              => '덧글 수',
    'ordertitle'         => '순위',
    'ModDel'             => '삭제',
    'ModEdit'            => '편집',
    'ModMove'            => '이동',
    'ModStick'           => '주목',
    'ModBan'             => '금지',
    'addmoderator'       => "모더레이트를 추가",
    'delmoderator'       => " 선택한 모더레이트를 삭제\n",
    'moderatorwarning'   => '<b>주의: 게시판을 찾을 수 없습니다. </b><br><br> 모더레이터 추가에 앞서서 게시판 카테고리를 작성 하시고 최소한 게시판 하나는 만드시기 바랍니다.',
    'private'           => '개인정보 게시판',
    'filtertitle'       => '모더레이터 정보표시',
    'addmessage'        => '새 모더레이터 추가',
    'allowedfunctions'  => '허가를 받은 권한',
    'userrecords'       => '사용자의 기록',
    'grouprecords'      => '그룹의 기록',
    'filterview'        => '필터 살펴보기',
    'readonly'           => 'Readonly Forum',
    'readonlydscp'       => 'Only the Moderator can post to this forum',
    'hidden'             => 'Hidden Forum',
    'hiddendscp'         => 'Forum does not show in the forum index',
    'hideposts'          => 'Hide New posts',
    'hidepostsdscp'      => 'Updates will not show in the New Posts Blocks or RSS Feeds'
);


$LANG_GF94 = array (
    'mod_title'          => '모더레이터',
    'createmod'          => '모더레이터 작성',
    'deletemod'          => '모더레이터 삭제',
    'currentmods'        => '현재의 모더레이터:',
    'moderates'          => '모더레이터',
    'deletemsg'          => '(주의: 이 단추를 클릭하면 즉시 삭제 됩니다.)',
    'username'           => '사용자 이름:',
    'forforum'           => '게시판용:',
    'modper'             => '접속권한:',
    'candelete'          => '삭제가능:',
    'canban'             => '금지가능:',
    'canedit'            => '편집가능:',
    'canmove'            => '이동가능:',
    'canstick'           => '스티키(Sticky) 작성:',
    'addsuc'             => '모더레이터 정보를 추가 하였습니다.',
    'editsuc'            => '모더레이터 정보를 변경 하였습니다.',
    'removesuc'          => '모더레이터 삭제: ',
    'removesuc2'         => '모더레이터는 모든 게시판으로 부터 삭제 되었습니다.',
    'modexists'          => '모더레이터는 존재합니다',
    'modexistsmsg'       => '에러: 이 모더레이터는 이미 등록을 마쳤습니다.',
    'transfer'           => '..',
    'removemodnote1'     => '다음 게시판의 모더레이터를 해임 하시겠습니까, %s씨? ',
    'removemodnote2'     => '일단 해임하면 그 게시판을 관리 할 수 없습니다.',
    'removemodnote3'     => '모든 게시판에서 이 모더레이터를 해임 하시겠습니까, %s씨?',
    'removemodnote4'     => '일단 해임하면 어떠한 게시판에도 모더레이터가 될 수 없습니다.',
    'allforums'          => '모든 게시판'
);


$LANG_GF95 = array (
    'header1'           => '투고된 토픽에 관하여 논의해 보기로 합시다.',
    'header2'           => '투고된 토픽에 관한 논의 &nbsp;&raquo;&nbsb;%s',
    'notyet'            => '이 기능은 아직 완전히 꾸며지지 않았습니다',
    'delall'            => '전부 삭제',
    'delallmsg'         => '모든 메세지를 삭제 하시겠습니까,: %s씨?',
    'underforum'        => '<b> %s (ID #%s)',
    'moderate'          => '모더레이터 하기',
    'nomess'            => '투고된 메세지는 아직 없습니다.'
);

$LANG_GF96 = array (
    'gfipman'            => '금지IP 주소',
    'ban'                => '금지',
    'noips'              => '<p style="margin:0px; padding:5px;"> 금지 된 IP 주소는 없습니다!</p>',
    'unban'              => '금지취소',
    'ipbanned'           => '금지IP 주소',
    'banip'              => '금지IP 주소확정',
    'banipmsg'           => '금지 하시겠습니까, IP %s씨?',
    'specip'             => '금지 IP 주소를 지정 하시기 바랍니다!',
    'ipunbanned'         => '금지는 해제 되었습니다.'
);

// IM.php
$LANG_GF97 = array (
    'msgsent'            => '메세지는 보내졌습니다!',
    'msgsave'            => '%s씨에 대한 귀하의 메세지는 보내졌습니다.',
    'msgreturn'          => '귀하의 박스로.',
    'msgerror'           => '메세지는 보내지지 않았습니다.  <a href="javascript:history.back()"> 돌아가기</a> 를 클릭 하셔서 필드 전체를 채워 놓으시기 바랍니다.',
    'msgdelok'           => '삭제 되었습니다',
    'msgdelsuccess'      => '메세지가 삭제 되었습니다.',
    'msgdelerr'          => '메세지가 삭제 되지 않았습니다. <a href=\"javascript:history.back()\">돌아가기</a> 를 클릭하여 한가지를 선택하시기 바랍니다.',
    'msgpriv'            => '개인정보 메세지',
    'msgprivnote1'       => '%s씨 개인정보 메세지가 있습니다.',
    'msgprivnote2'       => '%s씨 개인정보 메세지가 다수 있습니다.',
    'msgto'              => '사용자 이름으로:',
    'msgmembers'         => '회원목록:'
);


$PLG_forum_MESSAGE1 = 'Forum Plugin Upgrade completed - no errors';
$PLG_forum_MESSAGE5 = '게시판 플러그인의 업그레이드에 실패 하였습니다. 에러로그(error.log) 를 살펴보시기 바랍니다.';

?>