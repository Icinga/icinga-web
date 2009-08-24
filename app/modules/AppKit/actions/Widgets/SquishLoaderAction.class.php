<?php

class AppKit_Widgets_SquishLoaderAction extends ICINGAAppKitBaseAction
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
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		$type = $rd->getParameter('type');
		$files = array ();
		$loader = $this->getContext()->getModel('SquishFileContainer', 'AppKit');
		$loader->setType($type);
		
		switch ($type) {
			case AppKit_SquishFileContainerModel::TYPE_JAVASCRIPT:
				$files = AgaviConfig::get('de.icinga.appkit.include_javascript', array());
			break;
			
			case AppKit_SquishFileContainerModel::TYPE_STYLESHEET:
				$files = AgaviConfig::get('de.icinga.appkit.include_styles', array());
			break;
		}
		
		// Adding preconfigured javascript files
		foreach ($files as $file) {
			$loader->addFile($type, $file);
		}
		
		$this->setAttribute('content', $loader->squishContents());
		
		return $this->getDefaultViewName();
	}
}

?>