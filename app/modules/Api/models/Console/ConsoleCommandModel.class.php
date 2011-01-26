<?php
class Api_Console_ConsoleCommandModel extends IcingaApiBaseModel {
	protected $command;
	protected $arguments = array();
	/**
	 * @property Api_Console_ConsoleInterfaceModel 
	 */
	protected $connection;
	
	protected $pipeCmd = null;
	
	protected $stdout = null;
	protected $append_stdout = false;
	protected $stderr = null;
	protected $append_stderr = false;
	protected $stdin = null;

	public function setCommand($cmd) {
		$this->command = escapeshellcmd($cmd);
	}
	
	public function addArgument($value, $key=null) {
		if($key)
			$this->$arguments[$key] = $value;
		else
			$this->$arguments[] = $value;
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
	public function pipeCmd(Api_Console_ConsoleInterfaceModel $cmd = null) {
		$this->pipeCmd = $cmd;
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
	
	public function initialize($context, array $parameters = array()) {
		if(isset($parameters["command"]))
			$this->setCommand($parameters["command"]);
		if(isset($parameters["arguments"]))
			$this->arguments = $parameters["arguments"];	
		if(isset($parameters["connection"])) {
			// Don't care about autoloading, as this should be called by a connectionModel
			if($parameters["connection"] instanceof Api_Console_ConsoleInterfaceModel)
				$this->connection = $parameters["connection"];
		}
	}
	
	public function getCommandString() {
		$this->isValid(true);	
		$cmd = $this->$command;
		foreach($arguments as $name=>$arg) {
			if(is_nan($name)) {
				$cmd .= escapeshellcmd($name);
				if($name[strlen($name)-1] != '=')
					$cmd .= ' ';	
			}
			$cmd .= escapeshellarg($arg);	
		}
		if($this->stderr)
			$cmd .= " 2".($this->append_stderr ? '>> ' : '> ').escapeshellcmd($this->stderr);
		if($this->stdout)
			$cmd .= ($this->append_stdout ? ' >> ' : ' > ').escapeshellcmd($this->stdout);
		if($this->stdin)
			$cmd .= ' < '.escapeshellcmd($this->stdin);
		if($this->pipeCmd instanceof Api_Console_ConsoleInterfaceModel)
			$cmd .= $his->pipeCmd->getCommandString();
			
		return $cmd;
	}
	
	public function isValid($throwOnError = false, &$err = null) {
		try {
			if($this->command == null) 
				throw Exception("No command specified");
			if($this->connectio == null)
				throw Exception("No connection specified");

			$this->validateCommand($host);
			$this->validateStdin($host);				
			$this->validateStdout($host);				
			$this->validateStderr($host);				
			return true;
		} catch(Exception $e) {
			if($throwOnError)
				throw ApiRestrictedCommandException($e->getMessage());
			$err = $e->getMessage();
			return false;
		}
	}
	
	protected function validateCommand() {
		$access = $this->connection->getAccessDefinition();
		$command = $this->getCommand();
		foreach($access["x"] as $exec) {
			if(trim(escapeshellcmd($exec)) == trim($command))
				return true;
		}
		return false;
	}
	
	protected function validateStdin() {
		$inFile = $this->stdin;
		if(!$inFile)
			return true;
		$access = $this->connection->getAccessDefinition();
		foreach($access["r"] as $read) {
			if(trim(escapeshellcmd($read)) == trim($inFile))
				return true;
		}
		return false;
	}
	
	protected function validateStdout() {
		$outFile = $this->stdout;
		if(!$inFile)
			return true;
		$access = $this->connection->getAccessDefinition();
		foreach($access["w"] as $write) {
			if(trim(escapeshellcmd($write)) == trim($outFile))
				return true;
		}
		return false;
	}
	
	protected function validateStderr() {
		$errFile = $this->stdout;
		if(!$errFile)
			return true;
		$access = $this->connection->getAccessDefinition();
		foreach($access["w"] as $write) {
			if(trim(escapeshellcmd($write)) == trim($errFile))
				return true;
		}
		return false;
	}
	
}