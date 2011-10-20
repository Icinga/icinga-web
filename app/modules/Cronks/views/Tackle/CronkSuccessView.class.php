<?php

class Cronks_Tackle_CronkSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Tackle.Cronk');
	}
}

?>