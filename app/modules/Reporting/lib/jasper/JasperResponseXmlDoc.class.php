<?php

class JasperResponseXmlDoc extends DOMDocument implements Iterator {
    const XML_VERSION = '1.0';
    const XML_ENCODING = 'UTF-8';
    
    /**
     * @var DOMNodeList
     */
    private $__resourceNodeList = null;
    
    /**
     * @var integer
     */
    private $__position = 0;
    
    /**
     * Creates a new response xml document to iterate 
     * through its resource properties
     * @param string $xml_response_string
     */
    public function __construct($xml_response_string) {
        parent::__construct(self::XML_VERSION, self::XML_ENCODING);
        $this->loadXML($xml_response_string);
        
        if ($this->success() === true) {
            $this->__resourceNodeList = $this->getElementsByTagName('resourceDescriptor');
        }
    }
    
    public function returnCode() {
        $rc_node = $this->getElementsByTagName('returnCode')->item(0);
        
        while ($rc_node->hasChildNodes()) {
            $rc_node = $rc_node->firstChild;
        }
        
        return (integer)$rc_node->nodeValue;
    }
    
    public function success() {
        $rc = $this->returnCode();
        if ($rc===0) {
            return true;
        }
        
        return false;
    }
    
    public function current() {
	     $rd = new JasperResourceDescriptor();
	     $rd->loadFromDom($this->__resourceNodeList->item($this->__position));
	     return $rd;
    }

    public function next() {
	     $this->__position++;
    }

    public function key() {
	     return $this->__position;
    }

    public function valid() {
	     return ($this->__position >= 0 && $this->__position < $this->__resourceNodeList->length);
    }

    public function rewind() {
	     $this->__position = 0;
    }
}