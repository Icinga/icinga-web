<?php

require_once "manifestBaseClass.php";

class manifestDependencyCheckerTask extends manifestBaseClass {
	
	public function main() {
		parent::main();
		$this->resolveManifestVars(array('PATH_Icinga'));
		$this->checkDependencies();
	}

	protected function checkDependencies() {	
		$xml = $this->getXMLObject();
		foreach($xml->Dependencies->children() as $dependency) {
			$this->checkFor($dependency);
		}
	}
	
	protected function checkFor(SimpleXMLElement $dependency) {
		switch($dependency->getName()) {
			case 'Icinga-Web':
				$this->checkIcingaWeb($dependency);		
				echo "Icinga-Web version is correct\n";
				break;
			case 'PHP':
				$this->checkPHP($dependency);
				echo "PHP Versions and extensions are correct\n";
				break;
		}
	}
	
	protected function checkIcingaWeb(SimpleXMLElement $dependency) {
		$version = (String) $dependency->Version;
		if($version) {
			$basePath = $this->project->getUserProperty("PATH_Icinga");
			$isMinVersion = true;
			$versionSplitted = explode(".",$version);
			$config = new DOMDocument("1.0");
			$config->load($basePath."/app/config/icinga.xml");
			if(!$config)
				throw new BuildException("Couldn't find icinga.xml");
			
			$configXPath = new DOMXPath($config);
			$configXPath->registerNamespace("default","http://agavi.org/agavi/1.0/config");

			// Check major version
			$versionEntries = $configXPath->query('//default:setting[@name="appkit.version.major"]');
			$major = $versionEntries->item(0)->nodeValue;
			if($versionSplitted[0]>$major)
				throw new BuildException("Icinga-Web has incorrect version, at least ".$version." needed");
								
			// Check minor version
			$versionEntries = $configXPath->query('//default:setting[@name="appkit.version.minor"]');
			$minor = $versionEntries->item(0)->nodeValue;
			if($versionSplitted[1]>$minor)
				throw new BuildException("Icinga-Web has incorrect version, at least ".$version." needed");

			// Check patch version
			$versionEntries = $configXPath->query('//default:setting[@name="appkit.version.patch"]');
			$patch = $versionEntries->item(0)->nodeValue;
			if($versionSplitted[2]>$patch)
				throw new BuildException("Icinga-Web has incorrect version, at least ".$version." needed");	
									
		}
		
		return true;
	}

	protected function checkPHP(SimpleXMLElement $dependency) {
		$version = (String) $dependency->Version;
		if($version) {
			if(version_compare(PHP_VERSION,$version)<0)
				throw new BuildException("You need at least PHP Version ".$version." to install this plugin");	
		}
		foreach($dependency->Extensions->children() as $extension) {
			if(!extension_loaded((String)$extension))
				throw new BuildException("Missing extension ".(String) $extension);
		}
		return true;
	}
}
	