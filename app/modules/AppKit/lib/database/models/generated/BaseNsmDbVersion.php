<?php
Doctrine_Manager::getInstance()->bindComponent('NsmDbVersion', 'icinga_web');
/**
 * Icinga web table version
 */

abstract class BaseNsmDbVersion extends Doctrine_Record {

    public function setTableDefinition() {

        $this->setTableName('nsm_db_version');
        $this->hasColumn("vers_id", 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => true,
                             'autoincrement' => false
                         ));
        $this->hasColumn("version", 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'autoincrement' => false
                         ));
    }

    public static function getInitialData() {
        return array(
                   array('vers_id'=>'1','version'=>'2'),
               );
    }

    public function setUp() {
        parent::setUp();
    }
}
