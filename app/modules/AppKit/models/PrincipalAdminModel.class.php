<?php

class AppKit_PrincipalAdminModel extends ICINGAAppKitBaseModel
{
	
	public function __construct() {
		
	}
	
	public function getTargetArray() {
		
		$out = array ();
		
		foreach (Doctrine::getTable('NsmTarget')->findAll() as $r) {
			
			$out[$r->target_name] = array (
				'name'			=> $r->target_name,
				'description'	=> $r->target_description,
				'type'			=> $r->target_type,
				'fields'		=> array ()
			);
			
			foreach ($r->getTargetObject()->getFields() as $fname=>$fdesc) {
				$out[$r->target_name]['fields'][$fname] = $fdesc;
			}
			
		}
		
		return $out;
	}
	
}

?>