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


class AppKitDoctrineDatabase extends AgaviDoctrineDatabase {
    private static $doctrineloaded = false;
    private static $doctrineFile = array(
            "debug" => "lib/Doctrine.php",
            "compressed" => "Doctrine.compiled.php"
    );
    
    private $use_retained = false;
    
    public function initialize(AgaviDatabaseManager $databaseManager, array $parameters = array()) {
        if (!self::$doctrineloaded) {
            $this->initializeDoctrine();
        }

        parent::initialize($databaseManager, $parameters);
        $this->connection->setPrefix($this->getParameter('prefix',""));

        if ($this->getParameter('caching')) {
            $this->initializeCache();
        }
        
        if ($this->getParameter('use_retained')) {
            $this->use_retained = true;
        }
    }
    
    /**
     * Getter for use retained flag
     * @todo Check for connection switching
     * @return boolean The flag
     */
    public function useRetained() {
        return $this->use_retained;
    }


    /**
     * Require doctrine orm
     */
    private function initializeDoctrine() {
        $type = "debug";

        if (AgaviConfig::get('modules.appkit.doctrine_use_compressed')) {
            $type = "compressed";
        }

        if (file_exists(($path = AgaviConfig::get('modules.appkit.doctrine_path')))) {
            $file = self::$doctrineFile[$type];
            require_once($path. '/'.$file);
        }

        if (!class_exists('Doctrine')) {
            throw new AppKitException('Could not include doctrine!');
        }

        self::$doctrineloaded = true;
    }

    private function initializeCache() {
        $param = $this->getParameter('caching');

        if (empty($param)) {
            return;
        }

        if (isset($param['enabled'])) {
            if (!$param['enabled']) {
                return;
            }
        } else {
            return;
        }

        $cache = null;

        if (isset($param['driver'])) {
            $cache = $this->setupQueryCache($param);
        }

        if (!$cache) {
            return;
        }

        // setup query cache
        if (isset($param['use_query_cache']) && $param['use_query_cache']) {
            $this->connection->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cache);
        }

        // setup result cache
        if (isset($param['use_result_cache']) && $param['use_result_cache']) {
            $this->connection->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cache);

            if (isset($param['result_cache_lifespan'])) {
                $this->connection->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, $param['result_cache_lifespan']);
            }
        }

    }


    private function setupQueryCache($param) {
        $type = strtoupper($param['driver']);

        switch ($type) {
            case 'APC':
                return new Doctrine_Cache_Apc();

            case 'MEMCACHE':
                if (!isset($param['memcache_host']) ||
                        !isset($param['memcache_port'])) {
                    return null;
                }

                $server = array(
                        'host' => $param['memcache_host'],
                        'port' => $param['memcache_port'],
                        'persistent' => true
                );
                return new Doctrine_Cache_Memcache(array(
                                'servers' => $server,
                                'compression' => false
                ));
            
            default:
                return null;
        }
    }
}
