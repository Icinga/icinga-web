<?php

class LocalConsoleConnection extends BaseConsoleConnection {

    public function exec(Api_Console_ConsoleCommandModel $cmd) {
        $cmdString = $cmd->getCommandString();
        $this->checkFileExistence($cmd);
        
        exec($cmdString,$out,$ret);
        $cmd->setReturnCode($ret);
        $cmd->setOutput($out);
    }

    protected function checkFileExistence(Api_Console_ConsoleCommandModel $cmd) {
        if ($cmd->getStdin()) {
            if (!file_exists($cmd->getStdin())) {
                throw new AppKitException("File ".$cmd->getStdin()." does not exist");
            }
        }

        if ($cmd->getPipedCmd()) {
            $this->checkFileExistence($cmd->getPipedCmd());
        }
    }

    public function __construct(array $settings = array()) {
        // No setup needed
    }

}
