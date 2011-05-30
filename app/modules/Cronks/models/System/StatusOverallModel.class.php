<?php

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
                       IcingaApi::TARGET_HOST_STATUS_SUMMARY =>
                       array(self::TYPE_HOST, IcingaHostStateInfo::Create()->getStateList()),
                       IcingaApi::TARGET_SERVICE_STATUS_SUMMARY =>
                       array(self::TYPE_SERVICE, IcingaServiceStateInfo::Create()->getStateList())
                   );

        $target = array();

        foreach($sources as $stype=>$tarray) {
            $type = explode("_",strtoupper($stype));
            $search = $this->api->createSearch()->setSearchTarget($stype);
            $search->setSearchFilter($type[0]."_IS_PENDING","0","=");
            IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
            $this->buildDataArray($search, $tarray[0], $tarray[1], $target, $stype);
        }

        return $target;

    }

    private function normalizeData(array &$data, $state_field, $count_field='COUNT') {
        $out = array();
        foreach($data as $k=>$v) {
            if (array_key_exists($state_field, $v) && array_key_exists($count_field, $v)) {
                $out[ $v[$state_field] ] = $v[ $count_field ];
            }
        }
        return $out;
    }

    private function buildDataArray(IcingaApiSearch &$search, $type, array $states, array &$target,$stype) {
        $data = $search->setResultType(IcingaApi::RESULT_ARRAY)->fetch()->getAll();

        $this->addPending($data,$stype);

        $field = sprintf('%s_STATE', strtoupper($type));
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
        $result = $search->setResultType(IcingaApi::RESULT_ARRAY)->fetch()->getAll();

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
        return $json;
    }

}

?>
