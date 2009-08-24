<?php

/**
 * AppKitCache - caching for AppKit
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 * examples:
 * 
 * writing:
 * $cache = new AppKitCache();
 * $cache->setDir('/tmp/')->setFile('cachetest.tmp')->setExpiry(30)->setData('nix')->writeCache();
 *
 * reading and clearing:
 * $cacheNew = new AppKitCache();
 * echo $cacheNew->setDir('/tmp/')->setFile('cachetest.tmp')->setExpiry(30)->getCache());
 * $cacheNew->clearCache();
 *
 * forced reading (won't check expiry):
 * $cacheNew = new AppKitCache();
 * echo $cacheNew->setDir('/tmp/')->setFile('cachetest.tmp')->setExpiry(30)->getCache(true));
 */
class AppKitCache {

	/*
	 * VARIABLES
	 */
	private $config = array (
		'data'		=> false,
		'dir'		=> false,
		'expiry'	=> false,
		'file'		=> false,
	);

	private $cacheRead = false;
	private $cacheFile = false;

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 * @param	array			$config			configuration data (see class variable $config) as associative array
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
	}

	/**
	 * gets config values
	 * @param	string			$key			configuration key (see class variable $config)
	 * @return	mixed							configuration value (false on error)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __get ($key = false) {
		$retVal = false;
		if ($key !== false && array_key_exists($key, $this->config)) {
			$retVal = $this->config[$key];
		} else {
			throw new AppKitCacheException(__CLASS__ . ': get(): Invalid key "' . $key . '"!');
		}
		return $retVal;
	}

	/**
	 * sets config variables
	 * @param	string			$key			configuration key (see class variable $config)
	 * @param	string			$value			configuration value
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __set ($key = false, $value = false) {
		switch ($key) {
			case 'data':
				$this->setData($value);
				break;
			case 'dir':
				$this->setDir($value);
				break;
			case 'expiry':
				$this->setExpiry($value);
				break;
			case 'file':
				$this->setFile($value);
				break;
			default:
				throw new AppKitCacheException(__CLASS__ . ': set(): Invalid key: "' . $key . '"!');
				break;
		}
		return $this;
	}

	/**
	 * sets cache data
	 * @param	string			$data			cache data
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setData ($data = false) {
		$this->config['data'] = $data;
		return $this;
	}

	/**
	 * sets cache dir
	 * @param	string			$dir			cache dir
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setDir ($dir = false) {
		if ($dir !== false && file_exists($dir) && is_dir($dir)) {
			$this->config['dir'] = $dir;
		} else {
			throw new AppKitCacheException(__CLASS__ . ': setDir(): Invalid directory: "' . $dir . '"!');
		}
		return $this;
	}

	/**
	 * sets cache expiry in seconds
	 * @param	string			$seconds		cache expiry in seconds
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setExpiry ($seconds = false) {
		if ($seconds !== false) {
			$this->config['expiry'] = (int)$seconds;
		} else {
			throw new AppKitCacheException(__CLASS__ . ': setExpiry(): Invalid time: "' . $seconds . '"!');
		}
		return $this;
	}

	/**
	 * sets cache file
	 * @param	string			$file			cache file
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setFile ($file = false) {
		if ($file !== false) {
			$this->config['file'] = $file;
		} else {
			throw new AppKitCacheException(__CLASS__ . ': setFile(): Invalid file: "' . $file . '"!');
		}
		return $this;
	}

	/**
	 * sets configuration data via an associative array
	 * @param	array			$config			configuration data (see class variable $config) as associative array
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config = array()) {
		if (!empty($config)) {
			foreach ($config as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			throw new AppKitCacheException(__CLASS__ . ': setConfig(): Invalid data!');
		}
		return $this;
	}

	/**
	 * creates complete file name of cache file and returns it
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							cache-file name
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCacheFileName ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		if ($this->dir !== false && $this->file !== false) {
			$this->cacheFile = $this->dir . '/' . $this->file;
		} else {
			throw new AppKitCacheException(__CLASS__ . ': writeCache(): No cache directory or file set!');
		}
		return $this->cacheFile; 
	}

	/**
	 * reads cache file if not done so far and returns cache data
	 * @param	boolean			$force			forces loading of file even if it expired
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							cache data
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCache ($force = false, $config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		if ($this->cacheRead === false) {
			$this->readCache($force);
		}
		return $this->data;
	}

	/**
	 * reads cache file
	 * @param	boolean			$force			forces loading of file even if it expired
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function readCache ($force = false, $config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$cacheFile = $this->getCacheFileName();
		if (file_exists($cacheFile)) {
			if ($force || !$this->cacheExpired()) {
				$this->data = file_get_contents($cacheFile);
				$this->cacheRead = true;
			}
		}
		return $this;
	}

	/**
	 * clears cache and deletes cache file if it exists
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function clearCache ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$cacheFile = $this->getCacheFileName();
		if (file_exists($cacheFile)) {
			if (is_writable($cacheFile)) {
				unlink($cacheFile);
			} else {
				throw new AppKitCacheException(__CLASS__ . ': clear(): Cannot delete cache file "' . $cacheFile . '"!');
			}
		}
		$this->data = false;
		$this->cacheRead = false;
		return $this;
	}

	/**
	 * writes cache file
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	object							AppKitCache
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function writeCache ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		if ($this->dir !== false) {
			file_put_contents($this->getCacheFileName(), $this->data);
		} else {
			throw new AppKitCacheException(__CLASS__ . ': writeCache(): No cache directory set!');
		}
		return $this;
	}

	/**
	 * reads cache file
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	boolean							true if cache is expired otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
		public function cacheExpired ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$expired = false;
		if ($this->dir !== false && $this-> file !== false) {
			if (time() - filemtime($this->getCacheFileName()) > $this->expiry) {
				$expired = true;
			}
		} else {
			throw new AppKitCacheException(__CLASS__ . ': fileExpired(): No cache directory or file set!');
		}
		return $expired;
	}

}

// add own exceptions
class AppKitCacheException extends Exception {}

?>