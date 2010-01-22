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
	
}

?>
