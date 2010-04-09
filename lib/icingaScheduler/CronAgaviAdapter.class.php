<?php
define("PATH_TO_AGAVI",dirname(__FILE__)."/../agavi/src/agavi.php");
define("PATH_TO_CONFIG",dirname(__FILE__)."/../../app/config.php");

class CronAgaviAdapter {
	protected $agaviPath = PATH_TO_AGAVI;
	protected $configPath = PATH_TO_CONFIG;
	private $__isLoaded = false;
	
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new self();		
		}
		return self::$instance;
	}
	
	protected function __construct() {	
		if(!$__isLoaded)
			$this->bootstrap();
	}
	
	public function getConfigVar($name) {
		return AgaviConfig::get($name,null);
	}

	public function getDBConnection() {
		return AgaviContext::getInstance()->getDatabaseManager();
	}
	
	private function bootstrap() {
		require_once($this->agaviPath);
		require_once($this->configPath);
		Agavi::bootstrap('development');
		AgaviConfig::set('core.default_context', 'console');
		AppKit::bootstrap();
		
	}
	private static $instance = 0;
	
}