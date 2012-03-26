<?php

/**
 * The base action from which all LDAP module actions inherit.
 */
class IcingaLConfBaseAction extends IcingaBaseAction
{
	public function isSecure() {
		return true;
	}
	
	public function getCredentials() {
		return 'lconf.user';
	}
}

?>