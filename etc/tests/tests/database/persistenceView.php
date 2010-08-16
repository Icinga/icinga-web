<?php

class persistenceView extends AgaviPhpUnitTestCase {
	public static function setUpBeforeClass() {
		Doctrine_Manager::connection()->beginTransaction();
		$context = AgaviContext::getInstance();
		$context->getUser()->getNsmUser(true)->user_id = 1;
		$context->getUser()->addCredential("appkit.admin");
		$context->getUser()->addCredential("appkit.admin.users");
		$context->getUser()->addCredential("appkit.admin.groups");
		$context->getUser()->addCredential("icinga.user");
		$context->getUser()->setAuthenticated(true);
	}
	
	/**
	 * @depends icingaDatabaseAccessibleTest::testInsert
	 */
	public function testReadState() {
		info("Testing Persistence functions \n");
		info("\tTesting state-read\n");
		$params = array(
			"cmd" => "read",
			"id" => 1,
			"session" => "session",
			"user" => "user"
		);
		$paramHolder = new AgaviRequestDataHolder();
		$paramHolder->setParameters($params);
		$controller = AgaviContext::getInstance()->getController();
		$container = $controller->createExecutionContainer("AppKit","Ext.ApplicationState",$paramHolder,"javascript");
		try {
			$result = $container->execute();
			$data = json_decode($result->getContent(),true);
		} catch(Exception $e) {
			$this->fail("An exception was thrown during state read: ".$e->getMessage());
		}
		if(!$data["success"])
			$this->fail("Could not read view state! Your cronk settings may not be saved in icinga-web.");

		success("\tReading state succeeded!\n");
	}
	
	/**
	 * @depends testReadState
	 */
	public function testSaveState() {
		info("\tTesting state-save\n");
		$token = str_shuffle("ABCDEF123456789");
		$params = array(
			"cmd" => "write",
			"data" => '[{"name":"test-case-setting","value":"TEST_CASE_'.$token.'"}]',
			"id" => 1,
			"session" => "session",
			"user" => "user"
		);		
		$paramHolder = new AgaviRequestDataHolder();
		$paramHolder->setParameters($params);
		$controller = AgaviContext::getInstance()->getController();
		$container = $controller->createExecutionContainer("AppKit","Ext.ApplicationState",$paramHolder,"javascript","write");
		
		try {
			$result = $container->execute();
			$data = json_decode($result->getContent(),true);
		} catch(Exception $e) {
			$this->fail("An exception was thrown during state write: ".$e->getMessage());
		}
		
		// Check for success state
		if(@!$data["success"])
			$this->fail("Could not write view state! Your cronk settings may not be saved in icinga-web.");			
		
		// Finally get sure the enry is really set
		$entryFound = false;
		foreach($data["data"] as $entry) {
			if($entry["name"] == 'test-case-setting' && $entry["value"] == 'TEST_CASE_'.$token)
				$entryFound = true;
		}
		if(!$entryFound)
			$this->fail("Write returned success, but preference could not be found in DB!\n");
			
		success("\tWriting state succeeded!\n");
	}
	
	public static function tearDownAfterClass() {
		Doctrine_Manager::connection()->rollback();
	}
}