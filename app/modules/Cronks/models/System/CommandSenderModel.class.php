<?php

class Cronks_System_CommandSenderModel extends CronksBaseModel {
	
	const TIME_KEY				= 'V2Pxq9J2GVt1dk6OO0x3'; // Please change this if you need more security!
	const TIME_ALGO				= 'ripemd160';	// Please never change this!!!
	const TIME_VALID			= 5;	// Key is valid 5 minutes

	private $selection			= array ();
	private $data				= array ();
	private $command			= null;

	public function  initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
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
		
		$co = array ();
		
		foreach ($this->selection as $sel) {
			$item = array ();
			$item = array_merge((array)$sel, $this->data);
			
			$co[] = $this->createSingleCommandObject($this->command, $item);
		}
		
		return $co;
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
		$data .= '-'. $this->getContext()->getUser()->getNsmUser()->user_id;
		$data .= '-'. session_id();
		
		// OK, pid is too hard ;-)
		// $data .= '-'. getmypid();
		
		// var_dump($data);
		
		return hash_hmac(self::TIME_ALGO, $data, self::TIME_KEY);
	}
	
	/**
	 * Check the auth agains the input data and the key
	 * @param string $command
	 * @param string $json_selection
	 * @param string $key
	 * @return boolean
	 */
	public function checkAuth($command, $json_selection, $json_data, $key) {
		$data = $command. '-'. $json_selection. '-'. $json_data;
		$test = hash_hmac(self::TIME_ALGO, $data, $this->genTimeKey());
		
		// var_dump(array($test, $key));
		
		if ($key === $test) {
			return true;
		}
		
		return false;
	}
	
	
	
}

?>