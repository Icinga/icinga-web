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


class JasperResourceDescriptor extends DOMDocument implements JasperI {

    /**
     * @var AgaviParameterHolder
     */
    private $__descriptorAttributes = null;

    /**
     * @var AgaviParameterHolder
     */
    private $__resourceProperties = null;

    /**
     * @var AgaviParameterHolder
     */
    private $__resourceParameters = null;

    private $__label = null;

    private $__crdate = null;

    /**
     * The root node of the document
     * @var DOMElement
     */
    private $__rootNode = null;

    public function __construct() {
        parent::__construct(self::XML_VERSION, self::XML_ENCODING);
        $this->resetDocument();
    }

    /**
     * Loads the object from string
     * @param string $xml_string
     */
    public function loadFromXml($xml_string) {
        $doc = new DOMDocument(self::XML_VERSION, self::XML_ENCODING);
        $doc->loadXML($xml_string);
        return $this->loadFromDom($doc);
    }

    /**
     * Loads the object from foreign DOM
     * @param DOMNode $node
     */
    public function loadFromDom(DOMNode $node) {
        $new_root = $this->importNode($node, true);
        $this->resetDocument($new_root);
        $this->syncDataStructures();
    }

    /**
     * Syncs the data structures from XML structure
     */
    public function syncDataStructures() {
        foreach($this->__rootNode->attributes as $attribute) {
            $this->__descriptorAttributes->setParameter($attribute->name, $attribute->value);
        }

        if (is_array(($tmp = $this->queryToArray('//resourceDescriptor/resourceProperty', 'name')))) {
            $this->__resourceProperties->setParameters($tmp);
        }

        if ($this->getElementsByTagName('label')->length) {
            $this->__label = trim($this->getElementsByTagName('label')->item(0)->nodeValue);
        }

        if ($this->getElementsByTagName('creationDate')->length) {
            $this->__crdate = trim($this->getElementsByTagName('creationDate')->item(0)->nodeValue);
        }
    }

    /**
     * Converts a XQuery to array structure
     * @param string $query
     * @param string $key_attribute Which attribute is used for the hash
     * @return array
     */
    public function queryToArray($query, $key_attribute) {
        $xpath = new DOMXPath($this);
        $list = $xpath->query($query);

        if ($list && $list instanceof DOMNodeList) {
            $out = array();
            foreach($list as $node) {
                $key = $node->getAttribute($key_attribute);
                $out[$key] = trim($node->nodeValue);
            }
            return $out;
        }
    }

    /**
     * Reset the object into initial state
     * @param DOMNode $new_root
     */
    private function resetDocument(DOMNode &$new_root=null) {
        $this->__descriptorAttributes = new AgaviParameterHolder();
        $this->__resourceProperties = new AgaviParameterHolder();
        $this->__resourceParameters = new AgaviParameterHolder();

        if ($this->__rootNode) {
            $this->removeChild($this->__rootNode);
        }

        if ($new_root === null) {
            $this->__rootNode = $this->createElement('resourceDescriptor');
        } else if ($new_root instanceof DOMNode) {
            $this->__rootNode = $new_root;
        }

        $this->appendChild($this->__rootNode);
    }

    /**
     * Returns the descriptor attributes
     * @return AgaviParameterHolder
     */
    public function getResourceDescriptor() {
        return $this->__descriptorAttributes;
    }

    /**
     * Get the timstamp from response when its created
     * @return DateTime
     */
    public function getCrdate() {
        // Java provides ms
        return strftime('%Y-%m-%d %H:%M:%S', $this->__crdate/1000);
    }

    /**
     * Returns the resource label from the descriptor
     * @return string
     */
    public function getLabel() {
        return $this->__label;
    }

    /**
     * Returns the properties object
     * @return AgaviParameterHolder
     */
    public function getProperties() {
        return $this->__resourceProperties;
    }
}

?>