<?php
// This file is part of the NETWAYSAppKit
// (c) 2009 NETWAYS GmbH
//
// NETWAYSAppKit is a framework build on top of Agavi to
// develop applications with the same context as modules
//
// For the complete license information please take a look
// into the LICENSE file in the root directory. This file
// was distributed with the source package.

/**
 * The AppKit singleton implementation. Abstractclass to 
 * implemnt the getInstance method in the childs. Also protects
 * the constructor
 * 
 * @package     NETWAYSAppKit
 * @subpackage  AppKit
 * 
 * @author      Marius Hein
 * @copyright   Authors
 * @copyright   NETWAYS GmbH
 * 
 * @version     $Id$
 *
 */
abstract class AppKitSingleton extends AppKitBaseClass {
	
	private static $instances = array ();
	
	/**
	 * Returns the instance, also initiates the
	 * creation of a new object
	 * 
	 * @return AppKitBaseClass
	 * 
	 * @author Marius Hein
	 */
	public static function getInstance() {
		$args = func_get_args();
		$class_name = array_shift($args);
		
		if (!self::checkInstance($class_name)) {
			self::$instances[$class_name] =
				self::createInstance($class_name, $args);
		}
		
		return self::$instances[$class_name];
	}
	
	/**
	 * Creates an object instance by reflection
	 * 
	 * @param string $className
	 * @param array $parameters
	 * @return StdClass
	 * 
	 * @author Marius Hein
	 */
	private static function createInstance($className, array $parameters) {
		$ref = new ReflectionClass($className);
		return $ref->newInstanceArgs($parameters);
	}
	
	public static function checkInstance($class_name) {
		if (array_key_exists($class_name, self::$instances) && self::$instances[$class_name] instanceof $class_name) {
			
			return true;
		}
		
		return false;
	}

	/**
	 * The protected constructor, checks if we have already an instance
	 * @throws AppKitSingletonException
	 * 
	 * @author Marius Hein
	 */
	public function __construct() {
		$class = get_class($this);
		if (self::checkInstance($class)) {
		
			throw new AppKitSingletonException("This is a singleton, try $class::getInstance() instead!");
		}
		else {
			self::$instances[$class] = $this;
		}
	}
	
	private function __clone() { }

}

/**
 * Singleton exception inheritance
 * 
 * @package     NETWAYSAppKit
 * @subpackage  AppKit
 * 
 * @author      Marius Hein
 * @copyright   Authors
 * @copyright   NETWAYS GmbH
 * 
 * @version     $Id$
 *
 */
class AppKitSingletonException extends AppKitException {}

?>