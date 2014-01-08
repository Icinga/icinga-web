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


/**
 * Providing information about icinga commands to core
 * @author mhein
 *
 */
class Cronks_System_CommandInfoModel extends CronksBaseModel
    implements AgaviISingletonModel {

    /**
     * @var IcingaApiCommandCollection
     */
    private $commandDispatcher = null;

    public function  initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->commandDispatcher = $context->getModel("Commands.CommandDispatcher","Api");

    }

    /**
     * Returns a json parsable structure of a command
     * @param string $name
     * @return array
     */
    public function getCommandInfo($name) {
        $cmd = $this->commandDispatcher->getCommand($name);
        $result = array(
                      "fields"=>array(),
                      "types" => array(),
                      "tk" => $this->getContext()->getModel("System.CommandSender","Cronks")->genTimeKey()
                  );
        $cmd = $this->commandDispatcher->getCommand($name);
        foreach($cmd["parameters"] as $field) {
            $name = $field["alias"];
            $result["fields"][] = $name;
            $result["types"][$name] = $field;
        }
        return $result;
    }

}
