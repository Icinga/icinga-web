<?php

class AppKitSelectDoctrineSource extends AppKitSelectSource {

    private $data			= null;
    private $selected		= null;
    private $key_field		= null;
    private $value_field	= null;

    /**
     *
     * @param Doctrine_Collection $data
     * @param Doctrine_Collection|Doctrine_Record $selected
     * @param string $key_field
     * @param string $val_field
     * @return unknown_type
     * @author Marius Hein
     */
    public function __construct(Doctrine_Collection $data, $selected, $key_field, $val_field) {
        parent::__construct('dummy', null);

        if ($selected instanceof Doctrine_Collection) {
            $obj = $selected->getFirst();

            if ($obj && !$data->getFirst() instanceof $obj) {
                throw new AppKitSelectSourceException('Data and selected objects should use the same element source: '. get_class($data->getFirst()));
            }
        }

        elseif($selected instanceof Doctrine_Record) {
            $check_class = get_class($data->getFirst());

            if ($selected && !$selected instanceof $check_class) {
                throw new AppKitSelectSourceException('Data and selected objects should use the same element source: '. get_class($data->getFirst()));
            }
        }
        elseif($selected === null) {
            $selected = null;
        }
        else {
            throw new AppKitSelectSourceException('No suitable datasource object given Doctrine_Collection|Doctrine_Record');
        }

        $this->data			= $data;
        $this->selected		= $selected;
        $this->key_field	= $key_field;
        $this->value_field	= $val_field;
    }

    public function applyDomChanges(DomNode &$dom) {
        parent::applyDomChanges($dom);

        foreach($this->data as $data) {
            $element = $dom->ownerDocument->createElement('option', $data-> { $this->value_field });
            $element->setAttribute('value', $data-> { $this->key_field });


            if ($this->selected instanceof Doctrine_Collection) {
                foreach($this->selected as $selected) {
                    if ($data-> { $this->key_field } == $selected-> { $this->key_field }) {
                        $element->setAttribute('selected', 'selected');
                    }
                }
            }

            elseif($this->selected instanceof Doctrine_Record) {
                if ($data-> { $this->key_field } == $this->selected-> { $this->key_field }) {
                    $element->setAttribute('selected', 'selected');
                }
            }

            $dom->appendChild($element);
        }

    }

}

?>