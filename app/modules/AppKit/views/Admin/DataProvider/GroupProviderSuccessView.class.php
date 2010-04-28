<?php

class AppKit_Admin_DataProvider_GroupProviderSuccessView extends ICINGAAppKitBaseView
{
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
		$groupId = $rd->getParameter('groupId',false);
		$result;
		// return a single user when an id is provided
		if($groupId) {
			$group = $roleadmin->getRoleById($groupId);
			$role_users = array();
			if(!$group instanceof NsmRole)
				return "{}";
			
			foreach($group->NsmUser as $user) {
				$id =  $user->get("user_id");
				$name = $user->get("user_name");
				$role_users[] = array("user_id"=>$id,"user_name"=>$name);
			}
			
			$result = $group->toArray();
			$result["users"] = $role_users;
		} else {	//return list of all users if no id is provided
			$groups = $roleadmin->getRoleCollection(true)->toArray();
			$result = $groups;
		}
		return json_encode($result);
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Admin.DataProvider.GroupProvider');
	}
}

?>