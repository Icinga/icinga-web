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