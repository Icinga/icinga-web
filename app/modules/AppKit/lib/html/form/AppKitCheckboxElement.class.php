<?php

class AppKitCheckboxElement extends AppKitFormElement {

    private $checked = false;

    /**
     *
     * @param string $name
     * @param mixed $value
     * @param bollean $checked
     * @param string $caption
     * @return AppKitCheckboxElement
     * @author Marius Hein
     */
    public static function create($name, $value, $checked=false, $caption=null) {
        return new AppKitCheckboxElement($name, $value, $checked, $caption);
    }

    public function __construct($name, $value, $checked=false, $caption=null) {
        parent::__construct(AppKitFormElement::TYPE_CHECKBOX, $name, $value, $caption);
        $this->setChecked($checked);
    }

    /**
     *
     * @param $checked
     * @return AppKitCheckboxElement
     * @author Marius Hein
     */
    public function setChecked($checked) {
        $this->checked = $checked;
        return $this;
    }

    protected function buildTag() {
        if ($this->checked===true) {
            $this->addAttribute('checked', 'checked');
        }

        return parent::buildTag();
    }

    /*
    public function renderElement() {
    	$hidden = AppKitHiddenElement::create($this->name. '_check', 1);

    	$out = $hidden->renderElement();
    	$out .= parent::renderElement();

    	return $out;
    }
    */

}

?>