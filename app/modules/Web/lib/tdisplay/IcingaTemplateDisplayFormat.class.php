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
	
	public function formatDate($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		$source = $method_params->getParameter('source');
		$format = $method_params->getParameter('format');
		
		if (!$format) return $val;
		
		$date = null;
		
		if ($source && !preg_match('@iso@', $source)) {
			
		}
		else {
			$date = strtotime($val);
		}
		
		return date($format, $date);
	}
	
	public function agaviDateFormat($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		$tm = $this->getAgaviTranslationManager();
		return $tm->_d($val, $method_params->getParameter('domain', 'date-tstamp'));
	}
	
	public function durationString($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
		return "LLL";
	}
}
?>