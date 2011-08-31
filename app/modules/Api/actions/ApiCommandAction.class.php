<?php

class Api_ApiCommandAction extends IcingaApiBaseAction {
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
    private $instances           = array();
    
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

        if ($user->hasCredential("appkit.api.access") || $user->hasCredential("appkit.user")) {
            return true;
        }

        $validation->setError("Error","Invalid credentials for api access!");
        return false;
    }



    public function executeRead(AgaviRequestDataHolder $rd) {
        $this->executeWrite($rd);

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
        $api->setData($data);
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
    
    

    private function buildCommandArray($command,array $targets, $data) {
        $commands = array();
        foreach($targets as $target)	{
            $cmd = IcingaApiConstants::getCommandObject();
            $cmd->setCommand($command);
            foreach($target as $field=>$value) {

                $cmd->setTarget($field,$value);
            }
            foreach($data as $field=>$value) {

                $cmd->setTarget($field,$value);
            }
            $commands[] = $cmd;
        }
        return $commands;
    }
}

?>
