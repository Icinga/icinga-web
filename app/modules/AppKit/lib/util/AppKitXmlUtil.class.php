<?php

class AppKitXmlUtil {
	
	
	/**
	 * @param AgaviXmlConfigDomDocument $document
	 * @param string $query
	 * @return DOMNodeList
	 */
	public static function extractEntryNode(AgaviXmlConfigDomDocument $document, $query) {
		$list = $document->getXPath()->query($query);
		
		if ($list instanceof DOMNodeList && $list->length==1) {
			return $list->item(0);
		}
	}
	
	public static function createXIncludeNode(AgaviXmlConfigDomDocument $document, $file, $pointer) {
		$element = $document->createElementNS('http://www.w3.org/2001/XInclude', 'xi:include');
		
		$element->setAttribute('href', $file);
		$element->setAttribute('xpointer', $pointer);
		
		return $element;
	}
	
	public static function includeXmlFilesToTarget(AgaviXmlConfigDomDocument $document, $query, $pointer, array $files) {
		$targetNode = self::extractEntryNode($document, $query);
	
		if (!$targetNode) {
			return;
		}	
		
		foreach ($files as $file) {
			$node = self::createXIncludeNode($document, $file, $pointer);
			$targetNode->appendChild($node);
		}
	}
	
}

?>
