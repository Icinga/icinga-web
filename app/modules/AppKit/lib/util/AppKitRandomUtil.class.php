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