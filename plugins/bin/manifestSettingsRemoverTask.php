<?php
	
class ManifestSettingsRemoverTask extends Task {
    private $file = null;
	private $xmlObject = null;
	
    public function setFile($str) {
        $this->file = $str;
    }

    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }
	public function getFile() {
		return $this->file;
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
		
		$manifest = $this->getXMLObject();
		$manifest->preserveWhiteSpace = false;
		$manifestSearcher = new DOMXPath($manifest);
		$cfgFiles = $manifestSearcher->query("//Config/Files/*");
		foreach($cfgFiles as $file) {
			$file = $file->nodeName;
			$this->removeConfigVars($file);
		}
    }

	
	public function removeConfigVars($file) {
		$manifest = $this->getXMLObject();
		$manifestSearcher = new DOMXPath($manifest);
		
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/".strtolower($file).".xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($configDOM);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$xpathSearcher->registerNamespace("xi","http://www.w3.org/2001/XInclude");
		
		$settings = $manifestSearcher->query("//Config/Files/".$file."/*");
		foreach($settings as $config) {
			$query = '';
			$nodeToRemove = null;
			$attr = $config->getAttribute("name");
			$type = $config->getAttribute("type");
			$isXML = $config->getAttribute("asXML");
			$pname = $config->getAttribute("pname");
			// Check if it is a named parameter
			if($pname) {
				$query = "//default:setting[@name='".$attr."']//default:parameter[@name='".$pname."']";
				$nodeToRemove = $xpathSearcher->query($query)->item(0);
			} else {
				$query = "//default:setting[@name='".$attr."']/default:parameter";
				$nodes = $xpathSearcher->query($query);
				foreach($nodes as $node) {
					if($node->nodeValue == $config->nodeValue)  {
						$nodeToRemove = $node;
						break;
					}
				}
			}
			if(!$nodeToRemove) {
				echo "\nCouldn't find node $attr";
				continue;
			}
			$nodeToRemove->parentNode->removeChild($nodeToRemove);
		}
		$pluginName = $this->project->getUserProperty("PLUGIN_Name");
		// remove reference
		$includes = $xpathSearcher->query("//xi:include[@href='plugins/".$pluginName.".xml']")->item(0);
		if($includes) 
			$includes->parentNode->removeChild($includes);
			
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

?>