<?php

class AppKit_Widgets_HeaderDataAction extends AppKitBaseAction {

	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function execute(AgaviRequestDataHolder $rd) {
		$type = $rd->getParameter('type', 'javascript');
		
		switch($type) {
			case 'javascript':
				$includes = array(
					$this->getContext()->getRouting()->gen('appkit.squishloader.javascript')
				);
				$includes[] = $this->getContext()->getRouting()->gen('appkit.ext.applicationState', array('cmd' => 'init'));
				
				break;
			case 'css':
				$includes = array(
					$this->getContext()->getRouting()->gen('appkit.squishloader.css')
				);

				$resources = $this->getContext()->getModel('Resources', 'AppKit');
				
				$imports = $resources->getCssImports();
				
				$this->setAttribute('imports', $imports);
				
				break;
		}
		$this->setAttribute('includes', $includes);
		
		return $this->getDefaultViewName();
	}
	
}