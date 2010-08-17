<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IcingaApiClassUtil
 *
 * @author mhein
 */
class IcingaApiClassUtil {

	private static $className = null;
	private static $classFile = null;

	public static function initialize() {

		if (self::$classFile !== null && self::$className !== null) {
			return true;
		}

		if (AgaviConfig::get('modules.web.version')) {
			self::$className = AgaviConfig::get('modules.web.api.class');
			self::$classFile = AgaviConfig::get('modules.web.api.file');
			self::checkClass();
		}
		else {
			throw new IcingaApiClassUtilException('Module \'Web\' not ready initialized!');
		}

		return true;
	}

	public static function checkClass() {
		if (!self::checkClassExists(self::$className)) {
			self::includeClassFile(self::$classFile);
		}

		return true;
	}

	private static function includeClassFile($classFile) {
		if (file_exists(self::$classFile)) {
			require_once(self::$classFile);
			return true;
		}

		throw new IcingaApiClassUtilException('Classfile \'%s\' does not exist!', $classFile);
	}

	private static function checkClassExists($className) {
		return class_exists(self::$className);
	}

}

class IcingaApiClassUtilException extends IcingaBaseException {}


?>
