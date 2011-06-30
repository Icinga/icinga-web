<?php

class Reporting_Report_GenerateSuccessView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
 		
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    $output = array (
	        'success' => $this->getAttribute('success', false)
	    );
	    
	    if ($this->hasAttribute('error')) {
	        $output['errors'] = array (
	            'message' => $this->getAttribute('error', 'Some error')
	        );
	    }
	    
	    return json_encode($output);
	}
}

?>