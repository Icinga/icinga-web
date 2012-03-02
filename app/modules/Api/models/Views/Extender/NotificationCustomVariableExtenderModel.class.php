<?php

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
                getTargetValues(IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE)
                ->toArray();

        $host_targetVals = $this->user->
                getTargetValues(IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST)
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