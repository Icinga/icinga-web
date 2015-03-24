<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class Api_Views_Extender_MultiLikeExtenderModel extends IcingaBaseModel 
    implements DQLViewExtender {
    
    
    public function extend(IcingaDoctrine_Query $query,array $params) 
    {
        $target = $params["target"]; 
        $column = $params["column"]; 
        $ornull = ( (isset($params["ornull"]) && $params["ornull"] == true) ? true : false );
        $user = $this->getContext()->getUser()->getNsmUser();
        $targetVals = $user->getTargetValues($target,true)->toArray();
        if(empty($targetVals))
            return false;
        $dqlParts = array();
        $dqlValues = array();
        foreach($targetVals as $currentTarget) {
            $dqlParts[] = "$column LIKE '".$currentTarget["tv_val"]."'";
        }
        if ($ornull == true)
            $dqlParts[] = "$column IS NULL";
        $dql = "(".implode(" OR ", $dqlParts).")";
        $query->orWhere($dql);
    }
} 
