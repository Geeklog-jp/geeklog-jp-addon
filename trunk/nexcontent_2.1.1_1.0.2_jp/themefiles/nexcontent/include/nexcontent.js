/* 
Javascript libray of functions used by the Nextide nexcontent Plugin
Date: Aug,2004
Author: Blaine Lang
Copyright 2004 (c) Nextide Inc. All Rights Reserved.
*/
  
  if (getCookie('leftblocksmode') == '') {
        document.cookie = "leftblocksmode=show";
  }

  function showblock( id )
  {
    document.getElementById(id).style.display = '';
  }

  function setcurrent(obj)
  {
    if (obj.id == 'se_link101') {
        obj.className= 'navsubcurrent';
        document.getElementById('se_link102').className = 'navsubmenu';
        document.getElementById('se_link103').className = 'navsubmenu';
        document.getElementById('se_link104').className = 'navsubmenu';
        document.getElementById('se_link102').style.backgroundColor = '';
        document.getElementById('se_link103').style.backgroundColor = '';
        document.getElementById('se_link104').style.backgroundColor = '';
        document.getElementById('se_div1').style.display = '';
        document.getElementById('se_div2').style.display = 'none';
        document.getElementById('se_div3').style.display = 'none';
        document.getElementById('se_div4').style.display = 'none';
    } else if (obj.id == 'se_link102') {
        obj.className= 'navsubcurrent';
        document.getElementById('se_link101').className = 'navsubmenu';
        document.getElementById('se_link103').className = 'navsubmenu';
        document.getElementById('se_link104').className = 'navsubmenu';
        document.getElementById('se_link101').style.backgroundColor = '';
        document.getElementById('se_link103').style.backgroundColor = '';
        document.getElementById('se_link104').style.backgroundColor = '';
        document.getElementById('se_div1').style.display = 'none';
        document.getElementById('se_div2').style.display = '';
        document.getElementById('se_div3').style.display = 'none';
        document.getElementById('se_div4').style.display = 'none';
    } else if (obj.id == 'se_link103') {
        obj.className= 'navsubcurrent';
        document.getElementById('se_link101').className = 'navsubmenu';
        document.getElementById('se_link102').className = 'navsubmenu';
        document.getElementById('se_link104').className = 'navsubmenu';
        document.getElementById('se_link101').style.backgroundColor = '';
        document.getElementById('se_link102').style.backgroundColor = '';
        document.getElementById('se_link104').style.backgroundColor = '';
        document.getElementById('se_div1').style.display = 'none';
        document.getElementById('se_div2').style.display = 'none';
        document.getElementById('se_div3').style.display = '';
        document.getElementById('se_div4').style.display = 'none';
    } else if (obj.id == 'se_link104') {
        obj.className= 'navsubcurrent';
        document.getElementById('se_link101').className = 'navsubmenu';
        document.getElementById('se_link102').className = 'navsubmenu';
        document.getElementById('se_link103').className = 'navsubmenu';
        document.getElementById('se_link101').style.backgroundColor = '';
        document.getElementById('se_link102').style.backgroundColor = '';
        document.getElementById('se_link103').style.backgroundColor = '';
        document.getElementById('se_div1').style.display = 'none';
        document.getElementById('se_div2').style.display = 'none';
        document.getElementById('se_div3').style.display = 'none';
        document.getElementById('se_div4').style.display = '';
}

  }

  function hideblock( id )
  {
    document.getElementById(id).style.display = '';
  }

  function getCookie (name)
  {
    var dc = document.cookie;
    var cname = name + "=";
    var clen = dc.length;
    var cbegin = 0;

    while (cbegin < clen) {
        var vbegin = cbegin + cname.length;
        if (dc.substring(cbegin, vbegin) == cname) {
            var vend = dc.indexOf (";", vbegin);
            if (vend == -1) vend = clen;
                return unescape(dc.substring(vbegin, vend));
        }
        cbegin = dc.indexOf(" ", cbegin) + 1;
        if (cbegin== 0) break;
    }
    return null;
  }