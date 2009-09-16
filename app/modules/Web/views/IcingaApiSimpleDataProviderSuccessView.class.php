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
		$jsonData = array();
		$model = $this->getContext()->getModel('IcingaApiSimpleDataProvider', 'Web');

		$srcId = $rd->getParameter('src_id');
		$filter = $rd->getParameter('filter');

		$jsonData = $model->setSourceId($srcId)->setFilter($filter)->fetch();

		// store final count
		//$jsonData['status_data']['count'] = count($jsonData['status_data']['data']);

		return json_encode($jsonData);

	}

}

?>