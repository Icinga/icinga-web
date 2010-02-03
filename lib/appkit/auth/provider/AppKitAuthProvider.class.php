<?php

abstract class AppKitAuthProvider extends AgaviParameterHolder {
	
	public function __construct() {
		
	}
	
	public function initializeAuthProvider (array $parameters = array ()) {
		$this->setParameters($parameters);
	}
	
	/**
	 * Returns a NsmUser record or null 
	 * @param string $username
	 * @return NsmUser
	 */
	public abstract function isUserAvailable($username);
	
	/**
	 * Returns true or false if a user is logged in!
	 * @param string $user
	 * @param string $passwd
	 * @return null
	 */
	public abstract function isAuthenticated(NsmUser $user, $password);
	
}

class AppKitAuthProviderException extends AppKitException {}

?>