<?php

interface AppKitIAuthProvider {
	
	/**
	 * doAuthenticate
	 * 
	 * Tries to Authenticate an user
	 * This means the user is available on the provider
	 * and ready to ask for authentication
	 * 
	 * @param NsmUser $user
	 * @param string $password
	 * @return boolean
	 */
	public function doAuthenticate(NsmUser &$user, $password);
	
	/**
	 * isAvailable
	 * 
	 * Checks if a user is available by the provider
	 * 
	 * @param mixed $uid
	 */
	public function isAvailable($uid);
	
	/**
	 * getUserdata
	 * 
	 * Returns the userdata if allowed to import or
	 * update the users profile data
	 * 
	 * Enter description here ...
	 * @param mixed $uid
	 * @param boolean $authid
	 * @return array
	 */
	public function getUserdata($uid, $authid=false);
}

?>