<?php

class LConf_Backend_snippetSearchSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		try {
			$properties = $rd->getParameters();
			$context = $this->getContext();
			
			$connectionId = $properties["connectionId"];
			$from = $properties["search"];
			$isRegExp = $rd->getParameter("regExp",false);
			$to = $rd->getParameter("replace","");
			$unique = $rd->getParameter("uniqueResults",false);
			$filters = explode(",",$rd->getParameter("filters"));
		
			$context->getModel("LDAPClient","LConf");
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
		
			if(!$client) {
				throw new AgaviException("Connection error. Please reconnect.");
				return null;
			}
			
			return json_encode(
				array(
					"success" => true,
					"metaData" => array(
						"idProperty" => 'id',
						"root" => "result",
						"fields" => array("dn","property","value","type","iconCls")
					),
					"result" => $client->searchSnippetOccurences($from,$unique,$isRegExp)
				)
			);
			
			
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.snippetSearch');
	}
}

?>