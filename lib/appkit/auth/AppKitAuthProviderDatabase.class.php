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
		
		if (!$username || !strlen($username)>0) return null;
		
		$res = Doctrine_Query::create()
		->from('NsmUser')
		->andWhere('user_disabled=? and user_name=?', array(false, $username))
		->execute();
		
		if (($user = $res->getFirst()) instanceof NsmUser) {
			return $user;
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isAuthenticated()
	 */
	public function isAuthenticated($username, $password) {
		if (($user = $this->isUserAvailable($username)) !== null) {
			$test_hash = hash_hmac(NsmUser::HASH_ALGO, $password, $user->user_salt);
			if ($test_hash === $user->user_password) {
				return true;
			}
		}
		
		return false;
	}
	
}

?>