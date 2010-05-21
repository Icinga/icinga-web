<?php

class IcingaServiceStateInfo extends IcingaStateInfo {
	
	/**
	 * List of status id's with corresponding
	 * status names
	 * 
	 * @var array
	 */
	protected $state_list = array (
		IcingaConstants::STATE_OK		=> 'OK',
		IcingaConstants::STATE_WARNING	=> 'WARNING',
		IcingaConstants::STATE_CRITICAL	=> 'CRITICAL',
		IcingaConstants::STATE_UNKNOWN	=> 'UNKNOWN',
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