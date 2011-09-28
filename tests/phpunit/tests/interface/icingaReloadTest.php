<?php

/**
* @depends agaviBootstrapTest::testBootstrap 
*/	
class icingaUserOperations extends PHPUnit_Framework_TestCase {
	/**
	 * @depends agaviBootstrapTest::testBootstrap 
	 */	
	public static function setUpBeforeClass() {
		try {
			Doctrine_Manager::connection()->beginTransaction();
			$context = AgaviContext::getInstance();
			$context->getUser()->addCredential("appkit.admin");
			$context->getUser()->addCredential("appkit.admin.users");
			$context->getUser()->addCredential("appkit.admin.groups");
			$context->getUser()->addCredential("icinga.user");
			$context->getUser()->addCredential("icinga.control.admin");
			$context->getUser()->setAuthenticated(true);
		} catch(Exception $e) {
			error("Exception on connection retrieval: ".$e->getMessage()."\n");
		}
	}
	
	public function testValidateConfig() {
		$context = AgaviContext::getInstance();
		$icingaCmd = $context->getModel("IcingaControlTask","Api",array());
		$code = $icingaCmd->validateConfig();

		$this->assertEquals($code,0,"Icinga validation returned code ".$code);	
	}
/*
	public function testInvalidValidation() {
		$context = AgaviContext::getInstance();		$icingaCmd = $context->getModel("IcingaControlTask","Api",array());
		$code = $icingaCmd->validateConfig("InvalidConfig!");

		$this->assertFalse($code == 0,"Invalid validation returned success"); 	
	}

	public function testIcingaReload() {
		$context = AgaviContext::getInstance();
		$icingaCmd = $context->getModel("IcingaControlTask","Api",array());
		$code = $icingaCmd->reloadIcinga();

		$this->assertEquals($code,0,"Icinga reload returned code ".$code);	
	}

	public function testIcingaStop() {
		$context = AgaviContext::getInstance();
		$icingaCmd = $context->getModel("IcingaControlTask","Api",array());
		$code = $icingaCmd->stopIcinga();
	
		$this->assertEquals($code,0,"Icinga restart returned code ".$code);
	} 
*/
/*
	public function testIcingaRestart() {
		$context = AgaviContext::getInstance();
		$icingaCmd = $context->getModel("IcingaControlTask","Api",array("host"=>"vm_host1"));
		$code = $icingaCmd->restartIcinga();

		$this->assertEquals($code,0,"Icinga restart returned code ".$code);	
	}
*/

	/**
	 * @group Interface
	 */
	public function testIcingaStatus() {
		$context = AgaviContext::getInstance();
		$icingaCmd = $context->getModel("IcingaControlTask","Api",array("host"=>"vm_host1"));
		$code = $icingaCmd->getIcingaStatus();
	
		$this->assertTrue($code < 2,"Icinga status returned code ".$code);
	}
	
}

?>
