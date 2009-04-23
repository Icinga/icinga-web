<?php

class AppKit_Admin_Users_EditSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		try {
			$useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
			$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
			
			if ($rd->getParameter('id') == 'new') {
				$user = new NsmUser();
				$this->setAttribute('title', 'Create a new user');
			}
			else {
				$user = $useradmin->getUserById($rd->getParameter('id'));
				
				$this->setAttribute('title', 'Edit '. $user->givenName());
			}
			
			
			$this->setAttribute('user', $user);
			$this->setAttribute('roles', $roleadmin->getRoleCollection());
			
		}
		catch (AppKitDoctrineException $e) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
			$this->setAttribute('title', 'An error occured');
		}
		
		
	}
}

?>