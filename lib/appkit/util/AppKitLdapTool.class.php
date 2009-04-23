<?php

class AppKitLdapTool {
	
	private $ldap_dsn		= null;
	private $ldap_binddn	= null;
	private $ldap_bindpw	= null;
	private $ldap_basedn	= null;
	
	private $ldap_resource	= null;
	private $ldap_valid		= false;
	
	private $ldap_options	= array (
		LDAP_OPT_REFERRALS			=> 0,
		LDAP_OPT_DEREF				=> LDAP_DEREF_ALWAYS,
		LDAP_OPT_PROTOCOL_VERSION	=> 3,
	);
	
	/**
	 * Just an empty constructor
	 */
	public function __construct() {
		
	}
	
	/**
	 * Sets the ldap dsn
	 * @param string $dsn
	 * @return boolean
	 */
	public function setLdapDsn($dsn) {
		$this->ldap_dsn = $dsn;
		return true;
	}
	
	/**
	 * Sets bind credentials
	 * @param string $binddn
	 * @param string $bindpw
	 * @return boolean
	 */
	public function setCredentials($binddn, $bindpw) {
		$this->ldap_binddn = $binddn;
		$this->ldap_bindpw = $bindpw;
		return true;
	}
	
	/**
	 * Sets the basedn
	 * @param string $basedn
	 * @return boolean
	 */
	public function setBaseDn($basedn) {
		$this->ldap_basedn = $basedn;
		return true;
	}
	
	/**
	 * Connect to the ldap server and try a bind
	 * @throws AppKitLdapToolException
	 * @return boolean
	 */
	public function doConnect() {
		
		// Try to connect ...
		$resource = ldap_connect($this->ldap_dsn);
		if (!is_resource($resource)) {
			throw new AppKitLdapToolException("Could not connect to ldap_dsn: ". $this->ldap_dsn);
		}
		else {
			$this->ldap_resource =& $resource;
		}
		
		// Setting some options
		$this->applyLdapOptions();
		
		// Binds to a user
		$this->doDefaultBind();

		return true;
	}
	
	public function doDefaultBind() {
		return $this->doBind($this->ldap_binddn, $this->ldap_bindpw);
	}
	
	public function doBind($binddn, $bindpw) {
		if ( @ldap_bind($this->ldap_resource, $binddn, $bindpw) === false) {
			$this->throwLdapException();
		}
		
		return true;
	}
	
	private function applyLdapOptions() {
		foreach ($this->ldap_options as $opt_id=>$opt_val) {
			ldap_set_option($this->ldap_resource, $opt_id, $opt_val);
		}
	}
	
	/**
	 * Return true if we've made a successful connect and bind
	 * @return boolean
	 */
	public function isValid() {
		return $this->ldap_valid;
	}
	
	/**
	 * Unbind and disconnects from server
	 * @return unknown_type
	 */
	public function doShutdown() {
		if (is_resource($this->ldap_resource)) {
			@ldap_unbind($this->ldap_resource);
			@ldap_close($this->ldap_resource);
			$this->ldap_valid = false;
		}
	}
	
	/**
	 * Throws a ldap exception with ldap_error message
	 * @return unknown_type
	 */
	private function throwLdapException() {
		throw new AppKitLdapToolException($this->getLastError());
	}
	
	/**
	 * Returns the last error number
	 * @return integer
	 */
	public function getLastErrorno() {
		if (is_resource($this->ldap_resource)) {
			return (int)ldap_errno($this->ldap_resource);
		}
		$this->throwLdapException();
	}
	
	/**
	 * Returns the last error message
	 * @return string
	 */
	public function getLastError() {
		if (is_resource($this->ldap_resource)) {
			return (string)sprintf('%s (%d)', ldap_error($this->ldap_resource), ldap_errno($this->ldap_resource));
		}
		
		throw new AppKitLdapToolException('ldap is not a resource yet!');
	}
	
	/**
	 * Sends ldapqueries to the server and returns an array as the result
	 * @param string $query
	 * @param array $attributes
	 * @return array
	 */
	public function doQuery($query, array $attributes = array ()) {
		$res = null;
		if (count($attributes)) {
			$res = ldap_search($this->ldap_resource, $this->ldap_basedn, $query, $attributes);
		}
		else {
			$res = ldap_search($this->ldap_resource, $this->ldap_basedn, $query);
		}
		
		if ($res !== false ) {
			return $this->getEntriesAsArray($res);
		}
		
		return false;
	}
	
	/**
	 * Returns the count of a search result
	 * @param $item
	 * @return unknown_type
	 */
	public function resultCount(array $item) {
		if (array_key_exists('objectclass', $item)) {
			return 1;
		}
		elseif (array_key_exists('count', $item)) {
			return $item['count'];
		}
		
		return false;
	}
	
	/**
	 * Well, this method parses the search result for specific attributes.
	 * It also parses multi result array so you can use this in a for-each loop
	 * @param array $item
	 * @param array $attributes
	 * @param array $return
	 * @return array
	 */
	public function resultParse(array $item, array $attributes, array &$return = array ()) {
		
		if ($item['objectclass']) {
			foreach ($attributes as $attribute) {
				if (array_key_exists($attribute, $item)) {
					if ($item[$attribute]['count'] == 1) {
						$return[$attribute] = $item[$attribute][0];
					}
					else {
						$return[$attribute] = $item[$attribute];
					}
				}
			}
		}
		else {
			foreach ($item as $check => $subitem) {
				if (is_array($subitem)) {
					$return[$check] = array ();
					$this->resultParse($subitem, $attributes, $return[$check]);
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Converts the ldap result pointer in an array
	 * @param resource $search_res
	 * @return array
	 */
	private function getEntriesAsArray($search_res) {
		$result = ldap_get_entries($this->ldap_resource, $search_res);
		if ($this->resultCount($result) == 1) {
			return $result[0];
		}
		
		return $result;
	}
}

class AppKitLdapToolException extends AppKitException {}

?>