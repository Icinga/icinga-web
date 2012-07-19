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


class IcingaDoctrineDatabase extends AppKitDoctrineDatabase {

    const CONNECTION_ICINGA = 'icinga';
    const CONNECTION_WEB    = 'icinga_web';

    public static $icingaConnections = array(
        
    );
    /**
     * When working with icinga objects and multiple addon databases
     * this method ensures that you're working on the right space!
     */
    public static function resetCurrentConnection() {
        Doctrine_Manager::getInstance()->setCurrentConnection(self::CONNECTION_ICINGA);
    }
    
    public function initialize(AgaviDatabaseManager $databaseManager, array $parameters = array()) {
        parent::initialize($databaseManager, $parameters);
        self::$icingaConnections[] = $this->getName();
    }
    
    /**
     * Moved to AppKitDoctrineDatabase
     * 
     * This is not the right place but a quick fix for connection
     * switching problems
     */
    // public function useRetained() {}
}

?>
