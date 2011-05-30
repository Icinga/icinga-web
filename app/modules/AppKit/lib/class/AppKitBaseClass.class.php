<?php

abstract class AppKitBaseClass {
	
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

?>