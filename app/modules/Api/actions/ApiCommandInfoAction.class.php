<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Api_ApiCommandInfoAction extends IcingaApiBaseAction implements IAppKitDispatchableAction {
    public function getDefaultViewName() {
        return 'Success';
    }
    
    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->executeWrite($rd);
    }
    
    public function executeWrite(AgaviRequestDataHolder $rd) {
        if (!$this->context->getUser()->isAuthenticated()) {
            return array('Api', 'GenericError');
        }
        
        $model = $this->getContext()->getModel('Commands.CommandInfo', 'Api');
        
        $commands = array ();
        
        if ($rd->getParameter('command')) {
            $commands = $model->getInfo($rd->getParameter('command'));
        } elseif ($rd->getParameter('type')) {
            $commands = $model->filterCommandByType($rd->getParameter('type'));
        } else {
            $commands = $model->getAll();
        }
        
        $this->setAttributeByRef('commands', $commands);
        
        return $this->getDefaultViewName();
    }
    
    public function isSecure() {
        return true;
    }
    public function getCredentials() {
        return "icinga.user";
    }
}