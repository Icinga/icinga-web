<?php

class AppKitAgaviContext extends AgaviContext {

	/**
	 * (non-PHPdoc)
	 * @see lib/agavi/src/core/AgaviContext#initialize()
	 */
	public function initialize() {
		/*
		 * Make our settings ready
		 * before run agavi
		 */
		$this->initializePhpSettings();
		$this->initializeDoctrine();
		$this->initializeAutosettings();
		$this->initializeModules();
		$this->initializeEventHandling();
		$this->setLanguageDomain();
		
		parent::initialize();
	}

	/**
	 * Load all needed modules
	 */
	private function initializeModules() {
		(array)$list = AgaviConfig::get('org.icinga.appkit.init_modules', array());

		if (array_search('AppKit', $list) == false) {
			AgaviController::initializeModule('AppKit');
		}
		
		foreach ($list as $mod_name) {
			AgaviController::initializeModule($mod_name);
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
	
	private function initializeAutosettings() {
		// Try to set the web path to correct urls within the frontend
		if(AgaviConfig::get('core.default_context') =='web') {
			// Try to set the web path to correct urls within the frontend
			if (AgaviConfig::get('org.icinga.appkit.web_path') == null) {
				AgaviConfig::set('org.icinga.appkit.web_path', AppKitStringUtil::extractWebPath(), true, true);
			}
		}
		
		// Version
		$version = sprintf(
			'%s/v%d.%d.%d-%s',
			AgaviConfig::get('org.icinga.appkit.version.name'),
			AgaviConfig::get('org.icinga.appkit.version.major'),
			AgaviConfig::get('org.icinga.appkit.version.minor'),
			AgaviConfig::get('org.icinga.appkit.version.patch'),
			AgaviConfig::get('org.icinga.appkit.version.extension')
		);
		AgaviConfig::set('org.icinga.appkit.version.release', $version, true, true);
	}
	
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
	
	private function initializeEventHandling() {
		// Register additional events from config file
		if (is_array($events = AgaviConfig::get('modules.appkit.custom_events'))) {
			AppKitEventDispatcher::registerEventClasses($events);
		}
		
		return true;
	}
	
	private static function setLanguageDomain() {
		return true;
		try {
			$context = AgaviContext::getInstance(AgaviConfig::get('core.default_context'));
			$user = $context->getUser();	
			if($user) {		
				$user = $user->getNsmUser(true);
			}
		
			if(!$user)
				return true;
		
			$translationMgr = $context->getTranslationManager();
					
			try {
				$locale = $user->getPrefVal("org.icinga.appkit.locale",$translationMgr->getDefaultLocaleIdentifier(),true);
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