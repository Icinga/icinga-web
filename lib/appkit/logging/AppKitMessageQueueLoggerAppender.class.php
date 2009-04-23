<?php

class AppKitMessageQueueLoggerAppender extends AgaviLoggerAppender {
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		$this->setParameter('factory_name', 'MessageQueue');
		
		parent::initialize($context, $parameters);
	}
	
	public function write($message) {
		
		if(($layout = $this->getLayout()) === null) {
			throw new AgaviLoggingException('No Layout set');
		}
		
		$queue = AppKitFactories::getInstance()
		->getFactory($this->getParameter('factory_name', 'MessageQueue'));
		
		$queue->enqueue(AppKitMessageQueueItem::Log( $this->getLayout()->format($message) ));
		
	}
	
	public function shutdown() {
		// Do nothing here ... ;-)
	}
}

?>