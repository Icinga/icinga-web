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
            $context = AgaviContext::getInstance();

            if($context !== null && AgaviConfig::get('exception.templates.' . $context->getName()) !== null) {
                include(AgaviConfig::get('exception.templates.' . $context->getName()));
            } else {
                include(AgaviConfig::get('exception.default_template'));
            }

            die();
        } else {
            return true;
        }
    }


}

class AppKitPHPError extends Exception {}

?>