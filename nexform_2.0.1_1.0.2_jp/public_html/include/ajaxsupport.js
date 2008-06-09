var xmlhttp = null;

function ajaxUpdateFieldOption(chk,op) {
    var chkvar = chk.name;
    var chkvalue = chk.checked;

    /* Need to parse out the record id from the checkbox var name */
    recstring = chkvar.split("[");
    recstring = recstring[1].split("]");
    rec=recstring[0];

    var fa = document.getElementById('fieldaction_'+rec);
    var fs = document.getElementById('fieldstatus_'+rec);
    fs.firstChild.nodeValue='Updating ...';
    fa.style.display='none';
    fs.style.display='';

    xmlhttp = new XMLHttpRequest();
    var qs = '?op='+op+'&mode=field&rec=' + rec +
        '&setting=' + chkvalue;
    xmlhttp.open('GET',  public_url + '/ajaxupdate.php' + qs, true);
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4) {
        receiveRequestOptionType(xmlhttp.responseXML);
      }
    };
    xmlhttp.send(null);
}

function receiveRequestOptionType(dom) {
    var rec = dom.getElementsByTagName('record');
    //alert ('Return record is: ' + rec[0].firstChild.nodeValue);
    var fa = document.getElementById('fieldaction_'+rec[0].firstChild.nodeValue);
    var fs = document.getElementById('fieldstatus_'+rec[0].firstChild.nodeValue);
    fs.firstChild.nodeValue='&nbsp;';
    fa.style.display='';
    fs.style.display='none';
}
