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
