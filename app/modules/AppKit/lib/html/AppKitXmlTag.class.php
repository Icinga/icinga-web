<?php

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
