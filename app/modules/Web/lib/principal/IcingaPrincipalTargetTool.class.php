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


class IcingaPrincipalTargetTool {

    public static function applyApiSecurityPrincipals(/*IcingaApiSearchIdo*/ &$search) {
        $user = AgaviContext::getInstance()->getUser()->getNsmUser();

        $sarr = $user->getTargetValuesArray();
        AppKitLogger::verbose("TargetValuesArray = %s", var_export($sarr, true));
        $models = $user->getTargets(null, true, true);
        $parts = array();
        foreach($models as $model) {
            if ($model->target_type != 'icinga') {
                continue;
            }

            $targetname = $model->get('target_name');

            if (!isset($sarr[$targetname])) {
                continue;
            }

            $to = $model->getTargetObject($targetname);

            if (!self::checkIfTargetAffectsSearch($to,$search,$sarr[$targetname])) {
                continue;
            }

            if (count($sarr[$targetname]) > 0) {
                $parts[] = $to->getMapArray($sarr[$targetname]);
            } else {
                $map = $to->getCustomMap();

                if ($map) {
                    $search->setSearchFilterAppendix($map, IcingaApiConstants::SEARCH_AND);
                }
            }
        }

        if (count($parts) > 0) {
            $query = join(' AND ', $parts);
            $search->setSearchFilterAppendix($query, IcingaApiConstants::SEARCH_AND);

            return true;
        }

        return false;
    }


    static protected function checkIfTargetAffectsSearch(/*IcingaDataPrincipalTarget*/ $target,/*IcingaApiSearchIdo*/ $search,$apiMapping) {
        // check the mapping
        if (empty($apiMapping)) {
            return $target->getCustomMap();
        }

        if ($apiMapping === false) {
            return false;
        }

        $apiFields = array();

        foreach($apiMapping as $ap) {
            $apiFields[] = $target->getApiMappingField(key($ap));
        }

        $columns = $search->getAffectedColumns();
        $isect = array_intersect(array_values($apiFields), $columns);

        return !empty($isect);
    }
}

?>
