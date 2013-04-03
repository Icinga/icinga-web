<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class Api_Views_Extender_NotificationCustomVariableExtenderModel extends IcingaBaseModel implements DQLViewExtender {
    /**
     *
     * @var NsmUser
     */
    private $user;
    private static $applied = false;
    public function extend(IcingaDoctrine_Query $query,array $params) {
        if(self::$applied)
            return true;

        $alias  = $params["alias"];
        $this->user = $this->getContext()->getUser()->getNsmUser();
       
        $svc_targetVals = $this->user->
                getTargetValues(IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE, true)
                ->toArray();

        $host_targetVals = $this->user->
                getTargetValues(IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST, true)
                ->toArray();
        
        if(empty($svc_targetVals) && empty($host_targetvals)) {
            return true;
        }
 
        $query->innerJoin("$alias.customvariables cv");
        $cvPart_svc = $this->createWherePart(2,$svc_targetVals,$alias);
        $cvPart_host = $this->createWherePart(1,$host_targetVals,$alias);


        if($cvPart_svc != "" && $cvPart_host != "") {
            $wherePart = "( $cvPart_svc OR $cvPart_host )";
        }

        $query->addWhere($wherePart);
        self::$applied = true;
    }

    private function createWherePart($objecttype, array $targetVals,$alias) {

        $keymap = array(
            "cv_name" => "varname",
            "cv_value" => "varvalue"
        );

        $chain = false;
        $cvPart = "";
        foreach($targetVals as $cvKeyValuePair) {
            if(!$chain) {
                $cvPart .= "( $alias.objecttype_id = $objecttype AND (";
            } else {
                $cvPart .= " AND ";
            }
            $cvPart .= "cv.".$keymap[$cvKeyValuePair["tv_key"]]." =
                '".$cvKeyValuePair["tv_val"]."'";
            $chain = true;
        }
        if(!empty($targetVals)) {
            $cvPart .= "))";
        }
        return $cvPart;
    }
}
