<?php

class Api_ApiCommandInfoAction extends IcingaApiBaseAction implements IAppKitDispatchableAction {
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
	    
	    $model = $this->getContext()->getModel('Commands.CommandInfo', 'Api');
	    
	    $commands = array ();
	    
	    if ($rd->getParameter('command')) {
	        $commands = $model->getInfo($rd->getParameter('command'));
	    } elseif ($rd->getParameter('type')) {
	        $commands = $model->filterCommandByType($rd->getParameter('type'));
	    } else {
	        $commands = $model->getAll();
	    }
	    
	    $this->setAttributeByRef('commands', $commands);
	    
	    return $this->getDefaultViewName();
	}
	
	public function isSecure() {
	    return true;
	}
	public function getCredentials() {
	    return "icinga.user";
	}
}