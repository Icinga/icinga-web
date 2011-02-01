<?php

class ServiceDetailTest extends PHPUnit_Framework_TestCase {
	public function serviceProvider() {
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
		$host = $model->getServicesByName(array("pop3-gmx"),array(200));
		return $host->getFirst();	
	}
	
	public function testGetServices() {
		info("Testing service retrieval\n");
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
		$this->assertFalse(is_null($model->getServices()));
		$this->assertEquals($model->getServices(array(200))->count(),1);
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetStatus() {
		info("Testing service status retrieval\n");
		$service = $this->serviceProvider();
		$status = $service->status;
		$this->assertFalse(is_null($status),"Service status returned null");
		$this->assertEquals($status->instance_id, $service->instance_id,"Service instance doesn't match status instance");
		$this->assertTrue($status->service == $service,"Status belonging to the service is not the original status");
		success("Service status retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetId() {
		info("Testing service id retrieval\n");
		$service = $this->serviceProvider();
		$this->assertTrue(is_numeric($service->service_id),"Service id didn't return integer");
		$this->assertTrue(is_numeric($service->service_object_id),"Service object_id didn't return integer");
		success("Service id retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetInstance() {
		info("Testing instance id retrieval\n");
		$service = $this->serviceProvider();
		$this->assertTrue(is_numeric($service->instance_id),"Service id didn't return integer");
	
		success("Instance id retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetName() {	
		info("Testing name retrieval\n");
		$service = $this->serviceProvider();
		$this->assertEquals($service->display_name,'POP3',"Service name didn't match");
	
		success("Name retrieval succeeded\n");
	}


	/**
	*  We could go on and test every attribute, but if those 6 match, 
	*  there shouldn't occure anymore problems
	**/
	/**
	*	@depends testGetServices
	**/
	public function testGetCheckcommand() {
		info("Testing check command retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->checks),"Service Checks could not be retrieved, returned null");
		
		info("****DB Fixture update needed, no service checks available\n");
	
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetEventhandler() {
		info("Testing eventhandler retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->eventHandlerCommand),"Eventhandler could not be retrieved, returned null");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetTimeperiods() {
		info("Testing timeperiod retrieval for service\n");
		$service = $this->serviceProvider();
		$notifyTimeperiods = $service->notificationTimeperiod;
		$checkTimeperiods = $service->checkTimeperiod;
		$this->assertFalse(is_null($notifyTimeperiods),"Notification Timeperiods returned null");
		$this->assertFalse(is_null($checkTimeperiods),"Notification Timeperiods returned null");
		$this->assertEquals($notifyTimeperiods->instance_id,$service->instance_id,"Notify timeperiods returned wrong instance id");	
		$this->assertEquals($checkTimeperiods->instance_id,$service->instance_id,"Check timeperiods returned wrong instance_id");	
	
		success("Timeperiod retrieval succeeded");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetStateHistory() {	
		info("Testing timeperiod retrieval for service\n");
		$service = $this->serviceProvider();

		$this->assertFalse(is_null($service->history));
		$this->markTestIncomplete("DB Fixture update needed, statehistory not available for services");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetEscalations() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetDowntimeHistory() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetDependencies() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetServicegroups() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetScheduledDowntimes() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetCustomvars() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetTimedEvents() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetContacts() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetContactgroups() {
		$this->fail("Not implemented");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetServiceChecks() {
		$this->fail("Not implemented");
	}	

	/**
	*	@depends testGetServices
	**/
	public function testGetHost() {
		$this->fail("Not implemented");
	}

}
