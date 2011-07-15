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
	    
	    $resource->doJasperRequest();
	    
	    return json_encode(array (
	    	'success' => true,
	    	'count' => count(($data=$resource->getMetaData())),
	    	'data' => $data
	    ));
	}
	
	public function executeSimple(AgaviRequestDataHolder $rd) {
	    
	    $client = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array (
	    	        'jasperconfig' => $rd->getParameter('jasperconfig')
	    ));
	     
	    $resource = $this->getContext()->getModel('ContentResource', 'Reporting', array (
	    	        'jasperconfig' => $rd->getParameter('jasperconfig'),
	    	        'client' => $client,
	    	        'uri' => $rd->getParameter('uri')
	    ));
	    
	    $resource->doJasperRequest();
	    
	    $m = $data=$resource->getMetaData();
	    
	    if ($m['has_attachment'] && $m['download_allowed']) {
	        $this->getResponse()->setHttpHeader('content-length', $m['content_length'], true);
	        $this->getResponse()->setHttpHeader('content-type', $m['content_type'], true);
	        return $resource->getContent();
	    }
	    
	    return null;
	}
}

?>