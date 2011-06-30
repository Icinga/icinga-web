<?php

class Reporting_ReportGeneratorModel extends ReportingBaseModel {
    
    /**
     * @var JasperResourceDescriptor
     */
    private $__report = null;
    
    /**
     * @var SoapClient
     */
    private $__client = null;
    
    /**
     * @var string
     */
    private $__format = null;
    
    private $__parameters = array ();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__report = $this->getParameter('report');
        $this->__client = $this->getParameter('client');
        $this->__format = $this->getParameter('format', 'pdf');
        $this->__parameters = $this->getParameter('parameters', array ());
        
        if (!$this->__report instanceof JasperResourceDescriptor) {
            throw new AppKitModelException('report must be instance of JasperResourceDescriptor');
        }
        
        if (!$this->__client instanceof SoapClient) {
            throw new AppKitModelException('client must be instance of SoapClient');
        };
        
    }
    
    public function getFormat() {
        return strtoupper($this->__format);
    }
    
    public function getReportData() {
        $uri = $this->__report->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_URI);
        $request = new JasperRequestXmlDoc('runReport');
        $request->setArgument('RUN_OUTPUT_FORMAT', $this->getFormat());
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $uri);
        foreach ($this->__parameters as $pName=>$pValue) {
            $request->setParameter($pName, $pValue);
        }
        $document = $this->getReportOutput($request);
        
        return $document;
    }
    
    private function getReportOutput(JasperRequestXmlDoc $doc) {
        $data = null;
        try {
            $this->__client->runReport($doc->getSoapParameter());
            $data = $this->parseReportResponse($this->__client);
        }
        catch (SoapFault $e) {
            if ($e->getMessage() == 'looks like we got no XML document') {
                $data = $this->parseReportResponse($this->__client);
            }
            else {
                throw $e;
            }
        }
        
        return $data;
    }
    
    private function parseReportResponse(SoapClient $client) {
        $headers = $client->__getLastResponseHeaders();
        $body = $client->__getLastResponse();
        $matches = array ();
        if (preg_match('/boundary="([^"]+)"/', $headers, $matches)) {
            $parts = explode($matches[1], $body);
            foreach ($parts as $part) {
                if (preg_match('/content-id:\s+<report>/i', $part)) {
                    list($header,$content) = explode("\r\n\r\n", $part);
                    return $content;
                }
            }
        }
    }
}

?>