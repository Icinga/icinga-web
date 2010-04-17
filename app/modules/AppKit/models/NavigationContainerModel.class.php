<?php

class AppKit_NavigationContainerModel extends ICINGAAppKitBaseModel
implements AgaviISingletonModel, AppKitNavContainerInterface
{
	/**
	 * @var AppKitNavContainer
	 */
	private $navContainer = null;
	
	/**
	 * @var AgaviTranslationManager
	 */
	private $tm = null;
	
	/**
	 * @var AgaviWebRouting
	 */
	private $ro = null;
	
	public function __construct() {
		$this->navContainer = new AppKitNavContainer();
	}
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->tm =& $this->getContext()->getTranslationManager();
		$this->ro =& $this->getContext()->getRouting();	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/menu/AppKitNavContainerInterface#getContainer()
	 */
	public function getContainer() {
		return $this->navContainer;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/menu/AppKitNavContainerInterface#getContainerIterator()
	 */
	public function getContainerIterator() {
		return $this->navContainer->getIterator();
	}
	
	/**
	 * Returns a nav item by name
	 * @param string $name
	 * @return AppKitNavItem
	 * @author Marius Hein
	 */
	public function getNavItemByName($name) {
		foreach ($this->getContainerIterator() as $item) {
			if ($item->getName() == $name) {
				return $item;
				break;	
			}
		}
		
		return null;
	}
	
	/**
	 * Returns the menu tree as json
	 * @return string
	 */
	public function getJsonData() {
		$d = array();		
		$this->arrayProc($d, $this->navContainer);
		return json_encode($d);
	}
	
	private function arrayProc(&$array, AppKitNavContainer &$container) {
		$array['items']=array();
		$array = &$array['items'];
		
		foreach ($container as $item) {
			
			$tmp = array('text' => $this->tm->_($item->getCaption()));
			
			if ($item->getRoute()) {
				$tmp['href'] = $this->ro->gen($item->getRoute());
			}
			
			if ($item->getJsHandler()) {
				$tmp['handler'] = $item->getJsHandler();
			}
			
			if ($item->hasChildren()) {
				$tmp['menu'] = array();
				$this->arrayProc($tmp['menu'], $item->getContainer());
			}
			
			array_push($array, $tmp);
		}
	}
	
}

?>