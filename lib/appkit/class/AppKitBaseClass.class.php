<?php

abstract class AppKitBaseClass extends AppKit {
	
	public function toString() {
		return get_class($this);
	}
	
	public function __toString() {
		return $this->toString();
	}
	
	/**
	 * Returns the agavi routing
	 * @return AgaviRouting
	 */
	public function getAgaviRouter() {
		return $this->getAgaviContext()->getRouting();
	}
	
	/**
	 * Returns the agavi context
	 * @return AgaviContext
	 */
	public function getAgaviContext() {
		return AgaviContext::getInstance();
	}
	
	/**
	 * Returns the agavi translation manager
	 * @return AgaviTranslationManager
	 */
	public function getAgaviTranslationManager() {
		return $this->getAgaviContext()->getTranslationManager();
	}
	
	/**
	 * Returns the agavi request
	 * @return AgaviRequest
	 */
	public function getAgaviRequest() {
		return $this->getAgaviContext()->getRequest();
	}
	
}

/**
 * Custom exception class to use printf formats
 * @author mhein
 *
 */
class AppKitException extends Exception {
	
	/**
	 * Customized constructor
	 * @param $mixed
	 */
	public function __construct($mixed) {
		
		$args = func_get_args();
		
		if (AppKitStringUtil::detectFormatSyntax($mixed)) {
			$format = array_shift($args);
			
			parent::__construct( vsprintf($format, $args) );
		}
		else {
			call_user_method_array('parent::__construct', $this, $args);
		}
	}
	
}

?>