<?php
// Sets the appkit version
AgaviConfig::set('de.icinga.appkit.version', '1.0', true, true);
AgaviConfig::set('de.icinga.appkit.release', 'ICINGAAppKit/'. AgaviConfig::get('de.netways.appkit.version'), true, true);

// Test if we're on the web, preparing the menu container structure
if (AgaviConfig::get('core.default_context') == 'web') {
	require_once('AppKitMenuCreator.class.php');
	AppKitEventDispatcher::getInstance()->addListener('appkit.menu', new AppKitMenuCreator());
}

?>