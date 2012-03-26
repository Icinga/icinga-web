<?php

class LConf_Backend_Cronks_CustomVarDNCollectorAction extends IcingaLConfBaseAction
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
	
	public function executeRead() {
		return $this->getDefaultViewName();
	}
	
	public function executeWrite() {
		return $this->getDefaultViewName();
	}
	
	public function getDefaultViewName()
	{
		return 'Success';
	}	
	
	public function isSecure() {
		return true;
	}

    public function getCredentials() {
        return array('icinga.user');
    }
}

?>