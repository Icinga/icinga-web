<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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
 * BaseIcingaContactgroups
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $contactgroup_id
 * @property integer $instance_id
 * @property integer $config_type
 * @property integer $contactgroup_object_id
 * @property string $alias
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseIcingaContactgroups extends Doctrine_Record {
    public function setTableDefinition() {
        $conn = $this->getTable()->getConnection();
        if(!$conn)
            $conn = Doctrine_Manager::getInstance()->getConnection(IcingaDoctrineDatabase::CONNECTION_ICINGA);
        $prefix = $conn->getPrefix();
        $this->setTableName($prefix.'contactgroups');
        $this->hasColumn('contactgroup_id', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'autoincrement' => true,
                         ));
        $this->hasColumn('instance_id', 'integer', 2, array(
                             'type' => 'integer',
                             'length' => 2,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '0',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('config_type', 'integer', 2, array(
                             'type' => 'integer',
                             'length' => 2,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '0',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('contactgroup_object_id', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => true,
                             'default' => '0',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('alias', 'string', 255, array(
                             'type' => 'string',
                             'length' => 255,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
    }

    public function setUp() {
        $this->hasOne('IcingaInstances as instance', array(
                          'local' => 'instance_id',
                          'foreign' => 'instance_id'
                      ));


        $this->hasMany('IcingaHosts as hosts', array(
                           'local' => 'contactgroup_object_id',
                           'foreign' => 'host_id',
                           'foreignId' => 'host_id',
                           'refClass' => 'IcingaHostContactgroups',
                           'idField' => 'contactgroup_object_id'

                       ));
        $this->hasMany('IcingaServices as services', array(
                           'local' => 'contactgroup_object_id',
                           'foreign' => 'service_id',
                           'foreignId' => 'service_id',
                           'refClass' => 'IcingaServiceContactgroups',
                           'idField' => 'contactgroup_object_id'
                       ));

        $this->hasMany('IcingaContacts as members', array(
                           'local' => 'contactgroup_id',
                           'foreign' => 'contact_object_id',
                           'refClass' => 'IcingaContactgroupMembers',
                           'idField' => 'contactgroup_id'
                       ));
        $this->hasOne('IcingaObjects as object', array(
                          'local' => 'contactgroup_object_id',
                          'foreign' => 'object_id'
                      ));
        parent::setUp();

    }
}
