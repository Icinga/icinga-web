<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
    
    private function getGroupAsArray(NsmRole $r, $oldBehaviour=false) {
        if ($oldBehaviour == true) {
            return array(
                'role_id' => $r->role_id,
                'role_name' => $r->role_name,
                'role_description' => $r->role_description,
                'role_created' => $r->role_created,
                'role_modified' => $r->role_modified,
                'role_parent' => $r->role_parent,
                'role_disabled' => $r->role_disabled
            );
        }

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
    
    private function formatRole(NsmRole $r,$simple = false, $oldBehaviour = false) {
        
        $roleObject = $this->getGroupAsArray($r, $oldBehaviour);
        if($simple)
            return $roleObject;

        $roleObject["users"] = array();
        $users = $r->NsmUser;

       
        foreach($users as $user) {
            $roleObject["users"][] = array(
                "id"=>$user->user_id,
                "name"=>$user->user_name,
                "firstname" => $user->user_firstname,
                "lastname" => $user->user_lastname,
                "active"=>$user->user_disabled != true
            );
        }
        $principals = $r->getPrincipals();
        $roleObject["principals"] = array();
        foreach($principals as $principal)
            if (($targets = $principal->NsmPrincipalTarget)) {
            foreach($targets as $t)
                $roleObject["principals"][] = array(
                    "target" => $t->NsmTarget->toArray(),
                    "values" => $t->NsmTargetValue->toArray()
                );
            }
        return $roleObject;    
    }
    
    public function executeRead(AgaviRequestDataHolder $rd) {
        $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
        $groupId = $rd->getParameter('groupId',false);
        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $start = $rd->getParameter('start',false);
        $limit = $rd->getParameter('limit',false);
        $sort = $rd->getParameter('sort',false);
        $asc = ($rd->getParameter('dir','ASC') == 'ASC');
        $oldBehaviour = $rd->getParameter('oldBehaviour', false);
        $user = $this->getContext()->getUser();
        $groups = null;
        
        // Return roles the user belongs to
        if ($user->hasCredential('appkit.admin') == false && $user->hasCredential('appkit.admin.groups') == false) {
            $groups = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc, true);
        
        // Global access to all rules
        } else {
            // return a single role when an id is provided
            if ($groupId) {
                $group = $roleadmin->getRoleById($groupId);
                $role_users = array();

                if (!$group instanceof NsmRole) {
                    return "{}";
                }

                

                $result = $this->formatRole($group, false, $oldBehaviour);

                $this->setAttribute("role",$result);
                
                return $this->getDefaultViewName();

            } else {    //return list of all roles if no id is provided

                if ($start === false || $limit === false) {
                   
                    $groups = $roleadmin->getRoleCollection($disabled);
                } else {
                    $groups = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc);
                }
            }
        }
        
        if ($groups && $groups instanceof Doctrine_Collection) {
            $result = array();
            foreach($groups as $group) {
                $result[] = $this->formatRole($group,true,$oldBehaviour);
            }
            $this->setAttribute("roles",$result);
        }
        
        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        // Let the form populate filter display the errors!
        return 'Success';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $user = $this->getContext()->getUser();

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
            AppKitDoctrineUtil::getConnection()->beginTransaction();
            $roleadmin->updateRoleData($role, $rd);

            if (!$rd->getParameter("ignorePrincipals",false)) {
                $padmin->updatePrincipalValueData(
                    $role->NsmPrincipal,
                    $rd->getParameter('principal_target', array()),
                    $rd->getParameter('principal_value', array())
                );
            }

            AppKitDoctrineUtil::getConnection()->commit();
            
            if (!$rd->getParameter("ignorePrincipals",false)) {
                $useradmin = $this->getContext()->getModel('UserAdmin','AppKit');
                $allUsers = $useradmin->getUsersCollection()->toArray();
                $roleUsers = $rd->getParameter("role_users",array());
                $this->updateUserRoles($allUsers,$role,$useradmin,$roleUsers);
            }
          
        } catch (Exception $e) {
            $this->setAttribute("error", $e->getMessage());
            try {
                AppKitDoctrineUtil::getConnection()->rollback();
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
            AppKitDoctrineUtil::getConnection()->beginTransaction();
            $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
            $ids = $rd->getParameter("ids",array());
            foreach($ids as $id) {
                $role = $roleadmin->getRoleById($id);

                if (!$role) {
                    continue;
                }

                $roleadmin->removeRole($role);
            }
            AppKitDoctrineUtil::getConnection()->commit();
        } catch (Exception $e) {
            try {
                AppKitDoctrineUtil::getConnection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}
            $this->setAttribute("error",$e->getMessage());
        }
            
        return 'Success';
    }


}

?>
