<?php

class IcingaDataHostCustomVariablePrincipalTarget extends IcingaDataPrincipalTarget {
	
	public function __construct() {
		
		parent::__construct();
		
		$this->setFields(array(
			'cv_name'	=> 'Name of the custom variable',
			'cv_value'	=> 'Value contained ba the variable'
		));
		
		$this->setApiMappingFields(array(
			'cv_name'	=> 'HOST_CUSTOMVARIABLE_NAME',
			'cv_value'	=> 'HOST_CUSTOMVARIABLE_VALUE'
		));
		
		$this->setType('IcingaDataTarget');
		
		$this->setDescription('Limit data access to customvariables');
	}
	
}

?>