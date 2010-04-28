<?php

class AppKit_Admin_Users_EditSuccessView extends ICINGAAppKitBaseView
{
	public function executeSimplecontent(AgaviRequestDataHolder $rd) {
		if($this->getContainer()->getRequestMethod() == "write")
			return null;
		$this->setupHtml($rd);
		$useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
		$roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');
		
		if ($rd->getParameter('id') == 'new') {
			$user = new NsmUser();
			$this->setAttribute('title', 'Create a new user');
			$this->setAttribute('new',true);
		}
		else {
			$user = $useradmin->getUserById($rd->getParameter('id'));
			
			$this->setAttribute('title', 'Edit '. $user->givenName());
		}
		$this->setAttribute('user', $user);
	
		$this->setAttribute('roles', $roleadmin->getRoleCollection());
		$this->setAttribute('container', $rd->getParameter('container',null));
		$exec = $this->getContext()->getController()->createExecutionContainer("AppKit","Admin.PrincipalEditor",null,'simplecontent');
		$resp = $exec->execute()->getContent();
		$this->setAttribute("principal_editor",$resp);
	}
	
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
			$exec = $this->getContext()->getController()->createExecutionContainer("AppKit","Admin.PrincipalEditor",null,'simplecontent');
			$resp = $exec->execute()->getContent();
			$this->setAttribute("principal_editor",$resp);
		}
		catch (AppKitDoctrineException $e) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
			$this->setAttribute('title', 'An error occured');
		}
		
		
	}
}

?>