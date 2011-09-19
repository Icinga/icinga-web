<?php

/**
 * Status provider for the StatusSummaryCronk
 * @author mhein
 *
 */
class Cronks_System_StatusOverallModel extends CronksBaseModel {

    const TYPE_HOST		= 'host';
    const TYPE_SERVICE	= 'service';

    /**
     *
     * @var Web_Icinga_ApiContainerModel
     */
    private $api	= null;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
    }

    private function getData() {

        $sources = array(
                       IcingaApiConstants::TARGET_HOST_STATUS_SUMMARY =>
                       array(self::TYPE_HOST, IcingaHostStateInfo::Create()->getStateList()),
                       IcingaApiConstants::TARGET_SERVICE_STATUS_SUMMARY =>
                       array(self::TYPE_SERVICE, IcingaServiceStateInfo::Create()->getStateList())
                   );

        $target = array();

        foreach($sources as $stype=>$tarray) {
            $type = explode("_",strtoupper($stype));
            $search = $this->api->createSearch()->setSearchTarget($stype);
            //$search->setSearchFilter($type[0]."_IS_PENDING","0","<=");
            IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);

            $this->buildDataArray($search, $tarray[0], $tarray[1], $target, $stype);

        }

        return $target;

    }

    private function normalizeData(array &$data, $state_field, $count_field='COUNT') {
        $out = array();
        foreach($data as $k=>$v) {
            if (array_key_exists($state_field, $v) && array_key_exists($count_field, $v)) {
                if(!isset($out[$v[$state_field]]))
                    $out[$v[$state_field]] = 0;
                $out[ $v[$state_field] ] += $v[ $count_field ];
            }
        }
        return $out;
    }

    private function buildDataArray(&$search, $type, array $states, array &$target,$stype) {
        $field = sprintf('%s_CURRENT_STATE', strtoupper($type));
        $pendingField = sprintf('%s_IS_PENDING', strtoupper($type));
        
        $search->setResultColumns(array($field,$pendingField));
        $search->setSearchGroup(sprintf('%s_IS_PENDING', strtoupper($type)));
        $data = $search->setResultType(IcingaApiConstants::RESULT_ARRAY)->fetch()->getAll();
     
        $data = $this->normalizeData($data, $field);
        $sum = 0;

        foreach($states as $sid=>$sname) {
            $count = 0;
            
            if (array_key_exists($sid, $data)) {
                $count = $data[$sid];
            }

            $sum += $count;
            $target[] = array(
                            'type'	=> $type,
                            'state'	=> $sid,
                            'count'	=> $count
                        );
        }

        $target[] = array(
                        'type'	=> $type,
                        'state'	=> 100,
                        'count'	=> $sum
                    );
    }

    protected function addPending(&$data,$stype) {
        $type = explode("_",strtoupper($stype));

        $search = $this->api->createSearch()->setSearchTarget($stype);
        $search->setSearchFilter($type[0]."_IS_PENDING","0",">");
        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
        $result = $search->setResultType(IcingaApiConstants::RESULT_ARRAY)->fetch()->getAll();

        if (count($result) > 0) {
            $result = $result[0];
        } else {
            return;
        }

        foreach($result as $type=>&$val) {
            if (preg_match("/^.*_STATE$/",$type)) {
                $val = 99;
            }
        }

        $data[] = $result;
    }
    
    protected function getInstanceDataStatus() {
        $model = $this->getContext()->getModel('ApiDataRequest','Api');
        
        $r = $model->createRequestDescriptor();
        $instances = $r->select('p.programstatus_id, p.instance_id, p.last_command_check, i.instance_name')
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

    /**
     * @return AppKitExtJsonDocument
     */
    public function &getJson() {
        $data = $this->getData();
        $json = new AppKitExtJsonDocument();

        foreach(array_keys($data[0]) as $f) {
            $json->hasField($f);
        }

        $json->setSuccess(true);
        $json->setData($data);
        $json->setSortinfo('type');
        
        $json->addMiscData('rowsInstanceStatus', $this->getInstanceDataStatus());
        
        return $json;
    }

}