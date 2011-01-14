<?php

class Api_ApiHostRequestModel extends ApiDataRequestBaseModel  
{
	public static $STATE_UP = 0;
	public static $STATE_DOWN = 1;
	public static $STATE_UNREACHABLE = 2;
	
	public function getHostsByName(array $names) {
		$desc = $this->createRequestDescriptor();		
		$desc->select('*')->from("IcingaHosts h")->whereIn("h.display_name",$names);
		return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
	}

	public function getHostsById(array $ids) {}
	public function getHostsByHostgroupNames(array $hostgroups) {}
	public function getHostsByHostgroupIds(array $hostgroups) {}
	public function getHostsByInstances(array $instances) {}
	public function getHostsByState(array $statesToShow) {}
	public function getHostsByAddress(array $addesses) {}
}
