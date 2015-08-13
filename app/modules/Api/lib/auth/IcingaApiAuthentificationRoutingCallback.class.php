<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class IcingaApiAuthentificationRoutingCallback extends AgaviRoutingCallback {
    
    const INSUFFICIENT_MSG = 'User has insufficient rights to access the api';
    
    /**
     * @var AppKitSecurityUser
     */
    private $user = null;
    
    public function initialize(AgaviContext $context, array &$route) {
        parent::initialize($context, $route);
        $this->user = $this->context->getUser();
    }
    
    private function checkAuthorisation() {
        
        if (!$this->user->isAuthenticated()) {
            return false;
        }
        
        if (!$this->user->hasCredential("appkit.api.access") && !$this->user->hasCredential("icinga.user")) {
            return false;
        }
        
        return true;
    }
    
    public function onMatched(array &$parameters, AgaviExecutionContainer $container) {
        $validation = $container->getValidationManager();
        
        $errors = array ();
        
        if (isset($parameters['authkey'])) {
            $container->setAttribute('flag', true, 'org.icinga.api.auth');
            try {
                $this->user->doAuthKeyLogin($parameters['authkey']);
            } catch (AgaviSecurityException $e) {
                $errors[] = 'Log in failed by authkey';
            }
        }
        

        
        if ($this->checkAuthorisation() == false) {
            $errors[] = self::INSUFFICIENT_MSG;
        }
        
        if (count($errors)) {
            $container->setAttributeByRef('errors', $errors, 'org.icinga.api.auth');
            $container->setAttribute('success', false, 'org.icinga.api.auth');
            return false;
        }
        
        $container->setAttribute('success', true, 'org.icinga.api.auth');
        return true;
    }
    
    public function onNotMatched(AgaviExecutionContainer $container) {
        $garbage = array();
        return $this->onMatched($garbage, $container);
    }
}
