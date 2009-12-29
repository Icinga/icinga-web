<?php

class IcingaPrincipalTargetTool {
	
	public static function applyApiSecurityPrincipals(array $models, IcingaApiSearchIdo &$search) {
		$user = AgaviContext::getInstance()->getUser()->getNsmUser();
		
		$sarr = $user->getTargetValuesArray();
		$parts = array ();
		foreach ($models as $model) {
			if (isset($sarr[$model])) {
				$to = $user->getTarget($model)->getTargetObject();
				if (count($sarr[$model]) > 0) {
					foreach ($sarr[$model] as $vdata) {
						$parts[] = $to->getMapArray($vdata);
					}
				}
				else {
					$map = $to->getCustomMap();
					if ($map !== false) {
						$parts[] = $map;
					}
				}
				
			}
		}
		
		if (count($parts) > 0) {
			$query = join(' OR ', $parts);
			$search->setSearchFilterAppendix($query, IcingaApi::SEARCH_AND);
			
			return true;
		}
		
		return false;
	}
	
}

?>