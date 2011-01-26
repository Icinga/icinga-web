<?php

/**
* @depends agaviBootstrapTest::testBootstrap 
*/	
class configSettingsTest extends PHPUnit_Framework_TestCase {

	public function testAPIConnection() {
		$root = AgaviConfig::get("core.root_dir");
		info("\tTesting if API Connection is available\n");
		$ctx = AgaviContext::getInstance();
		try {
			$API = $ctx->getModel("Icinga.ApiContainer","Web");		
		} catch(IcingaApiException $e) {
			error("Could not connect to API. The API Connector returned the following message:\n". 
					$e->getMessage().")\n".
				  	"Without the icinga-API you will not be able to request any information from icinga!\n");
			$this->fail("Icinga-API connection test failed. Please correct the api settings in ".$root."/app/modules/AppKit/config/module.xml and retry");
		}
		success("\tAPI Connection available\n");
	}	
	
	
}
