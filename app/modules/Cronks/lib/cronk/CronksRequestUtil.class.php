<?php

class CronksRequestUtil {

    protected static $requestAttributes = array(
            'cmpid', 'parentid', 'stateuid', 'state'
                                          );

    public static function makeRequestDataStruct(AgaviRequestDataHolder $rd) {
        $data = array();
        foreach(self::$requestAttributes as $attr) {
            if($rd->hasParameter($attr)) {
                $data[$attr] = $rd->getParameter($attr);
            }
        }
        return $data;
    }

    public static function makeRequestDataJson(AgaviRequestDataHolder $rd) {
        return json_encode(self::makeRequestDataStruct($rd));
    }

    public static function echoJsonString(AgaviRequestDataHolder $rd)  {
        echo self::makeRequestDataJson($rd);
    }

}

?>