<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class NsmRole extends BaseNsmRole
{

	public function setUp () {

		parent::setUp();

		$this->hasMany('NsmUser', array (	'local'		=> 'usro_role_id',
											'foreign'	=> 'usro_user_id',
											'refClass'	=> 'NsmUserRole'));

        $options = array (
        	'created' =>  array('name'	=> 'role_created'),
        	'updated' =>  array('name'	=> 'role_modified'),
        );

		$this->actAs('Timestampable', $options);

	}

}