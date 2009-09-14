<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusSummarySuccessView extends ICINGACronksBaseView
{

	private $objects = array('host', 'service');

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'System.StatusSummary');
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

		foreach ($this->objects as $object) {
			// fetch process object data
			$statusData = $model->init($object)->fetchData()->getStatusData();
			$jsonData['status_data']['data'] = array_merge($jsonData['status_data']['data'], $statusData);
		}

		// store final count
		$jsonData['status_data']['count'] = count($jsonData['status_data']['data']);

		return json_encode($jsonData);
	}

}

?>
