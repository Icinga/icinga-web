<?php

class LConf_Backend_modifyNodeErrorView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		try {
			$action = $rd->getParameter("xaction");
			$properties = json_decode($rd->getParameter("properties"),true);
			$parentDN = $rd->getParameter("parentNode");
			$connectionId = $rd->getParameter("connectionId");
			$context = $this->getContext();
			$context->getModel("LDAPClient","LConf");
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
			
			if(!$client) {
				throw new AgaviException("Connection error. Please reconnect.");
				return null;
			}
			$client->setCwd($parentDN);
			$sourceConn = null;
			
			if(isset($properties["targetConnId"]))
				$sourceConn = $properties["targetConnId"];
			switch($rd->getParameter("xaction")) {
				case 'update':
				case 'create':
					$client->addNode($parentDN, $properties);
					break;
				case 'destroy':
					$client->removeNodes($properties);
					break;
				case 'move':
					$client->moveNode($properties["sourceDN"],$properties["targetDN"],$sourceConn);
					break;
				case 'clone':
					$client->cloneNode($properties["sourceDN"],$properties["targetDN"],$sourceConn);
					break;

			}
			return "{success:true}";
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.modifyNode');
	}
}

?>