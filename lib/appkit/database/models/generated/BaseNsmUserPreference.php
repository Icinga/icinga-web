<?php

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
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
abstract class BaseNsmUserPreference extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('nsm_user_preference');
        $this->hasColumn('upref_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('upref_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('upref_val', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('upref_longval', 'blob', null, array(
             'type' => 'blob',
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('upref_key', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('upref_created', 'timestamp', null, array(
             'type' => 'timestamp',
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('upref_modified', 'timestamp', null, array(
             'type' => 'timestamp',
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
    $this->hasOne('NsmUser', array(
             'local' => 'upref_user_id',
             'foreign' => 'user_id'));
    }
    
	public function get($val) {
		$val = parent::get($val);
		if(is_resource($val))
			return stream_get_contents($val);
		return $val;
	}
}