<?php

class AppKit_Ext_HeaderSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('json_menu_data', $this->jsonMenuData());
	}

	public function executeJavascript(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('json_menu_data', $this->jsonMenuData());
	}
	
	private function jsonMenuData() {
		$model = $this->getContext()->getModel('NavigationContainer', 'AppKit');
		
		// Notify the watcher to provide their menu data
		if ($model->getContainer()->Count() === 0) {
			AppKitEventDispatcher::getInstance()->triggerSimpleEvent('appkit.menu', 'we need the menu here ...');
		}
		
		return $model->getJsonData();
	}

}

?>
