<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
