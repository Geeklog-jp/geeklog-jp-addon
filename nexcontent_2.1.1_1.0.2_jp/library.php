<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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

function nexcontent_formatPage($catid, $pageid, $content) {
    global $_CONF, $CONF_SE, $_TABLES;

    $result = DB_query("SELECT imagefile,imagenum,autoscale FROM {$_TABLES['nexcontent_images']} WHERE page_id = '$pageid' ORDER BY imagenum");
    $nrows = DB_numRows($result);
    $errors = array();
    $pageImageDir = $CONF_SE['uploadpath'] . "/{$pageid}/";
    $pageImageURL = $CONF_SE['public_url']. "/images/{$pageid}/";

    $breaktag = '[break]';
    // Count the number of break tags to figure out the column width to use.
    $offset = 0;
    $startpos = 0;
    $columns=1;
    $strpos = strpos($content, $breaktag,$offset);
    while ($strpos !== FALSE) {
        $columns++;
        $offset = $strpos +7;
        $strpos = strpos($content, $breaktag,$offset);
    }

    $width = round(100/$columns);
    $newtag = '</td><td class="content" width="'.$width.'%">';
    $content = str_replace($breaktag,$newtag, $content);

    $content = PLG_replacetags($content);

    /* For each image - format page location */
    for ($i = 1; $i <= $nrows; $i++) {
        list($image,$imagenum,$scaleopt) = DB_fetchArray($result);
        if (file_exists($pageImageDir . $image)) {
            if( $scaleopt == '0') {  // If don't use scaled image and there is an original image - use it.
                $pos = strrpos($image,'.');
                $filename = strtolower(substr($image, 0,$pos));
                $ext = strtolower(substr($image, $pos));
                $origimage = "{$filename}_original{$ext}";
                if (file_exists($pageImageDir . $origimage)) {
                    $image = $origimage;
                }
            }
            $dimensions = GetImageSize($pageImageDir . $image);
            if (!empty($dimensions[0]) AND !empty($dimensions[1])) {
                $sizeattributes = 'width="' . $dimensions[0] . '" height="' . $dimensions[1] . '" ';
            } else {
                $sizeattributes = '';
            }
            //$sizeattributes = 'width="100%"';

            $norm =  '[image' . $imagenum . ']';
            $center =  '[image' . $imagenum . '_center]';             
            $left =  '[image' . $imagenum . '_left]';
            $right = '[image' . $imagenum . '_right]';
            $icount = substr_count($content, $norm) + substr_count($content, $left) + substr_count($content, $right) + substr_count($content, $center);
            if ($icount > 0) {
                $imgSrc = $pageImageURL . $image;
                $content = str_replace($norm,  '<img class="se_image" ' . $sizeattributes . ' src="' . $imgSrc . '" alt="">', $content);                 
                $content = str_replace($center,  '<div style="width:100%;text-align:center;padding:5px 0px 5px 0px;"><img ' . $sizeattributes . ' src="' . $imgSrc . '" alt=""></div>', $content);
                $content = str_replace($left,  '<img class="se_image_left" ' . $sizeattributes . ' src="' . $imgSrc . '"  alt="">', $content);
                $content = str_replace($right, '<img class="se_image_right" ' . $sizeattributes . ' src="' . $imgSrc . '"  alt="">', $content);
            }
        }
    }

    /* Strip out any custom block formatting tags */
    $content = nexcontent_stripBlockTags($content);

    return $content;
}


function nexcontent_stripBlockTags($content) {
    global $_CONF, $_TABLES;

    $sql = "SELECT name FROM {$_TABLES['blocks']} ";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    for ($i = 1; $i <= $nrows; $i++) {
        $A = DB_fetchArray($result);
        $blk_left =  '[block_' . $A['name'] . '_left]';
        $blk_right =  '[block_' . $A['name'] . '_right]';

        if ( strpos($content, $blk_left) !== FALSE ) {
            $content = str_replace($blk_left,'', $content);
        }
        if ( strpos($content, $blk_right) !== FALSE ) {
            $content = str_replace($blk_right,'', $content);
        }

    }
    return $content;
}


