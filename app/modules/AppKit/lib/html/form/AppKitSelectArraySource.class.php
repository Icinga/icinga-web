<?php

class AppKitSelectArraySource extends AppKitSelectSource {

    private $data = array();
    private $selected = array();

    public function __construct(array $data, array $selected) {
        parent::__construct('dummy', null);
        $this->data = $data;
        $this->selected = $selected;
    }

    public function applyDomChanges(DomNode &$dom) {
        parent::applyDomChanges($dom);

        foreach($this->data as $key=>$val) {
            $element = $dom->ownerDocument->createElement('option', $val);
            $element->setAttribute('value', $key);

            if(in_array($key, $this->selected) === true) {
                $element->setAttribute('selected', 'selected');
            }

            $dom->appendChild($element);
        }

    }

}

?>