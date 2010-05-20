<?php

class IcingaHostStateInfo extends IcingaStateInfo {
	
	/**
	 * List of status id's with corresponding
	 * status names
	 * 
	 * @var array
	 */
	protected $state_list = array (
		IcingaConstants::HOST_UP			=> 'UP',
		IcingaConstants::HOST_DOWN			=> 'DOWN',
		IcingaConstants::HOST_UNREACHABLE	=> 'UNREACHABLE'
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