<?php

class Web_Icinga_ApiSearchSuccessView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setAttribute('_title', 'Icinga.ApiSearch');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) 
	{
		return json_encode($rd->getParameter("searchResult",null));
	}

	public function executeXml(AgaviRequestDataHolder $rd) 
	{
		$results = $rd->getParameter("searchResult",null);
		$DOM = new DOMDocument("1.0","UTF-8");
		$root = $DOM->createElement("results");
		$DOM->appendChild($root);
		foreach($results as $result) {
			$resultNode = $DOM->createElement("result");
			$root->appendChild($resultNode);
			foreach($result as $fieldname=>$field) {
				$node = $DOM->createElement("column");
				$node->nodeValue = $field;
			
				$name = $DOM->createAttribute("name");
				$name->nodeValue = $fieldname;
				$node->appendChild($name);
				$resultNode->appendChild($node);
			}				
		}
		return $DOM->saveXML();
	}

	public function executeSimple(AgaviRequestDataHolder $rd) 
	{
		
	}
}

?>