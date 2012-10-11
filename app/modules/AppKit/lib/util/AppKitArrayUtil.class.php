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
 * Set of array helper methods
 * @author mhein
 *
 */
class AppKitArrayUtil {
    /**
     * Search a tree like array structure
     * @param mixed $needle
     * @param array $haystack
     * @return boolean found or not
     */
    public static function searchKeyRecursive($needle, array $haystack) {
        $out = false;
        foreach($haystack as $key=>$val) {
            if ($key == $needle) {
                $out = true;
                break;
            }

            elseif(is_array($val)) {
                $out = self::searchKeyRecursive($needle, $val);
            }
            elseif($out == true) {
                break;
            }
        }
        return $out;
    }

    /**
     * Flatten an recursive tree structure
     *
     * @param array $data
     * @param string $key_prefix
     * @param array $dump
     * @return array
     */
    public static function flattenArray(array &$data, $key_prefix='', array &$dump = array()) {
        foreach($data as $k=>$v) {
            if (is_array($v)) {
                self::flattenArray($v, $key_prefix. '.'. $k, $dump);
            } else {
                $dump[$key_prefix. '.'. $k] = $v;
            }
        }

        return $dump;
    }

    /**
     * Combines an array tree to flat one. The new keys will bis the
     * keys at the end of the leaf structure
     *
     * @param array $array
     * @param boolean $check_keys
     * @deprecated Not used in icinga-web
     * @return array
     */
    public static function uniqueKeysArray(array $array, $check_keys=false) {
        $out = array();
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        foreach($it as $key=>$item) {

            if ($check_keys == true && array_key_exists($key, $out)) {
                throw new AppKitArrayUtilException('Key %s already defined', $key);
            }

            $out[$key] = $item;
        }

        return $out;
    }
    
    /**
     * Returns unique collection of a multidimensional
     * array
     * @param array $input
     * @return array
     */
    public static function uniqueMultidimensional(array $input) {
        $check = array();
        array_multisort($input);
        foreach ($input as $key=>$array) {
            $check[md5(json_encode($array))] = $key;
        }
        return array_intersect_key($input, array_flip($check));
    }

    /**
     * Splits a string into parts and respects spaces
     * @param string $string
     * @param string $split_char
     * @param boolean $auto_convert Try to findout the right types for value
     * @return array
     */
    public static function trimSplit($string, $split_char=',', $auto_convert=true) {
       
        // Avoid ugly array items 
        $string = trim($string);

        // Tests of is_array in code
        if (strlen($string) < 1) {
            return null;
        }

        $data = preg_split('/\s*'. preg_quote($split_char). '\s*/', $string);
   
        // Some database systems are more type safe (pg) and throws
        // a bunch of errors if you ignore that
        if ($auto_convert===true) {
            foreach ($data as &$val) {
                if (is_float($val) === true) {
                    $val = (float)$val;
                } elseif (is_numeric($val)) {
                    $val = (integer)$val;
                }
            }
        }

        return $data;
    }

    /**
     * Sorting associative arrays of arrays by subfields
     * @param array $arry
     * @param string $field fieldname existing in sub array
     * @param string $op something like '<' or '>'
     */
    public static function subSort(array &$arry, $field, $op='<') {
        $cfn = create_function('$a, $b', 'return ($a[\''. $field. '\'] '. $op. '$b[\''. $field. '\']) ? -1 : 1;');
        return uasort($arry, $cfn);
    }

    /**
     * Helper function for xml2array
     * @param DOMElement $element
     * @return boolean
     */
    private static function hasChildren(DOMElement &$element) {
        $hasChildren = false;

        if ($element->hasChildNodes()) {
            foreach($element->childNodes as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $hasChildren = true;
                    break;
                }
            }
        }

        return $hasChildren;
    }

    /**
     * Converts a XML fragment to an array based on
     * id attributes like name or id itself
     * @param DOMNodeList $l
     * @param array $a
     */
    public static function xml2Array(DOMNodeList $l, &$a) {
        foreach($l as $n) {


            if ($n->nodeType == XML_ELEMENT_NODE) {

                $name = null;

                if ($n->hasAttribute('name')) {
                    $name = $n->getAttribute('name');
                }

                elseif($n->hasAttribute('id')) {
                    $name = $n->getAttribute('id');
                }
                else {
                    $name = $n->nodeName;
                }

                if (self::hasChildren($n)) {
                    $a[$name] = array();
                    self::xml2Array($n->childNodes, $a[$name]);
                } else {
                    $c = $n->textContent;

                    if ($c=='false') {
                        $c=false;
                    }

                    elseif($c=='true') $c=true;

                    $a[$name] = $c;
                }
            }
        }
    }

    /**
     * Rewrite array key to new ones based on a associative
     * map array
     * @param array $array
     * @param array $map
     * @param boolean $remove_unused remove keys not found
     */
    public static function swapKeys(array &$array, array $map, $remove_unused=false) {
        foreach($map as $src=>$target) {
            if (isset($array[$src])) {
                $array[$target] = $array[$src];
            } else {
                $array[$target] = null;
            }

            unset($array[$src]);
        }

        if ($remove_unused) {
            $sect = array_intersect_key($array, array_flip($map));
            $array = $sect;
        }
    }
    
    /**
     * replace_recursive proc function
     * @param mixed $array
     * @param mixed $array1
     * @return array
     */
    private static function replaceRecursiveProc($array, $array1) {
        foreach ($array1 as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
                $value = self::replaceRecursiveProc($array[$key], $value);
            }
            $array[$key] = $value;
        }
        return $array;
    }
    
    /**
     * PHP implementation of array_replace_recursive because it
     * is only available PHP > 5.3
     * 
     * It slightly differs from PHP native implementation because
     * it returns the replacement array
     * 
     * @see http://www.de.php.net/array_replace_recursive
     * @author Gregor[at]der-meyer[dot]de
     * @deprecated In flavour of PHP 5.3
     * @param array $array
     * @param array $array1
     * @return array The replacement
     */
    public static function replaceRecursive($array, $array1) {
        
    
        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }
        for ($i = 1; $i < count($args); $i++) {
            if (is_array($args[$i])) {
                $array = self::replaceRecursiveProc($array, $args[$i]);
            }
        }
        return $array;
    }
    
    /**
     * Checks for item in a string list
     * @todo Maybe this is slow
     * @param string $list
     * @param string $match
     * @param string $split_char
     * @return boolean the result
     */
    public static function matchAgainstStringList($list, $match, $split_char=',') {
        return in_array($match, self::trimSplit($list, $split_char));
    }
    
    /**
     * Converts an array containing ISO-8859-1 string to utf-8
     * 
     * @param array $obj
     */
    public static function toUTF8_recursive(array &$obj) {
        foreach($obj as $field=>&$value) {
            if(is_string($value) && !AppKitStringUtil::isUTF8($value))
                $value = utf8_encode($value);
            else if(is_array($value))
                AppKitArrayUtil::toUTF8_recursive($value);
        }
    }
    /**
     * Converts an array containing ISO-8859-1 string to utf-8
     * 
     * @param array $obj
     */
    public static function toISO_recursive(array &$obj) {
        foreach($obj as $field=>&$value) {
            if(is_string($value))
                $value = utf8_decode($value);
            else if(is_array($value))
                AppKitArrayUtil::toISO_recursive($value);
        }
    }
}

class AppKitArrayUtilException extends AppKitException {}
