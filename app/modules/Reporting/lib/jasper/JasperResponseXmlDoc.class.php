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


class JasperResponseXmlDoc extends DOMDocument implements Iterator, Countable, JasperI {

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

        if (!$rc_node || !$rc_node->hasChildNodes()) {
            return 0;
        }

        while ($rc_node->hasChildNodes()) {
            $rc_node = $rc_node->firstChild;
        }

        return (integer)$rc_node->nodeValue;
    }

    public function returnMessage() {
        $rc_node = $this->getElementsByTagName('returnMessage')->item(0);

        while ($rc_node->hasChildNodes()) {
            $rc_node = $rc_node->firstChild;
        }

        return (string)$rc_node->nodeValue;
    }

    public function success() {
        $rc = $this->returnCode();

        if ($rc===0) {
            return true;
        }

        return false;
    }

    public function count() {
        $xpath = new DOMXPath($this);
        $nodes = $xpath->evaluate('resourceDescriptor[@name]');

        if ($nodes && $nodes->length) {
            return $nodes->length;
        }

        return 0;
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