<?php
/**
 * Singleton class that holds the manifest file DOM and extracts PATH Variables
 * Prevents the manifest to be loaded x times as a simple_xml object 
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class manifestStore {
	private static $xml = array();
	
	public static function getManifest($name,$project = null) {
		if(!self::$xml[$name]) {
			self::$xml[$name] = self::parseFile($name, $project);
		}
		return self::$xml[$name];
	}
	
	protected static function parseFile($file,$project) {
	//	libxml_use_internal_errors();
		$XML = simplexml_load_file(realpath($file));

		if(!$XML) {
			self::showErrors();
			throw new BuildException("Invalid XML!");
		}
		$XML = self::resolveManifestVars($XML,$file,$project);
		return $XML;
	}
	
	
	protected static function resolveManifestVars($xml,$file,$project) {
		
		$project->setUserProperty("PLUGIN_Name",(String) $xml->Meta->Name);
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
		
	protected static function showErrors() {
		printf("Invalid XML!\n The following errors occured:\n");
		$errors = libxml_get_errors();
		foreach($errors as $error) {
			printf("\n".$error->line." : ".$error->message." (Code ".$error->code().")");
		}
	}
	
}