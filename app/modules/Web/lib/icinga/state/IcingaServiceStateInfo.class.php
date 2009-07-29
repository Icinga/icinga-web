<?php

class IcingaServiceStateInfo extends IcingaStateInfo {
	
	/**
	 * List of status id's with corresponding
	 * status names
	 * 
	 * @var array
	 */
	protected $state_list = array (
		0	=> 'OK',
		1	=> 'WARNING',
		2	=> 'CRITICAL',
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