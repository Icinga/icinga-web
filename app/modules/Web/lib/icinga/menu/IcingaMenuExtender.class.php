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
			
				// Navigation for "icinga"
				$icinga = $nav->getContainer()->addItemBefore('appkit.admin', AppKitNavItem::create('icinga', 'icinga')
				->setCaption('Monitoring')
				);

				//$icinga->addSubItem(AppKitNavItem::create('icinga.tacticalOverview', 'icinga.tacticalOverview')
				//->setCaption('Tactical Overview')
				//);

				$icinga->addSubItem(AppKitNavItem::create('icinga.serviceDetail', 'icinga.serviceDetail')
				->setCaption('Service Detail')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.hostDetail', 'icinga.hostDetail')
				->setCaption('Host Detail')
				);
				
				$icinga->addSubItem(AppKitNavItem::create('icinga.viewTest', 'icinga.viewTest')
				->setCaption('Portal')
				);
				
				/*
				$icinga->addSubItem(AppKitNavItem::create('icinga.hostgroupOverview', 'icinga.hostgroupOverview')
				->setCaption('Hostgroup Overview')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.hostgroupSummary', 'icinga.hostgroupSummary')
				->setCaption('Hostgroup Summary')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.hostgroupGrid', 'icinga.hostgroupGrid')
				->setCaption('Hostgroup Grid')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.servicegroupOverview', 'icinga.servicegroupOverview')
				->setCaption('Servicegroup Overview')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.servicegroupSummary', 'icinga.servicegroupSummary')
				->setCaption('Servicegroup Summary')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.servicegroupGrid', 'icinga.servicegroupGrid')
				->setCaption('Servicegroup Grid')
				);

				$icinga->addSubItem(AppKitNavItem::create('icinga.statusMap', 'icinga.statusMap')
				->setCaption('Status Map')
				);
				*/
				
		}
		
		return true;

	}

}

?>