<?php

require_once "manifestBaseClass.php";

class ManifestRouteExtractorTask extends manifestBaseClass {
	
    public function main() {
		parent::main();
    	$this->removeRoutes();
	}
	public function removeRoutes() {
		$manifest = $this->getXMLObject();
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/routing.xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpaces = false;
		$configDOM->load($configPath);
		
		$configSearcher = new DOMXPath($configDOM);
		$configSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		foreach($manifest->Config->Routes->children() as $route) {
			$context = $route["context"];
			$name = $route["name"];
			$routeToRemove = $configSearcher->query("//default:configuration[@context='".$context."']//route[@name='".$name."']")->item(0);
			if(!$routeToRemove)
				throw new BuildException("Route ".$name." not found!");
			$configDOM->removeChild($routeToRemove);				
		}
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}
	
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
}