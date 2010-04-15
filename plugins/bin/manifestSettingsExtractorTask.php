<?php
class ManifestSettingsExtractorTask extends Task {
    private $file = null;
    private $toFile = null;
	private $xmlObject = null;
	private $__DOMELEMENTS = array();
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
		$this->extractSettings();
	}
	
	protected function extractSettings() {
		$manifest = $this->getXMLObject();
		$manifestXPath = new DOMXPath($manifest);
		$settings = $manifestXPath->query("//Config/Files/*");
		foreach($settings as $fileNode) {
			$file = $fileNode->nodeName;
			if(!$fileNode->hasChildNodes())
				continue;
			
			foreach($fileNode->childNodes as $setting)  {
				if($setting->nodeType != XML_ELEMENT_NODE)
					continue;

				if($setting->getAttribute("fromConfig")) {
					$this->extractSetting($file,$setting);
				}
			}
		}
		foreach($this->__DOMELEMENTS as $name=>$element) {
			$element["target"]->save($this->getTofile()."/".$name.".xml");
		}
	}
	
	public function extractSetting($path,DOMElement $setting) {
		
		$routeXML = new DOMDocument("1.0","UTF-8");
		$routeXML->load($this->project->getUserProperty("PATH_Icinga")."app/config/".strtolower($path).".xml");
		$target;
		$root;
		if(!isset($this->__DOMELEMENTS[$path]))	 {
			$target = $target = new DOMDocument("1.0","UTF-8");
			$root = $target->createElementNS("http://agavi.org/agavi/1.0/config","settings");
			$target->appendChild($root);
			$this->__DOMELEMENTS[$path] = array("target"=>$target,"root"=>$root);
		} else {
			$target = $this->__DOMELEMENTS[$path]["target"];
			$target = $this->__DOMELEMENTS[$path]["root"];
		}
		$manifest = $this->getXMLObject();
		
		$searcher = new DOMXPath($routeXML);
		$searcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$pname = $setting->getAttribute("pname");
		$name = $setting->getAttribute("name");
		$query = "";

		if($pname)	{
			$query = "//default:setting[@name='".$name."']//default:parameter[@name='".$pname."']";
		} else {
			$query = "//default:setting[@name='".$name."']";
		}
		$results = $searcher->query($query);

		if($results->length == 0) {
			echo "Couldn't find setting ".$name.(($pname)?"/".$pname : "")."\n";
			return null;
		}
		
		foreach($results as $result) {
			$rootNode = $root;
			if($pname) {
				$rootNode = $target->createElement("setting");
				$rootNode->setAttribute("name",$name);
				$root->appendChild($rootNode);
			}
			$rootNode->appendChild($target->importNode($result,true));
		}
		
	}
	
}