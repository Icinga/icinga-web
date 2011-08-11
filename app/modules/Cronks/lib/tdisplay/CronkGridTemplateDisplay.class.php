<?php

class CronkGridTemplateDisplay {

    private static $instance = null;

    /**
     * @var AgaviContext
     */
    protected static $context = null;

    /**
     * @var Web_Icinga_ApiContainerModel
     */
    protected static $api = null;


    public function __construct() {

        if (self::$context === null) {
            self::$context = AgaviContext::getInstance();
        }

        if (self::$api === null) {
            self::$api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
        }

    }

    protected function wrapImagePath($path) {
        return AgaviConfig::get('org.icinga.appkit.web_path'). $path;
    }

    /**
     * @return AgaviContext
     */
    protected function getContext() {
        return self::$context;
    }

    /**
     * @return Web_Icinga_ApiContainerModel
     */
    protected function getApi() {
        return self::$api;
    }

}

?>
