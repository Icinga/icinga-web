<?php 

/**
 * Data provider model for summary status
 * @author mhein
 *
 */
class Cronks_Provider_StatusSummaryModel extends CronksBaseModel {
    /**
     * (non-PHPdoc)
     * @see CronksBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
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
        
        $query = IcingaDoctrine_Query::create()
        ->select(
             'a.current_state, '
            . 'a.has_been_checked, '
            . 'a.should_be_scheduled, '
            . 'a.scheduled_downtime_depth, '
            . 'a.problem_has_been_acknowledged, '
            . 'a.passive_checks_enabled, '
            . 'a.active_checks_enabled, '
            . 'count(x.display_name) as count'
        );
        
        $filter = $this->getContext()->getModel('Filter.DoctrineUserRestriction', 'Api');
        $filter->setCurrentUser();
        
        if ($type==='host') {
            $query->from('IcingaHosts x')
            ->leftJoin('x.status a');
            
            $filter->enableModels(array(
                IcingaIPrincipalConstants::TYPE_HOSTGROUP => 'x',
                IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST => 'x',
                IcingaIPrincipalConstants::TYPE_CONTACTGROUP => 'x'
            ));
            
        } elseif ($type==='service') {
            $query->from('IcingaServices x')
            ->leftJoin('x.status a')
            ->leftJoin('x.host h')
            ->leftJoin('h.status hs');
            
           $query->addSelect('hs.current_state, hs.scheduled_downtime_depth, hs.problem_has_been_acknowledged');
           $query->addGroupBy('hs.current_state, hs.scheduled_downtime_depth, hs.problem_has_been_acknowledged');
           
           $filter->enableModels(array(
               IcingaIPrincipalConstants::TYPE_HOSTGROUP => 'h',
               IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST => 'h',
               IcingaIPrincipalConstants::TYPE_SERVICEGROUP => 'x',
               IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE => 'x',
               IcingaIPrincipalConstants::TYPE_CONTACTGROUP => 'x'
           ));
        }
        
        $query->addGroupBy('a.current_state, a.has_been_checked, a.should_be_scheduled, a.scheduled_downtime_depth, a.problem_has_been_acknowledged, a.passive_checks_enabled, a.active_checks_enabled')
        ->disableAutoIdentifierFields(true);
        
        $query->addFilter($filter);
        
        $records = $query->execute(array(), Doctrine::HYDRATE_SCALAR);
        
        $out = $this->createTypeDescriptor($type);
        
        foreach ($records as $record) {
            $state = $record['a_current_state'];
            
            if (!is_numeric($state)) {
                continue;
            }
            
            if ((!$record['a_has_been_checked'])) {
                $state = IcingaConstants::HOST_PENDING;
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
                    $out[$state]['unacknowledged'] += $record['x_count'];
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
        ->select('p.programstatus_id, p.instance_id, p.last_command_check, i.instance_name')
        ->from('IcingaProgramstatus p')
        ->innerJoin('p.instance i')->execute();
    
        $checkTime = 300;
    
        $diff = 0;
    
        $out = array();
    
        $status = false;
    
        foreach ($instances as $instance) {
    
            $date = (int)strtotime($instance->last_command_check);
            $diff = (time()-$date);
    
            if ($diff < $checkTime && $instance->is_currently_running) {
                $status = true;
            } else {
                $status = false;
            }
    
            $out[] = array (
                        'instance' => $instance->instance->instance_name,
                        'status' => $status,
                        'last_check' => $instance->last_command_check,
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
        $data = $this->getData();
        $json = new AppKitExtJsonDocument();
        foreach(array_keys($data[0]) as $f) {
            $json->hasField($f);
        }
        $json->setSuccess(true);
        $json->setData($data);
        $json->setSortinfo('type');
        
        $json->addMiscData('rowsInstanceStatus', $this->getDataForInstance());
        
        return $json;
    }
}

?>
