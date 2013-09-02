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


class Api_Views_Extender_CustomVariableExtenderModel extends IcingaBaseModel 
    implements DQLViewExtender {
    
    /**
     *
     * @var NsmUser
     */
    private $user;
    private static $impl = 0;

    public function extend(IcingaDoctrine_Query $query,array $params) {
        // target, host or service
        $target = $params["target"];
        // alias for the table to join from
        $alias  = $params["alias"];

        $this->user = $this->getContext()->getUser()->getNsmUser();
        $aliasAbbr = "cv";
        $impl = ++Api_Views_Extender_CustomVariableExtenderModel::$impl;
        switch($target) {
            case 'host':
                $aliasAbbr = "h_cv_$impl";
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST;
                break;
            case 'service':
                $aliasAbbr = "s_cv_$impl";
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE;
                break;
        }
        $targetVals = $this->user->getTargetValues($target,true)->toArray();
        if(empty($targetVals))
           return;

        $keymap = array(
            "cv_name" => "varname",
            "cv_value" => "varvalue"
        );

        $pairs = array();

        $CVcredentials = array();

        // build correct array with the data we need
        foreach($targetVals as $targetData) {
            if(isset($targetData["tv_pt_id"]) and isset($targetData["tv_key"])) {
                $tvid = $targetData["tv_pt_id"];
                if($targetData["tv_key"] == "cv_name")
                    $CVcredentials[$tvid]["name"] = $targetData["tv_val"];
                else if($targetData["tv_key"] == "cv_value")
                    $CVcredentials[$tvid]["value"] = $targetData["tv_val"];
            }
        }

        // make a join for each CV permission
        $query->leftJoin("$alias.customvariables ".$aliasAbbr);

        // now we build the sql data
        foreach($CVcredentials as $tvid => $cvdata) {
            // skip incomplete sets
            if(!isset($cvdata["name"]) || !isset($cvdata["value"]))
                continue;


            $pairs[] = "($aliasAbbr.varname LIKE '".$cvdata["name"]."' and $aliasAbbr.varvalue LIKE '".$cvdata["value"]."')";
        }
        if ($target == IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE) {
            $pairs[] = $params["alias"].'.service_object_id IS NULL';
        }
        $query->orWhere(join(" OR ", $pairs));
    }
}
