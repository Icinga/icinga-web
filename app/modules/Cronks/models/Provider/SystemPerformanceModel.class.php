<?php 

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
    
    public function getSummaryCounts($type) {
        $this->checkObjectType($type);
        
        $table = null;
        $prefix = null;
        
        if ($type===IcingaConstants::TYPE_HOST) {
            $table = 'IcingaHosts';
            $prefix = 'host';
        } else {
            $table = 'IcingaServices';
            $prefix = 'service';
        }
        
        $tarray = array();
        $out = array();
        foreach (array('active', 'passive', 'disabled') as $garbage) {
            $f = sprintf('%s_checks_%s', $prefix, $garbage);
            $tarray[$garbage] = $f;
            $out[$f] = (int)0;
        }
        
        $data = IcingaDoctrine_Query::create()
        ->from($table. ' a')
        ->select('count(a.'. $prefix. '_id) as count, a.passive_checks_enabled, a.active_checks_enabled')
        ->groupBy('a.passive_checks_enabled, a.active_checks_enabled')
        ->disableAutoIdentifierFields(true)
        ->execute(array(), Doctrine::HYDRATE_SCALAR);

        foreach ($data as $f) {
            if ($f['a_active_checks_enabled']) {
                $out[$tarray['active']] += (int)$f['a_count'];
            } elseif (!$f['a_active_checks_enabled'] && !$f['a_passive_checks_enabled']) {
                $out[$tarray['disabled']] += (int)$f['a_count'];
            } elseif ($f['a_passive_checks_enabled']) {
                $out[$tarray['passive']] += (int)$f['a_count'];
            }
        }
        
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
   
        $result = array();
        if (is_array($data) && count($data) === 1) {
            $data = $data[0];

            $result[$prefix."_".$field."_min"] = sprintf('%.3f',$data[$prefix."_min"]);
            $result[$prefix."_".$field."_avg"] = sprintf('%.3f',$data[$prefix."_sum"]/$data[$prefix."_count"]);
            $result[$prefix."_".$field."_max"] = sprintf('%.3f',$data[$prefix."_max"]);
            return $result;
        }
    }
    
    public function getCombined() {
        $out = array ();
        $out = array_merge(
            $this->getSummaryStatusValues(IcingaConstants::TYPE_HOST, 'latency'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_SERVICE, 'latency'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_HOST, 'execution_time'),
            $this->getSummaryStatusValues(IcingaConstants::TYPE_SERVICE, 'execution_time'),
            $this->getSummaryCounts(IcingaConstants::TYPE_HOST),
            $this->getSummaryCounts(IcingaConstants::TYPE_SERVICE)
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
