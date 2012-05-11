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


class AppKit_Auth_Provider_HTTPBasicAuthenticationModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {

    protected $parameters_default = array(
                                        self::AUTH_MODE => self::MODE_SILENT
                                    );

    const DATASOURCE_NAME   = '_SERVER';

    private static $sources_list = array(
                                       '_SERVER'
                                   );

    private static $source_map = array(
                                     'auth_name'    => 'http_uservar',
                                     'auth_type'    => 'http_typevar'
                                 );

    private static $source_map_defaults = array(
            'auth_name' => 'REMOTE_USER,PHP_AUTH_USER',
            'auth_type' => 'AUTH_TYPE'
                                          );

    private $auth_name = null;
    private $auth_type = null;


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

    public function isAvailable($uid, $authid=null) {
        return true;
    }

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

    public function  determineUsername() {
        $source = $this->getVarSource();

        foreach(self::$source_map as $class_target => $config_target) {
            $search_keys = AppKitArrayUtil::trimSplit($this->getParameter($config_target, self::$source_map_defaults[$class_target]));

            if (isset($search_keys[0]) && ($search_value = $source->getParameter($search_keys[0]))) {
                if ($class_target == 'auth_name') {
                    $search_value = strtolower($search_value);

                    if ($strip = strtolower($this->getParameter('auth_strip_domain', ''))) {
                        $m = '~@' . preg_quote($strip, '~') . '~';
                        $this-> { $class_target } = preg_replace($m, '', $search_value);
                    } else {
                        $this-> { $class_target } = $search_value;
                    }
                } else {
                    $this-> { $class_target } = $search_value;
                }
            }
        }

        if ($this->auth_type) {
            $this->auth_type = strtolower($this->auth_type);
        }

        $this->log('Auth.Provider.HTTPBasicAuthentification: Got data (auth_name=%s, auth_type=%s)', $this->auth_name, $this->auth_type, AgaviLogger::DEBUG);

        return $this->auth_name;
    }

    public function getAuthName() {
        return $this->auth_name;
    }

    public function getAuthType() {
        return $this->auth_type;
    }
    
    public function getRealm() {
        return $this->getParameter('http_realm');
    }

}

?>
