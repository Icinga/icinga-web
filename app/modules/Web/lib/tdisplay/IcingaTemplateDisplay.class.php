<?php

class IcingaTemplateDisplay extends AppKitSingleton {

    /**
     * @var AgaviContext
     */
    protected $context = null;

    /**
     * @var Web_Icinga_ApiContainerModel
     */
    protected $api = null;

    public function __construct() {
        parent::__construct();
        $this->context = AgaviContext::getInstance(AgaviConfig::get('core.default_context'));
        $this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');

    }

    protected function wrapImagePath($path) {
        return AgaviConfig::get('org.icinga.appkit.web_path'). $path;
    }

    /**
     * @return AgaviContext
     */
    protected function getContext() {
        return $this->context;
    }

    /**
     * @return Web_Icinga_ApiContainerModel
     */
    protected function getApi() {
        return $this->api;
    }

}

?>