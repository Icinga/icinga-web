<?php

class AppKit_Auth_Provider_DatabaseModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {

	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null) {
		if ($user instanceof NsmUser && $user->user_id > 0) {
			
			$test_hash = hash_hmac(NsmUser::HASH_ALGO, $password, $user->user_salt);
			
			$this->log('Auth.Provider.Database: HASH(%s)', $test_hash, AgaviLogger::DEBUG);
			
			if ($test_hash === $user->user_password) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#isAvailable()
	 */
	public function isAvailable($uid, $authid=null) {
		
		$res = Doctrine_Query::create()
		->select('COUNT(u.user_id) as cnt')
		->from('NsmUser u')
		->where('u.user_name=? and user_disabled=?', array($uid, 0))
		->execute(null, Doctrine::HYDRATE_ARRAY);
		
		if ($res[0]['cnt'] !== 0) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#getUserdata()
	 */
	public function getUserdata($uid, $authid=false) {
		
	}
	
}

?>