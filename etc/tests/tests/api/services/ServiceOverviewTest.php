<?php

class ServiceOverviewTest extends PHPUnit_Framework_TestCase {
	public function testServiceListing() {
		info("Testing general service listing\n");
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
		$services = $model->getServices();	
		
		$this->assertFalse(is_null($services),"Service listing returned null");
		$this->assertFalse($services->count() == 0,"No services returned");

		$firstServiceHost = $services->getFirst()->host;
		$servicesForHost = $model->getServices(array($firstServiceHost));	
		$this->assertFalse(is_null($servicesForHost));

		foreach($servicesForHost as $service) {
			$this->assertTrue($service->host == $firstServiceHost,"Host filter returned wrong services");
		}
	}
	public function testStatusFilters() {	
		info("Testing service listing with status filter\n");
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
		
		$services = $model->getServicesByState(array(IcingaServices::$STATE_WARNING));
		foreach($services as $service) {
			$this->assertEquals($service->status->current_state,IcingaServices::$STATE_WARNING);
		}	
		$services = $model->getServicesByState(array(IcingaServices::$STATE_OK,IcingaServices::$STATE_UNKNOWN));
		foreach($services as $service) {
			$isOk = $service->status->current_state == IcingaServices::$STATE_OK;
			$isOk == $isOk || $service->status->current_state == IcingaServices::$STATE_UNKNOWN;
			$this->assertTrue($isOk);
		}

		$services = $model->getServicesByState(array(IcingaServices::$STATE_OK,IcingaServices::$STATE_UNKNOWN),array($services->getFirst()->host));
		foreach($services as $service) {
			$isOk = $service->status->current_state == IcingaServices::$STATE_OK;
			$isOk == $isOk || $service->status->current_state == IcingaServices::$STATE_UNKNOWN;
			$this->assertTrue($isOk);
		}
			
	}
	public function testNameFilters() {
		info("Testing service listing with name filter\n");
		$model = AgaviContext::getInstance()->getModel("ApiServiceRequest","Api");
	
		$services = $model->getServicesByName(array("MySQL"));	
		$this->assertFalse(is_null($services));
		$this->assertFalse($services->count() == 0);

		foreach($services as $service) {
			$this->assertEquals($service->display_name,"MySQL");
		}
		$cpServices = $services;
	
		$services = $model->getServicesByName(array("My%"),array("c1-db1"));	
		$this->assertFalse(is_null($services));	
		$this->assertFalse($services->count() == 0);
		
		foreach($services as $service) {
			$this->assertEquals($service->display_name,"MySQL");
		}
		$this->assertFalse($services->count() == $cpServices->count());
	}

	public function testCustomVarFilters() {
		$this->markTestIncomplete("Custom Var filter test not implemented");
	}
	
	public function testServicegroupFilters() {
		$this->markTestIncomplete("Servicegroup filter test not implemented");
	}
}
