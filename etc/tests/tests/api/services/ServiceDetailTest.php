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
		$this->assertFalse(is_null($status),"Service status returned null\n");
		$this->assertEquals($status->instance_id, $service->instance_id,"Service instance doesn't match status instance\n");
		$this->assertTrue($status->service == $service,"Status belonging to the service is not the original status\n");
		success("Service status retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetId() {
		info("Testing service id retrieval\n");
		$service = $this->serviceProvider();
		$this->assertTrue(is_numeric($service->service_id),"Service id didn't return integer\n");
		$this->assertTrue(is_numeric($service->service_object_id),"Service object_id didn't return integer\n");
		success("Service id retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetInstance() {
		info("Testing instance id retrieval\n");
		$service = $this->serviceProvider();
		$this->assertTrue(is_numeric($service->instance_id),"Service id didn't return integer\n");
	
		success("Instance id retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetName() {	
		info("Testing name retrieval\n");
		$service = $this->serviceProvider();
		$this->assertEquals($service->display_name,'POP3',"Service name didn't match\n");
	
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
		$this->assertFalse(is_null($service->checks),"Service Checks could not be retrieved, returned null\n");
		
		info("****DB Fixture update needed, no service checks available\n");
	
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetEventhandler() {
		info("Testing eventhandler retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->eventHandlerCommand),"Eventhandler could not be retrieved, returned null\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetTimeperiods() {
		info("Testing timeperiod retrieval for service\n");
		$service = $this->serviceProvider();
		$notifyTimeperiods = $service->notificationTimeperiod;
		$checkTimeperiods = $service->checkTimeperiod;
		$this->assertFalse(is_null($notifyTimeperiods),"Notification Timeperiods returned null\n");
		$this->assertFalse(is_null($checkTimeperiods),"Notification Timeperiods returned null\n");
		$this->assertEquals($notifyTimeperiods->instance_id,$service->instance_id,"Notify timeperiods returned wrong instance id\n");	
		$this->assertEquals($checkTimeperiods->instance_id,$service->instance_id,"Check timeperiods returned wrong instance_id\n");	
	
		success("Timeperiod retrieval succeeded\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetStateHistory() {	
		info("Testing timeperiod retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->history));

		$this->markTestIncomplete("DB Fixture update needed, statehistory not available for services\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetEscalations() {
		info("Testing escalation retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->escalations->toArray()));
	
		$this->markTestIncomplete("DB Fixture update needed, escalations not available for services\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetDowntimeHistory() {
		info("Testing downtime history for services\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->downtimeHistory));

		$this->markTestIncomplete("DB Fixture update needed, downtime history not available for services\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetDependencies() {
		info("Testing service dependencies\n");	
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->dependencies));
		
		$this->markTestIncomplete("DB Fixture update needed, dependencies available for services\n");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetServicegroups() {
		info("Testing servicegroup retrieval for service\n");
		$service = $this->serviceProvider();	
		$groups = $service->servicegroups;
	
		$this->assertFalse(is_null($groups),"Couldn't retrieve servicegroups");
		$this->assertEquals($groups->count(),2,"Group count didn't match excpected value");
		foreach($groups as $group) {
			$found = false;
			foreach($group->services as $service_toCheck) {
				if($service_toCheck == $service) {
					$found = true;
					break;
				}
			}

			$this->assertTrue($found,"Returned servicegroup didn't contain service");
		}
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetScheduledDowntimes() {	
		info("Testing scheduled downtimes for service\n");
		$service = $this->serviceProvider();
		$dTimes = $service->scheduledDowntimes;	
		$this->assertFalse(is_null($dTimes));

		$this->markTestIncomplete("DB Fixture update needed, couldn't test scheduled downtimes");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetCustomvars() {
		info("Testing custom variables for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->customvariables));		

		$this->markTestIncomplete("DB Fixture update needed, couldn't test service cv's");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetTimedEvents() {
		info("Testing timed event retrieval for service\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->timedevents));
		
		$this->markTestIncomplete("DB Fixture update needed, couldn't test timed events");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetContacts() {
		info("Testing contact retrieval for services\n");
		$service = $this->serviceProvider();
		$this->assertFalse(is_null($service->contacts));
	
		$this->markTestIncomplete("DB Fixture update needed, couldn't service contacts");
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetContactgroups() {
		info("Testing contactgroup retrieval for services\n");
		$service = $this->serviceProvider();
		$groups = $service->contactgroups;	
		$this->assertFalse(is_null($groups));
			
		foreach($groups as $group) {
			$found = false;
			foreach($group->services as $service_toCheck) {	
				if($service_toCheck == $service) {
					$found = true;
					break;
				}
			}
			$this->assertTrue($found,"Returned contactgroup didn't contain service");
		}
	}

	/**
	*	@depends testGetServices
	**/
	public function testGetHost() {
		info("Testing host retrieval for service\n");
		$service = $this->serviceProvider();
		$host = $service->host;
	
		$this->assertFalse(is_null($host),"Couldn't retrieve host for service, returned null");
	
		$this->assertEquals($host->instance_id, $service->instance_id,"Wrong instance id");
	}

}
