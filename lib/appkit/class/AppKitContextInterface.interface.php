<?php

interface AppKitContextInterface {
	
	const FACTORY_MESSAGEQUEUE	= 'MessageQueue';
	const FACTORY_AUTHPROVIDER	= 'AuthProvider';
	
	/**
	 * 
	 * @param string $name
	 * @return AppKitFactoryInterface
	 * @author Marius Hein
	 */
	public function getFactory($name);
	
	/**
	 * 
	 * @return AppKitMessageQueue
	 * @author Marius Hein
	 */
	public function getMessageQueue();
	
	/**
	 * 
	 * @return AppKitAuthProvider
	 * @author Marius Hein
	 */
	public function getAuthProvider();
	
	
}

?>