<?php

/**
 * Providing summary preformance information from icinga
 * @author mhein
 *
 */
class Cronks_System_MonitorPerformanceDataModel extends CronksBaseModel {

    private static $sources = array(
                                  array(IcingaApiConstants::TARGET_HOST, array(
                                            'HOST_EXECUTION_TIME_MIN', 'HOST_EXECUTION_TIME_MAX', 'HOST_EXECUTION_TIME_AVG',
                                            'HOST_LATENCY_MIN', 'HOST_LATENCY_MAX', 'HOST_LATENCY_AVG'
                                        )),

                                  array(IcingaApiConstants::TARGET_HOST_SERVICE, array(
                                            'SERVICE_EXECUTION_TIME_MIN', 'SERVICE_EXECUTION_TIME_MAX', 'SERVICE_EXECUTION_TIME_AVG',
                                            'SERVICE_LATENCY_MIN', 'SERVICE_LATENCY_MAX', 'SERVICE_LATENCY_AVG'
                                        )),

                                  array(
                                      IcingaApiConstants::TARGET_HOST,
                                      array('HOST_OBJECT_ID'),
                                      IcingaApiConstants::SEARCH_TYPE_COUNT,
                                      array(array('HOST_CHECK_TYPE', 0)),
                                      'NUM_ACTIVE_HOST_CHECKS'
                                  ),

                                  array(
                                      IcingaApiConstants::TARGET_HOST,
                                      array('HOST_OBJECT_ID'),
                                      IcingaApiConstants::SEARCH_TYPE_COUNT,
                                      array(array('HOST_CHECK_TYPE', 1)),
                                      'NUM_PASSIVE_HOST_CHECKS'
                                  ),

                                  array(
                                      IcingaApiConstants::TARGET_SERVICE,
                                      array('SERVICE_OBJECT_ID'),
                                      IcingaApiConstants::SEARCH_TYPE_COUNT,
                                      array(array('SERVICE_CHECK_TYPE', 0)),
                                      'NUM_ACTIVE_SERVICE_CHECKS'
                                  ),

                                  array(
                                      IcingaApiConstants::TARGET_SERVICE,
                                      array('SERVICE_OBJECT_ID'),
                                      IcingaApiConstants::SEARCH_TYPE_COUNT,
                                      array(array('SERVICE_CHECK_TYPE', 1)),
                                      'NUM_PASSIVE_SERVICE_CHECKS'
                                  ),
                              );

    /**
     * @var AgaviParameterHolder
     */
    private $data = null;

    /**
     * @var Web_Icinga_ApiContainerModel
     */
    private $api = null;

    /**
     * (non-PHPdoc)
     * @see app/modules/AppKit/lib/model/AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->data = new AgaviParameterHolder();

        $this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
    }

    private function buildData() {

        foreach(self::$sources as $source) {
            try {
                $res = $this->api->createSearch()
                       ->setResultType(IcingaApiConstants::RESULT_ARRAY)
                       ->setSearchTarget($source[0])
                       ->setResultColumns($source[1]);
                IcingaPrincipalTargetTool::applyApiSecurityPrincipals($res);
                $res->setIgnoreIds(true);
                if (isset($source[2])) {
                    $res->setSearchType($source[2]);
                }

                if (isset($source[3])) {
                    $res->setSearchFilter($source[3]);
                }

                
                $arr = $res->fetch()->getRow();       
                $query = $res->getSqlQuery();
                foreach($arr as $key=>$value) {

                    if (isset($source[4])) {
                        $key = $source[4];
                    }

                    if (is_numeric($value) && strpos($value, '.') !== false) {
                        $value = sprintf('%.2f', $value);
                    }

                    $this->data->setParameter($key, $value);
                }

            } catch (IcingaApiException $e) {
                return false;
            }

        }

        return true;

    }

    public function getData() {
        $this->buildData();
        return $this->data;
    }

    public function getJson() {
        $data = $this->getData();

        $doc = new AppKitExtJsonDocument();
        foreach($data->getParameterNames() as $name) {
            $doc->hasField($name);
        }

        $doc->setData(array($data->getParameters()));

        return $doc;
    }

}