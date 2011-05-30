<?php

class AppKit_DataProvider_GroupProviderSuccessView extends AppKitBaseView {

    public function executeJson(AgaviRequestDataHolder $rd) {
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
            // return a single user when an id is provided
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

                $result = $group->toArray();
                $result["users"] = $role_users;

                return json_encode(array("roles" => $result, "totalCount" => count($result), 'success' => true));

            } else {	//return list of all users if no id is provided

                if ($start === false || $limit === false) {
                    $groups = $roleadmin->getRoleCollection($disabled);
                } else {
                    $groups = $roleadmin->getRoleCollectionInRange($disabled,$start,$limit,$sort,$asc);
                }

                $result = array();
                $result = $groups;
            }
        }

        $doc = new AppKitExtJsonDocument();

        // Disable metadata
        if (!$rd->getParameter('addMeta', false) == true) {
            $doc->setAttribute(AppKitExtJsonDocument::ATTR_NOMETA);
        }

        $doc->setMeta(AppKitExtJsonDocument::PROPERTY_ROOT, 'roles');
        $doc->setMeta(AppKitExtJsonDocument::PROPERTY_TOTAL, 'totalCount');

        $doc->applyFieldsFromDoctrineRelation(Doctrine::getTable('NsmRole'));

        $doc->addDataCollection($result);

        $doc->setSuccess(true);

        return $doc->getJson();
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.GroupProvider');
    }
}

?>