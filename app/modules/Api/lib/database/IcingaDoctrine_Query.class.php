<?php
class IcingaDoctrine_Query extends Doctrine_Query {
    public static function create($conn = NULL, $class = NULL) {
         
        return new IcingaDoctrine_Query($conn);
    }
/*
    public function execute($attr,$hyd) {
        ApiDataRequestBaseModel::applySecurityPrincipals($this);
        return parent::execute($attr,$hyd);
    }
**/
    public function toGroup()   {
        $where = $this->_dqlParts['where'];
        if (count($where) > 0) {
          array_splice($where, count($where) - 1, 0, '(');
          $this->_dqlParts['where'] = $where;
        }
 
        return $this;
    }
 
    public function endGroup()  {
        $where = $this->_dqlParts['where'];
        if (count($where) > 0) {
          $where[] = ')';
          $this->_dqlParts['where'] = $where;
        }  
 
        return $this;
    }
}
?>
