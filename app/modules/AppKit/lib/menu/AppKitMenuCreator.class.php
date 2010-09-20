<?php

if (AgaviConfig::get('core.default_context') !== 'web') {
	throw new AppKitException('No web context, no need to build the menu!');
}

class AppKitMenuCreator extends AppKitEventHandler implements AppKitEventHandlerInterface {

	public function initializeHandler() {
		$this->registerMenuExtender();
	}
	
	/**
	 * This builds up our menus structure, after building the base, trigger the ready event
	 * all other menu classes can add their own structures
	 * (non-PHPdoc)
	 * @see lib/appkit/event/AppKitEventHandlerInterface#handleEvent()
	 */
	public function handleEvent(AppKitEvent &$event) {
		
		// Our main structure
		$this->initModel();
		
		// Prepare the event
		$event = new AppKitEvent('appkit.menu.ready');
		$event->setInfo('Menu is built, you can add your navigation!');
		$event->setObject($this->getContainer());
		
		// Trigger all listeners
		AppKitEventDispatcher::getInstance()->triggerEvent($event);
		
		return true;
	}
	
	private function registerMenuExtender() {
		// Register the following handler
		if (is_array(($handler = AgaviConfig::get('modules.appkit.menu_extender')))) {
			foreach ($handler as $class) {
				$ref = new ReflectionClass($class);
				if ($ref->isInstantiable()) {
					AppKitEventDispatcher::getInstance()->addListener('appkit.menu.ready', $ref->newInstance());
				}
			}
		}
	}
	
	/**
	 * 
	 * @return AppKitSecurityUser
	 * @author Marius Hein
	 */
	private static function getUser() {
		return AgaviContext::getInstance()->getUser();
	}
	
	/**
	 * 
	 * @return AppKit_NavigationContainerModel
	 * @author Marius Hein
	 */
	private static function getContainer() {
		return AgaviContext::getInstance()->getModel('NavigationContainer', 'AppKit');
	}
	
	private static function initModel() {

		$nav = self::getContainer();

		if ($nav->getContainer()->Count() == 0) {

			$user = self::getUser();

//			$nav->getContainer()->addItem(AppKitNavItem::create('appkit', 'index_page')
//			->setCaption('Home')
//			->addAttributes('extjs-iconcls', 'icinga-icon-application')
//			);

			//Add more homelinks
			if (is_array($home_links = AgaviConfig::get('org.icinga.appkit.home_links'))) {
				foreach ($home_links as $link_route=>$link_caption) {
					$nav->getContainer()->addSubItem('appkit', AppKitNavItem::create($link_route, $link_route)
						->setCaption($link_caption)
					);
				}
			}
//
//			// Display only if we do not trust apache
//			// if (!AppKitFactories::getInstance()->getFactory('AuthProvider') instanceof AppKitAuthProviderHttpBasic) {
//				if ($user->isAuthenticated()) {
//					$nav->getContainer()->addSubItem('appkit', AppKitNavItem::create('appkit.logout', 'appkit.logout')
//					->setCaption('Logout')
//					->addAttributes('extjs-iconcls', 'icinga-icon-lock-open')
//					);
//				}
//				else {
//					$nav->getContainer()->addSubItem('appkit', AppKitNavItem::create('appkit.login', 'appkit.login')
//					->setCaption('Login')
//					->addAttributes('extjs-iconcls', 'icinga-icon-lock')
//					);
//				}
//			// }

			if ($user->isAuthenticated()) {
				
				// MENU FOR ADMIN
				if ($user->hasCredential('appkit.admin')) {
					$admin = $nav->getContainer()->addItem(AppKitNavItem::create('appkit.admin', 'appkit.admin')
					->setCaption('Admin')
					->addAttributes('extjs-iconcls', 'icinga-icon-wrench')
					);
					$admin->addSubItem(AppKitNavItem::create('appkit.admin.users', 'appkit.admin.users')
					->setCaption('Users')
					->addAttributes('extjs-iconcls', 'icinga-icon-user')
					);
					$admin->addSubItem(AppKitNavItem::create('appkit.admin.groups', 'appkit.admin.groups')
					->setCaption('Groups')
					->addAttributes('extjs-iconcls', 'icinga-icon-group')
					);
					$admin->addSubItem(AppKitNavItem::create('appkit.admin.logs', 'appkit.admin.logs')
					->setCaption('Logs')
					->addAttributes('extjs-iconcls', 'icinga-icon-note')
					);
				}
				
			}
		}

	}

}

?>