<?php

class IcingaDataCustomVariablePrincipalTarget extends IcingaDataPrincipalTarget {
	
	public function __construct() {
		
		parent::__construct();
		
		$this->setFields(array(
			'cv_name'	=> 'Name of the custom variable',
			'cv_value'	=> 'Value contained ba the variable'
		));
		
		$this->setType('IcingaDataTarget');
		
		$this->setDescription('Limit data access to customvariables');
	}
	
}

?>