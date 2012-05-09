<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class Web_Icinga_ApiCommandAction extends IcingaWebBaseAction {
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

        $api = $this->getContext()->getModel("Icinga.ApiContainer","Web");

        $commands = $this->buildCommandArray($command,$targets,$data);

        // send it
        try {
            $api->dispatchCommandArray($commands);
            $this->setAttribute("success",true);
        } catch (IcingaApiCommandException $e) {
            $str= "";
            foreach($api->getLastErrors() as $err) {
                $str .= $err->getMessage()."\n";
            }
            $this->setAttribute("error",$str);
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
