<?php

class AppKitHiddenElement extends AppKitFormElement {

	private $checked = false;
	
	/**
	 * 
	 * @param string $type
	 * @param string $name
	 * @param string $caption
	 * @return AppKitHiddenElement
	 * @author Marius Hein
	 */
	public static function create($name, $value) {
		return new AppKitHiddenElement($name, $value);
	}
	
	public function __construct($name, $value) {
		parent::__construct(self::TYPE_HIDDEN, $name, $value, null);
	}

}

?>