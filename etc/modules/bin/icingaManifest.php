<?php
require_once "phing/types/DataType.php";
/**
 * Datatype representing a manifest.xml
 * Must be passed by reference to various installer classes
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class icingaManifest extends DataType {
	/**
	 * The manifest.xml in SimpleXML representation
	 * @var SimpleXMLElement
	 */
	private static $xml = array();
	/**
	 * The path to manifest.xml
	 * @var string
	 */
	protected $file = null;
	/**
	 * The name of the module
	 * @var string
	 */
	protected $moduleName;
	/**
	 * The author of the module
	 * @var string
	 */
	protected $moduleAuthor;
	/**
	 * The description of the module
	 * @var string
	 */
	protected $moduleDescription;
	/**
	 * The module version
	 * @var string
	 */
	protected $moduleVersion;
	/**
	 * The company
	 * @var string
	 */
	protected $moduleCompany;

	public function setModuleName($name) {
		$this->moduleName = $name;
	}
	public function setModuleAuthor($author) {
		$this->moduleAuthor = $author;
	}
	public function setModuleDescription($desc) {
		$this->moduleDescription = $desc;
	}
	public function setModuleVersion($vers) {
		$this->moduleVersion = $vers;
	}
	public function setModuleCompany($company) {
		$this->moduleCompany = $company;
	}
	/**
	 * Loads an xml if not done yet
	 */
	public function lazyLoad() {
		if(empty($this->xml))
			$this->main();
	}

	public function getModuleName() {
		$this->lazyLoad();
		return $this->moduleName;
	}
	public function getModuleVersion() {
		$this->lazyLoad();
		return $this->moduleVersion;
	}
	public function getModuleAuthor() {
		$this->lazyLoad();
		return $this->moduleAuthor;
	}
	public function getModuleDescription() {
		$this->lazyLoad();
		return $this->moduleDescription;
	}
	public function getModuleCompany() {
		$this->lazyLoad();
		return $this->moduleCompany;
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getFile() {
		return $this->file;
	}
	/**
	 * TODO: Is there a possibility to check if we are in a static context?
	 * Returns the xml from a static context
	 * @return SimpleXMLElement
	 */
	static public function s_getManifestAsSimpleXML() {
		return self::$xml;
	}
	/**
	 * Returns a manifest as SimpleXML
	 * @return SimpleXMLElement
	 */
	public function getManifestAsSimpleXML() {
		$this->lazyLoad();
		return $this->xml;
	}
	
	/**
	 * Parses the file and extracts the meta data
	 */
	public function main() {
		$this->xml = $this->parseFile();
		$this->extractMetaData();
	}

	public function getManifest($name) {
		return $this->xml;
	}
	
	/**
	 * Loads the file as an SimpleXML, resolves the paths and returns a
	 * SimpleXMLElement
	 * @return SimpleXMLElement
	 */
	protected function parseFile() {
		$file = $this->getFile();
		//	libxml_use_internal_errors();
		$XML = simplexml_load_file(realpath($file));

		if(!$XML) {
			$this->showErrors();
			throw new BuildException("Invalid XML!");
		}
		$XML = $this->resolveManifestVars($XML,$file);
		
		return $XML;
	}

	/**
	 * Resolves all %TOKEN% with their corresponding values, exports them as properties and
	 * returns a fully resolved SimpleXML file
	 * 
	 * @param SimpleXMLElement $xml The unresolved SimpleXML (for data extraction)
	 * @param string $file The .xml file path
	 * @return SimpleXMLElement
	 */
	protected function resolveManifestVars($xml,$file) {
		$project = $this->getProject();
		$project->setUserProperty("MODULE_Name",(String) $xml->Meta->Name);
		$xmlString = file_get_contents($file);
		// Read paths
		if(isset($xml->Files->Paths))	{
			foreach($xml->Files->Paths->children() as $pathName=>$path)	{
				if(!$project->getProperty("PATH_".(String) $pathName))
				$project->setUserProperty("PATH_".(String) $pathName,(String) $path);
			}
		}
		$properties = $project->getUserProperties();
		// Resolve reference in paths
		foreach($properties as $property=>$value) {
			foreach($properties as &$pathValue) {
				$pathValue = str_replace("%".$property."%",$value,$pathValue);
			}
		}
		// Resolve paths
		foreach($properties as $property=>$value) {
			$xmlString = str_replace("%".$property."%",$value,$xmlString);
		}

		return (simplexml_load_string($xmlString));
	}

	protected function showErrors() {
		printf("Invalid XML!\n The following errors occured:\n");
		$errors = libxml_get_errors();
		foreach($errors as $error) {
			printf("\n".$error->line." : ".$error->message." (Code ".$error->code().")");
		}
	}

	public function getRef(Project $p) {
		if (!$this->checked ) {
			$stk = array();
			array_push($stk, $this);
			$this->dieOnCircularReference($stk, $p);
		}
		$o = $this->ref->getReferencedObject($p);
		if ( !($o instanceof icingaManifest) ) {
			throw new BuildException($this->ref->getRefId()." doesn't denote an icingaManifest");
		} else {
			return $o;
		}
	}


	protected function extractMetaData() {
		$meta = $this->xml->Meta;
		$this->setModuleName((String) $meta->Name);
		$this->setModuleVersion((String) $meta->Version);
		$this->setModuleDescription((String) $meta->Description);
		$this->setModuleAuthor((String) $meta->Author);
		$this->setModuleCompany((String) $meta->Company);
	}

}