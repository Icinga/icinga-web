<?php

class LConf_LDAPResultModel extends IcingaLConfBaseModel
{


	

	protected function formatToPropertyArray(array $arr) {
		$returnArray = array();
		foreach($arr as $attribute=>$value) {
			if(!is_array($value)) 
				continue;
			
			$valueCount = $value["count"];
			if($valueCount == 1) {
				$returnArray[$attribute] = $value[0];
			} else {
				$returnArray[$attribute] = array();
				for($i=0;$i<$valueCount;$i++) {
					$returnArray[$attribute][] = $value[$i];
				}
			}			
 		}

 		return $returnArray;
	}
	
	
}

?>