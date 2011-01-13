<?php

class HostDetailTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		info("Testing API: Host details \n",true);
	}

	public function hostProvider() {
		$model = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$host = $model->getHostsByName(array("c1-db1"));
		return $host->getFirst();
	}
	
	public function testGetHost() {
		$model = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$host = $model->getHostsByName(array("c1-db1","test"));
		$this->assertFalse(is_null($host),"Host request returned null");
		$this->assertEquals($host->count(),1,"Number of hosts is wrong, should be 1, is ".$host->count());
		
		info("Getting host succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetStatus() {
		$host = $this->hostProvider();
		// Test if status is available
		$this->assertFalse(is_null($host->status),"Couldn't receive host status");
		$this->assertEquals($host->status->host_object_id,$host->host_object_id, 'Host id in status object is not the one of the original host');
		
		$this->assertEquals($host->status->host,$host, 'Host object returned by status->host is not the original host');
		info("Getting host status succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetId() {
		$host = $this->hostProvider();
		$this->assertEquals($host->host_id,181,"Id of returned host is wrong, should be 181 in the dbFixture");
		info("Getting host id succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetInstance() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->instance));
		$this->assertEquals($host->instance->instance_name,"default");
		info("Getting host instance succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetAlias() {
		$host = $this->hostProvider();
		$this->assertEquals($host->alias,"company1-datenbank1");
		info("Getting host alias succeeded\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetDisplayname() {
		$host = $this->hostProvider();
		$this->assertEquals($host->display_name,"c1-db1");
		info("Getting host display_name succeeded\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetAddress() {
		$host = $this->hostProvider();
		$this->assertEquals($host->address,"10.10.100.31");
		info("Getting host address succeeded\n");
	}
	

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetEventhandlerCommand() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->eventHandlerCommand));
		info("********** DB Fixture upgrade needed - no eventhandler commands available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetNotificationTimeperiod() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->notificationTimeperiod));
		$this->assertEquals($host->notificationTimeperiod->alias,'"Normal" Working Hours');	
		info("Getting notification timeperiod succeeded\n");		
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetStateHistory() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->stateHistory));
		$this->assertEquals($host->stateHistory->count(),4);
		info("Getting host history succeeded\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetEscalations() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->escalations));
		info("********** DB Fixture upgrade needed - no hostescalations available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetDowntimeHistory() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->downtimeHistory));

		info("********** DB Fixture upgrade needed - no downtimehistory available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	public function testGetDependencies() {
		$this->fail("Not implemented");
	}
	public function testGetHostgroups() {
		$this->fail("Not implemented");
	}
	public function testGetScheduledDowntimes() {
		$this->fail("Not implemented");
	}
	public function testGetCustomvars() {
		$this->fail("Not implemented");
	}
	public function testGetTimedEvents() {
		$this->fail("Not implemented");
	}
	public function testGetContacts() {
		$this->fail("Not implemented");
	}
	public function testGetContactgroups() {
		$this->fail("Not implemented");
	}
	public function testGetHostChecks() {
		$host = $this->hostProvider();
		$host->checks->toArray();
		$this->assertFalse(is_null($host->checks));
		info("********** DB Fixture upgrade needed - no hostchecks available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object");
	}	
	public function testGetServices() {
		$this->fail("Not implemented");
	}

}
