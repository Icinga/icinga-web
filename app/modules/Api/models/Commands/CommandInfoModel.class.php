<?php

class Api_Commands_CommandInfoModel extends IcingaApiBaseModel implements AgaviISingletonModel {
    
    private $config = array();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/icingaCommands.xml'));
    }
    
    public function getInfo($commandName=null) {
      
        if ($commandName !== null && array_key_exists($commandName, $this->config)) {
            return $this->config[$commandName];
        }
        
        return $this->config;
    }
}

?>