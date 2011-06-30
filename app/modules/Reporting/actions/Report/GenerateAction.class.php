<?php

class Reporting_Report_GenerateAction extends ReportingBaseAction {
    public function getDefaultViewName() {
        return 'Success';
    }
    
    public function executeRead(AgaviParameterHolder $rd) {
        return $this->executeWrite($rd);
    }
    
    public function executeWrite(AgaviParameterHolder $rd) {
        
        $data = (array)json_decode($rd->getParameter('data', ""));
        unset($data['_output_format']);
        
        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array (
            'jasperconfig' => $rd->getParameter('jasperconfig')
        ));
        
        $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);
        
        $parameters = $this->getContext()->getModel('JasperParameterStruct', 'Reporting', array (
        	        'client'    => $client,
        	        'uri'       => $rd->getParameter('uri'),
        	        'filter'	=> 'reportUnit' 
        ));
        
        $reports = $parameters->getObjects();
        
        $creator = $this->getContext()->getModel('ReportGenerator', 'Reporting', array(
            'client' => $client,
            'report' => $reports[0],
            'format' => $rd->getParameter('output_type'),
            'parameters' => $data
        ));
        
        
        try {
            $data = $creator->getReportData();
            
            $userFile = $this->getContext()->getModel('ReportUserFile', 'Reporting');
            
            $userFile->storeFile($data, $rd->getParameter('output_type'), $reports[0]);
            $this->setAttribute('success', true);
        } catch (AppKitModelException $e) {
            $this->setAttribute('success', false);
            $this->setAttribute('error', $e->getMessage());
        }
        
        return $this->getDefaultViewName();
    }
    
    public function isSecure() {
        return true;
    }
    
    public function getCredentials() {
        return array ('icinga.user');
    }
    
    public function handleError(AgaviRequestDataHolder $rd) {
        $this->setAttribute('success', false);
        $this->setAttribute('error', 'Validation failed');
        return $this->getDefaultViewName();
    }
}

?>