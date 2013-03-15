<?php
define("PATH_TO_AGAVI",dirname(__FILE__)."/../agavi/src/agavi.php");
define("PATH_TO_CONFIG",dirname(__FILE__)."/../../app/config.php");
define("PATH_TO_DOCTRINE",dirname(__FILE__)."/../../lib/doctrine/lib/Doctrine.php");
define("PATH_TO_DOCTRINE_MODELS",dirname(__FILE__)."/../../app/modules/AppKit/lib/database/models");
/**
 * Singleton class that will is used as a communication interface with agavi
 * Loads the bootstrap 
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class CronAgaviAdapter {
	/**
	 * The oath where the agavi.php file lies
	 * @var String
	 */
	protected $agaviPath = PATH_TO_AGAVI;
	/**
	 * The path where the config.php file lies
	 * @var String
	 */
	protected $configPath = PATH_TO_CONFIG;

	protected $__isLoaded = false;

	
	/**
	 * Returns the current instance of CronAgaviAdapter
	 * @return CronAgaviAdapter
	 */
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new self();		
		}
		return self::$instance;
	}
	
	/**
	 * Creates a new adapter and starts the bootstrap
	 */
	protected function __construct() {	
		if(!$this->__isLoaded)
			$this->bootstrap();
	}
	
	/**
	 * Returns a Agavi config entry $name
	 * @param String $name
	 * @return mixed The config entry or null if not set
	 */
	public function getConfigVar($name) {
		return AgaviConfig::get($name,null);
	}

	/**
	 * Returns the database manager used by agavi
	 *
	 * @return AgaviDatabaseManager
	 */
	public function getDBConnection() {
		return AgaviContext::getInstance()->getDatabaseManager()->getDatabase('icinga_web');
	}
	
	/**
	 * Loads the agavi and appkit bootstrap
	 * TODO:   the appkit bootstrap is not really needed, the console context
	 * 		   uses it's factories. Perhaps a "cron" context can be created to minimize
	 * 		   memory usage and optimize performance
	 *  
	 */
	private function bootstrap() {
		require_once($this->agaviPath);
		require_once($this->configPath);
	
		// +---------------------------------------------------------------------------+
        // Setting the running context to web ...
        Agavi::bootstrap('development');
        AgaviConfig::set('core.default_context', 'console');

        AgaviController::initializeModule('AppKit');

        AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');
        AppKitAgaviContext::getInstance();

	}
	
	/**
	 * The current instance of CronAgaviAdapter
	 * @var CronAgaviAdapter
	 */
	private static $instance = 0;
	
}
