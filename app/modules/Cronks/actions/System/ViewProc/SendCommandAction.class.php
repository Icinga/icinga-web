<?php

class Cronks_System_ViewProc_SendCommandAction extends CronksBaseAction {
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

    public function executeRead(AgaviParameterHolder $rd) {
        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {

        $data = json_decode($rd->getParameter('data'), true);
        $selection = json_decode($rd->getParameter('selection'),true);
        $command = $rd->getParameter('command');
        $auth = $rd->getParameter('auth');

        $this->log('SendCommandAction: Prepare to send command (command=%s)', $command, AgaviLogger::INFO);

        $IcingaApiCommand = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');

        // The model
        $sender = $this->getContext()->getModel('System.CommandSender', 'Cronks');

        if ($sender->checkAuth($rd->getParameter('command'), $rd->getParameter('selection'), $rd->getParameter('data'), $auth) === true) {

            $this->log('SendCommandAction: Successfull authentication (hmac=%s)', $auth, AgaviLogger::DEBUG);

            // Prepare the data
            $sender->setCommandName($command);
            $sender->setSelection($selection);
            $sender->setData($data);
            try {
                $sender->dispatchCommands();
            
                $this->setAttribute('ok', true);
                $this->setAttribute('error', null);
            } catch(Exception $e) {
                $this->setAttribute("ok", false);
                $this->setAttribute("error",$e->getMessage());
            }
            
        } else {
            $this->setAttribute('ok', false);
            $this->setAttribute('error', 'Authentification failed');
            $this->log('SendCommandAction: Authentification failed! (hmac=%s)', $auth, AgaviLogger::DEBUG);
        }

        return $this->getDefaultViewName();
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        $this->setAttribute('ok', false);
        $this->setAttribute('error', 'Validation failed');
        $this->log('SendCommandAction: Validation failed, wrong parameters!', AgaviLogger::ERROR);
        return $this->getDefaultViewName();
    }
}

?>
