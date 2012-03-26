<?php

class LConf_Admin_LConfPrincipalAdminModel extends IcingaLConfBaseModel {
	static public $fieldMap = array(
		"users" => "user_id",
		"groups" => "role_id"
	);
	
	public function addPrincipals($connection_id,$target,$values) {

		$values = json_decode($values,true);
	
		if(!is_array(@$values[0]))
			$values = array($values);
		$this->removeExistingPrincipalsFromValues($connection_id,$target,$values);
		$field = self::$fieldMap[$target];
		foreach($values as $valueToInsert) {
			$entry = new LconfPrincipal();
			$entry->set("principal_".$field,$valueToInsert[$field]);
			$entry->set("connection_id",$connection_id);
			$entry->save();
		}
	}

	public function removeExistingPrincipalsFromValues($connection_id,$target,&$values) {
		// check if principal already exists
		
		$currentPrincipals = Doctrine_Query::create()->select("*")
				->from("LconfPrincipal lconf")
				->andWhere("connection_id = ?",$connection_id)->execute()->toArray();

		// check which principals already exist
		$deletionMark = array();
		$field = self::$fieldMap[$target];
		
		foreach($values as $nr=>&$value) {
			$pKey = $value[$field];
			foreach($currentPrincipals as $principal) {
				if($pKey == $principal["principal_".$field]) {
					$deletionMark[] = $nr;
					break;
				}
			} 
		}

		foreach($deletionMark as $mark) {
			unset($values[$mark]);
		}
	
	}
	
	public function removePrincipals($connection_id,$target,$values) {
		$field = self::$fieldMap[$target];
		$values = json_decode($values);
		if(!is_array($values))
			$values = array($values);
		
		$currentPrincipals = Doctrine_Query::create()->select("*")
				->from("LconfPrincipal lconf")
				->whereIn("lconf.principal_".$field,$values)
				->andWhere("lconf.connection_id = ?",$connection_id)->execute();
		

		foreach($currentPrincipals as $principal)
			$principal->delete();
	}

}