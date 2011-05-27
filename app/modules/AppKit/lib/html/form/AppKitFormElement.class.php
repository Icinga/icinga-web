<?php

class AppKitFormElement extends AppKitXmlTag {
    const DEEFAULT_CLASS_PREFIX	= 'element-';
    const DEFAULT_ID_PREFIX		= 'elementid';

    const TYPE_BUTTON		= 'button';
    const TYPE_SUBMIT		= 'submit';
    const TYPE_TEXT			= 'text';
    const TYPE_PASSWORD		= 'password';
    const TYPE_CHECKBOX		= 'checkbox';
    const TYPE_RADIO		= 'radio';
    const TYPE_IMAGE		= 'image';
    const TYPE_HIDDEN		= 'hidden';

    static $id_counter	= 0;

    protected $name		= null;
    protected $caption	= null;
    protected $id			= null;
    protected $type		= null;
    protected $value		= null;
    protected $class		= array();

    /**
     *
     * @param string $type
     * @param string $name
     * @param string $caption
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public static function create($type, $name, $value, $caption=null) {
        return new AppKitFormElement($type, $name, $value, $caption);
    }

    public function __construct($type, $name, $value, $caption=null, $tag_name='input') {
        parent::__construct($tag_name, null);
        $this->setType($type);
        $this->setName($name);
        $this->setCaption($caption);
        $this->setValue($value);
        $this->addClass(self::DEEFAULT_CLASS_PREFIX.($type ? $type : $tag_name));
        $this->setId($this->generateHtmlId());
    }

    /**
     *
     * @param $value
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     *
     * @param $type
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @param $name
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @param $caption
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function setCaption($caption) {
        $this->caption = $caption;
        return $this;
    }

    /**
     *
     * @param $id
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     *
     * @param $class_name
     * @return AppKitFormElement
     * @author Marius Hein
     */
    public function addClass($class_name) {
        $this->class[] = $class_name;
    }

    public function removeClass($class_name) {
        if($this->classExists($class_name)) {
            if(($index = array_search($class_name, $this->class)) !== false) {
                array_slice($this->class, $index, 1);
                return true;
            }
        }

        return false;
    }

    public function classExists($class_name) {
        return in_array($class_name, $this->class);
    }

    public function renderElement() {
        $this->buildTag();
        return parent::renderElement();
    }

    public function toString() {
        return $this->renderElement();
    }

    /**
     *
     * @return AppKitFormElement
     * @author Marius Hein
     */
    protected function buildTag() {
        $this->addAttribute('name', $this->name);

        if(count($this->class)) {
            $this->addAttribute('class', implode(' ', $this->class));
        }

        $this->addAttribute('id', $this->id);

        if(isset($this->type)) {
            $this->addAttribute('type', $this->type);
        }

        if(isset($this->value)) {
            $this->addAttribute('value', $this->value);
        }

        switch($this->type) {
            case 'checkbox':
            case 'radio':
                $label = $this->dom->createElement('label', $this->caption);
                $label->setAttribute('for', $this->id);
                $this->dom->appendChild($label);
                break;
        }

        return $this;
    }

    protected function generateHtmlId() {
        return self::DEFAULT_ID_PREFIX. ++self::$id_counter;
    }

}

?>