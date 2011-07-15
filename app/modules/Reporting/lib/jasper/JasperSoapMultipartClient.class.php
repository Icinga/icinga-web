<?php

class JasperSoapMultipartClient extends SoapClient {
    
    const CID_ATTACHMENT = 'attachment';
    const CID_REPORT = 'report';
    const CID_RESPONSE = 'direct-soap-reply';
    const SOAP_BLIND_ERROR = 'looks like we got no XML document';
    const HEADER_SPLIT = "\r\n\r\n";
    
    private $__header = array ();
    private $__data = array ();
    
    public function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    
    /**
     * We have our own envelope request doc so create the request
     * from that and do not call the methods seperately
     * @param JasperRequestXmlDoc $doc
     * @throws SoapFault
     * @return boolean
     */
    public function doRequest(JasperRequestXmlDoc $doc) {
        $function = $doc->getOperationName();
        
        try {
            $this->__call($function, array($doc->getSoapParameter()));
            $this->parseSoapReply();
        } catch (SoapFault $e) {
            if ($e->getMessage() == self::SOAP_BLIND_ERROR) {
                $this->parseSoapReply();
            } else {
              /**
               * That is not the wanted error, so throw again
               */
              throw $e;  
            }
        }
        
        return true;
        
    }
    
    /**
     * Prepares parsed header and reply.
     * @return boolean always true
     */
    private function parseSoapReply() {
        $this->__header = array ();
        $this->__data = array ();
        
        $response_header = $this->__header[self::CID_RESPONSE] = $this->parseHeader($this->__getLastResponseHeaders());
        
        if (strpos($response_header['content-type'], 'multipart/related') === 0) {
            $this->parseMultipartSoapReply();
        } else {
            $this->__data[self::CID_RESPONSE] = $this->__getLastResponse();
        }
        
        return true;
    }
    
    private function parseMultipartSoapReply() {
        $m = array (); // Our match object
        if (preg_match('/start="<([^">]+)>".+boundary="([^"]+)"/', $this->__header[self::CID_RESPONSE]['content-type'], $m)) {
            $response = $this->__getLastResponse();
            $response_cid = $m[1];
            $boundary = $m[2];
            
            $parts = explode('--'. $boundary, $response);
            foreach ($parts as $part) {
                $m = array ();
                if (preg_match('/content-id:\s+<([^>]+)>/i', $part, $m)) {
                    $content_id = $m[1];
                    list ($header_string, $data) = explode(self::HEADER_SPLIT, $part);
                    $this->__header[$content_id] = $this->parseHeader($header_string);
                    $this->__data[$content_id] = $data;
                }
            }
            
            /*
             * We need the default response from multipart
             */
            if (array_key_exists($response_cid, $this->__header)) {
                $this->__header[self::CID_RESPONSE] =& $this->__header[$response_cid];
                $this->__data[self::CID_RESPONSE] =& $this->__data[$response_cid];
            }
        }
        
        return true;
    }
    
    /**
     * Creates an array from http header string
     * @param string $string the raw header
     * @return array
     */
    private function parseHeader($string) {
        $out = array ();
        $m = array (); // Our array to match tokens
        $offset = 0;
        $string = trim($string);
        if (preg_match('/^(HTTP\/(.+))$/m', $string, $m)) {
            $out['HTTP'] = $m[1];
            $offset = strlen($out['HTTP']);
        }
        
        if (preg_match_all('/^([^:]+):\s+(.+)$/m', $string, $m, PREG_SET_ORDER, $offset)) {
            foreach ($m as $array) {
                $out[strtolower($array[1])] = trim($array[2]);
            }
        }
        
        return $out;
    }
    
    /**
     * Returns an array of headers for the content id
     * @param string $content_id
     * @return array
     */
    public function getHeadersFor($content_id) {
        if (array_key_exists($content_id, $this->__header)) {
            return $this->__header[$content_id];
        }
    }
    
    /**
     * Returns a single header for content id
     * @param string $content_id
     * @param string $header_name e.g. content-type
     */
    public function getHeaderFor($content_id, $header_name) {
        if (isset($this->__header[$content_id][$header_name])) {
            return $this->__header[$content_id][$header_name];
        }
    }
    
    /**
     * Returns the response body of the content id
     * @param stringt $content_id
     */
    public function getDataFor($content_id) {
        if (array_key_exists($content_id, $this->__data)) {
            return $this->__data[$content_id];
        }
    }
    
    /**
     * If response is the jasper xml envelope, we can return 
     * the ready to use jasper response object
     * @param string $content_id
     * @return JasperResponseXmlDoc
     */
    public function getJasperResponseFor($content_id) {
        if (preg_match('/^text\/xml/', $this->getHeaderFor($content_id, 'content-type'))) {
            $response = new JasperResponseXmlDoc($this->getDataFor($content_id));
            return $response; 
            
        }
    }
    
    /**
     * Returns an array of all available content id's of the request
     * @return array
     */
    public function getContentIds() {
        return array_keys($this->__header);
    }
    
    /**
     * Returns true if a content id exists
     * @param string $content_id
     * @return boolean exists in headers and bodies
     */
    public function hasContentId($content_id) {
        return array_key_exists($content_id, $this->__header) && array_key_exists($content_id, $this->__data);
    }
    
}

?>