<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
 * IDO Implementation of filtergroup
 *
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 *
 */
class IcingaApiSearchFilterGroupIdo extends IcingaApiSearchFilterGroup {
    public function createQueryStatement() {
        $statement = "";
        foreach($this as $filter) {
            if ($statement) {
                $statement .= " ".$this->getType()." ";    // Add chain type (AND/OR)
            }

            $statement .= $filter->createQueryStatement();
        }

        return "(".$statement.")";
    }

    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly=false) {
        $dql = "";
        $content = "";
        $first = true;

        foreach($this as $filter) {
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

        if ($dqlOnly) {
            return $dql;
        }

        if ($this->getType() == 'AND') {
            $q->andWhere($dql);
        } else {
            $q->orWhere($dql);
        }
    }
}

?>
