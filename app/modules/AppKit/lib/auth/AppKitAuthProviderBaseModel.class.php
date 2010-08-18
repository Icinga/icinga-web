<?php

class AppKitAuthProviderBaseModel extends IcingaBaseModel {

	protected $parameters_default = array (
		AppKitIAuthProvider::AUTH_MODE => AppKitIAuthProvider::MODE_DEFAULT
	);

	public function  initialize(AgaviContext $context, array $parameters = array()) {
		$parameters = $parameters + $this->parameters_default;

		parent::initialize($context, $parameters);

		$this->initializeProvider();

		$this->log('Auth.Provider: Object (name=%s) initialized', $this->getProviderName(), AgaviLogger::DEBUG);
	}

	protected function initializeProvider() {}

	protected function loadUserByDQL($value, $dql='user_name=?') {
		$users = Doctrine::getTable('NsmUser')->findByDql($dql, array($value));
		if ($users->count() == 1) {
			return $users->getFirst();
		}
	}

	/**
	 * If a provider is authoritative for
	 * authentification
	 * @return boolean
	 */
	public function isAuthoritative() {
		return $this->testBoolean(AppKitIAuthProvider::AUTH_AUTHORITATIVE);
	}
	
	/**
	 * If a provider allowed resume other providers to authentificate
	 * @return boolean
	 */
	public function resumeAuthentification() {
		return $this->testBoolean(AppKitIAuthProvider::AUTH_RESUME);
	}
	
	public function canUpdateProfile() {
		return $this->testBoolean(AppKitIAuthProvider::AUTH_UPDATE);
	}
	
	public function canCreateProfile() {
		return $this->testBoolean(AppKitIAuthProvider::AUTH_CREATE);
	}

	public function testBoolean($setting_name) {
		return ($this->getParameter($setting_name, false) !== false) ? true : false;
	}

	public function testBinary($setting_name, $flag) {
		$test = $this->getParameter($setting_name);
		if ($test && ($test & $flag)>0) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the name of a provider
	 * @return boolean
	 */
	public function getProviderName() {
		return $this->getParameter('name');
	}
	
	public function getDefaultGroups() {
		$string = $this->getParameter('auth_groups');
		if ($string) {
			return explode(',', $string);
		}
	}
	
	protected function mapUserdata(array $data) {
		$re = array ();
		foreach ($this->getParameter('auth_map', array()) as $k=>$f) {
			if (array_key_exists($f, $data)) {
				$re[$k] = $data[$f];
			}
		}
		$re['user_authsrc'] = $this->getProviderName();
		return $re;
	}
	
	public function determineUsername() {
		return null;
	}
	
}

class AppKitAuthProviderException extends AppKitException {}

?>