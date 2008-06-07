<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexfilter.class.php                                                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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


if (strpos ($_SERVER['PHP_SELF'], 'nexfilter.class.php') !== false) {
    die ('This file can not be used on its own.');
}


class nexfilter {

    var $_dirtydata = array();      // Data to be filtered
    var $_cleandata = array();      // Cleaned Data after filtering
    var $_logmode = false;
    var $_checkwords = true;
    var $_checkhtml = true;
    var $_prepfordb = false;
    var $_prepforweb = false;
    var $_stripaccents = true;     // Used with AJAX code to remove french accent characters that trigger JS errors
    var $_maxlength = 0;            // Set to 0 to disable

   /* Defines used for the stripaccess method */
   var $_stripchars = "ŠŒšœŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖØÙÚÛÜİßàáâãäåæçèéêëìíîïğñòóôõöøùúûüıÿ";
   var $_stripreplace = "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy";


    /* Filter modes allows this class to be extended.
     * Need to have matching class method _cleanType
     */
    var $_filtermodes = array('int','char','text');



    public function setLogging($state) {
        if ($state === true or $state == 'on') {
            $this->_logmode = true;
        } elseif ($state === false or $state == 'off') {
            $this->_logmode = false;
        }
    }

    public function setCheckwords($state) {
        if ($state === true or $state == 'on') {
            $this->_checkwords = true;
        } elseif ($state === false or $state == 'off') {
            $this->_checkwords = false;
        }
    }

    public function setPrepfordb($state) {
        if ($state === true or $state == 'on') {
            $this->_prepfordb = true;
            $this->_prepforweb = false;
        } elseif ($state === false or $state == 'off') {
            $this->_prepfordb = false;
        }
    }

    public function setPrepforweb($state) {
        if ($state === true or $state == 'on') {
            $this->_prepforweb = true;
            $this->_prepfordb = false;
        } elseif ($state === false or $state == 'off') {
            $this->_prepforweb = false;
        }
    }

    public function setStripAccents($state) {
        if ($state === true or $state == 'on') {
            $this->_stripaccents = true;
        } elseif ($state === false or $state == 'off') {
            $this->_stripaccents = false;
        }
    }

    public function setMaxlength($length) {
        if ($length > 0) {
            $this->_maxlength = $length;
        } else {
            $this->_maxlength = 0;
        }
    }

    private function _initFilter() {
        $this->_dirtydata = array();
        $this->_cleandata = array();
    }

    /* apply the free webtext filter to input which may need to contain quote's or other special characters */
    private function _filterText( $var ) {
        // Need to call addslashes again as COM_checkHTML strips it out
        if ($this->_checkhtml) $var = COM_checkHTML($var);
        if ($this->_checkwords) $var = COM_checkWords($var);
        $var = COM_killJS($var);
        if ($this->_maxlength > 0) {
            $var = substr($var, 0, $this->_maxlength);
        }
        if ($this->_prepfordb) {
            $var = addslashes($var);
        } elseif ($this->_prepforweb) {
            $var = stripslashes($var);
            if ($this->_stripaccents) {
                // Use the string translate function to replace accent characters
                $var = strtr($var,$this->_stripchars,$this->_stripreplace);
            }
        }
        return $var;
    }

    /* Default filter for character and numeric data */
    private function _applyFilter( $parameter, $isnumeric = false ) {
        $p = COM_stripslashes( $parameter );
        $p = strip_tags( $p );
        $p = COM_killJS( $p ); // doesn't help a lot right now, but still ...
        if( $isnumeric ) {
            // Note: PHP's is_numeric() accepts values like 4e4 as numeric
            if( !is_numeric( $p ) || ( preg_match( '/^-?\d+$/', $p ) == 0 )) {
                $p = 0;
            }
        } else {
            if ($this->_checkwords) $p = COM_checkWords($p);
            $p = preg_replace( '/\/\*.*/', '', $p );
            $pa = explode( "'", $p );
            $pa = explode( '"', $pa[0] );
            $pa = explode( '`', $pa[0] );
            $pa = explode( ';', $pa[0] );
            //$pa = explode( ',', $pa[0] );
            $pa = explode( '\\', $pa[0] );
            $p = $pa[0];

            if ($this->_prepfordb) {
                $p = addslashes($p);
            } elseif ($this->_prepforweb) {
                $p = stripslashes($p);
                if ($this->_stripaccents) {
                    // Use the string translate function to replace accent characters
                    $p = strtr($p,$this->_stripchars,$this->_stripreplace);
                }
            }
        }

        if ($this->_maxlength > 0) {
            $p = substr($p, 0, $this->_maxlength);
        }

        if( $this->_logmode ) {
            if( strcmp( $p, $parameter ) != 0 ) {
                COM_errorLog( "Filter applied: >> $parameter << filtered to $p [IP {$_SERVER['REMOTE_ADDR']}]", 1);
            }
        }

        return $p;
    }


    private function _cleanText() {

        foreach ($this->_dirtydata['text'] as $var => $value) {
            // Check if this variable is an array - maybe a checkbox or multiple select
            if (is_array($value)) {
                $subvalues_array = array();
                foreach ($value as $subvalue) {
                    $subvalues_array[] = $this->_filterText($subvalue);
                }
                $this->_cleandata['text'][$var] = $subvalues_array;
            } else {
                $this->_cleandata['text'][$var] = $this->_filterText($value);
            }
        }

    }


