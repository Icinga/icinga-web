<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IcingaSLASummary
 *
 * @author jmosshammer
 */
class IcingaSLASummary extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->hasColumn('object_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('sla_state', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('percentage', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
   }
   
   public function __get($name) {
       if($name != "object" && $name != "host" && $name != "service")
           return parent::__get($name);
       if($name == "object")
           return Doctrine::getTable("IcingaObjects")->findOneBy("object_id", $this->object_id);
       if($name == "host")
           return Doctrine::getTable("IcingaServices")->findOneBy("host_object_id", $this->object_id);
       if($name == "service")
           return Doctrine::getTable("IcingaHosts")->findOneBy("service_object_id", $this->object_id);
       
   }
   
  
}
