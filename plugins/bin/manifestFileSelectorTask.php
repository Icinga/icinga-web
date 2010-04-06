<?php

require_once "manifestBaseClass.php";

class manifestFileSelectorTask extends manifestBaseClass {

    private $source = null;
    /**
     * The setter for the attribute "message"
     */

    public function setPropertyName($propertyName) {
    	$this->propertyName = $propertyName;
    }
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
    	parent::main();
		$this->fetchFileList();
	}
	
	protected function fetchFileList() {
		$files;
		switch($this->getSource())	{
			case 'Config':
				$files = $this->getConfigList();
				break;
			case 'Plugin':
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
	
	protected function getFileList() {
		$xml = $this->getXMLObject();
		$files = $xml->Files;
		$fileset = new FileSet();
		$icingaPath = $this->project->getUserProperty("PATH_Icinga");
		$coreDir = $this->project->getUserProperty("coreDir");
		$fileset->setDir($icingaPath);
		
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
	
	protected function getPath($path) {
		$icingaPath = $this->project->getUserProperty("PATH_Icinga");
		return str_replace($icingaPath,"",$path);
	}
	
	protected function getConfigList() {
		$config = new FileSet();
		$config->setDir($this->project->getUserProperty("PATH_Icinga"));
		$name = $this->project->getUserProperty("PLUGIN_Name");
		$config->setIncludes("app/config/plugins/".$name.".xml");
		return $config;	
	}
	
	protected function getDBModels() {
		$xml = $this->getXMLObject();
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

	protected function getSQLDescriptors() {
		$xml = $this->getXMLObject();
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
	
	protected function loadModelsWithPrefix(&$includes,$prefix) {
		$xml = $this->getXMLObject();
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
