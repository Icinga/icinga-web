<?php

class AppKit_Admin_Groups_IndexAction extends NETWAYSAppKitBaseAction
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
	
	public function isSecure() {
		return true;
	}
	
	public function getCredentials() {
		return array ('appkit.admin', 'appkit.admin.groups');
	}
	
	public function execute(AgaviRequestDataHolder $rd) {
		// We need the execute method to work with parameter od the request!
		
		if ($rd->getParameter('id')) {
			try {
				$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
				$role = $roleadmin->getRoleById($rd->getParameter('id'));
				
				if ($rd->getParameter('toggleActivity', false) == true) {
					$roleadmin->toggleActivity($role);
				}
			}
			catch (Exception $e) {
				
			}
		}
		
		return 'Success';
	}
}

?>