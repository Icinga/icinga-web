<?php

require_once "manifestBaseClass.php";

class ManifestXPathRemoverTask extends manifestBaseClass {
	protected $target;
	protected $removePath;
	protected $cfgPath;
	protected $ns;
	protected $uri;
	
    public function main() {
		parent::main();
    	$this->removeRoutes();
	}
	
	public function setTarget($target) 	{
		$this->target = $target;
	}
	public function setRemovePath($path) {
		$this->removePath = $path;
	}
	public function setCfgPath($cfg) {
		$this->cfgPath = $cfg;
	}
	public function setNs($ns) {
		$this->ns = $ns;
	}
	public function setUri($uri) {
		$this->uri = $uri;
	} 
	
	public function removeRoutes() {
		$manifest = $this->getXMLObject();
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/".$this->target;
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpaces = false;
		$configDOM->load($configPath);
		
		$configSearcher = new DOMXPath($configDOM);
		$configSearcher->registerNamespace($this->ns,$this->uri);
		$root = $this->getManifestNode();
		$counter = 0;
		foreach($root as $node) {
			$query = $this->buildQuery($node);
			$routeToRemove = $configSearcher->query($query)->item(0);
			if(!$routeToRemove) {
				echo ("\nCouldn't find a config note that was marked to be deleted - please check if you need to remove it manually!\n");
				continue;
			}
			$counter++;
			$routeToRemove->parentNode->removeChild($routeToRemove);				
		}
		echo $counter." config nodes removed in ".$this->target."\n";
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}
	
	protected function getManifestNode() {
		$manifest = $this->getXMLObject();
		return $manifest->xpath($this->cfgPath);
	}
	
	protected function buildQuery(SimpleXMLElement $node) {
		$rawPath = $this->removePath;
		foreach($node->attributes() as $attr=>$value) {
			$rawPath = str_replace("%".$attr."%",$value,$rawPath);
		}
		$rawPath = str_replace("%VALUE%",(String) $node,$rawPath);
		return $rawPath;
	}
	
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
}