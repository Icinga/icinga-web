<?php

class Api_RelationProviderAction extends IcingaApiBaseAction {
	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
	    return $this->executeWrite($rd);
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
	    
	    $model = $this->context->getModel('Relation.DataModel', 'Api');
	    
	    try {
	        $this->setAttribute('data', $model->getRelationDataForObjectId($rd->getParameter('objectId')));
	    } catch (AppKitModelException $e) {
	        return "Error";
	    }
	    
	    return $this->getDefaultViewName();
	}
	
	public function isSecure() {
	    return true;
	}
	public function getCredentials() {
	    return "icinga.user";
	}
}