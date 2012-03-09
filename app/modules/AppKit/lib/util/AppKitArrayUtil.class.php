<?php

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
     * @return array
     */
    public static function trimSplit($string, $split_char=',') {
        return preg_split('/\s*'. preg_quote($split_char). '\s*/', $string);
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
}

class AppKitArrayUtilException extends AppKitException {}
