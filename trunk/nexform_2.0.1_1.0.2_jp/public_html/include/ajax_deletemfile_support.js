var xmlhttp = null;


/* Only really need the record id of the result to delete, but the fieldid is used to re-create the html.
*  Need to re-attach / replace the HTML for the file listing
*  The fieldid is part of the ID field used to define the DOM attrbute (table)
*/
function ajaxDeleteFile(fieldid,rec,fieldname) {

    if (confirm('Delete file?')) {
        //alert('field:'+fieldid+',rec:'+rec);
        var countname = str_replace('_', '-', fieldname) + 'count';
        var filecount = document.getElementById(countname).value;

        xmlhttp = new XMLHttpRequest();
        var qs = '?op=delete&mode=file&rec=' + rec + '&field=' + fieldid + '&fieldname=' + fieldname + '&filecount=' + filecount;
        xmlhttp.open('GET', site_url + '/ajaxupdate.php' + qs, true);
        xmlhttp.onreadystatechange = function() {
          if (xmlhttp.readyState == 4) {
            receiveFileListing(xmlhttp.responseXML);
          }
        };
        xmlhttp.send(null);
    }
}

function ajaxClearErrorMessage(fieldname) {
    if (confirm('Clear this error message?')) {
        document.getElementById('tbl_'+fieldname).style.border = '0';
        document.getElementById(fieldname).style.display = 'none';
    }
}

function receiveFileListing(dom) {
    var ofield = dom.getElementsByTagName('field');
    var field = ofield[0].firstChild.nodeValue
    var ofieldname = dom.getElementsByTagName('fieldname');
    var fieldname = ofieldname[0].firstChild.nodeValue
    var ocontent = dom.getElementsByTagName('content');

    //alert('tblmfile-id'+field);

    // Get HTML content returned and updated displayed Template Variables
    var obj = document.getElementById('tblmfile-parent-id'+fieldname);
    if (obj.parentNode) {
        // Check for chunks of data returning - look into using the DOM normalize method
        html = ocontent[0].childNodes[0].nodeValue;
        if (ocontent[0].childNodes[1]) {
            html = html + ocontent[0].childNodes[1].nodeValue;
        }
        if (ocontent[0].childNodes[2]) {
            html = html + ocontent[0].childNodes[2].nodeValue;
        }
        obj.parentNode.innerHTML = html;
    }

}
