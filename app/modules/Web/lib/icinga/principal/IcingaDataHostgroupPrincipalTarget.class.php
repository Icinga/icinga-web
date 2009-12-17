<?php

class IcingaDataHostgroupPrincipalTarget extends IcingaDataPrincipalTarget {
	
	public function __construct() {
		
		parent::__construct();
		
		$this->setFields(array(
			'hostgroup'	=> 'The sql part of a hostgroup name'
		));
		
		$this->setType('IcingaDataTarget');
		
		$this->setDescription('Limit data access to hostgroups');
		
		$this->setApiMappingFields(array(
			'hostgroup'	=> 'HOSTGROUP_NAME'
		));
	}
	
}

?>