<?php
/**
 * The replacement agavi context to handle all bootstrap things
 * in that
 */
class AppKitAgaviContext extends AgaviContext {
	/**
	 * (non-PHPdoc)
	 * @see lib/agavi/src/core/AgaviContext#initialize()
	 */
	public function initialize() {

		// Fill in the context
		AppKitAgaviUtil::initContext($this);

		/*
		 * Make our settings ready
		 * before run agavi
		 */
		$this->buildVersionString();
		$this->initializePhpSettings();
		$this->initializeDoctrine();
		$this->initializeModules();
		
		parent::initialize();
		
		$this->initializeAutosettings();
		
		$this->initializeExceptionHandling();
	}
	
	/**
	 * Our own exception handler created here
	 */
	private function initializeExceptionHandling() {
		AppKitExceptionHandler::initializeHandler();
	}
	
	/**
	 * Load all needed modules
	 */
	private function initializeModules() {
		(array)$list = AgaviConfig::get('org.icinga.appkit.init_modules', array());

		if (array_search('AppKit', $list) == false) {
			AppKitAgaviUtil::initializeModule('AppKit');
		}
		
		foreach ($list as $mod_name) {
			AppKitAgaviUtil::initializeModule($mod_name);
		}


	}
	
	/**
	 * Require doctrine orm
	 */
	private function initializeDoctrine() {
		if (!class_exists('Doctrine')) {
			if (file_exists(($path = AgaviConfig::get('modules.appkit.doctrine_path')))) {
				require_once ($path. '/Doctrine.php');
			}
		}
		
		if (!class_exists('Doctrine')) {
			throw new AppKitException('Could not include doctrine!');
		}
	}
	
	/**
	 * Inject some dynamic settings into AgaviConfig
	 */
	private function initializeAutosettings() {
		// Try to set the web path to correct urls within the frontend
		if(AgaviConfig::get('core.default_context') =='web') {
			// Try to set the web path to correct urls within the frontend
			if (AgaviConfig::get('org.icinga.appkit.web_path', null) == null) {
				AgaviConfig::set('org.icinga.appkit.web_path', AppKitStringUtil::extractWebPath(), true, true);
			}
		}
		
		// Global temp directory
		AgaviConfig::set('core.tmp_dir', AgaviConfig::get('core.data_dir'). '/tmp');
	}

	/**
	 * Glue our version string together
	 */
	private function buildVersionString() {
		if (AgaviConfig::get('org.icinga.version.extension', false) == false) {
			$version_format = "%s/v%d.%d.%d";
		}
		else {
			$version_format = "%s/v%d.%d.%d-%s";
		}

		AgaviConfig::set('org.icinga.version.release', sprintf(
			$version_format,
			AgaviConfig::get('org.icinga.version.name'),
			AgaviConfig::get('org.icinga.version.major'),
			AgaviConfig::get('org.icinga.version.minor'),
			AgaviConfig::get('org.icinga.version.patch'),
			AgaviConfig::get('org.icinga.version.extension')
		), true, true);
	}
	
	/**
	 * Change PHP settings at runtime
	 * @throws AppKitException
	 */
	private function initializePhpSettings() {
		// Applying PHP settings
		if (is_array($settings = AgaviConfig::get('modules.appkit.php_settings'))) {
			foreach ($settings as $ini_key=>$ini_val) {
				if (ini_set($ini_key, $ini_val) === false) {
					throw new AppKitException("Could not set ini setting $ini_key to '$ini_val'");
				}
			}
		}
	}

}
