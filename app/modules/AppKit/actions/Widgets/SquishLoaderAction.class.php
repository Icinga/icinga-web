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

		// We need the data in the action,
		// it's too late in the view
		AppKitModuleUtil::getInstance()->applyToRequestAttributes($this->getContainer());
		
		$loader = $this->getContext()->getModel('SquishFileContainer', 'AppKit', array('type' => 'javascript'));

		try {
			
			$loader->addFiles(
				$this->getContext()->getRequest()->getAttribute('app.javascript_files', AppKitModuleUtil::DEFAULT_NAMESPACE, array())
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

?>