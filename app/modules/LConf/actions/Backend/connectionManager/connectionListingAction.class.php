<?php

class LConf_Backend_connectionManager_connectionListingAction extends IcingaLConfBaseAction
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
	
	public function executeRemove(AgaviRequestDataHolder $rd) {
		try {
			$id = $rd->getParameter("connection_id",false);
			
			if(!is_array($id))
				$id = array($id);
				
			foreach($id as $curId) {
				$connectionManager = $this->getContext()->getModel("LDAPConnectionManager","LConf");
				$connectionManager->dropConnection($curId);
			}
		} catch(Exception $e) {
			$rd->setParameter("_error",$e->getMessage());
		}
		return "Success";
	}
	
	public function executeWrite(AgaviRequestDataHolder $rd) {
		try {
			$alteredConnection = $rd->getParameter("connections");
			$alteredConnection = json_decode($alteredConnection,true);
			
			if(!$alteredConnection)
				throw new AppKitException("Invalid JSON send");
			// always wrap as array to make it iteratable
			if(isset($alteredConnection["connection_ldaps"])) {
				if($alteredConnection["connection_ldaps"] == "on")
					$alteredConnection["connection_ldaps"] = 1; 				
			} else $alteredConnection["connection_ldaps"] = '0';
			
			if(isset($alteredConnection["connection_tls"])) {
				if($alteredConnection["connection_tls"] == "on")
					$alteredConnection["connection_tls"] = 1; 
			} else $alteredConnection["connection_tls"] = '0'; 

			if(isset($alteredConnection["connection_name"]))
				$alteredConnection = array($alteredConnection);
			$userId = $this->getContext()->getUser()->getNsmUser()->get("user_id");
			$connectionManager = $this->getContext()->getModel("LDAPConnectionManager","LConf");
			$mgr = $this->getContext()->getModel("Admin.LConfPrincipalAdmin","LConf");

			foreach($alteredConnection as $connection) { 
				$id = $connectionManager->addConnection($connection);
				$mgr->addPrincipals($id,"users",'{"user_id":"'.$userId.'"}');
			}	
		} catch(Exception $e) {
			
			$rd->setParameter("_error",$e->getMessage());
		}
		return "Success";
	}
	
	public function handleError(AgaviRequestDataHolder $rd) {
		$ctx = $this->getContext();
		$errors = $this->getContainer()->getValidationManager()->getErrorMessages();
		foreach($errors as $error) 	 {
			$ctx->getLoggerManager()->log($error["message"]);
		}
		$rd->setParameter("_error","Incompatible request submitted");
		
		return 'Success';
	}
	
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	public function isSecure() {
		return true;
	}

}

?>
