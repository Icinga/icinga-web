<?php

class AppKit_Admin_Groups_IndexSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Groups');
		
		$groupadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
		
		$groups = $groupadmin->getRoleQuery(true);
		
		$pager = AppKitDoctrinePager::createNew($groups, $rd->getParameter('page_offset'), 'appkit.admin.groups');
		
		$this->setAttribute('group_collection', $groups->execute());
		$this->setAttribute('group_pager', $pager);
		
		if ($groups->count() <= 0) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error('No groups found, this is really bad!'));
		}
	}
}

?>