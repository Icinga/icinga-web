<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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
 * Data provider model for summary status
 * @author mhein
 * @author mfrosch
 * @package IcingaWeb
 * @subpackage Cronks
 *
 */
class Cronks_Provider_StatusSummaryModel extends CronksUserCacheDataModel {

    /**
     * @var Cronks_Provider_ProgramStatusModel
     */
    private $programStatus;

    /**
     * (non-PHPdoc)
     * @see CronksBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->programStatus = $this->getContext()->getModel('Provider.ProgramStatus', 'Cronks');
        $this->setUniqueCacheIdentifier('statussummary');
    }

    private function createStateDescriptor($state, $name, $type) {
        return array (
            'state' => $state,
            'state_name' => $name,
            'type' => $type,
            'count' => 0,
            'acknowledged' => 0,
            'unacknowledged' => 0,
            'active' => 0,
            'passive' => 0,
            'disabled' => 0,
            'working' => 0,
            'handled' => 0,
            'allProblems' => 0
        );
    }

    private function createTypeDescriptor($type) {
        $list = array();

        if ($type==='host') {
            $list = IcingaHostStateInfo::Create()->getStateList();
        } elseif ($type==='service') {
            $list = IcingaServiceStateInfo::Create()->getStateList();
        } else {
            throw new AppKitModelException("Type not known: $type");
        }

        $out = array ();

        foreach ($list as $state=>$name) {
            $out[$state] = $this->createStateDescriptor($state, $name, $type);
        }

        return $out;
    }

    public function getDataForType($type) {

        if ($type !== 'service' && $type !== 'host') {
            throw new AppKitModelException("Type $type is useless!");
        }

        // set view
        if ($type === 'host') {
            $viewName = "TARGET_SUMMARY_HOST";
        }
        elseif ($type==='service') {
            $viewName = "TARGET_SUMMARY_SERVICE";
        }

        // load view
        $view = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
            "view" => $viewName,
            "connection" => 'icinga'
        ));

        // get the result directly - the viewManager would normalize them
        $records = $view->getQuery()
                   ->disableAutoIdentifierFields(true)
                   ->execute(null,Doctrine_Core::HYDRATE_SCALAR);

        $out = $this->createTypeDescriptor($type);

        foreach ($records as $record) {
            $state = $record['a_current_state'];

            /* DO NOT check for this, due to a missing inital (pending)
               state this *can* be NULL (#3838) -mfrosch
            *//*
            if (!is_numeric($state)) {
                continue;
            }
            */

            if ((!$record['a_has_been_checked'])) {
                $state = IcingaConstants::HOST_PENDING;
            }

            /*
             * Test for target data because this can break the whole cronk, #6126 (mh)
             */
            if (!array_key_exists($state, $out)) {
                continue;
            }

            if ($type === 'service') {
                if ($record['hs_current_state'] !== "0" && (int)$record['hs_current_state'] > 0) {
                    $out[$state]['handled'] += $record['x_count'];
                } elseif ((int)$record['a_scheduled_downtime_depth'] > 0) {
                    $out[$state]['handled'] += $record['x_count'];
                } elseif ($record['a_problem_has_been_acknowledged'] == 1) {
                    $out[$state]['acknowledged'] += $record['x_count'];
                } else {
                    $out[$state]['unacknowledged'] += $record['x_count'];
                }
            } elseif ($type === 'host') {
                if ($record['a_problem_has_been_acknowledged'] == 1) {
                    if ($record['a_problem_has_been_acknowledged']) {
                        $out[$state]['acknowledged'] += $record['x_count'];
                    } elseif ($record['a_scheduled_downtime_depth']) {
                        $out[$state]['handled'] += $record['x_count'];
                    } else {
                        $out[$state]['unacknowledged'] += $record['x_count'];
                    }
                } else {
                    if ($record['a_scheduled_downtime_depth']) {
                        $out[$state]['handled'] += $record['x_count'];
                    } else {
                        $out[$state]['unacknowledged'] += $record['x_count'];
                    }
                }
            }

            // Active / passive
            if ($record['a_passive_checks_enabled']) {
                $out[$state]['active'] += $record['x_count'];
            } elseif ($record['a_active_checks_enabled']) {
                $out[$state]['passive'] += $record['x_count'];
            } else {
                $out[$state]['disabled'] += $record['x_count'];
            }

            // Sum
            $out[$state]['count'] += $record['x_count'];

        }
        foreach ($out as $state=>$array) {

            $out[$state]['working'] = ($g=(int)$out[$state]['count'] - (int)$out[$state]['disabled']) > 0 ? $g : 0;


            /*
             *
             */
            // $out[$state]['unacknowledged'] = ($g=(int)$out[$state]['count'] - (int)$out[$state]['acknowledged'] - (int)$out[$state]['handled']) > 0 ? $g : 0;

        }

        $sum = $this->createStateDescriptor(100, 'TOTAL', $type);
        foreach ($out as $a) {
            foreach ($sum as $k=>&$b) {
                if (!is_numeric($b) || $k=='state') {
                    continue;
                }
                $b+=$a[$k];
            }
        }

        $sum['allProblems'] = $sum['count'] - $out[0]['count'];

        $out[] = $sum;

        return $out;
    }

    protected function getDataForInstance() {
        $instances = IcingaDoctrine_Query::create()
        ->select('p.programstatus_id, p.instance_id, p.status_update_time, i.instance_name')
        ->from('IcingaProgramstatus p')
        ->innerJoin('p.instance i')->execute();

        $checkTime = 300;

        $diff = 0;

        $out = array();

        $status = false;

        foreach ($instances as $instance) {

            $date = (int)strtotime($instance->status_update_time);
            $diff = (time()-$date);

            if ($diff < $checkTime && $instance->is_currently_running) {
                $status = true;
            } else {
                $status = false;
            }

            $out[] = array (
                        'instance' => $instance->instance->instance_name,
                        'status' => $status,
                        'last_check' => $instance->status_update_time,
                        'start' => $instance->program_start_time,
                        'diff' => $diff,
                        'check' => $checkTime
            );
        }

        return $out;
    }

    public function getData() {
        $out = array();
        $out = array_merge($this->getDataForType('host'), $this->getDataForType('service'));
        return $out;
    }

    public function getJson() {

        $json = new AppKitExtJsonDocument();

        if (!$this->programStatus->isApplicable() || $this->programStatus->config_dump_in_progress === '1') {
            $data = $this->retrieveData();
            $json->addMiscData('fromCache', true);
        } else {
            $data = array($this->getData(), $this->getDataForInstance());
            $this->writeData($data);
            $json->addMiscData('fromCache', false);
        }


        foreach(array_keys($data[0][0]) as $f) {
            $json->hasField($f);
        }
        $json->setSuccess(true);
        $json->setData($data[0]);
        $json->setSortinfo('type');

        $json->addMiscData('rowsInstanceStatus', $data[1]);

        return $json;
    }
}
