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

		foreach ($result as $row) {
			$dataTmp = array();
			foreach ($result->getRow() as $key => $value) {
				$dataTmp[$key] = $value;
			}
			array_push($jsonData['result']['data'], $dataTmp);
		}

		// store final count
		$jsonData['result']['count'] = count($jsonData['result']['data']);
		//var_dump($jsonData);

		return json_encode($jsonData);

	}

}

?>