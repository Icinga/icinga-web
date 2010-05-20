<?php

define("STORAGE_FILE",dirname(__FILE__)."/res/storage.dat");

/**
 * Singleton that provides and updates meta data about the cronjobs 
 * (last execution and result)
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class CronJobMetaProvider {
	private $storage = null;
	private static $semaphore = 0;
	
	public function getStorage() {
		return $this->storage;
	}
	public function setStorage($storage) {
		$this->storage = $storage;
	}
	
	static public function  processFinished() {
		self::$semaphore--;
		if(!self::$semaphore) {
			self::getInstance()->saveStorage();
		}
	}
	
	static public function processStarted() {
		self::$semaphore++;
	}

	private function loadStorage() {
		$storage = null; 
		if(file_exists(STORAGE_FILE)) {
			$storage = file_get_contents(STORAGE_FILE);
			$storage = unserialize($storage);
		}
		if(!$storage)
			$storage = array();
		$this->setStorage($storage);
	}
	
	public function saveStorage() {
		$storage = $this->getStorage();
		$serialized = serialize($storage);
		file_put_contents(STORAGE_FILE,$serialized);
	}
	
	public static function getInstance() {
		if(self::$instance == null)
			self::$instance = new self();
		return self::$instance;
	}
	
	private function __construct() {
		$this->loadStorage();
	}
	
	static private $instance = 0;
	

}
