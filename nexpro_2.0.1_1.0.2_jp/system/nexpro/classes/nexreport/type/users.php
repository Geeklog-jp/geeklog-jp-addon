<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | users.php                                                                 |
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

class users_report extends report {

    // Private Properties


    var $_projectid = 0;             // integer

    var $_processid = 0;             // integer
 

    function _constructor($options)
    { 
        if (isset($options['id'])) {
            parent::_construct($options['id']);
        } else {
            parent::_construct();
        }
    }


    function _format_status($value) {

		$retval = '';
		switch ($value) {
			case 0:
				$retval = 'Disabled';
				break;
			case 1:
				$retval = 'Awaiting Activation';
				break;
			case 2:
				$retval = 'Awaiting Approval';
				break;
			case 3:
				$retval = 'Active';
				break;
		}
		return $retval;

    }

    function _format_showonline($value) {

		$retval = '';
		switch ($value) {
			case 0:
				$retval = 'Disabled';
				break;

            case 1:
                $retval = 'Enabled';
                break;

        }
        return $retval;
    }


}  


?>