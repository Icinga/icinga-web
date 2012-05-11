<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
            throw new AgaviException("Your default timezone is 'System/Localtime', which will causes problems with icinga-web date function."
                   ."Please set  date.timezone in your php.ini or set a default timezone in the app/config/translations.xml file of icinga-web.");
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