<?php

class AppKitDoctrineDatabase extends AgaviDoctrineDatabase {
    private static $doctrineloaded = false;
    private static $doctrineFile = array(
                                       "debug" => "lib/Doctrine.php",
                                       "compressed" => "Doctrine.compiled.php"
                                   );
    public function initialize(AgaviDatabaseManager $databaseManager, array $parameters = array()) {
        if (!self::$doctrineloaded) {
            $this->initializeDoctrine();
        }

        parent::initialize($databaseManager, $parameters);
        $this->connection->setPrefix($this->getParameter('prefix',""));

        if ($this->getParameter('caching')) {
            $this->initializeCache();
        }
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
