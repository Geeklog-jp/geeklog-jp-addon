<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | orchestrator.php                                                          |
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

require_once ('../lib-common.php');

$nfclass= new nexflow();
$nfclass->set_debug(false);

/* Query to get a list of all un-completed tasks for type 'IF, Batch and Batch Function'
** Loop while we have new tasks of these type so that we can complete all possible tasks
*/

$processlist = array();
$retval = nf_getListofUncompletedTasks(&$processlist);
$taskcount = $retval['count'];
$processlist = $retval['list'];
$i = 1;
do {
    if ($nfclass->_debug ) {
        COM_errorLog("Orchestrator: Loop:$i, $taskcount un-completed tasks found");
    }
    $nfclass->clean_queue();
    $retval = nf_getListofUncompletedTasks(&$processlist);
    $taskcount = $retval['count'];
    $processlist = $retval['list'];
    $i++;
} while ($taskcount > 0);


?>