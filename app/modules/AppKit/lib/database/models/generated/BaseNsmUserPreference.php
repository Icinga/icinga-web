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

Doctrine_Manager::getInstance()->bindComponent('NsmUserPreference', 'icinga_web');

/**
 * BaseNsmUserPreference
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $upref_id
 * @property integer $upref_user_id
 * @property string $upref_val
 * @property blob $upref_longval
 * @property string $upref_key
 * @property timestamp $upref_created
 * @property timestamp $upref_modified
 * @property NsmUser $NsmUser
 *
 * @package    IcingaWeb
 * @subpackage AppKit
 * @author     Icinga Development Team <info@icinga.org>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseNsmUserPreference extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('nsm_user_preference');
        $this->hasColumn('upref_id', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => true,
                             'autoincrement' => true,
                         ));
        $this->hasColumn('upref_user_id', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('upref_val', 'string', 100, array(
                             'type' => 'string',
                             'length' => 100,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('upref_longval', 'clob', null, array(
                             'type' => 'clob',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('upref_key', 'string', 50, array(
                             'type' => 'string',
                             'length' => 50,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('upref_created', 'timestamp', null, array(
                             'type' => 'timestamp',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('upref_modified', 'timestamp', null, array(
                             'type' => 'timestamp',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));

        $this->index('upref_search_key_idx', array('fields' => array('upref_key')));
        $this->index('principal_role_id_ix', array('fields' => array('upref_user_id')));

        $this->index('upref_user_key_unique_idx', array(
                         'fields' => array(
                             'upref_user_id',
                             'upref_key',
                         ),
                         'type' => 'unique'
        ));

    }

    public function setUp() {
        parent::setUp();
        $this->hasOne('NsmUser', array(
                          'local' => 'upref_user_id',
                          'foreign' => 'user_id',
                          'onDelete' => 'CASCADE',
                          'onUpdate' => 'CASCADE'
                      ));
    }

    public function set($name,$value,$load = true) {
        if ($col = $this->getTable()->getColumnDefinition($name)) {
            if ($col["type"] == 'blob') {
                $value = base64_encode($value);
            }
        }

        parent::set($name,$value,$load);
    }

    public function get($column, $load=true) {
        $val = parent::get($column, $load);

        if (is_resource($val)) {
            $val = stream_get_contents($val);
        }

        if ($col = $this->getTable()->getColumnDefinition($column)) {
            if ($col["type"] == 'blob') {
                $val = base64_decode($val);
            }
        }

        return $val;
    }

}
