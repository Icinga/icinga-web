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

Doctrine_Manager::getInstance()->bindComponent('NsmDbVersion', 'icinga_web');
/**
 * Icinga web table version
 */

abstract class BaseNsmDbVersion extends Doctrine_Record {

    public function setTableDefinition() {

        $this->setTableName('nsm_db_version');
        $this->hasColumn("id", 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => true,
                             'autoincrement' => false
        ));
        $this->hasColumn("version", 'string', 32, array(
                             'type' => 'string',
                             'length' => 32,
                             'fixed' => false,
                             'unsigned' => false,
                             'autoincrement' => false,
                             'notnull' => true
        ));
        $this->hasColumn('modified', 'timestamp', null, array(
                             'type' => 'datetime',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false
        ));
        $this->hasColumn('created', 'timestamp', null, array(
                             'type' => 'datetime',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false
        ));
    }

    public static function getInitialData() {
        return array(
            array('id'=>'1','version'=>AgaviConfig::get('org.icinga.version.release'))
        );
    }

    public function setUp() {
        parent::setUp();
    }
}
