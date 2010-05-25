<?php

class IcingaDataServiceCustomVariablePrincipalTarget extends IcingaDataPrincipalTarget {
	
	public function __construct() {
		
		parent::__construct();
			$this->setDefaultTarget('service');
			
		$this->setFields(array(
			'cv_name'	=> 'Name of the custom variable',
			'cv_value'	=> 'Value contained ba the variable'
		));
		
		$this->setApiMappingFields(array(
			'cv_name'	=> 'SERVICE_CUSTOMVARIABLE_NAME',
			'cv_value'	=> 'SERVICE_CUSTOMVARIABLE_VALUE'
		));
		
		
		$this->setType('IcingaDataTarget');
		
		$this->setDescription('Limit data access to customvariables');
	}
	
}

?>