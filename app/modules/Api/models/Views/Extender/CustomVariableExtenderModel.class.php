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
        $this->user = $this->getContext()->getUser()->getNsmUser();
        switch($target) {
            case 'host':
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST;
                break;
            case 'service':
                $target = IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE;
                break;
        }
        $targetVals = $this->user->getTargetValues($target)->toArray();
        if(empty($targetVals))
           return;
        $query->innerJoin("$alias.customvariables cv");
        
        foreach($targetVals as $cvKeyValuePair) {
            $query->andWhere(
                "(cv.varname = '".$cvKeyValuePair["tv_key"]."'
                    AND cv.varvalue = '".$cvKeyValuePair["tv_val"]."')"
                
            );
        }
    }
}