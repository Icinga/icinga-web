<?php

class AppKitMessageQueue extends AppKitQueue implements AppKitFactoryInterface {
	
	private $parameters = array ();
	
	public function initializeFactory(array $parameters=array()) {
		$this->parameters = $parameters;
	}
	
	public function shutdownFactory() {
		
	}
	
}

?>