<?php

class AppKit_DataProvider_GroupProviderSuccessView extends AppKitBaseView {

    
    public function executeJson(AgaviRequestDataHolder $rd) {
        $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');

        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $result = $this->getAttribute("role",false);
        if($result !== false)
            return json_encode(array("sucess"=>true,"role" => $result));
        
        $result = $this->getAttribute("roles",false);
        if($result !== false) {
            $totals = $roleadmin->getRoleCount($disabled);
            return json_encode(array("success"=>true,"roles" => $result, "totalCount" => $totals));
        }
        $error = $this->getAttribute("error");
        if($error)
            return json_encode(array("success"=>false,"error" => $error));
        else
            return json_encode(array("success"=>true,"role"=>array()));
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.GroupProvider');
    }
}

?>