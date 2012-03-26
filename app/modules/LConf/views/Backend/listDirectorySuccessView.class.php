<?php

class LConf_Backend_listDirectorySuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		$filters = $rd->getParameter("filters","[]");
		$filters = json_decode($filters,true);
		$connectionId = $rd->getParameter("connectionId");
		$context = $this->getContext();
		// Register Class
		$context->getModel("LDAPClient","LConf");
		$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
		if(!is_array($filters))
			$filters = array($filters);
		$nodeList = array();
		if(isset($filters["MAGIC_WORD"])) {
			$nodeList = $client->searchSnippetOccurences($filters["MAGIC_WORD"]);			
		} else {
			$this->applyFilters($filters,$client);
			$client->setCwd($rd->getParameter("node"));
			$list = $client->listCurrentDir();
			// filter out base node information
			if(!is_array($list))
				return null;
			$nodeList = $this->reformatList($list,$client);	
		}
		return json_encode($nodeList);
	}
	
	protected function aliasFilter($dn) {
		$or_group = $this->getContext()->getModel("LDAPFilterGroup","LConf",array("OR"));
		$group = $this->getContext()->getModel("LDAPFilterGroup","LConf");
		$group->addFilter($this->getContext()->getModel("LDAPFilter","LConf",array("objectclass","alias")));
		$group->addFilter($this->getContext()->getModel("LDAPFilter","LConf",array("aliasedobjectname",$dn)));		
		$or_group->addFilter($group);
		$or_group->addFilter($this->getContext()->getModel("LDAPFilter","LConf",array("dn",$dn)));
		return $or_group;
	}
	
	protected function applyFilters(array $filters,LConf_LDAPClientModel $client) {
		if(empty($filters))
			return true;
		$filterMgr = $this->getContext()->getModel("LDAPFilterManager","LConf");
		$allFilters = $this->getContext()->getModel("LDAPFilterGroup","LConf");
		
		foreach($filters as $type=>$filter) {
			if($type === "ALIAS") {
				$allFilters->addFilter($this->aliasFilter($filter));
			} else
				$allFilters->addFilter($filterMgr->getFilterAsLDAPModel($filter));
		}		

		$client->setFilter($allFilters);
	}
	
	protected function reformatList($list,LConf_LDAPClientModel $client) {
		$startCWD = $client->getCwd();
		$nodeList = array();
		foreach($list as $key=>$node) {
			// we already have the information about the base node, so skip these
			if(!is_int($key)) 
				continue;
			// check for leafs
			$dn = preg_replace("/^\*\d{4}\*/","",$node["dn"]);
			$client->setCwd($dn);
			if(isset($node["aliasdn"]))
				$client->setCwd($node["aliasedobjectname"][0]);
			
			$subs = $client->listCurrentDir();

			if(!is_array($subs) || !@$subs["count"])
				$node["isLeaf"] = true;
			else 
				$node["count"] = count($subs)-1;
			$node["parent"] = $startCWD;	
			$nodeList[] = $node;	
		}
		$client->setCwd($startCWD);
		return $nodeList;
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.listDirectory');
	}
}

?>