<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}



class AppKit_Auth_Provider_AuthKeyModel extends AppKitAuthProviderBaseModel  implements AppKitIAuthProvider {

    protected $parameters_default = array(
                                        AppKitIAuthProvider::AUTH_MODE => AppKitIAuthProvider::MODE_SILENT
                                    );

    /**
     * (non-PHPdoc)
     * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
     */
    public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null) {

        if ($user instanceof NsmUser && $user->user_id > 0) {
            if ($user->user_authkey === $password) {
                return true;
            }
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#isAvailable()
     */
    public function isAvailable($uid, $authid=null) {

        $res = AppKitDoctrineUtil::createQuery()
               ->select('COUNT(u.user_id) as cnt')
               ->from('NsmUser u')
               ->where('u.user_authkey=? and user_disabled=? and user_authsrc = ?', array($uid, 0,'auth_key'));

        $res = $res->execute(null, Doctrine::HYDRATE_ARRAY);

        if (isset($res[0]['cnt']) && $res[0]['cnt'] == 1) {
            return true;
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#getUserdata()
     */
    public function getUserdata($uid, $authid=false) {

    }
}
