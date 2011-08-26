<?php
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
    protected static $xmlLoaded = false;
    public function setConsoleContext(IcingaConsoleInterface $model) {
        $this->consoleContext = $model;
    }

    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        if (isset($parameters["console"]) && $parameters["console"] instanceof IcingaConsoleInterface) {
            $this->setConsoleContext($parameters["console"]);
        }

        parent::initialize($ctx,$parameters);
        $this->loadConfig();
    }

    public function submitCommand($cmd_name,array $params,
                                  $commandClass = array("Console.ConsoleCommand","Api")) {
        $command = $this->getCommand($cmd_name);
        $string = $this->buildCommandString($command,$params);
        $cmd = $this->getContext()->getModel($commandClass[0],$commandClass[1],
                                             array(
                                                     "command" => "printf",
                                                     "connection" => $this->consoleContext,
                                                     "arguments" => array($string)
                                             )
                                            );
        $cmd->stdoutFile("icinga_pipe");

        try {
            $this->consoleContext->exec($cmd);
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
            if (!isset($params[$vals["alias"]]) && $vals["required"]) {
                throw new MissingCommandParameterException($vals["alias"]." is missing");
            } else if (!isset($params[$vals["alias"]])) {
                $str .= ";";
            } else {
                $val = $params[$vals["alias"]];

                switch ($vals["type"]) {
                    case "date":
                        $val = strtotime($val);
                        break;

                }

                $str .= ";".$val;
            }
        }
        return $str;
    }

    public function getCommands() {
        return $this->config;
    }

    public function getCommand($name) {

        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else {
            throw new UnknownIcingaCommandException("Command $name is undefined");
        }
    }

    protected function loadConfig() {
        $this->config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/icingaCommands.xml'));
    }

}
