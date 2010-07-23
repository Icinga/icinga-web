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

	protected $colors = array (
		IcingaConstants::HOST_UP			=> '00cc00',
		IcingaConstants::HOST_DOWN			=> 'cc0000',
		IcingaConstants::HOST_UNREACHABLE	=> 'ff8000'
	);



	/**
	 * Shortcut to create an object instance on the fly
	 * 
	 * @param mixed $type
	 * @return IcingaHostStateInfo
	 */
	public function Create($type=99) {
		$class = __CLASS__;
		return new $class($type);
	}
	
}

?>