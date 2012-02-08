<?php

class Api_ApiCommandAction extends IcingaApiBaseAction {
    
    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->executeWrite($rd);
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        if (!$this->context->getUser()->isAuthenticated() || !$this->context->getUser()->hasCredential('icinga.user')) {
	        return array('Api', 'GenericError');
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
        } catch (Exception $e) {
            $this->setAttribute("error",$e->getMessage());
            return 'Error';
        }

        return 'Success';
    }
    
    public function handleError(AgaviRequestDataHolder $rd) {
        return "Error";
    }
}
