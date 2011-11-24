<?php
class Api_Console_ConsoleCommandModel extends IcingaApiBaseModel implements IcingaConsoleCommandInterface {
    protected $command;
    protected $arguments = array();
    /**
     * @property Api_Console_ConsoleInterfaceModel
     */
    protected $connection;
    protected $pipeCmd = null;
    protected $symList = array();
    protected $stdout = null;
    protected $append_stdout = false;
    protected $stderr = null;
    protected $append_stderr = false;
    protected $stdin = null;
    protected $host;
    protected $output = "";
    protected $returnCode = 0;

    public function setCommand($cmd) {
        $this->command = escapeshellcmd($cmd);
    }

    public function addArgument($value, $key=null) {
        if ($key) {
            $this->arguments[$key] = $value;
        } else {
            $this->arguments[] = $value;
        }
    }

    public function stdinFile($file = null) {
        $this->stdin = escapeshellcmd($file);
    }
    public function stdoutFile($file = null,$append = false) {
        $this->stdout = escapeshellcmd($file);
        $this->append_stdout = $append;
    }
    public function stderrFile($file = null,$append = false) {
        $this->stderr = escapeshellcmd($file);
        $this->append_stderr = $append;
    }
    public function pipeCmd(IcingaConsoleCommandInterface $cmd = null) {
        $this->pipeCmd = $cmd;
    }
    public function setOutput($string) {
        $this->output = $string;
    }
    public function setReturnCode($code) {
        $this->returnCode = $code;
    }
    public function setConnection($conn) {
        $this->connection = $conn;
    }
    public function setHost($host) {
        $this->host = $host;
        $this->expandSymbols();
    }
    public function getHost() {
        return $this->host;
    }
    public function getStdin() {
        return $this->stdin;
    }
    public function getStderr() {
        return $this->stderr;
    }
    public function getStdout() {
        return $this->stdout;
    }
    public function getPipedCmd() {
        return $this->pipeCmd;
    }

    public function getCommand() {
        return $this->command;
    }
    public function getArguments() {
        return $this->arguments;
    }
    /**
    * @deprecated
    **/
    public function getConnection() {
        return $this->connection;
    }
    public function getOutput() {
        if (is_array($this->output)) {
            $this->output = implode("\n",$this->output);
        }

        return $this->output;
    }
    public function getReturnCode()	{
        return intval($this->returnCode);
    }

    public function initialize(AgaviContext $context, array $parameters = array()) {

        if (isset($parameters["command"])) {
            $this->setCommand($parameters["command"]);
        }
        
        if (isset($parameters["connection"])) {
            $this->setHost($parameters["connection"]->getHostName());
            $this->setConnection($parameters["connection"]);
        }


        if (isset($parameters["arguments"])) {
            $this->arguments = $parameters["arguments"];
        }
    }

    public function getCommandString() {
        $this->isValid(true);
        $cmd = $this->command;
        foreach($this->arguments as $name => $arg) {
            if (!is_int($name)) {
                $cmd .= ' '.escapeshellcmd($name);

                if ($name[strlen($name)-1] != '=') {
                    $cmd .= ' ';
                }
            }

            if ($arg != '') {
                $cmd .= ' '.escapeshellarg($arg);
            }
        }

        if ($this->stderr) {
            $cmd .= ' 2'.($this->append_stderr ? '>> ' : '> ').escapeshellcmd($this->stderr);
        }

        if ($this->stdout) {
            $cmd .= ' '.($this->append_stdout ? ' >> ' : ' > ').escapeshellcmd($this->stdout);
        }

        if ($this->stdin) {
            $cmd .= ' < '.escapeshellcmd($this->stdin);
        }

        if ($this->pipeCmd instanceof Api_Console_ConsoleCommandModel) {
            $cmd .= ' | '.$this->pipeCmd->getCommandString();
        }

        return $cmd;
    }

    public function isValid($throwOnError = false, &$err = null) {
        try {
            $this->expandSymbols();

            if ($this->command == null) {
                throw new AppKitException("No command specified");
            }

            if ($this->connection == null) {
                throw new AppKitException("No connection specified");
            }

            $this->validateCommand();
            $this->validateStdin();
            $this->validateStdout();
            $this->validateStderr();
            return true;
        } catch (ApiRestrictedCommandException $e) {
            if ($throwOnError) {
                throw new ApiRestrictedCommandException($e->getMessage());
            }

            $err = $e->getMessage();
            return false;
        }
    }

    protected function expandSymbols() {

        $this->stdinFile(AccessConfig::expandSymbol($this->stdin,"r",$this->host));
        $this->stdoutFile(AccessConfig::expandSymbol($this->stdout,"w",$this->host));
        $this->stderrFile(AccessConfig::expandSymbol($this->stderr,"w",$this->host));
        $this->setCommand(AccessConfig::expandSymbol($this->command,"x",$this->host));
        $matches = array();
        // replace %%VAL%% symbols in the argument list
        foreach($this->arguments as $key=>&$val) {
            $found = preg_match('/%%(\w+)%%/',$val,$matches);

            if ($found == 0) {
                continue;
            }

            foreach($matches as $match) {
                if (!isset($this->symList[$match])) {
                    continue;
                }

                $val = str_replace("%%".$match."%%",AccessConfig::expandSymbol($match,"rw",$this->host),$val);
            }
        }
    }


    protected function validateCommand() {
       
        $command = $this->getCommand();
        if(!AccessConfig::canExecute($command,$this->host)) 
            throw new ApiRestrictedCommandException($command." is not allowed");
    }

    protected function validateStdin() {
        $inFile = $this->stdin;

        if (!$inFile) {
            return true;
        }

        if(!AccessConfig::canRead($inFile,$this->host))
            throw new ApiRestrictedCommandException($inFile." is not read enabled");
    }

    protected function validateStdout() {
        $outFile = $this->stdout;

        if (!$outFile) {
            return true;
        }

        if(!AccessConfig::canWrite($outFile,$this->host))
            throw new ApiRestrictedCommandException($outFile." is not write enabled");
    }

    protected function validateStderr() {
        $errFile = $this->stdout;

        if (!$errFile) {
            return true;
        }

        if(!AccessConfig::canWrite($errFile,$this->host))
            throw new ApiRestrictedCommandException($errFile." is not read enabled");
    }
}
