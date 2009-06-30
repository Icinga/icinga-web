<?php

class IcingaViewExtenderLink implements IcingaViewExtenderConstInterface {
	
	const PARAM_IMAGE		= 'image_file';
	const PARAM_LINK_TYPE	= 'link_type';
	const PARAM_CAPTION		= 'link_caption';
	
	const VALUE_LINK_TEXT	= 'text';
	const VALUE_LINK_IMAGE	= 'image';
	
	/**
	 * The name of the link
	 * @var string
	 */
	private $name			= null;
	
	private $type			= null;
	
	/**
	 * The agavi name for the route to be used
	 * @var string
	 */
	private $route_name		= null;
	
	/**
	 * Arguments for the link
	 * @var array
	 */
	private $route_args		= array();
	
	/**
	 * @var AgaviParameterHolder
	 */
	private $parameters		= null; 
	
	/**
	 * Generic constructor
	 * @param string $name
	 * @return mixed
	 */
	public function __construct($type=null, $name = null) {
		if ($name !== null) $this->setName($name);
		if ($type !== null) $this->setType($type);
		$this->parameters = new AgaviParameterHolder();
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns a parameter object
	 * @return AgaviParameterHolder
	 */
	public function Parameters() {
		return $this->parameters;
	}
	
	public function addRouteArg($key, $val) {
		$this->route_args[$key] = $val;
	}
	
	public function setRouteArgs(array $args) {
		$this->route_args = $args;
	}
	
	public function getRouteArgs() {
		return $this->route_args;
	}
	
	public function setRoute($route_name) {
		$this->route_name = $route_name;
	}
	
	public function getRoute() {
		return $this->route_name;
	}
	
	public function setParameter($key, $val) {
		$this->Parameters()->setParameter($key, $val);
	}
	
	public function getParameter($key, $default=null) {
		return $this->Parameters()->getParameter($key, $default);
	}
}

?>