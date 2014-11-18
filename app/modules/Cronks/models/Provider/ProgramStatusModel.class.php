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

/**
 * Model to deal with program status data
 *
 * @property integer $programstatus_id
 * @property integer $instance_id
 * @property string $status_update_time
 * @property string $program_start_time
 * @property string $program_end_time
 * @property integer $is_currently_running
 * @property integer $process_id
 * @property integer $daemon_mode
 * @property string $last_command_check
 * @property string $last_log_rotation
 * @property integer $notifications_enabled
 * @property integer $active_service_checks_enabled
 * @property integer $passive_service_checks_enabled
 * @property integer $active_host_checks_enabled
 * @property integer $passive_host_checks_enabled
 * @property integer $event_handlers_enabled
 * @property integer $flap_detection_enabled
 * @property integer $failure_prediction_enabled
 * @property integer $process_performance_data
 * @property integer $obsess_over_hosts
 * @property integer $obsess_over_services
 * @property integer $modified_host_attributes
 * @property integer $modified_service_attributes
 * @property string $global_host_event_handler
 * @property string $global_service_event_handler
 * @property integer $config_dump_in_progress
 */
class Cronks_Provider_ProgramStatusModel extends CronksBaseModel {

    /**
     * Data container for properties
     *
     * @var array
     */
    private $data = array();

    /**
     * Name of instance
     *
     * @var string
     */
    private $instance;

    /**
     * If the model can fetch proper data
     *
     * @var bool
     */
    private $applicable = false;

    /**
     * (non-PHPdoc)
     * @see CronksBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        if (isset($parameters['instance'])) {
            $this->instance = $parameters['instance'];
        }

        $this->refresh();
    }

    /**
     * Refresh data in the container
     */
    public function refresh() {
        $query = IcingaDoctrine_Query::create()
            ->from('IcingaProgramstatus p')
            ->limit(1);

        if ($this->instance) {
            $query->innerJoin('p.instance i with i.instance_name=?', array($this->instance));
        }

        $row = $query->execute()->getFirst();

        $this->data = array();

        if ($row) {
            $this->applicable = true;
            foreach ($row as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Magic property getter
     *
     * @param   string $name Property name
     *
     * @return  mixed
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Getter for applicable
     *
     * @return bool
     */
    public function isApplicable() {
        return $this->applicable;
    }
}