<?php

class AppKitSelectCheckboxElement extends AppKitFormElement {

    /**
     * @var AppKitSelectSourceInterface
     */
    private $source = null;

    private $multiple = false;

    /**
     *
     * @param string $type
     * @param string $name
     * @param string $caption
     * @return AppKitSelectElement
     * @author Marius Hein
     */
    public static function create($name, $caption, AppKitSelectSourceInterface  $source) {
        return new AppKitSelectCheckboxElement($name, $caption, $source);
    }

    public function __construct($name, $caption, AppKitSelectSourceInterface $source) {
        parent::__construct(self::TYPE_CHECKBOX, $name, 0, $caption, 'div');

        $this->setType(self::TYPE_CHECKBOX);
        $this->setName($name);
        $this->setCaption($caption);
        $this->setValue(0);
        $this->addClass(self::DEEFAULT_CLASS_PREFIX. self::TYPE_CHECKBOX);
        $this->setId($this->generateHtmlId());
        $this->setSource($source);
    }

    /**
     * @param AppKitSelectSource $source
     * @return AppKitSelectElement

     * @author Marius Hein
     */
    public function setSource(AppKitSelectSourceInterface $source) {
        $this->source =& $source;
        return $this;
    }

    /**
     * @param $bool
     * @return AppKitSelectElement
     * @author Marius Hein
     */
    public function setMultiple($bool=true) {
        $this->multiple = $bool;
        return $this;
    }

    protected function buildTag() {

        $dom = $this->dom->createElement('select');
        $this->source->applyDomChanges($dom);

        foreach($dom->childNodes as $node) {
            if ($node->getAttribute('value')) {

                $check = AppKitCheckboxElement::create(
                             $this->name,
                             $node->getAttribute('value'),
                             false,
                             $node->nodeValue
                         );

                if ($node->hasAttribute('selected')) {
                    $check->setChecked(true);
                }

                // Make the dom object ready!
                $check->renderElement();

                $br = $this->dom->createElement('br');
                $check->appendForeignNode($br);

                $this->importForeignDomDocument($check->getDomDocument());
            }
        }

        unset($this->tag);
    }

}

?>