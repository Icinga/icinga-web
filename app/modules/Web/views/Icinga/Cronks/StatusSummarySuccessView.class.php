<?php

class Web_Icinga_Cronks_StatusSummarySuccessView extends ICINGAWebBaseView
{

	private $jsonData = array (
		'status_data'	=> array (
			'count'	=> 0,
			'data'	=> array(),
		),
	);

	private $dataStates = array (
		'host'		=> array (
			0 => 'OK',
			1 => 'UNKNOWN',
			2 => 'DOWN',
		),
		'service'	=> array (
			0 => 'OK',
			1 => 'WARNING',
			2 => 'CRITICAL',
			3 => 'UNKNOWN',
		),
	);

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.StatusSummary');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {

		// init
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
			$data = $this->getStatusDataCollection('host', $state, $count);
			$dataCollection[$state] = $data;
			array_push($statesSet, $state);
		}
		for ($state = 0; $state < 3; $state++) {
			if (!in_array($state, $statesSet)) {
				$data = $this->getStatusDataCollection('host', $state);
				$dataCollection[$state] = $data;
			}
			array_push($this->jsonData['status_data']['data'], $dataCollection[$state]);
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
			$data = $this->getStatusDataCollection('service', $state, $count);
			$dataCollection[$state] = $data;
			array_push($statesSet, $state);
		}
		for ($state = 0; $state < 4; $state++) {
			if (!in_array($state, $statesSet)) {
				$data = $this->getStatusDataCollection('service', $state);
				$dataCollection[$state] = $data;
			}
			array_push($this->jsonData['status_data']['data'], $dataCollection[$state]);
		}

		// store final count
		$this->jsonData['status_data']['count'] = count($this->jsonData['status_data']['data']);

		return json_encode($this->jsonData);
	}

	public function getStatusDataCollection ($type, $state, $count = 0) {
		$data = array (
			'state_id'		=> $state,
			'state_name'	=> $this->dataStates[$type][$state],
			'count'			=> $count,
			'type'			=> $type,
		);
		return $data;
	}
}

?>