<?php

class Reporting_ReportGeneratorModel extends ReportingBaseModel {
    
    const HEADER_SPLIT = "\r\n\r\n";
    
    const CID_REPORT = 'report';
    
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
    
    private $__bodies = array ();
    
    private $__headers = array ();
    
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
        
        if ($this->getFormat() === 'HTML') {
            $request->setArgument('RUN_OUTPUT_IMAGES_URI', 'base64_inline_image:');
            $request->setArgument('IMAGES_URI', 'base64_inline_image:');
        }
        
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $uri);
        foreach ($this->__parameters as $pName=>$pValue) {
            $request->setParameter($pName, $pValue);
        }
        
        $this->runReport($request);
        
        /**
         * Insert foreign image data as base64 inline images that
         * the html report could be viewed stand alone without
         * additional resources
         */
        if ($this->getFormat() === 'HTML') {
            $this->htmlInsertInlineImages();
        }
        
        return $this->getContentById(self::CID_REPORT);
    }
    
    public function getContentById($cid) {
        if (array_key_exists(self::CID_REPORT, $this->__bodies)) {
            return $this->__bodies[self::CID_REPORT];
        }
    }
    
    private function htmlInsertInlineImages() {
        
        $content = &$this->__bodies[self::CID_REPORT];
//         var_dump($content);
        $matches = array ();
        while (preg_match('/base64_inline_image:(\w+)/', $content, $matches)) {
            $cid = $matches[1];
            if (isset($this->__bodies[$cid]) && isset($this->__headers[$cid])) {
                $data_string = sprintf('data:%s;base64,%s', $this->__headers[$cid]['content-type'], base64_encode($this->__bodies[$cid]));
//                 var_dump($data_string);
//                 var_dump($matches[0]);
//                 var_dump(preg_quote($matches[0]));
                $content = preg_replace('/'. $matches[0]. '/', $data_string, $content);
            } 
        }
//         var_dump($content);
//         die();
    }
    
    private function runReport(JasperRequestXmlDoc $doc) {
        $data = null;
        try {
            $org = $this->__client->runReport($doc->getSoapParameter());
            $this->parseReportResponse($this->__client, $org);
        }
        catch (SoapFault $e) {
            if ($e->getMessage() == 'looks like we got no XML document') {
                $this->parseReportResponse($this->__client, null);
            }
            else {
                throw $e;
            }
        }
        
        return $data;
    }
    
    private function parseHeader($header_string) {
        $matches = array ();
        $out = array ();
        if (preg_match_all('/^([^:]+):\s*(.+)$/m', $header_string, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $out[strtolower(trim($m[1]))] = trim($m[2]);
            }
        }
        return $out;
    }
    
    private function parseReportResponse(SoapClient $client, $result) {
        $headers = $client->__getLastResponseHeaders();
        $body = $client->__getLastResponse();
        
        if (preg_match('/Content-Type: multipart\/related/i', $headers)) {
        
            $matches = array ();
            if (preg_match('/boundary="([^"]+)"/', $headers, $matches)) {
                $parts = explode('--'. $matches[1], $body);
                foreach ($parts as $part) {
                    $m = array ();
                    if (preg_match('/content-id:\s+<([^>]+)>/i', $part, $m)) {
                        $cid = $m[1];
                        list($header,$content) = explode(self::HEADER_SPLIT, $part);
                        if ($header && $content) {
                            $this->__headers[$cid] = $this->parseHeader($header);
                            $this->__bodies[$cid] = $content;
                        }
                    }
                }
            }
        
        } else {
            if ($result) {
                $response = new JasperResponseXmlDoc($result);
                
                if ($response->success() == false) {
                    throw new AppKitModelException($response->returnMessage());
                }
                
                $this->__bodies[self::CID_REPORT] = $client->__getLastResponse();
                $this->__headers[self::CID_REPORT] = $client->__getLastResponseHeaders();
                
                
            } else {
                throw new AppKitModelException('Unknown soap fault');
            }
            
            return true;
        }
    }
}

?>