<?php

class AppKit_Auth_Provider_LDAPModel extends AppKitAuthProviderBaseModel implements AppKitIAuthProvider {
	
	private $ldap_links = array();
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#doAuthenticate()
	 */
	public function doAuthenticate(NsmUser $user, $password, $username=null, $authid=null) {
		$authid = $user->getAuthId();
		$username = $user->user_name;
		
		$this->log('Auth.Provider.LDAP Trying authenticate (authkey=%s,user=%s)', $authid, $username, AgaviLogger::DEBUG);
		
		try {
			$conn = $this->getLdapConnection(false);
			$re = @ldap_bind($conn, $authid, $password);
			
			if ($this->isLdapError($conn)==false && $re === true && ldap_errno($conn) === 0) {
				$this->log('Auth.Provider.LDAP Successfull bind (authkey=%s,user=%s)', $authid, $username, AgaviLogger::DEBUG);
				return true;
			}
		}
		catch (AgaviSecurityException $e) {
			// PASS
		}
		
		$this->log('Auth.Provider.LDAP Bind failed (authkey=%s,user=%s)', $authid, $username, AgaviLogger::WARN);
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see app/modules/AppKit/lib/auth/AppKitIAuthProvider#isAvailable()
	 */
	public function isAvailable($uid, $authid=null) {
		$record = $this->getLdaprecord('(objectClass=*)', $authid);
		if (is_array($record)) {
			if ($record['dn'] === $authid) {
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
		$data = null;
		
		$this->log('Auth.Provider.LDAP Try import (user=%s, authid=%s)', $uid, $authid, AgaviLogger::DEBUG);
		
		if ($authid == $uid || $authid==false) {
			$data = $this->getLdaprecord($this->getSearchFilter($uid));				
		}
		elseif (strlen($authid) > strlen($uid)) {
			$data = $this->getLdaprecord('(objectClass=*)', $authid);
		}
		
		if (is_array($data)) {
			$re = (array)$this->mapUserdata($data);
			$re['user_authid'] = $data['dn'];
			$re['user_name'] = $data[$this->getParameter('ldap_userattr', 'uid')];
			$re['user_disabled'] = 0;
		}
		
		return $re;
	}
	
	private function getLdaprecord($filter, $dn=false) {
		$items = array ();
		try {
			$ldap = $this->getLdapConnection(true);
			
			$basedn = ($dn ? $dn : $this->getParameter('ldap_basedn'));
			
			$this->log('Auth.Provider.LDAP Prepare LDAPsearch (base=%s, filter=%s)', $basedn, $filter, AgaviLogger::DEBUG);
			
			$res = @ldap_search($ldap, $basedn, $filter);
			
			if ($this->isLdapError($ldap)) {
				return null;
			}
			
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
			else {
				$this->log('Auth.Provider.LDAP/getLdaprecord Filter returns no result (base=%s, filter=%s)', $basedn, $filter, AgaviLogger::DEBUG);
			}
			
			ldap_unbind($ldap);
			
			if (count($items)) {
				return $items;
			}
		}
		catch (AgaviSecurityException $e) {
			// PASS
		}
		
		return null;
	}
	
	private function getSearchFilter($uid) {
		$filter = $this->getParameter('ldap_filter_user');
		if ($filter) {
			return str_replace('__USERNAME__', $uid, $filter);
		}
	}
	
	private function ldapLink($uid) {
		if (isset($this->ldap_links[$uid]) && is_resource($this->ldap_links[$uid])) {
			return $this->ldap_links[$uid];
		}
	}
	
	private function getLdapConnection($bind=false) {
		
		$linkid = (int)$bind;
		
		if (($res = $this->ldapLink($linkid)) !== null) {
			$this->log('Auth.Provider.LDAP Using existing link (linkid=%d,res=%s)', $linkid, $res, AgaviLogger::ERROR);
			return $res;
		}
		
		$this->log('Auth.Provider.LDAP Try LDAP connect (dsn=%s,bind=%s)', $this->getParameter('ldap_dsn'), ($bind==true) ? 'true' : 'false', AgaviLogger::DEBUG);
		
		$res = ldap_connect($this->getParameter('ldap_dsn'));
		
		$this->log('Auth.Provider.LDAP got resource %s', $res, AgaviLogger::DEBUG);
		
		ldap_set_option($res, LDAP_OPT_DEREF, LDAP_DEREF_NEVER);
		ldap_set_option($res, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);
		
		if ($bind === true) {
			
			$binddn = $this->getParameter('ldap_binddn');
			$bindpw = $this->getParameter('ldap_bindpw');
			
			$re = @ldap_bind($res, $binddn, $bindpw);
			
			if ($re !== true) {
				$this->log('Auth.Provider.LDAP Bind failed: (dn=%s)', $binddn, AgaviLogger::ERROR);
				throw new AgaviSecurityException('Auth.Provider.LDAP: Bind failed');
			}
			
			$this->log('Auth.Provider.LDAP Successfully bind (dn=%s)', $binddn, AgaviLogger::DEBUG);
		}
		
		if (ldap_errno($res)) {
			$this->log('Auth.Provider.LDAP connection error: %s (%d)', ldap_error($res), ldap_errno($res), AgaviLogger::ERROR);
			throw new AgaviSecurityException('Auth.Provider.LDAP: '. ldap_error($res). ' ('. ldap_errno($res). ')');
		}
		
		$this->log('Auth.Provider.LDAP connection successfully (%s)', $this->getParameter('ldap_dsn'), AgaviLogger::INFO);
		
		$this->ldap_links[$linkid] =& $res;
		
		return $res;
	}
	
	private function isLdapError(&$ldap, $log=true) {
		if (is_resource($ldap)) {
			if (ldap_errno($ldap)) {
				if ($log==true) {
					$this->log('Auth.Provider.LDAP Error: %s (errno=%d,resource=%d)', ldap_error($ldap), ldap_errno($ldap), $ldap, AgaviLogger::DEBUG);
				}
				
				return true;
			}
		}
		
		return false;
	}
	
}

?>
