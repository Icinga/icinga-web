<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusSummarySuccessView extends ICINGACronksBaseView
{

	private $objectDefs = array(
		'host'		=> array (
			'column'	=> 'host_state',
			'target'	=> IcingaApi::TARGET_HOST_STATUS_SUMMARY,
		),
		'service'		=> array (
			'column'	=> 'service_state',
			'target'	=> IcingaApi::TARGET_SERVICE_STATUS_SUMMARY,
		),
	);

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

		$model = $this->getContext()->getModel('System.StatusSummary', 'Cronks');
		$api = AppKitFactories::getInstance()->getFactory('IcingaData');

		foreach ($this->objectDefs as $objectKey => $objectData) {

			// get object data
			$result = $api->API()->createSearch()
				->setSearchTarget($objectData['target'])
				->fetch();

			// process object data
			$model->init($objectKey);
			foreach ($result as $row) {
				$model->addData($row->{$objectData['column']}, $row->count);
			}
			$jsonData['status_data']['data'] = array_merge($jsonData['status_data']['data'], $model->getStatusData());

		}

		// store final count
		$jsonData['status_data']['count'] = count($jsonData['status_data']['data']);

		return json_encode($jsonData);
	}

}

?>