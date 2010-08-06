<?php

class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction
{
	
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName() {
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		
		$files = array ();
		$actions = array ();
		
		$loader = $this->getContext()->getModel('SquishFileContainer', 'AppKit', array('type' => 'javascript'));
		
		try {
		
			$files = AgaviConfig::get('org.icinga.appkit.include_javascript', array());
			
			if (array_key_exists('squished', $files)) {
				$loader->addFiles($files['squished']);
			}
			
			if (array_key_exists('action', $files)) {
				$loader->setActions($files['action']);
			}
			
			$loader->squishContents();
			
			$this->setAttributeByRef('model', $loader);
		
		}
		catch(AppKitModelException $e) {
			$this->setAttribute('errors', $e->getMessage());
		}
		
		return $this->getDefaultViewName();
	}
}

?>