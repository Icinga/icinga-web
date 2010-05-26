<?php

class AppKit_Admin_Groups_EditAction extends AppKitBaseAction
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
		return array ('appkit.admin', 'appkit.admin.users');
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		// We need the execute method to work with parameter od the request!
		return 'Success';
	}
	
	public function handleError(AgaviRequestDataHolder $rd) {
		// Let the form populate filter display the errors!
		return 'Success';
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
		
		try {
			if($rd->getParameter("role_parent") == -1) {
				$rd->setParameter("role_parent",null);
			}
			$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
			$padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');

			if ($rd->getParameter('id') == 'new') {
				$role = new NsmRole();
			}
			else {
				$role = $roleadmin->getRoleById($rd->getParameter('id'));
			}
			
			// Update the basics
			Doctrine_Manager::connection()->beginTransaction();
			$roleadmin->updateRoleData($role, $rd);
			if(!$rd->getParameter("ignorePrincipals",false)) {
				$padmin->updatePrincipalValueData(
					$role->NsmPrincipal, 
					$rd->getParameter('principal_target', array ()),
					$rd->getParameter('principal_value', array ())
				);
			}
				Doctrine_Manager::connection()->commit();
			
			
			if ($rd->getParameter('id') == 'new') {
				$this->setAttribute('redirect', 'appkit.admin.groups.edit');
				$this->setAttribute('redirect_params', array('id' => $role->role_id));
			}
		}
		catch (Exception $e) {
			try {
				Doctrine_Manager::connection()->rollback();
			}
			catch (Doctrine_Transaction_Exception $e) {}
			
			
		}
		
		return 'Success';
	}
}

?>