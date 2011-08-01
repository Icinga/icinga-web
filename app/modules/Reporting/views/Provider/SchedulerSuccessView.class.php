<?php

class Reporting_Provider_SchedulerSuccessView extends ReportingBaseView {
    
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.Scheduler');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    $data = array (
	        'success' => (bool)$this->getAttribute('success', false)
	    );
	    
	    if ($this->hasAttribute('error')) {
	        $data['error'] = $this->getAttribute('error');
	    }
	    
	    return json_encode($data);
	}
}

?>