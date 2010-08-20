<?php


class AppKit_Auth_Provider_AuthKeyModel extends AppKitAuthProviderBaseModel  implements AppKitIAuthProvider{

	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser &$user, $password) {
		if ($user instanceof NsmUser && $user->user_id > 0) {
			return true;
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
		->where('u.user_name=? and user_disabled=? and user_authsrc = ?', array($uid, 0,'auth_key'))
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