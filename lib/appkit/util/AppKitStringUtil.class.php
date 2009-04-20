<?php

class AppKitStringUtil implements AppKitHtmlEntitiesInterface {
	
	/**
	 * Replaces a string with marker within an attay
	 * @param string $string
	 * @param array $markers
	 * @param boolean $toupper
	 * @param string $wrapper
	 * @return string
	 */
	public static function replaceMarkerArray($string, array $markers, $toupper=true, $wrapper='__%s__') {
		
		foreach ($markers as $marker=>$replace) {
			$string = str_replace(sprintf($wrapper, $toupper ? strtoupper($marker) : $marker), $replace, $string);
		}
		
		return $string;
		
	}
	
	public static function htmlPseudoPreformat($string) {
		// Converts spaces into breaking spaces ^^
		$string = str_replace(' ', self::ENTITY_BSP, $string);
		$string = nl2br($string);
		return $string;
	}
	
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
	
}

?>