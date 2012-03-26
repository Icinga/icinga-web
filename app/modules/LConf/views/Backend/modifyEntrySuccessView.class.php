<?php

class LConf_Backend_modifyEntrySuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd)
	{
		try {
			$parameters= $rd->getParameters();
			
			$node = $parameters["node"];
			$connectionId = $parameters["connectionId"];
			$properties = json_decode($parameters["properties"],true);
			$context = $this->getContext();
			$context->getModel("LDAPClient","LConf");
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
			if(!$client) {
				throw new AgaviException("Connetion error. Please reconnect.");
				return null;
			}
			$client->setCwd($node);
			
			switch($parameters["xaction"]) {
				case 'create':
					$client->addNodeProperty($node, $properties);
					return "{success :true}";
					break;
				case 'update':
					$client->modifyNode($node, $properties);
					return "{success :true}";
					break;
				case 'destroy':
					$client->removeNodeProperty($node, $properties);
					return "{success :true}";
					break;
				default:
					throw new AgaviException("Unknown action: ".$parameters["action"]);
			}

		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.ModifyEntry');
	}
}

?>