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
 * @author Marius Hein <marius.hein@netways.de>
 * @author Eric Lippmann <eric.lippmann@netways.de>
 */
class AppKitExceptionHandler {

    const LOG_LEVEL = AgaviLogger::FATAL;

    /**
     * Sets user-defined error and exception handler functions so that
     * errors and exceptions apper in the icinga-web logs.
     */
    public static function initializeHandler() {
        set_exception_handler(array('AppKitExceptionHandler', 'logException'));
        set_error_handler(array('AppKitExceptionHandler', 'exceptionOnError'));
    }

    /**
     * Treats PHP's non-exception errors as exceptions.
     */
    public static function exceptionOnError(
        $errno, $errstr, $errfile, $errline, array $errcontext = array()
    ) {
        $message = sprintf('PHP Error %s (%s:%d)', $errstr, $errfile, $errline);
        self::logException(new AppKitPHPError($message, $errno));
    }

    /**
     * Logs exceptions to the icinga-web logs.
     */
    public static function logException(Exception $e) {
        AppKitAgaviUtil::log(
            'Uncaught %s: %s (%s:%d)', get_class($e), $e->getMessage(),
            $e->getFile(), $e->getLine(), self::LOG_LEVEL);
        // Rethrow exception, so Agavi can handle and render it.
        // See AgaviController#dispatch().
        throw $e;
    }
}

class AppKitPHPError extends Exception {}
