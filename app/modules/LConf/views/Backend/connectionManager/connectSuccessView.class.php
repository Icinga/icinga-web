<?php

class LConf_Backend_connectionManager_connectSuccessView extends IcingaLConfBaseView
{
	public function executeJSON(AgaviRequestDataHolder $rd) {
		try {
			$context = $this->getContext();
			$scope = $rd->getParameter("scope",array("global"));

			$connectionId = $rd->getParameter("connection_id",false);
			$connectionJson = $rd->getParameter("connection",false);
			
			if(!$connectionId && !$connectionJson)
				throw new AgaviException("No connectionId provided!");
			$connection = json_decode($connectionJson,true);
			if(!$connection) {
			
				$connectionMgr = $context->getModel("LDAPConnectionManager","LConf");
				if(!$connectionMgr->userIsGranted($connectionId))
					throw new AgaviException("You are not allowed to access this connection.");
					
				$connectionMgr->getConnectionsForUser();			
				$connection = $connectionMgr->getConnectionById($connectionId);
			} else {
				$connection = $context->getModel("LDAPConnection","LConf",array($connection));
			}

			if(!$connection)
				throw new AgaviException("Connection failed. Please check your credentials ");
			
			$connManager = $context->getModel("LDAPClient","LConf",array($connection));
			$connManager->connect();
			return json_encode(array(
							"ConnectionID"=>$connManager->getId(),
							"RootNode"=>$connManager->getCwd())
							);
			
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.connectionManager.connect');
	}
}

?>