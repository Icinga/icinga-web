<?php

class AppKitModuleRoutingHandler extends AgaviRoutingConfigHandler {
	
	const ENTRY_XPATH = '//ae:configurations/ae:configuration[@context=\'web\']/routing:routes/routing:route[@name=\'modules\']';
	
	public function execute(AgaviXmlConfigDomDocument $document) {
		
		// set up our default namespace
		$document->setDefaultNamespace(self::XML_NAMESPACE, 'routing');
		
		AppKitXmlUtil::includeXmlFilesToTarget(
			$document, 
			self::ENTRY_XPATH, 
			'xmlns(ae=http://agavi.org/agavi/config/global/envelope/1.0) xmlns(r=http://agavi.org/agavi/config/parts/routing/1.0) xpointer(//ae:configurations/ae:configuration/r:routes/node())',
			AppKitModuleUtil::getInstance()->getSubConfig('agavi.include_xml.routing')
			);
		
		$document->xinclude();
			
		return parent::execute($document);
	}
		
}

?>