<?php

class AppKitEventHandler extends AppKitBaseClass {
	
	/**
	 * The constructor
	 * @author Marius Hein
	 */
	public function __construct() {
		
	}
	
	/**
	 * A method that can initialize the handler the first call if added to 
	 * the listener stack
	 * @author Marius Hein
	 */
	public function initializeHandler() {
		
	}
	
	/**
	 * A method placeholder to check the eventtype
	 * @param AppKitEvent $e
	 * @return boolean
	 * @throws AppKitEventHandlerException
	 * @author Marius Hein
	 */
	public function checkEventType(AppKitEvent &$e) {
		return true;
	}
	
	/**
	 * A placeholder to check the object type
	 * @param AppKitEvent $e
	 * @return boolean
	 * @throws AppKitEventHandlerException
	 * @author Marius Hein
	 */
	public function checkObjectType(AppKitEvent &$e) {
		return true;
	}
	
}

class AppKitEventHandlerException extends AppKitException {}

?>