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

Doctrine_Manager::getInstance()->bindComponent('CronkCategory', 'icinga_web');
/**
 * BaseCronkCategory
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $cc_id
 * @property string $cc_uid
 * @property string $cc_name
 * @property integer $cc_visible
 * @property integer $cc_position
 * @property boolean $cc_system
 * @property timestamp $cc_created
 * @property timestamp $cc_modified
 * @property Doctrine_Collection $CronkCategoryCronk
 * @property Doctrine_Collection $CronkPrincipalCategory
 * @property Doctrine_Collection $principals
 *
 * @package    IcingaWeb
 * @subpackage AppKit
 * @author     Icinga Development Team <info@icinga.org>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCronkCategory extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('cronk_category');
        $this->hasColumn('cc_id', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'fixed' => false,
                'unsigned' => false,
                'primary' => true,
                'autoincrement' => true,
        ));
        $this->hasColumn('cc_uid', 'string', 45, array(
                'type' => 'string',
                'length' => 45,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_name', 'string', 45, array(
                'type' => 'string',
                'length' => 45,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_visible', 'integer', 1, array(
                'type' => 'integer',
                'length' => 1,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'default' => '0',
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_position', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'default' => '0',
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_system', 'boolean', 4, array(
                'type' => 'boolean',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'default' => false,
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_created', 'timestamp', null, array(
                'type' => 'datetime',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('cc_modified', 'timestamp', null, array(
                'type' => 'datetime',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
        ));
    }

    public function setUp() {
        parent::setUp();
        $this->hasMany('CronkCategoryCronk', array(
                'local' => 'cc_id',
                'foreign' => 'ccc_cc_id'));

        $this->hasMany('CronkPrincipalCategory', array(
                'local' => 'cc_id',
                'foreign' => 'category_id'));

        $this->hasMany('NsmPrincipal as principals', array(
                'local' => 'category_id',
                'foreign' => 'principal_id',
                'refClass' => 'CronkPrincipalCategory'
        ));

    }
}
