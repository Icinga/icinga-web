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


/**
 *
 * Handles the XML request for jasperserver with soap.
 * @author mhein
 *
 */
class JasperRequestXmlDoc extends DOMDocument implements JasperI {

    /**
     * @var DOMElement
     */
    private $__rootNode = null;

    private $__resourceDescriptors = array(
                                         'name'        => '',
                                         'wsType'      => '',
                                         'uriString'   => '',
                                         'isNew'       => 'false'
                                     );

    private $__argumentData = array();

    private $__parameterData = array();

    private $__label = null;

    private $__documentReady = false;

    public function __construct($operationName) {
        parent::__construct(self::XML_VERSION, self::XML_ENCODING);

        $this->__rootNode = $this->__createRootNode($operationName);
        $this->appendChild($this->__rootNode);
    }

    public function getOperationName() {
        return $this->__rootNode->getAttribute('operationName');
    }

    /**
     * To create the document root, the node and its attributes
     * are always the same
     * @param string $operationName name of the operation to call e.g list or runReport
     */
    private function __createRootNode($operationName) {
        $root = $this->createElement('request');
        $root->setAttribute('operationName', $operationName);
        $root->setAttribute('locale', self::JASPER_LOCALE);
        return $root;
    }

    /**
     * Build the document structures based on parameters and arguments. This method
     * is also called if you convert ths class instance to string
     */
    public function createRequest() {

        if ($this->__documentReady === true) {
            return true;
        }

        foreach($this->__argumentData as $aName=>$aValue) {
            $argument = $this->createElement('argument');
            $argument->setAttribute('name', $aName);
            $argument->appendChild($this->createCDATASection($aValue));
            $this->__rootNode->appendChild($argument);
        }

        $resourceDescriptor = $this->createElement('resourceDescriptor');

        foreach($this->__resourceDescriptors as $rName => $rValue) {
            $resourceDescriptor->setAttribute($rName, $rValue);
        }

        $label = $this->createElement('label', ($this->__label==null) ? 'null' : $this->__label);
        $resourceDescriptor->appendChild($label);

        foreach($this->__parameterData as $pName => $pValue) {
            $parameter = $this->createElement('parameter');
            $parameter->setAttribute('name', $pName);
            $parameter->appendChild($this->createCDATASection($pValue));
            $resourceDescriptor->appendChild($parameter);
        }

        $this->__rootNode->appendChild($resourceDescriptor);

        return $this->__documentReady = true;
    }

    /**
     * Remove all DOM structures to build new
     */
    public function resetDocument() {
        foreach($this->getElementsByTagName('argument') as $delNode) {
            $this->__rootNode->removeChild($delNode);
        }

        foreach($this->getElementsByTagName('resourceDescriptor') as $delNode) {
            $this->__rootNode->removeChild($delNode);
        }

        $this->__documentReady = false;
    }

    /**
     * Adds a parameter to resource descriptor
     * @param string $name
     * @param string $value
     */
    public function setParameter($name, $value) {
        $this->__parameterData[$name] = $value;
    }

    /**
     * Removes a parameter from stack
     * @param string $name
     */
    public function deleteParameter($name) {
        if (array_key_exists($name, $this->__parameterData)) {
            unset($this->__parameterData[$name]);
            return true;
        }
    }

    /**
     * Adds an argument to the request
     * @param string $name
     * @param string $value
     */
    public function setArgument($name, $value) {
        $this->__argumentData[$name] = $value;
    }

    /**
     * Deletes an argument from argument stack
     * @param string $name
     */
    public function deleteArgument($name) {
        if (array_key_exists($name, $this->__argumentData)) {
            unset($this->__argumentData[$name]);
            return true;
        }
    }

    /**
     * Adds a resaource descriptor attribute to rd node
     * @param string $name
     * @param string $value
     */
    public function setResourceDescriptor($name, $value) {
        $this->__resourceDescriptors[$name] = $value;
    }

    /**
     * Removes a resource descriptor attribute from stack
     * @param string $name
     */
    public function deleteResourceDescriptor($name) {
        if (array_key_exists($this->__resourceDescriptors[$name])) {
            unset($this->__resourceDescriptors[$name]);
        }
    }

    /**
     * Sets the label for the operation
     * @param string $label
     */
    public function setLabel($label) {
        $this->__label = $label;
    }

    /**
     * Converst the DOM structure to xml string. If the document is not
     * created yet the method creates the DOM for you
     * @return string
     */
    public function __toString() {
        if ($this->__documentReady === false) {
            $this->createRequest();
        }

        return $this->saveXML($this);
    }

    /**
     * Returns the XML as soap parameter ready to throw into
     * the jasperserver to Apache Axis
     * @param string $name
     * @return SoapParam
     */
    public function getSoapParameter($name=self::JASPER_SOAPPARAMNAME) {
        $param = new SoapParam((string)$this, $name);
        return $param;
    }
}

?>