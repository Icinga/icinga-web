<?php

class Api_ApiHostgroupRequestModel extends ApiDataRequestBaseModel 
{
	public function getHostgroupList() {}
	public function getHostgroupsForHosts(array $hosts) {}
	public function getHostgroupsForServices(array $services) {}
	public function getHostgroupByName($name) {}
	public function getHosgroupByInstance($instance_id) {}
}
