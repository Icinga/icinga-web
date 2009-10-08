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
				if ($key == 'host_current_state') {
					$value = IcingaHostStateInfo::Create($value)->getCurrentStateAsText();
				} elseif ($key == 'service_current_state') {
					$value = IcingaServiceStateInfo::Create($value)->getCurrentStateAsText();
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