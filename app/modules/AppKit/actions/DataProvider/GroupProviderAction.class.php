<?php

class AppKit_DataProvider_GroupProviderAction extends AppKitBaseAction {
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
    public function getDefaultViewName() {
        return 'Success';
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return null;
    }
    
    private function getGroupAsArray(NsmRole $r) {
        return array(
            "id" => $r->role_id,
            "name" => $r->role_name,
            "description" => $r->role_description,
            "created" => $r->role_created,
            "modified" => $r->role_modified,
            "parent" => $r->role_parent,
            "active" => $r->role_disabled != true
        );
     }
    
    public function executeRead(AgaviRequestDataHolder $rd) {
        $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
        $groupId = $rd->getParameter('groupId',false);
        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $start = $rd->getParameter('start',false);
        $limit = $rd->getParameter('limit',false);
        $sort = $rd->getParameter('sort',false);
        $asc = ($rd->getParameter('dir','ASC') == 'ASC');

        $result = array();

        $user = $this->getContext()->getUser();

        if ($user->hasCredential('appkit.admin') == false && $user->hasCredential('appkit.admin.groups') == false) {
            $result = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc, true);
        } else {
            // return a single role when an id is provided
            if ($groupId) {
                $group = $roleadmin->getRoleById($groupId);
                $role_users = array();

                if (!$group instanceof NsmRole) {
                    return "{}";
                }

                foreach($group->NsmUser as $user) {
                    $id =  $user->get("user_id");
                    $name = $user->get("user_name");
                    $role_users[] = array("user_id"=>$id,"user_name"=>$name);
                }

                $result = $this->getGroupAsArray($group);
                $result["users"] = $role_users;

                $this->setAttribute("role",$result);

            } else {	//return list of all roles if no id is provided

                if ($start === false || $limit === false) {
                    $groups = $roleadmin->getRoleCollection($disabled);
                } else {
                    $groups = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc);
                }

            
                $result = array();
                foreach($groups as $group) {
                   $result[] = $this->getGroupAsArray($group,true);
                }
                $this->setAttribute("roles",$result);
            }
        }
        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        // Let the form populate filter display the errors!
        return 'Success';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        if($user->hasCredential('appkit.admin') == false && $user->hasCredential('appkit.admin.groups') == false)
            throw new AgaviSecurityException (("Not Authorized"), 401);
        try {
            if ($rd->getParameter("role_parent") == -1) {
                $rd->setParameter("role_parent",null);
            }

            $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');

            if ($rd->getParameter('id') == 'new') {
                $role = new NsmRole();
            } else {
                $role = $roleadmin->getRoleById($rd->getParameter('id'));
            }

            // Update the basics
            Doctrine_Manager::connection()->beginTransaction();
            $roleadmin->updateRoleData($role, $rd);

            if (!$rd->getParameter("ignorePrincipals",false)) {
                $padmin->updatePrincipalValueData(
                    $role->NsmPrincipal,
                    $rd->getParameter('principal_target', array()),
                    $rd->getParameter('principal_value', array())
                );
            }

            $useradmin = $this->getContext()->getModel('UserAdmin','AppKit');
            $allUsers = $useradmin->getUsersCollection()->toArray();

            $roleUsers = $rd->getParameter("role_users","");
            $roleUsers = explode(";",$roleUsers);


            Doctrine_Manager::connection()->commit();
            $this->updateUserRoles($allUsers,$role,$useradmin,$roleUsers);


            if ($rd->getParameter('id') == 'new') {
                $this->setAttribute('redirect', 'modules.appkit.admin.groups.edit');
                $this->setAttribute('redirect_params', array('id' => $role->role_id));
            }
        } catch (Exception $e) {
            $this->setAttribute("error", $e->getMessage());
            try {
                Doctrine_Manager::connection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}


        }

        return 'Success';
    }


    protected function updateUserRoles($allUsers,$role,$useradmin,$roleUsers) {
        foreach($allUsers as $user) {
            if (!$user) {
                continue;
            }

            $curUser = $useradmin->getUserById($user["user_id"]);

            if (!$curUser) {
                continue;
            }

            $roleExists = false;
            $modified = false;
            foreach($curUser->NsmRole as $key=>$user_role) {
                if ($user_role == $role) {
                    $roleExists = true;

                    if (!in_array($user["user_id"],$roleUsers)) {
                        $modified = true;
                        unset($curUser->NsmRole[$key]);
                    }
                }
            }

            if (!$roleExists && in_array($user["user_id"],$roleUsers)) {
                $uRole = new NsmUserRole();
                $uRole->usro_user_id = $user["user_id"];
                $uRole->usro_role_id = $role->role_id;
                $curUser->NsmUserRole[] = $uRole;

                $modified = true;
            }

            if (!$modified) {
                continue;
            }

            try {
                $curUser->save();
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    }
    
    public function executeRemove(AgaviRequestDataHolder $rd) {
        try {
            Doctrine_Manager::connection()->beginTransaction();
            $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
            $ids = $rd->getParameter("group_id",false);
            foreach($ids as $id) {
                $role = $roleadmin->getRoleById($id);

                if (!$role) {
                    continue;
                }

                $roleadmin->removeRole($role);
            }
            Doctrine_Manager::connection()->commit();
        } catch (Exception $e) {
            try {
                Doctrine_Manager::connection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}
            $this->setAttribute("error",$e->getMessage());
        }
            
        return 'Success';
    }


}

?>