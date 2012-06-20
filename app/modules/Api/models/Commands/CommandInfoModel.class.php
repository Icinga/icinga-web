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


class Api_Commands_CommandInfoModel extends IcingaApiBaseModel implements AgaviISingletonModel {

    private $config = array();

    /**
     * @var AppKitSecurityUser
     */
    private $user = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/icingaCommands.xml'));

        $this->user = $context->getUser();

        if ($this->user->getNsmUser()->hasTarget('IcingaCommandRestrictions')) {
            $this->filterCommandsByUser($this->config);
        }
    }

    private function filterCommandsByUser(&$array) {
        static $filterFn = null;
        
        if ($filterFn === null) {
            $filterFn = create_function('$arr', 'return (isset($arr["isSimple"]) && $arr["isSimple"] == "true");');
        }
        
        $array = array_filter($array, $filterFn);
    }
    
    public function filterCommandByType($type) {
        $filterFn = create_function('$arr', 'return ($arr["type"] === "'. $type. '");');
        return array_filter($this->config, $filterFn);
    }
    
    public function getInfo($commandName=null) {
        return $this->filterCommandsByName($commandName);
    }
    
    public function filterCommandsByName($commandName=null) {

        if ($commandName !== null && $this->hasCommand($commandName)) {
            return $this->config[$commandName];
        }

        return $this->config;
    }
    
    public function getAll() {
        return $this->config;
    }
    
    public function hasCommand($commandName) {
        return array_key_exists($commandName, $this->config);
    }
}