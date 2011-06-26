<?php
class IcingaDoctrine_Query extends Doctrine_Query {
    public static function create($conn = NULL, $class = NULL) {
         
        return new IcingaDoctrine_Query($conn);
    }
/*
    public function execute($attr,$hyd) {
        ApiDataRequestBaseModel::applySecurityCredentials($this);
        return parent::execute($attr,$hyd);
    }
**/
  
}
?>