    private function _cleanChar() {

        foreach ($this->_dirtydata['char'] as $var => $value) {
            // Check if this variable is an array - maybe a checkbox or multiple select
            if (is_array($value)) {
                $subvalues_array = array();
                foreach ($value as $subvalue) {
                    $subvalues_array[] = $this->_applyFilter($subvalue);
                }
                $this->_cleandata['char'][$var] = $subvalues_array;
            } else {
                $this->_cleandata['char'][$var] = $this->_applyFilter($value);
            }
        }

    }

    private function _cleanInt() {

        foreach ($this->_dirtydata['int'] as $var => $value) {
            // Check if this variable is an array - maybe a checkbox or multiple select
            if (is_array($value)) {
                $subvalues_array = array();
                foreach ($value as $subvalue) {
                    $subvalues_array[] = $this->_applyFilter($subvalue,true);
                }
                $this->_cleandata['int'][$var] = $subvalues_array;
            } else {
                $this->_cleandata['int'][$var] = $this->_applyFilter($value,true);
            }
        }

    }

    public function cleanData($mode,$data) {
        if (in_array($mode,$this->_filtermodes)) {
            if (is_array($data)) {
                foreach ($data as $var => $value ) {
                  $this->_dirtydata[$mode][$var] = $value;
                }
            } else {
                $this->_dirtydata[$mode][] = $data;
            }
        }
    }


    /* Return the cleaned data loaded using the cleanData method
     * Or optionally pass in the data to be cleaned as well for a direct one-function call use
     */
    public function getCleanData($type='',$data='') {

        $retval = '';
        if (!empty($data)) {
            $this->cleanData($type,$data);
        }

        /* Check if we need to return just one type of filtered data */
        if ($type != '' AND in_array($type,$this->_filtermodes)) {
            $filterFunction = '_clean' . ucfirst($type);
            if (method_exists($this,$filterFunction)) {
                $this->$filterFunction();
                // If just one variable in clean data, then no need to return an array of values
                if (count($this->_cleandata[$type]) == 1) {
                    $retval = $this->_cleandata[$type][0];
                } else {
                    $retval = $this->_cleandata[$type];
                }
            }

        } else {
            /* Filter and return an associative array of filtered data - per filter type */
            foreach($this->_dirtydata as $type => $data)  {
                $filterFunction = '_clean' . ucfirst($type);
                if (method_exists($this,$filterFunction)) {
                    $this->$filterFunction();
                }
            }
            $retval = $this->_cleandata;
        }

        // Reset the filter class data now that we have processed the filtering
        $this->_initFilter;

        return $retval;
    }

    public function getWebData($type='',$data='') {

        $retval = '';
        $currentWebState = $this->_prepforweb;
        $currentDbState = $this->_prepfordb;
        $this->setPrepforweb(true);

        if (!empty($data)) {
            $this->cleanData($type,$data);
        }

        /* Check if we need to return just one type of filtered data */
        if ($type != '' AND in_array($type,$this->_filtermodes)) {
            $filterFunction = '_clean' . ucfirst($type);
            if (method_exists($this,$filterFunction)) {
                $this->$filterFunction();
                // If just one variable in clean data, then no need to return an array of values
                if (count($this->_cleandata[$type]) == 1) {
                    $retval = $this->_cleandata[$type][0];
                } else {
                    $retval = $this->_cleandata[$type];
                }
            }

        } else {
            /* Filter and return an associative array of filtered data - per filter type */
            foreach($this->_dirtydata as $type => $data)  {
                $filterFunction = '_clean' . ucfirst($type);
                if (method_exists($this,$filterFunction)) {
                    $this->$filterFunction();
                }
            }
            $retval = $this->_cleandata;
        }

        // Reset filter options
        $this->setPrepforweb($currentWebState);
        $this->setPrepfordb($currentDbState);

        return $retval;
    }

    public function getDbData($type='',$data='') {

        $retval = '';
        $currentWebState = $this->_prepforweb;
        $currentDbState = $this->_prepfordb;
        $this->setPrepfordb(true);

        if (!empty($data)) {
            $this->cleanData($type,$data);
        }

        /* Check if we need to return just one type of filtered data */
        if ($type != '' AND in_array($type,$this->_filtermodes)) {
            $filterFunction = '_clean' . ucfirst($type);
            if (method_exists($this,$filterFunction)) {
                $this->$filterFunction();
                // If just one variable in clean data, then no need to return an array of values
                if (count($this->_cleandata[$type]) == 1) {
                    $retval = $this->_cleandata[$type][0];
                } else {
                    $retval = $this->_cleandata[$type];
                }
            }

        } else {
            /* Filter and return an associative array of filtered data - per filter type */
            foreach($this->_dirtydata as $type => $data)  {
                $filterFunction = '_clean' . ucfirst($type);
                if (method_exists($this,$filterFunction)) {
                    $this->$filterFunction();
                }
            }
            $retval = $this->_cleandata;
        }

        // Reset filter options
        $this->setPrepforweb($currentWebState);
        $this->setPrepfordb($currentDbState);

        return $retval;
    }


} // End of class


?>