<?php

class Api_ApiCommandInfoAction extends IcingaApiBaseAction {
	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
	    return $this->executeWrite($rd);
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
	    if (!$this->context->getUser()->isAuthenticated()) {
	        return array('Api', 'GenericError');
	    }
	    return $this->getDefaultViewName();
	}
}