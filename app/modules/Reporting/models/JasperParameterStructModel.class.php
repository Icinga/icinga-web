<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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
        $request->setResourceDescriptor(JasperRequestXmlDoc::DESCRIPTOR_ATTR_URI, $this->__uri);

        $response = new JasperResponseXmlDoc($this->__client->get($request->getSoapParameter()));

        return $response;
    }

    public function getObjects() {
        $doc = $this->getJasperResponse();

        $out = array();

        foreach($doc as $rd) {
            if (!$this->__filter || $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE) == $this->__filter) {
                $out[] = $rd;
            }
        }

        return $out;
    }

    public function getJsonStructure() {
        $objects = $this->getObjects();
        $out = array();
        foreach($objects as $rd) {
            $tmp = array();

            $tmp = $rd->getResourceDescriptor()->getParameters();
            $tmp += $rd->getProperties()->getParameters() + $tmp;

            $tmp['label'] = $rd->getLabel();

            if ($this->__filter == 'inputControl') {
                $this->applyInputControlStructs($rd, $tmp);
            }

            $out[$rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME)] = $tmp;
        }

        return $out;
    }

    private function applyInputControlStructs(JasperResourceDescriptor &$rd, array &$target, $key='jsControl') {

        $struct = array();

        $rd_name = $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME);
        $rd_type = $rd->getProperties()->getParameter(JasperResourceDescriptor::PROP_DATATYPE_TYPE);

        if (array_key_exists($rd_name, $this->__nameMapping)) {
            $struct = $this->__nameMapping[$rd_name];
        }

        elseif(array_key_exists($rd_type, $this->__typeMapping)) {
            $struct = $this->__typeMapping[$rd_type];
       }

        $target[$key] = $struct;

        return true;
    }
}

?>
