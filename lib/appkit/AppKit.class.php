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
		
		// Require all needed base classes
		self::initBaseClasses();
		// Some auto generated config settings
		
		self::initAutoSettings();
		// Init our factories (Cache, MessageQueue, Auth, IcingaData, ...)
		
		AppKitFactories::loadFactoriesFromConfig('de.icinga.appkit.factories');
		
		// Configure and enable the autoloader
		self::initClassAutoloading();

		// Start doctrine
		self::initDoctrineOrm();
			
		// Apply some php ini settings
		self::initPhpConfiguration();
		
		// Apply some php ini settings
		// Init the event handler system
		self::initEventHandling();
	
		// Init the event handler system
		self::setLanguageDomain();
			
		// Say hello to our components
		AppKitEventDispatcher::getInstance()->triggerSimpleEvent('appkit.bootstrap', 'AppKit bootstrap finished');
		
		// give notice
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
	
	/**
	 * Delayed debug method, write test data to the queue to display within the site
	 * after one request
	 * 
	 * @param mixed $mixed
	 * @return boolean
	 */
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
		
		return true;
	}
	
	/**
	 * A centralized anchor to get version information
	 * @return string
	 */
	public static function getVersion() {
		return AgaviConfig::get('de.icinga.appkit.version.release');
	}
	
	// -----------------
	// Private interface
	// -----------------
	
	/**
	 * Helper method
	 * require needed base classes
	 * @return boolean
	 */
	private static function initBaseClasses() {
		// String handling
		require_once(self::$class_dir. '/html/AppKitHtmlEntitiesInterface.interface.php');
		require_once(self::$class_dir. '/util/AppKitStringUtil.class.php');
		
		// Require all to make the autoloader work.
		require_once(self::$class_dir. '/class/AppKitBaseClass.class.php');
		require_once(self::$class_dir. '/util/AppKitClassUtil.class.php');
		require_once(self::$class_dir. '/class/AppKitSingleton.class.php');
		require_once(self::$class_dir. '/class/AppKitAutoloader.class.php');
		
		// Classes for the factory to work
		require_once(self::$class_dir. '/class/AppKitFactoryInterface.interface.php');
		require_once(self::$class_dir. '/class/AppKitFactory.class.php');
		require_once(self::$class_dir. '/class/AppKitFactories.class.php');
		
		return true;
	}
	
	/**
	 * Helper method
	 * Configure and register the autoloader
	 * @return boolean
	 */
	private static function initClassAutoloading() {
		// Init the autoloader
		if (is_array(($autoloader_paths = AgaviConfig::get('de.icinga.appkit.autoloader.paths'))) && count($autoloader_paths)) {
			foreach ($autoloader_paths as $path) {
				AppKitAutoloader::addSearchPath($path);
			}
		}
		else {
			throw new AppKitAutoloaderException('de.icinga.appkit.autoloader.paths is incorrect');
		}
		
		if (is_array(($autoloader_prefixes = AgaviConfig::get('de.icinga.appkit.autoloader.prefixes'))) && count($autoloader_prefixes)) {
			foreach ($autoloader_prefixes as $prefix) {
				AppKitAutoloader::addNamePrefix($prefix);
			}
		}
		else {
			throw new AppKitAutoloaderException('de.icinga.appkit.autoloader.prefixes is incorrect');
		}
		
		AppKitAutoloader::getInstance()->registerAutoloadRequests();
		
		return true;
	}
	
	
	/**
	 * Helper method
	 * Auto-overwrite icinga.xml settings
	 * @return boolean
	 */
	private static function initAutoSettings() {
		// Try to set the web path to correct urls within the frontend
		if(AgaviConfig::get('core.default_context') =='web') {
			// Try to set the web path to correct urls within the frontend
			if (AgaviConfig::get('de.icinga.appkit.web_path') == null) {
				AgaviConfig::set('de.icinga.appkit.web_path', AppKitStringUtil::extractWebPath(), true, true);
			}
		}
		
		
		// Version
		$version = sprintf(
			'%s/v%d.%d.%d-%s',
			AgaviConfig::get('de.icinga.appkit.version.name'),
			AgaviConfig::get('de.icinga.appkit.version.major'),
			AgaviConfig::get('de.icinga.appkit.version.minor'),
			AgaviConfig::get('de.icinga.appkit.version.patch'),
			AgaviConfig::get('de.icinga.appkit.version.extension')
		);
		AgaviConfig::set('de.icinga.appkit.version.release', $version, true, true);
		
		return true;
	}
	
	/**
	 * Helper method
	 * Require Doctrine from icinga.xml
	 * @return boolean
	 */
	private static function initDoctrineOrm() {
		// Try to include doctrine
		if (file_exists($doctrine_dir = AgaviConfig::get('de.icinga.appkit.doctrine_path'))) {
			require_once($doctrine_dir. '/Doctrine.php');
		}
		else {
			// Cool, not really exists, leave a message :-)
			throw new AppKitException("Doctrine ('$doctrine_dir') not found!");
		}
		
		return true;
	}
	
	
	/**
	 * Helper method
	 * Overwrite php init settings e.g. for sessions, paths, ...
	 * @return boolean
	 */
	private static function initPhpConfiguration() {
		// Applying PHP settings
		if (is_array($settings = AgaviConfig::get('de.icinga.appkit.php_settings'))) {
			foreach ($settings as $ini_key=>$ini_val) {
				if (ini_set($ini_key, $ini_val) === false) {
					throw new AppKitException("Could not set ini setting $ini_key to '$ini_val'");
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Helper method
	 * Add custom events from icinga.xml to our event stack
	 * @return boolean
	 */
	private static function initEventHandling() {
		// Register additional events from config file
		if (is_array($events = AgaviConfig::get('de.icinga.appkit.custom_events'))) {
			AppKitEventDispatcher::registerEventClasses($events);
		}
		
		return true;
	}

	/**
	 * 
	 */
	private static function setLanguageDomain() {
		try {
			$context = AgaviContext::getInstance(AgaviConfig::get('core.default_context'));
			$user = $context->getUser();	
			if($user) {		
				$user = $user->getNsmUser(true);
			}
		
			if(!$user)
				return true;
		
			$translationMgr = $context->getTranslationManager();		
			$locale = $user->getPrefVal("de.icinga.appkit.locale",$translationMgr->getDefaultLocaleIdentifier());
			try {
				$translationMgr->setLocale($locale);
			} catch(Exception $e) {
				$translationMgr->setLocale($translationMgr->getDefaultLocaleIdentifier());
			}
			return true;
		
		} catch(AppKitDoctrineException $e) {
			return true;	
		}
	}
}

?>