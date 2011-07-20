<?php

class Reporting_Provider_SchedulerSuccessView extends ReportingBaseView {
    
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.Scheduler');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    
	}
}

?>