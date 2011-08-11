<?php

/**
 * Logoutput format to write apache like logfiles
 * @author mhein
 *
 */
class AppKitApacheLoggerFormat extends AgaviLoggerLayout {

    const UNKNOWN_NAME = 'unknown';

    private static $severity_names = array(
                                         AgaviLogger::DEBUG	=> 'debug',
                                         AgaviLogger::ERROR	=> 'error',
                                         AgaviLogger::FATAL	=> 'fatal',
                                         AgaviLogger::INFO	=> 'info',
                                         AgaviLogger::WARN	=> 'warn'
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
