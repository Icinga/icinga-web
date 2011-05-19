<?php

class agaviBootstrapTest extends PHPUnit_Framework_TestCase {

	public $includes = array(
		'/lib/agavi/src/agavi.php',
		'/lib/doctrine/lib/Doctrine.php'
	);

	public function testLibs() {
		$success = true;
		$basePath = IcingaWebTestTool::getRootPath();
		
		for($i=0;$i<count($this->includes);$i++) {
			$success = file_exists($basePath.$this->includes[$i]);
			if($success === false) {
				break;
			}
		}
		$this->assertTrue($success);
	}
	
	/**
	* @depends testLibs
	**/
	public function testBootstrap() {
    	$ctx = AgaviContext::getInstance('testing');
    	
    	$this->assertInstanceOf('AppKitAgaviContext', $ctx);
    	$this->assertInstanceOf('AgaviDoctrineDatabase', $ctx->getDatabaseManager()->getDatabase());
    	$this->assertInstanceOf('Doctrine_Manager', $ctx->getDatabaseManager()->getDatabase()->getDoctrineManager());
    	
    	try {
    	    $ctx->getDatabaseManager()->getDatabase()->connect();
    	}
    	catch(Doctrine_Connection_Exception $e) {
    	    $this->fail('No database connection');
    	}
	}
	
}
