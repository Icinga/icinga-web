<?php
class Api_Console_ConsoleCommandModel extends IcingaApiBaseModel {
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
    public function pipeCmd(Api_Console_ConsoleCommandModel $cmd = null) {
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
        if (empty($this->symList)) {
            $this->createSymbolList();
        }

        $access = $this->connection->getAccessDefinition();
        $this->stdinFile($this->getFullName($this->stdin,$access["r"]["files"]));
        $this->stdoutFile($this->getFullName($this->stdout,$access["w"]["files"]));
        $this->stderrFile($this->getFullName($this->stderr,$access["w"]["files"]));
        $this->setCommand($this->getFullName($this->command,$access["x"]["files"]));
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

                $val = str_replace("%%".$match."%%",$this->symList[$match],$val);
            }
        }
    }

    protected function createSymbolList() {
        $this->symList = array();
        foreach($this->connection->getAccessDefinition() as $access=>$content) {
            foreach($content as $fileOrFolder=>$symdefs) {
                foreach($symdefs as $symname=>$resolved) {
                    $this->symList[$symname] = $resolved;
                }
            }
        }
    }

    public function getFullName($symbol = null,array $whiteList = array()) {
        if ($symbol == null) {
            return null;
        }

        foreach($whiteList as $sym=>$name) {
            if (!$sym) {
                continue;
            }

            if ($sym == $symbol) {
                return $name;
            }
        }
        return $symbol;
    }

    protected function validateCommand() {
        $access = $this->connection->getAccessDefinition();
        $command = $this->getCommand();
        foreach($access["x"]["folders"] as $exec) {
            if (trim(escapeshellcmd($exec)) == trim(dirname($command))) {
                return true;
            }
        }
        foreach($access["x"]["files"] as $exec) {
            if (trim(escapeshellcmd($exec)) == trim($command)) {
                return true;
            }
        }

        throw new ApiRestrictedCommandException($command." is not allowed");
    }

    protected function validateStdin() {
        $inFile = $this->stdin;

        if (!$inFile) {
            return true;
        }

        $access = $this->connection->getAccessDefinition();
        foreach($access["r"]["folders"] as $read) {

            if (trim(escapeshellcmd($read)) == trim(dirname($inFile))) {
                return true;
            }
        }
        foreach($access["r"]["files"] as $sym=>$read) {
            if (trim(escapeshellcmd($read)) == trim($inFile)) {
                return true;
            }
        }
        throw new ApiRestrictedCommandException($inFile." is not read enabled");
    }

    protected function validateStdout() {
        $outFile = $this->stdout;

        if (!$outFile) {
            return true;
        }

        $access = $this->connection->getAccessDefinition();
        foreach($access["w"]["folders"] as $write) {
            if (trim(escapeshellcmd($write)) == trim(dirname($outFile))) {
                return true;
            }
        }
        foreach($access["w"]["files"] as $write) {
            if (trim(escapeshellcmd($write)) == trim($outFile)) {
                return true;
            }
        }
        throw new ApiRestrictedCommandException($outFile." is not write enabled");
    }

    protected function validateStderr() {
        $errFile = $this->stdout;

        if (!$errFile) {
            return true;
        }

        $access = $this->connection->getAccessDefinition();
        foreach($access["w"]["folders"] as $write) {
            if (trim(escapeshellcmd($write)) == trim(dirname($errFile))) {
                return true;
            }
        }
        foreach($access["w"]["files"] as $write) {
            if (trim(escapeshellcmd($write)) == trim($errFile)) {
                return true;
            }
        }

        throw new ApiRestrictedCommandException($errFile." is not read enabled");
    }
}
