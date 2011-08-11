<?php

/**
 * Collection of xml/dom helper functions
 * @author mhein
 *
 */
class AppKitXmlUtil {


    /**
     * Extract one element upon xpointer query and returns if found
     * @param AgaviXmlConfigDomDocument $document
     * @param string $query
     * @return DOMNodeList
     */
    public static function extractEntryNode(AgaviXmlConfigDomDocument $document, $query) {
        $list = $document->getXPath()->query($query);

        if ($list instanceof DOMNodeList && $list->length==1) {
            return $list->item(0);
        }
    }

    /**
     * Creates an XI element with file and Xpointer syntax
     * @param AgaviXmlConfigDomDocument	$document
     * @param string					$file
     * @param string					$pointer	Whole Xpointer syntax with ns and query
     */
    public static function createXIncludeNode(AgaviXmlConfigDomDocument $document, $file, $pointer) {
        $element = $document->createElementNS('http://www.w3.org/2001/XInclude', 'xi:include');

        $element->setAttribute('href', $file);
        $element->setAttribute('xpointer', $pointer);

        return $element;
    }

    /**
     * Prepares a DOM object for include other resource. After adding new XI nodes you have
     * to call xinclude() manually
     *
     * @param AgaviXmlConfigDomDocument $document	Our DOM object to work on
     * @param string					$query		Target to include (must match to one item)
     * @param string					$pointer	From where to take (This comes into the XI element)
     * @param array						$files		An array of files to use as source
     */
    public static function includeXmlFilesToTarget(AgaviXmlConfigDomDocument $document, $query, $pointer, array $files) {
        $targetNode = self::extractEntryNode($document, $query);

        if (!$targetNode) {
            return;
        }

        foreach($files as $file) {
            $node = self::createXIncludeNode($document, $file, $pointer);
            $targetNode->appendChild($node);
        }
    }

    /**
    * Checks wether $attribute is an attribute of the DOMElement $element or any of it's parents
    * Returns the attribute value or null if it doesn't exist
    *
    * @param 	AgaviXmlDomElement 	DomElement to start hangling up
    * @param	string				Attribute to search for
    *
    * @author	Jannis Moßhammer <jannis.mosshammer@netways.de>
    */
    public static function getInheritedAttribute(DomElement $element,$attribute) {
        $parent = $element;

        do {
            if ($parent->hasAttribute($attribute)) {
                return AgaviToolKit::literalize($parent->getAttribute($attribute));
            }

            $element = $parent;
            $parent = $element->parentNode;

        } while (($parent instanceof DomElement));

        return null;
    }
}
