<?php

class Api_ApiServiceRequestModel extends ApiDataRequestBaseModel 
{

	public function getServices() {
		$desc = $this->createRequestDescriptor();
		$desc->select('*')->from('IcingaServices s');
	
		return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
	}

	public function getServicesById(array $ids) {

	}

	public function getServicesByObjectId(array $ids) {

	}

	public function getServicesByServicegroupNames(array $names) {
	}

	public function getServicesByServicegroupIds(array $ids) {
	
	} 

	public function getServicesByInstanceIds(array $ids) {

	}

	public function getServicesByInstances(array $instanceNames) {
		
	}

	public function getServicesByState(array $statesToShow) {

	}

	public function getServicesByCustomVars(array $keyVals) {

	}
}
