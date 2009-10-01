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
		//var_dump(array($jsonData, $jsonDataEnc));

		return $jsonDataEnc;

	}

}

?>