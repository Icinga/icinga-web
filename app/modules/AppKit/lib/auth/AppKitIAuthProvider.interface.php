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


/**
 * The interface that the auth dispatcher can control our providers
 * @author mhein
 *
 */
interface AppKitIAuthProvider {

    const AUTH_CREATE          = 'auth_create';
    const AUTH_UPDATE          = 'auth_update';
    const AUTH_RESUME          = 'auth_resume';
    const AUTH_GROUPS          = 'auth_groups';
    const AUTH_ENABLE          = 'auth_enable';
    const AUTH_AUTHORITATIVE   = 'auth_authoritative';
    const AUTH_MODE            = 'auth_mode';
    const AUTH_NAME            = 'name';

    const MODE_DEFAULT          = 1;
    const MODE_SILENT           = 2;
    const MODE_BOTH             = 3;

    /**
     * doAuthenticate
     *
     * Tries to Authenticate an user
     * This means the user is available on the provider
     * and ready to ask for authentication
     *
     * @param NsmUser $user
     * @param string $password
     * @return boolean
     */
    public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null);

    /**
     * isAvailable
     *
     * Checks if a user is available by the provider
     *
     * @param mixed $uid
     */
    public function isAvailable($uid, $authid=null);

    /**
     * getUserdata
     *
     * Returns the userdata if allowed to import or
     * update the users profile data
     *
     * Enter description here ...
     * @param mixed $uid
     * @param boolean $authid
     * @return array
     */
    public function getUserdata($uid, $authid=false);

    /**
     * determineUsername
     *
     * If the provider has a change to 'guess'
     * the username before login (SSO, HTTP basic auth)
     * the provider get a change to tell that to
     * the dispatcher
     *
     * @return string
     */
    public function determineUsername();
    
    /**
     * Lots of magic: returns the provider name
     * @return string
     */
    public function getName();
}
