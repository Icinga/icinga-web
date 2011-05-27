<?php

class AppKitAgaviUtil {

    /**
     * @var AgaviContext
     */
    private static $context = null;

    public static function initContext(AgaviContext &$context) {
        if(self::$context === null) {
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

    public static function log($arg1) {
        if(is_array($arg1)) {
            $argv =& $arg1;
        } else {
            $argv = func_get_args();
        }

        if(count($argv) == 1) {
            $format = $arg1;
            $severity = AgaviLogger::ALL;
        } else {
            $severity = array_pop($argv);
            $format = array_shift($argv);
        }

        return self::getContext()->getLoggerManager()->log(@vsprintf($format, $argv), $severity);
    }

    public static function replaceConfigVars($text) {
        $m = array();

        if(preg_match_all('/%([^%]+)%/', $text, $m, PREG_SET_ORDER)) {
            foreach($m as $match) {
                if(AgaviConfig::has($match[1])) {
                    $text = preg_replace('/'. preg_quote($match[0]). '/', AgaviConfig::get($match[1]), $text);
                }
            }
        }

        return $text;
    }

    /**
     * Shortcut to initialize modules without strict warnings
     * @param AgaviController
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

        if($agaviController === null) {
            $agaviController = new AgaviController();
        }

        return $agaviController;
    }

}


?>
