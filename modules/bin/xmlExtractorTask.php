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
/**
 * Extracts parts of a xml document to a new one. Uses the icingaManifest to determine which
 * files to export, and how they should be exported.
 * Uses (simplified) xpath for extraction
 *  
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class xmlExtractorTask extends xmlHelperTask {
	/**
	 * Reference to @see icingaManifest.php
	 * @var Reference
	 */
	protected $ref;
	/*
	 * Path, where the xml lies
	 * @var String
	 */
	protected $path;

	// internal files
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
	
	/**
	 * Extracts the xml as described in manifest.xml
	 */
	public function main() {
		$manifest = $this->getManifest()->getManifestAsSimpleXML();
		$configNode = $manifest->Config;
		foreach($configNode->children() as $entryNode) {
			$this->processEntry($entryNode);
		}
	}
	/**
	 * Processes a <entry> node of an icingaManifest
	 * @param SimpleXMLElement $elem
	 */
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
	
	/**
	 * Processes a <nodes> node of an icingaManifest and determines whether to extract the 
	 * data from an existing xml or to write a new one
	 * @param SimpleXMLElement $nodes
	 */
	public function processNodeList(SimpleXMLElement $nodes) {
		// check type
		if($nodes["fromConfig"] == true) { // from Config
			$this->registerNamespacesFromFile();
			$root = $this->addBaseDom($nodes);
			$this->extractNodesFromXML($nodes,$root);
		} else if($nodes["base"]) {   //from manifest.xml
			$this->currentBase = $nodes["base"];
			$root = $this->addBaseDom($nodes,true);
			foreach($nodes->children() as $node) {
				$this->processNode($node,$root);
			}
		}	
	}
	/**
	 * Builds the base structure to append the final xml data to.
	 * Adds <PLACEHOLDER> Tags for skipped nodes ( node1//node2 => node1/PLACEHOLDER/node2)
	 * @param SimpleXMLElement $nodes the nodes Element
	 * @param boolean $noPlaceHolder if true, no placeholders will be created
	 */
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
	
	/**
	 * Adds a node described by an xpath
	 * @param mixed $elem The xpath
	 * @param DOMNode $parent The parent of the node or null if the root should be used
	 * @param DOMNode $value  The value of the node
	 * @param string $base the xpath to the parent
	 * @return SimpleXMLElement the added node
	 */
	public function addNodeFromXPath(mixed $elem,DOMNode $parent = null,DOMNode $value = null,$base='') {
		$doc = $this->currentTargetDOM;
		$docSearcher = new DOMXPath($doc);
		foreach($this->currentNs as $prefix=>$uri) {
			$docSearcher->registerNamespace($prefix,$uri);
		}
		// extract attributes
		$attrs = preg_split("/\[@(.*)\]/",$elem,-1,PREG_SPLIT_DELIM_CAPTURE);
		// extract namespaces
		$ns = preg_split("/:/",$elem,-1,PREG_SPLIT_DELIM_CAPTURE);
		//add default namespace when none is set
		if(count($ns) == 1) {
			array_push($ns,$ns[0]);
			$ns[0] = "default";
		}
		if(!isset($this->currentNs[$ns[0]]))
			throw new BuildException("Unregistered namespace ".$ns[0]);

		// Create the node and it's attributes
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
		// If there's a value, add it
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
		// append and return
		if($parent)
			$parent->appendChild($node);
		else 
			$doc->appendChild($node);
		return $node;
	}
	

	/**
	 * Provesses the <node> node and creates a node
	 * @param SimpleXMLElement $node
	 * @param DOMNode $root
	 */
	public function processNode(SimpleXMLElement $node,DOMNode $root) {
		$doc = $this->currentTargetDOM;
		$nodeToAdd = null;
		if($node["nodeName"]) 
			$nodeToAdd = $doc->createElement($node["nodeName"],(String) $node);
		else
			$nodeToAdd = $doc->createTextNode((String) $node);
			
		$this->addNodeFromXPath(new mixed((String) $node["path"]),$root,$nodeToAdd);
	}
	
	/**
	 * Extracts nodes from an existing xml file
	 * 
	 * @param SimpleXMLElement $nodes
	 * @param DOMNode $root
	 * 
	 * @return the new node
	 */
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
			// insert the node (the root already exists, so only the children are needed)
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
	
	/**
	 * Registers all the namespaces of a xml
	 */
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