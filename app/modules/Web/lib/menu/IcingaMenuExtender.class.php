<?php

class IcingaMenuExtender extends AppKitEventHandler implements AppKitEventHandlerInterface {

	public function checkObjectType(AppKitEvent &$e) {
		if (!$e->getObject() instanceof AppKit_NavigationContainerModel) {
			throw new AppKitEventException('Object should be AppKit_NavigationContainerModel');
		}
			
		return true;
	}

	public function handleEvent(AppKitEvent &$event) {

		$nav = $event->getObject();

		$user = $nav->getContext()->getUser();
		
		if ($user->hasCredential('icinga.user')) {
			
				$icinga_base = AppKitNavItem::create('icinga', 'icinga')
				->setCaption('Monitoring')
				->addAttributes('extjs-iconcls', 'icinga-icon-dot');
			
				// Throws exception if the admin is not there ...
				if ($nav->getNavItemByName('appkit.admin')) {
					// Navigation for "icinga"
					$icinga = $nav->getContainer()->addItemBefore('appkit.admin', $icinga_base);
				}
				else {
					$icinga = $nav->getContainer()->addItem($icinga_base);
				}
				
				$icinga->addSubItem(AppKitNavItem::create('icinga.portalView', 'icinga.portalView')
					->setCaption('Portal')
					->addAttributes('extjs-iconcls', 'icinga-icon-application-cascade')
				);
		}
		
		// Adding some help
		$my = $nav->getContainer()->addItem(AppKitNavItem::create('help', null)
			->setCaption('Help')
			->addAttributes('extjs-iconcls', 'icinga-icon-help')
		)->addSubItem(AppKitNavItem::create('icinga-home')
			->setCaption('Icinga home')
			->addAttributes('extjs-iconcls', 'icinga-icon-world')
			->addAttributes('extjs-href', 'http://www.icinga.org')
		)->addSubItem(AppKitNavItem::create('icinga-about')
			->setCaption('About')
			->addAttributes('extjs-iconcls', 'icinga-icon-information')
			->setJsHandler("AppKit.util.contentWindow.createDelegate(null, [{ url: '". AgaviContext::getInstance()->getRouting()->gen('icinga.about') ."' }, { title: _('About') }])")
		);
		
		return true;

	}

}

?>
