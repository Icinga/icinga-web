<?php

class Reporting_FieldValueConverterModel extends ReportingBaseModel {
    
    const TYPE_DATE = 2;
    
    /**
     * @var SoapClient
     */
    private $__client = null;
    
    private $__uri = null;
    
    private $__p = array ();
    
    private $__inputControls = array ();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__client = $this->getParameter('client');
        $this->__uri = $this->getParameter('uri');
        $this->__p = $this->getParameter('parameters', array());
        
        if (!$this->__client instanceof SoapClient) {
            throw new AppKitModelException('Model needs a SoapClient to work');
        }
        
        if (!$this->__uri) {
            throw new AppKitModelException('Model needs a reportUnit uri to work');
        }
        
        
        $this->buildSoapRequest($this->__uri);
    }
    
    private function buildSoapRequest($uri) {
        $request = new JasperRequestXmlDoc('get');
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $uri);
        $descriptors = new JasperResponseXmlDoc($this->__client->get($request->getSoapParameter()));
        foreach ($descriptors as $rd) {
            if ($rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_TYPE) == 'inputControl') {
                $this->__inputControls[] = $rd;
            }
        }
    }
    
    public function getConvertedParameters() {
        $out = array ();
        foreach ($this->__inputControls as $rd) {
            $name = $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_NAME);
            if (array_key_exists($name, $this->__p)) {
                $type = $rd->getProperties()->getParameter('PROP_INPUTCONTROL_TYPE');
                $value = $this->__p[$name];
                
                switch ($type) {
                    case self::TYPE_DATE:
                        $value = $this->convertDate($value);
                    break;
                }
                
                $out[$name] = $value;
            }
        }
        return $out;
    }
    
    private function convertDate($value) {
        $tstamp = strtotime($value);
        if (is_numeric($tstamp) && $tstamp > 0) {
            // Java unix epoch are milliseconds
            return $tstamp*1000;
        }
        
        throw new AppKitModelException('Could not convert '. $value. ' to unix epoch');
    }
}

?>