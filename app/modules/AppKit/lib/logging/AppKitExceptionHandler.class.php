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
 Project wide exception and php error handler. This
 handler logs also all errors into agavi logger
 * @author mhein
 *
 */
class AppKitExceptionHandler {

    const LOG_LEVEL = AgaviLogger::FATAL;

    private static $oldExceptionHandler = null;
    private static $oldErrorHandler = null;
    private static $handlerException = array('AppKitExceptionHandler', 'logException');
    private static $handlerError = array('AppKitExceptionHandler', 'phpErrorException');

    /**
     * Prepare php to use something other that its internal stack
     */
    public static function initializeHandler() {
        self::$oldExceptionHandler = set_exception_handler(self::$handlerException);
        self::$oldErrorHandler = set_error_handler(self::$handlerError);
        ini_set('display_errors', false);
    }

    /**
     * Converts php simple error into an exception
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     * @param array $errcontext
     */
    public static function phpErrorException($errno, $errstr, $errfile, $errline, array $errcontext = array()) {
        $string = sprintf('PHP Error %s (%s:%d)', $errstr, $errfile, $errline);
        self::logException(new AppKitPHPError($string, $errno));
    }

    public static function logException(Exception $e) {
        AppKitAgaviUtil::log('Uncaught %s: %s (%s:%d)', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), self::LOG_LEVEL);

        // don't die in case of supressed errors (like the ob_clean in the agaviException has)

        if (error_reporting()) {
            $context = AgaviContext::getInstance();

            if ($context !== null && AgaviConfig::get('exception.templates.' . $context->getName()) !== null) {
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
