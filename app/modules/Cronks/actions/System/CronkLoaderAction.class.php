<?php

class Cronks_System_CronkLoaderAction extends CronksBaseAction
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
	
	public function executeRead(AgaviParameterHolder $rd) {
		return $this->getDefaultViewName();
	}
	
	public function executeWrite(AgaviParameterHolder $rd) {
		return $this->getDefaultViewName();
	}
	
	public function isSecure() {
		return true;
	}
	
	public function getCredentials() {
		return array ('icinga.user');
	}
	
	public function handleError(AgaviParameterHolder $rd) {
		return $this->getDefaultViewName();
	}
}

?>