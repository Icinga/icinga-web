<?php

class Reporting_Provider_ContentResourceSuccessView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.ContentResource');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    
	    $client = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array (
	        'jasperconfig' => $rd->getParameter('jasperconfig')
	    ));
	    
	    $resource = $this->getContext()->getModel('ContentResource', 'Reporting', array (
	        'jasperconfig' => $rd->getParameter('jasperconfig'),
	        'client' => $client,
	        'uri' => $rd->getParameter('uri')
	    ));
	    
	    if ($rd->getParameter('action') == 'meta') {
	        $data = $resource->getMetaData();
	    }
	    
	    return json_encode($rd);
	}
}

?>