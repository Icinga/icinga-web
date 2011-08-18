<?php
/**
* Interface for Console Commands
* TODO: Currently this holds the command as well as it's current state,
*       this could be outsourced to a commandresult class/interface
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
interface IcingaConsoleCommandInterface {
    public function setCommand($cmd);
    public function addArgument($value, $key=null);
    public function stdinFile($file = null);
    public function stdoutFile($file = null,$append = false);
    public function stderrFile($file = null,$append = false);
    public function pipeCmd(IcingaConsoleCommandInterface $cmd = null);
    public function setOutput($string);
    public function setReturnCode($code);
    public function setConnection($conn);
    public function setHost($host);
    public function getHost();
    public function getStdin();
    public function getStderr();
    public function getStdout();
    public function getPipedCmd();
    public function getCommand();
    public function getArguments();
    public function getConnection();
    public function getOutput();
    public function getReturnCode()	;
    public function initialize(AgaviContext $context, array $parameters = array());
    public function getCommandString();
    public function isValid($throwOnError = false, &$err = null);




}
