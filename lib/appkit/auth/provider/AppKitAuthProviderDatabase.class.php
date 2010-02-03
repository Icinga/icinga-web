<?php

class AppKitAuthProviderDatabase extends AppKitAuthProvider {
	
	/**
	 * This is only a dummy, to see how intialization works ...
	 * 
	 * 
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory()
	 */
	public function initializeFactory(array $parameters=array ()) {
		$this->setParameter('initialized', true);
		parent::initializeFactory($parameters);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isUserAvailable()
	 */
	public function isUserAvailable($username) {
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isAuthenticated()
	 */
	public function isAuthenticated(NsmUser $user, $password) {
		if ($user instanceof NsmUser && $user->user_id > 0) {
			$test_hash = hash_hmac(NsmUser::HASH_ALGO, $password, $user->user_salt);
			if ($test_hash === $user->user_password) {
				return true;
			}
		}
		
		return false;
	}
	
}

?>