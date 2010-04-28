<?php

class AppKit_DataProvider_UserProviderSuccessView extends ICINGAAppKitBaseView
{
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		$useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
		$userId = $rd->getParameter('userId',false);
		$result;
		// return a single user when an id is provided
		if($userId) {
			$user = $useradmin->getUserById($userId);
			if(!$user instanceof NsmUser)
				return "{}";
			$result = $this->formatUser($user);
		} else {	//return list of all users if no id is provided
			$users = $useradmin->getUsersCollection(true);
			$result = array();
			foreach($users as $user) {
				$result[] = $this->formatUser($user);
			}
		}
		return json_encode($result);
	}
	
	public function formatUser(NsmUser $user) {
		$groups = $user->NsmUserRole->toArray();
		$userObject = $user->toArray();
		$userObject["user_password"] = "";
		$userObject["roles"] = array();
		foreach($groups as $role) {
			$userObject["userroles[".$role["usro_role_id"]."]"] = true;
		}
		return $userObject;
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Admin.DataProvider.UserProvider');
	}
}

?>