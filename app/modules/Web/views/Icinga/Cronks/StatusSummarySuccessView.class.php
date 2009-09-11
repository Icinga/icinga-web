<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_Icinga_Cronks_StatusSummarySuccessView extends ICINGAWebBaseView
{

	private $objects = array('host', 'service');

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