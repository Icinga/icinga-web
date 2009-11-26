<?php

class IcingaData extends AppKitFactory {
	
	/**
	 * @var IcingaApi
	 */
	private $api = null;
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory()
	 */
	public function initializeFactory(array $parameters=array()) {
		parent::initializeFactory($parameters);
		
		if (!$this->getParameter('api_file')) throw new AppKitFactoryException('api_file does not exist!');
		
		if (!class_exists('IcingaApi')) {
			$this->includeApiFile($this->getParameter('api_file'));
		}
		
		$this->setParameter('api_type', AppKit::getConstant($this->getParameter('api_type')));
		
		$this->initApi();
		
		return true;
	}
	
	/**
	 * Create a api instance (creating and configure object)
	 * @return boolean	always true
	 */
	private function initApi() {
		$config = array ();
		foreach ($this->getParameters() as $key=>$val) {
			if (preg_match('@^config_@', $key)) {
				$key = preg_replace('@^config_@', null, $key, 1);
				$config[$key] = $val;
			}
		}
		
		$this->api = IcingaApi::getConnection($this->getParameter('api_type'), $config);
	}
	
	/**
	 * Include the api file
	 * @param string $file
	 * @return boolean
	 */
	private function includeApiFile($file) {
		$re = require_once($file);
		if (!$re) throw new AppKitFactoryException('Could not include the api file!');
		return true;
	}
	
	/**
	 * Returns the API for work
	 * @return IcingaApi
	 */
	public function API() {
		return $this->api;
	}
	
}

?>