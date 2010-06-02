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
		$this->initializeEventHandling();
		$this->setLanguageDomain();
		
		parent::initialize();
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
				$locale = $user->getPrefVal("de.icinga.appkit.locale",$translationMgr->getDefaultLocaleIdentifier(),true);
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