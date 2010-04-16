<?php


require_once "manifestBaseClass.php";
/**
 * Reads the meta data the manifest.xml and stores it into UserProperties
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class ManifestMetaReaderTask extends manifestBaseClass {

	public function main() {
    	parent::main();
		$this->readMeta();
	}
	
	protected function readMeta() {
		$xml = $this->getXMLObject();
		$meta = $xml->Meta;
		$this->project->setUserProperty("PLUGIN_Name",(String) $meta->Name);	
		$this->project->setUserProperty("PLUGIN_Version",(String) $meta->Version);
		$this->project->setUserProperty("PLUGIN_Description",(String) $meta->Description);
		$this->project->setUserProperty("PLUGIN_Author",(String) $meta->Author);
		$this->project->setUserProperty("PLUGIN_Company",(String) $meta->Company);
	}
}