function nexcontent_getCategoryBlockTagHTML(&$content,$location) {
    $tag_prefix =  '[categorymenu_'.$location; 
    $offset = 0;
    $prev_offset = 0;
    $contentlen = strlen ($content);
    $categoryBlockHTML = '';
    while (true) {
        $start_pos = strpos (strtolower ($content), $tag_prefix, $offset);
        if ($start_pos === false) {
            break;
        } else {
            $end_pos = strpos (strtolower ($content), ']', $start_pos);              
            $ctag = substr ($content, $start_pos, ($end_pos - $start_pos)) .']';
            $parms = explode (':', $ctag);
            $category = str_replace(']','',$parms[1]);
            $categoryBlockHTML .= phpblock_nexcontentBlockmenu($category);
            $content = str_replace($ctag,'', $content);            
        }
    }
    return $categoryBlockHTML;
}




function nexcontent_showblocks(&$content,$location) {
    global $content, $_CONF, $_TABLES;

    /* Check if category menu has been requested - requires page to be using customblock mode */
    $tag =  '[categorymenu_'.$location;   
    $categoryBlockHTML = nexcontent_getCategoryBlockTagHTML($content,$location);

    $sql = "SELECT bid, name,type,title,content,rdfurl,phpblockfn,help,allow_autotags FROM {$_TABLES['blocks']} ORDER BY blockorder,title asc";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);
    $blockPosition=0;
    for ($i = 1; $i <= $nrows; $i++) {
        $A = DB_fetchArray($result);
        $tag =  '[block_' . $A['name'] . '_'.$location.']';
        if ( strpos($content, $tag) !== FALSE ) {
            $blockPosition++;
            if ($blockPosition == 2) {
                $retval .= $categoryBlockHTML;
                $categoryBlockHTML = '';
            } 
            $retval .= COM_formatBlock($A);
        }
    }
    if ($categoryBlockHTML != '') {
        $retval .= $categoryBlockHTML;
    }
    return $retval;
}


function nexcontent_recursiveView(&$node,$cid) { 
    global $_CONF,$_TABLES,$catid;

    $sql = "SELECT id,pid,name FROM {$_TABLES['nexcontent_pages']} WHERE pid ='$cid' AND type='category'";
    $sql .= COM_getPermSQL('AND');
    $sql .= ' ORDER BY pageorder,id';
    $query = DB_QUERY($sql);
    while ( list($id,$pid,$name) = DB_fetchARRAY($query)) {
        //echo "<br>subfunction -> id:$id, pid:$pid, name:$name";
        $pquery = DB_query("SELECT id FROM {$_TABLES['nexcontent_pages']} WHERE pid='{$id}' AND type='page'");
        $numpages = DB_numRows($pquery);
        if ($numpages > 0) {
            $name = $name .'&nbsp;('.$numpages.')';
        }
        if ($catid == $id) {
            $name = '<span class="treeMenuSelected">' .$name . '</span>';
        }
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['nexcontent_pages'], 'pid', $id) > 0) {
            $subnode[$id] = new HTML_TreeNode(array('text' => $name ,'link' => $_CONF['site_admin_url'] ."/plugins/nexcontent/index.php?catid=" .$id ,'icon' => 'folder.gif'));
            nexcontent_recursiveView($subnode[$id], $id);
            $node->addItem($subnode[$id]);
        } else { 
            $node->addItem(new HTML_TreeNode(array('text' => $name, 'link' =>$_CONF['site_admin_url'] ."/plugins/nexcontent/index.php?catid=" .$id , 'icon' => 'folder.gif')));
        } 
    } 
}

