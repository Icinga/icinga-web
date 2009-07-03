<?php

class IcingaHostStateInfo extends IcingaStateInfo {
	
	/**
	 * List of status id's with corresponding
	 * status names
	 * 
	 * @var array
	 */
	protected $state_list = array (
		0	=> 'UP',
		1	=> 'DOWN',
		2	=> 'UNREACHABLE',
		3	=> 'UNKNOWN',
	);
	
	/**
	 * Shortcut to create an object instance on the fly
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