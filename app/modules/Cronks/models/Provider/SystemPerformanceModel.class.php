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


class Cronks_Provider_SystemPerformanceModel extends CronksBaseModel {
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }
    
    private function checkObjectType($type, $throw=true) {
        if ($type === IcingaConstants::TYPE_HOST || $type === IcingaConstants::TYPE_SERVICE) {
            return true;
        } elseif ($throw===true) {
            throw new AppKitModelException('Type is not TYPE_HOST or TYPE_SERVICE');
        }
        
        return false;
    }
    
    public function getSummaryCounts() {
        // load view
        $view = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
            "view" => "TARGET_SUMMARY_COUNTS",
            "connection" => 'icinga'
        ));
        
        // get the result directly - the viewManager would normalize them
        $out = $view->getResult();
        $out = $out[0];
        return $out;
    }
    
    public function getSummaryStatusValues($type, $field='latency') {
        $this->checkObjectType($type);
        
        $table = null;
        $joinTable = null;
        $prefix = null;
        
        if ($type===IcingaConstants::TYPE_HOST) {
            $table = 'IcingaHoststatus';
            $joinTable = 'host';
            $prefix = 'host';
        } else {
            $table = 'IcingaServicestatus';
            $joinTable = 'service'; 
            $prefix = 'service';
        }
        
        $data = IcingaDoctrine_Query::create()
        ->from($table. ' '. $prefix)
        ->select(sprintf($prefix.'.active_checks_enabled, COUNT(%1$s) as count, SUM(%1$s) as sum,MAX(%1$s) as max,MIN(%1$s) as min',$field))
        ->having($prefix.'.active_checks_enabled = ?','1')
        ->innerJoin($prefix.".".$joinTable)
        ->disableAutoIdentifierFields(true)
        ->execute(array(), Doctrine::HYDRATE_SCALAR);
               
        if (is_array($data) && count($data) === 1) {
            $data = $data[0];

            $result[$prefix."_".$field."_min"] = sprintf('%.3f',$data[$prefix."_min"]);
            $result[$prefix."_".$field."_avg"] = sprintf('%.3f',$data[$prefix."_sum"]/$data[$prefix."_count"]);
            $result[$prefix."_".$field."_max"] = sprintf('%.3f',$data[$prefix."_max"]);
            return $result;
        } else if (is_array($data) && count($data) === 0) {
            $result[$prefix."_".$field."_min"] = "--";
            $result[$prefix."_".$field."_avg"] = "--";
            $result[$prefix."_".$field."_max"] = "--";
            return $result;
        }
    }
    
    public function getCombined() {
        $out = array_merge(
            $this->getSummaryCounts(),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_HOST, 'latency'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_SERVICE, 'latency'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_HOST, 'execution_time'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_SERVICE, 'execution_time')
        );
        return $out;
    }
    
    public function getJson() {
        $json = new AppKitExtJsonDocument();
        $data = $this->getCombined();
        $json->hasFieldBulk($data);
        $json->setData(array($data));
        $json->setSuccess(true);
        return $json;
    }
}
