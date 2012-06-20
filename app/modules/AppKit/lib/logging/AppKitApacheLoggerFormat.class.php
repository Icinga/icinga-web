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
 * Logoutput format to write apache like logfiles
 * @author mhein
 *
 */
class AppKitApacheLoggerFormat extends AgaviLoggerLayout {

    const UNKNOWN_NAME = 'unknown';

    private static $severity_names = array(
                                         AgaviLogger::DEBUG => 'debug',
                                         AgaviLogger::ERROR => 'error',
                                         AgaviLogger::FATAL => 'fatal',
                                         AgaviLogger::INFO  => 'info',
                                         AgaviLogger::WARN  => 'warn'
                                     );

    public static function levenToString($level) {
        if (isset(self::$severity_names[$level])) {
            return self::$severity_names[$level];
        }

        return self::UNKNOWN_NAME;
    }

    /**
     * (non-PHPdoc)
     * @see AgaviLoggerLayout::format()
     */
    public function  format(AgaviLoggerMessage $message) {
        return sprintf(
                   '[%s] [%s] %s',
                   strftime($this->getParameter('timestamp_format', '%c')),
                   self::levenToString($message->getLevel()),
                   (string)$message
               );
    }

}
