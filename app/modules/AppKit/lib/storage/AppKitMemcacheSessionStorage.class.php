<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

class AppKitMemcacheSessionStorage extends AgaviSessionStorage {

    /**
     * @var NsmSession
     */
    private $NsmSession = null;
    private $host = "localhost";
    private $port = 11211;
    private $prefix;

    /**
     * @var Memcache
     */
    private $memcache = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {

        // initialize the parent
        parent::initialize($context, $parameters);
        $this->host = isset($parameters["host"]) ? $parameters["host"] : 'localhost';
        $this->port = isset($parameters["port"]) ? $parameters["port"] :  11211;
        session_set_save_handler(
            array(&$this, 'sessionOpen'),
            array(&$this, 'sessionClose'),
            array(&$this, 'sessionRead'),
            array(&$this, 'sessionWrite'),
            array(&$this, 'sessionDestroy'),
            array(&$this, 'sessionGC')
        );

    }

    public function sessionClose() {
        // Hm, the same as sessionOpen?!

    }

    /**
     * Trigger the sesstion destroy and remove
     * data from database
     * @param string $id
     */
    public function sessionDestroy($id) {
        memcache_delete($this->memcache,$this->prefix.$this->getParameter('session_name').":".$id);
        return true;
    }

    /**
    Memcache has a expire value
     * @param integer $lifetime
     */
    public function sessionGC($lifetime) {
        /**/
        return true;
    }

    /**
     * Trigger to open the session
     * @param string $path
     * @param string $name
     */
    public function sessionOpen($path, $name) {
        $this->prefix = $path.$name;
        $this->memcache = memcache_pconnect($this->host,$this->port);
    }

    /**
     * Reads data from doctrine tables and return its content
     * @param string $id
     * @throws AppKitDoctrineSessionStorageException
     */
    public function sessionRead($id) {
        $session = memcache_get($this->memcache,$this->prefix.$this->getParameter('session_name').":".$id);
        if(!$session) {
            memcache_add($this->memcache,$this->prefix.$this->getParameter('session_name').":".$id,"");
            return '';
        }

        return $session;

    }

    /**
     * Writes session data to database tables
     * @param string $id
     * @param mixed $data
     */
    public function sessionWrite($id, &$data) {
        memcache_set($this->memcache,$this->prefix.$this->getParameter('session_name').":".$id,$data);
    }

}

class AppKitDoctrineSessionStorageException extends AppKitException {}
