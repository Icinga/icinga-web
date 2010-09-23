<?php

class Web_Icinga_ApiSearchSuccessView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{

		$this->setAttribute('_title', 'Icinga.ApiSearch');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) 
	{
		$count = $rd->getParameter("searchCount");
		// just return the entities
		if(!$rd->getParameter("withMeta",false) && !$count)
			return json_encode($rd->getParameter("searchResult",null));
		
		// provide meta data for ExtJs stores
		$searchResult = $rd->getParameter("searchResult");
		$meta = $this->getMetaDataArray($rd); 
		$count = $rd->getParameter("searchCount");
		$result = array("metaData" => $meta,"result"=>$searchResult,"success"=>"true");
		if($count) {
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
	
	public function executeXml(AgaviRequestDataHolder $rd) 
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
		return $DOM->saveXML();
	}

	public function executeSimple(AgaviRequestDataHolder $rd) 
	{
		
	}
}

?>