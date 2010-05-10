<?php

/**
 * Mixed wrapper class for the incredible annoying Phing InrospectionHelper class
 * that wants type hints in some functions with mixed parameters...
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */

class mixed {
	public $val = null;
	public function setValue($val) {
		$this->val = $val;
	}
	public function __toString() {
		return $this->val;
	}
	public function __construct($val) {
		$this->val = $val;
	}
}

require_once("xmlHelperTask.php");
class xmlExtractorTask extends xmlHelperTask {
	protected $ref;
	protected $path;
	private $currentFile;
	private $currentDOM;
	private $currentBase;
	private $currentTargetDOM;
	private $currentNs = array();
	
	public function setCurrentFile($currentFile) {
		$this->currentFile = $currentFile;
	}
	public function getCurrentFile() {
		return $this->currentFile;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function setRefid($ref){
		$this->ref = $ref;
	}
	
	public function getManifest() {
		return $this->ref->getReferencedObject($this->getProject());
	}
	
	public function main() {
		$manifest = $this->getManifest()->getManifestAsSimpleXML();
		$configNode = $manifest->Config;
		foreach($configNode->children() as $entryNode) {
			$this->processEntry($entryNode);
		}
	}
	
	public function processEntry(SimpleXMLElement $elem) {
		$this->currentFile = $elem['file'];
		echo "\t Extracting XML from ".$this->currentFile."\n";
		
		$this->currentTargetDOM = new DOMDocument("1.0","UTF-8");
		$this->extractNS($elem);	
		foreach($elem->children() as $nodes) {
			$this->processNodeList($nodes);
		}
		$path_icinga = $this->getProject()->getUserProperty("PATH_Icinga");
		$fname = str_replace($path_icinga,"%PATH_Icinga%",$this->currentFile);
		
		$this->currentTargetDOM->formatOutput=true;
		$this->currentTargetDOM->save($this->getPath()."/".str_replace("/","_",$fname));
	}
	
	public function processNodeList(SimpleXMLElement $nodes) {
		// check type
		if($nodes["fromConfig"] == true) {
			$this->registerNamespacesFromFile();
			$root = $this->addBaseDom($nodes);
			$this->extractNodesFromXML($nodes,$root);
		} else if($nodes["base"]) {
			$this->currentBase = $nodes["base"];
			$root = $this->addBaseDom($nodes,true);
			foreach($nodes->children() as $node) {
				$this->processNode($node,$root);
			}
		}	
	}
	
	public function addBaseDom(SimpleXMLElement $nodes,$noPlaceHolder = false) {
		$base = $nodes["base"];
		$str = preg_split("/\//",$base);
		$parent =  $this->currentTargetDOM;
		$base = '';
		foreach($str as $elem) {
			if($elem == '') {  
				if($parent ==  $this->currentTargetDOM || $noPlaceHolder)
					continue;
				// Adds a placeholder node for skipped node, so they can be resolved later
				$node = $this->currentTargetDOM->createElement("PLACEHOLDER");
				$parent->appendChild($node);
			} else {
				$node = $this->addNodeFromXPath(new mixed($elem),$parent,null,$base);
			}
			$base .= '/'.$elem;
			$parent = $node;
		}
		return $node;
	}
	
	public function addNodeFromXPath(mixed $elem,DOMNode $parent = null,DOMNode $value = null,$base='') {
		$doc = $this->currentTargetDOM;
		$docSearcher = new DOMXPath($doc);
		foreach($this->currentNs as $prefix=>$uri) {
			$docSearcher->registerNamespace($prefix,$uri);
		}
		
		$attrs = preg_split("/\[@(.*)\]/",$elem,-1,PREG_SPLIT_DELIM_CAPTURE);
		$ns = preg_split("/:/",$elem,-1,PREG_SPLIT_DELIM_CAPTURE);
		if(count($ns) == 1) {
			array_push($ns,$ns[0]);
			$ns[0] = "default";
		}
		if(!isset($this->currentNs[$ns[0]]))
			throw new BuildException("Unregistered namespace ".$ns[0]);
		$node = $doc->createElementNS($this->currentNs[$ns[0]],$attrs[0]);

		for($i=1;$i<count($attrs);$i++) {
			$attribute = preg_split("/=/",$attrs[$i],-1);
			$attribute[1] = substr($attribute[1],1,-1);
			if($attribute[0]) {
				$attDOM = $doc->createAttribute(trim($attribute[0]));
				$attDOM->value = $attribute[1];
				$node->appendChild($attDOM);		
			}
		}
		if($value) {
			$node->appendChild($value);
		}	

		$result = $docSearcher->query($base."/".$elem);
		// Check whether the node already exists
		if($result->length > 0) {
			if(!$node->nodeValue)  {  // empty node, no value check needed
				if($result->item(0)->parentNode->nodeType == XML_DOCUMENT_NODE) // two root elements are not allowed
					return $result->item(0);
				$parent = $result->item(0)->parentNode; // append to the existing node
			} else {
				foreach($result as $item) { 
					if($item->nodeValue == $node->nodeValue) {
						$parent = $item->parentNode;
					}
				}
			}
		}
		
		if($parent)
			$parent->appendChild($node);
		else 
			$doc->appendChild($node);
		return $node;
	}
	

	
	public function processNode(SimpleXMLElement $node,DOMNode $root) {
		$doc = $this->currentTargetDOM;
		$nodeToAdd = null;
		if($node["nodeName"]) 
			$nodeToAdd = $doc->createElement($node["nodeName"],(String) $node);
		else
			$nodeToAdd = $doc->createTextNode((String) $node);
			
		$this->addNodeFromXPath(new mixed((String) $node["path"]),$root,$nodeToAdd);
	}
	
	
	
	public function extractNodesFromXML(SimpleXMLElement $nodes,DOMNode $root) {
		$this->currentDOM = new DOMDocument("1.0","UTF-8");
		$this->currentDOM->load($this->currentFile);
		if(!$this->currentDOM)
			throw new BuildException("Couldn't find ".$this->currentFile." - stopping xml extraction");
		$searcher = new DOMXPath($this->currentDOM);

		$query = (String) $nodes["base"];
		foreach($this->currentNs as $prefix=>$uri) {
			$searcher->registerNamespace($prefix,$uri);
		} ;
		
		/*
		 * add default namespaces, skip attributes and contexts
		 * /ae:configuration/parameter ==> /ae:configuration/default:parameter
		 * Looks worse than it is:
		 * (?<![@':]) 	= Negative Lookbehind -> Exclude items with @,' or : at the beginning (@=attribute,' = attribute vale, : = ns-prefix)
		 * (\w+)  	= The node name to prepend the 'default:' (only non-whitespace)
		 * (?!:) 		= Negative Lookahead -> Exclude items starting with : (and therefore having a ns-prefix)
		 */
		$query = preg_replace("/\b(?<![@':])(\w+)(?!:)\b/","default:$1",$query);
		$result = $searcher->query($query);
		if(!$result || !$result->item(0))
			throw new BuildException("XPath ".$query." seems to be invalid.");	
		$result = $result->item(0);
		$root = $this->resolvePath($root,$result);		
		if($result) {
			// copy attrs
			foreach($result->attributes as $name=>$attr) {

				$attrNode = $this->currentTargetDOM->createAttribute((String) $attr->name);
				$attrNode->nodeValue = (String) $attr->value;
				$root->appendChild($attrNode);
			}
 			foreach($result->childNodes as $child) {
				$node = $this->currentTargetDOM->importNode($child,true);
				$root->appendChild($node);
			}
		} else 
			throw new BuildException("XPath ".$query." returned null");	

		return $root;
	}
	/**
	 * Resolves a path and removes the PLACEHOLDER-Tags
	 * 
	 * @param DOMNode $node 	// the result node on which $result will be appended
	 * @param DOMNode $result	// the extracted node from the target DOM
	 */
	private function resolvePath(DOMNode $root, DOMNode $result) {
		$curNode = $root->parentNode;
		$copy = $root->cloneNode(true);
		$appended = false;
		// bring result and root to the same level
		$sourceDOM = $this->currentDOM;
		$targetDOM = $this->currentTargetDOM;
		$subPath = null;
		while($curNode) {
			// Search for a placeholder tag
			if($curNode->nodeName != "PLACEHOLDER") {
				$curNode = $curNode->parentNode;
				$result = $result->parentNode;
				continue;
			}
			// get previous node
			$lastKnown = $curNode->parentNode;
		
			while($result->parentNode && $result->parentNode->nodeName != $lastKnown->nodeName) {
				$result = $result->parentNode;
				if(!$subPath) {
					$subPath = $targetDOM->importNode($result,false);
					if(!$appended)
						$subPath->appendChild($copy);
					$appended = true;				
				} else {
					$newPath = $targetDOM->importNode($result);
					$newPath->appendChild($subPath);
					$subPath = $newPath;
				}
			}
			if($subPath) {
				$lastKnown->appendChild($subPath);
				$lastKnown->removeChild($curNode);
				
			}
			$curNode = $lastKnown;
			$curNode = $curNode->parentNode;
			$subPath = null;
		}
		if($appended)
			return $copy;
		return $root;
	}
	
	/**
	 * Resolves the Path to $curNode until the dom is reached
	 *
	 * @param DOMNode $curNode
	 */
	private function resolveTo(DOMNode $curNode,DOMNode $target) {
		$sourceDOM = $this->currentDOM;
		$searcher = new DOMXPath($sourceDOM);
		
		$targetDOM = $this->currentDOM;
		$t_searcher = new DOMXPath($targetDOM);
		
		foreach($this->currentNs as $ns=>$uri) {
			$searcher->registerNamespace($ns,$uri);
			$t_searcher->registerNamespace($ns,$uri);
		}
		$result = $searcher->query("//".$curNode->nodeName)->item(0)->parentNode;
		while($result = $result->parentNode != $target) {
			$result->appendChild($nodeToAppend);
			$nodeToAppend = $result;
		}
		$targetDOM->appendChild($nodeToAppend);
	}
	
	private function registerNamespacesFromFile() {
		$this->currentNs = array();
		// DOMDocument doesn't seem to support namespace extraction, so SimpleXML is used
		$xml = simplexml_load_file($this->currentFile);
		foreach($xml->getNamespaces(true) as $prefix=>$uri) {
			if($prefix == "")
				$prefix = "default";
			$this->currentNs[$prefix] = $uri;
		}	
	}
	
	public function extractNS(SimpleXMLElement $nodes) {
		$this->currentNs = array();
		foreach($nodes->attributes() as $name=>$value) {
			if($name=="file")
				continue;
			$this->currentNs[$name] = $value;
		}
		if(empty($this->currentNs))	
			$this->currentNs["default"] = '';
	}
	
	
}