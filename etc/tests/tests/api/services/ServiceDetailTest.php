<?php

class ServiceDetailTest extends PHPUnit_Framework_TestCase {
	public function serviceProvider() {
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
		$host = $model->getHostsByName(array("pop3-gmx"));
		return $host->getFirst();
	
	}

	public function testGetStatus() {
		
	}
	public function testGetId() {
		$this->fail("Not implemented");
	}
	public function testGetInstance() {
		$this->fail("Not implemented");
	}
	public function testGetAlias() {
		$this->fail("Not implemented");
	}
	
	public function testGetDisplayname() {
		$this->fail("Not implemented");
	}
	public function testGetAddress() {
		$this->fail("Not implemented");
	}
	/**
	*  We could go on and test every attribute, but if those 6 match, 
	*  there shouldn't occure anymore problems
	**/
	public function testGetCheckcommand() {
		$this->fail("Not implemented");
	}
	public function testGetEventhandler() {
		$this->fail("Not implemented");
	}
	public function testGetTimeperiod() {
		$this->fail("Not implemented");
	}
	public function testGetStateHistory() {
		$this->fail("Not implemented");
	}
	public function testGetEscalations() {
		$this->fail("Not implemented");
	}
	public function testGetDowntimeHistory() {
		$this->fail("Not implemented");
	}
	public function testGetDependencies() {
		$this->fail("Not implemented");
	}
	public function testGetServicegroups() {
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
	public function testGetServiceChecks() {
		$this->fail("Not implemented");
	}	
	public function testGetHost() {
		$this->fail("Not implemented");
	}

}
