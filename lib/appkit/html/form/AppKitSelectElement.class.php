<?php

class AppKitSelectElement extends AppKitFormElement {

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
		return new AppKitSelectElement($name, $caption, $source);
	}
	
	public function __construct($name, $caption, AppKitSelectSourceInterface $source) {
		parent::__construct(null, $name, null, $caption, 'select');
		
		$this->setType($type);
		$this->setName($name);
		$this->setCaption($caption);
		$this->setValue($value);
		// $this->addClass(self::DEEFAULT_CLASS_PREFIX. $type);
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
		$this->source->applyDomChanges($this->getDomElement());
		parent::buildTag();
		
		if ($this->multiple === true) {
			$this->addAttribute('multiple', 'multiple');
			$this->addAttribute('size', '5');
		}
	}

}

?>