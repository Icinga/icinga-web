<?php

interface IcingaConsoleInterface {
    public function getHostName();

    public function exec(IcingaConsoleCommandInterface $cmd);

}
