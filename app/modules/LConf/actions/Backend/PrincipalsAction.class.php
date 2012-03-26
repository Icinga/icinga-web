<?php 

class LConf_Backend_PrincipalsAction extends IcingaLConfBaseAction
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
		return 'Success';
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
		$target = $rd->getParameter("target",false);
		if($target)
			$values = $rd->getParameter($target);
		$connection_id = $rd->getParameter("connection_id");
	
		switch($rd->getParameter("xaction",false))  {
			case 'destroy':
				if(!$connection_id || !$values || !$target) {
					$rd->setParameter('_error',"Invalid parameter count!");
					break;
				}					
				$mgr = $this->getContext()->getModel("Admin.LConfPrincipalAdmin","LConf");
				$mgr->removePrincipals($connection_id,$target,$values);
				
				break;
			case 'create' :
				if(!$connection_id || !$values || !$target) {
					$rd->setParameter('_error',"Invalid parameter count!");
					break;
				}			
				$mgr = $this->getContext()->getModel("Admin.LConfPrincipalAdmin","LConf");
				$mgr->addPrincipals($connection_id,$target,$values);
				break;
		}
		
		return 'Success';
	}
	
	public function getCredentials() {
		return 'lconf.user';
	}
	
	public function isSecure() {
		return true;
	}
}

?>
