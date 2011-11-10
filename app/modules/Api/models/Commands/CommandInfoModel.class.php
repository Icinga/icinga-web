<?php

class Api_Commands_CommandInfoModel extends IcingaApiBaseModel implements AgaviISingletonModel {

    private $config = array();

    /**
     * @var AppKitSecurityUser
     */
    private $user = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/icingaCommands.xml'));

        $this->user = $context->getUser();

        if ($this->user->getNsmUser()->hasTarget('IcingaCommandRestrictions')) {
            $this->filterCommands($this->config);
        }
        
    }

    private function filterCommands(&$array) {
        foreach ($array as $key=>$val) {
            if (isset($val['isSimple']) && $val['isSimple'] !== 'true') {
                unset($array[$key]);
            }
        }
        
        return $array;
    }

    public function getInfo($commandName=null) {

        if ($commandName !== null && $this->hasCommand($commandName)) {
            return $this->config[$commandName];
        }

        return $this->config;
    }
    
    public function hasCommand($commandName) {
        return array_key_exists($commandName, $this->config);
    }
}