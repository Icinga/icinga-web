<?php

class AppKit_Admin_Users_IndexSuccessView extends AppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Users');
		
		$useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
		
		$users = $useradmin->getUsersQuery(true);
		
		$pager = AppKitDoctrinePager::createNew($users, $rd->getParameter('page_offset', 1), 'appkit.admin.users');
		
		$this->setAttribute('user_collection', $users->execute());
		$this->setAttribute('user_pager', $pager);
		
		if ($users->count()<=0) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error('No users found, this is really bad!'));
		}
	}
}

?>