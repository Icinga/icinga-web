<?php

class HostOverviewTest extends PHPUnit_Framework_TestCase {
	public function testHostListing() {
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$hosts = $hostModel->getHosts();
		$this->assertFalse(is_null($hosts),"Host listing failed, returned null");
		$this->assertEquals($hosts->count(),30,"Wrong number of hosts returned in listing");	
		success("Host listing succeeded \n");
	}
	
	/**
	*   @depends HostOverviewTest::testHostListing
	**/
	public function testStatusFilters() {
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$hosts = $hostModel->getHostsByState(array(0,1,2));
		$allHosts = $hostModel->getHosts();
		$this->assertFalse(is_null($hosts),false,"Filter hosts by state returned null");
		$this->assertEquals($allHosts,$hosts,"Filter by state (show all) didn't return all");
		$upHosts = $hostModel->getHostsByState(array(IcingaHosts::$STATE_UP));
		$downHosts = $hostModel->getHostsByState(array(IcingaHosts::$STATE_DOWN));
		$unreachableHosts = $hostModel->getHostsByState(array(IcingaHosts::$STATE_UNREACHABLE));
		
		foreach($upHosts as $__host)
			$this->assertEquals($__host->status->current_state,IcingaHosts::$STATE_UP,"Hosts-Up contained host that isn't up");
		
		foreach($downHosts as $__host)
			$this->assertEquals($__host->status->current_state,IcingaHosts::$STATE_DOWN,"Hosts-Down contained host that isn't down");
		
		foreach($unreachableHosts as $__host)
			$this->assertEquals($__host->status->current_state,IcingaHosts::$STATE_UNREACHABLE,"Hosts-Unreachable contained host that isn't unreachable");
		
		$this->assertTrue($allHosts->count() == ($upHosts->count()+$downHosts->count()+$unreachableHosts->count()), "Sum of all states didn't return all hosts");	
		
		success("Host filter by status succeeded \n");
	}
	
	/**
	*   @depends HostOverviewTest::testHostListing
	**/
	public function testNameFilters() {			
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$hosts = $hostModel->getHostsByName(array("yahoo-www"));
		$this->assertFalse(is_null($hosts),"Filtered hostlist (exact) returned null");
		$this->assertEquals($hosts->count(),1,"Wrong filter result (exact)");
		$this->assertEquals($hosts->getFirst()->display_name,"yahoo-www","Wrong host returned in filter");
		
		$hosts = $hostModel->getHostsByName(array("yahoo-%"));
		$this->assertFalse(is_null($hosts), "Filtered hostlist (wildcards) returned null");
		
		$this->assertEquals($hosts->count(),2,"Wrong filter result (wildcards)");
		success("Host Filter via name succeeded\n");
	}
	
	/**
	*   @depends HostOverviewTest::testHostListing
	**/
	public function testInstanceFilters() {
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		$allHosts = $hostModel->getHosts();
		$hosts = $hostModel->getHostsByInstances(array("default"));
		$hostIds = $hostModel->getHostsByInstanceIds(array(1));
		
		$this->assertFalse(is_null($hostIds->toArray()));
		$this->assertFalse(is_null($hostIds->toArray()));
		$this->assertEquals($hosts,$hostIds,"Id filter doesn't match text count");
		
		$instanceCount =0;
		foreach($allHosts as $_host) {
			$found = false;
			if($_host->instance->instance_name == "default") {
				$instanceCount++;
				foreach($hosts as $_checkHost) {
					if($_checkHost == $_host) {
						$found = true;
						break;
					}
				}
				$this->assertTrue($found,"Host with default instance not returned by instance filter");
			}
		}
		
		$this->assertEquals($instanceCount,$hosts->count(),"Returned Host count doesn't  match instance count");
		success("Host Filter by instance suceeded\n");
	}
	
	/**
	*   @depends HostOverviewTest::testHostListing
	**/
	public function testIdFilters() {
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");	
		
		$hosts = $hostModel->getHostsByObjectId(array(-1));
		$this->assertFalse(is_null($hosts),"Couldn't retrieve host by object id");
		$this->assertTrue($hosts->count() == 0,"Invalid object id returned hosts");
		
		$hosts = $hostModel->getHostsById(array(-1));
		$this->assertFalse(is_null($hosts),"Couldn't retrieve host by id");
		$this->assertTrue($hosts->count() == 0,"Invalid host id returned hosts");
	
		$hosts = $hostModel->getHostsById(array("181"));
		$this->assertEquals($hosts->count(),1,"Valid host id returned wrong nr of hosts");
		$newHost = $hostModel->getHostsByObjectId(array($hosts->getFirst()->host_object_id));
		$this->assertTrue($newHost->getFirst() == $hosts->getFirst(),"Filter by Object id didn't return proper host");
		
		success("Id Filter check succeeded\n");
	}
	

	public function testCustomVarFilters() {	
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");	
		
		$hosts = $hostModel->getHostsByCustomVars(array("RACK"=>"%"));		
		$this->assertFalse(is_null($hosts),"Retrieving hosts with cv RACK failed, returned null");
		$this->assertEquals($hosts->count(),10,"Host count with cv RACK didn'T match expected value");
		
		$hosts = $hostModel->getHostsByCustomVars(array("RACK"=>"1"));
		$this->assertFalse(is_null($hosts),"Retrieving hosts with cv RACK 1 failed, returned null");
		$this->assertEquals($hosts->count(),7,"Host count with cv RACK 1 didn't match expected value");

		success("Filter by custom vars succeeded\n");
	}


	public function testHostgroupFilters() {
		$hostModel = AgaviContext::getInstance()->getModel("ApiHostRequest","Api");
		
		$hosts = $hostModel->getHostsByHostgroupIds(array(64,66));
		$this->assertFalse(is_null($hosts),"Retrieving hosts by hostgroup returned null");
		foreach($hosts as $host) {
			$inHostgroup = false;
			foreach($host->hostgroups as $hostgroup) {
				if($hostgroup->hostgroup_id == '64' || 
						$hostgroup->hostgroup_id == '66') {
					$inHostgroup = true;
					break;
				}
			}
			$this->assertTrue($inHostgroup,"Wrong host returned\n");
		}

		$n_hosts = $hostModel->getHostsByHostgroupNames(array('gmx','Mail Servers'));
		$this->assertFalse(is_null($n_hosts),"Retrieving hosts by hostgroup name returned null");
		$this->assertTrue($hosts == $n_hosts,"Returning hosts by hostgroupname instead of id returned different set");
	
		success("Filter by hostgroups succeeded\n");
	}

}
