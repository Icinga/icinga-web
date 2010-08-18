<?php

class AppKit_Login_AjaxLoginSuccessView extends AppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		if ($this->getContext()->getUser()->isAuthenticated() !== true) {
			$this->getResponse()->setHttpStatusCode('401');
		}
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$authenticated = false;
		$errors = array();
		$user = $this->getContext()->getUser();
		
		if ($this->getAttribute('authenticated', false) === true && $user->isAuthenticated() && $this->getAttribute('executed', false) === true) {
			$authenticated = true;
		}
		else {
			$errors['username'] = 'Login failed!';
			$this->getResponse()->setHttpStatusCode('401');
		}
		
		return json_encode(array(
			'success'		=> $authenticated,
			'errors'		=> $errors
		));
		
	}
}

?>