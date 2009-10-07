<?php

class AppKitAutoloader extends AppKitSingleton {
	
	const MAX_TRIES				= 2;
	const CACHE_FACTORY_NAME	= 'CacheProvider';
	const CACHE_REGION_NAME		= 'appkit.cache.autoloader';
	
	private static $pattern_file = array (
		'@([\w\d\_\-\.]+)\.(class|interface)\.php@'
	);
	
	private static $pattern_name = null;
	
	private static $pattern_array = array (
		
	);
	
	private static $search_ignore = '@\.svn@';
	
	private static $search_array = array (
	
	);
	
	/**
	 * Returns a singleton instance if AppKitAutoloader
	 * @return AppKitAutoloader
	 */
	public static function getInstance() {
		return parent::getInstance('AppKitAutoloader');
	}

	private $classes_collected = array ();
	
	private $classes_required = array ();
	
	public static function addSearchPath($path) {
		if (file_exists($path)) {
			self::$search_array[] = $path;
			return true;
		}
		
		throw new AppKitAutoloaderException("Path '$path' does not exist!");
		
	}
	
	public function __construct() {
		parent::__construct();
		
		if (is_array($classes = $this->getCache()->getValue('classes', null, self::CACHE_REGION_NAME))) {
			$this->classes_collected = $classes;
		}
	}
	
	public function registerAutoloadRequests() {
		return spl_autoload_register(array(&$this, 'autoLoad'));
	}
	
	public function autoLoad($className) {
		if ($this->isValidClass($className)) {
			$file = $this->findClassfile($className);
			
			if (file_exists($file)) {
				require_once($file);
				return true;
			}
			
		}
		
		return false;
		
	}
	
	private function isValidClass($className) {
		if (preg_match($this->getNamePattern(), $className)) return true;
		
		return false;
	}
	
	private function findClassfile($className, $nosearch=false, $nesting=0) {
		
		// Try to find the file within the cache!
		if (is_array($this->classes_collected) && count($this->classes_collected)) {
			if (array_key_exists($className, $this->classes_collected)) {
				return $this->classes_collected[$className];
			}
			return false;
		}
		
		// Prevent nesting call errors
		if ($nesting > self::MAX_TRIES) {
			trigger_error('More than '. self::MAX_TRIES. ' find calls, thats to much!', E_USER_ERROR);
		}
		
		// Maybe not, collecting all together
		foreach (self::$search_array as $path) {
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
			foreach ($iterator as $fileInfo) {
				$file = $this->getSplFileInfoPath($fileInfo);
				if (!preg_match(self::$search_ignore, $file)) {
					foreach (self::$pattern_file as $pattern) {
						$m = array ();
						if (preg_match($pattern, $fileInfo->getFilename(), $m)) {
							$this->classes_collected[$m[1]] = $this->getSplFileInfoPath($fileInfo);
						}
					}
				}
			}
		}
		
		// Add collected classes to the cache
		$this->getCache()->setValue('classes', $this->classes_collected, self::CACHE_REGION_NAME);
		
		return $this->findClassfile($className, false, $nesting+1);
	}
	
	private function getSplFileInfoPath(SplFileInfo $file) {
		return $file->getPath(). '/'. $file->getFilename();
	}
	
	private function getNamePattern() {
		if (self::$pattern_name === null) {
			self::$pattern_name = sprintf('@^(%s)@', implode('|', self::$pattern_array));
		}
		
		return self::$pattern_name;
	}
	
	public static function addNamePrefix($prefix) {
		self::$pattern_array[] = $prefix;
	}
	
	/**
	 * This is our cache object to speedup class loading
	 * @return AppKitCache
	 */
	private function getCache() {
		static $cache = null;
		
		if ($cache === null) {
			$cache =& AppKitFactories::getInstance()->getFactory(self::CACHE_FACTORY_NAME);
			$cache->addRegion(self::CACHE_REGION_NAME);
		}
		
		return $cache;
	}
}

class AppKitAutoloaderException extends AppKitException { }

?>