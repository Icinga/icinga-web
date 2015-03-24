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

/**
 * Interface to the world to handle security and permissions for
 * cronk categories
 * 
 * @package IcingaWeb
 * @subpackage Cronks
 * @author mhein
 * @since 1.8.0
 */
class Cronks_Provider_CategorySecurityAction extends CronksBaseAction {
    
    /**
     * @var Cronks_Provider_CronkCategorySecurityModel
     */
    private $security = null;
    
    /**
     * (non-PHPdoc)
     * @see AgaviAction::initialize()
     */
    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
    }
    
    /**
     * Lazy model getter
     * @return Cronks_Provider_CronkCategorySecurityModel
     */
    private function getCategorySecurityModel() {
        if ($this->security === null) {
            $this->security = $this->getContext()
            ->getModel('Provider.CronkCategorySecurity' ,'Cronks');
        }
        
        return $this->security;
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
            $this->getCategorySecurityModel()
                ->setCategoryUid($rd->getParameter('catid'));
            
            $xaction = $rd->getParameter('xaction', 'read');
            
            if ($xaction === 'read') {
                $success = true;
            } elseif ($xaction === 'write') {
                $params = json_decode($rd->getParameter('j'));
                $this->getCategorySecurityModel()->updateRoles($params->roles);
                $success = true;
            }
            
            $this->setAttribute('category', $this->getCategorySecurityModel()
                ->getCategory());
            
            $this->setAttribute('roles', $this->getCategorySecurityModel()
                    ->getRoles());
            
            $this->setAttribute('role_uids', $this->getCategorySecurityModel()
                    ->getRoleUids());
            
            $this->setAttribute('principals', $this->getCategorySecurityModel()
                    ->getPrincipals());
            
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