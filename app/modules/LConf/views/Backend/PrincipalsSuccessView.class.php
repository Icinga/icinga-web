<?php

class LConf_Backend_PrincipalsSuccessView extends IcingaLConfBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.Principals');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd)
	{
		$ctx = $this->getContext();
		if($error = $rd->getParameter("_error")) {
			$this->getContainer()->getResponse()->setHttpStatusCode("500");
			return json_encode(array("error" => $error));
		}
		$model = $ctx->getModel("LDAPConnectionManager","LConf");
		$target = $rd->getParameter("target");
		$connection_id = $rd->getParameter("connection_id");

		if($target) {
			switch($target) {
				case 'groups':
					$result = array("groups" => $model->getGroupsForConnection($connection_id));
					return json_encode($result);
					break;
				case 'users':
					$result = array("users" => $model->getUsersForConnection($connection_id));
					return json_encode($result);
					break;
		
			}
		}
	}
}

?>