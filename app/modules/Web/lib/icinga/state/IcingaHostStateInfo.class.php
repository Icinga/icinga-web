<?php

class IcingaHostStateInfo extends IcingaStateInfo {
	
	protected $state_list = array (
		0	=> 'OK',
		1	=> 'DOWN',
		2	=> 'UNREACHABLE',
		3	=> 'UNKNOWN',
	);
	
	/**
	 * 
	 * @param mixed $type
	 * @return IcingaHostStateInfo
	 */
	public function Create($type) {
		$class = __CLASS__;
		return new $class($type);
	}
	
}

?>