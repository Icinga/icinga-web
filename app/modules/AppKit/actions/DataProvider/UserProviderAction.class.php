<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class AppKit_DataProvider_UserProviderAction extends AppKitBaseAction {

    private static $sortArray = array(
        "firstname" => "user_firstname",
        "name" => "user_name",
        "lastname" => "user_lastname",
        "modified" => "user_modified",
        "created" => "user_created",
        "last_login" => "user_last_login",
        "authsrc" => "user_authsrc",
        "disabled" => "user_disabled",
        "id" => "user_id",
        "email" => "user_email"
    );
    public function getDefaultViewName() {
        return 'Success';
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('appkit.admin', 'appkit.admin.users');
    }

    /**
     * Return a ready mapped row record
     * @param NsmUser $user
     * @return array
     */
    private function getUserAsArray(NsmUser $user) {
        return array(
            "id"            => $user->user_id,
            "name"          => $user->user_name,
            "lastname"      => $user->user_lastname,
            "firstname"     => $user->user_firstname,
            "authsrc"       => $user->user_authsrc,
            "authkey"       => $user->user_authkey,
            "email"         => $user->user_email,
            "description"   => $user->user_description,
            "modified"      => $user->user_modified,
            "created"       => $user->user_created,
            "last_login"    => $user->user_last_login == 0 ? 'Not determined yet' : $user->user_last_login,
            "disabled"      => $user->user_disabled
        );
    }

    private function formatUser(NsmUser $user, $simple = false) {

        $userObject = $this->getUserAsArray($user);
        if($simple)
            return $userObject;
        $groups = $user->NsmRole;
        $userObject["roles"] = array();
        foreach($groups as $role) {

            $userObject["roles"][] = array(
                "id" => $role->role_id,
                "name" => $role->role_name,
                "description" => $role->role_name,
                "active" => $role->role_disabled != 1
            );
        }



        $userObject["principals"] = array();
        $targets = $user->getTargets(null,true);

        foreach($targets as $t) {
            //if($t->target_type != "icinga")
            //    continue;
            $userObject["principals"][] = array(
                "target" => $t->toArray(),
                "values" => $user->getTargetValues($t->target_name)->toArray()

            );
        }

        return $userObject;
    }

    public function executeRead(AgaviRequestDataHolder $rd) {

        /** @var $useradmin AppKit_UserAdminModel */
        $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');

        $userId = $rd->getParameter('userId',false);
        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $start = $rd->getParameter('start',false);
        $limit = $rd->getParameter('limit',false);
        $sort = $rd->getParameter('sort',false);
        $asc = ($rd->getParameter('dir','ASC') == 'ASC');
        $query = $rd->getParameter('query');

        if ($query) {
            $useradmin->setQuery($query);
        }

        $result = null;
        if($sort) {
            if(array_key_exists($sort, self::$sortArray))
                $sort = self::$sortArray[$sort];
            else
                $sort = false;
        }

        // return a single user when an id is provided
        if ($userId !== false) {
            $user = $useradmin->getUserById($userId);

            if (!$user instanceof NsmUser) {
                return "{}";
            }

            $result = $this->formatUser($user);

            $this->setAttribute("user", $result);

        } else {    //return list of all users if no id is provided
            $users = null;
            if ($start === false || $limit === false) {
                $users = $useradmin->getUsersCollection($disabled);
            } else {
                $users = $useradmin->getUsersCollectionInRange($disabled,$start,$limit,$sort,$asc);
            }

            $result = array();
            foreach($users as $user) {
                $result[] = $this->formatUser($user,true);
            }
            $this->setAttribute("users", $result);
       }
       return 'Success';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        // We need the execute method to work with parameter od the request!
        try {
            AppKitDoctrineUtil::getConnection()->beginTransaction();

            /** @var $useradmin AppKit_UserAdminModel **/
            $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');

            /** @var $padmin AppKit_PrincipalAdminModel **/
            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');

            if ($rd->getParameter('id') == 'new') {
                $user = new NsmUser();
            } else {
                $user = $useradmin->getUserById($rd->getParameter('id'));
            }

            $useradmin->updateUserData($user, $rd);

            if ($rd->getParameter('password_validate', false) !== false) {
                $useradmin->updateUserPassword($user, $rd->getParameter('password_validate'));
            }

            // Updating the roles
            $useradmin->updateUserroles($user, $rd->getParameter('userroles', array()));

            $padmin->updatePrincipalValueData(
                $user->principal,
                $rd->getParameter('principal_target', array()),
                $rd->getParameter('principal_value', array())
            );

            AppKitDoctrineUtil::getConnection()->commit();
        } catch (Exception $e) {
            try {
                AppKitDoctrineUtil::getConnection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}
            print_r($e->getTraceAsString());
            $this->setAttribute("error",$e->getMessage());

        }

        return 'Success';
    }

    public function executeRemove(AgaviRequestDataHolder $rd) {
        $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
        $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
        
        $ids = $rd->getParameter("ids",array());
        
        foreach($ids as $id) {
            $user = $useradmin->getUserById($id);

            if (!$user) {
                continue;
            }

            $useradmin->removeUser($user);
        }

        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return 'Success';
    }

}

?>
