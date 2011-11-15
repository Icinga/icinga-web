<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SlaProvider
 *
 * @author jmosshammer
 */
class Api_SLAProviderAction extends IcingaApiBaseAction {
    
    public function executeRead(AgaviRequestDataHolder $rd) {

        $filter = $this->getContext()->getModel("SLA.SLAFilter","Api");
        $params = $rd->getParameters();

        foreach($params as $name=>$value) {
            switch($name) {
                case 'starttime':
                    $filter->setStartTime($value);
                    break;
                case 'endtime':
                    $filter->setEndTime($value);
                    break;
                case 'hostsonly':
                    $filter->useOnlyHosts();
                    break;
                case 'timespan':
                    $filter->setTimespan($value);
                    break;
                case 'servicesonly':
                    $filter->useOnlyServices();
                    break;
                case 'instanceIds':
                    $filter->setInstanceIds($value);
                    break;
                case 'hostnames':
                    $filter->setHostnamePattern($value);
                    break;
                case 'servicenames':
                    $filter->setServicenamePattern($value);
                    break;
                case 'hostgroups':
                    $filter->setHostgroupnames($value);
                    break;
                case 'servicegroups':
                    $filter->setServicegroupnames($value);
                    break;
                case 'ids':
                    $filter->setObjectId($value);
                    break;

            }
                
        }
        $this->addPrincipalsToFilter($filter);
        $stmt = IcingaSlahistoryTable::getSummary(null, $filter);
        $this->setAttribute("result", $stmt);
        return "Success";
    }
    
    private function addPrincipalsToFilter(&$filter) {
        $user = AgaviContext::getInstance()->getUser()->getNsmUser();

        $sarr = $user->getTargetValuesArray();

        foreach($sarr as $principal=>$values) {
          
            switch($principal) {
                case 'IcingaHostgroup':
                    $filter->setHostgroupNames($values[0]);
                    break;
                case 'IcingaServicegroup':
                    $filter->setServicegroupNames($values[0]);
                    break;
                case 'IcingaHostCustomVariablePair':
                    foreach($values as $cv) {
                        $filter->addHostCV($cv["cv_name"],$cv["cv_value"]);
                    }
                    break;
                case 'IcingaServiceCustomVariablePair':
                    foreach($values as $cv) {
                        $filter->addServiceCV($cv["cv_name"],$cv["cv_value"]);
                    }
                    break;
            }
        }
    }
    
    public function isSecure() {
        return true;
    }
    public function getCredentials() {
        return "icinga.user";
    }
}

?>
