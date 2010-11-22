<?php

interface AppKitIAuthProvider {

	const AUTH_CREATE			= 'auth_create';
	const AUTH_UPDATE			= 'auth_update';
	const AUTH_RESUME			= 'auth_resume';
	const AUTH_GROUPS			= 'auth_groups';
	const AUTH_ENABLE			= 'auth_enable';
	const AUTH_AUTHORITATIVE	= 'auth_authoritative';
	const AUTH_MODE				= 'auth_mode';

	const MODE_DEFAULT			= 1;
	const MODE_SILENT			= 2;
	const MODE_BOTH				= 3;

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
	public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null);
	
	/**
	 * isAvailable
	 * 
	 * Checks if a user is available by the provider
	 * 
	 * @param mixed $uid
	 */
	public function isAvailable($uid, $authid=null);
	
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

	/**
	 * determineUsername
	 *
	 * If the provider has a change to 'guess'
	 * the username before login (SSO, HTTP basic auth)
	 * the provider get a change to tell that to
	 * the dispatcher
	 *
	 * @return string
	 */
	public function determineUsername();
}

?>