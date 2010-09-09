<?php
require_once "phing/Task.php";
/**
 * Injects settings to an agaviconfig
 * @FIXME: use xmlMergerTask for this
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class settingsInjectorTask extends Task {
	protected $source = null;
	protected $cfgTarget = null;
	protected $settingsToImport = array();
	
	public function setSource($src) {
		$this->source = $src;
	}
	public function setCfg($cfg) {
		$this->cfgTarget = $cfg;
	}
	public function setSettingstoimport($str) {
		$this->settingsToImport = explode(",",$str);
	}
	
	public function getSource() {
		return $this->source;
	}
	public function getCfg() {
		return $this->cfgTarget;
	}
	public function getSettingsToImport() {
		return $this->settingsToImport;
	}
	
	public function init() {}
	
	public function main() {
		// load targetDOM
		$DOMTarget = new DOMDocument("1.0","UTF-8");
		$DOMTarget->load($this->getCfg());
		$targetSearcher = new DOMXPath($DOMTarget);
		$targetSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$targetSearcher->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
				
		$DOMSource = new DOMDocument("1.0","UTF-8");
		$DOMSource->load($this->getSource());
		echo $this->getSource();
		$sourceSearcher = new DOMXPath($DOMSource);
		$sourceSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$sourceSearcher->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
				
		$settingsToImport = $this->getSettingsToImport();
		foreach($settingsToImport as $setting) {
			$setting = explode("/",$setting); // distinguish between setting and param
			$settingName = $setting[0];
			
			$paramName = isset($setting[1]) ? $setting[1] : null;
			$query = "//setting".($settingName ? "[@name='".$settingName."']" : '').
					  	($paramName ? "/ae:parameter".($paramName != '*' ? "[@name='".$paramName."']" : '') : '')."
					  |//default:setting".($settingName ? "[@name='".$settingName."']" : '').
					  	($paramName ? "/ae:parameter".($paramName != '*' ? "[@name='".$paramName."']" : '') : '');
			echo $query;
			if($targetSearcher->query($query)->length) {
				echo "Item ".$settingName."/".$paramName." already exists! \n";
				continue;
			}
			
			$sourceResult = $sourceSearcher->query($query)->item(0);
			if(!$sourceResult) {
				echo "Item ".$settingName."/".$paramName." not found! \n";
				continue;
			}
			
			$DOMTarget->getElementsByTagName("setting")->item(0)->appendChild($DOMTarget->importNode($sourceResult,true));
		}
		$DOMTarget->formatOutput = true;
		$DOMTarget->save($this->getCfg());
	}
}
