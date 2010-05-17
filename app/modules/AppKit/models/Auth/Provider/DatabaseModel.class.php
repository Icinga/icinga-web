<?php

class AppKit_Auth_Provider_DatabaseModel extends AppKitBaseModel implements AppKitIAuthProvider {

	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser &$user, $password) {
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#isAvailable()
	 */
	public function isAvailable($uid) {
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#getUserdata()
	 */
	public function getUserdata($uid) {
		
	}
	
}

?>