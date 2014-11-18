<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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


class Cronks_Provider_ObjectInfoIconsModel extends CronksBaseModel {
    
    const TYPE_HOST = 1;
    const TYPE_SERVICE = 2;
    
    const FIELD_NOTIFICATION_ENABLED = 'notification_enabled';
    const FIELD_CHECK_TYPE = 'check_type';
    const FIELD_CURRENT_STATE = 'current_state';
    const FIELD_IS_FLAPPING = 'is_flapping';
    const FIELD_IN_DOWNTIME = 'in_downtime';
    const FIELD_PROBLEM_ACK = 'problem_acknowledged';
    
    private $__oids = array ();
    
    private $__type = null;
    
    /**
     * @var type IcingaDoctrine_Query
     */
    private $__request = null;
    
    private static $__fields = array (
        'notifications_enabled', 'active_checks_enabled', 
        'passive_checks_enabled', 'current_state', 
        'problem_has_been_acknowledged', 'is_flapping',
        'scheduled_downtime_depth'
    );
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__type = $this->getParameter('type');
        $this->__oids = AppKitArrayUtil::trimSplit($this->getParameter('oids'));
        
        if (!in_array($this->__type, array(self::TYPE_HOST, self::TYPE_SERVICE))) {
            throw new AppKitModelException('Parameter type not valid!');
        }
        
        if (!is_array($this->__oids)) {
            throw new AppKitModelException('oids must be an array');
        }
        
        $model = $this->getContext()->getModel("ApiDataRequest","Api");
        
        $this->__request = $model->createRequestDescriptor($parameters["connection"]);
    }
    
    public function getData() {
        $table = null;
        $fieldPrefix = null;
        
        if ($this->__type == self::TYPE_HOST) {
            $table = 'IcingaHoststatus';
            $fieldPrefix = 'host_';
        } elseif ($this->__type == self::TYPE_SERVICE) {
            $table = 'IcingaServicestatus';
            $fieldPrefix = 'service_';
        }
        
        $idfield = $fieldPrefix. 'object_id';
        
        $fields = self::$__fields;
        $fields[] = $idfield;
        
        $records = $this->__request
        ->select()
        ->from($table. ' t')
        ->andWhereIn('t.'. $idfield, $this->__oids)
        ->execute();
        
        $out = array ();
        
        if ($records) {
            foreach ($records as $record) {
                $tmp = array (
                    self::FIELD_CURRENT_STATE => 3,
                    self::FIELD_IN_DOWNTIME => false,
                    self::FIELD_IS_FLAPPING => false,
                    self::FIELD_NOTIFICATION_ENABLED => false,
                    self::FIELD_CHECK_TYPE => 'active'
                );
                
                $tmp[self::FIELD_CURRENT_STATE] = $record->current_state;
                
                if ($record->scheduled_downtime_depth > 0) {
                    $tmp[self::FIELD_IN_DOWNTIME] = true;
                }
                
                if ($record->is_flapping) {
                    $tmp[self::FIELD_IS_FLAPPING] = true;
                }
                
                if ($record->notifications_enabled == 1) {
                    $tmp[self::FIELD_NOTIFICATION_ENABLED] = true;
                }
                
                if (!$record->active_checks_enabled && $record->passive_checks_enabled) {
                    $tmp[self::FIELD_CHECK_TYPE] = 'passive';
                } elseif (!$record->active_checks_enabled && !$record->passive_checks_enabled) {
                    $tmp[self::FIELD_CHECK_TYPE] = 'disabled';
                } else {
                    $tmp[self::FIELD_CHECK_TYPE] = 'active';
                }
                
                if ($record->problem_has_been_acknowledged && $record->current_state > 0) {
                    $tmp[self::FIELD_PROBLEM_ACK] = true;
                }
                
                $out[$record->$idfield] = $tmp;
            }
        }
        
        return $out;
    }
    
}

?>