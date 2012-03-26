<?php

class LConf_Backend_getPropertiesSuccessView extends IcingaLConfBaseView
{	
	/**
	 * Rebuilds a stored connection and returns a json-formatted view of the
	 * properties. Returns a string on success or a Status-500 Response containing
	 * the error message if an exception is thrown. 
	 * 
	 * required POST-Parameter: 
	 * node: The LDAP Node whose parameters should be shown
	 * connectionId: The id of the stored connection
	 * 
	 * @param AgaviRequestDataHolder $rd
	 * @return String 
	 */
	public function executeJson(AgaviRequestDataHolder $rd)
	{
		try {	
			// POST Parameter retrieval
			$node = $rd->getParameter("node");
			$connectionId = $rd->getParameter("connectionId");
			
			$context = $this->getContext();
			
			// restore connection from store
			$client = LConf_LDAPClientModel::__fromStore($connectionId,$context->getStorage());
			//$client->setCwd();
			// Get the raw property list
			$list = $client->getNodeProperties($node,array(),true);
			if(!is_array($list)) // no properties fund, return null
				return null;
			// Format the list for json_encoding
			$nodeList = $this->reformatList($list);
			return json_encode($nodeList);
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	
	/**
	 * Formats a raw LConf_read response to a suitable response for 
	 * the client application
	 * 
	 * @param array $list
	 * @return array $reformatted
	 */
	protected function reformatList($list) {
		$nodeList = array("properties" => array());
		foreach($list as $key=>$node) {
			// we don't care about the child nodes, so skip these
			if($key  == "count" || is_int($key)) 
				continue;
			// check if there are multiple properties (like 2 objectclasses)
			if(is_array($node)) { // multiple 
				foreach($node as $nodeKey=>$value) {
					// numeric indexes store the property name 
					// which is already defined in $key
					if(!is_int($nodeKey)) 
						continue;
					// create a property record for the response	
					
					$nodeList["properties"][] = $this->getResult($key,$nodeKey,$value);	
				}
			} else { // only one property
				// create a property record for the response
				
				$nodeList["properties"][] = $this->getResult($key,$key,$node);
			}
		}
		return $nodeList;
	}
	
	public function getResult($key,$nodeKey,$value) {
		
		$baseParams = array("property" => $key,
					  "id" => $key."_".$nodeKey);
		if(!is_array($value)) {
			$baseParams["value"] = $value;	
			return $baseParams;
		}
		if(!isset($value[0]))
			$value = array($value);
		
		if(isset($value[0])) {
			$baseParams["value"] = $value[0]["value"];
			$baseParams["parent"] = $value[0]["dn"];
		}
		return $baseParams;
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.getProperties');
	}
}

?>