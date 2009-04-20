<?php

class AppKitLoggerManager extends AgaviLoggerManager {
	
	/**
	 * @var ReflectionFunction
	 */
	private $sprintf = null;
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->sprintf = new ReflectionFunction('sprintf');
	}
	
	public function logFormat($format, array $parts = array(), $loggerOrSeverity=null) {
		$message = $this->sprintf->invokeArgs(array_merge(array($format), $parts));
		return parent::log($message, $loggerOrSeverity);
	}
	
	public function logFormatArray(array $parts = array (), $loggerOrSeverity=null) {
		return $this->logFormat(array_shift($parts), $parts, $loggerOrSeverity);
	}
	
	public function logFatal($message, $param1=null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::FATAL);
	}
	
	public function logError($message, $param1= null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::ERROR);
	}
	
	public function logWarn($message, $param1= null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::WARN);
	}
	
	public function logInfo($message, $param1= null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::INFO);
	}
	
	public function logDebug($message, $param1= null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::DEBUG);
	}
	
	public function logAll($message, $param1= null) {
		return $this->logFormatArray(func_get_args(), AgaviLogger::ALL);
	}
}

?>