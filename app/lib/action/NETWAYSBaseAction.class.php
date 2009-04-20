<?php

/**
 * The base action from which all project actions inherit.
 */
class NETWAYSBaseAction extends AgaviAction implements AppKitContextInterface
{


	public function getFactory($name) {
		return AppKitFactories::getInstance()->getFactory($name);
	}
	

	public function getMessageQueue() {
		return $this->getFactory(self::FACTORY_MESSAGEQUEUE);
	}
	

	public function getAuthProvider() {
		return $this->getFactory(self::FACTORY_AUTHPROVIDER);
	}
	
}

?>