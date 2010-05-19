<?php

class IcingaDataServicegroupPrincipalTarget extends IcingaDataPrincipalTarget {
	

	public function __construct() {
		
		parent::__construct();
		
		$this->setFields(array(
			'servicegroup'	=> 'The sql part of a servicegroup name'
		));
		
		$this->setApiMappingFields(array(
			'servicegroup' => 'SERVICEGROUP_NAME'
		));
		
		$this->setType('IcingaDataTarget');
		
		$this->setDescription('Limit data access to servicegroups');
		
	}
	
}

?>