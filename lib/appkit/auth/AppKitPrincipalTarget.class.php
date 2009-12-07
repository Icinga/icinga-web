<?php

abstract class AppKitPrincipalTarget extends AppKitBaseClass {
	
	protected $fields		= array ();
	protected $type			= null;
	protected $description	= null;
	
	public function __construct() {
		
	}
	
	protected function setFields(array $a) {
		$this->fields = $a;
	}
	
	protected function setType($t) {
		$this->type = $t;
	}
	
	protected function setDescription($d) {
		$this->description = $d;
	}
	
}

class AppKitPrincipalTargetException extends AppKitException {}

?>