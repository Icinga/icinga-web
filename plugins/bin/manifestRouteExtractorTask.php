<?php

require_once "phing/Task.php";
require_once "manifestStore.php";

class ManifestRouteExtractorTask extends Task {
    private $file = null;
    private $toFile = null;
	private $xmlObject = null;
	
    public function setFile($str) {
        $this->file = $str;
    }
    public function setTofile($target) {
    	$this->toFile = $target;
    }
    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }
	public function getFile() {
		return $this->file;
	}
	public function getTofile() {
		return $this->toFile;
	}
    public function getXMLObject() {
    	return $this->xmlObject;
    }
    
    public function init() {
		 
    }
	
    public function main() {
    	$file = $this->getFile();
    	$DOM = new DOMDocument("1.0","UTF-8");
    	$DOM->load($file);
		$this->setXMLObject($DOM);
		$this->extractRoutes();
	}
	
	public function extractRoutes() {
		$routeXML = new DOMDocument("1.0","UTF-8");
		$routeXML->load($this->project->getUserProperty("PATH_Icinga")."app/config/routing.xml");
		
		$manifest = $this->getXMLObject();
		$routes = $manifest->getElementsByTagName("Route");
		
		$ResultDOM = new DOMDocument("1.0","UTF-8");
		$rootNode = $ResultDOM->createElementNS("http://agavi.org/agavi/1.0/config","configurations");
		$contextNodes = array();
		foreach($routes as $routeToSearch) {
			$name = $routeToSearch->getAttribute("name");
			$name = explode(".",$name);
			$context = $routeToSearch->getAttribute("context");
			$node = $ResultDOM->createElement("RouteDefinition");
			$node->setAttribute("context",$context);
			$node->setAttribute("fullname",implode(".",$name));
			
			$xpath = "/default:configurations/default:configuration";
			if($context != "")
				$xpath .= '[@context="'.$context.'"]';
			$first = true;
			foreach($name as $currentNameElement) {
				if(!$first)  // add the dot to the route
					$currentNameElement = ".".$currentNameElement;
				$xpath .= '//*[@name="'.$currentNameElement.'"]';
				$first = false;
			}
			
			$xpathObject = new DOMXPath($routeXML);
			$xpathObject->registerNamespace("default","http://agavi.org/agavi/1.0/config");
			$entries = $xpathObject->query($xpath);
			
			if($entries->length == 0)
				throw new BuildException("Couldn't find route ".implode(".",$name));
			
			foreach($entries as $entry) {
				$node->appendChild($ResultDOM->importNode($entry,true));
			}
			$rootNode->appendChild($node);
		}	
		$ResultDOM->appendChild($rootNode);
		$ResultDOM->formatOutput = true;
		
		$ResultDOM->save($this->getTofile());
	}
	
}