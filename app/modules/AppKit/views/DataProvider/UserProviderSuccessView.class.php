<?php

class AppKit_DataProvider_UserProviderSuccessView extends AppKitBaseView {

    public function executeJson(AgaviRequestDataHolder $rd) {
        $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');

        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $result = $this->getAttribute("user",false);
        if($result !== false)
            return json_encode(array("sucess"=>true,"user" => $result));
        
        $result = $this->getAttribute("users",false);
        if($result !== false) {
            $totals = $useradmin->getUserCount($disabled);
            return json_encode(array("success"=>true,"users" => $result, "totalCount" => $totals));
        }
        $error = $this->getAttribute("error");
        if($error)
            return json_encode(array("success"=>false,"error" => $error));
        else
            return json_encode(array("success"=>true,"user"=>array()));
    }

   

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.UserProvider');
    }
}

?>