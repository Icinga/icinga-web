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
 * Working with strings
 * @author mhein
 *
 */
class AppKitStringUtil {

    /**
     * Replaces a string with marker within an attay
     * @param string $string
     * @param array $markers
     * @param boolean $toupper
     * @param string $wrapper
     * @return string
     */
    public static function replaceMarkerArray($string, array $markers, $toupper=true, $wrapper='__%s__') {

        foreach($markers as $marker=>$replace) {
            $string = str_replace(sprintf($wrapper, $toupper ? strtoupper($marker) : $marker), $replace, $string);
        }

        return $string;

    }

    /**
     * Convert bytes to an human readable string
     * @deprecated Not used
     * @param integer $val
     * @param $arg
     * @return string
     */
    public static function bytesReadable($val, $arg = false) {
        $a =& $val;

        $unim = array("B","KB","MB","GB","TB","PB");
        $c = 0;

        while ($a>=1024) {
            $c++;
            $a = $a/1024;
        }

        return number_format($a,($c ? 2 : 0),",",".")." ".$unim[$c];
    }

    /**
     * Checks if the given string contains a prinf format
     * @param string $string
     * @return boolean
     */
    public static function detectFormatSyntax($string) {
        return preg_match('@%([bcdeufFosxX]|\d+\$[bcdeufFosxX])@',  $string);
    }

    /**
     * Calculates the real web path, not affected by
     * rewrite and agavi routing
     *
     * @return string
     */
    public static function extractWebPath() {
        // Starting with the complete string
        $request_uri = $_SERVER['REQUEST_URI'];

        // Remove the fake path from the
        // uri
        if ($_SERVER['QUERY_STRING']) {
            $q = preg_replace('@&@', '?', $_SERVER['QUERY_STRING'], 1);
            $request_uri = preg_replace('@'. preg_quote($q, '@'). '$@', '', $request_uri);
        }

        // Removing the index.php
        $request_uri = preg_replace('@index.php$@', '', $request_uri, 1);

        // Take care about the leading slash
        if (!preg_match('@^\/@', $request_uri)) {
            $request_uri = '/'. $request_uri;
        }

        return $request_uri;
    }

    /**
     * Converts an absolute path to web resource into relative
     * @param string $path path or resource
     * @return string the converted path
     */
    public static function absolute2Rel($path) {
        $abw = AgaviConfig::get('org.icinga.appkit.web_absolute_path');
        return AgaviConfig::get('org.icinga.appkit.web_path'). preg_replace('@^'. preg_quote($abw). '@', '', $path);
    }

    /**
     * Returns true if an url is found
     * @param string $url
     * @return boolean
     */
    public static function detectUrl($url) {
        return preg_match('@^[a-z]{3,8}://.+$@', $url);
    }

}