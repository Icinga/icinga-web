<?php

class AppKitTextboxElement extends AppKitFormElement {

	/**
	 * @var AppKitSelectSourceInterface
	 */
	private $source = null;
	
	private $multiple = false;
	
	/**
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $caption
	 * @return AppKitSelectElement
	 * @author Marius Hein
	 */
	public static function create($name, $value, $caption=null) {
		return new AppKitTextboxElement($name, $value, $caption);
	}
	
	public function __construct($name, $value, $caption=null) {
		parent::__construct(null, $name, null, $caption, 'textarea');
		
		$this->setNotEmpty();
		
		$this->setType($type);
		$this->setName($name);
		$this->setCaption($caption);
		$this->setValue($value);
		$this->setId($this->generateHtmlId());
		
	}
	
	public function setValue($value) {
			return $this->setContent($value);
	}
}

?>