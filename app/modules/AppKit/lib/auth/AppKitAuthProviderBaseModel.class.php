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
 * Base class for writing auth providers
 * @author mhein
 *
 */
abstract class AppKitAuthProviderBaseModel extends IcingaBaseModel {

    /**
     * Default parameters for the new provider
     * @var unknown_type
     */
    protected $parameters_default = array(
                                        AppKitIAuthProvider::AUTH_MODE => AppKitIAuthProvider::MODE_DEFAULT
                                    );
    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function  initialize(AgaviContext $context, array $parameters = array()) {
        $parameters = $parameters + $this->parameters_default;

        parent::initialize($context, $parameters);

        $this->initializeProvider();

        $this->log('Auth.Provider: Object (name=%s) initialized', $this->getProviderName(), AgaviLogger::DEBUG);
    }

    /**
     * Method to overwrite explicit provider intialization
     */
    protected function initializeProvider() {}

    /**
     * Shortcut to load an user
     * @param $value
     * @param $dql
     * @return NsmUser
     */
    protected function loadUserByDQL($value, $dql='user_name=?') {
        $users = Doctrine::getTable('NsmUser')->findByDql($dql, array($value));

        if ($users->count() == 1) {
            return $users->getFirst();
        }
    }

    /**
     * If a provider is authoritative for
     * authentification
     * @return boolean
     */
    public function isAuthoritative() {
        return $this->testBoolean(AppKitIAuthProvider::AUTH_AUTHORITATIVE);
    }

    /**
     * If a provider allowed resume other providers to authentificate
     * @return boolean
     */
    public function resumeAuthentification() {
        return $this->testBoolean(AppKitIAuthProvider::AUTH_RESUME);
    }

    /**
     * If we can update existig user profiles
     * @return boolean
     */
    public function canUpdateProfile() {
        return $this->testBoolean(AppKitIAuthProvider::AUTH_UPDATE);
    }

    /**
     * If we can create new user profiles
     * @return boolean
     */
    public function canCreateProfile() {
        return $this->testBoolean(AppKitIAuthProvider::AUTH_CREATE);
    }

    /**
     * Shortcut to test object parameters for real boolean parameters
     * @param boolean $setting_name
     * @return boolean
     */
    public function testBoolean($setting_name) {
        return ($this->getParameter($setting_name, false) !== false) ? true : false;
    }

    /**
     * Test object parameters against binary conditions (flags)
     * @param string $setting_name
     * @param integer $flag
     * @return boolean
     */
    public function testBinary($setting_name, $flag) {
        $test = $this->getParameter($setting_name);

        if ($test && ($test & $flag)>0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the name of a provider
     * @return string
     */
    public function getProviderName() {
        return $this->getParameter('name');
    }

    /**
     * Default groups used by this provider
     * @return array List of groups
     */
    public function getDefaultGroups() {
        $string = $this->getParameter('auth_groups');

        if ($string) {
            return AppKitArrayUtil::trimSplit($string);
        }
    }

    /**
     * Maps all provider user fields to our internal user record by
     * xml configuration
     * @param array $data List of fields matching for NsmUser
     */
    protected function mapUserdata(array $data) {
        $re = array();
        foreach($this->getParameter('auth_map', array()) as $k=>$f) {
            if (array_key_exists($f, $data)) {
                $re[$k] = $data[$f];
            }
        }
        $re['user_authsrc'] = $this->getProviderName();
        return $re;
    }

    /**
     * So providers can guess usernames
     * @return string
     */
    public function determineUsername() {
        return null;
    }
    
    public function getName() {
        return $this->getParameter(AppKitIAuthProvider::AUTH_NAME);
    }

}

class AppKitAuthProviderException extends AppKitException {}
