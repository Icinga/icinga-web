<?php

class AppKitAuthProviderLdap extends AppKitAuthProviderDatabase {
	
	const TYPE_MSAD		= 1;
	const TYPE_OPENLDAP	= 2;
	
	/**
	 * @var AppKitLdapTool
	 */
	private $ldap		= null;
	
	private $attribute_username = array (
		self::TYPE_MSAD		=> 'samaccountname',
		self::TYPE_OPENLDAP	=> 'uid',
	);
	
	public function __construct() {
		parent::__construct();
		$this->ldap = new AppKitLdapTool();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory()
	 */
	public function initializeFactory(array $parameters=array ()) {
		parent::initializeFactory($parameters);
		
		// Morph the ldap_type into a constant value
		$this->setParameter('ldap_type', AppKit::getConstant($this->getParameter('ldap_type')));
		
		// Try to setup some basics!
		$this->initLdap();
	}
	
	public function shutdownFactory() {
		$this->ldap->doShutdown();
	}
	
	/**
	 * Inits our ldap query object
	 * @return boolean
	 */
	private function initLdap() {
		if ($this->ldap->isValid() === false) {
			$this->ldap->setLdapDsn($this->getParameter('ldap_dsn'));
			$this->ldap->setCredentials($this->getParameter('ldap_binddn'), $this->getParameter('ldap_bindpw'));
			$this->ldap->setBaseDn($this->getParameter('ldap_basedn'));
			$this->ldap->doConnect();
			return true;
		}
	}
	
	/**
	 * Returns the username attribute name, depending on the
	 * ldap type
	 * @return atring
	 */
	private function getUsernameAttribute() {
		return $this->attribute_username[$this->getParameter('ldap_type')];
	}
	
	/**
	 * Returns the binddn of the user
	 * @param $user
	 * @return unknown_type
	 */
	private function getBindDn(array $user) {
		switch ($this->getParameter('ldap_type')) {
			//case self::TYPE_MSAD:
			//	return sprintf('%s@%s', $user['samaccountname'], $this->getParameter('ldap_domain'));
			//break;
			default:
				return $user['dn'];
			break;
		}
	}
	
	private function getLdapUser($username) {
		$this->initLdap();
		$ldap =& $this->ldap;
		
		$ldap_attributes = array('dn', 'cn', $this->getUsernameAttribute());
		
		$query = AppKitStringUtil::replaceMarkerArray($this->getParameter('ldap_filter_user'), array('username' => $username));
		
		$result = $ldap->doQuery($query);
		
		if ($ldap->resultCount($result) == 1 && ($ldap_user = $ldap->resultParse($result, $ldap_attributes))) {
			return $ldap_user;
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isUserAvailable()
	 */
	public function isUserAvailable($username) {		
		if ( ($ldap_user = $this->getLdapUser($username)) ) {
			$ldap_username = $ldap_user[ $this->getUsernameAttribute() ];
			// Load the user from database
			return parent::isUserAvailable($ldap_username);
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/auth/AppKitAuthProvider#isAuthenticated()
	 */
	public function isAuthenticated($username, $password) {
		
		if ( ($ldap_user = $this->getLdapUser($username)) ) {
			
			// Get the binddn
			$binddn = $this->getBindDn($ldap_user);
			
			// This our default, nothing!
			$auth_test = false;
			
			// Try to authenticate
			try {
				$auth_test = $this->ldap->doBind($binddn, $password);
			}
			catch (AppKitLdapToolException $e) {
				$auth_test = false;
			}
			
			// Shutdown the ldap for reactivating the next time
			$this->ldap->doShutdown();
			
			// If the bind was successfull, return the truth!
			if ($auth_test === true) return true;
			
		}
		
		return null;
	}
	
}

?>