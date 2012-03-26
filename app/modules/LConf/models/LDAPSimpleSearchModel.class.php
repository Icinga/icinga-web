<?php 
class LConf_LDAPSimpleSearchModel {
	protected $client = null;
	protected $isUnique = false;
	
	public function setClient(LConf_LDAPClientModel $client) {
		$this->client = $client;
	}
	/**
	 * @return LConf_LDAPClientModel
	 */
	public function getClient() {
		return $this->client;
	}
	
	public function initialize($context,array $parameters) {
		$this->setClient($parameters["client"]);	
		$this->isUnique = $parameters["unique"] != "false";
	}
	
	public function search($snippet) {

		$client = $this->getClient();
		$connection = $client->getConnection();
		$entries = @ldap_search($connection,$client->getBaseDN(),"objectclass=*");
		$entries = ldap_get_entries($connection,$entries);
		$found = array();
		foreach($entries as $e_key=>$entry) {
			if(!is_int($e_key) || !is_array($entry))
				continue;
			if($this->isUnique && isset($found[$entry["dn"]]))
				continue;
				
			foreach($entry as $key=>$elem) {
				if(is_int($key) || $key == "count")
					continue;
				if($key == "dn")
					continue;
				
				$this->checkEntries($elem,$key,$snippet,$found,$entry);
				
			}
		}
		return array_values($found);
	}
	
	protected function getObjectType($entry) {
		$cfg = AgaviConfig::get("modules.lconf.searchCategories");
		foreach($entry["objectclass"] as $key=>$cls) {
			foreach($cfg as $cat_entry) {
				if($cls == $cat_entry["objectclass"])
					return $cat_entry;
			}
		}
		return "Uncategorized";
		
	}
	
	protected function checkEntries($elem,$key,$snippet,&$found,$entry) {
		$dn = $entry["dn"];
		foreach($elem as $subKey=>$value) {
			if(!is_int($subKey))
				continue;
			if($this->checkIfSnippetOccures($value,$snippet)) {
				$type = $this->getObjectType($entry);
				if(!is_array($type))
					$value = array("id"=>$dn.$key.$value,"dn"=>$dn,"property"=>$key,"value"=>$value,"type"=>$type);
				else 
					$value = array("id"=>$dn.$key.$value,"dn"=>$dn,"property"=>$key,"value"=>$value,"type"=>$type["category"],"iconCls"=>$type["iconCls"]);
				if($this->isUnique)
					$found[$dn] = $value;
				else 
					$found[] = $value;
			}
		}
	}
	
	public function checkIfSnippetOccures($source,$snippet) {
		if(preg_match("/".preg_quote($snippet,"/")."/",$source))
			return true;
	}
	
}

?>
