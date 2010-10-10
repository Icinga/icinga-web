<?php
require_once "phing/mappers/FileNameMapper.php";
/**
 * @FIXME: Remove it?
 * @deprecated This class is deprecated and not longer needed
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
**/
class manifestMapper implements FileNameMapper {
	
	protected $from;
	private static $xmlObject = null;
    private static $rules = array();
    
	public function setXMLObject(SimpleXMLElement $xml) {
    	self::$xmlObject = $xml;
    }
	public function setRules(array $rules) {
		self::$rules = $rules;
	}    
	public function addRule($from,$to) {
		self::$rules[$from] = $to;
	}
    
	public function getManifest() {
		return icingaManifest::s_getManifestAsSimpleXML();
	}
	
	public function getXMLObject() {
    	return self::$xmlObject;
    }
	public function getRules() {
		if(empty(self::$rules))
			$this->buildRules();
		return self::$rules;	
	}    
     
    public function main($sourceFilename) {

		$rules = $this->getRules();
		$matches = array();
		
		foreach($rules as $from=>$to) {
			preg_match($from,$sourceFilename,$matches);
			if(!empty($matches)) {
				return array(preg_replace($from,$to,$sourceFilename));
			}
		}
		return array($sourceFilename);
	}
	
	protected function buildRules() {
		$xml = $this->getManifest();
		$additional = $xml->Files->Additional;
		
		if(!$additional)
			return null;
		foreach($additional->children() as $path) {
			if(!$path["to"])
				continue;
			$to = "../".$path["to"];
			$from = preg_replace("/\*{1,2}/","(.*)",(String) $path);
			$from = str_replace("/","\/",$from);	
			$this->addRule("/".$from."/",$to);
		}
	}

	
	protected function showErrors() {
		printf("Invalid XML!\n The following errors occured:\n");
		foreach(libxml_get_errors() as $error) {
			printf("\n".$error->line." : ".$error->message." (Code ".$error->code().")");
		}
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function setTo($path) {}
}