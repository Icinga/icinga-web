<?php

class Reporting_JasperParameterStructModel extends ReportingBaseModel {
    
    private $__client = null;
    
    private $__uri = null;
    
    private $__typeMapping = array();
    
    private $__nameMapping = array();
    
    private $__filter = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__client = $this->getParameter('client');
        $this->__uri = $this->getParameter('uri');
        
        $this->__filter = $this->getParameter('filter');
        
        if (!$this->__client instanceof SoapClient) {
            throw new AppKitModelException('Model needs SoapClient (parameter client)');
        }
        
        if (!$this->__uri) {
            throw new AppKitModelException('Model needs Jasper uri of the report (parameter uri)');
        }
        
        $this->__typeMapping = AgaviConfig::get('modules.reporting.parameter.mapping.type');
        
        $this->__nameMapping = AgaviConfig::get('modules.reporting.parameter.mapping.name');
    }
    
    /**
     * Returns a JasperResponseXmlDoc to working on
     * @return JasperResponseXmlDoc
     */
    private function getJasperResponse() {
        $request = new JasperRequestXmlDoc('get');
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $this->__uri);
        
        $response = new JasperResponseXmlDoc($this->__client->get($request->getSoapParameter()));
        
        return $response;
    }
    
    public function getObjects() {
        $doc = $this->getJasperResponse();
        
        $out = array ();
        
        foreach ($doc as $rd) {
            if (!$this->__filter || $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_TYPE) == $this->__filter) {
              $out[] = $rd;  
            }
        }
        
        return $out;
    }
    
    public function getJsonStructure() {
        $objects = $this->getObjects();
	$out = array ();
        foreach ($objects as $rd) {
            $tmp = array();
            
            $tmp = $rd->getResourceDescriptor()->getParameters();
            $tmp += $rd->getProperties()->getParameters() + $tmp;
            
            $tmp['label'] = $rd->getLabel();
            
            if ($this->__filter == 'inputControl') {
                $this->applyInputControlStructs($rd, $tmp);
            }
            
            $out[$rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_NAME)] = $tmp;
        }
        
        return $out;
    }
    
    private function applyInputControlStructs(JasperResourceDescriptor &$rd, array &$target, $key='jsControl') {
        
        $struct = array();
        
        $rd_name = $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_NAME);
        $rd_type = $rd->getProperties()->getParameter('PROP_INPUTCONTROL_TYPE');
        
        if (array_key_exists($rd_name, $this->__nameMapping)) {
            $struct = $this->__nameMapping[$rd_name];
        }
        elseif (array_key_exists($rd_type, $this->__typeMapping)) {
            $struct = $this->__typeMapping[$rd_type];
        }
        
        $target[$key] = $struct;
        
        return true;
    }
}

?>
