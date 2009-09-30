<?php
/**
 * An event to add the state data to the javascript header
 * @author mhein
 */
class AppKitExtStateProviderData extends AppKitEventHandler implements AppKitEventHandlerInterface {
	
	private $parameters = array();
	
	public function __construct(array &$parameters = array()) {
		
		if (!array_key_exists('route', $parameters)) {
			throw new AppKitEventHandlerException('Parameter "route" is missing');
		}
		
		$this->parameters =& $parameters;
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/event/AppKitEventHandlerInterface#handleEvent($event)
	 */
	public function handleEvent(AppKitEvent &$event) {
		
		// We need an AgaviContext here
		if (!$event->getObject() instanceof AgaviContext) {
			throw new AppKitEventHandlerException('A agavi context object is missing');
		}
		
		// Extract the context
		$context =& $event->getObject();
		
		// The loader url
		$source = $context->getRouting()->gen($this->parameters['route']);
		
		$headerData = $context->getModel('HeaderData', 'AppKit');
		$headerData->addJsFile( $source );
		
		return true;
	}
	
}

?>
