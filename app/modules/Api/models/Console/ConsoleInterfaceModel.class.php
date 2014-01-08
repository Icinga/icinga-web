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


class ApiUnknownConsoleException extends AppKitException {};

class Api_Console_ConsoleInterfaceModel extends IcingaApiBaseModel implements IcingaConsoleInterface {
    protected $hosts = array();
    protected $hostname;
    protected $connections = array();
    public function getHostName() {
        return $this->hostname;
    }

    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        if (!isset($parameters["host"]) && !isset($parameters["icingaInstance"])) {
            $this->hostname = AccessConfig::getDefaultHostname();
        } else if (!isset($parameters["host"]) && isset($parameters["icingaInstance"])) {
            $this->hostname = AccessConfig::getHostnameByInstance($parameters["icingaInstance"]);
        }

        if(isset($parameters["host"]))
            $this->hostname = AccessConfig::getHostByName($parameters["host"]);
        $this->initConnection();
    }

    public function exec(IcingaConsoleCommandInterface $cmd)    {
        if(empty($this->connections)) {
            return false;
        }
        foreach($this->connections as $name=>$connection) {

            $currentCmd = clone($cmd);
            $currentCmd->setHost($name);
            $currentCmd->setConnection($this);
            $connection->exec($currentCmd);

            $cmd->setReturnCode($currentCmd->getReturnCode());
            $cmd->setOutput($currentCmd->getOutput());
            if($cmd->getReturnCode() != 0)
                break;
        }
    }


    protected function initConnection() {
        
        $this->hosts = AccessConfig::getHostByName($this->hostname);

        if(isset($this->hosts["auth"])) {
            $result = array();
            $result[$this->hostname] = $this->hosts;
            $this->hosts = $result;
        }
        $this->setUpConsole();
    } 

    protected function setUpConsole() {
    
        foreach($this->hosts as $name=>$host) {
            $type = strtolower($host["auth"]["type"]);
            $type[0] = strtoupper($type[0]);
            $class = $type."ConsoleConnection";

            if (!class_exists($class,true)) {
                throw new ApiUnknownConsoleException("Unknown connection type: $type");
            }

            $this->connections[$name] = new $class($host);
        }
    }
};
