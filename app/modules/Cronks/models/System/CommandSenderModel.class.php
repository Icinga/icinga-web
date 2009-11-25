<?php

class Cronks_System_CommandSenderModel extends ICINGACronksBaseModel
{
	
	const TIME_KEY				= 'V2Pxq9J2GVt1dk6OO0x3';
	const TIME_ALGO				= 'ripemd160';
	const TIME_VALID			= 5;	// Key is valid 5 minutes

	private $command_objects 	= array ();
	private $selection			= array ();
	private $data				= array ();
	private $command			= null;
	
	public function __construct($command = null) {
		if ($command !== null) {
			$this->setCommandName($command);
		}
	}
	
	public function setCommandName($command) {
		$this->command = $command;
	}
	
	public function setSelection(array $selection) {
		$this->selection = (array)$selection;
	}
	
	public function setData(stdClass &$data) {
		$this->data = (array)$data;
	}
	
	public function buildCommandObjects() {
		foreach ($this->selection as $sel) {
			$item = array ();
			$item = array_merge((array)$sel, $this->data);
			
			$this->command_objects[] = $this->createSingleCommandObject($this->command, $item);
		}
		
		return true;
	}
	
	private function createSingleCommandObject($command_name, array $data) {
		$cmd = IcingaApi::getCommandObject();
		$cmd->setCommand($command_name);
		
		foreach ($data as $name=>$value) {
			$cmd->setTarget($name, $value);
		}
		
		return $cmd;
	}
	
	/**
	 * Generate a time key
	 * @return string
	 */
	public function genTimeKey() {
		$data = strftime('%Y-%d-%H-').  (date('i') - (date('i') % self::TIME_VALID));
		return hash_hmac(self::TIME_ALGO, $data, self::TIME_KEY);
	}
	
	/**
	 * Check the auth agains the input data and the key
	 * @param string $command
	 * @param string $json_selection
	 * @param string $key
	 * @return boolean
	 */
	public function checkAuth($command, $json_selection, $key) {
		$data = $command. '-'. $json_selection;
		$test = hash_hmac(self::TIME_ALGO, $data, $this->genTimeKey());
		
		if ($key === $test) {
			return true;
		}
		
		return false;
	}
	
	
	
}

?>