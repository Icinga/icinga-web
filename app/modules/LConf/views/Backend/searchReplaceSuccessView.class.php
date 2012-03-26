<?php

class LConf_Backend_searchReplaceSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		try {
			$properties = $rd->getParameters();
			$context = $this->getContext();
			
			$connectionId = $properties["connectionId"];
			$from = $properties["search"];
			$fields = explode(",",$properties["fields"]);
			$to = $rd->getParameter("replace","");
			$filters = explode(",",$rd->getParameter("filters"));
		
			$isSissyMode = $rd->getParameter("sissyMode",false);
	
			$context->getModel("LDAPClient","LConf");
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
			$this->applyFilters($filters,$client);
			
			return $client->searchReplace($from,$to,$fields,$isSissyMode);
			
			if(!$client) {
				throw new AgaviException("Connection error. Please reconnect.");
				return null;
			}
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	
	protected function applyFilters(array $filters,LConf_LDAPClientModel $client) {
		if(empty($filters))
			return true;
		$filterMgr = $this->getContext()->getModel("LDAPFilterManager","LConf");
		$allFilters = $this->getContext()->getModel("LDAPFilterGroup","LConf");

		foreach($filters as $filter) {
			if(!$filter)
				continue;
			$allFilters->addFilter($filterMgr->getFilterAsLDAPModel($filter));
		}		

		$client->setFilter($allFilters);
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.searchReplace');
	}
}

?>