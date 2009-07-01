<?php

class IcingaStateInfo {
	
	const WRAP_LEFT		= '(';
	const WRAP_RIGHT	= ')';
	const UNKNOWN_TEXT	= 'NOT RECOGNIZED';
	
	private $current_state = null;
	
	private $state_list = array (
	
	);
	
	public function __construct($type) {
		if (is_int($type)) {
			$this->setStateById($type);
		}			
	}
	
	public function setStateById($id) {
		$this->current_state = $id;
		return true;
	}
	
	public function getCurrentState() {
		return $this->current_state;
	}
	
	private function getWrappedText($text) {
		return sprintf('%s%s%s', self::WRAP_LEFT, $text, self::WRAP_RIGHT);
	}
	
	private function getStateText($state) {
		if (array_key_exists($state, $this->state_list)) {
			return $this->state_list[$state];
		}
		return self::UNKNOWN_TEXT;
	}
	
}

?>