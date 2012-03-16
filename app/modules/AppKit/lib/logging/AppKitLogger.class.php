<?php

/**
 * Description of AppKitLogger
 *
 * @author jmosshammer
 */
final class AppKitLogger {

    private static $verbose = null;

    private function  __construct() {
        throw new AppKitException("AppKitLogger can only be accessed statically");
    }

    private function __clone() {
        throw new AppKitException("AppKitLogger can only be accessed statically");
    }
    private static $logger = null;

    private static function getVerbose() {
        if(!is_array(self::$verbose)) {
            self::$verbose = AgaviConfig::get("modules.appkit.debug.verbose");
            if(self::$verbose == null)
                self::$verbose = array();
        }
        return self::$verbose;
    }

    private static function getLogger() {
        if(self::$logger == null) {
            $ctx = AgaviContext::getInstance();
            self::$logger = $ctx->getLoggerManager();
        }
        return self::$logger;
    }

    private static function getCallerInfo() {
        $trace = debug_backtrace(false);
        if(count($trace) == 0)
            return;
       for($i=0;$i<count($trace);$i++) {
           if($trace[$i]["class"] != "AppKitLogger")
               return $trace[$i];
       }
       return null;
    }

    private static function formatMessage($arg1) {
        $argv;
        if (is_array($arg1[0])) {
            $argv =& $arg1[0];
        } else {
            $argv = $arg1;
        }

        if (count($argv) == 1) {
            $format = $arg1;
        } else {
            $format = array_shift($argv);
        }
        if(!is_string($format)) {
            $format = json_encode($format);
        }
        foreach($argv as &$arg) {
            if(!is_string($arg))
                $arg = json_encode($arg);
        }

        
        return @vsprintf($format, $argv);
    }

    /**
     * Like debug logging, but only messages from classes enabled in AppKit/config/debug.xml
     * will be written to the log
     */
    public static function verbose() {
        $verbose = self::getVerbose();

        $caller = self::getCallerInfo();
        if(!in_array($caller["class"], $verbose))
            return;

        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;

        $msg = $caller["class"]."::".$caller["function"]."()@".(isset($caller["line"]) ? $caller["line"] : "?") ." : ".$msg ;
        
        $log->log($msg,AgaviLogger::DEBUG);
    }

    public static function debug() {
        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;
        if($caller = self::getCallerInfo()) {
            $msg .= " (".$caller["class"]."::".$caller["function"]."(), line ".(isset($caller["line"]) ? $caller["line"] : "?") .")";
        }
        $log->log($msg,AgaviLogger::DEBUG);
    }
    public static function fatal() {
        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;
        if($caller = self::getCallerInfo()) {
            $msg .= " (".$caller["class"]."::".$caller["function"]."(), line ".$caller["line"].")";
        }
        $log->log($msg,AgaviLogger::FATAL);
    }
    public static function info() {
        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;
        $log->log($msg,AgaviLogger::INFO);
    }
    public static function warn() {
        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;
        if($caller = self::getCallerInfo()) {
            $msg .= " (".$caller["class"]."::".$caller["function"]."(), line ".$caller["line"].")";
        }
        $log->log($msg,AgaviLogger::WARN);
    }
    public static function error() {
        $msg = self::formatMessage(func_get_args());
        $log = self::getLogger();
        if($log == null)
            return;

        if($caller = self::getCallerInfo()) {
            $msg .= " (".$caller["class"]."::".$caller["function"]."(), line ".$caller["line"].")";
        }
        $log->log($msg,AgaviLogger::ERROR);
    }

}
?>
