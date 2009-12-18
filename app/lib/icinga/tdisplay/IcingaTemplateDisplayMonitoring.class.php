<?php

class IcingaTemplateDisplayMonitoring extends IcingaTemplateDisplay {

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}
	
	public function serviceStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		return (string)IcingaServiceStateInfo::Create($val)->getCurrentStateAsHtml();
	}
	
	public function hostStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		return (string)IcingaHostStateInfo::Create($val)->getCurrentStateAsHtml();
	}
	
	public function icingaConstants($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		$type = $method_params->getParameter('type');
		$ref = new ReflectionClass('IcingaConstantResolver');
		
		if (($m = $ref->getMethod($type))) {
			if ($m->isPublic() && $m->isStatic()) {
				return  $this->getAgaviTranslationManager()->_($m->invoke(null, $val));
			}
		}
		
		return $val;
	}
}

?>