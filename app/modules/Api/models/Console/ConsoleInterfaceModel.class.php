<?php

class ApiUnknownConsoleException extends AppKitException {};

class Api_Console_ConsoleInterfaceModel extends IcingaApiBaseModel implements IcingaConsoleInterface {
    protected $host;
    protected $hostname;
    protected $connection;
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
        if ($this->connection == NULL) {
            return false;
        }

        $cmd->setConnection($this);
        $this->connection->exec($cmd);
    }


    protected function initConnection() { 
        $this->host = AccessConfig::getHostByName($this->hostname);
      
        $this->setUpConsole();
    } 

    protected function setUpConsole() {
        
        $type = strtolower($this->host["auth"]["type"]);
        $type[0] = strtoupper($type[0]);
        $class = $type."ConsoleConnection";

        if (!class_exists($class,true)) {
            throw new ApiUnknownConsoleException("Unknown connection type: $type");
        }

        $this->connection = new $class($this->host);
    }
};
