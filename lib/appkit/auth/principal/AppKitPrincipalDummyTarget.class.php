<?php 

class AppKitPrincipalDummyTarget extends AppKitPrincipalTarget {
	
	public function __construct() {
		
		parent::__construct();
		
		$this->setFields(array (
			'test1'	=> 'A test field',
			'test2'	=> 'Another test value field'
		));
		
		$this->setType('TestPrincipalTarget');
		
		$this->setDescription('A dummy target with values');
		
	}
	
}

?>