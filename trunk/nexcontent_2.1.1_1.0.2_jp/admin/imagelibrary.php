<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.1.1 for the nexPro Portal Server                     |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | imagelibrary.php                                                          |
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

function makethumbnail($image_name,$src,$dest) {
    global $_CONF, $CONF_SE;
    $do_create = 0;
    if ($image_info = @getimagesize($src)) {
        if ($image_info[2] == 1 || $image_info[2] == 2 || $image_info[2] == 3) {
            $do_create = 1;
        }
    }
    if ($do_create) {
        $dimension = (intval($CONF_SE['auto_thumbnail_dimension'])) ? intval($CONF_SE['auto_thumbnail_dimension']) : 100;
        $resize_type = (intval($CONF_SE['auto_thumbnail_resize_type'])) ? intval($CONF_SE['auto_thumbnail_resize_type']) : 1;
        $quality = (intval($CONF_SE['auto_thumbnail_quality']) && intval($CONF_SE['auto_thumbnail_quality']) <= 100) ? intval($CONF_SE['auto_thumbnail_quality']) : 100;

        if (create_thumbnail($src, $dest, $quality, $dimension, $resize_type)) {
            $new_thumb_name = $new_name;
            $chmod = @chmod ($dest,$CONF_SE['image_perms']);
        }
    }
}


function resize_image($file, $quality, $dimension, $resize_type = 1) {
    global $CONF_SE;
    $image_info = (defined("IN_CP")) ? getimagesize($file) : @getimagesize($file);
    if (!$image_info) {
        return false;
    }
    $file_bak = $file.".bak";
    if (!rename($file, $file_bak)) {
        return false;
    }
    $width_height = get_width_height($dimension, $image_info[0], $image_info[1], $resize_type);
    $resize_handle = "resize_image_".$CONF_SE['convert_tool'];
    if ($resize_handle($file_bak, $file, $quality, $width_height['width'], $width_height['height'], $image_info)) {
        @chmod($file, $CONF_SE['image_perms']);
        @unlink($file_bak);
        $chmod = @chmod ($file,$CONF_SE['image_perms']);
        return true;
    } else {
        rename($file_bak, $file);
        return false;
    }
}

function get_width_height($dimension, $width, $height, $resize_type = 1) {
    if ($resize_type == 2) {
        $new_width = $dimension;
        $new_height = floor(($dimension/$width) * $height);
    } elseif ($resize_type == 3) {
        $new_width = floor(($dimension/$height) * $width);
        $new_height = $dimension;
    } else {
        $ratio = $width / $height;
        if ($ratio > 1) {
            $new_width = $dimension;
            $new_height = floor(($dimension/$width) * $height);
        } else {
            $new_width = floor(($dimension/$height) * $width);
            $new_height = $dimension;
        }
    }
    return array("width" => $new_width, "height" => $new_height);
}


function create_thumbnail($src, $dest, $quality, $dimension, $resize_type) {
    global $CONF_SE;
    if (file_exists($dest)) {
        @unlink($dest);
    }
    $image_info = (defined("IN_CP")) ? getimagesize($src) : @getimagesize($src);
    if (!$image_info) {
        return false;
    }
    $width_height = get_width_height($dimension, $image_info[0], $image_info[1], $resize_type);
    $resize_handle = "resize_image_".$CONF_SE['convert_tool'];
    if ($resize_handle($src, $dest, $quality, $width_height['width'], $width_height['height'], $image_info)) {
        @chmod($dest, $CONF_SE['image_perms']);
        return true;
    } else {
        return false;
    }
}

function resize_image_gd($src, $dest, $quality, $width, $height, $image_info) {
    $types = array(1 => "gif", 2 => "jpeg", 3 => "png");
    if ($CONF_CLUB['gd_type'] = "GD2") {
        $thumb = imagecreatetruecolor($width, $height);
    } else {
        $thumb = imagecreate($width, $height);
    }
    $image_create_handle = "imagecreatefrom".$types[$image_info[2]];
    if ($image = $image_create_handle($src)) {
        if ($CONF_CLUB['gd_type'] = "GD2") {
            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
        } else {
            imagecopyresized($thumb, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
        }
        $image_handle = "image".$types[$image_info[2]];
        $image_handle($thumb, $dest, $quality);
        imagedestroy($image);
        imagedestroy($thumb);
    }
    return (file_exists($dest)) ? 1 : 0;
}

function resize_image_im($src, $dest, $quality, $width, $height, $image_info) {
  global $_CONF;
  //$_CONF['path_to_mogrify']       = '/usr/X11R6/bin/mogrify';
  //$command = $_CONF['path_to_mogrify'] ." -antialias -sample $width"."x"."$height $src $dest";
  $command = "convert -geometry x$width $src $dest";
  //echo "<br>$command";
  system($command);
  return (file_exists($dest)) ? 1 : 0;
}


?>