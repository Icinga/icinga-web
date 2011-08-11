<?php

interface IcingaConsoleInterface {
    public function getHostName();
    public function getAccessDefinition();
    public function exec(IcingaConsoleCommandInterface $cmd);

}
