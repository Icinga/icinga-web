<?php

class Api_Relation_DataModelModel extends IcingaApiBaseModel {
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }
    
    public function getRelationDataForObjectId($objectId) {
        $data = array ();
        
        $data['contactgroup'] = $this->getContactDetails($objectId);
        $data['object'] = $this->getObjectData($objectId);
        
        return $data;
    }
    
    public function getContactDetails($objectId) {
        $record = IcingaDoctrine_Query::create()
        ->select('c.contact_id, c.alias, co.name1, h.display_name')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.hosts h WITH h.host_object_id=?', $objectId)
        ->execute();
        
        $record = IcingaDoctrine_Query::create()
        ->select('c.contact_id, c.alias, co.name1, h.display_name')
        ->from('IcingaContacts c')
        ->innerJoin('c.object co')
        ->innerJoin('c.contactgroups cg')
        ->innerJoin('cg.hosts h')
        ->execute();
        
        die("DY");
    }
    
    public function getObjectData($objectId) {
        $record = IcingaDoctrine_Query::create()
        ->from('IcingaObjects o')
        ->select('o.object_id, o.objecttype_id, o.name1, o.name2, o.is_active')
        ->andWhere('o.object_id=?', $objectId)
        ->execute(array($objectId))
        ->getFirst();
        
        if ($record instanceof IcingaObjects) {
            return $record->toArray();
        }
    }
}