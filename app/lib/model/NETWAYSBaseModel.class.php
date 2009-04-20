<?php

/**
 * The base model from which all project models inherit.
 */
class NETWAYSBaseModel extends AgaviModel implements AppKitContextInterface
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