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


class AppKitXmlTag {

    /**
     * @var DomDocument
     */
    protected $dom = null;

    /**
     * @var DomElement
     */
    protected $tag = null;

    /**
     * Constructor for AppKitXmlTag
     * @param string $tag_name
     * @param string $content
     */
    public function __construct($tag_name, $content=null) {
        $this->dom = new DOMDocument('1.0');
        $this->tag = $this->dom->createElement($tag_name);

        if ($content !== null) {
            $this->setContent($content);
        }
    }

    /**
     * Static creator for AppKitXmlTag
     * @param string $tag_name
     * @param string $content
     * @return AppKitXmlTag
     */
    public static function create($tag_name, $content=null) {
        return new AppKitXmlTag($tag_name, $content);
    }

    /**
     * Adds an text node to the tag
     * @param string $content
     * @return AppKitXmlTag
     */
    public function setContent($content) {

        if ($content instanceof AppKitXmlTag) {
            $content = $content->getDomElement();
        }

        if ($content instanceof DOMNode) {
            $this->tag->appendChild($this->dom->importNode($content, true));
        }

        elseif(is_null($content)) {
            // DO NOTHING ... :-)
        }
        elseif(!is_object($content) && !is_array($content)) {
            $textNode = $this->dom->createTextNode($content);
            $this->tag->appendChild($textNode);
        }
        else {
            throw new AppKitXmlTagException('Type of $content is not supported(DomElement, AppKitXmlTag, <STRING>)');
        }

        return $this;
    }

    /**
     * Adding an attribute
     * @param string $name
     * @param mixed $value
     * @return AppKitXmlTag
     */
    public function addAttribute($name, $value) {
        $this->tag->setAttribute($name, $value);
        return $this;
    }

    /**
     * Adding a bulk of attributes
     * @param array $attributes
     * @return AppKitXmlTag
     */
    public function addAttributeArray(array $attributes) {
        foreach($attributes as $key=>$val) {
            $this->addAttribute($key, $val);
        }
        return $this;
    }

    /**
     * Flags the tag to be not empty
     * @return AppKitXmlTag
     */
    public function setNotEmpty() {
        $textNode = $this->dom->createTextNode('');
        $this->tag->appendChild($textNode);
        return $this;
    }

    /**
     * Renders the element to string
     * @return string
     */
    public function renderElement() {


        if (isset($this->tag) && $this->tag instanceof DOMElement) {
            if ($this->dom->hasChildNodes()) {
                $this->dom->insertBefore($this->tag, $this->dom->firstChild);
            } else {
                $this->dom->appendChild($this->tag);
            }
        }

        $out = '';

        foreach($this->dom->childNodes as $node) {
            $out .= $this->dom->saveXml($node);
        }

        return $out;
    }

    /**
     * Appends a foreign node to the dom doc!
     * @param DOMNode $node
     * @param boolean $deep
     * @return AppKitXmlTag
     * @author Marius Hein
     */
    public function appendForeignNode(DOMNode $node, $deep=true) {
        $newnode = $this->dom->importNode($node, $deep);
        $this->dom->appendChild($newnode);
        return $this;
    }

    public function importForeignDomDocument(DOMDocument $doc) {
        foreach($doc->childNodes as $node) {
            $this->appendForeignNode($node, true);
        }
    }

    public function __toString() {
        return $this->renderElement();
    }

    /**
     * Returns the corresponding dom element
     * @return DomElement
     * @author Marius Hein
     */
    public function getDomElement() {
        return $this->tag;
    }

    /**
     *
     * @return DOMDocument
     * @author Marius Hein
     */
    public function getDomDocument() {
        return $this->dom;
    }

}

class AppKitXmlTagException extends AppKitException {}
