<?php

class Web_Icinga_Cronks_StatusSummarySuccessView extends ICINGAWebBaseView
{

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.StatusSummary');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {

		// init
		$jsonData = array (
			'status_data'	=> array (
				'count'	=> 0,
				'data'	=> array(),
			),
		);

		$model = $this->getContext()->getModel('Icinga.Cronks.StatusSummary', 'Web');
		$api = AppKitFactories::getInstance()->getFactory('IcingaData');

		// get host data
		$result = $api->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST_STATUS_SUMMARY)
			->fetch();

		// process host data
		$statesSet = array();
		$dataCollection = array();
		foreach ($result as $row) {
			$state = (int)$row->host_state;
			$count = (int)$row->count;
			$data = $model->getStatusDataCollection('host', $state, $count);
			$dataCollection[$state] = $data;
			array_push($statesSet, $state);
		}
		for ($state = 0; $state < 3; $state++) {
			if (!in_array($state, $statesSet)) {
				$data = $model->getStatusDataCollection('host', $state);
				$dataCollection[$state] = $data;
			}
			array_push($jsonData['status_data']['data'], $dataCollection[$state]);
		}

		// get service data
		$result = $api->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_SERVICE_STATUS_SUMMARY)
			->fetch();

		// process service data
		$statesSet = array();
		$dataCollection = array();
		foreach ($result as $row) {
			$state = (int)$row->service_state;
			$count = (int)$row->count;
			$data = $model->getStatusDataCollection('service', $state, $count);
			$dataCollection[$state] = $data;
			array_push($statesSet, $state);
		}
		for ($state = 0; $state < 4; $state++) {
			if (!in_array($state, $statesSet)) {
				$data = $model->getStatusDataCollection('service', $state);
				$dataCollection[$state] = $data;
			}
			array_push($jsonData['status_data']['data'], $dataCollection[$state]);
		}

		// store final count
		$jsonData['status_data']['count'] = count($jsonData['status_data']['data']);

		return json_encode($jsonData);
	}

}

?>