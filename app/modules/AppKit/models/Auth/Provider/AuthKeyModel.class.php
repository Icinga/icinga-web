<?php


class AppKit_Auth_Provider_AuthKeyModel extends AppKitAuthProviderBaseModel  implements AppKitIAuthProvider{

	protected $parameters_default = array (
		AppKitIAuthProvider::AUTH_MODE => AppKitIAuthProvider::MODE_SILENT
	);
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null) {
		if ($user instanceof NsmUser && $user->user_id > 0) {
			if ($user->user_authkey === $username) {
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
		->where('u.user_authkey=? and user_disabled=? and user_authsrc = ?', array($uid, 0,'auth_key'));

		$res = $res->execute(null, Doctrine::HYDRATE_ARRAY);
		
		if (isset($res[0]['cnt']) && $res[0]['cnt'] === 1) {
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