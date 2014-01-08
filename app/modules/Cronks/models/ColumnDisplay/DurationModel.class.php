<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Cronks_ColumnDisplay_DurationModel extends CronksBaseModel implements AgaviISingletonModel {
    
    private $hostQuery = null;
    
    private $serviceQuery = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->serviceQuery = IcingaDoctrine_Query::create()->select('ss.servicestatus_id, ss.last_state_change')
        ->from('IcingaServicestatus ss')
        ->innerJoin('ss.instance i')
        ->innerJoin('i.programstatus p')
        ->andWhere('ss.service_object_id=?');
        
        $this->hostQuery = IcingaDoctrine_Query::create()->select()
        ->from('IcingaHoststatus hs')
        ->innerJoin('hs.instance i')
        ->innerJoin('i.programstatus p')
        ->andWhere('hs.host_object_id=?');
    }
    
    private function getDateString($val, $type) {
        $query = null;
        
        switch ($type) {
            case 'service':
                $query =& $this->serviceQuery;
            break;
            
            case 'host':
                $query =& $this->hostQuery;
            break;
            
            default:
                throw new AppKitModelException('Type not found for method durationString()');
        } 
        
        $res = $query->execute(array($val));
        
        if ($res->count()) {
            $record = $res->getFirst();
            
            $tstamp = $record->last_state_change;
            
            $check = strtotime($tstamp);
            if ($check <= 0) {
                $tstamp = $record->instance->programstatus->program_start_time;
            }
            
            return $tstamp;
        }
    }
    
    public function durationString($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        $tstamp = $this->getDateString($val, $method_params->getParameter('type'));
        return $this->simpleDurationString($tstamp);
    }
    
    public function simpleDurationString($val, AgaviParameterHolder $method_params=null, AgaviParameterHolder $row=null) {
        static $durationMap = array (
                    'w' => 604800,
                    'd' => 86400,
                    'h' => 3600,
                    'm' => 60,
                    's' => 1
        );
        
        $new_val = $this->context->getTranslationManager()->_d($val, 'date-tstamp');
        
        $tstamp = strtotime($val);
        
        $diff = time() - $tstamp;
        if ($diff > 0) {
            $out = array ();
            foreach ($durationMap as $k=>$v) {
                $m = $diff%$v;
                if ($diff===$m) {
                    continue;
                } else {
                    $out[] = floor($diff/$v).$k;
                    $diff = $m;
                }
        
                if ($m===0) {
                    break;
                }
            }
        
            return implode(' ', $out);
        }
        
        return '0s';
    }
    
}
