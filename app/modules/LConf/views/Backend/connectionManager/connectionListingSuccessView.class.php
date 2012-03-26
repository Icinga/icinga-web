<?php

class LConf_Backend_connectionManager_connectionListingSuccessView extends IcingaLConfBaseView
{
	public function executeJSON(AgaviRequestDataHolder $rd) {
		if($error = $rd->getParameter("_error",false)) {
			$this->getContainer()->getResponse()->setHttpStatusCode(500);
			return json_encode(array("error"=>$error));
		}
		$context = $this->getContext();
		$user = $context->getUser();
		$scope = $rd->getParameter("scope",array("global"));
		$connManager = $context->getModel("LDAPConnectionManager","LConf");
		if(!$this->getContext()->getUser()->hasCredentials("lconf.admin"))
			$connManager->getConnectionsForUser($this->getContext()->getUser()->getNsmUser());
		else
			$connManager->getConnectionsFromDB();

		return $connManager->__toJSON();
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.connectionManager.connectionListing');
	}
}

?>