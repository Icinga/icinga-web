<?php

class agaviBootstrapTest extends PHPUnit_Framework_TestCase {

	public $basePath = "/usr/local/icinga-web";
	
	public $includes = array(
		'/lib/agavi/src/agavi.php',
		'/app/modules/AppKit/lib/AppKit.class.php',
		'/app/modules/AppKit/lib/class/AppKitBaseClass.class.php',
		'/app/modules/AppKit/lib/class/AppKitSingleton.class.php',
		'/app/modules/AppKit/lib/module/AppKitModuleUtil.class.php'
	);

	public function testRequiredFileAvailabilty() {
		$success = 1;
		for($i=0;$i<count($this->includes);$i++) {
			$success = file_exists($this->basePath.$this->includes[$i]);
			if($success === false)
				error("Could not include ".$this->includes[$i]);
		}
		//info("Checking if icinga-web files are accessible\n");
		$this->assertTrue($success);
	}
	
	/**
	* @depends testRequiredFileAvailabilty
	**/
	public function testBootstrap() {
		$success = true;
		if(isset($arguments['environment'])) {
			$env = $arguments['environment'];
			unset($arguments['environment']);
		} else {
			$env = 'testing';
		}
		require_once($this->basePath.'/lib/agavi/src/agavi.php');
		try {
			include('config.php');
			Agavi::bootstrap($env);
			AgaviConfig::set('core.default_context', $env);
			// Initialize the appkit framework
			foreach($this->includes as $include)
				require_once($this->basePath.$include);
			AgaviController::initializeModule('Web');
			AgaviController::initializeModule('AppKit');
			AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');

			$ctx = AgaviContext::getInstance();
			$ctx->getDatabaseManager()->getDatabase()->connect();

		} catch(Exception $e) {
			error("Icinga bootstrap failed with message : ".$e->getMessage()."\n");
			$success = false;
		}
		ob_end_flush();		
		$this->assertTrue($success);
	}
	
}
