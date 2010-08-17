<?php

class AppKitArrayUtil {
	
	public static function arrayKeyInsertBefore(array $input, $before, array $insert) {
		$new = array ();
		$old = $input;
		
		foreach ($old as $key=>$val) {
			if ($key == $before) {
				foreach ($insert as $iKey=>$iVal) {
					$new[$iKey] = $iVal;
				}
			}
			$new[$key] = $val;
		}
		return $new;
	}
	
	public static function searchKeyRecursive($needle, array $haystack) {
		$out = false;
		foreach ($haystack as $key=>$val) {
			if ($key == $needle) {
				$out = true;
				break;
			}
			elseif (is_array($val)) {
				$out = self::searchKeyRecursive($needle, $val);
			}
			elseif ($out == true) {
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
	public static function flattenArray(array &$data, $key_prefix='', array &$dump = array ()) {
		foreach ($data as $k=>$v) {
			if (is_array($v)) {
				self::flattenArray($v, $key_prefix. '.'. $k, $dump);
			}
			else {
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
	 * @return array
	 */
	public static function uniqueKeysArray(array $array, $check_keys=false) {
		$out = array ();
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($array) );
		foreach ($it as $key=>$item) {

			if ($check_keys == true && array_key_exists($key, $out)) {
				throw new AppKitArrayUtilException('Key %s already defined', $key);
			}
			
			$out[$key] = $item;
		}
		
		return $out;
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
	
}

class AppKitArrayUtilException extends AppKitException {}

?>
