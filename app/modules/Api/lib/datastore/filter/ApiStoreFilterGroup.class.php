<?php

class ApiStoreFilterGroup extends GenericStoreFilterGroup {
    public static function parse($filter,$field,$instance = null) {
        return GenericStoreFilterGroup::parse($filter,$field,"ApiStoreFilterGroup");
    }
    
    public function __toDQL(IcingaDoctrine_Query $q) { 
        $dql = " (";
        $first = true;
        foreach($this->getSubFilters() as $filter) {
            $filterDQL = $filter->__toDQL($q,true);
            if($filterDQL)
                $dql .= (($first) ? ' ' : ' '.$this->getType())." ".$filterDQL;
            $first = false;
        }
        $dql .= ") ";
        if($this->getType() == 'OR')
            $q->orWhere($dql);
        else 
            $q->andWhere($dql);
    }
}


