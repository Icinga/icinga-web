<?php

class Api_Relation_DataModelModel extends IcingaApiBaseModel {
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }
    
    public function getRelationDataForObjectId($objectId) {
        $data = array ();
        
        $data['object'] = $this->getObjectData($objectId);
        $data['contact'] = $this->getContactDetails($objectId, $data['object']['objecttype_id']);
        $data['customvariable'] = $this->getCustomVariables($objectId);
        $data['hostgroup'] = $this->getHostgroups($objectId);
        $data['servicegroup'] = $this->getServicegroups($objectId);
        
        return $data;
    }
    
    public function getContactDetails($objectId, $objecttypeId) {
        $type = null;
        $out = array ();
        
        if ($objecttypeId == IcingaConstants::TYPE_HOST) {
            $type = 'host';
        } elseif ($objecttypeId == IcingaConstants::TYPE_SERVICE) {
            $type = 'service';
        }
        
        $records = IcingaDoctrine_Query::create()
        ->select('c.contact_id, c.alias as contact_alias, co.name1 as contact_name, c.email_address as contact_email_address, co.object_id as contact_object_id, NULL as contactgroup_name, NULL as contactgroup_object_id')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.'. $type. 's h WITH h.'. $type. '_object_id=?', $objectId)
        ->execute(null, Doctrine::HYDRATE_ARRAY);
        
        if (count($records)) {
            $out = $records;
        }
        
        $records = IcingaDoctrine_Query::create()
        ->select('c.contact_id, c.alias as contact_alias, co.name1 as contact_name, c.email_address as contact_email_address, co.object_id as contact_object_id,  cg.alias as contactgroup_name, cg.contactgroup_object_id as contactgroup_object_id')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.contactgroups cg')
        ->innerJoin('cg.'. $type. 's h WITH h.'. $type. '_object_id = ?', $objectId)
        ->execute(null, Doctrine::HYDRATE_ARRAY);
        
        if (count($records)) {
            $out = array_merge($out, $records);
        }
        
        return $out;
    }
    
    public function getHostContactDetails($objectId) {
        
        $out = array();
        
        $records = IcingaDoctrine_Query::create()
        ->select('c.alias, co.name1 as name1, co.object_id as contact_object_id, NULL as contactgroup, NULL as contactgroup_id')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.hosts h WITH h.host_object_id=?', $objectId)
        ->execute();
        
        foreach ($records as $record) {
            $out[] = array (
                'alias' => $record->alias,
                'email' => $record->email_address,
                'name'  => $record->name1,
                'contact_object_id' => $record->contact_object_id,
                'group' => null,
                'contactgroup_object_id' => null
            );
        }
        
        $records = IcingaDoctrine_Query::create()
        ->select('c.contact_id, c.alias, co.name1 as name1, cg.alias as group, cg.contactgroup_object_id as contactgroup_object_id')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.contactgroups cg')
        ->innerJoin('cg.hosts h WITH h.host_object_id=?', $objectId)
        ->execute();
        
        foreach ($records as $record) {
            $out[] = array (
                'alias' => $record->alias,
                'email' => $record->email_address,
                'name'  => $record->name1,
                'contact_object_id' => $record->object->object_id,
                'group' => $record->group,
                'contactgroup_object_id' => $record->contactgroup_object_id
            );
        }
        
        return $out;
    }
    
    public function getObjectData($objectId) {
        $record = IcingaDoctrine_Query::create()
        ->from('IcingaObjects o')
        ->select('o.object_id, o.objecttype_id, o.instance_id, o.name1, o.name2, o.is_active')
        ->andWhere('o.object_id=?', $objectId)
        ->execute()
        ->getFirst();
        
        if ($record instanceof IcingaObjects) {
            return $record->toArray();
        }
    }
    
    public function getCustomVariables($objectId) {
        $records = IcingaDoctrine_Query::create()
        ->select('c.varname, c.varvalue')
        ->from('IcingaCustomvariables c')
        ->appendCustomvarFilter()
        ->andWhere('c.object_id=?', array($objectId));
        
        return $records->execute(null, Doctrine::HYDRATE_ARRAY);
    }
    
    public function getHostgroups($objectId) {
        $records = IcingaDoctrine_Query::create()
        ->select('hg.alias as alias, o.name1 as name, o.object_id as hostgroup_object_id')
        ->from('IcingaHostgroups hg')
        ->innerJoin('hg.object o')
        ->innerJoin('hg.members m with m.host_object_id=?', $objectId);
        
        return $records->execute(null, Doctrine::HYDRATE_ARRAY);
    }
    
    public function getServicegroups($objectId) {
        $records = IcingaDoctrine_Query::create()
        ->select('sg.alias as alias, o.name1 as name, o.object_id as servicegroup_object_id')
        ->from('IcingaServicegroups sg')
        ->innerJoin('sg.object o')
        ->innerJoin('sg.members m with m.service_object_id = ?', $objectId);
        
        return $records->execute(null, Doctrine::HYDRATE_ARRAY);
    }
}