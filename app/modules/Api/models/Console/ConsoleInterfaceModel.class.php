<?php

class ApiUnknownConsoleException extends AppKitException {};

class Api_Console_ConsoleInterfaceModel extends IcingaApiBaseModel implements IcingaConsoleInterface {
    protected $hosts = array();
    protected $hostname;
    protected $connections = array();
    public function getHostName() {
        return $this->hostname;
    }

    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        if (!isset($parameters["host"]) && !isset($parameters["icingaInstance"])) {
            $this->hostname = AccessConfig::getDefaultHostname();
        } else if (!isset($parameters["host"]) && isset($parameters["icingaInstance"])) {
            $this->hostname = AccessConfig::getHostnameByInstance($parameters["icingaInstance"]);
        }

        if(isset($parameters["host"]))
            $this->hostname = AccessConfig::getHostByName($parameters["host"]);
        $this->initConnection();
    }

    public function exec(IcingaConsoleCommandInterface $cmd) 	{
        if(empty($this->connections)) {
            return false;
        }
        foreach($this->connections as $name=>$connection) {

            $currentCmd = clone($cmd);
            $currentCmd->setHost($name);
            $currentCmd->setConnection($this);
            $connection->exec($currentCmd);

            $cmd->setReturnCode($currentCmd->getReturnCode());
            $cmd->setOutput($currentCmd->getOutput());
            if($cmd->getReturnCode() != 0)
                break;
        }
    }


    protected function initConnection() {
        
        $this->hosts = AccessConfig::getHostByName($this->hostname);

        if(isset($this->hosts["auth"])) {
            $result = array();
            $result[$this->hostname] = $this->hosts;
            $this->hosts = $result;
        }
        $this->setUpConsole();
    } 

    protected function setUpConsole() {
    
        foreach($this->hosts as $name=>$host) {
            $type = strtolower($host["auth"]["type"]);
            $type[0] = strtoupper($type[0]);
            $class = $type."ConsoleConnection";

            if (!class_exists($class,true)) {
                throw new ApiUnknownConsoleException("Unknown connection type: $type");
            }

            $this->connections[$name] = new $class($host);
        }
    }
};
