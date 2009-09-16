<?php

class AppKitInlineIncluderUtil {
	
	public static function getJsFile($file) {
		$real_file = self::getFile($file);
		return $real_file;
	}
	
	private static function getFile($file, $path = null) {
		
		if ($path !== null) {
			$file = sprintf('%s/%s', $path, $file);
		}
		
		if (!file_exists($file)) {
			$file = sprintf('%s/%s', self::determinePath(), $file);
		}
		
		if (!file_exists($file)) {
			throw new AppKitInlineIncluderUtilException("Could not locale '$file'");
		}
		
		return $file;
	}
	
	private static function determinePath() {
		$trace = debug_backtrace();
		$trace = array_splice($trace, 2, 1);
		$step = array_shift($trace);
		$dir = dirname($step['file']);
		
		if (file_exists($dir)) {
			return $dir;
		}
		
		throw new AppKitInlineIncluderUtilException('Coult not determine the path');
		
	}
	
}

class AppKitInlineIncluderUtilException extends AppKitException {}

?>