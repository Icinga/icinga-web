<?php
/**
 * BaseNsmPrincipalTarget
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $pt_id
 * @property integer $pt_principal_id
 * @property integer $pt_target_id
 * @property NsmTarget $NsmTarget
 * @property NsmPrincipal $NsmPrincipal
 * @property Doctrine_Collection $NsmTargetValue
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseNsmPrincipalTarget extends Doctrine_Record
{
    
	public function setTableDefinition()
    {
        $this->setTableName('nsm_principal_target');
        $this->hasColumn('pt_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('pt_principal_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('pt_target_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
             
        $this->index('pt_target_id_ix',array('fields'=>array('pt_target_id')));
        $this->index('pt_principal_id_ix',array('fields'=>array('pt_principal_id')));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('NsmTarget', array(
             'local' => 'pt_target_id',
             'foreign' => 'target_id',
           	 'onDelete' => 'CASCADE',
        	 'onUpdate' => 'CASCADE'
        ));

        $this->hasOne('NsmPrincipal', array(
             'local' => 'pt_principal_id',
             'foreign' => 'principal_id',
           	 'onDelete' => 'CASCADE',
        	 'onUpdate' => 'CASCADE'));

        $this->hasMany('NsmTargetValue', array(
             'local' => 'pt_id',
             'foreign' => 'tv_pt_id'));
    }
    
    public static function getInitialData() {
		return array(
			array('pt_id'=>'1','pt_principal_id'=>'2','pt_target_id'=>'8'),
			array('pt_id'=>'2','pt_principal_id'=>'2','pt_target_id'=>'13'),
			array('pt_id'=>'3','pt_principal_id'=>'3','pt_target_id'=>'9'),
			array('pt_id'=>'4','pt_principal_id'=>'3','pt_target_id'=>'10'),
			array('pt_id'=>'5','pt_principal_id'=>'3','pt_target_id'=>'11'),
			array('pt_id'=>'6','pt_principal_id'=>'4','pt_target_id'=>'8'),
			array('pt_id'=>'7','pt_principal_id'=>'5','pt_target_id'=>'7')
		);
    }
}