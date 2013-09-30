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
        try {
            if($this->context->getUser()->getNsmUser()->getTarget('IcingaCommandRo')) {
                $errors = array('Commands are disabled for this user');
                $this->getContainer()->setAttributeByRef('errors', $errors, 'org.icinga.api.auth');
                $this->getContainer()->setAttribute('success', false, 'org.icinga.api.auth');
            }
            return array('Api', 'GenericError');

        } catch (AppKitDoctrineException $e) {
            // PASS
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
