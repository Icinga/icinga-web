<?php
include "settingsExtractorTask.php";

/**
 * Checks for config nodes that have the "fromConfig" attribute set to true
 * and exports these settings from the agavi config
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class ManifestSettingsExtractorTask extends settingsExtractorTask {
	private $xmlObject = null;
	/**
	 * Buffer of the dom-elements to write into $toFile
	 * @var array
	 */

  
    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }
	
    public function getXMLObject() {
    	return $this->xmlObject;
    }
    

    public function main() {
    	$file = $this->getFile();
    	$DOM = new DOMDocument("1.0","UTF-8");
    	$DOM->load($file);
		$this->setXMLObject($DOM);
		$this->extractSettings();
	}
	
	/**
	 * Checks for nodes to extract and saves them to $toFile
	 * 
	 */
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
					$this->extractSetting($file,$setting->getAttribute("name"),$setting->getAttribute("pname"));
				}
			}
		}
		foreach($this->__DOMELEMENTS as $name=>$element) {
			$element["target"]->save($this->getTofile()."/".$name.".xml");
		}
	}
	
	
}