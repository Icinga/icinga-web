<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusSummarySuccessView extends ICINGACronksBaseView
{

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

		// fetch process object data
		$jsonData['status_data']['data'] = $model->init($rd->getParameter('dtype'))->fetchData()->getStatusData();

		// store final count
		$jsonData['status_data']['count'] = count($jsonData['status_data']['data']);

		//return '{"status_data":{"count":1,"data":[{"OK":52,"UNKNOWN":8,"DOWN":0,"type":"Hosts"}]}}';
		return json_encode($jsonData);
	}

}

?>
