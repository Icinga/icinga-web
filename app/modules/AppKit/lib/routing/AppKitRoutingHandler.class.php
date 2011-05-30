<?php
class ExtDirectMissingActionException extends AgaviConfigurationException {}; 
class AppKitRoutingHandler extends AgaviRoutingConfigHandler {
	
	const ENTRY_XPATH = '//ae:configurations/ae:configuration[@context=\'web\']/routing:routes/routing:route[@name=\'modules\']';
	const XML_NAMESPACE = 'http://icinga.org/appkit/config/parts/routing/1.0'; 	
	
	private $directProviders = array();


	public function execute(AgaviXmlConfigDomDocument $document) {
		
		// set up our default namespace
		$document->setDefaultNamespace(self::XML_NAMESPACE, 'routing');
		
		AppKitXmlUtil::includeXmlFilesToTarget(
			$document, 
			self::ENTRY_XPATH, 
			'xmlns(ae=http://agavi.org/agavi/config/global/envelope/1.0) xmlns(r=http://icinga.org/appkit/config/parts/routing/1.0) xpointer(//ae:configurations/ae:configuration/r:routes/node())',
			AppKitModuleUtil::getInstance()->getSubConfig('agavi.include_xml.routing')
			);
	
		$document->xinclude();
	
		return $this->parent_execute($document);
	}


	/**
	* parent::execute would overwrite the routing default namespace with the one agavi uses, so we
	* cannot simply call the parent one (self::XML_NAMESPACE wouldn't refer to http://icinga.org/...)
	* This is just a copy of @See AgaviRoutingConfigHandler::execute and additionally calls the Ext.direct
	* Provider
	* 
    * @param 	AgaviXmlConfigDomDocument 	The DOMDocument to parse
    *
    * @author 	Jannis Moßhammer 	<jannis.mosshammer@netways.de>
    *
	**/
	private function parent_execute(AgaviXmlConfigDomDocument $document) {	
		$routing = AgaviContext::getInstance($this->context)->getRouting();
		$this->unnamedRoutes = array();
		$routing->importRoutes(array());
		$data = array();
		
		foreach($document->getConfigurationElements() as $cfg) {
			if($cfg->has('routes')) {		
				$this->parseRoutes($routing, $cfg->get('routes'));
			}
		}
	
		return serialize($routing->exportRoutes());


	}
	
	/**
	*	Delegates route-parsing to the inherited AgaviXmlConfigHandler but extracts 	
	*	information about ext.direct routes
	*
	* @param      AgaviRouting The routing instance to create the routes in.
	* @param      mixed        The "roles" node (element or node list)
	* @param      string       The name of the parent route (if any).
	*
	* @author     Jannis Moßhammer <jannis.mosshammer@netways.de>
	*/
	protected function parseRoutes($routing,$routes,$parent = null) 
	{
		foreach($routes as $route) {
				
			if($route->hasAttribute("extdirect_provider")) {
				$this->fetchExtDirectProviderInformation($route);
			}
		}
		parent::parseRoutes($routing,$routes,$parent = null);
	}	
	
	/**
	* Extracts module and action information from the current route
	* @param	AgaviDomElement	The route elment to search for 
	*	
	* @author   Jannis Moßhammer <jannis.mosshammer@netways.de>
	*/
	protected function fetchExtDirectProviderInformation(DomElement $route) {	
		$module = AppKitXmlUtil::getInheritedAttribute($route, "module");
		$action = AppKitXmlUtil::getInheritedAttribute($route, "action");
		if(!$action) {
			$r = print_r($route->getParameters(),true);
			throw new ExtDirectMissingActionException("Missing action in route exported	for Ext.Direct. Route settings: ".$r);
		}	
		if($module != null && $action != null) {
			array_push($this->directProviders,array(
				"module" => $module,
				"action" => $action
			));
		}
	}


}

?>
