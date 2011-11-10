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
            $this->filterCommandsByUser($this->config);
        }
    }

    private function filterCommandsByUser(&$array) {
        static $filterFn = null;
        
        if ($filterFn === null) {
            $filterFn = create_function('$arr', 'return (isset($arr["isSimple"]) && $arr["isSimple"] == "true");');
        }
        
        $array = array_filter($array, $filterFn);
    }
    
    public function filterCommandByType($type) {
        $filterFn = create_function('$arr', 'return ($arr["type"] === "'. $type. '");');
        return array_filter($this->config, $filterFn);
    }
    
    public function getInfo($commandName=null) {
        return $this->filterCommandsByName($commandName);
    }
    
    public function filterCommandsByName($commandName=null) {

        if ($commandName !== null && $this->hasCommand($commandName)) {
            return $this->config[$commandName];
        }

        return $this->config;
    }
    
    public function getAll() {
        return $this->config;
    }
    
    public function hasCommand($commandName) {
        return array_key_exists($commandName, $this->config);
    }
}