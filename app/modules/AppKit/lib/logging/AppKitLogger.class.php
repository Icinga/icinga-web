<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
