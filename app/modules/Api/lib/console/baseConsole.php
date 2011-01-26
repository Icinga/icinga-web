<?php
class ApiRestrictedCommandException extends AppKitException {};

abstract class baseConsole {
//	abstract public function getProcessPipes();
	abstract public function exec($cmd,$args,$stdout = null, $stderr = null);
	
	protected function getSecureCommand($cmd,array $args = array())  {
		$cmd = escapeshellcmd($cmd);
		$argString = $cmd .= " ";
		foreach($args as $name => &$arg) {
			$arg = escapeshellarg($arg);
			$argString .= escapeshellcmd($name)." ".$arg;			
		}	
		return $argString;
	}

	protected function isAllowedCommand($cmd) {
		
	}
}