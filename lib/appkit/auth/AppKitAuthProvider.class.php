<?php

abstract class AppKitAuthProvider extends AppKitFactory {
	
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
	public abstract function isAuthenticated($user, $passwd);
	
}

class AppKitAuthProviderException extends AppKitException {}

?>