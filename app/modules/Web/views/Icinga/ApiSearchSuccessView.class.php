<?php

class Web_Icinga_ApiSearchSuccessView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{

		$this->setAttribute('_title', 'Icinga.ApiSearch');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) 
	{
		$searchResult = $rd->getParameter("searchResult");
		
		$result = array(
			"result" => $searchResult,
			"success" => "true"
		);
			
		if (false !== $rd->getParameter("withMeta", false)) {
			// Configure ExtJS' JsonReader
			$result["metaData"] = $this->getMetaDataArray($rd);
		}
		
		$count = $rd->getParameter("searchCount");
		if ($count) {
			$count = array_values($count[0]);
			$result["total"] = $count[0];
		}

		return json_encode($result);
	}

	protected function getMetaDataArray(AgaviRequestDataHolder $rd) {
		$idField = $rd->getParameter("idField",false);
		$columns = $rd->getParameter("columns");
		if($idField)
			$metaData["idProperty"] = $idField;
		else if (count($columns) == 1)
			$metaData["idProperty"] = $idField = $columns[0];
		
		if($idField) {
			foreach($columns as &$column) {
				if($column = $idField)
					$columns[] = array("name"=>"idField","mapping"=>$column);
			}
		}
		$metaData["paramNames"] = array(
			'start' => 'limit_start',
			'limit' => 'limit'
		);
		$metaData["totalProperty"] = "total";
		$metaData["root"] = "result";
		$metaData["fields"] =$columns;
		return $metaData;
	}
	
	protected function createDOM(AgaviRequestDataHolder $rd) 
	{
		$results = $rd->getParameter("searchResult",null);
		$count = $rd->getParameter("searchCount");
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
		if($count) {
			$count = array_values($count[0]);
			$node = $DOM->createElement("total");
			$node->nodeValue = $count[0];
			$root->appendChild($node);
		}
		return $DOM;
	}

	public function executeXml(AgaviRequestDataHolder $rd) 
	{
		$DOM = $this->createDOM($rd);
		return $DOM->saveXML();
	}

	public function executeRest(AgaviRequestDataHolder $rd) 
	{
		$xml = $this->createDOM($rd);
		$xsltproc = new XSLTProcessor();
		$xsl = new DOMDocument();
		$xsl->load(AgaviConfig::get('core.module_dir').'/Web/data/results.xslt');
		$xsltproc->importStylesheet($xsl);
		$result = $xsltproc->transformToXML($xml);
		return $result;
	}

	public function executeSimple(AgaviRequestDataHolder $rd) 
	{
		
	}
}

?>