<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
 * Class AppKit_Auth_Provider_HTTPBasicAuthenticationModel
 *
 * Model that implements authentication based on http headers
 */
class AppKit_Auth_Provider_HTTPBasicAuthenticationModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {
    /**
     * Default parameters
     * @var array
     */
    protected $parameters_default = array(
        self::AUTH_MODE => self::MODE_SILENT
    );

    /**
     * Datasource name
     * @var string
     */
    const DATASOURCE_NAME = '_SERVER';

    /**
     * List of sources
     * @var string[]
     */
    private static $sources_list = array(
       '_SERVER'
   );

    /**
     * Sources
     * @var array
     */
    private static $source_map = array(
         'auth_name'    => 'http_uservar',
         'auth_type'    => 'http_typevar'
     );

    /**
     * Default sources map
     * @var array
     */
    private static $source_map_defaults = array(
        'auth_name' => 'REMOTE_USER,PHP_AUTH_USER',
        'auth_type' => 'AUTH_TYPE'
    );

    /**
     * Name of principal
     * @var string
     */
    private $auth_name = null;

    /**
     * Name of authentication type
     * @var string
     */
    private $auth_type = null;

    /**
     * @param NsmUser $user
     * @param string $password
     * @param null $username
     * @param null $authid
     * @return bool
     */
    public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null) {
        $tuser = $this->loadUserByDQL($user->user_name);
        $username = $user->user_name;
        $authname = $this->getAuthName();
        if($this->getParameter('auth_lowercase_username',false) == true) {
            $username = strtolower($username);
            $authname = strtolower($authname);
        }
        if ($tuser && $tuser instanceof NsmUser && $username == $authname) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $uid
     * @param null $authid
     * @return bool
     */
    public function isAvailable($uid, $authid=null) {
        return true;
    }

    /**
     * @param mixed $uid
     * @param bool $authid
     * @return array
     */
    public function getUserdata($uid, $authid=false) {
        return array(
                   'user_firstname' => $uid,
                   'user_lastname'      => $uid,
                   'user_name'          => $uid,
                   'user_authsrc'       => $this->getProviderName(),
                   'user_disabled'      => 0
               );
    }

    /**
     * @return AgaviParameterHolder
     * @throws AppKitAuthProviderException
     */
    private function getVarSource() {
        $source_name = $this->getParameter('http_source', self::DATASOURCE_NAME);

        if (array_search($source_name, self::$sources_list) === false) {
            throw new AppKitAuthProviderException('http_source (%s) is unknown', $source_name);
        }

        switch ($source_name) {
            case '_SERVER':
            default:
                return new AgaviParameterHolder($_SERVER);
                break;
        }
    }

    /**
     * Tries to detect username
     *
     * Sets appropriate data from header
     *
     * @return null|string
     */
    public function  determineUsername() {
        $source = $this->getVarSource();

        foreach(self::$source_map as $class_target => $config_target) {
            $search_keys = AppKitArrayUtil::trimSplit($this->getParameter($config_target, self::$source_map_defaults[$class_target]));
            $search_value = null;

            //  Looking for multiple keys and use the first match
            foreach ($search_keys as $search_key) {
                if ($source->getParameter($search_key) !== null) {
                    $search_value = $source->getParameter($search_key);
                    $this->log('Auth.Provider.HTTPBasicAuthentification: Got header data: %s=%s', $search_key, $search_value, AgaviILogger::DEBUG);
                    break;
                }
            }

            if ($search_value !== null) {
                if ($class_target == 'auth_name') {

                    // Fixes mixed auth models (case-sensitive and case-insensitive)
                    // see #3714 (Thanks dirk)
                    if ($this->getParameter('auth_lowercase_username',false) == true) {
                        $search_value = strtolower($search_value);
                    }

                    if ($strip = strtolower($this->getParameter('auth_strip_domain', ''))) {
                        $m = '~@' . preg_quote($strip, '~') . '~';
                        $this-> { $class_target } = preg_replace($m, '', $search_value);
                    } else {
                        $this-> { $class_target } = $search_value;
                    }
                } else {
                    $this-> { $class_target } = $search_value;
                }
            } else {
                $this->log('Auth.Provider.HTTPBasicAuthentification: No value found for %s/%s', $class_target, $config_target, AgaviILogger::DEBUG);
            }
        }

        if ($this->auth_type) {
            $this->auth_type = strtolower($this->auth_type);
        }

        $this->log(
            'Auth.Provider.HTTPBasicAuthentification: Got data (auth_name=%s, auth_type=%s)',
            $this->auth_name,
            $this->auth_type,
            AgaviLogger::DEBUG
        );

        return $this->auth_name;
    }

    /**
     * Getter for auth name
     * @return null|string
     */
    public function getAuthName() {
        return $this->auth_name;
    }

    /**
     * Getter for auth type
     * @return null|string
     */
    public function getAuthType() {
        return $this->auth_type;
    }

    /**
     * Getter for realm
     * @return string|null
     */
    public function getRealm() {
        return $this->getParameter('http_realm');
    }
}
