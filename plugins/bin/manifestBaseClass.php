<?php
require_once "phing/Task.php";
require_once "manifestStore.php";
/**
 * Base class for Tasks that read the manifest file and only use simple operations
 * Loads the manifest.xml to a simple_xml class.
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
abstract class manifestBaseClass extends Task {
	/**
	 * The manifest file path
	 * @var String
	 */ 
	private $file = null;
	/**
	 * The manifest XML representation
	 * @var unknown_type
	 */
	private static $xmlObject = null;

    public function setFile($str) {
        $this->file = $str;
    }
    public function setXMLObject(SimpleXMLElement $xml) {
    	self::$xmlObject = $xml;
    }
    
	public function getFile() {
		return $this->file;
	}
    public function getXMLObject() {
    	return self::$xmlObject;
    	
    }
    
    public function init() {}
    
    /**
     * Instantiates manifestStore
     */
    public function main() {
    	$file = $this->getFile();
    	if(!$file)
    		throw new BuildException("You must specify a target file for manifestreader");
   		if(!file_exists($file))
			throw new BuildException("Manifest file ".$file." doesn't exist!");
		$this->setXMLObject(manifestStore::getManifest($this->getFile(),$this->project));
    }
    

	protected function resolveManifestVars(array $exclude = array()) {
		$xml = $this->getXMLObject();
		$project=  $this->project;
		$project->setUserProperty("PLUGIN_Name",(String) $xml->Meta->Name);
		if(isset($xml->Files->Paths))	{
			foreach($xml->Files->Paths->children() as $pathName=>$path)	{
				if(in_array("PATH_".(String) $pathName,$exclude))
					continue;
				
				$project->setUserProperty("PATH_".(String) $pathName,(String) $path);			
			}
		}
		
		$xmlString = file_get_contents($this->getFile());
		foreach($project->getUserProperties() as $property=>$content) {
			$xmlString = str_replace("%".$property."%",$content,$xmlString);
		}
		$this->setXMLObject(simplexml_load_string($xmlString));
	}
}