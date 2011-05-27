<?php

class AppKit_DataProvider_PrincipalProviderSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $principalAdmin = $this->getContext()->getModel("PrincipalAdmin","AppKit");
        $groupId = $rd->getParameter("groupId");
        $userId = $rd->getParameter("userId");
        $result;

        if (!$groupId && !$userId) {
            $result = array_values($principalAdmin->getTargetArray());
        } else	if ($userId)  {
            $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
            $user = $useradmin->getUserById($userId);
            $prid = $user->NsmPrincipal->principal_id;
            $result = $principalAdmin->getSelectedValues($prid);
        } else if ($groupId) {
            $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
            $role = $roleadmin->getRoleById($groupId);
            $prid = $role->NsmPrincipal->principal_id;
            $result = $principalAdmin->getSelectedValues($prid);
        }

        return json_encode($result);
    }


    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.PrincipalProvider');
    }
}

?>