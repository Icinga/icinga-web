<?php

class AppKitAuthProviderHttpBasic extends AppKitAuthProviderDatabase {
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory()
	 */
	public function initializeFactory(array $parameters=array ()) {
		
		// This is a default value!
		$this->setParameter('field', 'REMOTE_USER');
		
		parent::initializeFactory($parameters);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#shutdownFactory()
	 */
	public function shutdownFactory() {
		
	}
	
	public function getUsername() {
		$field = $this->getParameter('field');
		if (array_key_exists($field, $_SERVER)) {
			return $_SERVER[$field];
		}
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isUserAvailable()
	 */
	public function isUserAvailable($username) {
		return parent::isUserAvailable($this->getUsername());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isAuthenticated()
	 */
	public function isAuthenticated($username, $password) {
		if (($user = $this->isUserAvailable($this->getUsername())) && $user instanceof NsmUser) {
			return true;
		}
		return null;
	}
	
}

?>