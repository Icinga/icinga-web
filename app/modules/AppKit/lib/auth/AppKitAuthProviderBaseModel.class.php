<?php

class AppKitAuthProviderBaseModel extends IcingaBaseModel {

	public function  initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);

		$this->getContext()->getLoggerManager()->log(
			sprintf('Auth provider %s initialized', $this->getName()),
			AgaviLogger::DEBUG
		);
	}

	public function getName() {
		return $this->getParameter('name');
	}

	/**
	 * If a provider is authoritative for
	 * authentification
	 * @return boolean
	 */
	public function isAuthoritative() {
		return $this->getParameter('auth_authoritative', false);
	}
	
	/**
	 * If a provider allowed resume other providers to authentificate
	 * @return boolean
	 */
	public function resumeAuthentification() {
		return $this->getParameter('auth_resume', false);
	}
	
	public function canUpdateProfile() {
		return $this->getParameter('auth_update', false);
	}
	
	public function canCreateProfile() {
		return $this->getParameter('auth_create', false);
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