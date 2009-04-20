<?php

class AppKit_SecureAction extends NETWAYSAppKitBaseAction
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
		$this->setAttributes($this->getContainer()->getAttributes('org.agavi.controller.forwards.secure'));
		
		// Okay log this abuse!
		$this->getContext()->getLoggerManager()->logWarn('Access with sufficient privileges to %s (%s) by %s (%s)', 
			$this->getAttribute('requested_action'),
			$this->getAttribute('requested_module'),
			$this->getContext()->getUser()->getAttribute('userobj')->user_name,
			$this->getContext()->getUser()->getAttribute('userobj')->givenName()
		);
		
		return $this->getDefaultViewName();
	}
}

?>