<?php
/**
 * Decorator for consoleinterfaces, pust sudo su -U %user%  in front of a command
 * execute via LocalConsoleInterface
 */
class __InternalSudoConsoleInterfaceDecoratorModel extends LocalConsoleConnection {
    private $decorates = null;
    private $sudoUser = null;

    public function exec(Api_Console_ConsoleCommandModel $cmd) {
        if($this->decorates == null)
            throw new AppKitException("Call to SudoConsoleInterfaceDecorator::decorate missing");

         if($this->decorates instanceof LocalConsoleConnection) {
            $cmdString = $cmd->getCommandString();
            if($this->sudoUser)
                $cmdString = "sudo -u ".$this->sudoUser." ".$cmdString;

            $this->checkFileExistence($cmd);
            
            exec($cmdString,$out,$ret);
            $cmd->setReturnCode($ret);
            $cmd->setOutput($out);
         } else {
             return $this->decorates->exec($cmd);
         }
    }

    public function __construct(array $settings = array()) {
    }

    public function decorate(BaseConsoleConnection $c) {
        $this->decorates = $c;
    }

    public function setSudoUser($user) {
        if($user)
            $this->sudoUser = escapeshellarg($user);
    }
}


class LConf_SudoConsoleInterfaceDecoratorModel extends Api_Console_ConsoleInterfaceModel {
     private $sudoUser = null;
     
    protected function setUpConsole() {
        parent::setUpConsole();
        $connections = array();
        foreach($this->connections as $name=>$connection) {
            $conn = new __InternalSudoConsoleInterfaceDecoratorModel();
            $conn->setSudoUser($this->sudoUser);
            $conn->decorate($connection);
            $connections[$name] = $conn;
        }
        $this->connections = $connections;
    }

     public function setSudoUser($user) {
        $this->sudoUser = $user;
        foreach($this->connections as $name=>$connection) {
             $connection->setSudoUser($this->sudoUser);
        }
    }
}