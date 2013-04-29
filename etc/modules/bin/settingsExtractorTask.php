<?php
require_once "phing/Task.php";
/**
 * Extracts settings from a agavi config (needed for cronk export)
 * @FIXME: Use the  xmlExtractorTask for this
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class settingsExtractorTask extends Task {
    protected $file = null;
    protected $toFile = null;
	private $parameter = "";
	private $setting = "";
	private $useAbsolute = false;
	protected $__DOMELEMENTS = array();
		
	public function setFile($str) {
        $this->file = $str;
    }
    public function setTofile($target) {
    	$this->toFile = $target;
    }
    public function setUseabsolute($bool) {
    	if($bool == 1)
    		$this->useAbsolute = true;
    	else
    		$this->useAbsolute = false;
    }
	public function setParameter($pname) {
		$this->parameter = $pname;
	}
	public function setSetting($setting) {
		$this->setting = $setting;
	}
	
    public function getFile() {
		return $this->file;
	}
	public function getTofile() {
		return $this->toFile;
	}
	public function getParameter() {
		return $this->parameter;
	}
	public function getSetting() {
		return $this->setting;
	}
	
	public function init() {
		
    } 
	
	public function main() {
		$toFile = $this->getToFile().".xml";
		if(file_exists($toFile)) {
			$target = $target = new DOMDocument("1.0","UTF-8");
			$target->load($toFile);
			$root = $target->firstChild;
			$this->__DOMELEMENTS[$path] = array("target"=>$target,"root"=>$root);
		}
		$this->extractSetting($this->getFile(),$this->getSetting(),$this->getParameter());

		foreach($this->__DOMELEMENTS as $name=>$element) {
			$element["target"]->save($toFile);
		}
	}

	
	/**
	 * Reads a agavi config $path and extracts the setting described in $setting 
	 * 
	 * @param String $path
	 * @param DOMElement $setting
	 */
	public function extractSetting($path,$name,$pname) {
		if(!$this->project->getUserProperty("PATH_Icinga"))
			$this->project->setUserProperty("PATH_Icinga","../");
			
		$base = $this->project->getUserProperty("PATH_Icinga");
		$cfgPath = $this->useAbsolute ? $base.$path : $base."app/config/".strtolower($path).".xml"; 
		$routeXML = new DOMDocument("1.0","UTF-8");
		$routeXML->load($cfgPath);
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
		
		$searcher = new DOMXPath($routeXML);
		$searcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$searcher->registerNamespace("ae","http://agavi.org/agavi/1.0/envelope");
		$query = "";

		if($pname != "" && $name != "")	{
			$query = "//default:setting[@name='".$name."']//default:parameter[@name='".$pname."']|//default:setting[@name='".$name."']//ae:parameter[@name='".$pname."']";
		} else if($pname != "") {
			$query = "//default:parameter[@name='".$pname."']|//ae:parameter[@name='".$pname."']";
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