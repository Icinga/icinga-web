<?php
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
