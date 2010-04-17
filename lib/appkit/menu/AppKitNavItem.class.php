<?php

class AppKitNavItem extends AppKitBaseClass {
	
	private $name		= null;
	private $caption	= null;
	private $route		= null;
	private $image		= null;
	private $html		= null;
	private $jshandler	= null;
	private $args		= array ();
	
	/**
	 * @var AppKitNavContainer
	 */
	private $sub_container = null;
	
	/**
	 * 
	 * @param string $name
	 * @return AppKitNavItem
	 */
	public static function create($name=null, $route=null) {
		return new AppKitNavItem($name, $route);
	}
	
	/**
	 * The constructor method
	 * @param string $name
	 * @param string $route
	 */
	public function __construct($name=null, $route=null) {
		if ($name!==null) $this->setName($name);
		if ($route!==null) $this->setRoute($route);
	}
	
	/**
	 * Set's an indentifier name
	 * @param string $name
	 * @return AppKitNavItem
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Returns the name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets an array of route arguments
	 * @param array $args
	 * @return AppKitNavItem
	 * @author Marius Hein
	 */
	public function setRouteArgs(array $args) {
		$this->args = $args;
	}
	
	/**
	 * set a single argument for the route
	 * @param string $key
	 * @param mixed $val
	 * @return AppKitNavItem
	 * @author Marius Hein
	 */
	public function setRouteArg($key, $val) {
		$this->args[$key] = $val;
		return $this;
	}
	
	/**
	 * Return the route arguments
	 * @return array
	 * @author Marius Hein
	 */
	public function getRouteArgs() {
		return $this->args;
	}
	
	/**
	 * 
	 * @param string $caption
	 * @return AppKitNavItem
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 * Returns the caption
	 * @return string
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 * 
	 * @param string $route
	 * @return AppKitNavItem
	 */
	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}
	
	/**
	 * Returns the route name
	 * @return string
	 */
	public function getRoute() {
		return $this->route;
	}
	
	/**
	 * Sets a javascript handler for the menu
	 * @param string $handler_string
	 * @return AppKitNavItem
	 */
	public function setJsHandler($handler_string) {
		$this->jshandler = $handler_string;
		return $this;
	}
	
	/**
	 * Returns the js handler if one
	 * @return string
	 */
	public function getJsHandler() {
		return $this->jshandler;
	}
	
	/**
	 * 
	 * @param $html
	 * @return AppKitNavItem
	 */
	public function setHtml($html) {
		$this->html = $html;
		return $this;
	}
	
	/**
	 * Returns the html
	 * @return string
	 */
	public function getHtml() {
		return $this->html;
	}
	
	/**
	 * 
	 * @param $image
	 * @return AppKitNavItem
	 */
	public function setImage($image) {
		$this->image = $image;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitBaseClass#toString()
	 */
	public function toString() {
		return $this->name;
	}
	
	/**
	 * 
	 * @param AppKitNavContainer $container
	 * @return AppKitNavItem
	 */
	public function addContainer(AppKitNavContainer &$container) {
		$this->sub_container =& $container;
		return $this;
	}
	
	/**
	 * 
	 * @param AppKitNavItem $sub_item
	 * @return AppKitNavContainer
	 */
	public function addSubItem(AppKitNavItem $sub_item) {
		$this->getContainer()->addItem($sub_item);
		return $this;
	}
	
	/**
	 * Returns true if the route match the last in the route stack
	 * @return boolean
	 */
	public function isActive() {
		$matched = $this->getAgaviRequest()->getAttribute('matched_routes', 'org.agavi.routing');
		
		if (in_array($this->getRoute(), $matched)) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * @return AppKitNavContainer
	 */
	public function getContainer() {
		if (!$this->sub_container instanceof AppKitNavContainer)
			$this->sub_container = new AppKitNavContainer();
		return $this->sub_container;
	}
	
	public function hasChildren() {
		return $this->getContainer()->hasChildren();
	}

}

?>