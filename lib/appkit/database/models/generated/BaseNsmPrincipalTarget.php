<?php

/**
 * BaseNsmPrincipalTarget
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $pt_id
 * @property integer $pt_principal_id
 * @property integer $pt_target_id
 * @property NsmPrincipal $NsmPrincipal
 * @property NsmTarget $NsmTarget
 * @property Doctrine_Collection $NsmTargetValue
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
abstract class BaseNsmPrincipalTarget extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('nsm_principal_target');
        $this->hasColumn('pt_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('pt_principal_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('pt_target_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
    $this->hasOne('NsmPrincipal', array(
             'local' => 'pt_principal_id',
             'foreign' => 'principal_id'));

        $this->hasOne('NsmTarget', array(
             'local' => 'pt_target_id',
             'foreign' => 'target_id'));

        $this->hasMany('NsmTargetValue', array(
             'local' => 'pt_id',
             'foreign' => 'tv_pt_id'));
    }
}