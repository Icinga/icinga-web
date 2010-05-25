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
			
			$pconfig['name'] = $pid;
			
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
	}
	
	public function &doAuthenticate($username, $password) {
		
		/**
		 * 1. Find the user
		 * 2. If not found, try to import
		 * 3. If the user is there, try to auth
		 */
		
		$user = $this->findUser($username);
		
		if ($user === null) {
			$user = $this->importUser($username);
			return false;
		}
		
		if ($user instanceof NsmUser && $user->user_id>0) {
			$provider = $this->getProvider($user->user_authsrc);
			
			// We've got a provider
			if (is_object($provider) && $provider instanceof AppKitIAuthProvider) {
				
				// The id we authenticate against
				$authid = $user->getAuthId();
				
				// Check if the user still available
				if ($provider->isAvailable($authid)) {
					
					// Update the userprofile
					if ($provider->canUpdateProfile()) {
						$this->updateProfile($user, $provider);
					}
					
					// Check password
					if ($provider->isAuthoritative() && $provider->doAuthenticate($user, $password)) {
						return $user;
					}
					
				}
				
				// Let others try authentification
				if ($provider->resumeAuthentification()) {
					if ($this->authentificateOthers($user, $provider->getProviderName()) == true) {
						return $user;
					}
				}
			}
		}
		
		throw new AgaviSecurityException("Authentification of $username failed!");
	}
	
	private function authentificateOthers(NsmUser &$user, $not=null) {
		
	}
	
	private function updateProfile(NsmUser &$user, AppKitIAuthProvider &$provider) {
		
	}
	
	private function importUser($username) {
		foreach ($this->provider_keys as $pid) {
			$provider = $this->getProvider($pid);
			if ($provider->canCreateProfile()) {
				 $data = $provider->getUserdata($username, false);
				 if (is_array($data)) {
				 	
				 	$user = new NsmUser();
				 	$user->fromArray($data, false);
				 	
				 	$groups = $provider->getDefaultGroups();
				 	if (is_array($groups)) {
				 		foreach ($groups as $group_name) {
				 			$group = Doctrine::getTable('NsmRole')->findOneBy('role_name', $group_name);
				 			$user->NsmRole[] = $group;
				 		}
				 	}
				 	
				 	$user->save();
				 	
				 	return $user;
				 }
			}
		}
	}
	
	/**
	 * Tries to find an existing user
	 * @param string $username
	 * @return NsmUser
	 */
	private function findUser($username) {
		$user = Doctrine::getTable('NsmUser')->findBySql('user_name=?', array($username));
		if ($user->count() == 1) {
			return $user->getFirst();
		}
		return null;
	}
	
}

?>