<?php
class ApiUnknownHostException extends AppKitException {};
class ApiUnknownConsoleException extends AppKitException {};

class Api_Console_ConsoleInterfaceModel extends IcingaApiBaseModel {
    protected $host;
    protected $connection;
    protected $access = array(
                            "r" => array(
                                "folders" => array(),
                                "files" => array()
                            ),
                            "w" => array(
                                "folders" => array(),
                                "files" => array()
                            ),
                            "x" => array(
                                "folders" => array(),
                                "files" => array()
                            )
                        );

    public function getHostName() {
        return $this->host;
    }

    public function getAccessDefinition() {
        return $this->access;
    }

    public function initialize(AgaviContext $context, array $parameters = array()) {
        if(!isset($parameters["host"])) {
            $parameters["host"] = AgaviConfig::get("modules.api.access.defaults.host","localhost");
        }

        $this->initConnection($parameters["host"]);
    }

    public function exec(Api_Console_ConsoleCommandModel $cmd) 	{
        if($this->connection == NULL) {
            return false;
        }

        $cmd->setConnection($this);
        $this->connection->exec($cmd);
    }

    protected function initConnection($name) {
        $hostList= AgaviConfig::get("modules.api.access.hosts",array());

        if(!isset($hostList[$name])) {
            throw new ApiUnknownHostException("Host ".$name." doesn't exists in api module.xml");
        }

        $this->host = $name;
        $this->updateAccess();
        $this->setUpConsole();
    }

    protected function updateAccess() {
        $hostList = AgaviConfig::get("modules.api.access.hosts",array());
        $defaults = AgaviConfig::get("modules.api.access.defaults",array());

        if($hostList[$this->host]["access"]["useDefaults"]) {
            $this->__updateAccessVals($defaults);
        }

        $this->__updateAccessVals($hostList[$this->host]);
    }

    protected function __updateAccessVals(array $container) {
        if(isset($container["access"]["r"])) {
            $this->access["r"] = array_merge_recursive($container["access"]["r"],$this->access["r"]);
        }

        if(isset($container["access"]["w"])) {
            $this->access["w"] = array_merge_recursive($container["access"]["w"],$this->access["r"]);
        }

        if(isset($container["access"]["rw"])) {
            $this->access["w"] = array_merge_recursive($container["access"]["rw"],$this->access["w"]);
            $this->access["r"] = array_merge_recursive($container["access"]["rw"],$this->access["r"]);
        }

        if(isset($container["access"]["x"])) {
            $this->access["x"] = array_merge_recursive($container["access"]["x"],$this->access["x"]);
        }
    }


    protected function setUpConsole() {
        $hostList = AgaviConfig::get("modules.api.access.hosts",array());
        $type = "local";

        if(isset($hostList[$this->host]["type"])) {
            $type = $hostList[$this->host]["type"];
        }

        $type = strtolower($type);
        $type[0] = strtoupper($type[0]);
        $class = $type."ConsoleConnection";

        if(!class_exists($class,true)) {
            throw new ApiUnknownConsoleException("Unknown connection type: $type");
        }

        $this->connection = new $class($hostList[$this->host]);
    }
};
