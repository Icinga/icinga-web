<?php

class Reporting_Provider_SchedulerJobView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.Scheduler');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
			'jasperconfig' => $rd->getParameter('jasperconfig')
	    ));
	    
	    $scheduler = $this->getContext()->getModel('JasperScheduler', 'Reporting', array (
	    	'client' => $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_SCHEDULER),
            'jasperconfig' => $rd->getParameter('jasperconfig'),
            'uri' => $rd->getParameter('uri')
	    ));
	    
	    $parameters = $this->getContext()->getModel('JasperParameterStruct', 'Reporting', array (
	    	'client' => $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY),
	        'jasperconfig' => $rd->getParameter('jasperconfig'),
	        'uri' => $rd->getParameter('uri'),
	        'filter' => 'inputControl'
	    ));
	    
	    $stdClass = new stdClass();
	    
	    if ($rd->hasParameter('job')) {
	        $stdClass->job = $scheduler->getJobDetail($rd->getParameter('job'));
	    } else {
	        $stdClass->job = null;
	    }
	    
	    $stdClass->inputControls = $parameters->getJsonStructure();
	    
	    return json_encode($stdClass);
	}
}

?>