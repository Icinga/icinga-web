<?php

class AppKitDoctrinePager extends Doctrine_Pager {
	
	private static $rangeType		= 'Sliding';
	
	private static $rangeOptions	= array (
		'chunk'		=> 8,
	);
	
	private static $url_replaces	= array (
		'page_offset'	=> '{%page_number}'
	);
	
	/**
	 * Creates a new pager on the fly
	 * @param Doctrine_Query $query
	 * @param integer $page_offset
	 * @param string $route_name
	 * @param integer $page_items
	 * @return AppKitDoctrinePager
	 * @author Marius Hein
	 */
	public static function createNew(Doctrine_query &$query, $page_offset, $route_name, $page_items=null) {
		
		if ($page_items === null || is_numeric($page_items) === false) {
			$page_items = AgaviConfig::get('de.icinga.appkit.pager.default_items');
		}
		
		return new AppKitDoctrinePager($query, $page_offset, $route_name, $page_items);
	}
	
	private $layout	= null;
	private $range	= null;
	private $url	= null;
	
	public function __construct(Doctrine_Query &$query, $page_offset, $route, $page_items) {
		parent::__construct($query, $page_offset, $page_items);
		$this->execute();
	}
	
	private function buildUrl($route_name) {
		$url = AgaviContext::getInstance('web')->getRouting()->gen($this->route, self::$url_replaces);
		foreach (self::$url_replaces as $value) {
			$url = str_replace(urlencode($value), $value, $url);
		}
		
		return $url;
	}
	
	/**
	 * Init the layout:
	 * 		- create the class
	 * 		- added some options
	 * 		- creates a default range
	 * @return boolean
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	private function initLayout() {
		if ($this->range === null || $this->layout == null) {
			$this->range = $this->getRange(self::$rangeType, self::$rangeOptions);
			$this->url = $this->buildUrl($route);
			$this->layout = new AppKitDoctrinePagerLayout($this, $this->range, $this->url);
			
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the pagerlayout
	 * @return AppKitDoctrinePagerLayout
	 * @author Marius Hein
	 */
	public function getLayout() {
		$this->initLayout();
		return $this->layout;
	}
	
	/**
	 * Display the pager
	 * @return strintg
	 * @author Marius Hein
	 * @throws Doctrine_Pager_Exception
	 */
	public function displayLayout() {
		return $this->getLayout()->display();
	}
	
}

?>