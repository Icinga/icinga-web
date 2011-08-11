<?php

/**
 * Working with random data
 * @author mhein
 *
 */
class AppKitRandomUtil {

    protected static $htmlid_counter = 0;

    /**
     * Inits the random number generator
     * @return boolean
     * @author Marius Hein
     */
    public static function initRand() {
        static $init = false;

        if ($init === true) {
            return true;
        }

        if (function_exists('posix_getpid')) {
            $bitc = posix_getpid();
        }

        elseif(function_exists('getmypid')) {
            $bitc = getmypid();
        }

        list($time, $usec) = explode(' ', microtime());

        mt_srand(((float)$time * ((float)$usec * 1000003)) ^ $bitc);

        $init = true;

        return $init;
    }

    /**
     * Returns a simple id string based on letters and numbers
     * @param unknown_type $length
     * @param unknown_type $prefix
     */
    public static function genSimpleId($length=5, $prefix="") {
        static $chars = null;

        if ($chars === null) {
            $chars = array_merge(
                         range(48, 57, 1), // 0..9
                         range(65, 90, 1), // A..Z
                         range(97, 122, 1) // a..z
                     );
            shuffle($chars); // MIX THE CHARS
        }

        $o = null;

        for ($i=0; $i<$length; $i++) {
            $o .= (chr($chars[mt_rand(0, count($chars)-1)]));
        }

        return $prefix. $o;
    }

    /**
     * Generate a unique continuous html id string
     * @param unknown_type $prefix
     */
    public static function htmlId($prefix='appkit-htmlid') {
        return sprintf('%s-%04d', $prefix, (++self::$htmlid_counter));
    }

}

// Lazy initialising
AppKitRandomUtil::initRand();