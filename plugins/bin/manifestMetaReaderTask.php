<?php


require_once "manifestBaseClass.php";

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