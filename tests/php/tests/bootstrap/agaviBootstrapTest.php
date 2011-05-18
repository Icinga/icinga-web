<?php

class agaviBootstrapTest extends PHPUnit_Framework_TestCase {

	public $basePath = ROOT_PATH;
	
	public $includes = array(
		'/lib/agavi/src/agavi.php',
		'/lib/doctrine/lib/Doctrine.php'
	);

	public function testLibs() {
		$success = 1;
		for($i=0;$i<count($this->includes);$i++) {
			$success = file_exists($this->basePath.$this->includes[$i]);
			if($success === false) {
				echo $this->basePath. $this->includes[$i];
				break;
			}
		}
		$this->assertTrue($success);
	}
	
	/**
	* @depends testLibs
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
			
			AgaviConfig::set('core.testing_dir', realpath(dirname(__FILE__)));
			AgaviConfig::set('core.app_dir', ROOT_PATH. DIRECTORY_SEPARATOR. 'app');
			AgaviConfig::set('core.root_dir', ROOT_PATH);
			
			Agavi::bootstrap($env);
			
			AgaviConfig::set('core.default_context', $env);
			
			AppKitAgaviUtil::initializeModule('AppKit');
			
			AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');

			$ctx = AppKitAgaviContext::getInstance('testing');
			$ctx->getDatabaseManager()->getDatabase()->connect();
			
		} catch(Exception $e) {
			$success = false;
		}
		
			ob_end_flush();
				
		$this->assertTrue($success);
	}
	
}
