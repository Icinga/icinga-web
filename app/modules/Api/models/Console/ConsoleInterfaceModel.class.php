<?php
class ApiUnknownHostException extends AppKitException {};

class Api_Console_ConsoleInterfaceModel extends IcingaApiBaseModel {
	protected $host;
	protected $connection;
	protected $access = array(
		"r" => array(),
		"w" => array(),
		"x" => array()
	);
	
	public function getHostName() {
		return $this->host;
	}

	public function getAccessDefinition() {
		return $this->access;	
	}
	
	public function initialize($context,array $parameters = array()) {
		if(!isset($parameters["host"])) {
			$parameters["host"] = AgaviConfig::get("modules.api.access.defaults.host","localhost");
		}
		$this->initConnection($parameters["host"]);
	}
	
	public function submitCommand(Api_Console_ConsoleInterfaceModel $cmd) 	{
		
	}
	
	protected function initConnection($name) {
		$hostList= AgaviConfig::get("modules.api.access.hosts",array());
		
		if(!isset($hostList[$name]))
			throw new ApiUnknownHostException("Host ".$name." doesn't exists in api module.xml");
		$this->host = $name;
		$this->updateAccess();
		$this->setUpConsole();
	}
	
	protected function updateAccess() {		
		$hostList = AgaviConfig::get("modules.api.access.hosts",array());
		$defaults = AgaviConfig::get("modules.api.access.defaults",array());
		$host = $hostList[$this->host];
	
	}

	protected function setUpConsole() {

	}
};
