<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | LDAP.auth.class.php                                                       |
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

class LDAP
{
    var $email;

	function authenticate($username, $password)
    {
        //initialization
		global $CONF_NEXPRO;
        $this->email = '';

		//connect to ldap server
        $ds=ldap_connect($CONF_NEXPRO['ldap_server']);

        if ($ds) { //if we connected
			if ($CONF_NEXPRO['ldap_type'] == 'Active Directory') {
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION,3);
				ldap_set_option($ds, LDAP_OPT_REFERRALS,0);
			}

            foreach ($CONF_NEXPRO['ldap_ous'] as $ou) {
                $bind_string = sprintf($CONF_NEXPRO['ldap_bind_string'], $username, $ou);
				$organization_string = sprintf($CONF_NEXPRO['ldap_organization_string'], $ou);

                // bind with appropriate dn to give update access
                $r=@ldap_bind($ds, $bind_string, $password);
                if ($r!==FALSE) {
                    //authenticated successfully
					//now search through the ldap for an email address
					$search_string = sprintf($CONF_NEXPRO['ldap_search_string'], $username);

                    $sr=ldap_search($ds, $organization_string, $search_string);
                    $count = ldap_count_entries($ds, $sr);

                    if ($count > 0) {
                        $info = ldap_get_entries($ds, $sr);
                        $this->email = $info[0][$CONF_NEXPRO['email_parm']][0];
                        ldap_close($ds);

                        return true;
                    }
                    else {
						continue;
                    }
                }
            }//end foreach $CONF_NEXPRO['ldap_ous']

            //if we reach this point in the code that means we weren't able to bind
            ldap_close($ds);

            return false;

        }//end if $ds

		return false;

    }//end method
}//end class LDAP
