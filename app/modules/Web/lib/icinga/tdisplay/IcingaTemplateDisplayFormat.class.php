<?php
class IcingaTemplateDisplayFormat extends IcingaTemplateDisplay {

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}

	public function formatTemplate($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		return "FORMAT";
	}
}
?>