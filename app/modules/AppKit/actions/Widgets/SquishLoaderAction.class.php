<?php

class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction {

	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		$ra = explode('.', array_pop(
			$this->getContext()->getRequest()->getAttribute(
				'matched_routes', 'org.agavi.routing'
			)
		));
		
		$type = array_pop($ra);
		
		$loader = $this->getContext()->getModel(
			'SquishFileContainer',
			'AppKit',
			array('type' => $type)
		);
		
		$resources = $this->getContext()->getModel('Resources', 'AppKit');
		
		switch($type) {
			case 'javascript':
				try {	
					$loader->addFiles(
						$resources->getJavascriptFiles()
					);
				
					$actions = $this->getContext()->getRequest()->getAttribute('app.javascript_actions', AppKitModuleUtil::DEFAULT_NAMESPACE, array());
				
					$this->setAttribute('javascript_actions', $actions);
				} catch(AppKitModelException $e) {
					$this->setAttribute('errors', $e->getMessage());
				}
				
				break;
			case 'css':
				try {
					$loader->addFiles(
						$resources->getCssFiles()
					);
				} catch(AppKitModelException $e) {
					$this->setAttribute('errors', $e->getMessage());
				}
				
				break;
		}
		
		$loader->squishContents();
		$this->setAttribute('content', $loader->getContent(). chr(10));
		
		return $this->getDefaultViewName();
	}
	
}