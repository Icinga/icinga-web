<?php

class AppKit_DataProvider_GroupProviderSuccessView extends AppKitBaseView
{
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
		$groupId = $rd->getParameter('groupId',false);
		$disabled = $rd->getParameter('hideDisabled',false) == "false";
		$start = $rd->getParameter('start',false);
		$limit = $rd->getParameter('limit',false);
		$sort = $rd->getParameter('sort',false);
		$asc = ($rd->getParameter('dir','ASC') == 'ASC');
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

			if($start === false || $limit === false)
				$groups = $roleadmin->getRoleCollection($disabled)->toArray();
			else 
				$groups = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc)->toArray();
			$result = array();	
			$result = $groups;
		}
		return json_encode(array("roles" => $result, "totalCount" => $roleadmin->getRoleCount($disabled)));
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Admin.DataProvider.GroupProvider');
	}
}

?>