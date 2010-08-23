<?php

class AppKitExceptionHandler extends AppKitBaseClass {
	
	const LOG_LEVEL = AgaviLogger::FATAL;
	
	private static $oldHandler = null;
	private static $handlerCallback = array('AppKitExceptionHandler', 'logException');
	
	public static function initializeHandler() {
		self::$oldHandler = set_exception_handler(self::$handlerCallback);
	}
	
	public static function logException(Exception $e) {
		// die("OKOK");
		// AppKitAgaviUtil::log('Uncatched %s: %s', get_class($e), $e->getMessage(), self::LOG_LEVEL);
	}	
}

?>