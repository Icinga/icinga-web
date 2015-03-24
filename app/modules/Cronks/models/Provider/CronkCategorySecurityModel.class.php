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
 * Model to deal with category permissions
 * @author mhein
 * @package IcingaWeb
 * @subpackage Cronks
 * @since 1.8.0
 */
class Cronks_Provider_CronkCategorySecurityModel extends CronksBaseModel {
    
    /**
     * Cat identified
     * @var stringt
     */
    private $category_uid = null;
    
    /**
     * CategoryData model
     * @var Cronks_Provider_CronkCategoryDataModel
     */
    private $category_model = null;
    
    /**
     * Category structure
     * @var array
     */
    private $category = array();
    
    /**
     * Array of roles
     * @var array
     */
    private $roles = null;
    
    /**
     * Array of principals
     * @var array
     */
    private $principals = null;
    
    /**
     * Array of role uids
     * @var array
     */
    private $role_uids = null;
    
    /**
     * Array of role names
     * @var array
     */
    private $role_names = null;
    
    /**
     * @param AgaviContext $context
     * @param array $parameters
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }
    
    /**
     * Lazy model getter for Provider.Category
     * @return Cronks_Provider_CronkCategoryDataModel
     */
    private function getCategoryModel() {
        if ($this->category_model === null) {
            $this->category_model = $this->getContext()
            ->getModel('Provider.CronkCategoryData', 'Cronks');
        }
        
        return $this->category_model;
    }
    
    /**
     * Setter for category_id
     * @param string $category_uid
     */
    public function setCategoryUid($category_uid) {
        $this->category_uid = $category_uid;
        $this->category = $this->getCategoryModel()->getCategory($category_uid); 
        
        // Reset structures
        $this->roles = null;
        $this->role_names = null;
        $this->role_uids = null;
        $this->principals = null;
    }
    
    /**
     * Getter for category_id
     * @return string
     */
    public function getCategoryUid() {
        return $this->category_uid;
    }
    
    /**
     * Return the category structure
     * @return array
     */
    public function getCategory() {
        return $this->category;
    }
    
    /**
     * Lazy loading of roles depending to the cronk
     * @return mixed
     */
    public function getRoles() {
        if ($this->roles === null) {
            $roles = AppKitDoctrineUtil::createQuery()
            ->select('r.role_id, r.role_name, r.role_description')
            ->from('NsmRole r')
            ->innerJoin('r.principal p')
            ->innerJoin('p.categories c WITH c.cc_uid=?', $this->getCategoryUid())
            ->execute();
            
            $this->roles = array();
            $this->role_names = array();
            $this->role_uids = array();
            $this->principals = array();
            
            foreach ($roles as $role) {
                
                $this->roles[] = array(
                    'role_name'         => $role->role_name,
                    'role_id'           => $role->role_id,
                    'role_description'  => $role->role_description,
                    'principal_id'      => $role->principal->principal_id
                );
                
                // Fill principals
                $this->principals[] = $role->principal->principal_id;
                
                // Fill roles_uids
                $this->role_uids[] = $role->role_id;
                
                // Fill role names
                $this->role_names[] = $role->role_name;
            }
        }
        
        return $this->roles;
    }
    
    /**
     * Checks for data and refresh if needed
     * @param mixed $value
     */
    private function checkRefresh($value) {
        if ($value===null) {
            $this->getRoles();
        }
    }
    
    /**
     * Return principals
     * @return array
     */
    public function getPrincipals() {
        $this->checkRefresh($this->principals);
        return $this->principals;
    }
    
    /**
     * Return all uids from roles as array
     * @return array
     */
    public function getRoleUids() {
        $this->checkRefresh($this->role_uids);
        return $this->role_uids;
    }
    
    /**
     * Return role names as array
     * @return array
     */
    public function getRoleNames() {
        $this->checkRefresh($this->role_names);
        return $this->role_names;
    }
    
    /**
     * Return role names as string, comma separated
     * @return string
     */
    public function getRoleNamesAsString() {
        return implode(',', $this->getRoleNames());
    }
    
    /**
     * Try to find the corresponding record from database
     * @return CronkCategory
     */
    private function getCategoryRecord() {
        return Doctrine::getTable('CronkCategory')
            ->findOneBy('cc_uid', $this->getCategoryUid());
    }
    
    /**
     * Drop existing permissions from category
     * @param CronkCategory $category
     */
    private function dropGroupPrincipals(CronkCategory $category) {
        $principals = AppKitDoctrineUtil::createQuery()
        ->select('p.principal_id')
        ->from('NsmPrincipal p')
        ->innerJoin('p.categories c WITH c.cc_id = ?', array($category->cc_id))
        ->andWhere('p.principal_type=?', array(NsmPrincipal::TYPE_ROLE))
        ->execute();
        
        if ($principals->count()) {
            $in=array();
            
            foreach ($principals as $principal) {
                $in[] = $principal->principal_id;
            }
            
            $delete = AppKitDoctrineUtil::createQuery()
            ->delete('CronkPrincipalCategory c')
            ->andWhere('c.category_id=?', array($category->cc_id))
            ->andWhereIn('c.principal_id', $in)
            ->execute();
        }
        
        $category->refresh(true);
    }
    
    /**
     * Create a corresponding system cronk record if
     * catgory was defined by xml
     */
    private function createSystemDummyCategory() {
        $category = new CronkCategory();
        $category->cc_system = true;
        $category->cc_uid= $this->category['catid'];
        $category->cc_name = $this->category['title'];
        $category->cc_visible = true;
        $category->save();
        return $category;
    }
    
    /**
     * Update the permissions for category by roles
     * @param array $roles
     * @throws AppKitModelException
     */
    public function updateRoles(array $roles) {
        
        if (!$this->getContext()->getUser()->hasCredential('icinga.cronk.category.admin')) {
            throw new AppKitModelException('Insuffcent credentials');
        }
        
        $category = $this->getCategoryRecord();
        
        if (!$category && $this->category['system'] === true) {
            $category = $this->createSystemDummyCategory();
        } else {
            $this->dropGroupPrincipals($category);
        }
        
        if (count($roles)) {
            foreach ($roles as $role_uid) {
                $role = Doctrine::getTable('NsmRole')->findOneBy('role_id', $role_uid);
                if ($role) {
                    $category->principals[] = $role->principal;
                }
            }
            $category->save();
        }
        
        if ($category->cc_system===true && $category->principals->count()<=0) {
            $category->delete();
        }
    }
}