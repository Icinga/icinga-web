<?php

class AppKitSecurityUser extends AgaviRbacSecurityUser {
	
	const USEROBJ_ATTRIBUTE = 'userobj';
	
	/**
	 * Login method, uses the AppKitAuthProvider to determine if this is correct
	 * @param string $username
	 * @param string $password
	 * @param boolean $isHashedPassword This is not needed at the moment, maybe for cookie autologin later
	 * @return boolean if the login was successfull
	 * @throws AppKitSecurityUserException
	 * @author Marius Hein
	 */
	public function doLogin($username, $password, $isHashedPassword=false) {
		
		// Okay, try to get our provider
		$provider = AppKitFactories::getInstance()->getFactory('AuthProvider');
		
		// Check the provider for the user existing
		if (($user = $provider->isUserAvailable($username)) !== null && $user instanceof NsmUser) {

			// Ask our provider if the existing user is authenticated
			if ($provider->isAuthenticated($username, $password) === true) {
				
				// Start from scratch
				$this->clearCredentials();
				
				// Set authenticated
				$this->setAuthenticated(true);
				
				// Load the corresponding db (Nsm-) user into the session
				$this->loadUserAttribute($user);
				
				// Grant related roles
				$this->applyDoctrineUserRoles($user);
				
				// Give notice
				$this->getContext()->getLoggerManager()
				->logInfo('User %s (%s) logged in!', $username, $user->givenName());
				
				// Return true
				return true;
			}
		}

		// Throw some warning into 
		$this->getContext()->getLoggerManager()
		->logWarn('Userlogin by %s failed!', $username);
		
		// And notify the caling component (if they want ...)
		throw new AppKitSecurityUserException("User '$username' is not known with this credentials");
	}
	
	/**
	 * Initiate the logout
	 * @return boolean
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public function doLogout() {
		$this->clearCredentials();
		$this->setAuthenticated(false);
		
		// Give notice
		$this->getContext()->getLoggerManager()
		->logInfo('User %s (%s) logged out!', $this->getAttribute('userobj')->user_name, $this->getAttribute('userobj')->givenName());
		
		return true;
	}
	
	/**
	 * This adds attributes to our session (At the moment only the NsmUser)
	 * @param NsmUser $user
	 * @return true always!
	 * @author Marius Hein
	 */
	private function loadUserAttribute(NsmUser &$user) {
		$this->setAttributeByRef('userobj', &$user);
		return true;
	}
	
	/**
	 * Applying the roles the the agavi rbac struct
	 * @param NsmUser $user
	 * @return boolean always true
	 * @author Marius Hein
	 */
	private function applyDoctrineUserRoles(NsmUser &$user) {
		foreach ($user->NsmRole as $role) {
			$this->grantRole($role->role_name);
		}
		
		return true;
	}
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
	}
	
	/**
	 * Returns the doctrine user object
	 * @return NsmUser
	 * @throws AppKitDoctrineException
	 * @author Marius Hein
	 */
	public function getNsmUser() {
		$user =& $this->getAttribute(self::USEROBJ_ATTRIBUTE);
		if ($user instanceof NsmUser) {
			return $user;
		}
		
		throw new AppKitDoctrineException('User attribute is no a NsmUser!');
	}
	
	/**
	 * reduced call for setPref within NsmUser
	 * @param string $key
	 * @param mixed $val
	 * @param boolean $overwrite
	 * @param boolean $blob
	 * @return mixed
	 * @author Marius Hein
	 */
	public function setPref($key, $val, $overwrite = true, $blob = false) {
		return $this->getNsmUser()->setPref($key, $val, $overwrite, $blob);
	}
	
	/**
	 * reduced call for getPrefVal within NsmUser
	 * @param string $key
	 * @param mixed $default
	 * @param boolean $blob
	 * @return mixed
	 * @author Marius Hein
	 */
	public function getPrefVal($key, $default=null, $blob = false) {
		return $this->getNsmUser()->getPrefVal($key, $default, $blob);
	}
	
	/**
	 * reduced call for delPref within NsmUser
	 * @param string $key
	 * @return boolean
	 * @author Marius Hein
	 */
	public function delPref($key) {
		return $this->getNsmUser()->delPref($ley);
	}
}

class AppKitSecurityUserException extends AppKitException {}

?>