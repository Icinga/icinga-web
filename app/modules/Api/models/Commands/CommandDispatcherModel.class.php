<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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
* CommandDispatcherModel
* Handles creation and submitting of commands to the icinga pipe
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class CommandDispatcherException extends AppKitException {}
class UnknownIcingaCommandException extends CommandDispatcherException {}
class MissingCommandParameterException extends CommandDispatcherException {}
class Api_Commands_CommandDispatcherModel extends IcingaApiBaseModel implements AgaviISingletonModel {
    protected $consoleContext = null;

    protected $config = null;

    /**
     * @var Api_Commands_CommandInfoModel
     */
    protected $commandInfoModel = null;

    public function setConsoleContext(IcingaConsoleInterface $model) {
        $this->consoleContext = $model;
    }

    public function initialize(AgaviContext $ctx, array $parameters = array()) {

        if (isset($parameters["console"]) && $parameters["console"] instanceof IcingaConsoleInterface) {
            $this->setConsoleContext($parameters["console"]);
        }

        parent::initialize($ctx,$parameters);

        $this->commandInfoModel = $ctx->getModel('Commands.CommandInfo', 'Api');
    }

    public function submitCommand($cmd_name,array $params,
                                  $commandClass = array("Console.ConsoleCommand","Api")) {

        try {
            $user = $this->getContext()->getUser()->getNsmUser();
            $onlySimple = $user->hasTarget('IcingaCommandRestrictions');

            $command = $this->getCommand($cmd_name);

            $string = $this->buildCommandString($command,$params);

            if($onlySimple && !$command["isSimple"])
                throw new Exception("Could not send command. Your user isn't allowed to send this command.");

            $this->context->getLoggerManager()->log(
                sprintf('(%s) %s', $user->user_name, $string),
                AppKitLogger::COMMAND
            );

            AppKitLogger::debug("Sending icinga-command %s",$string);
            $cmd = $this->getContext()->getModel($commandClass[0],$commandClass[1],
                                                 array(
                                                         "command" => "printf",
                                                         "arguments" => array($string)
                                                 )
                                                );
            $cmd->stdoutFile("icinga_pipe");

            ($this->consoleContext->exec($cmd));
            if($cmd->getReturnCode() != '0')
                throw new Exception("Could not send command. Check if your webserver's user has correct permissions for writing to the command pipe.");
        } catch (Exception $e) {

           $this->context->getLoggerManager()->log("Sending command failed ".$e->getMessage() );
           throw $e;

        }
    }

    private function buildCommandString(array $command, array $params) {
        $str = "[".time()."] ".$command["definition"];

        foreach($command["parameters"] as $param=>$vals) {
            if(!isset($vals["required"]))
                $vals["required"] = true;

            /*
             * Use default values if any
             */
            if (!isset($params[$vals["alias"]]) && $vals["required"] && array_key_exists('defaultValue', $vals)) {
                $params[$vals["alias"]] = $vals['defaultValue'];
            }

            if (!isset($params[$vals["alias"]]) && $vals["required"]) {
                throw new MissingCommandParameterException($vals["alias"]." is missing");
            } else if (!isset($params[$vals["alias"]])) {
                $str .= ";";
            } else {
                $val = ($params[$vals["alias"]]);

                /*
                 * Sanitize data to not break the whole command chain
                 */
                if (is_array($val) == true) {
                    $val = '';
                } else {
                    $val = preg_replace("/\n/"," ",$val);

                    switch ($vals["type"]) {
                        case "date":
                            $val = strtotime($val);
                            break;
                    }
                }

                // Perfdata is a a special case that requires | instead of ;
                if($param != "COMMAND_PERFDATA") {
                    $str .= ";".$val;
                } else {
                    if(trim($val) != "")
                        $str .= "|".$val;
                }
            }

        }
        return $str;
    }

    public function getCommands() {
        return $this->commandInfoModel->getInfo();
    }

    public function getCommand($name) {

        if ($this->commandInfoModel->hasCommand($name)) {
            return $this->commandInfoModel->getInfo($name);
        } else {
            throw new UnknownIcingaCommandException("Command $name is undefined");
        }
    }

}
