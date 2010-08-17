<?php

class AppKit_Auth_Provider_HTTPBasicAuthenticationModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {

	const DATASOURCE_NAME	= '_SERVER';

	private static $sources_list = array (
		'_SERVER'
	);

	private static $source_map = array (
		'auth_name'	=> 'http_uservar',
		'auth_type'	=> 'http_typevar'
	);

	private static $source_map_defaults = array (
		'auth_name'	=> 'REMOTE_USER,PHP_AUTH_USER',
		'auth_type'	=> 'AUTH_TYPE'
	);

	private $auth_name = null;
	private $auth_type = null;

	public function doAuthenticate(NsmUser &$user, $password) {

	}

	public function isAvailable($uid) {

	}

	public function getUserdata($uid, $authid=false) {

	}

	public function getUsername() {
		return $this->getContext()->getRequest()->getRequestData();
	}

	/**
	 * @return AgaviParameterHolder
	 */
	private function getVarSource() {
		$source_name = $this->getParameter('http_source', self::DATASOURCE_NAME);

		if (array_search($source_name, self::$sources_list) === false) {
			throw new AppKitAuthProviderException('http_source (%s) is unknown', $source_name);
		}

		switch ($source_name) {
			case '_SERVER':
			default:
				return new AgaviParameterHolder($_SERVER);
			break;
		}
	}

	public function  determineUsername() {
		$source = $this->getVarSource();

		foreach (self::$source_map as $class_target => $config_target) {
			$search_keys = AppKitArrayUtil::trimSplit($this->getParameter($config_target, self::$source_map_defaults[$class_target]));
			if (isset($search_keys[0]) && ($search_value = $source->getParameter($search_keys[0])) ) {
				$this->{ $class_target } = $search_value;
			}
		}

		if ($this->auth_type) {
			$this->auth_type = strtolower($this->auth_type);
		}

		$this->getContext()->getLoggerManager()->log(
			sprintf('%s: auth_name=%s, auth_type=%s', $this->getName(), $this->getAuthName(), $this->getAuthType()),
			AgaviLogger::INFO
		);

		return $this->auth_name;
	}

	public function getAuthName() {
		return $this->auth_name;
	}

	public function getAuthType() {
		return $this->auth_type;
	}

}

?>