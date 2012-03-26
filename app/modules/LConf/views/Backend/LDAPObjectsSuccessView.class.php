<?php

class LConf_Backend_LDAPObjectsSuccessView extends IcingaLConfBaseView
{

	public function executeJson(AgaviRequestDataHolder $rd) {
		$field = json_decode($rd->getParameter("field"),true);
		$limit = $rd->getParameter("limit",0);
		$offset = $rd->getParameter("start",0);
		$asTree = $rd->getParameter("asTree",false);
		$ctx = $this->getContext();
		$result = array();
		
		if(!is_array($field)) {
			$definitions = null;//$ctx->getStorage()->read("lconf.ldap.entites");
			if(!$definitions)
				$definitions = $this->loadStaticLDAPDefinitions();
			$result = $definitions;			
			if(!$asTree)  {
				$result = $definitions["DEFINITIONS"][$field];
				foreach($result as &$entry)
					$entry = array("entry"=>$entry);
			}
		} else if(isset($field["LDAP"])) {
			$connectionId = $rd->getParameter("connectionId");
			$ctx->getModel("LDAPClient","LConf");
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$ctx->getStorage());
			$filtergr = $ctx->getModel("LDAPFilterGroup","LConf",array("AND"));
			foreach($field["LDAP"] as $filter) {
				$filter = explode("=",$filter);
				$filterModel = $ctx->getModel("LDAPFilter","LConf",array($filter[0],$filter[1]));
				$filtergr->addFilter($filterModel);
			}
			
			foreach($client->searchEntries($filtergr,null,array(isset($field["Attr"]) ? $field["Attr"] : null)) as $entry) {
				if(is_int($entry))
					continue;
				if(isset($field["Attr"]) && $field["Attr"] != "dn" && $field["Attr"] != "*")
					$result[] = array("entry"=>$entry[$field["Attr"]][0]);
				else if(isset($field["Attr"]) && $field["Attr"] == "*")
                    $result[] = array("entry"=>$entry);
                else
					$result[] = array("entry"=>$entry["dn"]);

			}
		}
		$response = array();
		if($asTree) {
			$response = $this->buildTreeResponse($field,$result);
		} else {	
			$response = $this->buildResponse($field,$result,$limit,$offset);
		}
		return json_encode($response);
	}
	
	protected function buildTreeResponse($field, $result) {
		if(!$field)
			$field = "properties";
		$defs = $result["DEFINITIONS"][$field];
		$cats = $result["CATEGORIES"];
		$categoryListing = array();
		foreach($cats as $name=>$currentCategory) {
			$viewAndPattern = explode("|",$currentCategory);
			$objClassDefault = count($viewAndPattern) > 1 ? explode(';',$viewAndPattern[0]) : array();
			$patterns = explode(";",$viewAndPattern[count($viewAndPattern)-1]);
			$matches = array();
			foreach($patterns as $pattern) {
				$matchingSet = preg_grep("/".$pattern."/",$defs);
				$matches = array_merge($matches,$matchingSet);
			}
			$entry = array("text" => $name,"objclasses" => $objClassDefault, "leaf"=>false, "children" => array());
			foreach($matches as $match) {
				$entry["children"][] = array(
					"text" => $match,
					"leaf" => true
				);
			}
			$categoryListing[] = $entry;
		}
		return $categoryListing;
	}
	
	protected function buildResponse($field,$result,$limit=0,$offset=0) {

		$response = array(
			"metaData" => array(
				"idProperty" => "entry",
				"root" => "result",
				"fields" => array(
					"entry"  
				),
				"totalProperty" => "total"
			),
			"result" => array_slice($result,$offset,$limit > 0 ? $limit : null),
			"total" => count($result)
		);
		
		return $response;
	}
	
	public function loadStaticLDAPDefinitions() {
		$ctx = $this->getContext();
		$cfg = AgaviConfig::get("modules.lconf.ldap_definition_ini");
		$data = parse_ini_file($cfg,true);
		//$ctx->getStorage()->write("lconf.ldap.entites",$data);
		return $data;
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.LDAPObjects');
	}
}

?>
