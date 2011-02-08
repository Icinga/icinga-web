<?php

class Api_IcingaInstanceControllerAction extends IcingaApiBaseAction
{
	public function isSecure() {
		return true;
	}

	public function getCredentials() {
		return array("icinga.control.admin");
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
		
	}
	
	public function getDefaultViewName() {
		return "Success";
	}	
}
?>
