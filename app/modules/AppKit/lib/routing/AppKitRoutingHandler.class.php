<?php

class AppKitRoutingHandler extends AgaviRoutingConfigHandler {
	
	const ENTRY_XPATH = '//ae:configurations/ae:configuration[@context=\'web\']/routing:routes/routing:route[@name=\'modules\']';
	const XML_NAMESPACE = 'http://icinga.org/appkit/config/parts/routing/1.0'; 	
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
	* This is just a copy of @See AgaviRoutingConfigHandler::execute
	*
	**/
	private function parent_execute($document) {	
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
	
}

?>
