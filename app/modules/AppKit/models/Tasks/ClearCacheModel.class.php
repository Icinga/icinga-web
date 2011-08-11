<?php

/**
 * Export agavi's clear cache method outbound
 * @author mhein
 *
 */
class AppKit_Tasks_ClearCacheModel extends AppKitBaseModel {

    private static $additionalCacheDirs = array('CronkTemplates', 'Squished');

    /**
     * Trigger cache clearing and give notice
     */
    public function clearCache() {
        $this->clearAdditionalCache();
        $this->clearAgaviCache();
        return true;
    }

    private function clearAgaviCache() {
        AgaviToolkit::clearCache();
        $this->log('Agavi cache cleared through webinterface', AgaviLogger::INFO);
    }

    private function clearAdditionalCache() {
        $cacheDir = AgaviConfig::get('core.cache_dir');
        foreach(self::$additionalCacheDirs as $sub) {
            AppKitFileUtil::rmdir($cacheDir. DIRECTORY_SEPARATOR. $sub);
            $this->log('Cleared sub cache %s from webinterface', $sub, AgaviLogger::INFO);
        }
    }

}