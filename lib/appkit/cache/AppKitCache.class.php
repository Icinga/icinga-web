<?php 

abstract class AppKitCache extends AppKitBaseClass implements AppKitFactoryInterface {
	
	const COMMIT_POOL		= 1;
	const COMMIT_DISCRETE	= 2;
	
	const SERIALIZE_PHP		= 1;
	const SERIALIZE_NONE	= 2;
	
	protected $regions				= array();
	protected $data					= array();
	protected $config				= array();
	
	protected $cacheTransactionType	= false;
	protected $cacheSerializeType	= false;
	protected $cacheDefaultRegion	= null;
	protected $cacheDefaultLifetime	= 3600;
	protected $cacheBypass			= false;
	
	public function __construct() {
		
	}
	
	public function initializeFactory(array $parameters=array()) {
		
		if (array_key_exists('cacheDefaultRegion', $parameters)) {
			$this->setDefaultRegion($parameters['cacheDefaultRegion']);
		}
		
		if (array_key_exists('cacheTransactionType', $parameters)) {
			$this->cacheTransactionType = AppKit::getConstant($parameters['cacheTransactionType']);
		}
		
		if (array_key_exists('cacheSerializeType', $parameters)) {
			$this->cacheSerializeType = AppKit::getConstant($parameters['cacheSerializeType']);
		}
		
		if (array_key_exists('cacheDefaultLifetime', $parameters)) {
			$this->cacheDefaultLifetime = (int)$parameters['cacheDefaultLifetime'];
		}
		
		if (array_key_exists('cacheBypass', $parameters)) {
			$this->cacheBypass = (bool)$parameters['cacheBypass'];
		}
		
		
		$this->restoreData();
		
	}
	
	public function shutdownFactory() {
		if ($this->cacheTransactionType == self::COMMIT_POOL) {
			$this->storeAll();
		}
	}
	
	public function setDefaultRegion($region_name) {
		if (!$this->regionExists($region_name)) {
			$this->addRegion($region_name);
			$this->cacheDefaultRegion = $region_name;
		}
	}
	
	public function addRegion($region_name) {
		if (!array_key_exists($region_name, $this->regions)) {
			$this->regions[$region_name] = true;
			
			if (!array_key_exists($region_name, $this->data)) {
				$this->data[$region_name] = array();
			}
			
			if (!array_key_exists($region_name, $this->config)) {
				$this->config[$region_name] = array();
			}
			
			$this->config[$region_name]['added'] = time();
			$this->config[$region_name]['lifetime'] = $this->cacheDefaultLifetime;
			
			$this->restoreData($region_name);
			
			return true;
		}
		
		return false;
	}
	
	public function regionExists($region) {
		return array_key_exists($region, $this->regions);
	}
	
	public function setValue($name, $value, $region=null) {
		
		if ($this->cacheBypass === true) {
			return true;
		}
		
		if ($region===null) $region=$this->cacheDefaultRegion;
		if (!$this->regionExists($region)) {
			throw new AppKitCacheException('Region %s does not exist!', $region);
		}
		
		$this->data[$region][$name] = $value;
		
		if ($this->cacheTransactionType == self::COMMIT_DISCRETE) {
			$this->storeData();
		}
		
		return true;
	}
	
	public function getValue($name, $default=null,$region=null) {
		
		if ($this->cacheBypass === true) {
			return $default;
		}
		
		if ($region===null) $region=$this->cacheDefaultRegion;
		
		if (!$this->regionExists($region)) {
			throw new AppKitCacheException('Region %s does not exist!', $region);
		}
		
		if (array_key_exists($name, $this->data[$region])) {
			return $this->data[$region][$name];
		}
		else {
			return $default;
		}
	}
	
	public function loadAll() {
		foreach ($this->regions as $name=>$garbage) {
			$this->restoreData($name);
		}
	}
	
	public function storeAll() {
		foreach ($this->regions as $name=>$garbage) {
			$this->storeData($name);
		}
	}
	
	protected function storeData($region_name) {
		if (!array_key_exists('created', $this->config[$region_name])) {
			$this->config[$region_name]['created'] = time();
		}
	}
	
	protected function restoreData($region_name) {
		
	}
	
	protected function removeRegionData($region_name) {
		if ($this->regionExists($region_name)) {
			unset($this->data[$region_name]);
			unset($this->config[$region_name]['created']);
			$this->data[$region_name] = array ();
			return true;
		}
	}
	
	protected function getRegionData($region_name) {
		if ($this->regionExists($region_name)) {
			return ($this->data[$region_name]);
		}
		
		return null;
	}
	
	protected function getRegionRawData($region_name) {
		$raw = array (
			'data'		=> $this->getRegionData($region_name),
			'config'	=> $this->getRegionConfig($region_name)
		);
		
		return serialize($raw);
	}
	
	protected function getRegionConfig($region_name) {
		if ($this->regionExists($region_name)) {
			return ($this->config[$region_name]);
		}
		
		return null;
	}
	
	protected function setRegionData($region_name, array $data) {
		if ($this->regionExists($region_name)) {
			$this->data[$region_name] = $data;
			return true;
		}
		
		throw new AppKitCacheException('Region %s does not exist!', $region_name);
	}
	
	protected function setRegionConfig($region_name, array $config) {
		if ($this->regionExists($region_name)) {
			$this->config[$region_name] = $config;
			return true;
		}
		
		throw new AppKitCacheException('Region %s does not exist!', $region_name);
	}
	
	protected function setRegionRawData($region_name, $data) {
		$misc = unserialize($data);
		if (array_key_exists('data', $misc) && array_key_exists('config', $misc)) {
			$this->setRegionConfig($region_name, $misc['config']);
			$this->setRegionData($region_name, $misc['data']);
			return true;
		}
		
		throw new AppKitCacheException('Integrity error for region %s', $region_name);
	}
	
}

class AppKitCacheException extends AppKitException {};

?>