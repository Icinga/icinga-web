<?php

class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction {

	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		$files = array ();
		$actions = array ();
		
		$resources = $this->getContext()->getModel('Resources', 'AppKit');
		$loader = $this->getContext()->getModel('SquishFileContainer', 'AppKit', array('type' => 'javascript'));

		try {
			
			$loader->addFiles(
				$resources->getJavascriptFiles()
			);
			
			$loader->squishContents();

			$actions = $this->getContext()->getRequest()->getAttribute('app.javascript_actions', AppKitModuleUtil::DEFAULT_NAMESPACE, array());

			$this->setAttribute('javascript_actions', $actions);

			$this->setAttribute('javascript_content', $loader->getContent(). chr(10));
			
		
		}
		catch(AppKitModelException $e) {
			$this->setAttribute('errors', $e->getMessage());
		}
		
		return $this->getDefaultViewName();
	}
}