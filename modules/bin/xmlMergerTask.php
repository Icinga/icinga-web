<?php
/**
 * Task that merges two XML files via (simplified) XPath comparisons
 * 
 * For the index format, see @see xmlHelperTask.php
 * 
 */
require_once("actionQueueTask.php");
require_once("xmlHelperTask.php");

$vers = phpversion();
if($vers[1] < 3) 
	define("USE_XML_NSPREFIX_WORKAROUND",true);
else 
	define("USE_XML_NSPREFIX_WORKAROUND",false);
	
class xmlMergerTask extends xmlHelperTask {
	/**
	 * The xml target file to merge to
	 * @var string
	 */
	protected $target = null;
	/**
	 * The source xml file to merge with
	 * @var string
	 */
	protected $source = null;
	/**
	 * Flag that determines whether to overwrite existing nodes
 	 * @var boolean
	 */
	protected $overwrite = false;
	/**
	 * Are duplicated nodes allowed
	 * @var boolean
	 */
	protected $allowDuplicates = false;
	protected $manifestRef = false;
	protected $actionQueue = null;
	
	private $dom_source = null;
	private $dom_target = null;
	private $xpath_source = null;
	private $xpath_target = null;
		
	// Internal objects for merge
	private $__sourceIndex= array();
	private $__targetIndex= array();	
	
	public function setActionQueue($ref) {
		$this->actionQueue = $ref;
	}
	public function setTarget($target) {
		$this->target = $target;
	}
	public function setSource($source) {
		$this->source = $source;
	}
	public function setOverwrite($bool) {
		$this->overwrite = (boolean) $bool;
	}
	public function setAllowDuplicates($bool) {
		$this->allowDuplicates = (boolean) $bool;
	}
	public function setTargetDOM(DOMDocument $dom) {
		$this->dom_target = $dom;
	}
	public function setSourceDOM(DOMDocument $dom) {
		$this->dom_source = $dom;
	}
	public function setTargetXPath(DOMXPath $xpath) {
		$this->xpath_target = $xpath;
	}
	public function setSourceXPath(DOMXPath $xpath) {
		$this->xpath_source = $xpath;
	} 
		
	public function getActionQueue() {
		return actionQueueTask::getInstance();
	}
	public function getTarget() {
		return $this->target;
	}
	public function getSource() {
		return $this->source;
	}
	public function getOverwrite() {
		return $this->overwrite;
	}
	public function getAllowDuplicates() {
		return $this->allowDuplicates;
	}
	public function getTargetDOM() {
		return $this->dom_target;
	}
	public function getSourceDOM() {
		return $this->dom_source;
	}
	public function getTargetXPath() {
		return $this->xpath_target;
	}
	public function getSourceXPath() {
		return $this->xpath_source;
	}
		
	
	public function init() {
	}

	public function main() {
		$this->setupDOM();	
		$this->prepareMerge();
		$this->doMerge();
		$this->save();
	}
	
	private function save() {
		//save for queue
		$this->getActionQueue()->registerFile($this->getTarget());	
		$this->getTargetDOM()->save($this->getTarget());
		$this->reformat($this->getTarget());
	}
	/**
	 * Prepares the document, registers the Namespaces and sets up the DOMXPath searcher
	 * 
	 */
	private function setupDOM() {
		$target_file = $this->getTarget();
		$source_file = $this->getSource();
		if(!file_exists($target_file))
			throw new BuildException("Unknown XML-Merger target ".$target_file);
		if(!file_exists($source_file)) 
			throw new BuildException("Unknown XML-Merger source ".$source_file);

		$dom_target = new DOMDocument("1.0","UTF-8");
		$dom_source = new DOMDocument("1.0","UTF-8");
		$dom_target->load($target_file);
		$dom_source->load($source_file);
		$dom_target->formatOutput = true;
		$dom_source->formatOutput = true;
	
		$path_target = new DOMXPath($dom_target);
		$path_source = new DOMXPath($dom_source);
		
		$this->registerXPathNamespaces($target_file,$path_target);
		$this->registerXPathNamespaces($source_file,$path_source);

		$this->setTargetDOM($dom_target);
		$this->setSourceDOM($dom_source);
		
		$this->setTargetXPath($path_target);
		$this->setSourceXPath($path_source);
	}
	
	/**
	 * Creates the indizes for the source and target
	 */
	private function prepareMerge() {
		$index_target = $this->buildXPathIndex($this->getTargetXPath());
		$index_source = $this->buildXPathIndex($this->getSourceXPath());
		$this->__targetIndex = $index_target;
		$this->__sourceIndex = $index_source;
	}
	
