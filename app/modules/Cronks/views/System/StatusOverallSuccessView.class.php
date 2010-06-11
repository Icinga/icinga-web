<?php

class Cronks_System_StatusOverallSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'System.StatusOverall');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {
		$model = $this->getContext()->getModel('System.StatusOverall', 'Cronks');
		$model->getJson();
	}
}

?>