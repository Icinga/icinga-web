<?php
class IcingaTemplateDisplayFormat extends IcingaTemplateDisplay {

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}

	/**
	 * You can give a format to return a custom string
	 * @param $val
	 * @param $method_params
	 * @param $row
	 * @return unknown_type
	 */
	public function formatTemplate($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		
		$parser = new AppKitFormatParserUtil();
		$parser->setDefault($val);
		
		$parser->registerNamespace('field', AppKitFormatParserUtil::TYPE_ARRAY);
		$parser->registerData('field', $row->getParameters());
		
		return $parser->parseData($method_params->getParameter('format', '${*}'));
	}
}
?>