<?php
require_once("xmlHelperTask.php");

/**
 * Cretes the difference between two xml files. Does not remove altered entries
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */

class xmlRemoverTask extends xmlHelperTask{
	protected $ref;
	protected $path;
	private $currentFile;
	private $currentDOM;
	private $currentTargetFile;
	private $currentTargetDOM;
	private $currentNs = array();
	// Internal objects for diff
	private $__sourceIndex= array();
	private $__targetIndex= array();	
	
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
	
	public function init() {}
	
	public function main() {
		$nodes = scandir($this->getPath());
		foreach($nodes as $nodename) {
			if(substr($nodename,-4,4) != '.xml')
				continue;
			// first make a diff
			$this->xmlDiff($nodename);		
		}
	}
	/**
	 * Removes the contents of $xmlName from it's target (determined by it's name)
	 * @param string $xmlName The xml file that should be removed (%PATH_Icinga%_folder1_folder2_name.xml)
	 */
	public function xmlDiff($xmlName) {
		$icingaPath = $this->getProject()->getUserProperty("PATH_Icinga");
		$this->currentDOM = new DOMDocument("1.0","UTF-8");
		$this->currentFile = $this->getPath()."/".$xmlName;
		$this->currentDOM->load($this->currentFile);

		$this->currentTargetDOM = new DOMDocument("1.0","UTF-8");
		$this->currentTargetFile = str_replace("_","/",str_replace("%PATH_Icinga%",$icingaPath."/",$xmlName));
		$this->currentTargetDOM->load($this->currentTargetFile);
		
		$searcher_s = new DOMXPath($this->currentDOM);
		$searcher_t = new DOMXPath($this->currentTargetDOM);
		$removed;
		do {
			$removed = false;
			// index of the target (e.g. the agavi config)
			$index1 = $this->buildXPathIndex($searcher_t,true);
			// index of the xml found in .meta
			$index2 = $this->buildXPathIndex($searcher_s,true);
			// Check which indizes are matching
			$toRemove = array_intersect_key($index1,$index2);
			$this->removeValueDifferences($toRemove,$index2);
			foreach($toRemove as $name=>$element) {
				foreach($element as $node) {
					if($node["real"] && $node["elem"] ) {
						$node["elem"]->parentNode->removeChild($node["elem"]);
						$removed = true;
					}
				}
			}
			/**
			 * Go down one level on
			 */
			foreach($index2 as $name=>$element) {
				foreach($element as $node) {
					if($node["real"]) {
						$node["elem"]->parentNode->removeChild($node["elem"]);
						$removed = true;
					}
				}
			}
		} while($removed);
		
		$this->currentTargetDOM->save($this->currentTargetFile);
	}
	/**
	 * Marks elements with altered or different value as not-removable
	 * @param array $toRemove
	 * @param array $index2
	 */
	public function removeValueDifferences(array &$toRemove,array &$index2) {
		$matches = false;
		foreach($toRemove as $name=>&$element) {
			$origElem = $index2[$name];
			foreach($element as &$node) {
				$matches = false;
				if(!$node["real"])
					continue;
				if(!trim($node["value"]))
					continue;
				foreach($origElem as $origNode) {
					if($origNode["value"] == $node["value"]) {
						$matches = true;
					}
					break;
				}
				if(!$matches) {
					$node["real"] = false;
				}
			}
		}
	}
	
	
}
