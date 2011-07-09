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
	
		success("Getting host succeeded \n");
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
		success("Getting host status succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetId() {
		$host = $this->hostProvider();
        
		$this->assertEquals($host->host_id,181,"Id of returned host is wrong, should be 181 in the dbFixture");
		success("Getting host id succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetInstance() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->instance),"Instance could not be retrieved, returned null");
		$this->assertEquals($host->instance->instance_name,"default","Instance is not 'default'");
		success("Getting host instance succeeded \n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetAlias() {
		$host = $this->hostProvider();
		$this->assertEquals($host->alias,"company1-datenbank1","Host alias doesn't match");
		success("Getting host alias succeeded\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetDisplayname() {
		$host = $this->hostProvider();
		$this->assertEquals($host->display_name,"c1-db1","Host name doesn't match");
		success("Getting host display_name succeeded\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetAddress() {
		$host = $this->hostProvider();
		$this->assertEquals($host->address,"10.10.100.31","Host address doesn't match");
		success("Getting host address succeeded\n");
	}
	

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetEventhandlerCommand() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->eventHandlerCommand),"Host eventhandlerCommand could not be retrieved, returned null");
		success("********** DB Fixture upgrade needed - no eventhandler commands available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetNotificationTimeperiod() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->notificationTimeperiod),"Notificationtimeperiod could not be retrieved, returned null");
		$this->assertEquals($host->notificationTimeperiod->alias,'"Normal" Working Hours',"Timeperiod alias doesn't match");	
		success("Getting notification timeperiod succeeded\n");		
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetStateHistory() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->stateHistory),"Statehistory could not be retrieved, returned null");
		$this->assertEquals($host->stateHistory->count(),4,"Statehistory result count doesn't match expected value");
		success("Getting host history succeeded\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetEscalations() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->escalations),"Escalations could not be retrieved, returned null");
		info("********** DB Fixture upgrade needed - no hostescalations available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetDowntimeHistory() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->downtimeHistory),"Downtime history could not be retrieved, returned null");

		info("********** DB Fixture upgrade needed - no downtimehistory available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetDependencies() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->dependencies),"Dependencies could not be retrieved, returned null");
		
		info("********** DB Fixture upgrade needed - no host dependencies available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}

	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetHostgroups() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->hostgroups),"Hostgroups could not be retrieved, returned null");
	    
        $this->assertTrue($host->hostgroups->count() > 0, "Hostgroupcount doesn't match expected value");
		foreach($host->hostgroups as $hostgroup) {
			$this->assertEquals($hostgroup->instance_id,$host->instance_id,"Hostgroup from wrong instance returned");
		
		}

		$found = false;
		foreach($host->hostgroups->getFirst()->members as $hgMember) {
			if($hgMember == $host) {
				$found = true;
				break;
			}
		}
		$this->assertTrue($found,"Host not found in hostgroup member list");
		success("Retrieving hostgroups succeeded\n");
		
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetScheduledDowntimes() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->scheduledDowntimes),"Scheduled downtimes for host could not be retrieved, returned null");

		info("********** DB Fixture upgrade needed - no scheduled downtimes available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetCustomvars() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->customvariables),"Customvars for host could not be retrieved, returned null");	
		
		foreach($host->customvariables as $var) {
			$this->assertEquals($var->instance_id,$host->instance_id,"Customvar from wrong instance returned");
		}
		
		$found = false;
		foreach($host->customvariables->hosts as $cvhost) {
			if($cvhost = $host) {
				$found = true;
				break;
			}
		}
		$this->assertTrue($found,"Host not found in customvariable definition");
		success("Retrieving customvariables for host succeeded\n"); 	
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetTimedEvents() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->timedevents),"Timed events for host could not be retrieved, returned null");

		info("********** DB Fixture upgrade needed - no timed events for host available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetContacts() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->contacts),"Contacts for host couldn't be retrieved, returned null");

		info("********** DB Fixture upgrade needed - no host contacts available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object\n");
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetContactgroups() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->contactgroups), "Contactgroups for host couldn't be retrieved, returned null");
		$this->assertTrue($host->contactgroups->count() > 0,"Expected at least 1 contactgroup to be returned");
		
        $this->assertFalse(is_null($host->contactgroups->getFirst()->hosts),1,"Couldn't retrieve hosts for contactgroups" );
		$found = false;
		foreach($host->contactgroups as $group) {
			$this->assertEquals($group->instance_id,$host->instance_id,"Contactgroup from wrong instance returned");
		}
		foreach($host->contactgroups->getFirst()->hosts as $_host) {
			if($host == $_host) {
				$found = true;
				break;
			}
		}
		$this->assertTrue($found, "Couldn't find host in host list of contactgroup");
		success("Retrieving contactgroups succeeded\n");	
	}
	
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetHostChecks() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->checks));
		info("********** DB Fixture upgrade needed - no hostchecks available \n");
		$this->markTestIncomplete("DB fixture doesn't allow proper testing of this object");
	}	
	
		
	/**
	*   @depends HostDetailTest::testGetHost
	**/
	public function testGetServices() {
		$host = $this->hostProvider();
		$this->assertFalse(is_null($host->services));
		foreach($host->services as $service) {
			$this->assertEquals($service->instance_id,$host->instance_id,"Service from wrong instance returned");
			$this->assertEquals($service->host->host_id, $host->host_id,"Wrong service (from different host) returned");
		}
		
		//success("Retrieving Services from host succeeded\n");
	}

}
