<?php

class agaviDBExtractorTask extends Task {
	protected $src;
	protected $type;
	protected $user;
	protected $pass;
	protected $db;
	protected $host;
	protected $port;
	protected $toRef;
	
	protected static $__SUPPORTED_DRIVERS = array("mysql","pgsql","oracle");
	protected static $__EXPERIMENTAL_DRIVERS = array("pgsql","oracle");
	public function setToref($refname) {
		$this->toRef = $refname;
	}
	public function getToRef() {
		return $this->toRef;
	}
	
	public function getSrc() {
		return $this->src;
	}
	public function setSrc($src) {
		if(!file_exists($src))
			throw new BuildException("Invalid Database source.".
				"Please check if ".$src." is a valid path to the icinga databases.xml (located at %icinga-web%/app/config/)");

		$this->src = $src;
	}
	
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		if(!in_array($type,self::$__SUPPORTED_DRIVERS))
			throw new BuildException("Driver ".$type." is not supported (yet)!");
		if(in_array($type,self::$__EXPERIMENTAL_DRIVERS))
			echo "\n**************************WARNING***********************".
				 "\n The selected DB driver is only experimental supported! ".
				 "\n This means you *could* experience problems.".
			     "\n Please check https://dev.icinga.org/projects/icinga-web/issues".
				 "\n if you encounter problems and report a bug! Thank you!".
				 "\n********************************************************\n";
		$this->type = $type;				  
	}
	
	public function getUser() {
		return $this->user;
	}	
	public function setUser($user) {
		$this->user = $user;
	}
	
	public function getPass() {
		return $this->pass;
	}
	public function setPass($pass) {
		$this->pass = $pass;
	} 
	
	public function getDb() {
		return $this->db;
	}
	public function setDb($db) {
		$this->db = $db;
	}
	public function getHost() {
		return $this->host;
	}
	public function setHost($host) {
		$this->host = $host;
	}
	public function getPort() {
		return $this->port;
	}
	public function setPort($port) {
		$this->port = $port;
	}
	
	public function main() {
		$xml = simplexml_load_file($this->getSrc());
		if(!$xml instanceof SimpleXMLElement)
			throw new BuildException("An error occured while parsing database.xml!");		

		$xml->registerXPathNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
		$xml->registerXPathNamespace("default","http://agavi.org/agavi/config/parts/databases/1.0");

		$db = $xml->xpath('//default:database[@name="icinga_web"]//ae:parameter[@name="dsn"]');
		if(!$db || count($db)<1)
			throw new BuildException("Could not find database icinga_web in databases.xml");

		$dsn = (String) $db[0];
		$this->splitDSN($dsn);
		$this->buildRef();
	}
	
	public function splitDSN($dsn) {
		$splitReg = "/(.*):\/\/(.*):(.*)@(.*):(.*)\/(.*)/";
		$parts = array();
		preg_match($splitReg,$dsn,$parts);
		if(count($parts) != 7)
			throw new BuildException("Could not parse dsn ".$dsn);
		
		$this->setType($parts[1]);
		if(!$this->getUser())
			$this->setUser($parts[2]);
		if(!$this->getPass())
			$this->setPass($parts[3]);
		$this->setHost($parts[4]);
		$this->setPort($parts[5]);
		$this->setDb($parts[6]);
	}
	
	public function buildRef() {
		$dsn = $this->getType()."://".$this->getUser().":".$this->getPass()."@".$this->getHost().":".$this->getPort()."/".$this->getDb();
	
		$this->project->setProperty($this->getToRef(),$dsn);
	}
}
