<?php

class Reporting_JasperTreeStructModel extends ReportingBaseModel {
    
    private $__soap = null;
    
    private $__parent = null;
    
    private $__filter = array ();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__soap = $this->getParameter('client');
        
        $this->__parent = $this->getParameter('parentid');
        
        $this->__filter = $this->getParameter('filter', array ());
        
        if (!$this->__soap instanceof SoapClient) {
            throw new AppKitModelException('Model needs a soap client, parameter client');
        }
        
        if (!$this->__parent) {
            throw new AppKitModelException('Parent node (parameter parentid) not given');
        }
        
        if ($this->__filter && !$this->__filter instanceof Reporting_JasperTreeFilterModel) {
            throw new AppKitModelException('Filter must be a Reporting_JasperTreeFilterModel');
        }
    }
    
    public function getJsonStructure() {
        $request = new JasperRequestXmlDoc('list');
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_TYPE, 'folder');
        
        $uri = $this->__parent;
        
        if ($uri == 'root') {
            $uri = '/';
        }
        
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $uri);
        
        $response = new JasperResponseXmlDoc($this->__soap->list($request->getSoapParameter()));
        
        $out = array ();
        
        foreach ($response as $rd) {
            
            if ($this->__filter) {
                if ($this->__filter->matchDescriptor($rd) == false) {
                    continue;
                }
            }
            
            $p = $rd->getResourceDescriptor();
            
            $tmp = array (
                'id'    => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_URI),
                'text'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_NAME),
                'leaf'  => ($p->getParameter(JasperResourceDescriptor::DESCRIPTOR_TYPE)=='folder') ? false : true,
                'type'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_TYPE),
                'uri'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_URI),
            	'name'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_NAME),
            	'label'=> $rd->getLabel()
            );
            
            $tmp = (array)$rd->getProperties()->getParameters() + $tmp;
            
            $out[] = $tmp;
        }
        
        return $out;
    }
    
}

?>