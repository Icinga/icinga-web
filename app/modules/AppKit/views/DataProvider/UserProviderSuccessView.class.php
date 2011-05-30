<?php

class AppKit_DataProvider_UserProviderSuccessView extends AppKitBaseView {

    public function executeJson(AgaviRequestDataHolder $rd) {
        $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
        $userId = $rd->getParameter('userId',false);
        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $start = $rd->getParameter('start',false);
        $limit = $rd->getParameter('limit',false);
        $sort = $rd->getParameter('sort',false);
        $asc = ($rd->getParameter('dir','ASC') == 'ASC');

        $result;

        // return a single user when an id is provided
        if ($userId) {
            $user = $useradmin->getUserById($userId);

            if (!$user instanceof NsmUser) {
                return "{}";
            }

            $result = $this->formatUser($user);
        } else {	//return list of all users if no id is provided
            $users;

            if ($start === false || $limit === false) {
                $users = $useradmin->getUsersCollection($disabled);
            } else {
                $users = $useradmin->getUsersCollectionInRange($disabled,$start,$limit,$sort,$asc);
            }

            $result = array();
            foreach($users as $user) {
                $result[] = $this->formatUser($user);
            }
        }

        return json_encode(array("users" => $result, "totalCount" => $useradmin->getUserCount($disabled)));
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

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.UserProvider');
    }
}

?>