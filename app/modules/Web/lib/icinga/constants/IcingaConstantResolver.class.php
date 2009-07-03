<?php

/**
 * Static class to resolve cryptic nagios constant values into
 * human readable information
 * @author mhein
 *
 */
class IcingaConstantResolver {
	
	const TEXT_NOT_FOUND	= '(null)';
	
	static private $state_type = array (
		0	=> 'SOFT',
		1	=> 'HARD',
	);
	
	static private $active_check_type = array (
		0	=> 'PASSIVE',
		1	=> 'ACTIVE'
	);
	
	static private $boolean_states = array (
		0	=> 'No',
		1	=> 'Yes',
	);
	
	/**
	 * Generic resolver, change an integer into an array key
	 * @param array $input
	 * @param integer $key
	 * @return string
	 */
	protected static function resolveArrayConstants(array &$input, $key) {
		if (array_key_exists($key, $input)) return $input[$key];
		return self::TEXT_NOT_FOUND;
	}
	
	/**
	 * Changes state types into names
	 * @param integer $integer
	 * @return string
	 */
	public static function stateType($integer) {
		return self::resolveArrayConstants(self::$state_type, $integer);
	}
	
	/**
	 * Converts is_active boolean values into an text check type
	 * @param integer $integer
	 * @return string
	 */
	public static function activeCheckType($integer) {
		return self::resolveArrayConstants(self::$active_check_type, $integer);
	}
	
	/**
	 * Converts boolean states into yes/no
	 * @param boolean $boolean
	 * @return string
	 */
	public static function booleanNames($boolean) {
		$boolean ? $boolean = 1 : $boolean = 0;
		return self::resolveArrayConstants(self::$boolean_states, $boolean);
	} 
	
}

?>