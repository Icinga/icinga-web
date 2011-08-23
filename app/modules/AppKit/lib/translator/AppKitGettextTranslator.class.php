<?php

/**
 * AppKit implementation of the gettext translation  - whooo
 * Just to hook in the JSON js gettext translation on client
 * side
 * @author mhein
 *
 */
class AppKitGettextTranslator extends AgaviGettextTranslator {

    /**
     * Provide this information public to read the json compiled
     * client files
     * @return array
     */
    public function getDomainPaths() {
        return $this->domainPaths;
    }
    
    public function getDomainPathPattern() {
        return $this->domainPathPattern;
    }
}

?>