<?php

class IcingaTemplateDisplayServiceIcons extends IcingaTemplateDisplay {
	
	const COND_ERROR = 0xffff;
	
	private $image_path = '/images/status';
	
	private static $service_fields = array (
		'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_ACTIVE_CHECKS_ENABLED',
		'SERVICE_PASSIVE_CHECKS_ENABLED', 'SERVICE_CURRENT_STATE',
		'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED'
	);
	
	private static $service_conditions = array (
		'${field.SERVICE_NOTIFICATIONS_ENABLED}' => array (
			false	=> array ('ndisabled.png', 'Notifications disabled')
		),
		
		'!${field.SERVICE_ACTIVE_CHECKS_ENABLED} && !${field.SERVICE_PASSIVE_CHECKS_ENABLED}' => array (
			true	=> array ('off.png', 'Service disabled')
		),
		
		'!${field.SERVICE_ACTIVE_CHECKS_ENABLED} && ${field.SERVICE_PASSIVE_CHECKS_ENABLED}' => array (
			true	=> array ('passive.png', 'Passive only')
		),
		
		'${field.SERVICE_CURRENT_STATE} && ${field.SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED}' => array (
			true	=> array ('acknowledged.png', 'Problem has been acknowledged')
		)
	);
	
	private static $host_fields = array (
		'HOST_NOTIFICATIONS_ENABLED', 'HOST_ACTIVE_CHECKS_ENABLED',
		'HOST_PASSIVE_CHECKS_ENABLED', 'HOST_CURRENT_STATE',
		'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'
	);
	
	private static $host_conditions = array (
		'${field.HOST_NOTIFICATIONS_ENABLED}' => array (
			false	=> array ('ndisabled.png', 'Notifications disabled')
		),
		
		'${field.HOST_ACTIVE_CHECKS_ENABLED} && ${field.HOST_PASSIVE_CHECKS_ENABLED}' => array (
			false	=> array ('off.png', 'Service disabled')
		),
		
		'!${field.HOST_ACTIVE_CHECKS_ENABLED} && ${field.HOST_PASSIVE_CHECKS_ENABLED}' => array (
			true	=> array ('passive.png', 'Passive only')
		),
		
		'${field.HOST_CURRENT_STATE} && ${field.HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED}' => array (
			true	=> array ('acknowledged.png', 'Problem has been acknowledged')
		)
	);
	
	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}
	
	public function serviceIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		$id = $this->getObjectId($method_params->getParameter('field', null), $row);
		
		$dh = $this->getIcingaApi()->createSearch()
		->setSearchTarget(IcingaApi::TARGET_SERVICE)
		->setResultColumns(self::$service_fields)
		->setSearchFilter('SERVICE_OBJECT_ID', $id);
		
		return $this->buildIcons($dh, self::$service_conditions);
	}
	
	public function hostIcons($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		$id = $this->getObjectId($method_params->getParameter('field', null), $row);
		
		$dh = $this->getIcingaApi()->createSearch()
		->setSearchTarget(IcingaApi::TARGET_HOST)
		->setResultColumns(self::$host_fields)
		->setSearchFilter('HOST_OBJECT_ID', $id);
		
		return $this->buildIcons($dh, self::$host_conditions);
	}
	
	private function buildIcons(IcingaApiSearch &$dh, array $mapping) {
		$out = null;
		
		$parser = new AppKitFormatParserUtil();
		$parser->registerNamespace('field', AppKitFormatParserUtil::TYPE_ARRAY);
		
		foreach ($dh->fetch() as $res) {
			$row = (array)$res->getRow();
			$parser->registerData('field', $row);
			
			foreach ($mapping as $fkey=>$fm) {
				$cond = 'return (int)('. $parser->parseData($fkey). ');';
				
				if (($test = $this->evalCode($cond)) !== self::COND_ERROR) {
					// var_dump(array($fkey, $cond, $test));
					if (isset($fm[$test])) {
						// var_dump(" --> OK");
						$i = $fm[$test];
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
	
	private function evalCode($code) {
		$re = @eval($code);
		if ($re === false) {
			return self::COND_ERROR;
		}
		
		return (bool)$re;
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