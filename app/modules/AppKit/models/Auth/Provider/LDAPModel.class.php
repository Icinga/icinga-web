<?php

class AppKit_Auth_Provider_LDAPModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser &$user, $password) {
		$authid = $user->getAuthId();
		$conn = $this->getLdapConnection(false);
		$re = @ldap_bind($conn, $authid, $password);
		
		if ($re === true && ldap_errno($conn) === 0) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#isAvailable()
	 */
	public function isAvailable($uid) {
		$record = $this->getLdaprecord('(objectClass=*)', $uid);
		if (is_array($record)) {
			if ($record['distinguishedName'] === $uid) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#getUserdata()
	 */
	public function getUserdata($uid, $authid=false) {
		$ldap = $this->getLdapConnection(true);
		$re = null;
		if ($authid == false) {
			$data = $this->getLdaprecord($this->getSearchFilter($uid));
			if (is_array($data)) {
				$re = (array)$this->mapUserdata($data);
				$re['user_authid'] = $data['dn'];
				$re['user_name'] = $data['sAMAccountName'];
				$re['user_disabled'] = 0;
			}
		}
		
		return $re;
	}
	
	private function getLdaprecord($filter, $dn=false) {
		$items = array ();
		$ldap = $this->getLdapConnection(true);
		$res = ldap_search($ldap, ($dn ? $dn : $this->getParameter('ldap_basedn')), $filter);
		
		if ($res !== false && ($eid = ldap_first_entry($ldap, $res))) {
			$attrs = ldap_get_attributes($ldap, $eid);
			if (is_array($attrs)) {
				foreach ($attrs as $k=>$attr) {
					if (is_numeric($k)) {
						if ($attrs[$attr]['count'] == 1) {
							$items[$attr] = $attrs[$attr][0];
						}
					}
				}
				$items['dn'] = ldap_get_dn($ldap, $eid);
			}
			
			ldap_free_result($res);
			
		}
		
		ldap_unbind($ldap);
		
		if (count($items)) {
			return $items;
		}
	}
	
	private function getSearchFilter($uid) {
		$filter = $this->getParameter('ldap_filter_user');
		if ($filter) {
			return str_replace('__USERNAME__', quotemeta($uid), $filter);
		}
	}
	
	private function getLdapConnection($bind=false) {
		$res = ldap_connect($this->getParameter('ldap_dsn'));
		
		ldap_set_option($res, LDAP_OPT_DEREF, LDAP_DEREF_NEVER);
		ldap_set_option($res, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);
		
		if ($bind === true) {
			ldap_bind($res, $this->getParameter('ldap_binddn'), $this->getParameter('ldap_bindpw'));
		}
		
		if (ldap_errno($res)) {
			throw new AgaviSecurityException('AuthLDAP: '. ldap_error($res). ' ('. ldap_errno($res). ')');
		}
		
		return $res;
	}
	
}

?>