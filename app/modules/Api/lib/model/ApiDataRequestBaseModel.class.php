<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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

class ApiDataRequestBaseModel extends IcingaApiBaseModel {
    protected $database = "icinga";
    public function getDatabase() {
        return $this->database;
    }
    public function setDatabase($dbname) {
        $this->database = $dbname;
    }

    public static function applySecurityPrincipals(Doctrine_Query $q) {}
    /**
     * Returns the doctrine connection handler
     * @param String $connName The connection name. Defaults to "icinga" (optional)
         *
     * @return Doctrine_Connection or null
     */
    protected function getDatabaseConnection($connName = NULL) {

        if (!$connName) {
            $connName = $this->database;
        }

        $db = $this->getContext()->getDatabaseManager()->getDatabase($connName);
        $connection = null;

        if ($db) {
            $connection = $db->getConnection();
        }

        return $connection;
    }

    public function createRequestDescriptor($connName = NULL) {
        if (!$connName) {
            $connName = $this->database;
        }

        $DBALMetaManager = $this->getContext()->getModel("DBALMetaManager","Api");

        $DBALMetaManager->switchIcingaDatabase($connName);


        return IcingaDoctrine_Query::create();
    }


}

?>
