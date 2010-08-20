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

	public function guessUsername() {
		foreach ($this->provider_keys as $pid) {
			$provider = $this->getProvider($pid);
			if ( ( $name = $provider->determineUsername() ) !== null ) {
				return $name;
			}
		}
		return false;
	}

	public function hasSilentProvider() {
		return $this->hasProviderWithMode(AppKitIAuthProvider::MODE_SILENT);
	}

	private function hasProviderWithMode($mode) {
		foreach ($this->provider_keys as $pid) {
			$provider = $this->getProvider($pid);
			if ($provider->testBinary(AppKitIAuthProvider::AUTH_MODE, $mode)) {
				return true;
			}
		}
		return false;
	}
	
	public function &doAuthenticate($username, $password) {

		/**
		 * 1. Find the user
		 * 2. If not found, try to import
		 * 3. If the user is there, try to auth
		 */

		$this->log('Auth.Dispatch: Starting authenticate (username=%s)', $username, AgaviLogger::DEBUG);

		$user = $this->findUser($username);

		if ($user === null) {
			$user = $this->importUser($username);
		}
		
		if ($user instanceof NsmUser && $user->user_id>0) {

			$this->log('Auth.Dispatch: Userdata found in db (uid=%d)', $user->user_id, AgaviLogger::DEBUG);

			$provider = $this->getProvider($user->user_authsrc);
				
			// We've got a provider
			if (is_object($provider) && $provider instanceof AppKitIAuthProvider) {

				// The id we authenticate against
				$authid = $user->getAuthId();
			
				// Check if the user still available
				if ($provider->isAvailable($username, $authid)) {

					$this->log('Auth.Dispatch: Authoritative provider found (provider=%s, authid=%s)', $provider->getProviderName(), $authid, AgaviLogger::DEBUG);
					
					// Update the userprofile
					if ($provider->canUpdateProfile()) {
						$this->updateProfile($user, $provider);
					}
	
					// Check password
					if ($provider->isAuthoritative() && $provider->doAuthenticate($user, $password)) {
						$this->log('Auth.Dispatch: Successfull authentication (provder=%s)', $provider->getProviderName(), AgaviLogger::DEBUG);
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

		$this->log('Auth.Dispatch: User cound not authorized (username=%s)', $username, AgaviLogger::DEBUG);

		throw new AgaviSecurityException("Authentification of $username failed!");
	}
	
	private function authentificateOthers(NsmUser &$user, $not=null) {
		
	}
	
	private function updateProfile(NsmUser &$user, AppKitIAuthProvider &$provider) {
		
	}
	
	private function importUser($username) {

		$this->log('Auth.Dispatch: User %s not found, try to import', $username, AgaviLogger::DEBUG);
		
		foreach ($this->provider_keys as $pid) {
			$provider = $this->getProvider($pid);
			if ($provider->canCreateProfile()) {

				$this->log('Auth.Dispatch/import: %s will map the userdata', $provider->getProviderName(), AgaviLogger::DEBUG);

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
				 	
					$this->log(
						'Auth.Dispatch/import: user %s successfully imported (user_id=%d, provider=%s)',
						$username,
						$user->user_id,
						$provider->getProviderName(),
						AgaviLogger::DEBUG
					);

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
		// check if the name is an auth_key
		$user = Doctrine::getTable('NsmUser')->findBySql('user_authkey=?', array($username));
		if ($user->count() == 1) {
			return $user->getFirst();
		}
		
		return null;
	}
	
}

?>