<?php



abstract class xmlHelperTask extends Task {

	
	protected $registeredNS = array();
	protected function buildXPathIndex(DOMXPath $dom,$wValue = false) {
		$tree = array();
		$root = $dom->query(".")->item(0);
		if(!$root)
			return $tree;
		$curXPath = "/".($root->prefix ? "" : "default:").$root->nodeName;
		$curXPath .= $this->getAttributesPath($root);
		$tree[$curXPath] = array(array("elem" => $root, "real" => false));
		$children = $root->childNodes;
		$this->expandIndexTree($children,$curXPath,$tree,$wValue);
		return $tree;
	}
	
	/**
	 * Function that adds the childnodes of a DOMNodeList $nodes to an index tree $tree
	 * @param DOMNodeList $nodes
	 * @param DOMXPath $path
	 * @param array $tree
	 */
	protected function expandIndexTree(DOMNodeList $nodes,$path,array &$tree,$wValue = false) {
		$curPath = $path;
		foreach($nodes as $node) {
			if($node->nodeType == XML_COMMENT_NODE || $node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_DOCUMENT_NODE)
				continue;
			$curPath = $path;
			$curPath .= "/".$this->addNamespace($node->nodeName);
			$curPath .= $this->getAttributesPath($node);
			if(!isset($tree[$curPath])) 
				$tree[$curPath] = array();
			if($this->hasRealChildren($node)) {
				$tree[$curPath][] = array("value" => '',"elem" => $node, "real" => false);
				$this->expandIndexTree($node->childNodes,$curPath,$tree);
			} else {
	
				$tree[$curPath][] = array("value" => $node->nodeValue, "elem" => $node, "real" => true);
					
			}
		}
	}
	
	protected function getAttributesPath(DOMNode $node) {
		if(!$node->hasAttributes())
			return "";
		$attr = "[";	
		$and = false;
		foreach($node->attributes as $attribute) {
			if($and)
				$attr .=" and ";
			$attr .= "@".$attribute->name."='".$attribute->value."'";
			$and = true;
		}
		$attr = str_replace("/","%§%",$attr);
		$attr .="]";
		return $attr;
	}
	
	/**
	 * Checks whether a DOMNode $node has childnodes that aren't value nodes like comments or text nodes
	 * @param DOMNode $node
	 * @return Boolean 
	 */
	public function hasRealChildren(DOMNode $node) {
		if(!$node->hasChildNodes())
			return false;
			
		foreach($node->childNodes as $child) {
			if($node->nodeType != XML_COMMENT_NODE && $child->nodeType != XML_TEXT_NODE && $child->nodeName != '#comment') {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks whether str as a namespace and adds default: to it if not
	 * @param String $str
	 */
	protected function addNamespace($str) {
		if(strpos($str,":") === false) {
			return "default:".$str;
		}
		return $str;
	}
	
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
	protected function registerXPathNamespaces($file,DOMXPath &$path) {
		// DOMDocument doesn't seem to support namespace extraction, so SimpleXML is used
		$xml = simplexml_load_file($file);
		foreach($xml->getNamespaces(true) as $prefix=>$uri) {
			if($prefix == "")
				$prefix = "default";
			$path->registerNamespace($prefix,$uri);
			$this->registeredNS[$prefix] = $uri;
		}	
		
	}
}