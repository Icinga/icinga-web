<?php
class BPAddon_serviceModel extends BPAddonBaseModel
{
	protected $serviceName;
	protected $hostName;
	
	public function setServiceName($name) {
		$this->serviceName = $name;
	}
	public function setHostName($name) {
		$this->hostName = $name;
	}
	
	public function getServiceName() {
		return $this->serviceName;
	}
	public function getHostName() {
		return $this->hostName;
	}
	public function getConfigName() {
		return $this->getHostName().";".$this->getServiceName();
	}
	public function __construct(array $params = array()) {
		if(!empty($params))
			$this->__fromArray($params);
	} 
	
	public function __fromArray(array $params) {
		if(isset($params["service"]))
			$this->setServiceName(trim($params["service"]));
		if(isset($params["host"]))
			$this->setHostName(trim($params["host"]));
	}
	
	public function __toArray() {
		$arr = array();
		$arr["service"] = trim($this->getServiceName());
		$arr["host"] = trim($this->getHostName());
		return $arr;
	}
	
	public function __toConfig() {
		return $this->getConfigName();
	}
	
}