	/**
	 * Merges via index key comparisons. If the index exists in the target,
	 * the new node will be inserted there, otherwise the nearest parent is searched and the 
	 * fragement will be appended to it
	 */
	private function doMerge() {
		$src_index = $this->__sourceIndex;
		$tgt_index = $this->__targetIndex;
		foreach($src_index as $index=>$nodes) {
			if(isset($tgt_index[$index]))
				$this->mergeMatching($index,$nodes);
			else 
				$this->mergeParents($index,$nodes);
		}
	}
	
	/**
	 * Insert the nodes in $nodes at the index $index
	 * @param string $index The current position in the target and source tree
	 * @param array $nodes The nodes to add
	 */
	private function mergeMatching($index,array &$nodes) {
		$src = $this->__sourceIndex;
		$tgtDOM = $this->getTargetDOM();
		$tgt_index = $this->__targetIndex;

		foreach($nodes as $path=>$node) {
			if($node["real"]) {//element has no children 
				if($this->getOverwrite()) {
					$tgt_index[$index][0]["elem"]->nodeValue = $node["elem"]->nodeValue;
					continue;
				} else if (!$this->getAllowDuplicates()) {
					$dups = false;
					// check if node already exists
					foreach($tgt_index[$index] as $targetNode) {
						if($targetNode["elem"]->nodeValue == $node["elem"]->nodeValue) {
							$dups = true;
							break;
						}
					}
					if($dups)
						continue;
				}
				$tgt_index[$index][0]["elem"]->parentNode->appendChild($tgtDOM->importNode($node["elem"],true));
			}
		}
	}
	
	/**
	 * Climbs down the index tree until a matching parent is found and reconstructs the xml
	 * fragement in the targetDOM
	 * @param string $index The index of the source xml
	 * @param array $nodes The nodes to append
	 */
	private function mergeParents($index, array &$nodes) {
		$path_splitted = explode("/",$index);
		$target = $this->__targetIndex;
		foreach($nodes as $path=>$node) {
			if(!$node["real"]) // only process nodes without children
				continue;
			array_pop($path_splitted);
			
			while(!isset($target[implode($path_splitted,"/")]) && count($path_splitted)>1) {
				if($node["elem"]->parentNode) 	
					$node["elem"] = $node["elem"]->parentNode;
	
				array_pop($path_splitted);
			}
			
			$pathToAdd = implode($path_splitted,"/");

			if($node["elem"]->parentNode) {
				$newNode = $node["elem"]->parentNode->removeChild($node["elem"]);
				// get the prefix
				$prefix = preg_split("/:/",$newNode->nodeName);
				$prefix = (count($prefix) == 2 ? $prefix[0] : null);
				$im_node = $this->getTargetDOM()->importNode($newNode,true);
				// PHP removes the namespace prefix of our node, reappend it
				if($prefix != null && USE_XML_NSPREFIX_WORKAROUND) 
					$im_node = $this->fixPrefix($im_node,$prefix,$newNode);
				$target[$pathToAdd][0]["elem"]->appendChild($im_node);
				
			} 
			
			$this->removeSubnodesFromSourceIndex($pathToAdd);
		}
	}
	
	/**
	 * Add prefix $prefix to $node
	 * @param DOMNode $node 
	 * @param string $prefix
	 * @param DOMNode $newNode
	 */
	protected function fixPrefix(DOMNode $node, $prefix,DOMNode $newNode = null) {
		$result = $this->getTargetDOM()->createElement($prefix.":".$node->nodeName);
		// Copy attributes
		foreach($node->attributes as $att) {
			$attrNode = $this->getTargetDOM()->createAttribute($att->name);
			$attrNode->nodeValue = $att->value;
			$result->appendChild($attrNode);
		}
		// copy children 
		foreach($node->childNodes as $child) {
			$result->appendChild($child);			
		}
		if($newNode) {
			$target = $this->getTargetDOM();
			foreach($newNode->childNodes as $child) {
				$result->appendChild($target->importNode($child,true));		
			}
		}
		return $result;
	}
	
	
	/**
	 * If a subnode is appended, all inherited nodes must be removed from the index to avoid duplicate adds
	 * @param string $path 	Path that was added 
	 */
	private function removeSubnodesFromSourceIndex($path) {
		$index = $this->__sourceIndex;
		$keys = array_keys($index);
		$pathLength = strlen($path);
		foreach($keys as $name) {
			if(substr($name,0,$pathLength) == $path) {
				unset($index[$name]); 
			}
		}
		unset($index[$path]);
	}
	
}