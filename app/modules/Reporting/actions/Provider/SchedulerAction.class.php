<?php

class Reporting_Provider_SchedulerAction extends ReportingBaseAction {
	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function execute(AgaviParameterHolder $rd) {
	    
	    switch ($rd->getParameter('action')) {
	        case 'list':
	            return 'List';
	        break;
	        default:
	            return $this->getDefaultViewName();
	        break;
	    }
	    
	}
	
	public function isSecure() {
	    return true;
	}
	
	public function getCredentials() {
	    return array ('icinga.user');
	}
	
	public function handleError(AgaviRequestDataHolder $rd) {
	    return $this->getDefaultViewName();
	}
}

?>