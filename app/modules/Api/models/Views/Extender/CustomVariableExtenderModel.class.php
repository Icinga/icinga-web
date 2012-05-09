<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class Api_Views_Extender_CustomVariableExtenderModel extends IcingaBaseModel implements DQLViewExtender {
    /**
     *
     * @var NsmUser
     */
    private $user;

    public function extend(IcingaDoctrine_Query $query,array $params) {
        $target = $params["target"];
        $alias  = $params["alias"];
        $joinType = isset($params["joinType"]) ? $params["joinType"] : "inner";
        $whereAppendix = isset($params["where"]) ? $params["where"] : "";
        $isObject = isset($params["isObject"]);
        $objectTypeClause = $isObject ? " AND $alias.objecttype_id = " : "";
        $this->user = $this->getContext()->getUser()->getNsmUser();
        $aliasAbbr = "cv";
        switch($target) {
            case 'host':
                $aliasAbbr = "h_cv";
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST;
                if($objectTypeClause != "")
                    $objectTypeClause .= "1";
                break;
            case 'service':
                $aliasAbbr = "s_cv";
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE;
                if($objectTypeClause != "")
                    $objectTypeClause .= "2";
                break;
        }
        $targetVals = $this->user->getTargetValues($target)->toArray();
        if(empty($targetVals))
           return;
        if($joinType == "left")
            $query->leftJoin("$alias.customvariables ".$aliasAbbr);
        else
            $query->innerJoin("$alias.customvariables ".$aliasAbbr);

        $keymap = array(
            "cv_name" => "varname",
            "cv_value" => "varvalue"
        );
        foreach($targetVals as $cvKeyValuePair) {
            $query->andWhere(
                "($aliasAbbr.".$keymap[$cvKeyValuePair["tv_key"]]." = '".$cvKeyValuePair["tv_val"]."'
                    $objectTypeClause  ".$whereAppendix.")"
            );
        }
    }
}