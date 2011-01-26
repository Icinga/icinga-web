<?php

class localConsole extends baseConsole {
	protected $openProcs = array();
	
/*	protected function getConsolePipes() {
		$desc = array(
			"pipe" => "r",
			"pipe" => "w",
			"pipe" => "w"
		);
		proc_open("sh",$desc,$pipes);
		return $pipes;
	}
*/
	
	
	public function exec($cmd,array $args = array(),&$output = NULL) {
		if(!$this->isAllowedCommand($cmd)) 
			throw new ApiRestrictedCommandException("Command ".$cmd." is not allowed on this host");
		return exec($this->getSecureCommand($cmd),$output, $returnval);
	}
	
	
	public function __destruct() {
		$status;
		foreach($openProcs as $proc) {
			$status = proc_get_status($proc);
			if(!is_array($status))
				continue;
			if(!isset($status["running"]))
				continue;
			if($status["running"] == true)
				proc_close($proc);
		}
	}
}
