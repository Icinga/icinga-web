<?php

class ServicegroupOverviewTest extends PHPUnit_Framework_TestCase {
	public function testServicegroupListing() {
		$model = AgaviContext::getInstance()->getModel("ApiServicegroupRequest","Api"); 
		$hg = $model->getServicegroups();
		$this->assertFalse(is_null($hg));
		$this->assertTrue($hg->count() > 0);	
	}
	
	public function testStatusFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiServicegroupRequest","Api"); 
		
		$hg = $model->getServicegroupsByStates(array(1,2));		
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
			$this->assertTrue($matchingState,"Servicegroup with invalid states returned");
		}	
	}
	
	public function testServiceFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiServicegroupRequest","Api");	
		$hg = $model->getServicegroupsByServiceNames(array('POP3'));
		$this->assertFalse($hg->count() < 1,"No servicegroup returned for c1-db1");
		foreach($hg as $servicegroup) {
			$serviceFound = false;
			foreach($servicegroup->members as $service) {
				if($service->display_name == 'POP3') {
					$serviceFound = true;
					break;
				}
			}
			$this->assertTrue($serviceFound);
		}
		$hg = $model->getServicegroupsByServiceIds(array(271,283));	
		$this->assertFalse($hg->count() < 1,"No servicegroup returned for ids");
			
		foreach($hg as $servicegroup) {
			$serviceFound = false;
			foreach($servicegroup->members as $service) {
				if($service->service_id == 271 || $service->service_id == 283) {
					$serviceFound = true;
					break;
				}
			}
			$this->assertTrue($serviceFound);
		}

	}	


	public function testNameFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiServicegroupRequest","Api");	
		$hg = $model->getServicegroupsByNames(array('Database services'));
		foreach($hg as $servicegroup) {
			$this->assertEquals($servicegroup->alias,"Database services");
		}
		$allHosts = $model->getServicegroups();
		$hg = $model->getServicegroupsByNames(array('%services'));
		$this->assertEquals($allHosts->count(),$hg->count());
	}
	
	public function testInstanceFilters() {
		$model = AgaviContext::getInstance()->getModel("ApiServicegroupRequest","Api");	
		$hg = $model->getServicegroupsByInstances(array(1));
		$this->assertTrue($hg->count() > 0);
	}
	
}
