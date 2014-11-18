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

class Cronks_Provider_CronkSecurityAction extends CronksBaseAction {
    
    /**
     * @var Cronks_Provider_CronksDataModel
     */
    private $cronks = null;
    
    /**
     * @var Cronks_Provider_CronksSecurityModel
     */
    private $security = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        $this->cronks = $this->getContext()->getModel('Provider.CronksData', 'Cronks');
    }
    
    public function getDefaultViewName() {
        return 'Success';
    }
    
    public function executeRead(AgaviParameterHolder $rd) {
        return $this->executeWrite($rd);
    }
    
    public function executeWrite(AgaviParameterHolder $rd) {
        
        $success = false;
        $errors = array();
        
        try {
            $this->security = $this->getContext()->getModel('Provider.CronksSecurity', 'Cronks');
            $this->security->setCronkUid($rd->getParameter('cronkuid'));
            
            $xaction = $rd->getParameter('xaction', 'read');
            
            if ($xaction === "read") {
                $success = true;
            } elseif ($xaction === "write") {
                $data = json_decode($rd->getParameter('j'));
                $this->security->updateRoles($data->roles);
                $success = true;
            }
            
            $this->setAttribute('cronk', $this->security->getCronk());
            $this->setAttribute('roles', $this->security->getRoles());
            $this->setAttribute('role_uids', $this->security->getRoleUids());
            $this->setAttribute('principals', $this->security->getPrincipals());
        
        } catch(AppKitModelException $e) {
            $success = false;
            $errors[] = $e->getMessage();
        }
        
        $this->setAttribute('success', $success);
        $this->setAttribute('errors', $errors);
        
        
        return $this->getDefaultViewName();
    }
    
    public function isSecure() {
        return true;
    }
    
    public function getCredentials() {
        return array('icinga.cronk.admin');
    }
    
    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }
}