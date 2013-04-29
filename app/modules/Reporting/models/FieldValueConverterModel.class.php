<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class Reporting_FieldValueConverterModel extends ReportingBaseModel {

    /**
     * @var SoapClient
     */
    private $__client = null;

    private $__uri = null;

    private $__p = array();

    private $__inputControls = array();

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
        $request->setResourceDescriptor(JasperRequestXmlDoc::DESCRIPTOR_ATTR_URI, $uri);
        $descriptors = new JasperResponseXmlDoc($this->__client->get($request->getSoapParameter()));
        foreach($descriptors as $rd) {
            if ($rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE) == 'inputControl') {
                $this->__inputControls[] = $rd;
            }
        }
    }

    public function getConvertedParameters() {
        $out = array();
        foreach($this->__inputControls as $rd) {
            $name = $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME);

            if (array_key_exists($name, $this->__p)) {
                $type = $rd->getProperties()->getParameter('PROP_DATATYPE_TYPE');
                $value = $this->__p[$name];

                switch ($type) {
                    case JasperRequestXmlDoc::DT_TYPE_DATE_TIME:
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