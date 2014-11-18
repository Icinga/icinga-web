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

abstract class IcingaApiDoctrineRecord extends Doctrine_Record {

    public function __construct($table = null, $isNewEntry = false) {
        $dbalmm = AgaviContext::getInstance()->getModel("Api_DBALMetaManagerModel","Api");
        $db = $dbalmm->getCurrentDB();
        Doctrine_Manager::getInstance()->bindComponent($this->get, $db);
        parent::__construct($table, $isNewEntry);
    }
}
?>
