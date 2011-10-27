<?php

class Api_GenericErrorView extends IcingaApiBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'ApiCommand');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    $out = array(
	        'success' => $this->container->getAttribute('success', 'org.icinga.api.auth'),
	        'errors' => $this->container->getAttribute('errors', 'org.icinga.api.auth', array())
	    );
	    
	    return json_encode($out);
	}
	
	public function executeXml(AgaviRequestDataHolder $rd) {
	    $dom = new DOMDocument('1.0', 'utf-8');
	    $root = $dom->createElement('error');
	    $dom->appendChild($root);
	    
	    $errors = $this->container->getAttribute('errors', 'org.icinga.api.auth', array());
	    foreach ($errors as $error) {
	        $root->appendChild($dom->createElement('message', $error));
	    }
	     
	    return $dom->saveXML();
	}
}