<?php

class Reporting_Provider_JasperServerSuccessView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.JasperServer');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    
	    $jasper = $this->getContext()->getModel('JasperSoapClient', 'Reporting', array(
	        'jasperconfig' => $rd->getParameter('jasperconfig')
	    ));
	    
	    
	    return (json_encode(array ('PING' => $jasper->pingServer())));
	}
}

?>