/* Called to show available folders when Adding or Editing a (page or category) */
/* Requires Edit permission to category */
function nexcontent_getFolderList($selected='',$mode='',$exclude ='0',$pid='0',$level='1',$selectlist='') { 
    global $_TABLES;
    /* Retrieve all enabled TOP Level Menu Items for this level */
    $sql = "SELECT id,pid, type, name FROM {$_TABLES['nexcontent_pages']} WHERE pid='$pid' AND type='category'";
    if ($mode == '') {
        $sql .= COM_getPermSQL('AND','0',2);
    } else {
        $sql .= COM_getPermSQL('AND','0',3);
    }
    $sql .= ' ORDER BY pageorder';
    $query = DB_query($sql);
    while ( list($id,$pid,$type,$label) = DB_fetchARRAY($query)) {
        //echo "<br>Mode:$mode,Type:$pagetype, id:$id, selected:$selected, exclude:$exclude";
        if ( $id != $exclude AND DB_COUNT($_TABLES['nexcontent_pages'], 'pid', $id) > 0) {
            $selectlist .= '<option value="' . $id;
            $indent='';
            if ($level > 1) {
                for ($i=2; $i<= $level; $i++) {
                    $indent .= "--";
                }
            }
            if ($id == $selected) {
                $selectlist .= '" Selected>' .$indent .$label . '</option>' . LB;
            } else {
                $selectlist .= '">' . $indent .$label . '</option>' . LB;
            }
            $selectlist = nexcontent_getFolderList($selected,$mode,$exclude,$id,$level+1,$selectlist);

        } elseif ($mode == '' OR $mode == 'add' OR ($mode == 'edit' AND $id != $exclude)) {
            // Don't show current category in list if in Edit Category Mode - Can't make it a linked to itself 
            $indent = '';
            if ($level > 1) {
                for ($i=2; $i<= $level; $i++) {
                    $indent .= "--";
                }
            }
            $selectlist .= '<option value="' . $id;
            if ($id == $selected) {
                $selectlist .= '" Selected>' . $indent . $label . '</option>' . LB;
            } else {
                $selectlist .= '">' . $indent . $label . '</option>' . LB;
            }
        }
    }
    return $selectlist;
}


function nexcontent_submenu ($menuitems, $selected='', $parms='') {
    global $_CONF;

    $navbar = new Template($_CONF['path_layout'] . 'nexcontent/navbar');
    $navbar->set_file (array (
        'navbar'       => 'navbar.thtml',
        'menuitem'     => 'menuitem.thtml',
        ));
    for ($i=1; $i <= count($menuitems); $i++)  {
        $parms = explode( "=",current($menuitems) );
        $navbar->set_var( 'link',   current($menuitems));
        if (key($menuitems) == $selected) {
            $navbar->set_var( 'cssactive', 'id="active"');
            $navbar->set_var( 'csscurrent','id="current"');
        } else {
            $navbar->set_var( 'cssactive', '');
            $navbar->set_var( 'csscurrent','');
        }
        $navbar->set_var( 'label',  key($menuitems));
        $navbar->parse( 'menuitems', 'menuitem', true );
        next($menuitems);
    }
    $navbar->parse ('output', 'navbar');
    $retval = $navbar->finish($navbar->get_var('output'));
    return $retval;
}


function nc_copyRecord($table, $primary_key, $value) {
    //first get the values of the requested record
    $record = DB_query("SELECT * FROM $table WHERE $primary_key = '$value';");
    $R = DB_fetchArray($record, false);

    //then discover the schema of the table
    $schema = DB_query("DESCRIBE $table;");

    //now build an sql string to copy one to the other
    $fields = '';
    $values = '';
    while ($A = DB_fetchArray($schema, false)) {
        if ($A['Field'] != $primary_key) {
            if ($fields != '') {
                $fields .= ', ';
                $values .= ', ';
            }
            $fields .= $A['Field'];
            $values .= "'" . addslashes($R[$A['Field']]) ."'";
        }
    }
    $sql = "INSERT INTO $table ($fields) VALUES ($values);";
    DB_query($sql);
    $retval = DB_insertID();

    return $retval;
}


?>