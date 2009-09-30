<?php 

class AppKitCache extends AppKitBaseClass implements AppKitFactoryInterface {
	
	const COMMIT_POOL		= 1;
	const COMMIT_DISCRETE	= 2;
	
	const SERIALIZE_PHP		= 1;
	const SERIALIZE_NONE	= 2;
	
	private $regions				= array();
	private $provider				= null;
	
	private $cacheTransactionType	= false;
	private $cacheSerializeType		= false;
	private $cacheDefaultRegion		= null;
	private $cacheProviderName		= null;
	
	public function __construct() {
		
	}
	
	public function initializeFactory(array $parameters=array()) {
		
		
	}
	
	public function shutdownFactory() {
		
	}
	
	public function addRegion($region_name) {
		if (!array_key_exists($region_name, $this->regions)) {
			$this->regions[$region_name] = true;
			return true;
		}
		
		return false;
	}
	
	public function setCacheProvider(AppKitCacheProvider &$provider) {
		$this->provider =& $provider;
	}
	
}

class AppKitCacheException extends AppKitException {};

?>