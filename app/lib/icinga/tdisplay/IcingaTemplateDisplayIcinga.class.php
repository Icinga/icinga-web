<?php

class IcingaTemplateDisplayIcinga extends IcingaTemplateDisplay {
	
	private $image_path = '/images/status';
	
	private $service_fields = array (
		'SERVICE_NOTIFICATIONS_ENABLED' => array (
			false	=> array ('ndisabled.png', 'Notifications disabled')
		),
		
		'SERVICE_ACTIVE_CHECKS_ENABLED,SERVICE_PASSIVE_CHECKS_ENABLED' => array (
			false	=> array ('off.png', 'Service disabled')
		),
		
		'SERVICE_ACTIVE_CHECKS_ENABLED' => array (
			false	=> array ('passive.png', 'Passive only')
		),
		
		'SERVICE_CURRENT_STATE,SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array (
			true	=> array ('acknowledged.png', 'Problem has been acknowledged')
		)
	);
	
	private $host_fields = array (
		'HOST_NOTIFICATIONS_ENABLED' => array (
			false	=> array ('ndisabled.png', 'Notifications disabled')
		),
		
		'HOST_ACTIVE_CHECKS_ENABLED,HOST_PASSIVE_CHECKS_ENABLED' => array (
			false	=> array ('off.png', 'Service disabled')
		),
		
		'HOST_ACTIVE_CHECKS_ENABLED' => array (
			false	=> array ('passive.png', 'Passive only')
		),
		
		'HOST_CURRENT_STATE,HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array (
			true	=> array ('acknowledged.png', 'Problem has been acknowledged')
		)
	);
	
	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}
	
	public function serviceStatusIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		$id = $this->getObjectId($method_params->getParameter('field', null), $row);
		
		$dh = $this->getIcingaApi()->createSearch()
		->setSearchTarget(IcingaApi::TARGET_SERVICE)
		->setResultColumns($this->getFields($this->service_fields))
		->setSearchFilter('SERVICE_OBJECT_ID', $id);
		
		return $this->buildIcons($dh, $this->service_fields);
	}
	
	public function hostStatusIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		$id = $this->getObjectId($method_params->getParameter('field', null), $row);
		
		$dh = $this->getIcingaApi()->createSearch()
		->setSearchTarget(IcingaApi::TARGET_HOST)
		->setResultColumns($this->getFields($this->host_fields))
		->setSearchFilter('HOST_OBJECT_ID', $id);
		
		return $this->buildIcons($dh, $this->host_fields);
	}
	
	private function getFields(array $a) {
		$out = array ();
		foreach ($a as $k=>$garbage) {
			$values = split(',', $k);
			$out = array_merge($out, $values);
		}
		
		return array_unique($out);
	}
	
	private function getBooleanVal($key, array $row) {
		$keys = split(',', $key);
		$val = false;
		
		foreach ($keys as $sk) {
			if (array_key_exists($sk, $row)) {
				$val |= (bool)$row[$sk];
			}
		}
		return (bool)$val;
	}
	
	private function keyExists($key, array $row) {
		$keys = split(',', $key);
		$out = array_intersect($keys, array_keys($row));
		if (count($out)) {
			return true;
		}
		
		return false;
	}
	
	private function buildIcons(IcingaApiSearch &$dh, array $mapping) {
		$out = null;
		
		foreach ($dh->fetch() as $res) {
			$row = (array)$res->getRow();
			
			foreach ($mapping as $fkey=>$fm) {
				if ($this->keyExists($fkey, $row)) {
					$val = $this->getBooleanVal($fkey, $row);
					
					if (isset($fm[$val])) {
						$i = $fm[$val];
						$tag = AppKitXmlTag::create('img');
						
						if (isset($i[0])) {
							$tag->addAttribute('src', $this->wrapImagePath($this->image_path). '/'. $i[0]);
						}
						
						if (isset($i[1])) {
							$tag->addAttribute('alt', $i[1])
							->addAttribute('title', $i[1]);
						}
						
						$out .= (string)$tag;
					}
				}
			}
			
		}
		
		return $out;
	}
	
	private function getObjectId($field_name, AgaviParameterHolder $row) {

		if ($row->hasParameter($field_name)) {
			return $row->getParameter($field_name);
		}
		
		return null;
		
	}
	
	/**
	 * Returns a unique instance of the icinga api connection
	 * @return IcingaApiConnectionIdo
	 */
	private function getIcingaApi() {
		return AppKitFactories::getInstance()->getFactory('IcingaData')->API();
	}
	
}

?>