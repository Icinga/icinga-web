<?php

class Reporting_ContentResourceModel extends JasperConfigBaseModel {
    
    /**
     * @var Reporting_JasperSoapFactoryModel
     */
    private $__client = null;
    
    private $__uri = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__client = $this->getParameter('client');
        
        $this->__uri = $this->getParameter('uri', null);
        
        if (!$this->__client instanceof Reporting_JasperSoapFactoryModel) {
            throw new AppKitModelException('Client must be instance of Reporting_JasperSoapFactoryModel');
        }
        
        if ($this->__uri === null) {
            throw new AppKitModelException('Model must have uri parameter');
        }
    }
    
    /**
     * Fire the request and return the result. Also do a basic security 
     * checking against the configured root path
     * @throws AgaviSecurityException
     * @return JasperResponseXmlDoc
     */
    private function getJasperResource() {
        if ($this->checkUri($this->__uri) == false) {
            throw new AgaviSecurityException('You are not allowed to access '. $this->__uri);
        }
        
        $soap = $this->__client->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);
        
        $request = new JasperRequestXmlDoc('get');
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $this->__uri);
        
        
        $soap->doRequest($request);
        
        
        $response = $soap->getJasperResponseFor(JasperSoapMultipartClient::CID_RESPONSE);
        
        var_dump($response->current());
        
        return 'lalala';
    }
    
    public function getMetaData() {
        $response = $this->getJasperResource();
        
        die();
    }

}

?>