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
 * Class handling security information about cronks
 * @author mhein
 * @package IcingaWeb
 * @subpackage Cronks
 * @since 1.8.0
 */
class Cronks_Provider_CronksSecurityModel extends CronksBaseModel {

    /**
     * Cronk uid to work with
     * @var String
     */
    private $cronkuid = null;
    
    /**
     * @var Cronks_Provider_CronksDataModel
     */
    private $cronks = null;
    
    /**
     * Representation of a cronk
     * @var mixed
     */
    private $cronk = null;
    
    /**
     * Principals
     * @var mixed
     */
    private $principals = null;
    
    /**
     * Roleuids
     * @var mixed
     */
    private $role_uids = null;
    
    /**
     * Rolenames
     * @var mixed
     */
    private $role_names = null;
    
    /**
     * Roles struct
     * @var mixed
     */
    private $roles = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        if ($this->getParameter('security_only', false) !== true) {
            $this->cronks = $this->getContext()->getModel('Provider.CronksData', 'Cronks');
        }
        
        if ($this->getParameter('cronkuid', false) !== false) {
            $this->setCronkUid($this->getParameter('cronkuid'));
        }
    }
    
    /**
     * Setter for cronkuid
     * 
     * Resets collected data and prepare for fetching
     * 
     * @param string $cronkuid
     * @throws AppKitModelException
     */
    public function setCronkUid($cronkuid) {
        $this->cronkuid = $cronkuid;
        
        // Reset collected data
        $this->roles = null;
        $this->principals = null;
        $this->role_uids = null;
        $this->role_names = null;
        $this->cronk = null;
        
        if ($this->getParameter('security_only', false) !== true) {
            if (!$this->cronks->hasCronk($cronkuid)) {
                throw new AppKitModelException("Cronk $cronkuid does not exist!");
            }
            $this->cronk = $this->cronks->getCronk($cronkuid);
        }
    }
    
    /**
     * Returns true if the cronk os from xml source
     * @return boolean
     */
    public function isSystem() {
        return ($this->cronk['system'] === true) ?
            true : false;
    }
    
    /**
     * Returns the current cronk uid
     * @return string
     */
    public function getCronkUid() {
        return $this->cronkuid;
    }
    
    /**
     * Return the complete cronk struct
     * @return mixed
     */
    public function getCronk() {
        return $this->cronk;
    }
    
    /**
     * Lazy loading of roles depending to the cronk
     * @return mixed
     */
    public function getRoles() {
        
        if (!is_array($this->roles)) {
            $query = AppKitDoctrineUtil::createQuery()
            ->select('r.role_id, r.role_name, r.role_description')
            ->from('NsmRole r')
            ->innerJoin('r.principal p')
            ->innerJoin('p.cronks c WITH c.cronk_uid=?', $this->getCronkUid());
            
            $res = $query->execute();
            
            $this->principals = array();
            $this->role_uids = array();
            $this->role_names = array();
            
            foreach ($res as $role) {
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
     * Return the principal uids
     * @return mixed
     */
    public function getPrincipals() {
        if (!is_array($this->principals)) {
            $this->getRoles();
        }
        return $this->principals;
    }
    
    /**
     * Return the role uids
     * @return mixed
     */
    public function getRoleUids() {
        if (!is_array($this->role_uids)) {
            $this->getRoles();
        }
        return $this->role_uids;
    }
    
    /**
     * Return role names
     * @return mixed
     */
    public function getRoleNames() {
        if (!is_array($this->role_names)) {
            $this->getRoles();
        }
        return $this->role_names;
    }
    
    /**
     * Return role names as string (comma separated)
     * @return string
     */
    public function getRoleNamesAsString() {
        if ($this->hasDatabaseRoles() === true) {
            return implode(',', $this->getRoleNames());
        }
        return '';
    }
    
    /**
     * Test if roles available from database
     * @return boolean
     */
    public function hasDatabaseRoles() {
        if (is_array($this->getRoleNames()) && count($this->getRoleNames())) {
            return true;
        }
        return false;
    }
    
    /**
     * Try to find the cronk record
     * @return Cronk
     */
    private function getCronkRecord() {
        return Doctrine::getTable('Cronk')
            ->findOneBy('cronk_uid', $this->getCronkUid());
    }
    
    /**
     * Remove all principals from a cronk
     * @param Cronk $cronk
     */
    private function dropGroupPrincipals(Cronk $cronk) {
        $principals = 
        AppKitDoctrineUtil::createQuery()
        ->select('p.principal_id')
        ->from('NsmPrincipal p')
        ->innerJoin('p.cronks c WITH c.cronk_id=?', $cronk->cronk_id)
        ->andWhere('p.principal_type=?', array(NsmPrincipal::TYPE_ROLE))
        ->execute();
        
        $pids=array();
        foreach ($principals as $principal) {
            $pids[] = $principal->principal_id;
        }
        
        if (count($pids)>0) {
            AppKitDoctrineUtil::createQuery()
            ->delete('CronkPrincipalCronk cpc')
            ->andWhere('cpc.cpc_cronk_id=?', array($cronk->cronk_id))
            ->andWhereIn('cpc.cpc_principal_id', $pids)
            ->execute();
        }
        
        $cronk->refresh(true);
    }
    
    /**
     * If we want to work on a system cronk we need a related
     * database record. This is done here ...
     * 
     * @param array $cronk
     */
    private function createSystemCronkDatabaseEntry(array $cronk) {
        
        $root = Doctrine::getTable('NsmUser')->findOneBy('user_name', 'root');
        
        $record = new Cronk();
        $record->cronk_description = 'System cronk credential enty, please ignore!';
        $record->cronk_uid = $cronk['cronkid'];
        $record->cronk_name = $cronk['name'];
        $record->owner = $root;
        $record->cronk_system = true;
        
        $record->save();
        
        return $record;
    }
    
    /**
     * Update roles for the cronk if it's shared
     * or not
     * @param array $new_role_uids
     */
    public function updateRoles(array $new_role_uids) {
        $cronk = $this->getCronkRecord();
        
        // Maybe system cronk for credentials only
        if (!$cronk && $this->isSystem() === true) {
            $cronk = $this->createSystemCronkDatabaseEntry($this->cronk);
        }
        
        // Update if we have error prone records
        if ($cronk instanceof Cronk && $this->isSystem() === true) {
            $cronk->cronk_system = true;
        }
        
        $this->dropGroupPrincipals($cronk);
        
        if (count($new_role_uids)) {
            foreach ($new_role_uids as $role_uid) {
                $role = Doctrine::getTable('NsmRole')->findOneBy('role_id', $role_uid);
                $cronk->principals[] = $role->principal;
            }
        }
        
        // System cronk without principals is rather useless, just drop
        if ($cronk->cronk_system === true && $cronk->principals->count() <= 0) {
            $cronk->delete();
        } else {
            $cronk->save();
        }
    }
}