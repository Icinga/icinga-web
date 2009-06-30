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
	
}

?>
