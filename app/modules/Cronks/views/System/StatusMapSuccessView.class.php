<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusMapSuccessView extends CronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'System.StatusMap');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {

		$model = $this->getContext()->getModel('System.StatusMap', 'Cronks');

		$jsonData = $model->getParentChildStructure();

		return trim(json_encode($jsonData), '[]');
	}

}

?>