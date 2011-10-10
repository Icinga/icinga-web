<?php

class Cronks_ColumnDisplay_DurationModel extends CronksBaseModel implements AgaviISingletonModel {
    
    private $hostQuery = null;
    
    private $serviceQuery = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $model = $this->getContext()->getModel("ApiDataRequest","Api");
        
        $this->serviceQuery = $model->createRequestDescriptor()->select('ss.servicestatus_id, ss.last_state_change')
        ->from('IcingaServicestatus ss')
        ->innerJoin('ss.instance i')
        ->innerJoin('i.programstatus p')
        ->andWhere('ss.service_object_id=?');
        
        $this->hostQuery = $model->createRequestDescriptor()->select()
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
        
        $tstamp = strtotime($val);
        
        $diff = time() - $tstamp;
        
        if ($diff > 0) {
            $out = array ();
            foreach ($durationMap as $k=>$v) {
                $m = $diff%$v;
                if ($diff===$m) {
                    continue;
                } else {
                    $out[] = ceil($diff/$v).$k;
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
