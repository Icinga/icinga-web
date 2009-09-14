<?php

// Adding our applicationstate script here
if (AgaviConfig::get('core.default_context') == 'web') {
	
	class IcingaWebInitClass {
		public static function init() {
			$context = AgaviContext::getInstance();
			
			if ($context->getUser()->isAuthenticated()) {
			
				$headerData = $context->getModel('HeaderData', 'AppKit');
				$headerData->addJsFile($context->getRouting()->gen('icinga.ext.applicationState'));
			
			}
		}
	}
	
	IcingaWebInitClass::init();
}

?>