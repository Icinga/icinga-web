<?php

/**
 * Agavi toolkit, shortening things
 * @author mhein
 *
 */
class AppKitAgaviUtil {

    /**
     * @var AgaviContext
     */
    private static $context = null;

    /**
     * Inject static data
     * @param AgaviContext $context
     */
    public static function initContext(AgaviContext &$context) {
        if (self::$context === null) {
            self::$context =& $context;
        }

        return true;
    }

    /**
     * Returns the current agavi context
     * @return AgaviContext
     */
    public static function getContext() {
        return self::$context;
    }

    /**
     * Variable log method, provide sprintf format,
     * arguments and Agavi log level at once
     * @param mixed $arg1
     * @oaram mixed $argN LogLevel or sprintf format
     */
    public static function log($arg1) {
        if (is_array($arg1)) {
            $argv =& $arg1;
        } else {
            $argv = func_get_args();
        }

        if (count($argv) == 1) {
            $format = $arg1;
            $severity = AgaviLogger::ALL;
        } else {
            $severity = array_pop($argv);
            $format = array_shift($argv);
        }

        return self::getContext()->getLoggerManager()->log(@vsprintf($format, $argv), $severity);
    }

    /**
     *
     * Replace custom strings with agavi configuration items
     * @param string $text
     * @todo Move into better class space
     * @deprecated Please implement bether construct (Maybe Agavi itself can?)
     * @return string
     */
    public static function replaceConfigVars($text) {
        $m = array();

        if (preg_match_all('/%([^%]+)%/', $text, $m, PREG_SET_ORDER)) {
            foreach($m as $match) {
                if (AgaviConfig::has($match[1])) {
                    $text = preg_replace('/'. preg_quote($match[0]). '/', AgaviConfig::get($match[1]), $text);
                }
            }
        }

        return $text;
    }

    /**
     * Shortcut to initialize modules without strict warnings
     * @deprecated Use agavi onbord context method (but its not static)
     * @param string module name
     */
    public static function initializeModule($moduleName) {
        return self::getAgaviControllerInstance()->initializeModule($moduleName);
    }

    /**
     * Returns a single instance of the Agavi controller to work
     * with non-static methods
     * @return AgaviController
     */
    public static function getAgaviControllerInstance() {
        static $agaviController = null;

        if ($agaviController === null) {
            $agaviController = new AgaviController();
        }

        return $agaviController;
    }

}
