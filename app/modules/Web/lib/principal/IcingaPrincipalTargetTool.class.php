<?php

class IcingaPrincipalTargetTool {

    public static function applyApiSecurityPrincipals(/*IcingaApiSearchIdo*/ &$search) {
        $user = AgaviContext::getInstance()->getUser()->getNsmUser();

        $sarr = $user->getTargetValuesArray();
        $models = $user->getTargets();
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
                foreach($sarr[$targetname] as $vdata) {
                    $parts[] = $to->getMapArray($vdata);
                }
            } else {
                $map = $to->getCustomMap();

                if ($map) {
                    $search->setSearchFilterAppendix($map, IcingaApiConstants::SEARCH_AND);
                }
            }
        }

        if (count($parts) > 0) {
            $query = join(' OR ', $parts);
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
