<?php

class Api_Views_Extender_CustomVariableExtenderModel extends IcingaBaseModel implements DQLViewExtender {
    /**
     *
     * @var NsmUser
     */
    private $user;

    public function extend(IcingaDoctrine_Query $query,array $params) {
        $target = $params["target"];
        $alias  = $params["alias"];
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
        $query->innerJoin("$alias.customvariables ".$aliasAbbr);
        $keymap = array(
            "cv_name" => "varname",
            "cv_value" => "varvalue"
        );
        foreach($targetVals as $cvKeyValuePair) {
            $query->andWhere(
                "($aliasAbbr.".$keymap[$cvKeyValuePair["tv_key"]]." = '".$cvKeyValuePair["tv_val"]."'
                    $objectTypeClause )"
            );
        }
    }
}