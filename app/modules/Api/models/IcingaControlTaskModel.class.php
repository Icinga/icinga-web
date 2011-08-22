<?php
class InvalidIcingaCommandException extends AppKitException {};
class IcingaConfigValidationException extends AppKitException {};
class IcingaCommandSecurityExcpetion extends AppKitException {};
class Api_IcingaControlTaskModel extends AppKitBaseModel {
    private $host;
    private $availableCommands = array(
                                     "reload"=>	array("icinga.control.admin"),
                                     "restart"=> array("icinga.control.admin"),
                                     "validate" => array("icinga.control.admin"),
                                     "status" => array("icinga.control.admin","icinga.control.view")
                                 );
    private $validationError;

    public function getCli() {
        return AgaviContext::getInstance()->getModel("Console.ConsoleInterface","Api",array("host"=>$this->host));
    }

    public function initialize(AgaviContext $context, array $parameters = array()) {
        $host = isset($parameters["host"]) ? $parameters["host"] : AccessConfig::getDefaultHostname();
        $this->host = AccessConfig::getHostByName($host);
    }

    public function getValidationError() {
        return $this->validationError;
    }

    public function checkAccess($cmd) {
        if (!isset($this->availableCommands[$cmd])) {
            throw new InvalidIcingaCommandException("Command $cmd is not available");
        }

        $user = AgaviContext::getInstance()->getUser();
        $allowed = false;
        foreach($this->availableCommands[$cmd] as $credential) {
            if ($user->hasCredential($credential)) {
                $allowed = true;
                break;
            }
        }
        return $allowed;
    }

    private function execIcingaCmd($type) {
        $cli = $this->getCli();
        $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                       'command' => 'icinga_service'
                   ));
        $command->addArgument($type);
        $cli->exec($command);
        return $command;
    }

    public function validateConfig($cfg = NULL) {
        if (!$this->checkAccess("validate")) {
            throw new IcingaCommandSecurityException("Invalid credentials for config validation");
        }

        $cfg = $cfg ? $cfg : "%%icinga_cfg%%";

        $cli = $this->getCli();
        $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                       'command' => 'icinga_bin',
                   ));
        $command->addArgument($cfg,"-v");

        $cli->exec($command);

        if ($command->getReturnCode() != 0) {
            $this->validationError = $command->getOutput();
        }

        return $command->getReturnCode();
    }

    public function reloadIcinga() {
        if (!$this->checkAccess("reload")) {
            throw new IcingaCommandSecurityException("Invalid credentials for icinga reload");
        }

        if ($this->validateConfig() != 0) {
            throw new IcingaConfigValidationException($this->getValidationError());
        }

        $command = $this->execIcingaCmd('reload');
        return $command->getReturnCode();
    }

    public function stopIcinga() {
        if (!$this->checkAccess("restart")) {
            throw new IcingaCommandSecurityException("Invalid credentials for icinga restart");
        }

        if ($this->validateConfig() != 0) {
            throw new IcingaConfigValidationException($this->getValidationError());
        }

        $cli = $this->getCli();
        $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                       'command' => 'printf'
                   ));
        $command->stdoutFile("icinga_pipe");
        $command->addArgument("[".time()."] SHUTDOWN_PROGRAM");
        $cli->exec($command);

        return $command->getReturnCode();
    }

    public function restartIcinga() {
        if (!$this->checkAccess("restart")) {
            throw new IcingaCommandSecurityException("Invalid credentials for icinga restart");
        }

        if ($this->validateConfig() != 0) {
            throw new IcingaConfigValidationException($this->getValidationError());
        }

        $status = $this->getIcingaStatus();
        $cli = $this->getCli();
        $command;

        if ($status == 0) {
            $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                           'command' => 'printf'
                       ));
            $command->stdoutFile("icinga_pipe");
            $command->addArgument("[".time()."] RESTART_PROGRAM");
            $command->setConnection($cli);

            $cli->exec($command);
        } else { // output.cmd won't be read, use service
            $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                           'command' => 'icinga_service'
                       ));

            $command->addArgument('restart');
            $command->setConnection($cli);

            $cli->exec($command);

        }

        return $command->getReturnCode();
    }

    public function getIcingaStatus() {
        if (!$this->checkAccess("status")) {
            throw new IcingaCommandSecurityException("Invalid credentials for icinga restart");
        }

        $cli = $this->getCli();
        $command = AgaviContext::getInstance()->getModel("Console.ConsoleCommand","Api",array(
                       'command' => 'icinga_service'
                   ));
        $command->addArgument("status");
        $cli->exec($command);

        return $command->getReturnCode();
    }

}

?>
