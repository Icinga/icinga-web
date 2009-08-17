<?php

class IcingaTemplateDisplayMonitoring extends IcingaTemplateDisplay {

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}
	
	public function serviceStatus($val, AgaviParameterHolder $method_params) {
		return (string)IcingaServiceStateInfo::Create($val)->getCurrentStateAsHtml();
	}
	
	public function hostStatus($val, AgaviParameterHolder $method_params) {
		return (string)IcingaHostStateInfo::Create($val)->getCurrentStateAsHtml();
	}
	
}

?>