<?php
/**
*
* Filtergroup that operates on (Icinga)Doctrine_Query objects and allows to filter them.
*
* @package Icinga_Api
* @category DataStoreModifier
*
**/
class ApiStoreFilterGroup extends GenericStoreFilterGroup {

    /**
    * @see GenericStoreFilter::parse
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public static function parse($filter,$field,$instance = null) {
        return GenericStoreFilterGroup::parse($filter,$field,"ApiStoreFilterGroup");
    }
    /**
    *  Adds this filtergroup and all of it's nested filters to the Query object
    *  @param   IcingaDoctrine_Query    A query object to add the filter
    *
    *  @author   Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly = false) {
        $dql = "";
        $content = "";
        $first = true;

        foreach($this->getSubFilters() as $filter) {
            $filterDQL = $filter->__toDQL($q,true);

            if ($filterDQL) {
                // glue the operator type in front of the filter if it's not the first filter
                $content .= (($first) ? ' ' : ' '.$this->getType())." ".$filterDQL;
                $first = false;
            }
        }

        if (!$content) {
            return "";
        }

        $dql = "(".$content.")";

        // if($dqlOnly) {
        //     return $this->getType()." ".$content;
        // }
        if ($this->getType() == 'OR') {
            $q->orWhere($dql);
        } else {
            $q->andWhere($dql);
        }
    }
}


