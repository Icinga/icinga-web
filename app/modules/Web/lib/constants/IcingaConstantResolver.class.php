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
	
	static private $notification_types = array (
		0	=> 'Host',
		1	=> 'Service'
	);
	
	static private $logentry_types = array (
		IcingaConstants::NSLOG_RUNTIME_ERROR			=> "runtime error",
		IcingaConstants::NSLOG_RUNTIME_WARNING			=> "runtime warning",
		IcingaConstants::NSLOG_VERIFICATION_ERROR		=> "verify error",
		IcingaConstants::NSLOG_VERIFICATION_WARNING		=> "verify warning",
		IcingaConstants::NSLOG_CONFIG_ERROR				=> "config error",
		IcingaConstants::NSLOG_CONFIG_WARNING			=> "config warning",
		IcingaConstants::NSLOG_PROCESS_INFO				=> "process info",
		IcingaConstants::NSLOG_EVENT_HANDLER			=> "event handler",
		IcingaConstants::NSLOG_EXTERNAL_COMMAND			=> "external command",
		IcingaConstants::NSLOG_HOST_UP					=> "host up",
		IcingaConstants::NSLOG_HOST_DOWN				=> "host down",
		IcingaConstants::NSLOG_HOST_UNREACHABLE			=> "host unreachable",
		IcingaConstants::NSLOG_SERVICE_OK				=> "service OK",
		IcingaConstants::NSLOG_SERVICE_UNKNOWN			=> "service unknown",
		IcingaConstants::NSLOG_SERVICE_WARNING			=> "service warning",
		IcingaConstants::NSLOG_SERVICE_CRITICAL			=> "service critical",
		IcingaConstants::NSLOG_PASSIVE_CHECK			=> "passive check",
		IcingaConstants::NSLOG_INFO_MESSAGE				=> "info message",
		IcingaConstants::NSLOG_HOST_NOTIFICATION		=> "host notification",
		IcingaConstants::NSLOG_SERVICE_NOTIFICATION		=> "service notification"
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
	
	/**
	 * Return the logentrytype
	 * @param $key
	 * @return string
	 */
	public static function logentryType($key) {
		return self::resolveArrayConstants(self::$logentry_types, $key);
	}
	
	/**
	 * Returns the notification type
	 * @param $key
	 * @return unknown_type
	 */
	public static function notificationType($key) {
		return self::resolveArrayConstants(self::$notification_types, $key);
	}
	
}

?>