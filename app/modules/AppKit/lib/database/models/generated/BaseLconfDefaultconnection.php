<?php

/**
 * BaseLconfFilters
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $filter_id
 * @property integer $user_id
 * @property string $filter_name
 * @property string $filter_json
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLconfDefaultconnection extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('lconf_defaultconnection');
        $this->hasColumn('defaultconnection_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('connection_id', 'integer',4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
          ));
       	$this->index('defaultconn_unique', array (
			'fields' => array (
				'user_id'
			),
			'type' => 'unique'
		));
    }

	public function setUp()
    {
        parent::setUp();
        $this->hasOne('NsmUser', array(
             'local' => 'user_id',
             'foreign' => 'user_id',
        	 'onDelete' => 'CASCADE',
        	 'onUpdate' => 'CASCADE'
	        )
        );
        $this->hasOne('LconfConnection', array(
             'local' => 'connection_id',
             'foreign' => 'connection_id',
        	 'onDelete' => 'CASCADE',
        	 'onUpdate' => 'CASCADE'
	        )
        );
    }
}