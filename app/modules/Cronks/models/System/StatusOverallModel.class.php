<?php

/**
 * Status provider for the StatusSummaryCronk
 * @author mhein
 *
 */
class Cronks_System_StatusOverallModel extends CronksBaseModel {

    const TYPE_HOST		= 'host';
    const TYPE_SERVICE	= 'service';
    // indizes for the output
    const IDX_NO_PROBLEM = "RESOLVED";
    const IDX_OPEN_PROBLEM = "OPEN_PROBLEM";
    const IDX_SUM = "OVERALL";
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

    private function normalizeData(array &$data, $state_field, $count_field='COUNT',$dtime,$ack) {
        $out = array(

        );
        foreach($data as $index=>$currentStateSummary) {
            
            if (isset($currentStateSummary[$state_field]) && isset($currentStateSummary[$count_field])) {
                // no entries -> state count is 0
                if(!isset($out[$currentStateSummary[$state_field]]))
                    $out[$currentStateSummary[$state_field]] = array(
                        self::IDX_NO_PROBLEM => 0,
                        self::IDX_OPEN_PROBLEM => 0,
                        self::IDX_SUM => 0
                    );
                $currentStateField = $out[$currentStateSummary[$state_field]];
                // otherwise fill in the state count
                $currentStateField[self::IDX_SUM] += $currentStateSummary[ $count_field ];

                // get problem count
                if(isset($currentStateSummary[$ack]) && isset($currentStateSummary[$dtime])) {
                    if($currentStateSummary[$ack] == 1 || $currentStateSummary[$dtime] == 1)
                        $currentStateField[self::IDX_NO_PROBLEM] = $currentStateSummary[$count_field];
                    else
                       $currentStateField[self::IDX_OPEN_PROBLEM] = $currentStateSummary[$count_field];
                }
                $out[$currentStateSummary[$state_field]] = $currentStateField;
            }
        }
        return $out;
    }

    private function buildDataArray(&$search, $type, array $states, array &$target,$stype) {
        $field = sprintf('%s_CURRENT_STATE', strtoupper($type));
        $pendingField = sprintf('%s_IS_PENDING', strtoupper($type));
        $dtimeField = sprintf('%s_SCHEDULED_DOWNTIME_DEPTH', strtoupper($type));
        $ackField = sprintf('%s_PROBLEM_HAS_BEEN_ACKNOWLEDGED', strtoupper($type));

        $search->setResultColumns(array($field,$pendingField,$dtimeField,$ackField));
        $search->setSearchGroup(sprintf('%s_IS_PENDING', strtoupper($type)));
        $data = $search->setResultType(IcingaApiConstants::RESULT_ARRAY)->fetch()->getAll();
        $data = $this->normalizeData($data, $field,'COUNT',$dtimeField,$ackField);

        $sum_total      = 0;
        $sum_resolved   = 0;
        $sum_open       = 0;

        foreach($states as $sid=>$sname) {
            $count = 0;
            $entry = array(
                self::IDX_NO_PROBLEM => 0,
                self::IDX_OPEN_PROBLEM => 0,
                self::IDX_SUM => 0
            );
            if (array_key_exists($sid, $data)) {
                $entry = $data[$sid];
            }

            $sum_total += $entry[self::IDX_SUM];
            $sum_resolved += $entry[self::IDX_NO_PROBLEM];
            $sum_open += $entry[self::IDX_OPEN_PROBLEM];
            $targetEntry = array(
                    'type'      => $type,
                    'state'     => $sid,
                    'open'      => $entry[self::IDX_OPEN_PROBLEM],
                    'resolved'	=> $entry[self::IDX_NO_PROBLEM],
                    'count'     => $entry[self::IDX_SUM],
            );

            $target[] = $targetEntry;
        }

        $target[] = array(
                'type'      => $type,
                'state'     => 100,
                'open'      => $sum_open,
                'resolved'	=> $sum_resolved,
                'count'     => $sum_total
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