<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


/**
 * Can use inputcontrol structs and deliver their data values
 * @package IcingaWeb
 * @subpackage Reporting
 */
class Reporting_JasperDataResolverModel extends ReportingBaseModel {
    
    /**
     * @var SoapClient
     */
    private $__client = null;
    
    /**
     * @var JasperResourceDescriptor
     */
    private $__rd = null;
    
    private function l() {
        $args  = func_get_args();
        $this->context->getLoggerManager()->log(print_r($args, 1), 16);
    }
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->__client = $this->getParameter('client');
        
        $this->__rd = $this->getParameter('descriptor');

        if (!$this->__client instanceof SoapClient) {
            throw new AppKitModelException('Model needs SoapClient '
                    . '(parameter client)');
        }
        
        if (!$this->__rd instanceof JasperResourceDescriptor) {
            throw new AppKitModelException('Model needs a '
                    . 'JasperResourceDescriptor (parameter descriptor)');
        }
    }
    
    public function getData($data=array()) {
        $control_type = $this->__rd->getProperties()->getParameter(JasperResourceDescriptor::PROP_INPUTCONTROL_TYPE);
        
        /*
         * 3 == List of values
         */
        if ((int)$control_type === 3) {
            $reference_uri = $this->__rd->getProperties()->getParameter(JasperResourceDescriptor::PROP_REFERENCE_URI);
            if ($reference_uri) {
                $data = $this->getListvalues($reference_uri);
            }
        }
        
        return $data;
    }
    
    private function getListvalues($uri) {
        $request = new JasperRequestXmlDoc('get');
        $request->setResourceDescriptor(JasperRequestXmlDoc::DESCRIPTOR_ATTR_URI, $uri);

        $response = new JasperResponseXmlDoc($this->__client->get($request->getSoapParameter()));
        $data = array ();
        foreach ($response as $rd) {
            $data = $rd->getProperties()->getParameter(JasperResourceDescriptor::PROP_LOV);
            break;
        }
        return $data;
    }
    
}