<?php

class IcingaTemplateDisplayServiceIcons extends IcingaTemplateDisplay {

    /**
     * Eval condition return constant
     * @var integer
     */
    const COND_ERROR = 0xffff;

    /**
     * The image base path, appended to the web base path
     * @var string
     */
    private $image_path = '/images/status';

    /**
     * A list of fields to be used in the conditions map
     * @var array
     */
    private static $service_fields = array(
                                         'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_ACTIVE_CHECKS_ENABLED',
                                         'SERVICE_PASSIVE_CHECKS_ENABLED', 'SERVICE_CURRENT_STATE',
                                         'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'SERVICE_IS_FLAPPING',
                                         'SERVICE_SCHEDULED_DOWNTIME_DEPTH'
                                     );


    /**
     * The conditions map, the array key is php logical with
     * format parser syntax, all fields defined in the field list
     * can be used. Target array keys are true and false which holds
     * another array in it, container an image name and its alt
     * description
     * @var array
     */
    private static $service_conditions = array(
            '${field.SERVICE_NOTIFICATIONS_ENABLED}' => array(
                false	=> array('ndisabled.png', 'Notifications disabled')
            ),

            '!${field.SERVICE_ACTIVE_CHECKS_ENABLED} && !${field.SERVICE_PASSIVE_CHECKS_ENABLED}' => array(
                true	=> array('disabled.png', 'Service disabled')
            ),

            '!${field.SERVICE_ACTIVE_CHECKS_ENABLED} && ${field.SERVICE_PASSIVE_CHECKS_ENABLED}' => array(
                true	=> array('passive.png', 'Passive only')
            ),

            '${field.SERVICE_CURRENT_STATE} && ${field.SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED}' => array(
                true	=> array('acknowledged.png', 'Problem has been acknowledged')
            ),

            '${field.SERVICE_IS_FLAPPING}' => array(
                true	=> array('flapping.png', 'Service is flapping')
            ),

            '${field.SERVICE_SCHEDULED_DOWNTIME_DEPTH}' => array(
                true	=> array('downtime.png', 'In downtime')
            )
                                         );

    /**
     * A list of fields to be used in the conditions map
     * @var array
     */
    private static $host_fields = array(
                                      'HOST_ID',
                                      'HOST_NOTIFICATIONS_ENABLED', 'HOST_ACTIVE_CHECKS_ENABLED',
                                      'HOST_PASSIVE_CHECKS_ENABLED', 'HOST_CURRENT_STATE',
                                      'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'HOST_IS_FLAPPING',
                                      'HOST_SCHEDULED_DOWNTIME_DEPTH'
                                  );

    /**
     * The conditions map, the array key is php logical with
     * format parser syntax, all fields defined in the field list
     * can be used. Target array keys are true and false which holds
     * another array in it, container an image name and its alt
     * description
     * @var array
     */
    private static $host_conditions = array(
                                          '${field.HOST_NOTIFICATIONS_ENABLED}' => array(
                                                  false	=> array('ndisabled.png', 'Notifications disabled')
                                          ),

                                          '${field.HOST_ACTIVE_CHECKS_ENABLED} && ${field.HOST_PASSIVE_CHECKS_ENABLED}' => array(
                                                  false	=> array('disabled.png', 'Service disabled')
                                          ),

                                          '!${field.HOST_ACTIVE_CHECKS_ENABLED} && ${field.HOST_PASSIVE_CHECKS_ENABLED}' => array(
                                                  true	=> array('passive.png', 'Passive only')
                                          ),

                                          '${field.HOST_CURRENT_STATE} && ${field.HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED}' => array(
                                                  true	=> array('acknowledged.png', 'Problem has been acknowledged')
                                          ),

                                          '${field.HOST_IS_FLAPPING}' => array(
                                                  true	=> array('flapping.png', 'Host is flapping')
                                          ),

                                          '${field.HOST_SCHEDULED_DOWNTIME_DEPTH}' => array(
                                                  true	=> array('downtime.png', 'In downtime')
                                          )
                                      );

    /**
     * Returns a singleton class instance
     * @return IcingaTemplateDisplayServiceIcons
     */
    public static function getInstance() {
        return parent::getInstance(__CLASS__);
    }

    /**
     * Returns the service status icons from the map above defined
     * @param mixed $val
     * @param AgaviParameterHolder $method_params
     * @param AgaviParameterHolder $row
     * @return string
     */
    public function serviceIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {

        $id = $this->getObjectId($method_params->getParameter('field', null), $row);

        $dh = $this->getIcingaApi()->createSearch()
              ->setSearchTarget(IcingaApiConstants::TARGET_SERVICE)
              ->setResultColumns(self::$service_fields)
              ->setSearchFilter('SERVICE_OBJECT_ID', $id);

        return $this->buildIcons($dh, self::$service_conditions);
    }

    /**
     * Returns the host status icons from the map above defined
     * @param mixed $val
     * @param AgaviParameterHolder $method_params
     * @param AgaviParameterHolder $row
     * @return string
     */
    public function hostIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {

        $id = $this->getObjectId($method_params->getParameter('field', null), $row);
        $dh = $this->getIcingaApi()->createSearch()
              ->setSearchTarget(IcingaApiConstants::TARGET_HOST)
              ->setResultColumns(self::$host_fields)
              ->setSearchFilter('HOST_OBJECT_ID', $id);

        return $this->buildIcons($dh, self::$host_conditions);
    }

    /**
     * Build the icon frames with the field and the conditions map
     * @param IcingaApiSearch $dh
     * @param array $mapping
     * @return string html code
     */
    private function buildIcons(&$dh, array $mapping) {
        $out = null;

        $parser = new AppKitFormatParserUtil();
        $parser->registerNamespace('field', AppKitFormatParserUtil::TYPE_ARRAY);
/*
        foreach($dh->fetch() as $res) {
            $row = (array)$res->getRow();
            $parser->registerData('field', $row);

            foreach($mapping as $fkey=>$fm) {
                $cond = 'return (int)('. $parser->parseData($fkey). ');';

                if (($test = $this->evalCode($cond)) !== self::COND_ERROR) {
                    // var_dump(array($fkey, $cond, $test));
                    if (isset($fm[$test])) {
                        // var_dump(" --> OK");
                        $i = $fm[$test];
                        $tag = AppKitXmlTag::create('img');

                        if (isset($i[0])) {
                            $tag->addAttribute('src', $this->wrapImagePath($this->image_path). '/'. $i[0]);
                        }

                        if (isset($i[1])) {
                            $tag->addAttribute('alt', $i[1])
                            ->addAttribute('title', $i[1]);
                        }

                        $out .= (string)$tag;
                    }
                }


            }

        }
*/
        return $out;
    }

    /**
     * Evals some code and returns its value
     * or self::COND_ERROR if a error occures
     * @param string $code
     * @return mixed
     */
    private function evalCode($code) {
        $re = @eval($code);

        if ($re === false) {
            return self::COND_ERROR;
        }

        return (bool)$re;
    }

    /**
     * Returns the id value from the row
     * @param string $field_name
     * @param AgaviParameterHolder $row
     * @return mixed
     */
    private function getObjectId($field_name, AgaviParameterHolder $row) {

        if ($row->hasParameter($field_name)) {
            return $row->getParameter($field_name);
        }

        return null;

    }

    /**
     * Returns a unique instance of the icinga api connection
     * @return IcingaApiConnectionIdo
     */
    private function getIcingaApi() {
        static $api = null;

        if ($api === null) {
            $api = AgaviContext::getInstance()->getModel('Icinga.ApiContainer', 'Web')->getConnection();
        }

        return $api;
    }

}

?>
