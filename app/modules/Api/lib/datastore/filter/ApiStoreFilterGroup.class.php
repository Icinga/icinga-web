<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


