<?php

class AppKitHtmlHelper extends AppKitSingleton implements AppKitHtmlEntitiesInterface {
	
	/**
	 * Constructor method for
	 * @return AppKitHtmlHelper
	 */
	public static function getInstance() {
		return parent::getInstance('AppKitHtmlHelper');
	}
	
	/**
	 * Constructor method (short form) for
	 * @return AppKitHtmlHelper
	 */
	public static function Obj() {
		return self::getInstance();
	}
	
	const ROUTE_NAME_IMAGE = 'appkit.image';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function Image($image_name, $alt=false, array $attributes = array ()) {
		$tag = AppKitXmlTag::create('img')
		->addAttribute('src', $this->getAgaviRouter()->gen(self::ROUTE_NAME_IMAGE, array('image' => $image_name)));
		
		if ($title !== false) {
			$tag->addAttribute('alt', $alt);
			$tag->addAttribute('title', $alt);
		}
		
		$tag->addAttributeArray($attributes);
		
		return $tag;
		
	}
	
	/**
	 * @param string $route_name
	 * @param string $caption
	 * @param array $route_args
	 * @param array $attributes
	 * @return AppKitXmlTag
	 */
	public function LinkToRoute($route_name, $caption, array $route_args = array (), array $attributes = array (), AgaviRequestDataHolder $other_args = null) {
		if ($other_args != null) {
			$route_args = array_merge($other_args->getParameters(), $route_args);
		}
		
		// Rewrite the ambersands (Because DOM rewrites it again!)
		$href = $this->getAgaviRouter()->gen($route_name, $route_args);
		$href = str_replace('&amp;', '&', $href);
		
		return AppKitXmlTag::create('a')
		->setContent($caption)
		->setNotEmpty()
		->addAttribute('href', $href)
		->addAttributeArray($attributes);
	}
	
	public function LinkImageToRoute($route_name, $alt, $image_string, array $route_args = array (), array $attributes = array (), AgaviRequestDataHolder $other_args = null) {
		$image = $this->Image($image_string, $alt);
		return $this->LinkToRoute($route_name, $image, $route_args, $attributes, $other_args);
	}
	
	/**
	 * @param string $caption
	 * @param string $name
	 * @param boolean $submit
	 * @param array $attributes
	 * @return AppKitXmlTag
	 * @deprecated Use the HtmlFormElement classes instead
	 */
	public function Button($caption, $name=null, $submit=true, array $attributes = array ()) {
		$tag = AppKitXmlTag::create('input')
		->addAttribute('type', $submit==true ? 'submit' : 'button')
		->addAttribute('value', $caption);
		
		if ($name !== null) $tag->addAttribute('name', $name);
		
		$tag->addAttributeArray($attributes);
		
		return $tag;
	}
	
	public function HiddenField($name, $value) {
		return AppKitXmlTag::create('input')
		->addAttribute('type', 'hidden')
		->addAttribute('value', $value)
		->addAttribute('name', $name);
	}
	
	public function classAlternate($class1, $class2) {
		static $changer = false;
		$changer = !$changer;
		if ($changer) return $class1;
		else return $class2;
	}
	
	public static function genUniqueId($prefix='htmlelement-') {
		static $count = 0;
		$count++;
		return sprintf('%s%d', $prefix, $count);
	}
	
	public static function concatHtmlId($arg1) {
		static $seperator = '-';
		$args = func_get_args();
		return join($seperator, $args);
	}
	
	public static function simpleHtmlId($prefix='rid', $length=10) {
		return sprintf('%s-%s', $prefix, AppKitRandomUtil::genSimpleId($length));
	} 
	
	public static function yuiTabSelector($name, $check) {
		static $yui_selected_tab_class = 'selected';
		if ($name==$check) {
			return $yui_selected_tab_class;
		}
		
		return null;
	}
}

?>