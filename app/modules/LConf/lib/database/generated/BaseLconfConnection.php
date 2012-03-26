<?php
Doctrine_Manager::getInstance()->bindComponent('LconfConnection', 'icinga_web');

abstract class BaseLconfConnection extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('lconf_connection');
        $this->hasColumn('connection_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('connection_name', 'string', 32, array(
             'type' => 'string',
             'length' => 32,
			 'fixed' => false,
             'primary' => false,
             'notnull' => true,
          ));
        $this->hasColumn('connection_description', 'string', 256, array(
             'type' => 'string',
             'length' => 256,
			 'fixed' => false,
             'primary' => false,
             'notnull' => false,
          ));
        $this->hasColumn('owner', 'integer',4, array(
             'type' => 'integer',
             'length' => 4, 
             'primary' => false,
             'notnull' => false,
          ));

		$this->hasColumn('connection_binddn', 'string', 1024, array(
             'type' => 'string',
             'length' => 1024,
			 'fixed' => false,
             'primary' => false,
             'notnull' => true,
          ));
        $this->hasColumn('connection_bindpass', 'string', 64, array(
             'type' => 'string',
             'length' => 256,
			 'fixed' => false,
             'primary' => false,
             'notnull' => false,
        ));
        $this->hasColumn('connection_host', 'string', 64, array(
             'type' => 'string',
             'length' => 128,
			 'fixed' => false,
             'primary' => false,
             'notnull' => true,
             'defaukt' => 'localhost'
          ));
       $this->hasColumn('connection_port', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
			 'fixed' => false,
             'primary' => false,
             'notnull' => true,
       	     'default' => 389
          ));
          $this->hasColumn('connection_basedn', 'string', 1024, array(
             'type' => 'string',
             'length' => 1024,
             'fixed' => false,
             'primary' => false,
             'notnull' => false
             ));
        $this->hasColumn('connection_tls', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'default' => 0     
        ));
		$this->hasColumn('connection_ldaps', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'default' => 0     
        ));
    }

    public function setUp()
    {
		$this->hasOne('NsmUser as user_owner', array(
			'local' => 'owner',
			'foreign' => 'user_id'
		));
    	$this->hasMany('LconfPrincipal as principals', array(
             'local' => 'connection_id',
             'foreign' => 'connection_id'

        ));
        parent::setUp();
    }
}
