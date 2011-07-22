<?php

class Reporting_JasperTreeStructModel extends JasperConfigBaseModel {
    
    private $__soap = null;
    
    private $__parent = null;
    
    private $__filter = array ();
    
    private $__wsTypeIcons = array ();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__soap = $this->getParameter('client');
        
        $this->__parent = $this->getParameter('parentid');
        
        $this->__filter = $this->getParameter('filter', null);
        
        $this->__wsTypeIcons = AgaviConfig::get('modules.reporting.icon.wsType.mapping', array ());
        
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
    
    private function mapIconClassByType($type) {
        if (array_key_exists($type, $this->__wsTypeIcons)) {
            return $this->__wsTypeIcons[$type];
        }
        
        return null;
    }
    
    public function getJsonStructure() {
        $request = new JasperRequestXmlDoc('list');
        $request->setResourceDescriptor(JasperRequestXmlDoc::DESCRIPTOR_ATTR_TYPE, 'folder');
        
        $uri = $this->__parent;
        
        if ($this->hasParameter('tree_root')) {
            if (!preg_match('/^'. preg_quote($this->getParameter('tree_root'), '/'). '/', $uri)) {
                
                if ($uri !== 'root') {
                    $this->getContext()->getLoggerManager()->log(
                    	'Reports: Possible security hack, try accessing jasper server on '
                        . $uri
                        . ' without matching root path',
                        AgaviLogger::ERROR
                    );
                }
                
                $uri = $this->getParameter('tree_root');
            }
        } else {
            if ($uri == 'root') {
                $uri = '/';
            }
        }
        
        $request->setResourceDescriptor(JasperRequestXmlDoc::DESCRIPTOR_ATTR_URI, $uri);
        
        $response = new JasperResponseXmlDoc($this->__soap->list($request->getSoapParameter()));
        
        $out = array ();
        
        foreach ($response as $rd) {
            
            /*
             * Maybe we should dereference references without name, don't know 
             */
            if (!$rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME)) {
                continue;
            }
            
            if ($this->__filter) {
                if ($this->__filter->matchDescriptor($rd) == false) {
                    continue;
                }
            }
            
            $p = $rd->getResourceDescriptor();
            
            $tmp = array (
                'id'    => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_URI),
                'text'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME),
                'leaf'  => ($p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE)=='folder') ? false : true,
                'type'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE),
                'uri'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_URI),
            	'name'  => $p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME),
            	'label'=> $rd->getLabel(),
            	'iconCls' => $this->mapIconClassByType($p->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE))
            );
            
            $tmp = (array)$rd->getProperties()->getParameters() + $tmp;
            
            $out[] = $tmp;
        }
        
        return $out;
    }
    
}

?>