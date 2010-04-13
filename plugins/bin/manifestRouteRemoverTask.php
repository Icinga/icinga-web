<?php

require_once "manifestBaseClass.php";

class ManifestRouteRemoverTask extends manifestBaseClass {

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
			$routeToRemove = $configSearcher->query("//default:configuration[@context='".$context."']//default:route[@name='".$name."']")->item(0);
			if(!$routeToRemove)
			throw new BuildException("Route ".$name." not found!");
			$node = $routeToRemove->parentNode;
			$routeToRemove->parentNode->removeChild($routeToRemove);
			// remove empty branches
			$this->checkIfNodeIsEmpty($node);
				
			echo "Removing route ".$name."\n";
		}

		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}

	protected function checkIfNodeIsEmpty(DOMNode $node) {
		if($node->nodeName == "routes") {
			foreach($node->childNodes as $child) {
				if($child->nodeName == "route")
				return true;
			}
		}
		$node->parentNode->parentNode->removeChild($node->parentNode);

		return true;
	}


	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}

}