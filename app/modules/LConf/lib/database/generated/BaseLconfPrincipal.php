<?php
Doctrine_Manager::getInstance()->bindComponent('LconfPrincipal', 'icinga_web');
abstract class BaseLconfPrincipal extends Doctrine_Record
{
	
    public function setTableDefinition()
    {
        $this->setTableName('lconf_principal');
        $this->hasColumn('principal_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true
             ));
        $this->hasColumn('principal_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false
             ));
        $this->hasColumn('principal_role_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false
             ));
        $this->hasColumn('connection_id','integer',4,array(
        	'type' => 'integer',
        	'length' => 4,
        	'fixed' => false,
        	'unsigned' => false,
        	'primary' => false,
        	'notnull' => false,
        	'autoincrement' => false
        ));

    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('NsmUser', array(
             'local' => 'principal_user_id',
             'foreign' => 'user_id',
        ));

        $this->hasOne('NsmRole', array(
             'local' => 'principal_role_id',
             'foreign' => 'role_id'
        ));

        $this->hasOne('LconfConnection', array(
             'local' => 'connection_id',
             'foreign' => 'connection_id',
             'onDelete' => 'CASCADE',
        	 'onUpdate' => 'CASCADE'
        ));
    }
}
