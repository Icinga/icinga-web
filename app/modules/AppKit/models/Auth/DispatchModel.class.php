<?php

class AppKit_Auth_DispatchModel extends AppKitBaseModel implements AgaviISingletonModel {
	
	const NS_PROVIDER		= 'modules.appkit.auth.provider';
	const NS_DEFAULTS		= 'modules.appkit.auth.defaults';
	
	private $config			= array ();
	private $config_def		= array ();
	
	private $provider		= array ();
	private $provider_keys	= array ();
	
	/**
	 * Loads the provider config into an array for later use
	 * Enter description here ...
	 * @throws AgaviSecurityException
	 */
	private function loadProviderConfig() {
		$this->config_def = AgaviConfig::get(self::NS_DEFAULTS, array ());
		
		foreach (AgaviConfig::get(self::NS_PROVIDER, array ()) as $pid=>$pconfig) {
			
			$pconfig = array_merge($this->config_def, $pconfig);
			
			if (!$pconfig['auth_enable'] == true) {
				continue;
			}
			
			if (!isset($pconfig['auth_module']) || !isset($pconfig['auth_provider'])) {
				throw new AgaviSecurityException('auth_module or auth_provider missing for provider '. $pid);
			}
			
			$this->config[$pid] = $pconfig;
			$this->provider_keys[] = $pid;
		}
		
		if (!count($this->config)) {
			throw new AgaviSecurityException('No providers defined!');
		}
		
		return true;
	}
	
	/**
	 * Returns an provider object, if the object
	 * is not initialized, also creating an instance
	 * 
	 * @param string $id
	 * @return AppKitIAuthProvider
	 */
	private function getProvider($id) {
		if ($this->providerExists($id)) {
			
			if (!isset($this->provider[$id])) {
				
				$pconf = $this->config[$id];
				
				$this->provider[$id] = $this->getContext()->getModel(
					$pconf['auth_provider'], $pconf['auth_module'], $pconf
				);
				
			}
			
			return $this->provider[$id];
		}
		
		throw new AgaviSecurityException('Provider '. $id. ' does not exist!');
	}
	
	public function providerExists($id) {
		return array_key_exists($id, $this->config);
	}
	
	public function initialize(AgaviContext $context, array $parameters=array()) {
		parent::initialize($context, $parameters);
		$this->loadProviderConfig();
		
		$p = $this->getProvider('internal');
		$p->getParameters();
	}
	
}

?>