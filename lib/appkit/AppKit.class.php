<?php
// This file is part of the ICINGAAppKit
// (c) 2009 Netways GmbH
//
// ICINGAAppKit is a framework build on top of Agavi to
// develop applications with the same context as modules
//
// For the complete license information please take a look
// into the LICENSE file in the root directory. This file
// was distributed with the source package.

/**
 * This is the AppKit masterclass. Mainly used for inheritance 
 * rules and holding the bootstrab method to init the framework
 * before agavi starts.
 * 
 * @package     ICINGAAppKit
 * @subpackage  AppKit
 * 
 * @author      Marius Hein
 * @copyright   Authors
 * @copyright   Netways GmbH
 * 
 * @version     $Id$
 *
 */
class AppKit {
	
	/**
	 * @var string
	 */
	private static $class_dir = null;
	
	/**
	 * The bootstrap method to initialize the appkit (through AgaviConfig)
	 * 
	 * @return boolean
	 * @author Marius Hein
	 */
	public static function bootstrap() {
		
		// Our directory we live on!
		self::$class_dir = dirname(__FILE__);
		
		// Require all to make the autoloader work.
		require_once(self::$class_dir. '/class/AppKitBaseClass.class.php');
		require_once(self::$class_dir. '/util/AppKitClassUtil.class.php');
		require_once(self::$class_dir. '/class/AppKitSingleton.class.php');
		require_once(self::$class_dir. '/class/AppKitAutoloader.class.php');
		
		// var_dump(AgaviConfig::toArray());
		
		// Init the autoloader
		if (is_array(($autoloader_paths = AgaviConfig::get('de.icinga.appkit.autoloader.paths'))) && count($autoloader_paths)) {
			foreach ($autoloader_paths as $path) AppKitAutoloader::addSearchPath($path);
		}
		else {
			throw new AppKitAutoloaderException('de.icinga.appkit.autoloader.paths is incorrect');
		}
		
		if (is_array(($autoloader_prefixes = AgaviConfig::get('de.icinga.appkit.autoloader.prefixes'))) && count($autoloader_prefixes)) {
			foreach ($autoloader_prefixes as $prefix) AppKitAutoloader::addNamePrefix($prefix);
		}
		else {
			throw new AppKitAutoloaderException('de.icinga.appkit.autoloader.prefixes is incorrect');
		}
		
		AppKitAutoloader::getInstance()->registerAutoloadRequests();
		
		// Try to include doctrine
		if (file_exists($doctrine_dir = AgaviConfig::get('de.icinga.appkit.doctrine_path'))) {
			require_once($doctrine_dir. '/Doctrine.php');
		}
		else {
			// Cool, not really exists, leave a message :-)
			throw new AppKitException("Doctrine ('$doctrine_dir') not found!");
		}
		
		// Init our factories (mostly the AuthProvider)
		AppKitFactories::loadFactoriesFromConfig('de.icinga.appkit.factories');
		
		// Applying PHP settings
		if (is_array($settings = AgaviConfig::get('de.icinga.appkit.php_settings'))) {
			foreach ($settings as $ini_key=>$ini_val) {
				if (ini_set($ini_key, $ini_val) === false) {
					throw new AppKitException("Could not set ini setting $ini_key to '$ini_val'");
				}
			}
		}
		
		// Register additional events from config file
		if (is_array($events = AgaviConfig::get('de.icinga.appkit.custom_events'))) {
			AppKitEventDispatcher::registerEventClasses($events);
		}
		
		// Trigger an event!
		AppKitEventDispatcher::getInstance()->triggerSimpleEvent('appkit.bootstrap', 'AppKit bootstrap finished');
		
		return true;
	}
	
	/**
	 * Returns a constantvalue represented by a string input
	 * 
	 * @param string $value
	 * @return mixed
	 * @author Marius Hein
	 */
	public static function getConstant($value) {
		$value = preg_replace('@^CONST::@', null, $value);
		if (defined($value)) {
			return constant($value);
		}
		
		throw new AppKitException("Constant '$value' not defined!");
	}
	
	/**
	 * Create an instance of a class by function arguments
	 * @param string $class_name
	 * @param mixed $arg1
	 * @return StdClass
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public static function getInstance($class_name, $arg1=null) {
		$args = func_get_args();
		$class_name = array_shift($args);
		return self::getInstanceArray($class_name, $args);
	}
	
	/**
	 * Create an instance of a class by array arguments
	 * @param string $class_name
	 * @param array $args
	 * @return StdClass
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public static function getInstanceArray($class_name, array $args = array()) {
		$ref = new ReflectionClass($class_name);
		if ($ref->isInstantiable()) {
			return $ref->newInstanceArgs($args);
		}
		
		throw new AppKitException('Class '. $class_name. ' is not instantiable!');
	}
	
	public static function debugOut($mixed) {
		$queue = AppKitFactories::getInstance()->getFactory('MessageQueue');
		if ($queue instanceof AppKitQueue) {
			$args = func_get_args();
			if (count($args) == 1) $args = $args[0];
			ob_start();
			var_dump($args);
			$data = ob_get_clean();
			$queue->enqueue(AppKitMessageQueueItem::Debug($data));
		}
	}
	
}

?>