<?php
class AppKitExceptionHandler extends AppKitBaseClass {
	
	const LOG_LEVEL = AgaviLogger::FATAL;
	
	private static $oldExceptionHandler = null;
	private static $oldErrorHandler = null;
	private static $handlerException = array('AppKitExceptionHandler', 'logException');
	private static $handlerError = array('AppKitExceptionHandler', 'phpErrorException');
	
	public static function initializeHandler() {
		self::$oldExceptionHandler = set_exception_handler(self::$handlerException);
		self::$oldErrorHandler = set_error_handler(self::$handlerError);
		ini_set('display_errors', false);
	}
	
	public static function phpErrorException($errno, $errstr, $errfile, $errline, array $errcontext = array()) {
		$string = sprintf('PHP Error %s (%s:%d)', $errstr, $errfile, $errline);
		self::logException(new AppKitPHPError($string, $errno));
	}
	
	public static function logException(Exception $e) {
		
		AppKitAgaviUtil::log('Uncaught %s: %s (%s:%d)', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), self::LOG_LEVEL);
		// don't die in case of supressed errors (like the ob_clean in the agaviException has)
		if(error_reporting()) {
			if (!headers_sent()) {
				header('HTTP/1.1 500 Internal Server Error');
				header('Content-type: text/plain');
			}

			echo "-> 500 internal server error!\n\n";

			printf("=== Error ===\nUncaught exception %s thrown!\n\n", get_class($e));

			printf("=== Message ===\n%s\n\n", $e->getMessage());

			printf("=== Stacktrace ===\n%s", $e->getTraceAsString());
			echo error_reporting();
			die();
		}
	}
	
}

class AppKitPHPError extends Exception {}

?>