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
