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
 * Model to cache accessable object id's and stores the separately until
 * the icinga object base has changed
 * 
 * @package IcingaWeb
 * @subpackage AppKit
 * @author mhein
 * @since 1.8.0
 */
class AppKit_Credential_AggregatorModel extends AppKitBaseModel
    implements AgaviISingletonModel {
    
    /**
     * @var String Session key for the oids itself
     */
    const SESSION_KEY_OID = 'credential.oids';
    
    /**
     * @var String Session key for the count
     */
    const SESSION_KEY_COUNT = 'credential.count';
    
    /**
     * @var String Session key the database revision
     */
    const SESSION_KEY_REV = 'credential.revision';
    
    /**
     * @var AppKitSecurityUser
     */
    private $user = null;
    
    /**
     * @var ReflectionObject
     */
    private $ref = null;
    
    /**
     * Object ids belonging to the current user
     * @var array()
     */
    private $object_ids = array();
    
    /**
     * Initializing method, needed by agavi to run the model
     * 
     * @param AgaviContext $context
     * @param array $parameters
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->user = $this->getContext()->getUser();
        $this->ref = new ReflectionObject($this);
    }
    
    /**
     * Checks if the database revision matches to our s128ession
     * @return boolean
     */
    public function dataIsDeprecated() {
        $storage = $this->getContext()->getStorage();
        $dbrev = $storage->read(self::SESSION_KEY_REV);
        return !$this->matchActualRevision($dbrev);
    }
    
    /**
     * Checks if the session is valid
     * @return boolean
     */
    public function validCache() {
        $storage = $this->getContext()->getStorage();
        $dbrev = $storage->read(self::SESSION_KEY_REV);
        $count = $storage->read(self::SESSION_KEY_COUNT);
        return ($dbrev && $count) ? true : false;
    }
    
    /**
     * Writes our oids to session
     */
    public function writeCache() {
        $storage = $this->getContext()->getStorage();

        $dbrev = $this->getDatabaseRevision();
        $count = $this->getCount();
        $oids = json_encode($this->object_ids);
        
        $storage->write(self::SESSION_KEY_COUNT, $count);
        $storage->write(self::SESSION_KEY_OID, $oids);
        $storage->write(self::SESSION_KEY_REV, $dbrev);
    }
    
    /**
     * Reads oids from session and fill our object
     */
    public function readCache() {
        $storage = $this->getContext()->getStorage();
        $oids = $storage->read(self::SESSION_KEY_OID);
        $this->object_ids = (array)json_decode($oids);
        
        $username = $this->user->getNsmUser()->user_name;
        
        AppKitLogger::verbose('Credentials (%s): Read cache, %d objects', 
                $username, count($this->object_ids));
    }
    
    /**
     * Test if the current user is configured to collect oids
     * 
     * @return boolean
     */
    public function canCollect() {
        try {
            if ($this->user->isAuthenticated() === true) {
                if ($this->user->getNsmUser() instanceof NsmUser) {
                    return true;
                }
            }
        } catch (AppKitDoctrineException $e) {
            // PASS
        }
        
        return false;
    }
    
    /**
     * External interface to get groups. Decide what to do if
     * collecting from database or from session
     * 
     * @return array
     */
    public function getObjectIds() {
        
        if ($this->canCollect() === false) {
            return null;
        }
        
        if (count($this->object_ids) === 0) {
            if ($this->dataIsDeprecated() === true || $this->validCache() === false) {
                $this->collectAccessibleObjects();
                $this->writeCache();
            } elseif ($this->validCache() === true) {
                $this->readCache();
            }
        }
        
        return $this->object_ids;
    }
    
    /**
     * Return the count of all unique oids
     * @return integer
     */
    public function getCount() {
        return count($this->object_ids);
    }
    
    /**
     * Collect all from oids from the database based on credential
     * configuration for the current user
     * 
     * @return boolean Success or not
     */
    private function collectAccessibleObjects() {
        
        if ($this->canCollect() === false) {
            return false;
        }
        
        $user_name = $this->user->getNsmUser()->user_name;
        
        $time_start = microtime(true);
        
        $targets = $this->user->getNsmUser()->getTargetValuesArray();
        
        $count = 0;
        $hasCredentials = false;
        $this->context->getModel("DBALMetaManager","Api")->switchIcingaDatabase("icinga");
        foreach ($targets as $name=>$target) {
            if (count($target)>0 || $name === 'IcingaContactgroup') {
                
                $count ++;
                
                AppKitLogger::verbose('Credentials (%s): Fetching OIDs for %s', 
                        $user_name, $name);
                
                $this->dispatchCollection($name, $target);
                $hasCredentials = true;
            }
        }
        if(!$hasCredentials) {
            $this->object_ids = array("-1" => true);
        }
        $time_duration = microtime(true) - $time_start;
        
        AppKitLogger::verbose('Credentials (%s): Duration for %d types (%d oids): %.04fs',
                $user_name, $count, count($this->object_ids), 
                $time_duration);
        
        return true;
    }
    
    /**
     * Reset the object (forces to recollect from database)
     */
    public function reset() {
        $this->object_ids = array();
    }
    
    /**
     * Tests a database revision agains an argument
     * @param string $rev
     * @return boolean
     */
    private function matchActualRevision($rev) {
        $test = $this->getDatabaseRevision();
        return ($test===$rev) ? true : false; 
    }
    
    /**
     * Creates a md5 hash based on ido start data and first and last ids
     * @return string md5 hash
     */
    private function getDatabaseRevision() {
        $ts_array = array();
        
        $status_query = IcingaDoctrine_Query::create()
        ->select('p.instance_id, p.program_start_time')
        ->from('IcingaProgramstatus p')
        ->limit(255);
        
        $status_collection = $status_query->execute();
        foreach ($status_collection as $status) {
            $ts_array[$status->instance_id] = $status->program_start_time;
        }
        
        $id_query = IcingaDoctrine_Query::create()
        ->select('MIN(o.object_id) as minid, MAX(o.object_id) as maxid')
        ->from('IcingaObjects o')
        ->limit(1);
        
        $ids = $id_query->execute()->getFirst();
        
        $targets = $this->user->getNsmUser()->getTargetValuesArray();
        $target_id = md5(json_encode($targets));
        
        $id_array = array(
            json_encode($ts_array),
            $ids->minid,
            $ids->maxid,
            $target_id
        );
        
        return md5(implode('-', $id_array));
    }
    
    /**
     * Dispatcher for the Targets. Call the function which is responsible
     * to get the data from database
     * 
     * @param string $name
     * @param array $target
     */
    private function dispatchCollection($name, array $target) {
        $cb = array(&$this, 'get'. $name. 'Objects');
        if (is_callable($cb)) {
            call_user_func($cb, $target);
        } else {
            $this->getContext()->getLoggerManager()
                ->log("Could not fetch objects for credential: $name", AgaviLogger::ERROR);
        }
    }
    
    /**
     * 
     * Add all object ids found in a Doctrine_Collecton to our object
     * 
     * @param Doctrine_Collection $collection
     * @param array $fields Which properties of the object are object ids
     */
    private function addCollectionToObjectIds(Doctrine_Collection $collection, array $fields) {
        foreach ($collection as $item) {
            AppKitLogger::verbose("Adding %s to collection",$item);
            foreach ($fields as $field) {
                $this->object_ids[$item->$field] = true;
            }
        }
    }
    
    /**
     * Unified function to work for object ids in IcingaObjects table
     * 
     * @param array $values Passthrough from user targets
     * @param string $value_field Array key used for bind values
     * @param integer $objecttype_id Object type id for IcingaObjects
     * @param string $query_field For what to query in IcingaObjects
     */
    private function getSimpleObjects(array $values, $value_field, $objecttype_id, $query_field) {
        $query = IcingaDoctrine_Query::create()
        ->select('o.object_id')
        ->from('IcingaObjects o')
        ->where('o.objecttype_id=?', array($objecttype_id));
        
        $binds = array();
        foreach ($values as $value) {
            $binds[] = $value[$value_field];
        }
        
        $query->andWhere(implode(' OR ', 
            array_fill(0, count($binds), 'o.'. $query_field. ' LIKE ?')), $binds);
        
        $collection = $query->execute();
        
        $this->addCollectionToObjectIds($collection, array('object_id'));
    }
    
    /**
     * TargetFunction for single service permission
     * 
     * @param array $values
     */
    private function getIcingaServiceObjects(array $values) {
        $this->getSimpleObjects($values, 'value', 2, 'name2');
    }
    
    /**
     * TargetFunction for single host permissions
     * 
     * @param array $values
     */
    private function getIcingaHostObjects(array $values) {
        $this->getSimpleObjects($values, 'value', 1, 'name1');
    }
    
    /**
     * TargetFunction for hostgroups
     * 
     * @param array $values
     */
    private function getIcingaHostgroupObjects(array $values) {
        $query = IcingaDoctrine_Query::create()
        ->select('s.service_id, s.service_object_id, s.host_object_id')
        ->from('IcingaServices s')
        ->leftJoin('s.host h')
        ->leftJoin('h.object ho')
        ->leftJoin('h.hostgroups hg')
        ->leftJoin('hg.object hgo');
        
        $binds = array();
        foreach ($values as $value) {
            $binds[] = $value['hostgroup'];
        }
        
        $query->andWhere(implode(' OR ', 
            array_fill(0, count($binds), 'hgo.name1 LIKE ?')), $binds);
        
        $collection = $query->execute();
        
        $this->addCollectionToObjectIds($collection, array(
            'service_object_id', 'host_object_id'
        ));
    }
    
    private function getIcingaServicegroupObjects(array $values) {
        $query = IcingaDoctrine_Query::create()
        ->select('s.service_id, s.service_object_id, s.host_object_id')
        ->from('IcingaServices s')
        ->leftJoin('s.servicegroups sg')
        ->leftJoin('sg.object sgo');
        
        $binds = array();
        foreach ($values as $value) {
            $binds[] = $value['servicegroup'];
        }
        
        $query->andWhere(implode(' OR ', 
            array_fill(0, count($binds), 'sgo.name1 LIKE ?')), $binds);
        
        $collection = $query->execute();
        
        $this->addCollectionToObjectIds($collection, array(
            'service_object_id', 'host_object_id'
        ));
    }
    
    /**
     * Unified function for querying for customvariables
     * 
     * @param integer $objecttype_id
     * @param array $values Passthrough from user target
     */
    private function collectCustomVariableObjectIds($objecttype_id, array $values) {
        $query = IcingaDoctrine_Query::create()
        ->select('o.object_id')
        ->from('IcingaObjects o')
        ->innerJoin('o.customvariables cv')
        ->andWhere('o.objecttype_id=?', array($objecttype_id));
        
        $bindings = array();
        $parts = array();
        
        foreach ($values as $value) {
            $bindings[] = $value['cv_name'];
            $bindings[] = $value['cv_value'];
            $parts[] = '(cv.varname=? and cv.varvalue=?)';
        }
        
        $query->andWhere(implode(' OR ', $parts), $bindings);
        
        $collection = $query->execute();
        
        $this->addCollectionToObjectIds($collection, array('object_id'));
    }
    
    /**
     * Target function for host custom variable permissions
     * 
     * @param array $values
     */
    private function getIcingaHostCustomVariablePairObjects(array $values) {
        $this->collectCustomVariableObjectIds(1, $values);
    }
    
    /**
     * TargetFunction for service custom variable permissions
     * 
     * @param array $values
     */
    private function getIcingaServiceCustomVariablePairObjects(array $values) {
        $this->collectCustomVariableObjectIds(2, $values);
    }
    
    /**
     * TargetFunction for classic contactgroup permissions´
     * 
     * @param array $values
     */
    private function getIcingaContactgroupObjects(array $values) {
        
        $contact_name = $this->user->getNsmUser()->user_name;
        AppKitLogger::verbose("Colletion contactgroup oids for %s",$contact_name);
        $contacts_query = IcingaDoctrine_Query::create()
        ->select('c.contact_id')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co WITH co.name1=?', $contact_name);
        
        $contacts = $contacts_query->execute();
        AppKitLogger::verbose("Contactquery: %s",$contacts_query->getSqlQuery());
        AppKitLogger::verbose("Found %s contacts with this name (in different instances)",$contacts->count());
        foreach($contacts as $contact) {
            
            $this->addCollectionToObjectIds($contact->services, array('host_object_id', 'service_object_id'));
            $this->addCollectionToObjectIds($contact->hosts, array('host_object_id'));
            
            $group_query = IcingaDoctrine_Query::create()
            ->select('cg.contactgroup_id')
            ->from('IcingaContactgroups cg')
            ->innerJoin('cg.members m WITH m.contact_object_id=?', $contact->contact_object_id);
            
            $groups = $group_query->execute();
            
            foreach ($groups as $group) {
                
                $this->addCollectionToObjectIds($group->services, array('host_object_id', 'service_object_id'));
                $this->addCollectionToObjectIds($group->hosts, array('host_object_id'));
            }
        }
        
    }
}