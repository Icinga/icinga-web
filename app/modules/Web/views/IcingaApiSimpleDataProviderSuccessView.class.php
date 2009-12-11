<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_IcingaApiSimpleDataProviderSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'IcingaApiSimpleDataProvider');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {

		// init
		$jsonData = array(
			'result'	=> array (
				'count'	=> 0,
				'data'	=> array(),
			),
		);
		$model = $this->getContext()->getModel('IcingaApiSimpleDataProvider', 'Web');

		$srcId = $rd->getParameter('src_id');
		$filter = $rd->getParameter('filter');

		$result = $model->setSourceId($srcId)->setFilter($filter)->fetch();

		$tm = $this->getContext()->getTranslationManager();

		foreach ($result as $row) {
			foreach ($result->getRow() as $key => $value) {
				if ($key == 'HOST_CURRENT_STATE') {
					$value = IcingaHostStateInfo::Create($value)->getCurrentStateAsText();
				} 
				elseif ($key == 'SERVICE_CURRENT_STATE') {
					$value = IcingaServiceStateInfo::Create($value)->getCurrentStateAsText();
				}
				elseif (strpos($key, 'URL') !== false && AppKitStringUtil::detectUrl($value)) {
					$value = AppKitXmlTag::create('a', $value)
					->addAttribute('href', $value)
					->addAttribute('target', '_blank')
					->toString();
				}
				elseif (strpos($key, 'URL') !== false && !$value) {
					continue;
				}
				
				$dataTmp = array (
					'key'	=> $tm->_($key),
					'value'	=> $value,
				);
				array_push($jsonData['result']['data'], $dataTmp);
			}
		}

		// store final count and convert
		$jsonData['result']['count'] = count($jsonData['result']['data']);
		$jsonDataEnc = json_encode($jsonData);

		return $jsonDataEnc;

	}

}

?>