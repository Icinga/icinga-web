<?php

abstract class AppKitSelectSource extends AppKitXmlTag implements AppKitSelectSourceInterface {

    public function applyDomChanges(DomNode &$dom) {
        $element = $dom->ownerDocument->createElement('option', '-');
        $element->setAttribute('value', '');
        $dom->appendChild($element);
    }

}

class AppKitSelectSourceException extends AppKitException {}

?>