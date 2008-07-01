  window.onload = function()
  {
   var oFCKeditor = new FCKeditor( 'sitecontent' ) ;
   oFCKeditor.BasePath = "{site_url}/fckeditor/" ;
   //oFCKeditor1.Config['CustomConfigurationsPath'] = "{site_url}/fckeditor/myconfig.js";
   oFCKeditor.ToolbarSet = 'Default' ;
   oFCKeditor.Height = 400 ;
   oFCKeditor.ReplaceTextarea() ;
  }

  function changeTextAreaSize($option) {
    var size=0;
    $size = document.getElementById('sitecontent___Frame').height;
    if ($option == 'larger') {
        document.getElementById('sitecontent___Frame').height = +($size) + 50;
    } else if ($option == 'smaller') {
        document.getElementById('sitecontent___Frame').height = +($size) - 50;
    }
  }

  function showhidehelp($option) {
    if ($option == 'general') {
        if (document.getElementById('se_help1').value == 'Tag Help') {;
            document.getElementById('se_help1').value = 'Hide Help';
            document.getElementById('se_helpcontent1').style.display = '';
            document.getElementById('se_help2').style.display = 'none';
            document.getElementById('se_help3').style.display = 'none';
            document.getElementById('se_helpcontent3').style.display = 'none';
        } else {
            document.getElementById('se_help1').value = 'Tag Help';
            document.getElementById('se_helpcontent1').style.display = 'none';
            document.getElementById('se_helpcontent2').style.display = 'none';
            document.getElementById('se_helpcontent3').style.display = 'none';
            document.getElementById('se_help2').style.display = '';
            document.getElementById('se_help3').style.display = '';
        }
    } else if ($option == 'blocks') {
        document.getElementById('se_help1').value = 'Hide Help';
        document.getElementById('se_helpcontent1').style.display = 'none';
        document.getElementById('se_helpcontent2').style.display = '';
        document.getElementById('se_helpcontent3').style.display = 'none';
        document.getElementById('se_help2').style.display = 'none';
        document.getElementById('se_help3').style.display = 'none';
    } else if ($option == 'custom') {
        document.getElementById('se_help1').value = 'Hide Help';
        document.getElementById('se_helpcontent1').style.display = 'none';
        document.getElementById('se_helpcontent2').style.display = 'none';
        document.getElementById('se_helpcontent3').style.display = '';
        document.getElementById('se_help2').style.display = 'none';
        document.getElementById('se_help3').style.display = 'none';
    }
  }