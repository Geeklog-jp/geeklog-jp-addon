/*
Javascript libray of functions used by the nexform Plugin
Date: June,2005
Author: Blaine Lang
Copyright 2004 (c) Consult4Hire Inc. (portalparts.com)  All Rights Reserved.
*/
  
var win = null;
function NewWindow(mypage,myname,w,h,scroll){
    LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
    TopPosition  = (screen.height) ? (screen.height-h)/2 : 0;
    settings =
        'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
    win = window.open(mypage,myname,settings)
}


function feSetTab(obj,tabid)
{
    obj_id = 'fe_tab' + tabid;
    onavbar = document.getElementById('fe_tabnavbar');

    var tabs = onavbar.getElementsByTagName('a');
    /* Disable all the tabs and enable selected one after */
    for (var i = 0; i < tabs.length; i++ )
    {
        var node = tabs[i];
        node.className = 'navsubmenu';
        divid = i +1;
        odiv = document.getElementById('fe_div'+divid);
        odiv.style.display = 'none';
    }
    /* Enable selected Tab and Form Div */
    obj.className= 'navsubcurrent';
    obj.style.display = '';
    odiv = document.getElementById('fe_div'+tabid);
    odiv.style.display = '';
}


function feSetCurrent(obj)
  {
    if (obj.id == 'fe_link101') {
        obj.className= 'navsubcurrent';
        document.frm_edit.selectedtab.value=1;
        document.getElementById('fe_div1').style.display = '';
        if (document.getElementById('fe_link102'))
        {
            document.getElementById('fe_link102').className = 'navsubmenu';
            document.getElementById('fe_link102').style.backgroundColor = '';
            document.getElementById('fe_div2').style.display = 'none';
        }
        if (document.getElementById('fe_link103'))
        {
            document.getElementById('fe_link103').className = 'navsubmenu';
            document.getElementById('fe_link103').style.backgroundColor = '';
            document.getElementById('fe_div3').style.display = 'none';
        }
        if (document.getElementById('fe_link104'))
        {
            document.getElementById('fe_link104').className = 'navsubmenu';
            document.getElementById('fe_link104').style.backgroundColor = '';
            document.getElementById('fe_div4').style.display = 'none';
        }

    } else if (obj.id == 'fe_link102') {
        obj.className= 'navsubcurrent';
        document.frm_edit.selectedtab.value=2;
        document.getElementById('fe_link101').className = 'navsubmenu';
        document.getElementById('fe_link101').style.backgroundColor = '';
        document.getElementById('fe_div1').style.display = 'none';
        document.getElementById('fe_div2').style.display = '';
        if (document.getElementById('fe_link103'))
        {
            document.getElementById('fe_link103').className = 'navsubmenu';
            document.getElementById('fe_link103').style.backgroundColor = '';
            document.getElementById('fe_div3').style.display = 'none';
        }
        if (document.getElementById('fe_link104'))
        {
            document.getElementById('fe_link104').className = 'navsubmenu';
            document.getElementById('fe_link104').style.backgroundColor = '';
            document.getElementById('fe_div4').style.display = 'none';
        }

    } else if (obj.id == 'fe_link103') {
        document.frm_edit.selectedtab.value=3;
        obj.className= 'navsubcurrent';
        document.getElementById('fe_link101').className = 'navsubmenu';
        document.getElementById('fe_link102').className = 'navsubmenu';
        document.getElementById('fe_link101').style.backgroundColor = '';
        document.getElementById('fe_link102').style.backgroundColor = '';
        document.getElementById('fe_div1').style.display = 'none';
        document.getElementById('fe_div2').style.display = 'none';
        document.getElementById('fe_div3').style.display = '';
        if (document.getElementById('fe_link104'))
        {
            document.getElementById('fe_link104').className = 'navsubmenu';
            document.getElementById('fe_link104').style.backgroundColor = '';
            document.getElementById('fe_div4').style.display = 'none';
        }
    } else if (obj.id == 'fe_link104') {
        document.frm_edit.selectedtab.value=4;
        obj.className= 'navsubcurrent';
        document.getElementById('fe_link101').className = 'navsubmenu';
        document.getElementById('fe_link102').className = 'navsubmenu';
        document.getElementById('fe_link103').className = 'navsubmenu';
        document.getElementById('fe_link101').style.backgroundColor = '';
        document.getElementById('fe_link102').style.backgroundColor = '';
        document.getElementById('fe_link103').style.backgroundColor = '';
        document.getElementById('fe_div1').style.display = 'none';
        document.getElementById('fe_div2').style.display = 'none';
        document.getElementById('fe_div3').style.display = 'none';
        document.getElementById('fe_div4').style.display = '';
    }

  }


function setupCalendar (fieldname, if_format, shows_time) {
    Calendar.setup(
      {
        inputField : fieldname, // ID of the input field
        ifFormat : if_format, // the date format
        showsTime: shows_time,
        align: "tr"
      }
    );
}

function AddRowsToTable(form,id) {
     var tbl = document.getElementById('tblmfile-id'+id);
     var lastRow = tbl.rows.length;

     // if there is no header row in the table, then iteration = lastRow + 1

     var iteration = lastRow;
     var row = tbl.insertRow(lastRow);

     var cellRight = row.insertCell(0);
     var el = document.createElement('input');
     el.setAttribute('type', 'FILE');
     el.setAttribute('name', 'mfile_frm' + form +'_' + id + '[]');
     el.setAttribute('size', '40');
     cellRight.appendChild(el);
}

function RemoveRowFromTable(form,id) {
     var tbl = document.getElementById('tblmfile-id'+id);
     var lastRow = tbl.rows.length;
     if (lastRow > 1) tbl.deleteRow(lastRow - 1);
}


