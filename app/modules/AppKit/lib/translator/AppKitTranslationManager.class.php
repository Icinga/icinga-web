<?php

/**
 * AppKit implementation of translation manager
 * Load the current user defined locale if needed
 * @author jmosshammer
 *
 */
class AppKitTranslationManager extends AgaviTranslationManager {
    private $__localeLoaded = false;

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