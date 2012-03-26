<?php
/**
 * Provides a bugfix for icinga-web versions <= 1.6.2
 */
class  LConf_ConsoleCommandModel extends Api_Console_ConsoleCommandModel {
     protected function validateCommand() {

        $command = $this->getCommand();
        $cmdBase = preg_replace("/([^\\\]) .*/","$1",$command);

        if(!AccessConfig::canExecute($command,$this->host) && !AccessConfig::canExecute($cmdBase,$this->host))
            throw new ApiRestrictedCommandException($command." is not allowed");
    }
}