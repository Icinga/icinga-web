<?php

/**
 * Dealing with cronk requests in agavi templates: Interface
 * between PHP and JS
 * @author mhein
 *
 */
class CronksRequestUtil {

    /**
     * Default list of parameters used in a cronk action
     * @var array
     */
    protected static $requestAttributes = array(
            'cmpid', 'parentid', 'stateuid', 'state'
                                          );

    /**
     * Creates the right data structure to call the cronk script interface
     * @param AgaviRequestDataHolder $rd
     * @return array
     */
    public static function makeRequestDataStruct(AgaviRequestDataHolder $rd) {
        $data = array();
        foreach(self::$requestAttributes as $attr) {
            if ($rd->hasParameter($attr)) {
                $data[$attr] = $rd->getParameter($attr);
            }
        }
        return $data;
    }

    /**
     * Create the right data structore for the cronk script interface as json
     * @param AgaviRequestDataHolder $rd
     * @return string
     */
    public static function makeRequestDataJson(AgaviRequestDataHolder $rd) {
        return json_encode(self::makeRequestDataStruct($rd));
    }

    /**
     * Outputs the right data structure for the cronk script interface as json
     * @param AgaviRequestDataHolder $rd
     * @return null nothing
     */
    public static function echoJsonString(AgaviRequestDataHolder $rd)  {
        echo self::makeRequestDataJson($rd);
    }

}

?>