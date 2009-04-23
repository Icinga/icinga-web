<?php

class AppKit_NavigationContainerModel extends ICINGAAppKitBaseModel
implements AgaviISingletonModel, AppKitNavContainerInterface
{

	private $navContainer = null;
	
	public function __construct() {
		$this->navContainer = new AppKitNavContainer();
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
	
}

?>