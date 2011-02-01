<?php

class Api_ApiServiceRequestModel extends ApiDataRequestBaseModel 
{

	public function limitToHosts(Doctrine_Query $q,array $hosts = array()) {
		if(empty($hosts))
			return $q;
		$byName = array();
		$byId  = array();
		foreach($hosts as $host) {
			if(is_string($host))
				$byName[] = $host; 
			else if(is_int($host))
				$byId[] = $host;
			else if($host instanceof IcingaHosts) 
				$byId[] = $host->host_id;
		}
		$q->innerJoin('s.host h')->whereIn("h.display_name",$byName)->orWhereIn("h.host_id",$byId);
	}
	public function getServices(array $hosts = array()) {
		$desc = $this->createRequestDescriptor();
		$desc->select('*')->from('IcingaServices s');
	
		$this->limitToHosts($desc,$hosts);
		return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
	}

	public function getServicesByName(array $names,array $hosts = array()) {
		$useLike = false;
		foreach($names as $name) {
			if(strpos($name,'%') !== false) {
				$useLike = true;
				break;
			}
		}
		$desc = $this->createRequestDescriptor();		
		$desc->select('*')->from("IcingaServices s");
		if(!$useLike || $ignoreWildCards)
			$desc->whereIn("s.display_name",$names);
		else {
			$first = true;
			foreach($names as $name) {
				if($first)
					$desc->addWhere("s.display_name LIKE ?",array($name));	
				else	
					$desc->orWhere("s.display_name LIKE ?",array($name));	
				$first = false;
			}
		}
		$this->limitToHosts($desc,$hosts);
		return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
	}

	public function getServicesById(array $ids,array $hosts = array()) {
			
	}

	public function getServicesByObjectId(array $ids,array $hosts = array()) {

	}

	public function getServicesByServicegroupNames(array $names,array $hosts = array()) {
	}

	public function getServicesByServicegroupIds(array $ids,array $hosts = array()) {
	
	} 

	public function getServicesByInstanceIds(array $ids,array $hosts = array()) {

	}

	public function getServicesByInstances(array $instanceNames,array $hosts = array()) {
		
	}

	public function getServicesByState(array $statesToShow,array $hosts = array()) {

	}

	public function getServicesByCustomVars(array $keyVals,array $hosts = array()) {

	}
}
