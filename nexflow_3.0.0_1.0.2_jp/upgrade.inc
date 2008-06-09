<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
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

function upgrade_300() {
    global $_TABLES;

    $_SQL = array();

    //use this script to upgrade nexflow to 3.1.0
    //require_once ('../../../lib-common.php');

    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD fieldid MEDIUMINT(8) DEFAULT 0 NOT NULL AFTER formid";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD varValue VARCHAR(255) NULL AFTER fieldid";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD incValue MEDIUMINT(8) DEFAULT 0 NOT NULL AFTER varValue";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD varToSet INT(11) DEFAULT 0 NOT NULL AFTER incValue";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD offsetLeft INT(4) UNSIGNED DEFAULT 0 NOT NULL";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD offsetTop INT(4) UNSIGNED DEFAULT 0 NOT NULL";
    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("nexFlow Plugin 3.0.0 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during nexFlow plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET firstTask=0;");
    $res = DB_query("SELECT id FROM {$_TABLES['nftemplate']};");
    while (list ($id) = DB_fetchArray($res)) {
        //still need to set the logical id
        DB_query("INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, nf_stepType, taskname, firstTask) VALUES ($id, 9, 'Start', 1);");
        DB_query("INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, nf_stepType, taskname) VALUES ($id, 10, 'End');");
    }
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (9, 'Start', 0);");
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (10, 'End', 0);");
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (11, 'Set Process Variable', 0);");

    COM_errorLog("Success - Completed nexFlow plugin version 3.0.0 update",1);
    return 0;
}

?>