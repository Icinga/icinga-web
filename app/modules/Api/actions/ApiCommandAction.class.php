<?php

class Api_ApiCommandAction extends IcingaApiBaseAction {
    
    public function getDefaultViewName() {
        return 'Success';
    }

    public function checkAuth(AgaviRequestDataHolder $rd) {
        $user = $this->getContext()->getUser();
        $authKey = $rd->getParameter("authkey");
        $validation = $this->getContainer()->getValidationManager();

        if (!$user->isAuthenticated() && $authKey) {
            try {
                $user->doAuthKeyLogin($authKey);
            } catch (Exception $e) {
                $validation->setError("Login error","Invalid Auth key!");
                return false;
            }
        }

        if (!$user->isAuthenticated()) {
            $validation->setError("Login error","Not logged in!");
            return false;
        }
        if( $user->getNsmUser()->getTarget('IcingaCommandRo')) {
            $validation->setError("Error","Commands are disabled for this user!");
            return false;
            
        }
        if ($user->hasCredential("appkit.api.access") || $user->hasCredential("appkit.user")) {
            return true;
        }

        $validation->setError("Error","Invalid credentials for api command access!");
        return false;
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->executeWrite($rd);
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        if (!$this->checkAuth($rd)) {
            return "Error";
        }

        $command = $rd->getParameter("command");
        
        $targets = json_decode($rd->getParameter("target"),true);
        $data = json_decode($rd->getParameter("data"),true);

        if (!is_array($targets)) {
            $targets = array($targets);
        }

        $api = $this->getContext()->getModel("System.CommandSender","Cronks");
        $api->setCommandName($command);
        
        $api->setData(array_merge($data,array("data"=>$data)));
        $api->setSelection($targets);
        
        // send it
        try {
            $api->dispatchCommands();
            $this->setAttribute("success",true);
        } catch (IcingaApiCommandException $e) {
            $this->setAttribute("error",$e->getMessage());
            return 'Error';
        }

        return 'Success';
    }
    
    public function handleError(AgaviRequestDataHolder $rd) {
        return "Error";
    }
}
