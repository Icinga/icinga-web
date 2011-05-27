<?php

class HostgroupOverviewTest extends PHPUnit_Framework_TestCase {
	public function testHostgroupListing() {
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api"); 
		$hg = $model->getHostgroups();
		$this->assertFalse(is_null($hg));
		$this->assertTrue($hg->count() > 0);	
	}
	
	public function testStatusFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api"); 
		
		$hg = $model->getHostgroupsByStates(array(1,2));		
		foreach($hg as $group) {
			$matchingState = false;
			foreach($group->members as $member) {	
				$state = $member->status->current_state; 
				if($state == 1) {
					$matchingState = true;
				} else if($state == 2) {
					$matchingState = true;
				}
			}
			$this->assertTrue($matchingState,"Hostgroup with invalid states returned");
		}	
	}
	
	public function testHostFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api");	
		$hg = $model->getHostgroupsByHostNames(array('c1-db1'));
		$this->assertFalse($hg->count() < 1,"No hostgroup returned for c1-db1");
		foreach($hg as $hostgroup) {
			$hostFound = false;
			foreach($hostgroup->members as $host) {
				if($host->display_name == 'c1-db1') {
					$hostFound = true;
					break;
				}
			}
			$this->assertTrue($hostFound);
		}
		$hg = $model->getHostgroupsByHostIds(array(181,200));
		
		foreach($hg as $hostgroup) {
			$hostFound = false;
			foreach($hostgroup->members as $host) {
				if($host->host_id == 181 || $host->host_id == 200) {
					$hostFound = true;
					break;
				}
			}
			$this->assertTrue($hostFound);
		}

		$this->assertFalse($hg->count() < 1,"No hostgroup returned for c1-db1");
		
	}	


	public function testServiceFilters() {	
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api");	
		$hg = $model->getHostgroupsByServices(array('POP3'));
		foreach($hg as $hostgroup) {
			$containsService = false;
			foreach($hostgroup->members as $member) {
				foreach($member->services as $service) {
					if($service->display_name == 'POP3') {
						$containsService = true;
						break;
					}
				}
				if($containsService)
					break;
			}
			$this->assertTrue($containsService,"Hostgroup doesn't contain any of the services it should");
		}	
	}
	
	public function testNameFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api");	
		$hg = $model->getHostgroupsByNames(array('Company 1'));
		foreach($hg as $hostgroup) {
			$this->assertEquals($hostgroup->alias,"Company 1");
		}

		$hg = $model->getHostgroupsByNames(array('Company%'));
		foreach($hg as $hostgroup) {
			$isEqual = $hostgroup->alias == "Company 1";
			$isEqual = $isEqual ? true : $hostgroup->alias == "Company 2";
			$this->assertTrue($isEqual);
		}
	}
	
	public function testInstanceFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiHostgroupRequest","Api");	
		$hg = $model->getHostgroupsByInstances(array(1));
		$this->assertTrue($hg->count() > 0);
	}
	
}
