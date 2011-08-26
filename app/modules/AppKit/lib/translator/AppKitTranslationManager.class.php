<?php

/**
 * AppKit implementation of translation manager
 * Load the current user defined locale if needed
 * @author jmosshammer
 *
 */
class AppKitTranslationManager extends AgaviTranslationManager {
    private $__localeLoaded = false;
    public function initialize(AgaviContext $context, array $parameters = array()) {
        $this->context = $context;

		include(AgaviConfigCache::checkConfig(AgaviConfig::get('core.config_dir') . '/translation.xml'));
		$this->loadSupplementalData();
		$this->loadTimeZoneData();
		$this->loadAvailableLocales();
       
       
        
		if($this->defaultLocaleIdentifier === null) {
			throw new AgaviException('Tried to use the translation system without a default locale and without a locale set');
		}
		$this->setLocale($this->defaultLocaleIdentifier);

		if($this->defaultTimeZone === null) {
			$this->defaultTimeZone = date_default_timezone_get();
		} else {
            date_default_timezone_set($this->defaultTimeZone);
        }
        
        
		if($this->defaultTimeZone === 'System/Localtime') {
			// http://trac.agavi.org/ticket/1008
			throw new AgaviException("Your default timezone is 'System/Localtime', which likely means that you're running Debian, Ubuntu or some other Linux distribution that chose to include a useless and broken patch for system timezone database lookups into their PHP package, despite this very change being declined by the PHP development team for inclusion into PHP itself.\nThis pseudo-timezone, which is not defined in the standard 'tz' database used across many operating systems and applications, works for internal PHP classes and functions because the 'real' system timezone is resolved instead, but there is no way for an application to obtain the actual timezone name that 'System/Localtime' resolves to internally - information Agavi needs to perform accurate calculations and operations on dates and times.\n\nPlease set a correct timezone name (e.g. Europe/London) via 'date.timezone' in php.ini, use date_default_timezone_set() to set it in your code, or define a default timezone for Agavi to use in translation.xml. If you have some minutes to spare, file a bug report with your operating system vendor about this problem.\n\nIf you'd like to learn more about this issue, please refer to http://trac.agavi.org/ticket/1008");
		}
        
    }
    
    public function loadCurrentLocale() {
        if ($this->__localeLoaded) {
            return parent::loadCurrentLocale();
        }

        $user = $this->getContext()->getUser();

        if (!$user || !($user instanceof AppKitSecurityUser)) {
            return null;
        }

        try {

            $dbUser = $user->getNsmUser(true);

            $translation = $this->getContext()->getTranslationManager();

            if ($dbUser instanceof NsmUser) {
                $langDomain = $dbUser->getPrefVal("org.icinga.appkit.locale");

                if ($langDomain) {
                    $translation->setLocale($langDomain);
                }
            }

        } catch (Exception $e) {
            // ignore
        }

        $this->__localeLoaded = true;
        parent::loadCurrentLocale();
    }
}