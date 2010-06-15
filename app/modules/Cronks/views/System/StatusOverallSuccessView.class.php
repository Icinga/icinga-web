<?php

class Cronks_System_StatusOverallSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
	}

	public function executeJson(AgaviRequestDataHolder $rd) {
		$model = $this->getContext()->getModel('System.StatusOverall', 'Cronks');
		return (string)$model->getJson();
	}
}

?>