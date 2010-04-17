<?php

class AppKit_Ext_HeaderNavigationSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('menu', $this->executeJavascript($rd));
	}
	
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		$model = $this->getContext()->getModel('NavigationContainer', 'AppKit');
		
		if ($model->getContainer()->Count() === 0) {
			AppKitEventDispatcher::getInstance()->triggerSimpleEvent('appkit.menu', 'we need the menu here ...');
		}
		
		return chr(10)
			. 'AppKit.on(\'north-ready\', function(north, layout) {'. chr(10)
			. 'layout.setMenu(((' . $model->getJsonData(). ')));'. chr(10)
			. '});'. chr(10);
	}
}

?>