<?php
class ApiSSHNotInstalledException extends AppKitException {};
class ApiInvalidAuthTypeException extends AppKitException {};
class ApiCommandFailedException extends AppKitException {};

class SshConsoleConnection extends BaseConsoleConnection {	
	private $connected = false;
	private $host = null;
	private $port = 22;
	private $authType = "password";
	private $pubKeyLocation = "";
	private $privKeyLocation = "";
	private $password = null;
	private $username;
	private $resource;
	private $terminal;
	protected $stdout;
	protected $stderr;

	public function isConnected() {
		return $connected;
	}
	
	public function connect() {
		if($this->connected)
			return true;
		$success = false;
		$this->resource = ssh2_connect($this->host,$this->port, null);
		switch($this->authType) {
			case 'none':
				$success = ssh2_auth_none($this->resource,$this->username);
				break;
			case 'password':	
				$success = ssh2_auth_password($this->resource,$this->username,$this->password);
				break;
			case 'key':
				$success = ssh2_auth_key($this->resource,$this->username,$this->pubKeyLocation,$this->privKeyLocation,$this->password);
				break;
			default:
				throw new ApiInvalidAuthTypeException("Unknown authtype ".$this->authType);
		}
		if(!$success || !is_resource($this->resource))
			throw new ApiAuthorisationFailedException("SSH auth for user ".$this->username." failed (using authtype ".$this->authType);
		$this->connected = true;
		$this->terminal = ssh2_shell($this->resource);
		$this->stdout = ssh2_fetch_stream($this->terminal,SSH2_STREAM_STDIO);
		$this->stderr = ssh2_fetch_stream($this->terminal,SSH2_STREAM_STDERR);
	

	}
	
	public function onDisconnect($reason,$message,$language) {
		$this->connected = false;
	}
	
	/**
	*	Blocking doesn't quite work with ssh2, so this rather ugly method is used to read 
	*	console output. Read is stopped when "username@host:" is reached
	**/
	private function readUntilFinished($cmdString) {
		$buffer = "";
		$out = "";
		$timeout = 5;
		$start = time();
		while(true) { 
			$buffer = fgets($this->stdout,2048);	
			if(trim($buffer) == trim($cmdString))
				continue;
			if(preg_match('/'.$this->username.'@\w*?:/',$buffer))
				break;
			$out .= $buffer;
			$current = time();
			if($current-$start > $timeout) {	
				break;	
			}
			if(!$buffer)
				usleep(40000);
		}
		return $out;
	}

	public function exec(Api_Console_ConsoleCommandModel $cmd) {	
		$this->connect();
		$cmdString = $cmd->getCommandString();
		$r = array($this->stdout,$this->stderr);
		$w = null;
		$e = null;
		$out = "";
		// Discard content in buffer
		$this->readUntilFinished($cmdString);
		fwrite($this->terminal, $cmdString.PHP_EOL);	
		$out = $this->readUntilFinished($cmdString);	
		$cmd->setOutput($out);		
		$cmd->setReturnCode($this->getLastReturnValue());	
	}

	public function getLastReturnValue() {
		fwrite($this->terminal, "echo $?".PHP_EOL);
		$retVal = $this->readUntilFinished("echo $?");		
		return $retVal;
	}
	public function __construct(array $settings = array()) {
		$settings = $settings["ssh"];
		$this->checkSSH2Support();
		$this->host = $settings["host"];
		$this->port = $settings["port"];
		$this->authType = $settings["auth"]["type"];
		$this->setupAuth($settings["auth"]);
	}

	protected function setupAuth(array $settings) {
		switch($this->authType) {
			case 'none':
				$this->username = $settings["user"];
				break;
			case 'password':
				$this->password = $settings["password"];
				$this->username = $settings["user"];
				break;
			case 'key':
				if(isset($settings["password"]))
					$this->password = $settings["password"];
				$this->username = $settings["user"];
				$this->pubKey = $settings["pubKey"];
				$this->privKey = $settings["privKey"];
				break;
			default:
				throw new ApiInvalidAuthTypeExcpetion("Unknown auth type ".$this->authType);
		}
	}

	protected function checkSSH2Support() {
		if(!extension_loaded('ssh2'))
			throw new ApiSSHNotInstalledException("SSH support is not enabled, install the php ssh2 module in order to use SSH");
	}

}
