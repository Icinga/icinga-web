<?php

class AppKit_Widgets_ShowNavigationAction extends ICINGAAppKitBaseAction
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
	
	public function execute(AgaviRequestDataHolder $rd) {
		// Hopefully believing that the nav model could be initialized! (menu.php)
		if ($this->getContext()->getModel('NavigationContainer', 'AppKit')->getContainer()->Count() === 0) {
			AppKitEventDispatcher::getInstance()->triggerSimpleEvent('appkit.menu', 'we need the menu here ...');
		}
		
		return $this->getDefaultViewName();
	}
}

?>