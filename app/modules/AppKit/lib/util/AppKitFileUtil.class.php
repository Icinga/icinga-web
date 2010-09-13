<?php

class AppKitFileUtil {
	
	/**
	 * Tries to find a file with different suffixes 
	 * @param string $directory
	 * @param string $basename
	 * @param string $extension
	 * @param array $suffixes
	 * @throws AppKitFileUtilException
	 */
	public static function getAlternateFilename($directory, $basename, $extension, array $suffixes=array('.site')) {
		$suffixes[] = '';
		foreach ($suffixes as $suffix) {
			try {
				$filename = $directory. '/'. $basename. $suffix. $extension;
				self::fileExists($filename);
				return new SplFileObject($filename);
			}
			catch (AppKitFileUtilException $e) {}
			
		}
		throw new AppKitFileUtilException('Could not find any alternatives for '. $basename);
	}
	
	/**
	 * Returns true if a file exists
	 * @param $filename
	 * @throws AppKitFileUtilException
	 */
	public static function fileExists($filename) {
		if (is_file($filename)) {
			return true;
		}
		
		throw new AppKitFileUtilException('File %s does not exist!', $filename);
	}
	
}

class AppKitFileUtilException extends AppKitException {}

?>