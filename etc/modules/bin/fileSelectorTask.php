<?php

/**
 * Selects the files to install (or delete) for the module-installer 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class fileSelectorTask extends Task {
	/**
	 * Reference to icingaManifest
	 * @var Reference
	 */
	protected $ref;
	
	public function setRefid($ref){
		$this->ref = $ref;
	}
	
	/**
	 * Returns the icingaManifest instance
	 * @return icingaManifest
	 */
	public function getManifest() {
		return $this->ref->getReferencedObject($this->getProject());
	}
    private $source = null;
    
    /**
     * The name of the exported reference
     * @param string $propertyName
     */
    public function setPropertyName($propertyName) {
    	$this->propertyName = $propertyName;
    }
    /**
     * Which part of the module should be copied
     * ( - Module = main agavi module data
     *   - Doctrine = doctrine database models
     *   - SQL = The sql setup files 
     * @param $source
     */
    public function setSource($source) {
    	$this->source = $source;
    }
    
	public function getPropertyName() {
		return $this->propertyName;
	}
	public function getSource() {
		return $this->source;
	}
	
    public function init() {
		
    }
    
    /**
     * The main entry point method.
     */
    public function main() {
		$this->fetchFileList();
	}
	/**
	 * Fetches a FileSet and creates a reference for it
	 */
	protected function fetchFileList() {
		$files;
		switch($this->getSource())	{
			case 'Module':
				$files = $this->getFileList();
				break;
			case 'Doctrine': 
				$files = $this->getDBModels();
				break;
			case 'SQL':
				$files = $this->getSQLDescriptors();
				break;
		}
		$this->project->addReference($this->getPropertyName(),$files);
	}
	
	/**
	 * Returns a fileset of the agavi module data and the additional files
	 * @return FileSet 
	 */
	protected function getFileList() {
		$xml = $this->getManifest()->getManifestAsSimpleXML();
		$files = $xml->Files;
		$fileset = new FileSet();
		$icingaPath = $this->project->getUserProperty("PATH_Icinga");
		$coreDir = $this->project->getUserProperty("coreDir");
		$fileset->setDir($icingaPath);
		$includes ="";
		if($this->getPath($coreDir))
			$includes = $this->getPath($coreDir)."/**";
		$excludes = "";
		foreach($files->Excludes->children() as $type=>$address) {
			if($excludes != "")
				$excludes .= ",";

			$excludes .= (String) $address;
		}
		foreach($files->Additional->children() as $type=>$address) {	
			switch($type) {
				case 'File': 
					if($includes != "")
						$includes .= ",";
					$includes .= $this->getPath($address);
					break;						
				case 'Folder':
					if($includes != "")
						$includes .= ",";
					$includes .= $this->getPath($address."**");
					break;		
			}
		}

		$fileset->setIncludes($includes);
		$fileset->setExcludes($excludes);
		return $fileset;
	}
	
	/**
	 * Returns $path in relation to PATH_Icinga 
	 * @example /usr/local/icinga-web/my-path/file.txt would be my-path/file.txt
	 * @param string $path
	 * @return string $relativePath
	 */
	protected function getPath($path) {
		$icingaPath = $this->project->getUserProperty("PATH_Icinga");
		return str_replace($icingaPath,"",$path);
	}
	/**
	 * Returns a FileSet containing the doctrine DB models
	 * @return FileSet
	 */
	protected function getDBModels() {
		$xml = $this->getManifest()->getManifestAsSimpleXML();
		$db = $xml->Database;
		$modelList =  new FileSet();
		$icingaPath = $this->project->getUserProperty("PATH_Icinga");
		$appKitPath = $this->project->getUserProperty("PATH_AppKit");
		$base = $icingaPath.$appKitPath."database/models/";
		$modelList->setDir($base);
		
		if(!$db->Doctrine) {
			$modelList->setExcludes("**");
			return $modelList;
		}
		$includes = "";
		foreach($db->Doctrine->Models->children() as $type=>$content) {
			$type = (String) $type;
			$content = (String) $content;
			switch($type) {
				case 'Prefix':
					$this->loadModelsWithPrefix($includes,$content);
					break;
				case 'File': 
					if($includes != "")
						$includes .= ",";
					$includes .= ($content);
					break;						
				case 'Folder':
					if($includes != "")
						$includes .= ",";
					$includes .= ($content."/**");
					break;
			}						
		}
		
		$modelList->setIncludes($includes);
		return $modelList;
	}
	
	/**
	 * Returns a file descriptior with the sql files
	 * @return FileSet 
	 */
	protected function getSQLDescriptors() {
		$xml = $this->getManifest()->getManifestAsSimpleXML();
		$db = $xml->Database;
		$sqlList =  new FileSet();
		$sqlList->setDir($this->project->getUserProperty("PATH_Icinga"));
		$includes = null;
		if(!$db->SQLRoutines)
			$sqlList->setExcludes("**");
			return $sqlList;

		foreach($db->SQLRoutines->children() as $type=>$content) {
			$type = (String) $type;
			$content = $this->getPath((String) $content);
			switch($type) {	
				case 'File': 
					if($includes != "")
						$includes .= ",";
					$includes .= $content;
					break;						
				case 'Folder':
					if($includes != "")
						$includes .= ",";
					$includes .= $content."/**";
					break;
				}
		}
		$sqlList->setIncludes($includes);
		return $sqlList;
	}
	
	/**
	 * Adds a list of all models with the prefix $prefix to $includes 
	 * @example
	 * 		$prefix = "Nsm" would return
	 * 			NsmUser.php
	 * 			NsmRole.php
	 * 			generated/BaseNsmUser.php
	 * 			generated/BaseNsmUser.php
	 * 			...
	 * 
	 * @param string $includes The include descriptor of a FileSet
	 * @param $string $prefix  The prefix to search for
	 */
	protected function loadModelsWithPrefix(&$includes,$prefix) {
		$xml = $this->getManifest()->getManifestAsSimpleXML();
		$db = $xml->Database;
		if($includes != "")
			$includes .= ",";
		$includes .= $prefix."*.php";

		if($includes != "")
			$includes .= ",";
		$includes .= "generated/Base".$prefix."*.php";
	}
	
	

}

?>
