<?php

class AppKit_Admin_Groups_EditSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		try {
			if ($rd->getParameter('id') == 'new') {
				$role = new NsmRole();
				$this->setAttribute('title', 'Create a group');
				$this->setAttribute('role', $role);
			}
			else {
				$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
				$role = $roleadmin->getRoleById($rd->getParameter('id'));
				$this->setAttribute('title', 'Edit group '. $role->role_name);
				$this->setAttribute('role', $role);
			}
		}
		catch (AppKitDoctrineException $e) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
			$this->setAttribute('title', 'An error occured');
		}
	}
}

?>